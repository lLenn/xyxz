<?php

require_once Path :: get_path() . "pages/puzzle/theme/lib/theme_data_manager.class.php";
require_once Path :: get_path() . "pages/puzzle/theme/lib/theme_renderer.class.php";

class ThemeManager
{	
	private $user;
	private $renderer;
	
	function ThemeManager($user)
	{
		$this->user = $user;
		$this->renderer = new ThemeRenderer($this);
	}
	
	public function get_data_manager()
	{
		return ThemeDataManager::instance($this);
	}
	
	public function get_renderer()
	{
		return $this->renderer;
	}
	
	public function get_user()
	{
		return $this->user;
	}
	
}

?>