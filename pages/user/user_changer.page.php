<?php

class UserChanger
{

	private $manager;
	private $user_id;
	
	function UserChanger($manager)
	{
		$this->manager = $manager;
		$this->user_id = Request::get("user_id");
		if(is_null($this->user_id) || !is_numeric($this->user_id))
			$this->user_id = 0;
	}
	
	public function get_html()
	{
		if($this->manager->get_user()->is_admin() && is_null(Session::retrieve("logged_as_admin")))
		{
			if($this->user_id != 0)
			{
				Session::register_user_id($this->user_id);
				Session::register("logged_as_admin", $this->manager->get_user()->get_id());
				Session::unregister("shop_mode");
				header("location: index.php");
			}
		}
		elseif(Session::retrieve("logged_as_admin"))
		{
			Session::register_user_id(Session::retrieve("logged_as_admin"));
			Session::unregister("logged_as_admin");
			Session::unregister("language");
			header("location: index.php");
		}
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