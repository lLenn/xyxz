<?php

class UserImporter
{

	private $manager;
	private $id;
	
	function UserImporter($manager)
	{
		$this->manager = $manager;
		$this->id = Request::get("id");
		if(is_null($this->id) || !is_numeric($this->id))
			$this->id = 0;
	}
	
	public function save_changes()
	{
		if($_POST)
		{
			$list = $this->manager->get_data_manager()->retrieve_import_list_from_post($this->id != 0);
			if(Error::get_instance()->get_result())
			{
				$allowed = true;
				if($this->id == 0)
				{
					$coaches = $this->manager->get_data_manager()->retrieve_children($this->manager->get_user()->get_id(), true);
					foreach($coaches as $coach)
					{
						$count_list = 0;
						foreach($list["file"] as $line)
						{
							if($line[3] == $coach->get_username())
								$count_list++;
						}
						$allowed &= $this->check_allowed_objects($coach->get_id(), $count_list, $coach);
					}
				}
				else
					$allowed = $this->check_allowed_objects($this->id, count($list["file"]));

				if($allowed)
				{
					$checked_coaches = Array();
					foreach($list["file"] as $pupil)
					{
						$id = $this->id;
						$cond = $pupil[0] != "" && $pupil[1] != "" && $pupil[2] != "";
						if($this->id == 0)
						{
							foreach($coaches as $coach)
							{
								if($coach->get_username() == $pupil[3])
									$id = $coach->get_id();
							}
							
							if($id == 0)
							{
								$cond = false;
								if(!in_array($pupil[3], $checked_coaches))
								{
									Error::get_instance()->set_result(false);
									Error::get_instance()->append_message(sprintf(Language::get_instance()->translate(1403), $pupil[3]));
									$checked_coaches[] = $pupil[3];
								}
							}
						}
						
						if($cond)
						{
							$user = new User();
							$user->set_parent_id($id);
							if($list["emailaslogin"])
								$user->set_username($pupil[2]);
							else
								$user->set_username(str_replace(" ", "_", $list["prefix"] . "_" . $pupil[0] . "_" . $pupil[1]));
							$user->set_firstname($pupil[0]);
							$user->set_lastname($pupil[1]);
							$user->set_email($pupil[2]);
							$user->set_password(Hashing::hash($list["password"]));
							$user->set_language($this->manager->get_user()->get_language());
							$user->set_sex("M");
							$user->set_credits(0);
							$user->set_avatar("");
							$user->set_activation_code(1);
							$user->set_address("");
							$user->set_group_id(GroupManager::GROUP_PUPIL_ID);
							$user->set_extra_parent_ids(array());
							
							$user_chess_profile = new UserChessProfile();
							$user_chess_profile->set_user_id(0);
							$user_chess_profile->set_rd(350);
							$user_chess_profile->set_rating(1200);
	
							if(!$this->manager->get_data_manager()->username_exists($user->get_username()))
							{
								$user->set_chess_profile($user_chess_profile);
								$id = $this->manager->get_data_manager()->insert_user($user);
								$user->set_id($id);
								$this->manager->get_data_manager()->add_user_rights($user);
							}
							else
							{
								Error::get_instance()->set_result(false);
								Error::get_instance()->append_message(sprintf(Language::get_instance()->translate(1379), $user->get_username()));
							}
						}
						else
						{
							if(Error::get_instance()->get_result() && !($pupil[0] == "" && $pupil[1] == "" && $pupil[2] == ""))
							{
								Error::get_instance()->set_result(false);
								Error::get_instance()->append_message(Language::get_instance()->translate(1377));
							}
						}
					}
				}
						
				if(Error::get_instance()->get_result()) return '<p class="good">' . Language::get_instance()->translate(1378) . '</p>';
				else return '<p class="error">' . Error::get_instance()->get_message() . '</p>';
			}
			else
			{
				return '<p class="error">' . Error::get_instance()->get_message() . '</p>';
			}
		}
	}
	
	private function check_allowed_objects($id, $count_list, $coach = null)
	{
		$allowed_objects = RightManager::instance()->get_allowed_objects("User", $this->manager->get_data_manager()->retrieve_user($id));
		$children = count($this->manager->get_data_manager()->retrieve_children($id));
		if(!($allowed_objects == -1 || ($allowed_objects - $children >= $count_list)))
		{
			$message = sprintf(Language::get_instance()->translate(1376), Array($allowed_objects - $children));
			if(!is_null($coach))
				$message = sprintf(Language::get_instance()->translate(1404), $coach->get_username(), Array($allowed_objects - $children));
			Error::get_instance()->set_result(false);
			Error::get_instance()->append_message($message);
			return false;
		}
		return true;
	}
	
	public function get_html()
	{
		$html = array();
		if($this->manager->get_user()->is_admin() || ($this->manager->get_user()->get_group_id() == GroupManager::GROUP_COACH_ID && $this->id == $this->manager->get_user()->get_id()) || ($this->manager->get_user()->get_group_id() == GroupManager::GROUP_CLUB_ID && ($this->id == 0 || $this->manager->get_user()->is_child($this->id, true))))
		{
			$html[] = $this->save_changes();
			$html[] = $this->manager->get_renderer()->get_forms_renderer()->get_import_list_form($this->id != 0);
		}
		else
			$html[] = '<p class="error">' . Language::get_instance()->translate(85) .  '</p>';
		return implode("\n", $html);
	}
	
	public function get_title()
	{
		return '';
	}
	
	public function get_description()
	{
		return '';
	}
}
?>