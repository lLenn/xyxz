<?php

require_once Path :: get_path() . "pages/lesson/lib/lesson_data_manager.class.php";
require_once Path :: get_path() . "pages/lesson/lib/lesson_renderer.class.php";

class LessonManager
{
	const LESSON_BROWSER = "Lesson_Browser";
	
	private $user;
	private $renderer;
	private $lesson_excercise_manager;
	
	function LessonManager($user)
	{
		Language::get_instance()->add_section_to_translations(Language::LESSON);
		Language::get_instance()->add_section_to_translations(Language::DIFFICULTY);
		$this->user = $user;
		$this->renderer = new LessonRenderer($this);
	}

	public function get_lesson_excercise_manager()
	{
		if(is_null($this->lesson_excercise_manager))
		{
			require_once Path :: get_path() . "pages/lesson/lesson_excercise/lib/lesson_excercise_manager.class.php";
			$this->lesson_excercise_manager = new LessonExcerciseManager($this->user, $this);
		}
		return $this->lesson_excercise_manager;
	}
	
	public function get_data_manager()
	{
		return LessonDataManager::instance($this);
	}
	
	public function get_renderer()
	{
		return $this->renderer;
	}
	
	public function get_user()
	{
		return $this->user;
	}
	
	public function factory($action)
	{
		switch($action)
		{
			case self::LESSON_BROWSER: 
				require_once Path :: get_path() . "pages/lesson/lesson_browser.page.php";
				return $this->action_object = new LessonBrowser($this);
				break;
		}
	}
	
}

?>