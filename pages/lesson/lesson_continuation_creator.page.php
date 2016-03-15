<?php

class LessonContinuationCreator
{
	private $manager;
	private $id;
	private $user_id;
	private $lesson_continuation = null;
	
	function LessonContinuationCreator($manager)
	{
		$this->manager = $manager;
		$this->id = Request::get("id");
		if(is_null($this->id) || !is_numeric($this->id))
			$this->id = 0;
		$this->user_id = Request::get("user_id");
		if(is_null($this->user_id) || !is_numeric($this->user_id))
			$this->user_id = 0;
	}
	
	public function save_changes()
	{
		$html = array();
		if(!empty($_POST))
		{
			$this->lesson_continuation = $this->manager->get_data_manager()->retrieve_lesson_continuation_from_post();
			if(Error::get_instance()->get_result())
			{
				$success = $this->manager->get_data_manager()->insert_lesson_continuation($this->lesson_continuation);
				if($success)
				{
					header("Location: " . Url :: create_url(array("page" => "view_statistics", "id" => $this->user_id, "message" => urlencode(Language::get_instance()->translate(1071)), "message_type" => "good")));  
					exit;
				}
				else
					$html[] = "<p class='error'>" . Language::get_instance()->translate(1072) . "</p>";
			}
			else
				$html[] = "<p class='error'>" . Error::get_instance()->get_message() . "</p>";
		}
		return implode("\n", $html);
	}
	
	public function get_html()
	{
		$html = array();
		$user = UserDataManager::instance($this->manager)->retrieve_user_by_right($this->user_id, RightManager::READ_RIGHT);
		if(!is_null($user))
		{
			if($this->id != 0)
				$this->lesson_continuation = $this->manager->get_data_manager()->retrieve_lesson_by_user_id($this->id, $this->manager->get_user()->get_id());
			
			$html[] = '<div id="lesson_info">';
			$html[] = $this->save_changes();
			$html[] = $this->manager->get_renderer()->get_lesson_continuation_form($user, $this->lesson_continuation);
			$html[] = '</div>';
		}
		else
			$html[] = '<p class="error">' . Language::get_instance()->translate(85) . '</p>';
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