<?php

class LessonCreator
{
	private $manager;
	private $id;
	private $write_right;
	private $lesson = null;
	private $object_right = RightManager::UPDATE_RIGHT;
	
	function LessonCreator($manager)
	{
		$this->manager = $manager;
		$this->id = Request::get("id");
		if(is_null($this->id) || !is_numeric($this->id))
			$this->id = 0;
		$this->write_right = RightManager::instance()->check_right_location(RightManager :: WRITE_RIGHT, "lesson", $this->manager->get_user());
		if($this->id != 0)
			$this->object_right = RightManager::instance()->get_right_location_object("Lesson", $this->manager->get_user(), $this->id);
	}
	
	public function save_changes()
	{
		$html = array();
		if(!empty($_POST))
		{
			if($this->id == 0 || $this->object_right == RightManager::UPDATE_RIGHT)
			{
				$this->lesson = $this->manager->get_data_manager()->retrieve_lesson_from_post();
				if(Error::get_instance()->get_result())
				{
					$message = "";
					$success = false;
					$pos_message_type = Language::get_instance()->translate(222);
					$neg_message_type = Language::get_instance()->translate(223);
					if($this->id == 0)
					{
						$success = $this->manager->get_data_manager()->insert_lesson($this->lesson);
						if($success)
						{
							$map_rel = new CustomProperties();
							$map_rel->add_property("map_id", 0);
							$map_rel->add_property("object_id", $success);
							$map_rel->add_property("user_id", $this->manager->get_user()->get_id());
							$map_rel->add_property("location_id", RightManager::LESSON_LOCATION_ID);
							RightDataManager::instance(null)->insert_location_user_map_relation($map_rel);
						}
					}
					else
					{
						$success = $this->manager->get_data_manager()->update_lesson($this->lesson);
						$pos_message_type = Language::get_instance()->translate(224);
						$neg_message_type = Language::get_instance()->translate(225);
					}
					
					if($success)	$message = urlencode($pos_message_type);
					else			$html[] = "<p class='error'>".$neg_message_type.".</p>";
					if($success)
					{
						header("Location: " . Url :: create_url(array("page" => "browse_lessons", "id" => $this->id, "message" => $message, "message_type" => "good")));  
						exit;
					}
					//else
					//	dump(Error::get_instance());
				}
				else
				{
					//dump(Error::get_instance());
					$html[] = Error::get_instance()->print_error(true);
				}
			}
			elseif($this->id != 0 && $this->object_right == RightManager::READ_RIGHT)
			{
				//dump("check for read right!");
				//exit;
				$lesson = $this->manager->get_data_manager()->retrieve_lesson_from_post("", true);
				if(Error::get_instance()->get_result())
				{
					$this->lesson = $lesson;
					$this->manager->get_data_manager()->update_lesson_users($this->lesson);
					$success = $this->manager->get_data_manager()->update_lesson_visible_and_order($this->lesson);
					$success &= $this->manager->get_data_manager()->update_lesson_criteria($this->lesson);
					if($success)	$message = urlencode(Language::get_instance()->translate(224));
					else			$html[] = "<p class='error'>" . Language::get_instance()->translate(225) . "</p>";
					if($success)
					{
						header("Location: " . Url :: create_url(array("page" => "browse_lessons", "id" => $this->id, "message" => $message, "message_type" => "good")));  
						exit;
					}
				}
				else
				{
					$lesson->set_title($this->lesson->get_title());
					$lesson->set_description($this->lesson->get_description());
					$this->lesson = $lesson;
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
				$this->lesson = $this->manager->get_data_manager()->retrieve_lesson_by_user_id($this->id, $this->manager->get_user()->get_id());
			
			$html[] = '<link rel="stylesheet" href="'.Path::get_url_path().'plugins/jquery.multiselect2side/css/jquery.multiselect2side.css" type="text/css" media="screen" />';
			$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'plugins/jquery.multiselect2side/js/jquery.multiselect2side.js" ></script>';
			$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'pages/lesson/javascript/lesson_creator.js"></script>';	
			$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'core/javascript/limited_text_field.js"></script>';
			$html[] = '<div id="lesson_info">';
			$html[] = $this->save_changes();
			$html[] = $this->manager->get_renderer()->get_lesson_form($this->lesson);
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
		return '<p style="vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(226) . '</p>';
	}
	
}

?>