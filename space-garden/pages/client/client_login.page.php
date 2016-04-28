<?php

class ClientLogin
{
	private $manager;
	
	function ClientLogin($manager)
	{
		$this->manager = $manager;
	}
	
	public function get_html()
	{
		$html = array();
		$html[] = '<p class="title">' . Language::get_instance()->translate("form_sign_in") .'</p>';
		$html[] = $this->manager->get_renderer()->get_forms_renderer()->get_login_form();
		return implode("\n", $html);
	}
}

?>