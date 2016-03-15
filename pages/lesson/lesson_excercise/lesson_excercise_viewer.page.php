<?php

class LessonExcerciseViewer
{
	private $manager;
	private $id;
	//private $page_nr;
	///private $coach;
	
	function LessonExcerciseViewer($manager)
	{
		$this->manager = $manager;
		$this->id = Request::get("id");
		//$this->page_nr = Request::get("page_nr");
		$this->coach = Request::get("coach");
		/*
		if(is_null($this->id) || !is_numeric($this->id))
			$this->id = 0;
		if(is_null($this->page_nr) || !is_numeric($this->page_nr))
			$this->page_nr = 1;
		if(is_null($this->coach) || !is_numeric($this->coach))
			$this->coach = 0;
		elseif($this->coach != 0)
			$this->coach = 1;
		*/
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
		$html[] = '<div id="lesson_excercise_info">';
		if($this->coach == 0 && !is_null($user->get_parent_id()))
		{
			$user = UserDataManager::instance(null)->retrieve_user($user->get_parent_id());
		}
		if(!is_null($user) || $this->manager->get_data_manager()->is_lesson_course_excercise($this->id))
		{
			if($this->id != 0)
			{
				$group_id = 0;
				if(!is_null($this->manager->get_user()->get_group()))
					$group_id = $this->manager->get_user()->get_group_id();
	        	$pupil = $group_id==GroupManager::GROUP_PUPIL_ID?1:0;

	        	$id = html5Helper::storeObject("excercise", $this->id);
	        	$attempt = $this->manager->get_data_manager()->retrieve_excercise_attempt($this->id, $this->manager->get_user()->get_id());
	        	$previous_page = "browse_excercises%26id=".$this->id;
				if($group_id == GroupManager::GROUP_PUPIL_ID)
					$previous_page = "browse_excercises";

				$html[] = HTML5Helper::loadChessboardScripts();
				$html[] = '<div style="padding-top: 10px;" id="ExcerciseViewerBoard">';
				$html[] = '</div>';
				$html[] = '<script type="text/javascript">';
				$html[] = '  var appArgs = {centerToScreen: true, mobileFullscreen: true}';
				$html[] = '  var args = {appName: "ExcerciseViewer", objectSerial: "' . $id[0] . '", attempt: ' . $attempt . ', previousPage: "' . $previous_page . '"};';
				$html[] = '  var board = new chssBoard(document.getElementById("ExcerciseViewerBoard"), appArgs, args);';
				$html[] = ' board.initiate();';
				$html[] = '</script>';
				/*
				$html[] = '<script type="text/javascript">';
				$html[] = '  var appName = "ExcerciseViewer";';
				$html[] = '  var language = "' . Language::get_instance()->get_language() . '";';
				$html[] = '  var excerciseId = ' . $this->id . ';';
				$html[] = '  var userId = ' . $this->manager->get_user()->get_id() . ';';
				$html[] = '  var pupil = ' . 1 . ';';
				$html[] = '  var attempt = ' . $this->manager->get_data_manager()->retrieve_excercise_attempt($this->id, $this->manager->get_user()->get_id()) . ';';
				$html[] = '  var previousPage = "' . $previous_page . '";';
				$html[] = '  var flashvars = "appName="+appName+"&excerciseId="+excerciseId+"&attempt="+attempt+"&userId="+userId+"&previousPage="+previousPage+"&pupil="+pupil+"&language="+language;';
				$html[] = '</script>';
				$html[] = '<div style="padding-top: 10px;">';
				include Path::get_path() . "/flash/main.php";
				$html[] = '</div>';
				*/
			}
			/*
			$exc = $this->manager->get_data_manager()->retrieve_lesson_excercise_by_id($this->id, $user->get_id());
			if(!is_null($exc))
			{
				$count_pages = 0;
				if($exc->get_question_set_id())
					$count_pages++;
				if($exc->get_set_id())
					$count_pages++;
				if($exc->get_selection_set_id())
					$count_pages++;
				if($count_pages > 1)
				{
					$style_extra = '';
					if($this->page_nr!=1)
						$style_extra = ' style="margin-left: 475px;"';
					$html[] = '<div id="lesson_excercise_progress"'.$style_extra.'>';
					$url_arr = array('page' => 'view_excercise', 'id' => $this->id);
					if($this->coach)
						$url_arr['coach'] = $this->coach;
					if($this->page_nr!=1)
					{
						$url_arr['page_nr'] = $this->page_nr-1;
						$html[] = '<a href="' . Url::create_url($url_arr) . '">';
						$html[] = '<img style="float: left; margin-right: 5px; padding: 0; border: 0;" src="' . Path::get_url_path() . 'layout/images/buttons/progress_left.png"/>';
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
						$html[] = '<img style="float: left; margin-right: 5px; padding: 0; border: 0;" src="' . Path::get_url_path() . 'layout/images/buttons/progress_right.png"/>';
						$html[] = '</a>';
					}
					$html[] = '<br class="clearfloat"/>';
					$html[] = '</div>';
				}
				
				Request::set_get('previous_page', 'browse_excercises' . ($this->coach?'%26coach=1':''));
				$case = "selection";
				if($this->page_nr == 1 && $exc->get_question_set_id())
					$case = "question";
				if(($this->page_nr == 1 && !$exc->get_question_set_id() && $exc->get_set_id()) ||
				   ($this->page_nr == 2 && $exc->get_question_set_id() && $exc->get_set_id()))
					$case = "puzzle";
				switch($case)
				{
					case "question":
					require_once Path::get_path() . 'pages/question/lib/question_manager.class.php';
					$set_manager = new QuestionManager($this->manager->get_user());
					Request::set_get('id', $exc->get_question_set_id());
					if($count_pages >= 2)
					{
						Request::set_get('previous_page', 'view_excercise%26id=' . $this->id . '%26page_nr=2' . ($this->coach?"%26coach=1":""));
					}
					$html[] = $set_manager->factory(QuestionManager::QUESTION_SET_VIEWER)->get_html();
					break;	
					
					case "puzzle":
					require_once Path::get_path() . 'pages/puzzle/lib/puzzle_manager.class.php';
					$set_manager = new PuzzleManager($this->manager->get_user());
					Request::set_get('id', $exc->get_set_id());
					if($count_pages >= 2)
					{
						Request::set_get('previous_page', 'view_excercise%26id=' . $this->id . '%26page_nr=' . ($this->page_nr+1) . ($this->coach?"%26coach=1":""));
					}
					$html[] = $set_manager->factory(PuzzleManager::PUZZLE_SET_VIEWER)->get_html();
					break;		
				
					case "selection":
					require_once Path::get_path() . 'pages/selection/lib/selection_manager.class.php';
					$set_manager = new SelectionManager($this->manager->get_user());
					Request::set_get('id', $exc->get_selection_set_id());
					$html[] = $set_manager->factory(SelectionManager::SELECTION_SET_VIEWER)->get_html();
					break;	
				}
			}
			else
				$html[] = '<p class="error">' . Language::get_instance()->translate(240) . '</p>';
			*/
		}
		else
			$html[] = '<p class="error">' . Language::get_instance()->translate(85) . '</p>';
		$html[] = '</div>';
		return implode("\n",$html);
	}
}
?>