<?php

require_once Path :: get_path() . "pages/lesson/lesson_excercise/lib/lesson_excercise_data_manager.class.php";
require_once Path :: get_path() . "pages/lesson/lesson_excercise/lib/lesson_excercise_renderer.class.php";

class LessonExcerciseManager
{	
	private $parent_manager;
	private $user;
	private $renderer;
	
	function LessonExcerciseManager($user, $parent_manager)
	{
		$this->parent_manager = $parent_manager;
		$this->user = $user;
		$this->renderer = new LessonExcerciseRenderer($this);
	}

	public function get_parent_manager()
	{
		return $this->parent_manager;
	}
	
	public function get_data_manager()
	{
		return LessonExcerciseDataManager::instance($this);
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