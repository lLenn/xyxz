<?php

require_once Path::get_path() . 'pages/puzzle/theme/lib/theme_manager.class.php';

class LessonExcerciseCreator
{
	private $manager;
	private $id;
	private $write_right;
	private $object_right = RightManager::NO_RIGHT;
	private $lesson_excercise = null;
	
	function LessonExcerciseCreator($manager)
	{
		$this->manager = $manager;
		$this->id = Request::get("id");
		if(is_null($this->id) || !is_numeric($this->id))
			$this->id = 0;
		$this->write_right = RightManager::instance()->check_right_location(RightManager :: WRITE_RIGHT, "lesson", $this->manager->get_user());
		if($this->id != 0)
			$this->object_right = RightManager::instance()->get_right_location_object(RightManager::LESSON_EXCERCISE_LOCATION_ID, $this->manager->get_user(), $this->id);
		else
			$this->object_right = RightManager::UPDATE_RIGHT;
	}
	
	public function save_changes()
	{
		$html = array();
		if(!empty($_POST))
		{
			if($this->id == 0 || $this->object_right == RightManager::UPDATE_RIGHT)
			{
				$this->lesson_excercise = $this->manager->get_data_manager()->retrieve_lesson_excercise_from_post();
				if(Error::get_instance()->get_result())
				{
					$message = "";
					$success = false;
					$pos_message_type = Language::get_instance()->translate(235);
					$neg_message_type = Language::get_instance()->translate(236);
					if($this->id == 0)
					{
						$success = $this->manager->get_data_manager()->insert_lesson_excercise($this->lesson_excercise);
						if($success)
						{
							$map_rel = new CustomProperties();
							$map_rel->add_property("map_id", 0);
							$map_rel->add_property("object_id", $success);
							$map_rel->add_property("user_id", $this->manager->get_user()->get_id());
							$map_rel->add_property("location_id", RightManager::LESSON_EXCERCISE_LOCATION_ID);
							RightDataManager::instance(null)->insert_location_user_map_relation($map_rel);
						}
					}
					else
					{
						$success = $this->manager->get_data_manager()->update_lesson_excercise($this->lesson_excercise);
						$pos_message_type = Language::get_instance()->translate(237);
						$neg_message_type = Language::get_instance()->translate(238);
					}
					
					if($success)	$message = urlencode($pos_message_type);
					else			$html[] = "<p class='error'>" . $neg_message_type . "</p>";
					if($success)
					{
						header("Location: " . Url :: create_url(array("page" => "browse_excercises", "message" => $message, "message_type" => "good")));  
						exit;
					}
				}
				else
				{
					$html[] = Error::get_instance()->print_error(true);
				}
			}
			elseif($this->id != 0 && $this->object_right == RightManager::READ_RIGHT)
			{
				$lesson_excercise = $this->manager->get_data_manager()->retrieve_lesson_excercise_from_post(true);
				if(Error::get_instance()->get_result())
				{
					$this->lesson_excercise = $lesson_excercise;
					$this->manager->get_data_manager()->update_lesson_excercise_relations($this->lesson_excercise);
					$success = $this->manager->get_data_manager()->update_lesson_excercise_visible_and_order($this->lesson_excercise);
					$success &= $this->manager->get_data_manager()->update_lesson_excercise_criteria($this->lesson_excercise);
					if($success)	$message = urlencode(Language::get_instance()->translate(237));
					else			$html[] = "<p class='error'>" . Language::get_instance()->translate(238) . "</p>";
					if($success)
					{
						header("Location: " . Url :: create_url(array("page" => "browse_excercises", "message" => $message, "message_type" => "good")));  
						exit;
					}
				}
				else
				{
					$lesson_excercise->set_title($this->lesson_excercise->get_title());
					$lesson_excercise->set_description($this->lesson_excercise->get_description());
					$this->lesson = $lesson_excercise;
					$html[] = Error::get_instance()->print_error(true);
				}
			}
		}
		return implode("\n", $html);
	}
	
	public function get_html()
	{
		$html = array();
		$user = $this->manager->get_user();
		if($this->write_right)
		{
			if($this->id != 0)
				$this->lesson_excercise = $this->manager->get_data_manager()->retrieve_lesson_excercise_by_id($this->id, $this->manager->get_user()->get_id());
			$html[] = '<link rel="stylesheet" href="'.Path::get_url_path().'plugins/jquery.multiselect2side/css/jquery.multiselect2side.css" type="text/css" media="screen" />';
			$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'plugins/jquery.multiselect2side/js/jquery.multiselect2side.js" ></script>';
			$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'pages/lesson/lesson_excercise/javascript/lesson_excercise_creator.js"></script>';	
			$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'core/javascript/limited_text_field.js"></script>';
			$html[] = '<div id="lesson_info">';
			$html[] = $this->save_changes();
			$html[] = "<br/>";
			$html[] = $this->manager->get_renderer()->get_lesson_excercise_form($this->lesson_excercise, $this->object_right);
			$html[] = '</div>';
			$html[] = '<script type="text/javascript">';
	    	$html[] = '  var limit = 200;';
	    	$html[] = '</script>';
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
		return '<p style="vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(239) . '</p>';
	}
	
}

/*
class LessonExcerciseCreator
{
	private $manager;
	private $id;
	private $write_right;
	private $object_right = RightManager::NO_RIGHT;
	private $lesson_excercise = null;
	
	function LessonExcerciseCreator($manager)
	{
		$this->manager = $manager;
		$this->id = Request::get("id");
		if(is_null($this->id) || !is_numeric($this->id))
			$this->id = 0;
		$this->write_right = RightManager::instance()->check_right_location(RightManager :: WRITE_RIGHT, "lesson", $this->manager->get_user());
		if($this->id != 0)
			$this->object_right = RightManager::instance()->get_right_location_object(RightManager::LESSON_EXCERCISE_LOCATION_ID, $this->manager->get_user(), $this->id);
		else
			$this->object_right = RightManager::UPDATE_RIGHT;
	}
	
	public function save_changes()
	{
		$html = array();
		if(!empty($_POST))
		{
			$this->lesson_excercise = $this->manager->get_data_manager()->retrieve_lesson_excercise_from_post();
			if(Error::get_instance()->get_result() && ($this->id == 0 || $this->object_right == RightManager::UPDATE_RIGHT))
			{
				$message = "";
				$success = false;
				$pos_message_type = Language::get_instance()->translate(235);
				$neg_message_type = Language::get_instance()->translate(236);
				if($this->id == 0)
				{
					$success = $this->manager->get_data_manager()->insert_lesson_excercise($this->lesson_excercise);
					if($success)
					{
						$map_rel = new CustomProperties();
						$map_rel->add_property("map_id", 0);
						$map_rel->add_property("object_id", $success);
						$map_rel->add_property("user_id", $this->manager->get_user()->get_id());
						$map_rel->add_property("location_id", RightManager::LESSON_EXCERCISE_LOCATION_ID);
						RightDataManager::instance(null)->insert_location_user_map_relation($map_rel);
					}
				}
				else
				{
					$success = $this->manager->get_data_manager()->update_lesson_excercise($this->lesson_excercise);
					$pos_message_type = Language::get_instance()->translate(237);
					$neg_message_type = Language::get_instance()->translate(238);
				}
				
				if($success)	$message = $pos_message_type;
				else			$html[] = "<p class='error'>" . $neg_message_type . "</p>";
				if($success)
				{
					header("Location: " . Url :: create_url(array("page" => "browse_excercises", "message" => $message, "message_type" => "good")));  
					exit;
				}
			}
			elseif($this->id != 0 && $this->object_right == RightManager::READ_RIGHT)
			{
				Error::new_instance();
				$this->manager->get_data_manager()->update_lesson_excercise_relations($this->lesson_excercise);
				$success = $this->manager->get_data_manager()->update_lesson_excercise_visible_and_order($this->lesson_excercise);
				if($success)	$message = Language::get_instance()->translate(224);
				else			$html[] = "<p class='error'>" . Language::get_instance()->translate(225) . "</p>";
				if($success)
				{
					header("Location: " . Url :: create_url(array("page" => "browse_excercises", "message" => $message, "message_type" => "good")));  
					exit;
				}
			}
			else
				$html[] = Error::get_instance()->print_error(true);
		}
		return implode("\n", $html);
	}
	
	public function get_html()
	{
		$html = array();
		$user = $this->manager->get_user();
		if($this->write_right)
		{
			if($this->id != 0)
				$this->lesson_excercise = $this->manager->get_data_manager()->retrieve_lesson_excercise_by_id($this->id, $this->manager->get_user()->get_id());
			$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'pages/lesson/lesson_excercise/javascript/lesson_excercise_creator.js"></script>';	
			$html[] = '<div id="lesson_info">';
			$html[] = $this->save_changes();
			$html[] = "<br/>";
			$html[] = $this->manager->get_renderer()->get_lesson_excercise_form($this->lesson_excercise, $this->object_right);
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
		return '<p style="vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(239) . '</p>';
	}
	
}
*/
?>