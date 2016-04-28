<?php

class ClientLogout
{
	private $manager;
	
	function ClientLogout($manager)
	{
		$this->manager = $manager;
	}
	
	public function get_html()
	{
		if(is_null(WebApplication::get_user()))
			return Display :: display_message(Language::get_instance()->translate("logout_success"), Display :: MESSAGE_SUCCESS);
		else
			return Display :: display_message(Language::get_instance()->translate("logout_error"), Display :: MESSAGE_ERROR);
	}
	
}

?>