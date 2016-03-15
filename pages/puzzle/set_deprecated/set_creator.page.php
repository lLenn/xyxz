<?php

class SetCreator
{

	private $manager;
	private $puzzle_set;
	
	function SetCreator($manager)
	{
		$this->manager = $manager;
	}
	
	public function save_changes()
	{
		$html = array();
		$message = "";
		$message_type = "error";
		if(!empty($_POST))
		{
			$this->puzzle_set = $this->manager->get_data_manager()->retrieve_set_from_post();
			if(Error::get_instance()->get_result())
			{
				$success = $this->manager->get_data_manager()->insert_set($this->puzzle_set);
				if($success)
				{
					RightManager::instance()->add_location_object_user_right("Set", $this->manager->get_user()->get_id(), $success, RightManager::UPDATE_RIGHT);
					$message_type = "good";
					$message = Language::get_instance()->translate(317);
				}
				else
					$html[] = '<p class="error">' . Language::get_instance()->translate(318) . '</p>';
			}
			else 
				$html[] = Error::get_instance()->print_error(true);
		}
		if($message_type == "good")
		{
    		header("Location: " . Url :: create_url(array("page" => "browse_puzzle_sets", "id" => $success, "message" => $message, "message_type" => $message_type)));  
			exit;
		}
		return implode("\n", $html);
	}
	
	public function get_html()
	{
		$html = array();
		$html[] = '<link rel="stylesheet" href="'.Path::get_url_path().'plugins/jquery.multiselect2side/css/jquery.multiselect2side.css" type="text/css" media="screen" />';
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'plugins/jquery.multiselect2side/js/jquery.multiselect2side.js" ></script>';
		$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'pages/puzzle/set/javascript/set_form.js"></script>';
		$html[] = $this->save_changes();
		if(RightManager::instance()->check_right_location(RightManager::WRITE_RIGHT, "Set", $this->manager->get_user()))
			$html[] = $this->manager->get_renderer()->get_set_form($this->puzzle_set);
		else
			$html[] = '<p class="error">' . Language::get_instance()->translate(85) . '</p>';
		return implode("\n", $html);
	}
	
	public function get_title()
	{
		return "";
	}
		
	public function get_description()
	{
		return '<p style="vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(245) . '</p>';
	}
}
?>