<?php

class RightMapManager
{
	private $manager;
	private $section = self::PUZZLE;
	private $object_id;
	private $action;
	
	const PUZZLE = 1;
	const PUZZLE_SET = 2;
	const GAME = 3;
	const END_GAME = 4;
	const QUESTION = 5;
	const QUESTION_SET = 6;
	const VIDEO = 7;
	const LESSON = 8;
	const LESSON_EXCERCISE = 9;
	
	function RightMapManager($manager, $action)
	{
		$this->manager = $manager;
		$this->action = $action;
		$this->section = Request::get("section");
		$this->object_id = Request::get("id");
		if($this->section < 1 || $this->section > 10 || is_null($this->section))
			$this->section = self::PUZZLE;
	}
	
	public function switch_to_right_id($section)
	{
		switch($section)
		{
			case self::PUZZLE: return RightManager::PUZZLE_LOCATION_ID;
			case self::PUZZLE_SET: return RightManager::SET_LOCATION_ID;
			case self::GAME: return RightManager::GAME_LOCATION_ID;
			case self::END_GAME: return RightManager::ENDGAME_LOCATION_ID;
			case self::QUESTION: return RightManager::QUESTION_LOCATION_ID;
			case self::QUESTION_SET: return RightManager::QUESTIONSET_LOCATION_ID;
			case self::VIDEO: return RightManager::VIDEO_LOCATION_ID;
			case self::LESSON: return RightManager::LESSON_LOCATION_ID;
			case self::LESSON_EXCERCISE: return RightManager::LESSON_EXCERCISE_LOCATION_ID;
		}
	}
	
	public function get_prev_page($section)
	{
		switch($section)
		{
			case self::LESSON: return "browse_lessons";
			case self::LESSON_EXCERCISE: return "browse_excercises";
		}
	}
	
	public function get_title()
	{
		return "";
	}
	
	public function save_changes()
	{
		if($_POST)
		{
			if($this->action == RightManager::RIGHT_MAP_CREATOR || $this->action == RightManager::RIGHT_MAP_EDITOR)
			{
				$map = $this->manager->get_data_manager()->retrieve_location_user_map_from_post($this->switch_to_right_id($this->section), $this->manager->get_user()->get_id());
				if(Error::get_instance()->get_result())
				{
					if($this->action == RightManager::RIGHT_MAP_CREATOR)
					{
						$result = $this->manager->get_data_manager()->insert_location_user_map($map);
						if($result)
						{
							$map->set_id($result);
							$map->set_order($result);
							$result = $this->manager->get_data_manager()->update_location_user_map($map);
							if($result)
							{
								header("location: " .  Url::create_url(array("page"=>$this->get_prev_page($this->section), "message" => 890, "message_type" => "good")));
							}
							else return "<p class='error'>" . Language::get_instance()->translate(891) . "</p>";
						}
						else return "<p class='error'>" . Language::get_instance()->translate(891) . "</p>";
					}
					else
					{
						$prev_map = $this->manager->get_data_manager()->retrieve_location_user_map_by_id($this->object_id, $this->manager->get_user()->get_id());
						$map->set_id($this->object_id);
						$map->set_order($prev_map->get_order());
						$result = $this->manager->get_data_manager()->update_location_user_map($map);
						if($result) header("location: " .  Url::create_url(array("page"=>$this->get_prev_page($this->section), "message" => 898, "message_type" => "good")));
						else return "<p class='error'>" . Language::get_instance()->translate(899) . "</p>";
					}
				}
				else
				{
					return "<p class='error'>" . Language::get_instance()->translate(Error::get_instance()->get_message()) . "</p>";
				}
			}
			elseif($this->action == RightManager::RIGHT_MAP_CHANGER && is_numeric($this->object_id))
			{
				$prev_map = $this->manager->get_data_manager()->retrieve_location_user_map($this->manager->get_user()->get_id(), $this->object_id, $this->switch_to_right_id($this->section));
				if(!is_null($prev_map))
					$this->manager->get_data_manager()->delete_location_user_map_relation($prev_map->get_id(), $this->object_id);
				else
					$this->manager->get_data_manager()->delete_zero_location_user_map_relation($this->object_id, $this->switch_to_right_id($this->section), $this->manager->get_user()->get_id());
				if(Error::get_instance()->get_result())
				{
					$map_rel = $this->manager->get_data_manager()->retrieve_location_user_map_relation_from_post($this->switch_to_right_id($this->section), $this->manager->get_user()->get_id(), $this->object_id);
					if(Error::get_instance()->get_result())
					{
						if($map_rel->get_map_id() == 0)
						{
							$map_rel = new CustomProperties();
							$map_rel->add_property("map_id", 0);
							$map_rel->add_property("object_id", $this->object_id);
							$map_rel->add_property("user_id", $this->manager->get_user()->get_id());
							$map_rel->add_property("location_id", $this->switch_to_right_id($this->section));
						}
						$this->manager->get_data_manager()->insert_location_user_map_relation($map_rel);
						if(Error::get_instance()->get_result()) header("location: " . Url::create_url(array("page"=>$this->get_prev_page($this->section), "message" => 897, "message_type" => "good", "switched_map" => 1)));
						else return "<p class='error'>" . Language::get_instance()->translate(896) . "</p>";
					}
					else
					{
						return "<p class='error'>" . Language::get_instance()->translate(Error::get_instance()->get_message()) . "</p>";
					}
				}
				else
					return "<p class='error'>" . Language::get_instance()->translate(896) . "</p>";
				
			}
		}
	
		if($this->action == RightManager::RIGHT_MAP_DELETOR)
		{
			$rels = $this->manager->get_data_manager()->retrieve_location_user_map_relations($this->object_id);
			$this->manager->get_data_manager()->delete_location_user_map($this->object_id);
			if(Error::get_instance()->get_result())
			{
				foreach($rels as $rel)
				{
					$map_rel = new CustomProperties();
					$map_rel->add_property("map_id", 0);
					$map_rel->add_property("object_id", $rel->get_object_id());
					$map_rel->add_property("user_id", $this->manager->get_user()->get_id());
					$map_rel->add_property("location_id", $this->switch_to_right_id($this->section));
					RightDataManager::instance(null)->insert_location_user_map_relation($map_rel);
				}
				header("location: " . Url::create_url(array("page"=>$this->get_prev_page($this->section), "message" => 900, "message_type" => "good", "switched_map" => 1)));
			}
			else return header("location: " . Url::create_url(array("page"=>$this->get_prev_page($this->section), "message" => 901, "message_type" => "error")));
		}
		
		if($this->action == RightManager::RIGHT_MAP_UP || $this->action == RightManager::RIGHT_MAP_DOWN)
		{
			$this->manager->get_data_manager()->change_location_user_map_order($this->object_id, $this->manager->get_user()->get_id(), ($this->action == RightManager::RIGHT_MAP_UP?true:false));
			header("location: " . Url::create_url(array("page"=>$this->get_prev_page($this->section))));
		}
	}
	
	public function get_html()
	{
		$html = array();
		if($this->action == RightManager::RIGHT_MAP_CREATOR)
		{
			$html[] = $this->save_changes();
			$html[] = $this->manager->get_renderer()->get_map_form();
		}
		if($this->action == RightManager::RIGHT_MAP_EDITOR)
		{
			$map = $this->manager->get_data_manager()->retrieve_location_user_map_by_id($this->object_id, $this->manager->get_user()->get_id());
			if(!is_null($map))
			{
				$html[] = $this->save_changes();
				$html[] = $this->manager->get_renderer()->get_map_form($map);
			}
			else
				$html[] = '<p class="error">' . Language::get_instance()->translate(85) . '</p>';
		}
		elseif($this->action == RightManager::RIGHT_MAP_CHANGER && is_numeric($this->object_id))
		{
			$html[] = $this->save_changes();
			$html[] = $this->manager->get_renderer()->get_map_relation_form($this->switch_to_right_id($this->section), $this->manager->get_user()->get_id());
		}
		elseif($this->action == RightManager::RIGHT_MAP_DELETOR)
		{
			$map = $this->manager->get_data_manager()->retrieve_location_user_map_by_id($this->object_id, $this->manager->get_user()->get_id());
			if(!is_null($map))
			{
				$html[] = $this->save_changes();
			}
			else
				$html[] = '<p class="error">' . Language::get_instance()->translate(85) . '</p>';
		}
		elseif($this->action == RightManager::RIGHT_MAP_UP || $this->action == RightManager::RIGHT_MAP_DOWN)
		{
			$map = $this->manager->get_data_manager()->retrieve_location_user_map_by_id($this->object_id, $this->manager->get_user()->get_id());
			if(!is_null($map))
			{
				$html[] = $this->save_changes();
			}
			else
				$html[] = '<p class="error">' . Language::get_instance()->translate(85) . '</p>';
		}
		return implode("\n",$html);
	}

	public function get_description()
	{
	}
}
?>