<?php

class LessonContinuationAvailability
{
	const BROWSER = 1;
	const ADD_LESSON = 2;
	const ADD_LESSON_EXCERCISE = 3;
	
	const TYPE_LESSON = 10;
	const TYPE_LESSON_EXCERCISE = 11;
	
	private $manager;
	private $mode = self::BROWSER;
	private $tab = 0;
	
	function LessonContinuationAvailability($manager, $mode = self::BROWSER)
	{
		$this->manager = $manager;
		$this->mode = $mode;
		$this->tab = Request::get("tab");
		if(is_null($this->tab) || !is_numeric($this->tab) || $this->tab < 0 || $this->tab > 1)
			$this->tab = 0;
	}
	
	public function save_changes()
	{
		$html = array();
		if(!empty($_POST))
		{
			if($this->mode == self::ADD_LESSON || $this->mode == self::ADD_LESSON_EXCERCISE)
			{
				$type_id = self::TYPE_LESSON;
				$success_message = urlencode(Language::get_instance()->translate(1061));
				$error_message = Language::get_instance()->translate(1062);
				$tab = 0;
				if($this->mode == self::ADD_LESSON_EXCERCISE)
				{
					$type_id = self::TYPE_LESSON_EXCERCISE;
					$success_message = urlencode(Language::get_instance()->translate(1063));
					$error_message = Language::get_instance()->translate(1064);
					$tab = 1;
				}
				$success = true;
				foreach(Request::post("object_id") as $object_id)
				{
					$success &= $this->manager->get_data_manager()->insert_lesson_continuation_available_object($type_id, $object_id)?true:false;
				}
				if($success)
				{
					header("Location: " . Url :: create_url(array("page" => "admin_continuations", "message" => $success_message, "message_type" => "good", "tab" => $tab)));  
					exit;
				}
				else
					$html[] = "<p class='error'>" . $error_message . "</p>";
			}
			else
			{
				$filter = array();
				foreach($_POST as $index => $data)
				{
					if(substr($index, 0, 2) == "id")
						$filter[] = $data;
				}
				$type_id = self::TYPE_LESSON;
				$success_message = Language::get_instance()->translate(1065);
				$error_message = Language::get_instance()->translate(1066);
				if(!is_null(Request::post("save_all_lesson_excercise_av_table")))
				{
					$this->tab = 1;
					$type_id = self::TYPE_LESSON_EXCERCISE;
					$success_message = Language::get_instance()->translate(1067);
					$error_message = Language::get_instance()->translate(1068);
				}
				if($this->manager->get_data_manager()->delete_other_lesson_continuation_available_objects($filter, $type_id))
					$html[] = "<p class='good'>" . $success_message . "</p>";
				else
					$html[] = "<p class='error'>" . $error_message . "</p>";
			}
		}
		return implode("\n", $html);
	}
	
	public function get_html()
	{
		$html = array();
		if($this->manager->get_user()->is_admin())
		{
			$html[] = '<div id="lesson_info">';
			if($this->mode == self::BROWSER)
			{
				$changes = $this->save_changes();
				$display_message = Display::get_message();
				$html[] = '<div id="tabs">';
				
				$html[] = '<ul width="95%">';
				$html[] = '<li><a id="lessons_tab" href="#lessons">' . Language::get_instance()->translate(16) . '</a></li>';
				$html[] = '<li><a id="lesson_excercises_tab" href="#lesson_excercises">' . Language::get_instance()->translate(18) . '</a></li>';
				$html[] = '</ul>';
				
				$html[] = '<div id="lessons">';	
				if($this->tab == 0)
				{
					$html[] = $display_message;
					$html[] = $changes;
				}
				$html[] = $this->manager->get_renderer()->get_lesson_continuation_available_table(self::TYPE_LESSON);
				$html[] = '</div>';

				$html[] = '<div id="lesson_excercises">';
				if($this->tab == 1)
				{
					$html[] = $display_message;
					$html[] = $changes;
				}
				$html[] = $this->manager->get_renderer()->get_lesson_continuation_available_table(self::TYPE_LESSON_EXCERCISE);
				$html[] = '</div>';
				
				$html[] = '</div>';
					
				$html[] = '<script type="text/javascript">';
			    $html[] = '  var tabnumber = ' . $this->tab . ';';
			    $html[] = '</script>';
			}
			elseif ($this->mode == self::ADD_LESSON || $this->mode == self::ADD_LESSON_EXCERCISE)
			{
				$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'pages/lesson/javascript/lesson_continuation_availability.js"></script>';
				$title = Language::get_instance()->translate(1060);
				if($this->mode == self::ADD_LESSON_EXCERCISE)
					$title = Language::get_instance()->translate(1059);
				$html[] = '<p><h3 class="title">' . $title . ':</h3></p>';
				Language::get_instance()->add_section_to_translations(Language::THEME);
				$html[] = '<div id="continuation_form">';
				$html[] = $this->save_changes();
				$html[] = '<div id="continuation_search_form">';
				$html[] = '<form action="" method="post" id="lesson_continuation_search_form">';
				$html[] = '<input type="hidden" name="type_id" value="' . $this->mode . '"/>';
				$html[] = $this->manager->get_renderer()->get_lesson_continuation_availability_search($this->mode);
				$html[] = '</form>';
				$html[] = '</div>';
				$html[] = '<div id="continuation_search_result">';
				$html[] = '</div>';
				$html[] = '</div>';
			}
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