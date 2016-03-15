<?php

class LessonExcerciseRenderer
{
	private $manager;
	
	function LessonExcerciseRenderer($manager)
	{
		$this->manager = $manager;
	}
	
	public function get_lesson_excercise_list($user, $coach = false)
	{
		$extra_parent_ids = $user->get_extra_parent_ids();
		if((!is_null($user->get_parent_id()) && $coach == false) || (!empty($extra_parent_ids) && $coach == false) || $coach == true)
		{
			$parent_user = $user;
			if(!$coach)
				$parent_user = UserDataManager::instance(null)->retrieve_user($user->get_parent_id());
			$parent_users = array();
			if(!is_null($parent_user))
				$parent_users[] = $parent_user;
			if(!$coach)
			{
				foreach($extra_parent_ids as $id)
				{
					$parent_users[] = UserDataManager::instance(null)->retrieve_user($id);
				}
			}
			if(!empty($parent_users))
			{
				Language::get_instance()->add_section_to_translations(Language::THEME);
				$html = array();
				foreach($parent_users as $parent_user)
				{
					$first = true;
					$maps = RightDataManager::instance(null)->retrieve_location_user_maps(RightManager::LESSON_EXCERCISE_LOCATION_ID,$parent_user->get_id());
					$lesson_excercises_in_maps = array();
					foreach($maps as $map)
					{
						$lesson_excercises_in_maps[] = $this->manager->get_data_manager()->retrieve_lesson_excercises_by_user_id_visibility_and_criteria($parent_user->get_id(), true, $map);
					}
					$lesson_excercises_without_map = $this->manager->get_data_manager()->retrieve_lesson_excercises_by_user_id_visibility_and_criteria($parent_user->get_id(), true, "others");
					if(!$coach)
						LessonExcerciseDataManager::instance($this->manager)->filter_lesson_excercises($lesson_excercises_without_map, $lesson_excercises_in_maps, $user, $parent_user);
					
					foreach($lesson_excercises_in_maps as $index => $lesson_excercises)
					{
						if(count($lesson_excercises))
						{
							if($first)
								$html[] = "<p class='title'>" . Language::get_instance()->translate(145) . " ".$parent_user->get_name()."</p><br class='clearfloat'/>";
							$html[] = RightRenderer::render_map($this->manager->get_user(), $maps[$index], 9, $this->get_lesson_excercise_list_of_user($lesson_excercises, $coach, $user, $parent_user, $first));	
						}
							
					}
					
					$lesson_excercises = $lesson_excercises_without_map;
					if(count($lesson_excercises))
					{
						$prev_first = $first;
						if(!$prev_first)
						{
							$html[] = "<h3 class=\"title\">" . Language::get_instance()->translate(902) . ":</h3>";
							$html[] = "<div style='margin-left: 40px'>";
						}
						else
							$html[] = "<p class='title'>" . Language::get_instance()->translate(145) . " ".$parent_user->get_name()."</p><br class='clearfloat'/>";
												
						$html[] = $this->get_lesson_excercise_list_of_user($lesson_excercises, $coach, $user, $parent_user, $first);
						if(!$prev_first)
							$html[] = "</div>";
					}
				}
				return implode("\n", $html);
			}
		}
		return Language::get_instance()->translate(155);
	}
	
	public function get_lesson_excercise_list_of_user($excs, $coach, $user, $parent_user, &$first)
	{
		$html = array();
		$count = 1;
		foreach($excs as $exc)
		{
			if($first)
			{
				$first = false;
			}
			$html[] = "<div style='margin-left: 40px;" . ((!$exc->get_visible() && $exc->get_teaser())?"color: #999999":"") . "'>";
			$html[] = "<p class='medium_title'" . ((!$exc->get_visible() && $exc->get_teaser())?" style='color: #999999'":"") . ">" . Language::get_instance()->translate(146) . " ".$count."</p><br class='clearfloat'/>";
			$html[] = "<div class='record_name'>" . Language::get_instance()->translate(149) . " ".$count."</div><br class='clearfloat'/>";
			$html[] = "<div class='record_name'>" . Language::get_instance()->translate(54) . " :</div><div class='record_output'>".$exc->get_title()."</div><br class='clearfloat'/>";
			$html[] = "<div class='record_name'>" . Language::get_instance()->translate(55) . " :</div><div class='record_output'>".$exc->get_description()."</div><br class='clearfloat'/>";
			if($exc->get_visible())
			{
				$url_arr = array("page"=>"view_excercise", "id"=>$exc->get_id());
				if($coach)
					$url_arr["coach"] = 1;
				$html[] = "<div class='record_name'>&nbsp</div><div class='record_output'><a class='text_link' href='" . Url::create_url($url_arr) ."'>" . Language::get_instance()->translate(153) . "</a></div><br class='clearfloat'/><br/><br/>";
			}
			elseif($exc->get_teaser())
			{
				$html[] = "<div class='record_output' style='margin-left: 20px;'><b>" . Language::get_instance()->translate(949) . ": </b></div><br class='clearfloat'/>";
				if($exc->get_criteria_lesson_percentage())
	   			{
					$criteria_lesson_id = $exc->get_criteria_lesson_id();
						
					$c_lesson = $this->manager->get_parent_manager()->get_data_manager()->retrieve_lesson($exc->get_criteria_lesson_id());
					$html[] = "<div class='record_output' style='margin-left: 50px;'>" . sprintf(Language::get_instance()->translate(968), $exc->get_criteria_lesson_percentage(), $c_lesson->get_title()) . "</div><br class='clearfloat'/>";
	   			}
	   			
	   			if($exc->get_criteria_lesson_excercise_percentage())
	   			{
	   				$criteria_lesson_excercise_ids = $exc->get_criteria_lesson_excercise_ids();
					$excs = array();
					foreach($criteria_lesson_excercise_ids as $id)
					{
						$exc_chk = $this->manager->get_data_manager()->retrieve_lesson_excercise($id);
						$exc_chk->set_user_id($parent_user->get_id());
						$users = $exc_chk->get_user_ids();
						if(empty($users) || in_array($user->get_id(), $users))
						{
							$excs[] = $exc_chk->get_title();
						}
					}
					if(count($excs))
					{
						$output = "";
						$count = 0;
						$total = count($excs);
						foreach($excs as $e)
						{
							$output .= ($count!=0?($count+1==$total?" & ":", "):"") . $e;
							$count++;
						}
		   				$html[] = "<div class='record_output' style='margin-left: 50px;'>" . sprintf(Language::get_instance()->translate(969), $exc->get_criteria_lesson_excercise_percentage(), $output) . "</div><br class='clearfloat'/>";
		   			}
	   			}
			}
			$html[] = "<br/><br/></div>";
			$count++;
		}
		return implode("\n", $html);
	}
	
	public function get_new_lesson_excercise_list($user)
	{
		$html = array();
		$first = true;
		$excercises = $this->manager->get_data_manager()->retrieve_visible_and_new_lesson_excercises_by_user_id($user->get_parent_id());
		$other_parents = $user->get_extra_parent_ids();
		foreach($other_parents as $parent)
		{
			$excercises = array_merge($excercises, $this->manager->get_data_manager()->retrieve_visible_and_new_lesson_excercises_by_user_id($parent));	
		}
		foreach($excercises as $exc)
		{
			$user_ids = $exc->get_user_ids();
			if(empty($user_ids) || in_array($user->get_id(), $user_ids))
			{
				if($first)
				{	
					$html[] = "<b>" . Language::get_instance()->translate(156) . "</b><br/><br/>";
					$first = false;
				}

				$html[] = "<div class='record_name'>".$exc->get_title()."</div><div class='record_output'><a class='text_link' href='" . Url::create_url(array("page"=>"view_excercise", "id"=>$exc->get_id())) ."'>" . Language::get_instance()->translate(153) . "</a></div><br class='clearfloat'/>";
			}
		}
		$html[] = "<br>";
		return implode("\n", $html);
	}
	
	public function get_lesson_excercise_table($user_id, $editable = true, $title = true, $map = null, $reset = false)
	{
		$html = array();
		
		$lesson_excercises = $this->manager->get_data_manager()->retrieve_lesson_excercises_by_user_id($this->manager->get_user()->get_id(), $map);

		$table = new Table($lesson_excercises);
		$table->set_attributes(array("id" => "lesson_excercises_table"));
		$table->set_ids("id");
		if($editable)
		{
			$table->set_row_link("browse_excercises", "id");
		}
		else
		{
			$table->set_row_link("view_excercise", "id");
		}
		$table->set_no_data_message('<p>' . Language::get_instance()->translate(165) . '</p>');
		
		if($editable)
		{
			$table->set_editable(true);
			$table->set_editable_id("id");
			$table->set_sortable(true);
			$table->set_delete_title(Language::get_instance()->translate(170));
			$table->add_class_to_load(Path::get_path() . 'pages/lesson/lesson_excercise/lib/lesson_excercise.class.php');
			$table->add_language_to_load(Language::LESSON);
			$table->add_hidden_input("save_all", 1);
			if(is_object($map))
				$table->add_hidden_input("map_id", $map->get_id());
			elseif($map == 'others')
				$table->add_hidden_input("map_id", "others");
			$action = new Action("add_excercise", "id", Language::get_instance()->translate(169));
			$table->add_action($action);
			$action = new Action("change_map&section=9", "id", Language::get_instance()->translate(892), '', 'change_map');
			$table->add_action($action);
		}
		
		$columns = array();
		$column = new Column("#", "order");
		$column->set_style_attributes(array("width"=>"50px", "text-align"=>"right"));
		if($editable)
			$column->set_order(true);
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(54), "title");
		$column->set_style_attributes(array("width"=>"200px"));
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(55), "description");
		$column->set_style_attributes(array("width"=>"250px", "word-wrap" => "break-word"));
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(143), "users");
		$column->set_style_attributes(array("width"=>"150px"));
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(152), "themes");
		$column->set_style_attributes(array("width"=>"75px"));
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(274), "difficulty");
		$column->set_style_attributes(array("width"=>"75px"));
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(144), "visible_text");
		$column->set_style_attributes(array("width"=>"50px"));
		if($editable)
		{
			$column->set_editable(true);
			$column->set_editable_type("checkbox");
			$column->set_editable_name("visible");
			$column->set_editable_id("order");
		}
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(949), "criteria_visible_text");
		$column->set_style_attributes(array("width"=>"50px"));
		$column->set_title("criteria_visible_text_details");
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(920), "new_text");
		$column->set_style_attributes(array("width"=>"50px"));
		if($editable)
		{
			$column->set_editable(true);
			$column->set_editable_type("checkbox");
			$column->set_editable_name("new");
			$column->set_editable_id("order");
		}
		$columns[] = $column;
		$table->set_columns($columns);
		$html[] = $table->render_table();
		return implode("\n", $html);
	}
	
	/*
	public function get_lesson_excercise_table($user_id, $editable = true, $title = true, $map = null, $reset = false)
	{
		$html = array();
		if($title)
			$html[] = "<h3 class=\"title\">" . Language::get_instance()->translate(162) . "</h3>";
		$lesson_excercises = $this->manager->get_data_manager()->retrieve_lesson_excercises_by_user_id($user_id, $map);
		if(!$reset)
			$html[] = '<div id="lesson_excercise_table_' .(!is_null($map)?(is_object($map)?$map->get_id():$map):'') . '">';
		if(count($lesson_excercises))
		{
			$count = 0;
			if($editable)
			{
				$html[] = '<form name="lesson_excercise_form" action="" method="post">';
				$html[] = '<input class="input_element" type="hidden" name="save_all" value="1">';
				if(is_object($map))
					$html[] = '<input class="input_element" type="hidden" name="map_id" value="' . $map->get_id() . '">';
				elseif($map == 'others')
					$html[] = '<input class="input_element" type="hidden" name="map_id" value="others">';
				$html[] = '<div class="record">';
			}
			$html[] = '<table>';
			$html[] = '<tr>';
			$html[] = '<th>#</th>';
			$html[] = '<th>' . Language::get_instance()->translate(146) . '</th>';
			$html[] = '<th>' . Language::get_instance()->translate(164) . '</th>';
			$html[] = '<th>' . Language::get_instance()->translate(163) . '</th>';
			$html[] = '<th>' . Language::get_instance()->translate(985) . '</th>';
			$html[] = '<th>' . Language::get_instance()->translate(143) . '</th>';
			$html[] = '<th>' . Language::get_instance()->translate(144) . '</th>';
			$html[] = '<th>' . Language::get_instance()->translate(920) . '</th>';
			if($editable)
				$html[] = '<th></th>';
			$html[] = '</tr>';
			
			$html[] = '<tbody class="lesson_excercise_sortable sortable_table">';
			foreach($lesson_excercises as $lesson_excercise)
			{
				$html[] = $this->get_lesson_excercise_row($lesson_excercise, $count, $editable);
				$count++;
			}
			$html[] = '</tbody>';
			$html[] = '</table>';
			if($editable)
			{
				$html[] = '<div class="record_button">';
				$html[] = '<a id="submit_form" class="link_button" href="javascript:;">' . Language::get_instance()->translate(69) . '</a>';
				$html[] = '</div>';
				$html[] = '<div class="record_button">';
				$html[] = '<a class="link_button reset_lesson_excercise_form" href="javascript:;">' . Language::get_instance()->translate(70) . '</a>';
				$html[] = '</div>';
				$html[] = '</div>';
				$html[] = '</form>';
			}
		}
		else
			$html[] = "<p>" . Language::get_instance()->translate(165) . "</p>";
		if(!$reset)
			$html[] = "</div>";
		return implode("\n", $html);
	}
	
	public function get_lesson_excercise_row($lesson_excercise, $count, $editable)
	{
		$html = array();
		$html[] = '<tr class="row '.(($count%2==1)?"odd":"even").'">';
		$html[] = '<td class="row_cell">';
		$html[] = $lesson_excercise->get_order();
		$html[] = '</td>';
		$lesson = $this->manager->get_parent_manager()->get_data_manager()->retrieve_lesson($lesson_excercise->get_lesson_id());
		$name = "";
		$title_name = "";
		if(is_null($lesson))
		{
			$name = Language::get_instance()->translate(62);
		}
		else
		{
			$title_name = $lesson->get_title();
			$name = Utilities::truncate_string($title_name, 20);
		}
		$html[] = '<td class="row_cell" width="120px" ' . ($title_name!=""?'title="' . $title_name . '"':'') . '>' . $name . '</td>';
		
		require_once Path :: get_path() . 'pages/puzzle/set/lib/set_data_manager.class.php';
		require_once Path :: get_path() . 'pages/question/question_set/lib/question_set_data_manager.class.php';
		require_once Path :: get_path() . 'pages/selection/selection_set/lib/selection_set_data_manager.class.php';
		
		$name = "";
		$title_name = "";
		if($lesson_excercise->get_question_set_id())
		{
			$title_name = QuestionSetDataManager::instance(null)->retrieve_question_set($lesson_excercise->get_question_set_id())->get_name();
			$name = Utilities::truncate_string($title_name, 20);
		}		
		else
			$name = Language::get_instance()->translate(62);
		$html[] = '<td class="row_cell" width="120px" ' . ($title_name!=""?'title="' . $title_name . '"':'') . '>' . $name . '</td>';
		$name = "";
		$title_name = "";
		if($lesson_excercise->get_set_id())
		{
			$title_name = SetDataManager::instance(null)->retrieve_set($lesson_excercise->get_set_id())->get_name();
			$name = Utilities::truncate_string($title_name, 20);
		}
		else
			$name = Language::get_instance()->translate(62);
		$html[] = '<td class="row_cell" width="120px" ' . ($title_name!=""?'title="' . $title_name . '"':'') . '>' . $name . '</td>';
		$name = "";
		$title_name = "";
		if($lesson_excercise->get_selection_set_id())
		{
			$title_name = SelectionSetDataManager::instance(null)->retrieve_selection_set($lesson_excercise->get_selection_set_id())->get_name();
			$name = Utilities::truncate_string($title_name, 20);
		}
		else
			$name = Language::get_instance()->translate(62);
		$html[] = '<td class="row_cell" width="120px" ' . ($title_name!=""?'title="' . $title_name . '"':'') . '>' . $name . '</td>';
		$html[] = '<td class="row_cell" width="150px">';
		$users = $lesson_excercise->get_users(", ");
		$trunc_users = Utilities::truncate_string($users, 25);
		$html[] = '<div class="text" style="width: 150px" title="' . str_replace(", ", "\n",$users) . '">'.$trunc_users.'</div>';
		$html[] = '</td>';
		$html[] = '<td class="row_cell edit_cell" width="50px">';
		if($editable)
		{
			$html[] = '<div class="text">'. ($lesson_excercise->get_visible()?Language::get_instance()->translate(167):Language::get_instance()->translate(168)) .'</div>';
			$html[] = '<div class="input" style="display: none">';
			$html[] = '<input class="input_element" type="checkbox" name="visible_'.$lesson_excercise->get_order().'" '. ($lesson_excercise->get_visible()?'CHECKED':'').'>';
			$html[] = '</div>';
		}
		else
			$html[] = $lesson_excercise->get_visible()?Language::get_instance()->translate(167):Language::get_instance()->translate(168);
		$html[] = '</td>';
		$html[] = '<td class="row_cell edit_cell" width="50px">';
		if($editable)
		{
			$html[] = '<div class="text">'. ($lesson_excercise->get_new()?Language::get_instance()->translate(167):Language::get_instance()->translate(168)) .'</div>';
			$html[] = '<div class="input" style="display: none">';
			$html[] = '<input class="input_element" type="checkbox" name="new_'.$lesson_excercise->get_order().'" '. ($lesson_excercise->get_new()?'CHECKED':'').'>';
			$html[] = '</div>';
		}
		else
			$html[] = $lesson_excercise->get_new()?Language::get_instance()->translate(167):Language::get_instance()->translate(168);
		$html[] = '</td>';
		
		if($editable)
		{
			$html[] = '<td width="50px">';
			$html[] = '<a href="'.Url::create_url(array("page" => "add_excercise", "id" => $lesson_excercise->get_id())).'">';
			$html[] = '<img class="edit_record" src="' . Path :: get_url_path() . 'layout/images/buttons/edit.png" title="' . Language::get_instance()->translate(169) . '" style="border: 0">';
			$html[] = '</a>';
			$html[] = '<a href="'.Url::create_url(array("page" => "change_map", "section" => 9, "id" => $lesson_excercise->get_id())).'">';
			$html[] = '<img class="edit_record" src="' . Path :: get_url_path() . 'layout/images/buttons/map.png" title="' . Language::get_instance()->translate(892) . '" style="border: 0">';
			$html[] = '</a>';
			$html[] = '<img class="lesson_excercise_delete_record" src="' . Path :: get_url_path() . 'layout/images/buttons/delete.png" title="' . Language::get_instance()->translate(170) . '" style="border: 0">';
			$html[] = '</td>';
			
			$html[] = '<input type="hidden" name="id_'.$lesson_excercise->get_order().'" value="'. $lesson_excercise->get_id().'">';
		}
		$html[] = '</tr>';
		return implode("\n", $html);
	}
	*/
	public function get_shop_detail($lesson_excercise)
	{
		$html = array();
		$html[] = "<div class='record_name'>" . Language::get_instance()->translate(54) . " :</div><div class='record_output'>".$lesson_excercise->get_title()."</div><br class='clearfloat'/>";
		$html[] = "<div class='record_name'>" . Language::get_instance()->translate(55) . " :</div><div class='record_output'>".$lesson_excercise->get_description()."</div><br class='clearfloat'/>";
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(152) . ' :</div><div class="record_output">'.$lesson_excercise->get_themes().'</div><br class="clearfloat"/>';
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(274) . ' :</div><div class="record_output">'.$lesson_excercise->get_difficulty().'</div><br class="clearfloat"/>';
		$html[] = "<div>";
		$html[] = "<div class='out_holder'>";
		$html[] = "<div class='record_name out'>" . Language::get_instance()->translate(1151) . " >>></div><br class='clearfloat'/>";
		$html[] = "</div>";
		$html[] = "<div class='in_holder' style='display: none;'>";
		$html[] = "<div class='record_name in'><<< " . Language::get_instance()->translate(1055) . " :</div>";
		$html[] = "<div class='record_output'>";
		$html[] = $this->get_relation_table($lesson_excercise->get_id(), false, false);
		$html[] = "</div><br class='clearfloat'/>";
		$html[] = "</div>";
		$html[] = "</div>";
		return implode("\n", $html);
	}

	public function get_relation_table($lesson_excercise_id, $editable = true, $title = true)
	{
		$html = array();
		if($title)
			$html[] = "<h3 class=\"title\">" . ($editable?Language::get_instance()->translate(1007):Language::get_instance()->translate(1055)) . "</h3>";
		$lesson_components = $this->manager->get_data_manager()->retrieve_lesson_excercise_components_by_lesson_excercise_id($lesson_excercise_id);
		
		$table = new Table($lesson_components);
		$table->set_table_id("lesson_excercise_relation");
		$table->set_attributes(array("id" => "lesson_excercises_relation_table"));
		$table->set_add_header($title);
		if($editable)
		{
			$table->set_ids(array("id", "lesson_excercise_id"));
			//$table->set_row_link("edit_excercise_component", array("id", "lesson_excercise_id"));
			$table->set_editable(true);
			$table->set_editable_id("id");
			$table->set_sortable(true);
			$table->set_delete_title(Language::get_instance()->translate(1008));
			$table->add_class_to_load(Path::get_path() . 'pages/lesson/lesson_excercise/lib/lesson_excercise_component.class.php');
		}
		$table->set_no_data_message("<p>" . Language::get_instance()->translate(1009) . "</p>");
		
		$columns = array();
		$column = new Column("#", "order");
		$column->set_style_attributes(array("width"=>"50px", "text-align"=>"right"));
		$column->set_order(true);
		$columns[] = $column;
		$columns[] = new Column(Language::get_instance()->translate(161), "type_text");		
		$columns[] = new Column(Language::get_instance()->translate(149), "object_name");
		$table->set_columns($columns);	
		$html[] = $table->render_table();
		return implode($html, "\n");
	}
	
	public function get_lesson_excercise_form($lesson_excercise = null, $right = RightManager::UPDATE_RIGHT)
	{		
		$html = array();
		//dump($lesson_excercise);
		$submit = Language::get_instance()->translate(49);
		$title = Language::get_instance()->translate(138);
		if(!is_null($lesson_excercise) && $lesson_excercise->get_id() != 0)
		{
			$submit = Language::get_instance()->translate(56);
			$title = Language::get_instance()->translate(169);
		}
		$html[] = '<h3 class="title">'.$title.'</h3>';
		$html[] = '<form action="" method="post" id="lesson_excercise_creator_form">';
		$html[] = '<div class="record">';
		$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(54) . " :</div>";
		if($right == RightManager::UPDATE_RIGHT)
			$html[] = "<div class='record_input'><input type='text' name='title' style='width:300px;' ".(is_null($lesson_excercise)?"":"value='".$lesson_excercise->get_title()."'")."></div>";
		else
			$html[] = "<div class='record_output'>".$lesson_excercise->get_title()."</div><br class='clearfloat'/>";
		$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(55) . " :</div>";
		if($right == RightManager::UPDATE_RIGHT)
			$html[] = "<div class='record_input'><textarea class='limit_textarea' name='description' style='width:434px;height:100px;'>".(is_null($lesson_excercise)?"":$lesson_excercise->get_description())."</textarea></div>";
		else
			$html[] = "<div class='record_output'>". $lesson_excercise->get_description() . "</div><br class='clearfloat'/>";
			
		$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(274) . " :</div>";
		if($right == RightManager::UPDATE_RIGHT)
			$html[] = "<div class='record_input'><input type='text' name='rating' size='5' ".(is_null($lesson_excercise)?"":"value='".$lesson_excercise->get_rating()."'")."></div><br class='clearfloat'/>";
		else
			$html[] = "<div class='record_output'>". $lesson_excercise->get_rating() . "</div><br class='clearfloat'/>";
			
		$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(152) . " :</div>";
		if($right == RightManager::UPDATE_RIGHT)
		{
			$theme_manager = new ThemeManager(null);
			$theme_ids = array();
			if(!is_null($lesson_excercise))
				$theme_ids = $lesson_excercise->get_theme_ids();
			$html[] = "<div class='record_input'>" . $theme_manager->get_renderer()->get_selector($theme_ids) . "</div><br class='clearfloat'/>";
		}
		else
			$html[] = "<div class='record_output'>". $lesson_excercise->get_themes() . "</div><br class='clearfloat'/>";
			
		$html[] = "<div class='record_name'>" . Language::get_instance()->translate(144) . " :</div><div class='record_input'><input type='checkbox' name='visible' ".((!is_null($lesson_excercise)&&$lesson_excercise->get_visible())?"CHECKED":"")."></div>";
		
		$show_not_visible_box = is_null($lesson_excercise) || !$lesson_excercise->get_visible();
		//dump($show_not_visible_box);
		$show_criteria_box = $show_not_visible_box && (($_POST && DataManager :: parse_checkbox_value(Request::post("criteria_visible"))) || (!is_null($lesson_excercise) && ($lesson_excercise->get_criteria_lesson_percentage() || $lesson_excercise->get_criteria_lesson_excercise_percentage())));
		$html[] = '<div id="criteria_visible">';
		$html[] = "<div class='record_name'>" . Language::get_instance()->translate(937) . " :</div><div class='record_input'><input type='checkbox' name='criteria_visible' ".($show_criteria_box?"CHECKED":"")."></div><br class='clearfloat'>";
		$html[] = '<div id="criteria_options">';
		$html[] = "<div style='width: 225px;' class='record_name'>" . Language::get_instance()->translate(146) . " :</div>";
		$html[] = "<div class='record_input'>";
		//Criteria lesson_excercises
		$criteria_lesson_id = 0;
		$criteria_lesson_excercise_ids = array();
		if(!is_null($lesson_excercise))
		{
			$criteria_lesson_id = $lesson_excercise->get_criteria_lesson_id();
			$criteria_lesson_excercise_ids = $lesson_excercise->get_criteria_lesson_excercise_ids();
		}
		$lessons = $this->manager->get_parent_manager()->get_data_manager()->retrieve_lessons();
		if(count($lessons))
		{
			$html[] = '<table style="border: none" id="lesson_table">';
			foreach($lessons as $lesson_e)
			{
				$html[] = '<tr>';
				$html[] = '<td style="border: none; padding: 0px;" width="10px"><input type="radio" name="criteria_lesson_id" value="'.$lesson_e->get_id().'" '.($criteria_lesson_id == $lesson_e->get_id()?"CHECKED":"").' /></td>';
				$html[] = '<td style="border: none" width="200px">'.$lesson_e->get_title().'</td>';
				$html[] = '</tr>';
			}
			
			$html[] = '<tr>';
			$html[] = '<td style="border: none; padding: 0px;" width="10px"><input type="radio" name="criteria_lesson_id" value="0" '.((is_null($criteria_lesson_id) || !is_numeric($criteria_lesson_id) || $criteria_lesson_id == 0)?"CHECKED":"").' /></td>';
			$html[] = '<td style="border: none" width="200px">'.Language::get_instance()->translate(62).'</td>';
			$html[] = '</tr>';
			$html[] = '</table>';
		}
		else
			$html[] = "<p>" . Language::get_instance()->translate(141) . "</p>";
		$html[] = "<div style='width: 225px;' class='record_name'>" . Language::get_instance()->translate(938) . " :</div>";
		$html[] = "<div class='record_input'><input type='text' name='criteria_lesson_percentage' style='width:30px;' ".(is_null($lesson_excercise)?"value='0'":"value='".$lesson_excercise->get_criteria_lesson_percentage()."'")."> %</div><br class='clearfloat'>";
		
		$html[] = "<div style='width: 225px;' class='record_name'>" . Language::get_instance()->translate(18) . " :</div>";
		$html[] = "<div class='record_input'>";
		//criteria lesson excercises
		$lesson_excercises = $this->manager->get_data_manager()->retrieve_lesson_excercises_by_user_id($this->manager->get_user()->get_id());
		if(count($lesson_excercises))
		{
			$html[] = '<table style="border: none" id="lesson_table">';
			$count = 0;
			foreach($lesson_excercises as $lesson_excercise_e)
			{
				if(is_null($lesson_excercise) || $lesson_excercise_e->get_id() != $lesson_excercise->get_id())
				{
					$in_exc_arr = false;
					if(!is_null($lesson_excercise))
						$in_exc_arr = in_array($lesson_excercise_e->get_id(),$lesson_excercise->get_criteria_lesson_excercise_ids());
					$html[] = '<tr>';
					$html[] = '<td style="border: none; padding: 0px;" width="10px"><input type="checkbox" name="criteria_lesson_excercise_ids[]" value="'.$lesson_excercise_e->get_id().'" '.($in_exc_arr?"CHECKED":"").' /></td>';
					$html[] = '<td style="border: none" width="200px">'.$lesson_excercise_e->get_title().'</td>';
					$html[] = '</tr>';
					$count++;
				}
			}
			
			if($count == 0)
			{
				$html[] = '<tr>';
				$html[] = '<td style="border: none" width="200px" colspan="2">'.Language::get_instance()->translate(165).'</td>';
				$html[] = '</tr>';
			}
			$html[] = '</table>';
		}
		else
			$html[] = "<p>" . Language::get_instance()->translate(165) . "</p>";
		$html[] = "</div>";
		$html[] = "<div style='width: 225px;' class='record_name'>" . Language::get_instance()->translate(939) . " :</div>";
		$html[] = "<div class='record_input'><input type='text' name='criteria_lesson_excercise_percentage' style='width:30px;' ".(is_null($lesson_excercise)?"value='0'":"value='".$lesson_excercise->get_criteria_lesson_excercise_percentage()."'")."> %</div><br class='clearfloat'>";
		$html[] = "</div>";
		$html[] = '</div>';
		$html[] = '</div>';
		
		$html[] = "<div class='record_name'>" . Language::get_instance()->translate(920) . " :</div><div class='record_input'><input type='checkbox' name='new' ".((!is_null($lesson_excercise)&&$lesson_excercise->get_new())?"CHECKED":"")."></div>";
		$excercise_rel = array();
		if(!is_null($lesson_excercise))
			$excercise_rel = $this->manager->get_data_manager()->retrieve_lesson_excercise_relations_by_lesson_excercise_id($lesson_excercise->get_id(), $lesson_excercise->get_user_id());
		
		$html[] = "<div class='record_name'>" . Language::get_instance()->translate(173) . "</div><div class='record_input'><input type='checkbox' name='add_pupils' ".((count($excercise_rel))?"CHECKED":"")."/></div><br class='clearfloat'/>";
		$html[] = "<div class='record_name'></div>";
		$html[] = '<div id="pupils_div">';
		$pupils = UserDataManager::instance($this->manager)->retrieve_users_by_parent_id($this->manager->get_user()->get_id());
		if(count($pupils))
		{
			$html[] = '<table style="border: none" id="pupil_excercise_table">';
			$count = 0;
			foreach($pupils as $pupil)
			{
				$html[] = '<tr>';
				$html[] = '<td style="border: none; padding: 0px;" width="10px"><input type="checkbox" name="user_ids[]" value="'.$pupil->get_id().'" '.((in_array($pupil->get_id(), $excercise_rel))?"CHECKED":"").' /></td>';
				$html[] = '<td style="border: none" width="200px">'.$pupil->get_name().'</td>';
				$html[] = '</tr>';
				$count++;
			}
			$html[] = '</table>';
		}
		else
		{
			$html[] = "<p>" . Language::get_instance()->translate(174) . ".</p>";
		}
		$html[] = '</div><br class="clearfloat"/>';
		if(!is_null($lesson_excercise))
		{
			$html[] = '<input type="hidden" name="id" value="'.$lesson_excercise->get_id().'">';		
			$html[] = "<input type='hidden' name='order' value='".$lesson_excercise->get_order()."' />";
			$html[] = "<input type='hidden' name='user_id' value='".$lesson_excercise->get_user_id()."' />";
		}
		$html[] = '<div class="record_button"><a id="submit_form" class="link_button" href="javascript:;">'.$submit.'</a></div>';
		$html[] = '</div>';
		$html[] = '</form>';
		return implode("\n", $html);
	}
	/*
	public function get_lesson_excercise_form($lesson_excercise = null, $right = RightManager::UPDATE_RIGHT)
	{		
		$html = array();
		$submit = Language::get_instance()->translate(49);
		$prev_excercise = null;
		if(!is_null($lesson_excercise) && $lesson_excercise->get_id() != 0)
		{
			$submit = Language::get_instance()->translate(56);
			$prev_excercise = $this->manager->get_data_manager()->retrieve_lesson_excercise_by_id($lesson_excercise->get_id(), $this->manager->get_user()->get_id());
		}
		$html[] = '<form action="" method="post" id="lesson_excercise_creator_form">';
		$html[] = '<div class="record">';
		require_once Path :: get_path() . 'pages/question/lib/question_manager.class.php';
		$question_manager = new QuestionManager($this->manager->get_user());
		if(!is_null($lesson_excercise) && ($lesson_excercise->get_question_set_id() != 0 || ($prev_excercise != null && $prev_excercise->get_question_set_id() != 0)))
		{				
			$question_set = $question_manager->get_question_set_manager()->get_data_manager()->retrieve_question_set($prev_excercise->get_question_set_id());
			$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(175) . ":</div><div class='record_input'></div><br class='clearfloat'/>";
			
			if($right == RightManager::UPDATE_RIGHT)
			{
				$html[] = '<div style="margin-top: 10px; margin-left: 30px;" >';
				$html[] = '<div style="float: left"><input type="radio" name="question_set_id" value="0" ' . ($lesson_excercise->get_question_set_id()==0?"CHECKED":"") . '/></div>';
				$html[] = '<div style="float: left"><div class="record_name">' . Language::get_instance()->translate(51) . '</div></div>';
				$html[] = '</div><br class="clearfloat"/>';
			}
			
			$html[] = '<div style="margin-top: 10px; margin-left: 30px;" >';
			if($right == RightManager::UPDATE_RIGHT)
			{
				$html[] = '<div style="float: left">';
				$html[] = '<input type="radio" name="question_set_id" value="'.$prev_excercise->get_question_set_id().'" ' . ($lesson_excercise->get_question_set_id()==0?"":"CHECKED") . '/>';
				$html[] = '</div>';
			}
			$html[] = '<div style="float: left">';
			$html[] = $question_manager->get_question_set_manager()->get_renderer()->get_description_question_set($question_set);
			$html[] = '</div>';
			$html[] = '</div><br class="clearfloat"/><br/>';
		}
		
		if($right == RightManager::UPDATE_RIGHT)
		{
			$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(176) . ":</div>";
			$html[] = "<div class='record_input' style='max-height: 300px; overflow-y: scroll;' id='question_set_div'>";
			$html[] = $question_manager->get_question_set_manager()->get_renderer()->get_question_set_search(false, true, "submit_question_set_search_form");
			$html[] = "</div>";
			$html[] = "<br class='clearfloat'/>";
		}
		
		if($right == RightManager::UPDATE_RIGHT)
			$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(178) . "</div><br class='clearfloat'/>";

		require_once Path :: get_path() . 'pages/puzzle/lib/puzzle_manager.class.php';
		$puzzle_manager = new PuzzleManager($this->manager->get_user());
		if(!is_null($lesson_excercise) && ($lesson_excercise->get_set_id() != 0 || ($prev_excercise != null && $prev_excercise->get_set_id() != 0)))
		{	
			$set = $puzzle_manager->get_set_manager()->get_data_manager()->retrieve_set($prev_excercise->get_set_id());
			$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(177) . ":</div><div class='record_input'></div><br class='clearfloat'/>";
			
			if($right == RightManager::UPDATE_RIGHT)
			{
				$html[] = '<div style="margin-top: 10px; margin-left: 30px;" >';
				$html[] = '<div style="float: left"><input type="radio" name="set_id" value="0" ' . ($lesson_excercise->get_set_id()==0?"CHECKED":"") . '/></div>';
				$html[] = '<div style="float: left"><div class="record_name">' . Language::get_instance()->translate(51) . '</div></div>';
				$html[] = '</div><br class="clearfloat"/>';
			}
			
			$html[] = '<div style="margin-top: 10px; margin-left: 30px;" >';
			if($right == RightManager::UPDATE_RIGHT)
			{
				$html[] = '<div style="float: left">';
				$html[] = '<input type="radio" name="set_id" value="'.$prev_excercise->get_set_id().'" ' . ($lesson_excercise->get_set_id()==0?"":"CHECKED") . '/>';
				$html[] = '</div>';
			}
			$html[] = '<div style="float: left">';
			$html[] = $puzzle_manager->get_set_manager()->get_renderer()->get_description_set($set);
			$html[] = '</div>';
			$html[] = '</div><br class="clearfloat"/><br/>';
		}
		
		if($right == RightManager::UPDATE_RIGHT)
		{
			$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(179) . ":</div>";
			$html[] = "<div class='record_input' style='max-height: 300px; overflow-y: scroll;' id='set_div'>";
			$html[] = $puzzle_manager->get_set_manager()->get_renderer()->get_set_search(false);
			$html[] = "</div>";
			$html[] = "<br class='clearfloat'/>";
		}
		
		if($right == RightManager::UPDATE_RIGHT)
			$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(178) . "</div><br class='clearfloat'/>";
		
		require_once Path :: get_path() . 'pages/selection/lib/selection_manager.class.php';
		$selection_manager = new SelectionManager($this->manager->get_user());
		if(!is_null($lesson_excercise) && ($lesson_excercise->get_selection_set_id() != 0 || ($prev_excercise != null && $prev_excercise->get_selection_set_id() != 0)))
		{				
			$selection_set = $selection_manager->get_selection_set_manager()->get_data_manager()->retrieve_selection_set($prev_excercise->get_selection_set_id());
			$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(987) . ":</div><div class='record_input'></div><br class='clearfloat'/>";
			
			if($right == RightManager::UPDATE_RIGHT)
			{
				$html[] = '<div style="margin-top: 10px; margin-left: 30px;" >';
				$html[] = '<div style="float: left"><input type="radio" name="selection_set_id" value="0" ' . ($lesson_excercise->get_selection_set_id()==0?"CHECKED":"") . '/></div>';
				$html[] = '<div style="float: left"><div class="record_name">' . Language::get_instance()->translate(51) . '</div></div>';
				$html[] = '</div><br class="clearfloat"/>';
			}
			
			$html[] = '<div style="margin-top: 10px; margin-left: 30px;" >';
			if($right == RightManager::UPDATE_RIGHT)
			{
				$html[] = '<div style="float: left">';
				$html[] = '<input type="radio" name="selection_set_id" value="'.$prev_excercise->get_selection_set_id().'" ' . ($lesson_excercise->get_selection_set_id()==0?"":"CHECKED") . '/>';
				$html[] = '</div>';
			}
			$html[] = '<div style="float: left">';
			$html[] = $selection_manager->get_selection_set_manager()->get_renderer()->get_description_selection_set($selection_set);
			$html[] = '</div>';
			$html[] = '</div><br class="clearfloat"/><br/>';
		}
		
		if($right == RightManager::UPDATE_RIGHT)
		{
			$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(986) . ":</div>";
			$html[] = "<div class='record_input' style='max-height: 300px; overflow-y: scroll;' id='selection_set_div'>";
			$html[] = $selection_manager->get_selection_set_manager()->get_renderer()->get_selection_set_search(false, true, "submit_selection_set_search_form");
			$html[] = "</div>";
			$html[] = "<br class='clearfloat'/>";
		}
		
		$lesson_id = 0;
		if(!is_null($lesson_excercise))
			$lesson_id = $lesson_excercise->get_lesson_id();
		$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(180) . "</div><div class='record_input'><input type='checkbox' name='add_lesson' ".((!is_null($lesson_id) && is_numeric($lesson_id) && $lesson_id != 0)?"CHECKED":"")."/></div><br class='clearfloat'/>";
		$html[] = "<div class='record_name'></div>";
		$html[] = '<div style="margin-left: 30px" id="lesson_div">';
		$lessons = $this->manager->get_parent_manager()->get_data_manager()->retrieve_lessons();
		if(count($lessons))
		{
			$html[] = '<table style="border: none" id="lesson_excercise_table">';
			$count = 0;
			foreach($lessons as $lesson)
			{
				$html[] = '<tr>';
				$html[] = '<td style="border: none; padding: 0px;" width="10px"><input type="radio" name="lesson_id" value="'.$lesson->get_id().'" '.((!is_null($lesson_id) && is_numeric($lesson_id) && $lesson_id != 0)?"CHECKED":"").' /></td>';
				$html[] = '<td style="border: none" width="200px">'.$lesson->get_title().'</td>';
				$html[] = '</tr>';
				$count++;
			}
			$html[] = '</table>';
		}
		else
			$html[] = "<p>" . Language::get_instance()->translate(141) . "</p>";
		$html[] = '</div><br class="clearfloat"/>';
		$excercise_rel = array();
		if(!is_null($lesson_excercise))
			$excercise_rel = $this->manager->get_data_manager()->retrieve_lesson_excercise_relations_by_lesson_excercise_id($lesson_excercise->get_id(), $lesson_excercise->get_user_id());
		$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(173) . "</div><div class='record_input'><input type='checkbox' name='add_pupils' ".((count($excercise_rel))?"CHECKED":"")."/></div><br class='clearfloat'/>";
		$html[] = "<div class='record_name'></div>";
		$html[] = '<div id="pupils_div">';
		$pupils = UserDataManager::instance($this->manager)->retrieve_users_by_parent_id($this->manager->get_user()->get_id());
		if(count($pupils))
		{
			$html[] = '<table style="border: none" id="pupil_excercise_table">';
			$count = 0;
			foreach($pupils as $pupil)
			{
				$html[] = '<tr>';
				$html[] = '<td style="border: none; padding: 0px;" width="10px"><input type="checkbox" name="user_id[]" value="'.$pupil->get_id().'" '.((in_array($pupil->get_id(), $excercise_rel))?"CHECKED":"").' /></td>';
				$html[] = '<td style="border: none" width="200px">'.$pupil->get_name().'</td>';
				$html[] = '</tr>';
				$count++;
			}
			$html[] = '</table>';
		}
		else
		{
			$html[] = "<p>" . Language::get_instance()->translate(174) . ".</p>";
		}
		$html[] = '</div><br class="clearfloat"/>';
		$visible = false;
		if(!is_null($lesson_excercise))
			$visible = $lesson_excercise->get_visible();
		$new = false;
		if(!is_null($lesson_excercise))
			$new = $lesson_excercise->get_new();
		$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(144) . " :</div><div class='record_input'><input type='checkbox' name='visible' ".($visible?"CHECKED":"")." /></div><br class='clearfloat'/>";
		$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(920) . " :</div><div class='record_input'><input type='checkbox' name='new' ".($new?"CHECKED":"")." /></div><br class='clearfloat'/>";
		if(!is_null($lesson_excercise))
		{
			$html[] = "<input type='hidden' name='id' value='".$lesson_excercise->get_id()."' />";
			$html[] = "<input type='hidden' name='order' value='".$lesson_excercise->get_order()."' />";
		}
		$html[] = '<div class="record_button"><a id="submit_form" class="link_button" href="javascript:;">'.$submit.'</a></div>';
		$html[] = '</div>';
		$html[] = '</form>';
		return implode("\n", $html);
	}
	
	public function get_lesson_excercise_set_form($puzzle_manager, $right = RightManager :: READ_RIGHT, $form_search = false)
	{
		$html = array();
		$sets = null;
		if($form_search)
			$sets = $puzzle_manager->get_set_manager()->get_data_manager()->retrieve_sets_with_search_form($right);
		else
			$sets = $puzzle_manager->get_set_manager()->get_data_manager()->retrieve_sets($right);
		$used_set_ids = array();
		$user_excs = $this->manager->get_data_manager()->retrieve_lesson_excercises_by_user_id($this->manager->get_user()->get_id());
		foreach ($user_excs as $exc)
			$used_set_ids[] = $exc->get_set_id();
		if(count($sets))
		{
			$count = 0;
			foreach ($sets as $set)
			{
				if(!in_array($set->get_id(), $used_set_ids))
				{
					$count++;
					$html[] = '<div style="margin-top: 10px;">';
					$html[] = '<div style="float: left">';
					$html[] = '<input type="radio" name="set_id" value="'.$set->get_id().'"/>';
					$html[] = '</div>';
					$html[] = '<div style="float: left;">';
					$html[] = $puzzle_manager->get_set_manager()->get_renderer()->get_description_set($set);
					$html[] = '</div>';
					$html[] = '<br class="clearfloat"/>';
					$html[] = '</div>';
				}
			}
			if(!$count)
			{
				$html[] = '<p>' . Language::get_instance()->translate(181) . '</p>';
			}
		}
		else
		{
			if($form_search)
				$html[] = '<p>' . Language::get_instance()->translate(182) . '</p>';
			else
				$html[] = '<p>' . Language::get_instance()->translate(183) . '</p>';
		}
			
		return implode("\n", $html);
	}
	
	public function get_question_sets_form($question_manager, $right = RightManager :: READ_RIGHT, $form_search = false)
	{
		$html = array();
		$question_sets = null;
		if($form_search)
			$question_sets = $question_manager->get_question_set_manager()->get_data_manager()->retrieve_question_sets_with_search_form($right);
		else
			$question_sets = $question_manager->get_question_set_manager()->get_data_manager()->retrieve_question_sets($right);
		$used_question_set_ids = array();
		$user_excs = $this->manager->get_data_manager()->retrieve_lesson_excercises_by_user_id($this->manager->get_user()->get_id());
		foreach ($user_excs as $exc)
			$used_question_set_ids[] = $exc->get_question_set_id();
		if(count($question_sets))
		{
			$count = 0;
			foreach ($question_sets as $question_set)
			{
				if(!in_array($question_set->get_id(), $used_question_set_ids))
				{
					$count++;
					$html[] = '<div style="margin-top: 10px;">';
					$html[] = '<div style="float: left">';
					$html[] = '<input type="radio" name="question_set_id" value="'.$question_set->get_id().'"/>';
					$html[] = '</div>';
					$html[] = '<div style="float: left;">';
					$html[] = $question_manager->get_question_set_manager()->get_renderer()->get_description_question_set($question_set);
					$html[] = '</div>';
					$html[] = '<br class="clearfloat"/>';
					$html[] = '</div>';
				}
			}
			if(!$count)
			{
				$html[] = '<p>' . Language::get_instance()->translate(184) . '</p>';
			}
		}
		else
		{
			if($form_search)
				$html[] = '<p>' . Language::get_instance()->translate(185) . '</p>';
			else
				$html[] = '<p>' . Language::get_instance()->translate(186) . '</p>';
		}
			
		return implode("\n", $html);
	}
	
	public function get_selection_sets_form($selection_manager, $right = RightManager :: READ_RIGHT, $form_search = false)
	{
		$html = array();
		$selection_sets = null;
		if($form_search)
			$selection_sets = $selection_manager->get_selection_set_manager()->get_data_manager()->retrieve_selection_sets_with_search_form($right);
		else
			$selection_sets = $selection_manager->get_selection_set_manager()->get_data_manager()->retrieve_selection_sets($right);
		$used_selection_set_ids = array();
		$user_excs = $this->manager->get_data_manager()->retrieve_lesson_excercises_by_user_id($this->manager->get_user()->get_id());
		foreach ($user_excs as $exc)
			$used_selection_set_ids[] = $exc->get_selection_set_id();
		if(count($selection_sets))
		{
			$count = 0;
			foreach ($selection_sets as $selection_set)
			{
				if(!in_array($selection_set->get_id(), $used_selection_set_ids))
				{
					$count++;
					$html[] = '<div style="margin-top: 10px;">';
					$html[] = '<div style="float: left">';
					$html[] = '<input type="radio" name="selection_set_id" value="'.$selection_set->get_id().'"/>';
					$html[] = '</div>';
					$html[] = '<div style="float: left;">';
					$html[] = $selection_manager->get_selection_set_manager()->get_renderer()->get_description_selection_set($selection_set);
					$html[] = '</div>';
					$html[] = '<br class="clearfloat"/>';
					$html[] = '</div>';
				}
			}
			if(!$count)
			{
				$html[] = '<p>' . Language::get_instance()->translate(989) . '</p>';
			}
		}
		else
		{
			if($form_search)
				$html[] = '<p>' . Language::get_instance()->translate(988) . '</p>';
			else
				$html[] = '<p>' . Language::get_instance()->translate(186) . '</p>';
		}
			
		return implode("\n", $html);
	}
	*/

	public function get_type_selector($id = 0, $name='type_id', $no_text=false)
	{
		require_once Path ::get_path() . 'pages/lesson/lesson_excercise/lib/lesson_excercise_component.class.php';
		$html[] = '<select class="input_element" name="'.$name.'" style="min-width: 150px;">';
		$html[] = '<option value="0">' . Language::get_instance()->translate(161) . ':</option>';
		$arr = array();
		$arr[LessonExcerciseComponent::PUZZLE_TYPE] = Language::get_instance()->translate(206);
		$arr[LessonExcerciseComponent::QUESTION_TYPE] = Language::get_instance()->translate(208);
		$arr[LessonExcerciseComponent::SELECTION_TYPE] = Language::get_instance()->translate(1002);
		
		foreach($arr as $index => $value)
		{
			$str = '<option value="'.$index.'"';
			if($id == $index) $str .= "selected='selected'";
			$str .= ">".$value."</option>";
			$html[] = $str;
		}
		$html[] = '</select>';
		return implode("\n", $html);
	}
	
	public function render_lesson_excercise_available_email($lesson_excercise, $user)
	{
		$html = array();
		$html[] = "<p>" . Language::get_instance()->translate(203) . " " . $user->get_name() . ",</p>";
		$html[] = "<p>" . Language::get_instance()->translate(204) . "<br>";
		$html[] = "<p>" . Language::get_instance()->translate(2) . "</p>";
		return implode("\n", $html);
	}
	
	public function get_lesson_excercise_search($own_form = true, $title = true)
	{
		require_once Path::get_path() . "/pages/puzzle/lib/puzzle_manager.class.php";
		require_once Path::get_path() . "/pages/question/lib/question_manager.class.php";
		require_once Path :: get_path() . "pages/puzzle/difficulty/lib/difficulty_manager.class.php";
		$html = array();
		if($own_form)
		{
			$html[] = '<form action="" method="post" id="lesson_excercise_search_form">';
			$html[] = '<div class="record">';
		}
		/*
		$pm = new PuzzleManager($this->manager->get_user());
		$qm = new QuestionManager($this->manager->get_user());
		$html[] = $pm->get_set_manager()->get_renderer()->get_set_search(false, false);
		$html[] = $qm->get_question_set_manager()->get_renderer()->get_question_set_search(false, false);
		*/
		if($title)
			$html[] = '<p><h3 class="title">' . Language::get_instance()->translate(59) . ':</h3></p>';
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(61) . ':</div><div class="record_input"><input type="text" name="keywords"/></div>';
					
		$theme_manager = new ThemeManager($this->manager->get_user());
		$difficulty_manager = new DifficultyManager($this->manager->get_user());
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(152) . ' :</div><div class="record_input">'.$theme_manager->get_renderer()->get_selector().'</div>';
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(274) . ' :</div><div class="record_input">'.$difficulty_manager->get_renderer()->get_selector().'</div>';
		$html[] = '<div id="object_search"></div>';
		$html[] = '<div class="record_button"><a id="submit_search_form" class="link_button" href="javascript:;">' . Language::get_instance()->translate(63) . '</a></div><br class="clearfloat"/>';
		if($own_form)
			$html[] = '</div></form>';
		return implode("\n", $html);
	}
	/*
	public function get_lesson_excercise_row_header()
	{
		$html = array();
		$html[] = '<th>#</th>';
		$html[] = '<th>Set id</th>';
		$html[] = '<th>Vragenset id</th>';
		$html[] = '<th>Les id</th>';
		return implode("\n", $html);
	}
	
	public function get_lesson_excercise_row_render($lesson)
	{
		$html = array();
		$html[] = '<td>';
		$html[] = $lesson->get_id();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $lesson->get_set_id();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $lesson->get_question_set_id();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $lesson->get_lesson_id();
		$html[] = '</td>';
		return implode("\n", $html);
	}
	
	public function get_description_lesson_excercise($lesson_excercise)
	{
		$html = array();
		$html[] = '<div class="record_name"># :</div><div class="record_output">'.$lesson_excercise->get_id().'</div><br class="clearfloat"/>';
		$html[] = "<div class='record_name'>Set id :</div><div class='record_output'>".$lesson_excercise->get_set_id()."</div><br class='clearfloat'/>";
		$html[] = '<div class="record_name">Vragenset id :</div><div class="record_output">'.$lesson_excercise->get_question_set_id().'</div><br class="clearfloat"/>';
		$html[] = '<div class="record_name">Les id :</div><div class="record_output">'.$lesson_excercise->get_lesson_id().'</div><br class="clearfloat"/>';
		return implode("\n", $html);
	}
	*/
	public function get_lesson_excercise_row_header()
	{
		$html = array();
		
		$html[] = '<th>#</th>';
		$html[] = '<th>' . Language::get_instance()->translate(54) . '</th>';
		$html[] = '<th>' . Language::get_instance()->translate(55) . '</th>';
		return implode("\n", $html);
	}
	
	public function get_lesson_excercise_row_render($lesson)
	{
		$html = array();
		
		$html[] = '<td>';
		$html[] = $lesson->get_id();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $lesson->get_title();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $lesson->get_description();
		$html[] = '</td>';
		return implode("\n", $html);
	}
	
	public function get_description_lesson_excercise($lesson_excercise)
	{
		$html = array();
		$html[] = '<div class="record_name"># :</div><div class="record_output">'.$lesson_excercise->get_id().'</div><br class="clearfloat"/>';
		$html[] = "<div class='record_name'>" . Language::get_instance()->translate(54) . " :</div><div class='record_output'>".$lesson_excercise->get_title()."</div><br class='clearfloat'/>";
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(55) . ' :</div><div class="record_output">'.$lesson_excercise->get_description().'</div><br class="clearfloat"/>';
		
		//$html[] = "n/a";
		return implode("\n", $html);
	}
	
}

?>