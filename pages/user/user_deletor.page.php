<?php

class UserDeletor
{
	private $id;
	private $manager;
	
	function UserDeletor($manager)
	{
		$this->manager = $manager;
		$this->id = Request::get("id");
	}
	
	public function get_html()
	{	
		$success = $this->manager->get_data_manager()->delete_user($this->id);
		$message = urlencode(Language::get_instance()->translate(477));
		if(!$success) $message = urlencode(Language::get_instance()->translate(478));
		
		$message_type = "good";
		if(!$success) $message_type = "error";
		
    	header("Location: " . Url :: create_url(array("page" => "browse_members", "message" => $message, "message_type" => $message_type)));  
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