<?php

require_once Path :: get_path() . "pages/puzzle/set/lib/set_data_manager.class.php";
require_once Path :: get_path() . "pages/puzzle/set/lib/set_renderer.class.php";

class SetManager
{
	private $parent_manager;
	private $user;
	private $renderer;
	
	function SetManager($user, $parent_manager)
	{
		$this->parent_manager = $parent_manager;
		$this->user = $user;
		$this->renderer = new SetRenderer($this);
	}
	
	public function get_parent_manager()
	{
		return $this->parent_manager;
	}
	
	public function get_data_manager()
	{
		return SetDataManager::instance($this);
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