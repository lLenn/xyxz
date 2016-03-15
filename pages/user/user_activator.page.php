<?php

class UserActivator
{
	private $manager;
	private $id;
	private $code;
	
	function UserActivator($manager)
	{
		$this->manager = $manager;
		$this->id = Request::get('id');
		$this->code = Request::get('code');
	}
	
	
	public function get_html()
	{
		if(is_null($this->id) ||!is_numeric($this->id) || is_null($this->code)) 
			echo "<p class='error'>Invalid id/code</p>";	
		else
		{
			$usr = $this->manager->get_database_manager()->retrieve_user($this->id);
			if($usr->getActivationCode()==$this->code)
			{
				$usr->setActivated();
				$success = $this->manager->get_database_manager()->update($usr);
				if($success)
					echo "<p class='good'>Uw account is geactiveerd. U kan nu inloggen op de website. De website beheerders zullen u de nodige rechten verschaffen indien u daarom gevraagd heeft bij het inschrijven. Als u dit vergeten bent en dit alsnog wenst te doen, contacteer dan de <a href='mailto:tverheecke@hotmail.com'>webmaster</a>.</p>";
				else
					echo "<p class='error'>Een fout is opgetreden tijdens de activatie. Check of de link correct is. Contacteer de <a href='mailto:tverheecke@hotmail.com'>webmaster</a> als deze fout blijft voorkomen.</p>";
			}
			else
				echo "<p class='error'>Invalid code</p>";
		}
	}
	
	public function get_title()
	{
		return "<h1>Gebruiker activeren</h1>";
	}
}
?>