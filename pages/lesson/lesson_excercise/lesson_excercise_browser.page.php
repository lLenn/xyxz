<?php

class LessonExcerciseBrowser
{
	private $manager;
	private $id;
	private $coach;
	private $write_right;
	private $read_right;
	private $object_right = RightManager::NO_RIGHT;
	private $tab = 0;
	
	function LessonExcerciseBrowser($manager)
	{
		$this->manager = $manager;
		$this->id = Request::get("id");
		$this->coach = Request::get("coach");
		
		if(is_null($this->id) || !is_numeric($this->id))
		{
			$this->id = 0;
		}
		
		if(is_null($this->coach) || !is_numeric($this->coach))
		{
			$this->coach = 0;
		}
		elseif($this->coach != 0)
		{
			$this->coach = 1;
		}
			
		$this->write_right = RightManager::instance()->check_right_location(RightManager :: WRITE_RIGHT, "lesson", $this->manager->get_user());
		$this->read_right = RightManager::instance()->check_right_location(RightManager :: READ_RIGHT, "lesson", $this->manager->get_user());
		if($this->id != 0)
			$this->object_right = RightManager::instance()->get_right_location_object(RightManager::LESSON_EXCERCISE_LOCATION_ID, $this->manager->get_user(), $this->id);
	}
	
	public function save_changes()
	{
		$html = array();
		if(Request::get("send_mail")==1)
		{
			$excs = $this->manager->get_data_manager()->retrieve_visible_and_new_lesson_excercises_by_user_id($this->manager->get_user()->get_id());
			if(count($excs))
			{
				foreach($excs as $exc)
				{
					Mail::send_available_email($exc);
				}
				header("location: " . Url::create_url(array("page"=>"browse_excercises", "message_type"=>"good", "message"=>924)));
			}
			else
				header("location: " . Url::create_url(array("page"=>"browse_excercises", "message_type"=>"error", "message"=>925)));
		}
		
		if(!empty($_POST))
		{
			if(!is_null(Request::post('save_all')))
			{
				$map_id = Request::post("map_id");
				$rels_count = $this->manager->get_data_manager()->count_lesson_excercises_by_user_id($this->manager->get_user()->get_id(), $map_id);
				$status = true;
				$filter = array();
				for($i=1;$i<=$rels_count;$i++)
				{
					$id = Request::post("id_" . $i);
					if($id)
					{
						$rel = $this->manager->get_data_manager()->retrieve_lesson_excercise_by_id($id, $this->manager->get_user()->get_id());
						$rel->set_order($i);
						$rel->set_visible($this->manager->get_data_manager()->parse_checkbox_value(Request::post("visible_" . $i)));
						$rel->set_new($this->manager->get_data_manager()->parse_checkbox_value(Request::post("new_" . $i)));
						$status &= $this->manager->get_data_manager()->update_lesson_excercise_visible_and_order($rel);
						$filter[] = $id;
					}
					else
					{
						$status &= $this->manager->get_data_manager()->delete_other_lesson_excercises($this->manager->get_user()->get_id(), $filter, $map_id);
						break;
					}
				}
				if($status)
				{ 
					$html[] = '<p class="good">' . Language::get_instance()->translate(227) . '</p>';
				}
				else
				{
					$html[] = '<p class="error">' . Language::get_instance()->translate(228) . '</p>';
				}
				if($this->id != 0)
					$this->tab = 1;
			}
			elseif(!is_null(Request::post('save_all_lesson_excercise_relation')) && $this->object_right == RightManager::UPDATE_RIGHT)
			{
				$rels_count = $this->manager->get_data_manager()->count_lesson_excercise_components($this->id);
				$status = true;
				$filter = array();
				for($i=1;$i<=$rels_count;$i++)
				{
					$ids = Request::post("id_" . $i);
					if($ids)
					{
						$ids = explode('&', $ids);
						$id = $ids[0];
						$status &= $this->manager->get_data_manager()->update_lesson_excercise_component_order($id, $i);
						$filter[] = $id;
					}
					else
					{
						$status &= $this->manager->get_data_manager()->delete_other_lesson_excercise_components($this->id, $filter);
						break;
					}
				}
				if($status) $html[] = '<p class="good">' . Language::get_instance()->translate(1010) . '</p>';
				else		$html[] = '<p class="error">' . Language::get_instance()->translate(1011) . '</p>';
			}
		}
		$maps = RightDataManager::instance(null)->retrieve_location_user_maps(RightManager::LESSON_EXCERCISE_LOCATION_ID,$this->manager->get_user()->get_id());
		$this->manager->get_data_manager()->add_new_lesson_excercises($this->manager->get_user()->get_id(), $maps);
		if(Request::get("switched_map"))
			$this->manager->get_data_manager()->rearrange_lesson_excercise_order($maps);
		return implode("\n", $html);
	}
	
	public function get_html()
	{
		$html = array();	
			
		$user = $this->manager->get_user();
		if($this->write_right && $this->coach == 0)
		{
			$display_message = Display::get_message();
			$changes = $this->save_changes();
			$lesson_excercise = $this->manager->get_data_manager()->retrieve_lesson_excercise_by_id($this->id, $this->manager->get_user()->get_id());
			$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'pages/lesson/lesson_excercise/javascript/lesson_excercise_browser.js"></script>';
			$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'core/javascript/editable.js"></script>';
			$html[] = '<div id="lesson_info">'; //li
			
			$html[] = '<div id="tabs">'; //tb
			$html[] = '<ul width="95%">';
			if(!is_null($lesson_excercise))
			{
				$html[] = '<li><a id="details_tab" href="#lesson_excercise_details">' . Language::get_instance()->translate(74) . '</a></li>';
			}
			$html[] = '<li><a id="general_tab" href="#general">' . Language::get_instance()->translate(75) . '</a></li>';
			$html[] = '</ul>';
			if(!is_null($lesson_excercise))
			{
				$html[] = '<div id="lesson_excercise_details">';	
				if($this->tab == 0)
				{
					$html[] = $display_message;
					$html[] = $changes;
				}	
				$html[] = '<h3 class="title">' . Language::get_instance()->translate(149) . '</h3>';
				$html[] = $this->manager->get_renderer()->get_description_lesson_excercise($lesson_excercise);
				$html[] = '<p style="vertical-align:top;font-style:italic;font-size:11px;">';
				if($this->object_right==RightManager::UPDATE_RIGHT)
					$html[] = Language::get_instance()->translate(213);
				else
					$html[] = "&nbsp;";
				$html[] = '</p>';
				
				$html[] = '<div id="relation_table">';
				$html[] = $this->manager->get_renderer()->get_relation_table($this->id, ($this->object_right==RightManager::UPDATE_RIGHT?true:false));
				$html[] = '</div>';
				
				$html[] = '</div>';
				$html[] = '<script type="text/javascript">';
		   	 	$html[] = '  var lesson_excercise_id = ' . $this->id . ';';
		    	$html[] = '</script>';
			}
			$html[] = '<div id="general">'; //ge
			if($this->id == 0 || $this->tab == 1)
			{
				$html[] = $display_message;
				$html[] = $this->save_changes();
			}
			$html[] = '<p style="vertical-align:top;font-style:italic;font-size:11px;">';
			$html[] = Language::get_instance()->translate(229) . '<br/>';
			$html[] = Language::get_instance()->translate(230) . '<br/>';
			$html[] = Language::get_instance()->translate(216) . "<br/>";
			$html[] = Language::get_instance()->translate(231) . '<br/>';
			$html[] = '</p>';
			
			$html[] = "<div style='float: right; margin-top: 10px;'>";
			$html[] = "<a class='text_link' href='" . Url::create_url(array("page"=>"browse_excercises", "send_mail"=>1)) . "' title='" . Language::get_instance()->translate(923) . "'>" . Language::get_instance()->translate(921) . "</a>";
			$html[] = "</div>";
			
			$html[] = "<h3 class=\"title\">" . Language::get_instance()->translate(162) . "</h3>";
			$html[] = '<div id="lesson_excercise_div">'; //le
			$maps = RightDataManager::instance(null)->retrieve_location_user_maps(RightManager::LESSON_EXCERCISE_LOCATION_ID,$this->manager->get_user()->get_id());
			foreach($maps as $map)
			{
				$html[] = RightRenderer::render_map($this->manager->get_user(), $map, 9, $this->manager->get_renderer()->get_lesson_excercise_table($this->manager->get_user()->get_id(), true, false, $map));
			}
			if(count($maps))
			{
				$html[] = "<h3 class=\"title\">" . Language::get_instance()->translate(902) . ":</h3>";
				$html[] = "<div style='margin-left: 40px'>"; //mp
			}
			$html[] = $this->manager->get_renderer()->get_lesson_excercise_table($this->manager->get_user()->get_id(), true, false, "others");
			if(count($maps))
				$html[] = "</div>"; //mp
				
			$html[] = '</div>'; //le
			$html[] = '</div>'; //ge
			
			$html[] = '</div>'; //tb
			$html[] = '</div>'; //li
			
			$html[] = '<script type="text/javascript">';
		    $html[] = '  var tabnumber = ' . $this->tab . ';';
		    $html[] = '</script>';
		}
		elseif($this->read_right)
		{
			$html[] = '<div id="lesson_info">';
			$html[] = '<br/>';
			$html[] = '<p style="vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(232) . '</p>';
			$html[] = $this->manager->get_renderer()->get_lesson_excercise_list($user, $this->coach);
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
		if($this->write_right && $this->coach == 0)
		{
			$html = array();
			$html[] = '<p style="vertical-align:top;font-style:italic;font-size:11px;">';
			$html[] = Language::get_instance()->translate(233) . '<br/>';
			$html[] = Language::get_instance()->translate(234);
			$html[] = '</p>';
			return implode(" ", $html);
		}
	}
	
}

?>