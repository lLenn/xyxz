<?php

class LessonExcerciseComponentCreator
{

	private $manager; 
	private $lesson_excercise_component_id;
	private $lesson_excercise_component;
	private $lesson_excercise_id;
	private $object_right = RightManager::NO_RIGHT;

	function LessonExcerciseComponentCreator($manager)
	{
		$this->manager = $manager;
		$this->lesson_excercise_id = Request::get("lesson_excercise_id");
		if(is_null($this->lesson_excercise_id) || !is_numeric($this->lesson_excercise_id))
			$this->lesson_excercise_id = 0;
		$this->lesson_excercise_component_id = null; //Request::get("id");
		$this->object_right = RightManager::instance()->get_right_location_object(RightManager::LESSON_EXCERCISE_LOCATION_ID, $this->manager->get_user(), $this->lesson_excercise_id);
	}
	
	public function save_changes()
	{
		$html = array();
		$success = false;
		$mesage = "";
		if(!empty($_POST))
		{			
			$lesson_excercise_components = $this->manager->get_data_manager()->retrieve_lesson_excercise_components_from_post();		
			if(Error::get_instance()->get_result() && Request::get('id')==0)
			{
				$success = true;
				foreach($lesson_excercise_components as $lesson_excercise_component)
					$success &= $this->manager->get_data_manager()->insert_lesson_excercise_component($lesson_excercise_component)?true:false;
				if($success)	$message = urlencode(Language::get_instance()->translate(1005));
				else			$html[] = "<p class='error'>" . Language::get_instance()->translate(1003) . "</p>";
			}
			elseif(Error::get_instance()->get_result())
			{
				$success = $this->manager->get_data_manager()->update_lesson_excercise_component($this->lesson_excercise_component);
				if($success)	$message = urlencode(Language::get_instance()->translate(1006));
				else			$html[] = "<p class='error'>" . Language::get_instance()->translate(1004) . "</p>";
			}
			else
				$html[] = Error::get_instance()->print_error(true);
		}
		if($success)
		{
			header("Location: " . Url :: create_url(array("page" => "browse_excercises", "id" => $this->lesson_excercise_id, "message" => $message, "message_type" => "good")));  
			exit;
		}
		return implode("\n", $html);
	}
	
	public function get_html()
	{
		$html = array();
		if($this->object_right == RightManager::UPDATE_RIGHT)
		{
			$this->lesson_excercise_component = null; //$this->manager->get_data_manager()->retrieve_lesson_excercise_component($this->lesson_excercise_component_id);
			$html[] = '<script src="' . Path :: get_url_path() . 'pages/lesson/lesson_excercise/javascript/lesson_excercise_component_creator.js" type="text/javascript"></script>';
			$html[] = '<div id="lesson_excercise_info">';
			$html[] = $this->save_changes();
			$html[] = '<form action="" method="post" id="lesson_excercise_component_creator_form">';
			$html[] = '<div id="record">';
			if(!is_null($this->lesson_excercise_component))
				$html[] = '<input type="hidden" name="order" value="'.$this->lesson_excercise_component->get_order().'">';
			$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(246) . ' :</div>';
			$html[] = '<div class="record_input">';
			$type = 0;
			if(!is_null($this->lesson_excercise_component))
				$type = $this->lesson_excercise_component->get_type();
			$html[] = $this->manager->get_renderer()->get_type_selector($type);
			$html[] = '</div>';
			$html[] = '<div id="type_form">';
			if(!is_null($this->lesson_excercise_component))
			{
				$add_new = "";
			    switch($this->lesson_excercise_component->get_type())
			    {
			    	case LessonExcerciseComponent::PUZZLE_TYPE : $add_new = Language::get_instance()->translate(1180);
			    							 	   break;
			    	case LessonExcerciseComponent::QUESTION_TYPE : $add_new = Language::get_instance()->translate(1183);
			    							 	 break;
			    	case LessonExcerciseComponent::SELECTION_TYPE : $add_new = Language::get_instance()->translate(1185);
			    							 	 break;
			    }
			    
				$html[] = "<br class='clearfloat'/>";
				$html[] = "<div class='record_name_required'>" . $add_new . " :</div>";
				$html[] = "<div id='create_object_button'>";
				$html[] = '<div class="record_button"><a id="create_object" class="link_button" href="javascript:;"> ' . Language::get_instance()->translate(1186) . '</a></div>';
				$html[] = "</div>";
				$html[] = "<div id='create_object_holder' class='record_input' style='position: relative;'>";
				$html[] = "</div>";
				$html[] = "<br class='clearfloat'/>";
				$html[] = "<div id='created_objects_holder' style='display: none;'>";
			    $html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(200) . " :</div>";
				$html[] = "<div class='record' id='created_objects_input'></div><br class='clearfloat'/><br/>";
			    $html[] = "</div>";
			    $html[] = "<div id='search_holder'>";
			    switch($this->lesson_excercise_component->get_type())
			    {
			    	case LessonExcerciseComponent::PUZZLE_TYPE : $html[] = $this->manager->get_parent_manager()->get_renderer()->get_lesson_page_puzzle_form($this->lesson_excercise_component, false);
			    							 	   break;
			    	case LessonExcerciseComponent::QUESTION_TYPE : $html[] = $this->manager->get_parent_manager()->get_renderer()->get_lesson_page_question_form($this->lesson_excercise_component, false);
			    							 	 break;
			    	case LessonExcerciseComponent::SELECTION_TYPE : $html[] = $this->manager->get_parent_manager()->get_renderer()->get_lesson_page_selection_form($this->lesson_excercise_component, false);
			    							 	 break;
			    }
				$html[] = '</div>';	
			}
			$html[] = '</div>';	
			$html[] = '<div class="record_button"><a id="submit_form" class="link_button" href="javascript:;" ' . (is_null($this->lesson_excercise_component)?'style="display: none;"':'') . '> ' . (is_null($this->lesson_excercise_component) || $this->lesson_excercise_component->get_id() == 0?Language::get_instance()->translate(49):Language::get_instance()->translate(56)) . '</a></div>';
			$html[] = '</div>';	
			$html[] = '</form>';
			$html[] = '</div>';	
			
			$html[] = '<script type="text/javascript">';
			if(!is_null($this->lesson_excercise_component) && is_numeric($this->lesson_excercise_component->get_type()))
				$html[] = '  var current_type_id = ' . $this->lesson_excercise_component->get_type() . ';';
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