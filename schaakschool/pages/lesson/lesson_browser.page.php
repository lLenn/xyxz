<?php
class LessonBrowser
{
	private $manager;
	private $id;
	private $coach;
	private $write_right;
	private $read_right;
	private $object_right = RightManager::NO_RIGHT;
	private $tab = 0;
	function LessonBrowser($manager)
	{
		$this->manager = $manager;
		$this->id = Request::get("id");
		$this->coach = Request::get("coach");
		if(is_null($this->id) || !is_numeric($this->id))
			$this->id = 0;
		if(is_null($this->coach) || !is_numeric($this->coach))
			$this->coach = 0;
		elseif($this->coach != 0)
			$this->coach = 1;
		$this->write_right = RightManager::instance()->check_right_location(RightManager :: WRITE_RIGHT, "lesson", $this->manager->get_user());
		$this->read_right = RightManager::instance()->check_right_location(RightManager :: READ_RIGHT, "lesson", $this->manager->get_user());
		if($this->id != 0)
			$this->object_right = RightManager::instance()->get_right_location_object(RightManager::LESSON_LOCATION_ID, $this->manager->get_user(), $this->id);
	}
	
	public function save_changes()
	{
		$html = array();
		if(Request::get("send_mail")==1)
		{
			$lessons = $this->manager->get_data_manager()->retrieve_visible_and_new_lessons_by_user_id($this->manager->get_user()->get_id());
			if(count($lessons))
			{
				foreach($lessons as $lesson)
				{
					Mail::send_available_email($lesson);
				}
				header("location: " . Url::create_url(array("page"=>"browse_lessons", "message_type"=>"good", "message"=>924)));
			}
			else
				header("location: " . Url::create_url(array("page"=>"browse_lessons", "message_type"=>"error", "message"=>925)));
		}
		
		if(!empty($_POST))
		{
			if(!is_null(Request::post('save_all_lesson')))
			{
				$map_id = Request::post("map_id");
				$lessons_count = $this->manager->get_data_manager()->count_lessons(null, $map_id);
				$status = true;
				$filter = array();
				for($i=1;$i<=$lessons_count;$i++)
				{
					$id = Request::post("id_" . $i);
					if($id)
					{
						$lesson = $this->manager->get_data_manager()->retrieve_lesson_by_user_id($id, $this->manager->get_user()->get_id());
						$lesson->set_order($i);
						$lesson->set_visible($this->manager->get_data_manager()->parse_checkbox_value(Request::post("visible_" . $i)));
						$lesson->set_new($this->manager->get_data_manager()->parse_checkbox_value(Request::post("new_" . $i)));
						$status &= $this->manager->get_data_manager()->update_lesson_visible_and_order($lesson);
						$filter[] = $id;
					}
					else
					{
						$status &= $this->manager->get_data_manager()->delete_other_lessons($filter, $map_id);
						break;
					}
				}
				if($status) $html[] = '<p class="good">' . Language::get_instance()->translate(209) . '</p>';
				else		$html[] = '<p class="error">' . Language::get_instance()->translate(210) . '</p>';
				if($this->id != 0)
					$this->tab = 1;
			}
		    elseif(!is_null(Request::post('save_all_lesson_relation')) && $this->object_right == RightManager::UPDATE_RIGHT)
			{
				$rels_count = $this->manager->get_data_manager()->count_lesson_pages($this->id);
				$status = true;
				$filter = array();
				for($i=1;$i<=$rels_count;$i++)
				{
					$ids = Request::post("id_" . $i);
					if($ids)
					{
						$ids = explode('&', $ids);
						$id = $ids[0];
						$status &= $this->manager->get_data_manager()->update_lesson_page_order($id, $i);
						$filter[] = $id;
					}
					else
					{
						$status &= $this->manager->get_data_manager()->delete_other_lesson_pages($this->id, $filter);
						break;
					}
				}
				if($status) $html[] = '<p class="good">' . Language::get_instance()->translate(211) . '</p>';
				else		$html[] = '<p class="error">' . Language::get_instance()->translate(212) . '</p>';
			}
		}
		$maps = RightDataManager::instance(null)->retrieve_location_user_maps(RightManager::LESSON_LOCATION_ID,$this->manager->get_user()->get_id());
		$this->manager->get_data_manager()->add_visible_and_order_new_lessons($maps);
		if(Request::get("switched_map"))
			$this->manager->get_data_manager()->rearrange_lesson_order($maps);
		return implode("\n", $html);
	}
	
	public function get_html()
	{
		$html = array();	
			
		$user = $this->manager->get_user();
		if($this->write_right && $this->coach == 0)
		{
			$changes = $this->save_changes();
			$lesson = $this->manager->get_data_manager()->retrieve_lesson_by_user_id($this->id, $this->manager->get_user()->get_id());
			$display_message = Display::get_message();
			$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'pages/lesson/javascript/lesson_browser.js"></script>';
			$html[] = '<div id="lesson_info">';
			$html[] = '<div id="tabs">';
			$html[] = '<ul width="95%">';
			if(!is_null($lesson))
			{
				$html[] = '<li><a id="details_tab" href="#lesson_details">' . Language::get_instance()->translate(74) . '</a></li>';
			}
			$html[] = '<li><a id="general_tab" href="#general">' . Language::get_instance()->translate(75) . '</a></li>';
			$html[] = '</ul>';
			
			if(!is_null($lesson))
			{
				$html[] = '<div id="lesson_details">';	
				if($this->tab == 0)
				{
					$html[] = $display_message;
					$html[] = $changes;
				}	
				$html[] = '<h3 class="title">' . Language::get_instance()->translate(146) . '</h3>';
				$html[] = $this->manager->get_renderer()->get_description_lesson($lesson);
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
		   	 	$html[] = '  var lesson_id = ' . $this->id . ';';
		    	$html[] = '</script>';
			}
			$html[] = '<div id="general">';
			if($this->id == 0 || $this->tab == 1)
			{
				$html[] = $display_message;
				$html[] = $changes;
			}
			$html[] = '<p style="vertical-align:top;font-style:italic;font-size:11px;">';
			$html[] = Language::get_instance()->translate(214) . '<br/>';
			$html[] = Language::get_instance()->translate(215) . '<br/>';
			$html[] = Language::get_instance()->translate(216) . "<br/>";
			$html[] = Language::get_instance()->translate(217) . '<br/>';
			$html[] = '</p>';
			$html[] = "<div style='float: right; margin-top: 10px;'>";
			$html[] = "<a class='text_link' href='" . Url::create_url(array("page"=>"browse_lessons", "send_mail"=>1)) . "' title='" . Language::get_instance()->translate(922) . "'>" . Language::get_instance()->translate(921) . "</a>";
			$html[] = "</div>";
			$html[] = "<h3 class=\"title\">" . Language::get_instance()->translate(139) . "</h3>";
			$html[] = '<div id="lesson_table">';
			$maps = RightDataManager::instance(null)->retrieve_location_user_maps(RightManager::LESSON_LOCATION_ID,$this->manager->get_user()->get_id());
			foreach($maps as $map)
			{
				$html[] = RightRenderer::render_map($this->manager->get_user(), $map, 8, $this->manager->get_renderer()->get_table(RightManager :: READ_RIGHT, false, true, true, $map));
			}
			if(count($maps))
			{
				$html[] = "<h3 class=\"title\">" . Language::get_instance()->translate(902) . ":</h3>";
				$html[] = "<div style='margin-left: 40px'>";
			}
			$html[] = $this->manager->get_renderer()->get_table(RightManager :: READ_RIGHT, false, true, true, "others");
			if(count($maps))
				$html[] = "</div>";
			$html[] = '</div>';
			$html[] = '</div>';
			$html[] = '</div>';
			$html[] = '</div>';
				
			$html[] = '<script type="text/javascript">';
		    $html[] = '  var tabnumber = ' . $this->tab . ';';
		    $html[] = '</script>';
		}
		elseif($this->read_right)
		{
			$html[] = '<div id="lesson_info">';
			$html[] = '<br/>';
			$html[] = '<p style="vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(218) . '</p>';
			$html[] = $this->manager->get_renderer()->get_lesson_list($user, $this->coach);
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
			$html[] = "<h class='details'>" . Language::get_instance()->translate(219) . "</h>";
			$html[] = '<h class="general">' . Language::get_instance()->translate(220) . '</h><br/>';
			$html[] = Language::get_instance()->translate(221);
			$html[] = '</p>';
			return implode(" ", $html);
		}
	}
	
}

?>