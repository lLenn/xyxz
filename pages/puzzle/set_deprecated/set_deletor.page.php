<?php

class SetDeletor
{
	private $id;
	private $manager;
	
	function SetDeletor($manager)
	{
		$this->manager = $manager;
		$this->id = Request::get("id");
	}
	
	public function get_html()
	{	
		//$success = $this->manager->get_data_manager()->delete_set($this->id);
		//if($success)
		$success = RightManager::instance()->delete_location_object_user_right("Set", $this->manager->get_user()->get_id(), $this->id);
		
		$message = Language::get_instance()->translate(319);
		if(!$success) $message = Language::get_instance()->translate(320);
		
		$message_type = "good";
		if(!$success) $message_type = "error";
		
    	header("Location: " . Url :: create_url(array("page" => "browse_puzzle_sets", "message" => $message, "message_type" => $message_type)));  
		exit;
	}
	
	public function get_title()
	{
		return "";
	}
	
	public function get_description()
	{
		return '';
	}
}
?>