<?php

class LessonPageCreator
{

	private $manager; 
	private $lesson_page_id;
	private $lesson_page;
	private $lesson_id;
	private $object_right = RightManager::NO_RIGHT;

	function LessonPageCreator($manager)
	{
		$this->manager = $manager;
		$this->lesson_id = Request::get("lesson_id");
		if(is_null($this->lesson_id) || !is_numeric($this->lesson_id))
			$this->lesson_id = 0;
		$this->lesson_page_id = Request::get("id");
		$this->object_right = RightManager::instance()->get_right_location_object(RightManager::LESSON_LOCATION_ID, $this->manager->get_user(), $this->lesson_id);
	}
	
	public function save_changes()
	{
		$html = array();
		$success = false;
		$mesage = "";
		if(!empty($_POST))
		{
			if(Request::post("type_id") == 1)
			{
				$page_text = $this->manager->get_data_manager()->retrieve_lesson_page_text_from_post();
				if(Error::get_instance()->get_result() && Request::post('object_id')==0)
				{
					$success = $this->manager->get_data_manager()->insert_lesson_page_text($page_text);
					if(!$success) $html[] = "<p class='error'>" . Language::get_instance()->translate(241) . "</p>";
					else Request::set_post('object_id', $success);
				}
				elseif(Error::get_instance()->get_result())
				{
					$success = $this->manager->get_data_manager()->update_lesson_page_text($page_text);
					if(!$success) $html[] = "<p class='error'>" . Language::get_instance()->translate(242) . "</p>";
				}
				else
					$html[] = Error::get_instance()->print_error(true);
			}
			else
				$success = true;
			
			$this->lesson_page = $this->manager->get_data_manager()->retrieve_lesson_page_from_post();
			if($success)
			{
				$success = false;
				if(Error::get_instance()->get_result() && Request::get('id')==0)
				{
					$success = $this->manager->get_data_manager()->insert_lesson_page($this->lesson_page);
					if($success)	$message = urlencode(Language::get_instance()->translate(243));
					else			$html[] = "<p class='error'>" . Language::get_instance()->translate(241) . "</p>";
				}
				elseif(Error::get_instance()->get_result())
				{
					$success = $this->manager->get_data_manager()->update_lesson_page($this->lesson_page);
					if($success)	$message = urlencode(Language::get_instance()->translate(244));
					else			$html[] = "<p class='error'>" . Language::get_instance()->translate(242) . "</p>";
				}
				else
					$html[] = Error::get_instance()->print_error(true);
			}
			
		}
		if($success)
		{
			header("Location: " . Url :: create_url(array("page" => "browse_lessons", "id" => $this->lesson_id, "message" => $message, "message_type" => "good")));  
			exit;
		}
		return implode("\n", $html);
	}
	
	public function get_html()
	{
		$html = array();
		if($this->object_right == RightManager::UPDATE_RIGHT)
		{
			$this->lesson_page = $this->manager->get_data_manager()->retrieve_lesson_page($this->lesson_page_id);
			$html[] = '<script src="' . Path :: get_url_path() . 'pages/lesson/javascript/lesson_page_creator.js" type="text/javascript"></script>';
			$html[] = '<div id="lesson_info">';
			$html[] = $this->save_changes();
			$html[] = '<form action="" method="post" id="lesson_page_creator_form">';
			$html[] = '<div id="record">';
			if(!is_null($this->lesson_page))
				$html[] = '<input type="hidden" name="order" value="'.$this->lesson_page->get_order().'">';
			$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(54) . " :</div><div class='record_input'><input type='text' name='title' style='width:300px;' value='". (is_null($this->lesson_page)?'':$this->lesson_page->get_title()) ."'></div><br class='clearfloat'/>";
			$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(246) . ' :</div>';
			$html[] = '<div class="record_input">';
			$type = 0;
			if(!is_null($this->lesson_page))
				$type = $this->lesson_page->get_type();
			$html[] = $this->manager->get_renderer()->get_type_selector($type);
			$html[] = '</div>';
			$html[] = '<div id="type_form">';
			if(!is_null($this->lesson_page))
			{
				$add_new = "";
			    switch($this->lesson_page->get_type())
			    {
			    	case LessonPage::PUZZLE_TYPE : $add_new = Language::get_instance()->translate(1180);
			    							 	   break;
			    	case LessonPage::GAME_TYPE : $add_new = Language::get_instance()->translate(1181);
			    							 	 break;
			    	case LessonPage::VIDEO_TYPE : $add_new = Language::get_instance()->translate(1182);
			    							 	 break;
			    	case LessonPage::QUESTION_TYPE : $add_new = Language::get_instance()->translate(1183);
			    							 	 break;
			    	case LessonPage::END_GAME_TYPE : $add_new = Language::get_instance()->translate(1184);
			    							 	 break;
			    	case LessonPage::SELECTION_TYPE : $add_new = Language::get_instance()->translate(1185);
			    							 	 break;
			    }
			    if($this->lesson_page->get_type()!=LessonPage::TEXT_TYPE)
			    {
					$html[] = "<br class='clearfloat'/>";
					$html[] = "<div class='record_name_required'>" . $add_new . " :</div>";
					$html[] = "<div id='create_object_button'>";
					$html[] = '<div class="record_button"><a id="create_object" class="link_button" href="javascript:;"> ' . Language::get_instance()->translate(1186) . '</a></div>';
					$html[] = "</div>";
					$html[] = "<div id='create_object_holder' class='record_input' style='position: relative;'>";
					$html[] = "</div>";
					$html[] = "<br class='clearfloat'/>";
			    }
			    $html[] = "<div id='search_holder'>";
			    switch($this->lesson_page->get_type())
			    {
			    	case LessonPage::TEXT_TYPE : $html[] = $this->manager->get_renderer()->get_lesson_page_text_form($this->lesson_page);
			    							 	 break;
			    	case LessonPage::PUZZLE_TYPE : $html[] = $this->manager->get_renderer()->get_lesson_page_puzzle_form($this->lesson_page);
			    							 	   break;
			    	case LessonPage::GAME_TYPE : $html[] = $this->manager->get_renderer()->get_lesson_page_game_form($this->lesson_page);
			    							 	 break;
			    	case LessonPage::VIDEO_TYPE : $html[] = $this->manager->get_renderer()->get_lesson_page_video_form($this->lesson_page);
			    							 	 break;
			    	case LessonPage::QUESTION_TYPE : $html[] = $this->manager->get_renderer()->get_lesson_page_question_form($this->lesson_page);
			    							 	 break;
			    	case LessonPage::END_GAME_TYPE : $html[] = $this->manager->get_renderer()->get_lesson_page_end_game_form($this->lesson_page);
			    							 	 break;
			    	case LessonPage::SELECTION_TYPE : $html[] = $this->manager->get_renderer()->get_lesson_page_selection_form($this->lesson_page);
			    							 	 break;
			    }
				$html[] = '</div>';	
			}
			$html[] = '</div>';	
			$html[] = '<div class="record_button"><a id="submit_form" class="link_button" href="javascript:;" ' . (is_null($this->lesson_page)?'style="display: none;"':'') . '> ' . (is_null($this->lesson_page) || $this->lesson_page->get_id() == 0?Language::get_instance()->translate(49):Language::get_instance()->translate(56)) . '</a></div>';
			$html[] = '</div>';	
			$html[] = '</form>';
			$html[] = '</div>';	
			
			$html[] = '<script type="text/javascript">';
			if(!is_null($this->lesson_page) && is_numeric($this->lesson_page->get_type()))
				$html[] = '  var current_type_id = ' . $this->lesson_page->get_type() . ';';
			else
				$html[] = '  var current_type_id = 0;';
			$html[] = '</script>';
		}
		else
			$html[] = '<p class="error">' . Language::get_instance()->translate(85) . '</p>';
		return implode("\n", $html);
	}
	
	public function get_title()
	{
		return "";
	}
	
	public function get_description()
	{
		return '<p style="vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(245) . '</p>';
	}
}
?>