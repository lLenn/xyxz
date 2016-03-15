<?php

require_once Path :: get_path() . "pages/puzzle/difficulty/lib/difficulty_data_manager.class.php";
require_once Path :: get_path() . "pages/puzzle/difficulty/lib/difficulty_renderer.class.php";

class DifficultyManager
{	
	private $user;
	private $renderer;
	
	function DifficultyManager($user)
	{
		$this->user = $user;
		$this->renderer = new DifficultyRenderer($this);
	}
	
	public function get_data_manager()
	{
		return DifficultyDataManager::instance($this);
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