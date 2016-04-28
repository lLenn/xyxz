<?php

class ClientRegister
{

	private $manager;
	
	function ClientRegister($manager)
	{
		$this->manager = $manager;
	}
	
	public function save_changes()
	{
		$client_user_account = null;
		$output = array();
    	if($_POST)
    	{
	    	$post_result = $this->manager->get_data_manager()->retrieve_client_user_account_from_post();
			if(isset($post_result) && $post_result["error"]=="")
			{
				$save_result = $this->manager->get_data_manager()->insert_temp_client_user_account($post_result["client_user_account"]);
				if(!$save_result)
				{
					$output[] = Display::display_message(Language::get_instance()->translate("error_registering"), Display::MESSAGE_ERROR);
				}
				else
				{
					$send_result = $this->manager->send_registration_mail($post_result["client_user_account"]);
					if(!$send_result)
					{
						$output[] = Display::display_message(Language::get_instance()->translate("error_registering"), Display::MESSAGE_ERROR);
					}
					else
					{
						$output[] = Display::display_message(Language::get_instance()->translate("success_registering"), Display::MESSAGE_SUCCESS);
					}
				}
			}
			else
			{
				$output[] = Display::display_message($post_result["error"], Display::MESSAGE_ERROR);
			}
			$client_user_account = $post_result["client_user_account"];
    	}
    	return array("client_user_account" => $client_user_account, "result" => implode("\n", $output));
	}
	
	public function get_html($show_title = true)
	{	
		$html = array();
		if($show_title)
		{
			$html[] = "<p class='title'>" . Language::get_instance()->translate("register"). "</p>";
		}
		$save_result = $this->save_changes();
		$html[] = $save_result["result"];
		$html[] = $this->manager->get_renderer()->get_forms_renderer()->get_form();
		return implode("\n", $html);
	}
}
?>