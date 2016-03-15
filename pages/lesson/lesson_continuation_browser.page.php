<?php

class LessonContinuationBrowser
{
	private $manager;
	private $view_continuations = false;
	
	function LessonContinuationBrowser($manager)
	{
		$this->manager = $manager;
		$this->view_continuations = Request::get("view");
		if($this->view_continuations == 1)
			$this->view_continuations = true;
		else
			$this->view_continuations = false;
	}
	
	public function save_changes()
	{
		$html = array();
		if(!empty($_POST))
		{
			$filter = array();
			foreach($_POST as $index => $data)
			{
				if(substr($index, 0, 2) == "id")
					$filter[] = $data;
			}
			$success_message = Language::get_instance()->translate(1079);
			$error_message = Language::get_instance()->translate(1080);
			if($this->manager->get_data_manager()->delete_other_lesson_continuations($filter))
				$html[] = "<p class='good'>" . $success_message . "</p>";
			else
				$html[] = "<p class='error'>" . $error_message . "</p>";
		}
		return implode("\n", $html);
	}
	
	public function get_html()
	{
		$html = array();
		$html[] = '<div id="lesson_continuation_info">';
		$html[] = Display::get_message();
		if($this->manager->get_user()->get_group_id() != GroupManager::GROUP_GUEST_ID && $this->manager->get_user()->get_group_id() != GroupManager::GROUP_PUPIL_ID && $this->manager->get_user()->get_group_id() != GroupManager::GROUP_PUPIL_TEST_ID && $this->view_continuations == false)
		{
			$html[] = $this->save_changes();
			$html[] = $this->manager->get_renderer()->get_lesson_continuation_table();
		}
		elseif($this->manager->get_user()->get_group_id() != GroupManager::GROUP_GUEST_ID)
		{
			$html[] = '<script src="' . Path :: get_url_path() . 'pages/lesson/javascript/lesson_continuation_browser.js" type="text/javascript"></script>';
			$html[] = $this->manager->get_renderer()->get_lesson_continuation_list($this->view_continuations);
		}
		else
			$html[] = '<p class="error">' . Language::get_instance()->translate(85) . '</p>';
		$html[] = '</div>';
		return implode("\n",$html);
	}
	
	public function get_title()
	{
		return "";
	}
	
	public function get_description()
	{
		return '<p style="vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(226) . '</p>';
	}
	
}

?>