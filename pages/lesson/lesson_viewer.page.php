<?php
require_once Path::get_path() . 'pages/lesson/lesson_continuation_availability.page.php';

class LessonViewer
{
	private $manager;
	private $id;
	private $page_nr;
	private $coach;
	
	function LessonViewer($manager)
	{
		$this->manager = $manager;
		$this->id = Request::get("id");
		$this->page_nr = Request::get("page_nr");
		$this->coach = Request::get("coach");
		if(is_null($this->id) || !is_numeric($this->id))
			$this->id = 0;
		if(is_null($this->page_nr) || !is_numeric($this->page_nr))
			$this->page_nr = 1;
		if(is_null($this->coach) || !is_numeric($this->coach))
			$this->coach = 0;
		elseif($this->coach != 0)
			$this->coach = 1;
	}
	
	public function get_title()
	{
		return "";
	}
	
	
	public function get_description()
	{
		return '';
	}
	
	public function get_html()
	{
		$html = array();
		$user = $this->manager->get_user();
		$html[] = '<div id="lesson_info" style="padding-top: 15px;">';
		if($this->coach == 0 && !is_null($user->get_parent_id()) && $user->get_parent_id() != 0)
		{
			$user = UserDataManager::instance(null)->retrieve_user($user->get_parent_id());
		}
		if(!is_null($user) && ($this->manager->get_data_manager()->is_available_lesson($this->id) || RightManager::instance()->get_right_location_object("lesson", $user, $this->id) >= RightManager::READ_RIGHT) || $this->manager->get_data_manager()->is_lesson_course($this->id))
		{
			$page = $this->manager->get_data_manager()->retrieve_lesson_page_by_lesson_id_and_order($this->id, $this->page_nr);
			if(!is_null($page))
			{
				$group_id = 0;
				if(!is_null($this->manager->get_user()->get_group()))
					$group_id = $this->manager->get_user()->get_group_id();
        		$pupil = 1; //$group_id==GroupManager::GROUP_PUPIL_ID?1:0;
				$style_extra = '';
				if($this->page_nr!=1)
					$style_extra = ' style="margin-left: 375px;"';
				$html[] = '<div id="lesson_progress"'.$style_extra.'>';
				$count_pages = $this->manager->get_data_manager()->count_lesson_pages($this->id);
				$url_arr = array('page' => 'view_lesson', 'id' => $this->id);
				if($this->coach)
					$url_arr['coach'] = $this->coach;
				if($this->page_nr!=1)
				{
					$url_arr['page_nr'] = $this->page_nr-1;
					$html[] = '<a href="' . Url::create_url($url_arr) . '">';
					$html[] = '<img style="float: left; margin-right: 5px; padding: 0; border: 0;" src="' . Path::get_url_path() . 'layout/images/buttons/progress_left' . ($pupil?"":"_coach") . '.png"/>';
					$html[] = '</a>';
				}
				for($i=0;$i<$count_pages;$i++)
				{
					$url_arr['page_nr'] = $i+1;
					$html[] = '<a href="' . Url::create_url($url_arr) . '">';
					$html[] = '<img style="float: left; margin-right: 5px; padding: 0; border: 0;" src="' . Path::get_url_path() . 'pages/lesson/ajax/retrieve_lesson_image.ajax.php?count=' . $count_pages . '&i=' . $i . '&page=' . $this->page_nr . '"/>';
					$html[] = '</a>';
				}
				if($this->page_nr!=$count_pages)
				{
					$url_arr['page_nr'] = $this->page_nr+1;
					$html[] = '<a href="' . Url::create_url($url_arr) . '">';
					$html[] = '<img style="float: left; margin-right: 5px; padding: 0; border: 0;" src="' . Path::get_url_path() . 'layout/images/buttons/progress_right' . ($pupil?"":"_coach") . '.png"/>';
					$html[] = '</a>';
				}
				$html[] = '<br class="clearfloat"/>';
				$html[] = '</div>';
				$html[] = "<h2 class='title'>".$page->get_title()."</h2>";
				switch($page->get_type())
				{
					case LessonPage::TEXT_TYPE:
						$text = $this->manager->get_data_manager()->retrieve_lesson_page_text($page->get_type_object_id());
						$html[] = '<div style="width: 630px;">';
						$html[] = $text->get_text();
						$html[] = '</div>';
						break;
					case LessonPage::PUZZLE_TYPE:
						require_once Path::get_path() . 'pages/puzzle/lib/puzzle_manager.class.php';
						$puzzle_manager = new PuzzleManager($this->manager->get_user());
						Request::set_get('id', $page->get_type_object_id());
						$html[] = $puzzle_manager->factory(PuzzleManager::PUZZLE_VIEWER)->get_html();
						break;
					case LessonPage::GAME_TYPE:
						require_once Path::get_path() . 'pages/game/lib/game_manager.class.php';
						$game_manager = new GameManager($this->manager->get_user());
						Request::set_get('id', $page->get_type_object_id());
						$html[] = $game_manager->factory(GameManager::GAME_VIEWER)->get_html(false);
						break;
					case LessonPage::END_GAME_TYPE:
						require_once Path::get_path() . 'pages/game/lib/game_manager.class.php';
						$game_manager = new GameManager($this->manager->get_user());
						Request::set_get('id', $page->get_type_object_id());
						Request::set_get('end_game', 1);
						$html[] = $game_manager->factory(GameManager::GAME_VIEWER)->get_html(false);
						break;
					case LessonPage::VIDEO_TYPE:
						require_once Path::get_path() . 'pages/video/lib/video_manager.class.php';
						$video_manager = new VideoManager($this->manager->get_user());
						Request::set_get('id', $page->get_type_object_id());
						if($page->get_next())
						{
							$next_page = "";
							if($this->page_nr != $count_pages)
							{
								$next_page = "view_lesson%26id=" . $this->id . "%26page_nr=" .($this->page_nr+1) .($this->coach?"%26coach=1":"");
							}
							Request::set_get('next_page', $next_page);
						}
						$html[] = $video_manager->factory(VideoManager::VIDEO_VIEWER)->get_html(false);
						break;
					case LessonPage::QUESTION_TYPE:
						require_once Path::get_path() . 'pages/question/lib/question_manager.class.php';
						$question_manager = new QuestionManager($this->manager->get_user());
						Request::set_get('id', $page->get_type_object_id());
						$html[] = $question_manager->factory(QuestionManager::QUESTION_VIEWER)->get_html(false);
						break;
					case LessonPage::SELECTION_TYPE:
						require_once Path::get_path() . 'pages/selection/lib/selection_manager.class.php';
						$selection_manager = new SelectionManager($this->manager->get_user());
						Request::set_get('id', $page->get_type_object_id());
						$html[] = $selection_manager->factory(SelectionManager::SELECTION_VIEWER)->get_html(false);
						break;
				}
			}
			else
				$html[] = '<p class="error">' . Language::get_instance()->translate(240) . '</p>';
		}
		else
			$html[] = '<p class="error">' . Language::get_instance()->translate(85) . '</p>';
		$html[] = '</div>';
		return implode("\n",$html);
	}
}
?>