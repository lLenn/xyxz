<?php

class LessonContinuationShop
{
	private $manager;
	private $id;
	private $coach;
	
	function LessonContinuationShop($manager)
	{
		$this->manager = $manager;
		$this->id = Request::get("id");
		if(is_null($this->id) || !is_numeric($this->id))
			$this->id = 0;
		$this->coach = Request::get("coach");
		if(is_null($this->coach) || !is_numeric($this->coach))
			$this->coach = 0;
	}
	
	public function save_changes()
	{
		$html = array();
		if(!empty($_POST))
		{
			$group_id = $this->manager->get_user()->get_group_id();
			if($group_id != GroupManager::GROUP_GUEST_ID && $group_id != GroupManager::GROUP_PUPIL_ID && $group_id != GroupManager::GROUP_PUPIL_TEST_ID && $group_id != GroupManager::GROUP_CLUB_ID && $group_id != GroupManager::GROUP_CLUB_TEST_ID)
			{
				$am = new ArticleManager($this->manager->get_user());
				$lesson_continuation = $this->manager->get_data_manager()->retrieve_lesson_continuation($this->id);
				if(!is_null($lesson_continuation))
				{
					$coaches = UserDataManager::instance(null)->retrieve_siblings_by_user($this->manager->get_user());
					$dm = new DifficultyManager($this->manager->get_user());
					$lesson = $this->manager->get_data_manager()->retrieve_lesson($lesson_continuation->get_lesson_id());
					$cluster = $dm->get_data_manager()->retrieve_difficulty_cluster_by_rating($lesson->get_rating());
					$credits_buy = RightManager::instance()->get_data_manager()->retrieve_location_right_meta_data(RightManager::LESSON_LOCATION_ID, "credits_buy_" . $cluster->get_id());
					if(is_null($credits_buy) || !is_numeric($credits_buy) || $credits_buy < 0)
						$credits_buy = 0;
					else
					{
						foreach($coaches as $coach)
						{
							if(RightManager::instance()->get_right_location_object(RightManager::LESSON_LOCATION_ID, $coach, $lesson->get_id(), true) != RightManager::NO_RIGHT)
								$credits_buy = 0;
						}
					}
					$credits_total = $credits_buy;
					foreach($lesson_continuation->get_lesson_excercise_ids() as $excercise_id)
					{
						$exc = $this->manager->get_lesson_excercise_manager()->get_data_manager()->retrieve_lesson_excercise($excercise_id);
						$cluster = $dm->get_data_manager()->retrieve_difficulty_cluster_by_rating($exc->get_rating());
						$credits_buy = RightManager::instance()->get_data_manager()->retrieve_location_right_meta_data(RightManager::LESSON_EXCERCISE_LOCATION_ID, "credits_buy_" . $cluster->get_id());
						if(is_null($credits_buy) || !is_numeric($credits_buy) || $credits_buy < 0)
							$credits_buy = 0;
						else
						{
							foreach($coaches as $coach)
							{
								if(RightManager::instance()->get_right_location_object(RightManager::LESSON_EXCERCISE_LOCATION_ID, $coach, $excercise_id, true) != RightManager::NO_RIGHT)
									$credits_buy = 0;
							}
						}
						$credits_total += $credits_buy;
					}
					$user_credits = $this->manager->get_user()->get_credits();
					if($user_credits == -1 || $user_credits >= $credits_total)
					{	
						$credits_sell = RightManager::instance()->get_data_manager()->retrieve_location_right_meta_data(RightManager::LESSON_LOCATION_ID, "credits_sell_" . $cluster->get_id());
						if(is_null($credits_sell) || !is_numeric($credits_sell) || $credits_sell < 0)
							$credits_sell = 0;
						else
						{
							foreach($coaches as $coach)
							{
								if(RightManager::instance()->get_right_location_object(RightManager::LESSON_LOCATION_ID, $coach, $lesson->get_id(), true) != RightManager::NO_RIGHT)
									$credits_buy = 0;
							}
						}
						if($credits_sell != 0)
						{
							$parent_user = UserDataManager::instance(null)->retrieve_user(RightDataManager::instance(null)->retrieve_location_object_creator(RightManager::LESSON_LOCATION_ID, $lesson->get_id()));
							if(!is_null($parent_user) && $parent_user->get_credits() != -1)
							{
								$parent_user->set_credits($parent_user->get_credits() + $credits_sell);
								UserDataManager::instance(null)->update_user($parent_user);
								$am->get_data_manager()->insert_shop_bought(RightManager::LESSON_LOCATION_ID, $lesson->get_id(), $this->manager->get_user()->get_id(), $parent_user->get_id(), $credits_sell);
							}
						}
						RightManager::instance()->add_location_object_user_right(RightManager::LESSON_LOCATION_ID, $this->manager->get_user()->get_id(), $lesson->get_id(), RightManager::READ_RIGHT);
						$map_rel = new CustomProperties();
						$map_rel->add_property("map_id", 0);
						$map_rel->add_property("object_id", $lesson->get_id());
						$map_rel->add_property("user_id", $this->manager->get_user()->get_id());
						$map_rel->add_property("location_id", RightManager::LESSON_LOCATION_ID);
						RightDataManager::instance(null)->insert_location_user_map_relation($map_rel);
						foreach($lesson_continuation->get_lesson_excercise_ids() as $excercise_id)
						{
							$credits_sell = RightManager::instance()->get_data_manager()->retrieve_location_right_meta_data(RightManager::LESSON_EXCERCISE_LOCATION_ID, "credits_sell_" . $cluster->get_id());
							if(is_null($credits_sell) || !is_numeric($credits_sell) || $credits_sell < 0)
								$credits_sell = 0;
							else
							{
								foreach($coaches as $coach)
								{
									if(RightManager::instance()->get_right_location_object(RightManager::LESSON_EXCERCISE_LOCATION_ID, $coach, $excercise_id, true) != RightManager::NO_RIGHT)
										$credits_buy = 0;
								}
							}
							if($credits_sell != 0)
							{
								$parent_user = UserDataManager::instance(null)->retrieve_user(RightDataManager::instance(null)->retrieve_location_object_creator(RightManager::LESSON_EXCERCISE_LOCATION_ID, $excercise_id));
								if(!is_null($parent_user) && $parent_user->get_credits() != -1)
								{
									$parent_user->set_credits($parent_user->get_credits() + $credits_sell);
									UserDataManager::instance(null)->update_user($parent_user);
									$am->get_data_manager()->insert_shop_bought(RightManager::LESSON_EXCERCISE_LOCATION_ID, $excercise_id, $this->manager->get_user()->get_id(), $parent_user->get_id(), $credits_sell);
								}
							}
							RightManager::instance()->add_location_object_user_right(RightManager::LESSON_EXCERCISE_LOCATION_ID, $this->manager->get_user()->get_id(), $excercise_id, RightManager::READ_RIGHT);
							$map_rel = new CustomProperties();
							$map_rel->add_property("map_id", 0);
							$map_rel->add_property("object_id", $excercise_id);
							$map_rel->add_property("user_id", $this->manager->get_user()->get_id());
							$map_rel->add_property("location_id", RightManager::LESSON_EXCERCISE_LOCATION_ID);
							RightDataManager::instance(null)->insert_location_user_map_relation($map_rel);
						}
						if($user_credits!=-1)
						{
							$this->manager->get_user()->set_credits($user_credits - $credits_total);
							UserDataManager::instance(null)->update_user($this->manager->get_user());
						}
						$this->manager->get_data_manager()->update_lesson_continuation_bought($this->id, 1);
						$lesson_continuation->set_bought(1);
						if($lesson_continuation->get_to_user_id()!=$this->manager->get_user()->get_id())
						{
							$lesson_continuation->set_from_user_id($this->manager->get_user()->get_id());
							$lesson_continuation->set_requested(0);
							$this->manager->get_data_manager()->update_lesson_continuation($lesson_continuation);
							$this->manager->get_data_manager()->insert_transfered_lesson_continuation($lesson_continuation->get_id(), $lesson_continuation->get_to_user_id());
						}
						if(mysqli_errno(DataManager::get_connection()->get_connection()) == 0)
						{
							$page = "browse_continuations";
							if($this->coach)
								$page = "browse_continuations&view=1";
							header("Location: " . Url :: create_url(array("page" => $page, "message" => Language::get_instance()->translate(1189), "message_type" => "good")));  
							exit;
						}
						else
							return "<p class='error'>" . Language::get_instance()->translate(1190) . "</p>";
					}
					else
						$html[] = "<p class='error'>" . Language::get_instance()->translate(1191) . "</p>";
				}
			}
			elseif($group_id != GroupManager::GROUP_GUEST_ID && $group_id != GroupManager::GROUP_CLUB_ID && $group_id != GroupManager::GROUP_CLUB_TEST_ID)
			{
				$title = Language::get_instance()->translate(1195);
				$message_txt = Language::get_instance()->translate(1196) . ":\n\n";
				$message_txt .= Request::post('message_text') . "\n\n";
				$message_txt .= "<a href='" . Url::create_url(array("page"=>"buy_continuation","id"=>$this->id)) . "'>" . Url::create_url(array("page"=>"buy_continuation","id"=>$this->id)) . "</a>\n";
				$message = array();
				$message["id"] = 0;
				$message["from_user_id"] = $this->manager->get_user()->get_id();
				$message["to_user_ids"] = array($this->manager->get_user()->get_parent_id());
				$message["time"] = time();
				$message["read"] = 0;
				$message["title"] = addslashes($title);
				$message["message"] = addslashes($message_txt);
				$message = new Message($message);
				$success = MessageDataManager::instance(null)->insert_message($message);
				$this->manager->get_data_manager()->update_lesson_continuation_requested($this->id, 1);
				if(mysqli_errno(DataManager::get_connection()->get_connection()) == 0)
				{
					header("Location: " . Url :: create_url(array("page" => "browse_continuations", "message" => Language::get_instance()->translate(1197), "message_type" => "good")));  
					exit;
				}
				else
					return "<p class='error'>" . Language::get_instance()->translate(1198) . "</p>";
			}
		}
		return implode("\n", $html);
	}
	
	public function get_html()
	{
		$html = array();
		$lesson_continuation = $this->manager->get_data_manager()->retrieve_lesson_continuation($this->id);
		$group_id = $this->manager->get_user()->get_group_id();
		if(!is_null($lesson_continuation) && $group_id != GroupManager::GROUP_GUEST_ID && $group_id != GroupManager::GROUP_CLUB_ID && $group_id != GroupManager::GROUP_CLUB_TEST_ID)
		{
			$html[] = '<div id="lesson_info">';
			$group_id = $this->manager->get_user()->get_group_id();
			if($group_id != GroupManager::GROUP_GUEST_ID && $group_id != GroupManager::GROUP_PUPIL_ID && $group_id != GroupManager::GROUP_PUPIL_TEST_ID)
			{
				$html[] = $this->save_changes();
				$html[] = $this->manager->get_renderer()->get_lesson_continuation_shop_form($lesson_continuation);
			}
			else
			{
				$html[] = $this->save_changes();
				$html[] = $this->manager->get_renderer()->get_lesson_continuation_request_form($lesson_continuation);
			}
			$html[] = '</div>';
		}
		else
			$html[] = '<p class="error">' . Language::get_instance()->translate(85) . '</p>';
		return implode("\n",$html);
	}
	
	public function get_title()
	{
		return "";
	}
	
	public function get_description()
	{
		return '<p style="vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(226) . '</p>';
	}
	
}

?>