<?php

class UserActivationSender
{

	private $manager;
	
	function UserActivationSender($manager)
	{
		$this->manager = $manager;
		$this->user_id = Request::get('id');
	}
	
	public function get_html()
	{
		$usr = $this->manager->get_user();
		if(!is_null($usr) && $usr->isAdmin())
		{		
			$user = $this->manager->get_database_manager()->retrieve_user($this->user_id);
			$password = Utilities::randomCharSet(8, false);
			$user->setPassword($password);
			$manager = new UserManager($user);
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";			
			$headers .= 'From: MSV Eeklo <karel.boone@gmail.com>' . "\r\n";
			$manager->sendActivationMail($headers);
			$user->setPassword(Hashing::hash($password));
			$success = $this->manager->get_database_manager()->update($user);
			echo "<p class='good'>De activatiemail is verzonden.</p>";
		}
		else
			echo "<p class='error'>U bent niet gemachtigd deze pagina te bekijken.</p>";
	}
	
	public function get_title()
	{
		return "<h1>Activeringsmail verzenden</h1>";
	}
}
?>