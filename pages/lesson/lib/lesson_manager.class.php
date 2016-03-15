<?php

require_once Path :: get_path() . "pages/lesson/lib/lesson_data_manager.class.php";
require_once Path :: get_path() . "pages/lesson/lib/lesson_renderer.class.php";

class LessonManager
{
	const ID_CHEXXL = 502;
	const LESSON_BROWSER = "Lesson_Browser";
	const LESSON_CREATOR = "Lesson_Creator";
	const LESSON_EXCERCISE_BROWSER = "Lesson_Excercise_Browser";
	const LESSON_EXCERCISE_CREATOR = "Lesson_Excercise_Creator";
	const LESSON_EXCERCISE_VIEWER = "Lesson_Excercise_Viewer";
	const LESSON_EXCERCISE_COMPONENT_CREATOR = "Lesson_Excercise_Component_Creator";
	const LESSON_VIEWER = "Lesson_Viewer";
	const LESSON_PAGE_CREATOR = "Lesson_Page_Creator";
	const LESSON_DELETOR = "Lesson_Deletor";
	const LESSON_CONTINUATION_CREATOR = "Lesson_Continuation_Creator";
	const LESSON_CONTINUATION_BROWSER = "Lesson_Continuation_Browser";
	const LESSON_CONTINUATION_SHOP = "Lesson_Continuation_Shop";
	const LESSON_CONTINUATION_AVAILABILITY_BROWSER = "Lesson_Continuation_Availability_Browser";
	const LESSON_CONTINUATION_AVAILABILITY_ADD_LESSON = "Lesson_Continuation_Availability_Add_Lessons";
	const LESSON_CONTINUATION_AVAILABILITY_ADD_LESSON_EXCERCISE = "Lesson_Continuation_Availability_Add_Lesson_Excercises";
	const LESSON_COURSE_BROWSER = "Lesson_Course_Browser";
	const LESSON_COURSE_ADD_COURSE = "Lesson_Course_Creator";
	const LESSON_COURSE_ADD_COURSE_LESSON = "Lesson_Course_Lesson_Creator";
	const LESSON_CHEXXL_CONVERTOR = "Lesson_Chexxl_Convertor";
	
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
			case self::LESSON_CREATOR: 
				require_once Path :: get_path() . "pages/lesson/lesson_creator.page.php";
				return $this->action_object = new LessonCreator($this);
				break;
			case self::LESSON_VIEWER: 
				require_once Path :: get_path() . "pages/lesson/lesson_viewer.page.php";
				return $this->action_object = new LessonViewer($this);
				break;
			case self::LESSON_EXCERCISE_BROWSER: 
				require_once Path :: get_path() . "pages/lesson/lesson_excercise/lesson_excercise_browser.page.php";
				return $this->action_object = new LessonExcerciseBrowser($this->get_lesson_excercise_manager());
				break;
			case self::LESSON_EXCERCISE_CREATOR: 
				require_once Path :: get_path() . "pages/lesson/lesson_excercise/lesson_excercise_creator.page.php";
				return $this->action_object = new LessonExcerciseCreator($this->get_lesson_excercise_manager());
				break;
			case self::LESSON_EXCERCISE_VIEWER: 
				require_once Path :: get_path() . "pages/lesson/lesson_excercise/lesson_excercise_viewer.page.php";
				return $this->action_object = new LessonExcerciseViewer($this->get_lesson_excercise_manager());
				break;
			case self::LESSON_EXCERCISE_COMPONENT_CREATOR: 
				require_once Path :: get_path() . "pages/lesson/lesson_excercise/lesson_excercise_component_creator.page.php";
				return $this->action_object = new LessonExcerciseComponentCreator($this->get_lesson_excercise_manager());
				break;
			case self::LESSON_PAGE_CREATOR: 
				require_once Path :: get_path() . "pages/lesson/lesson_page_creator.page.php";
				return $this->action_object = new LessonPageCreator($this);
				break;
			case self::LESSON_DELETOR: 
				require_once Path :: get_path() . "pages/lesson/lesson_deletor.page.php";
				return $this->action_object = new LessonDeletor($this);
				break;		
			case self::LESSON_CONTINUATION_CREATOR: 
				require_once Path :: get_path() . "pages/lesson/lesson_continuation_creator.page.php";
				return $this->action_object = new LessonContinuationCreator($this);
				break;		
			case self::LESSON_CONTINUATION_BROWSER: 
				require_once Path :: get_path() . "pages/lesson/lesson_continuation_browser.page.php";
				return $this->action_object = new LessonContinuationBrowser($this);
				break;		
			case self::LESSON_CONTINUATION_SHOP: 
				require_once Path :: get_path() . "pages/lesson/lesson_continuation_shop.page.php";
				return $this->action_object = new LessonContinuationShop($this);
				break;		
			case self::LESSON_CONTINUATION_AVAILABILITY_ADD_LESSON:
			case self::LESSON_CONTINUATION_AVAILABILITY_ADD_LESSON_EXCERCISE:
			case self::LESSON_CONTINUATION_AVAILABILITY_BROWSER:
				require_once Path :: get_path() . "pages/lesson/lesson_continuation_availability.page.php";
				switch($action)
				{
					case self::LESSON_CONTINUATION_AVAILABILITY_ADD_LESSON: $type = LessonContinuationAvailability::ADD_LESSON; break;
					case self::LESSON_CONTINUATION_AVAILABILITY_ADD_LESSON_EXCERCISE: $type = LessonContinuationAvailability::ADD_LESSON_EXCERCISE; break;
					case self::LESSON_CONTINUATION_AVAILABILITY_BROWSER: $type = LessonContinuationAvailability::BROWSER; break;
				}
				return $this->action_object = new LessonContinuationAvailability($this, $type);
				break;
			case self::LESSON_COURSE_BROWSER:
			case self::LESSON_COURSE_ADD_COURSE:
			case self::LESSON_COURSE_ADD_COURSE_LESSON:
				require_once Path :: get_path() . "pages/lesson/lesson_course_browser.page.php";
				switch($action)
				{
					case self::LESSON_COURSE_BROWSER: $type = LessonCourseBrowser::BROWSER; break;
					case self::LESSON_COURSE_ADD_COURSE: $type = LessonCourseBrowser::ADD_COURSE; break;
					case self::LESSON_COURSE_ADD_COURSE_LESSON: $type = LessonCourseBrowser::ADD_COURSE_LESSON; break;
				}
				return $this->action_object = new LessonCourseBrowser($this, $type);
				break;		
			case self::LESSON_CHEXXL_CONVERTOR:
				require_once Path :: get_path() . "pages/lesson/lesson_chexxl_convertor.page.php";
				return $this->action_object = new LessonChexxlConvertor($this);
				break;
		}
	}
	
}

?>