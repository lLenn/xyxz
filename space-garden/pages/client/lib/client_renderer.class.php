<?php

require_once Path::get_path() . 'pages/client/lib/client_forms.class.php';

class ClientRenderer
{

	private $manager;
	private $forms;
	
	function ClientRenderer($manager)
	{
		$this->manager = $manager;
		$this->forms = new ClientForms($manager);
	}
	
	public function get_forms_renderer()
	{
		return $this->forms;
	}
	
}

?>