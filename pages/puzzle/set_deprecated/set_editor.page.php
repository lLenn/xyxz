<?php

class SetEditor
{

	private $manager;
	private $id;
	private $puzzle_set;
	
	function SetEditor($manager)
	{
		$this->manager = $manager;
		$this->id = Request::get("id");
		if(is_null($this->id) || !is_numeric($this->id)) $this->id = 0;
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
				$success = $this->manager->get_data_manager()->update_set($this->puzzle_set);
				if($success)
				{
					$message_type = "good";
					$message = Language::get_instance()->translate(321);
				}
				else
					$html[] = '<p class="error">' . Language::get_instance()->translate(322) . '</p>';
			}
			else 
				$html[] = Error::get_instance()->print_error(true);
		}
		if($message_type == "good")
		{
    		header("Location: " . Url :: create_url(array("page" => "browse_puzzle_sets", "id" => $this->id, "message" => $message, "message_type" => $message_type)));  
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
		if(RightManager::instance()->get_right_location_object("Set", $this->manager->get_user(), $this->id) == RightManager::UPDATE_RIGHT)
		{
			if(is_null($this->puzzle_set))
				$set = $this->manager->get_data_manager()->retrieve_set($this->id);
			else
				$set = $this->puzzle_set;	
			if(!is_null($set) && is_object($set))
				$html[] = $this->manager->get_renderer()->get_set_form($set);
			else
			{
    			header("Location: " . Url :: create_url(array("page" => "browse_puzzle_sets", "message" => Language::get_instance()->translate(324), "message_type" => "error")));  
				exit;
			}
		}
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
		return '<p style="vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(323) . '</p>';
	}
}
?>