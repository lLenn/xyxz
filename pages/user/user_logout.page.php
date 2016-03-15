<?php

class UserLogout
{
	private $manager;
	
	function UserLogout($manager)
	{
		$this->manager = $manager;
	}
	
	public function get_html()
	{
		if($this->manager->get_user()==null)
			echo "<p class='good'>U bent succesvol uitgelogd.</p>";
		else
			echo "<p class='error'>Uw poging om uit te loggen is mislukt. Gelieve de webmaster te contacteren indien u deze fout blijft ondervinden.</p>";
	}
	
	public function get_title()
	{
		return "<h1>Afmelden</h1>";
	}

	public function get_description()
	{
		return '<p style="vertical-align:top;font-style:italic;font-size:11px;">Meld u af.</p>';
	}
	
}

?>