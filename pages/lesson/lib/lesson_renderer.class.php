<?php

require_once Path::get_path() . 'pages/puzzle/theme/lib/theme_manager.class.php';

class LessonRenderer
{
	private $manager;
	
	function LessonRenderer($manager)
	{
		$this->manager = $manager;
	}
	
	public function get_actions()
	{
		$write_right = RightManager::instance()->check_right_location(RightManager::WRITE_RIGHT, 'lesson', $this->manager->get_user());
		$html = array();
		$html[] = '<ul class="menu menu_actions menu_vertical" id="menu_actions">';
		if(Alias::instance()->get_alias(Request::get("page")) == LessonManager :: LESSON_BROWSER && $write_right)
		{
			$object_right = RightManager::NO_RIGHT;
			$id = Request::get("id");
			if(!is_numeric($id))
				$id = null;
			else
				$object_right = RightManager::instance()->get_right_location_object(RightManager::LESSON_LOCATION_ID, $this->manager->get_user(), $id);
			
			if(!is_null($id))
			{
				$html[] = '<li>';
				$html[] = "<a class='general' href=\"" . Url :: create_url(array('page' => 'create_map', 'section' => 8)) . "\" title=\"" . Language::get_instance()->translate(888) . "\">" . Language::get_instance()->translate(888) . "</a>";
				$html[] = '</li>';
				$html[] = '<br/>';
				if(GroupManager::group_is_not_test($this->manager->get_user()->get_group_id()) && !GroupManager::is_free_group($this->manager->get_user()->get_group_id()))
				{
					$html[] = '<li>';
					$html[] = "<a class='general' href=\"" . Url :: create_url(array('page' => 'shop', 'section' => 8)) . "\" title=\"" . Language::get_instance()->translate(815) . "\">" . Language::get_instance()->translate(815) . "</a>";
					$html[] = '</li>';
				}
				$html[] = '<li>';
				$html[] = "<a class='general' href=\"" . Url :: create_url(array('page' => 'browse_lessons', 'coach' => 1)) . "\" title=\"" . Language::get_instance()->translate(134) . "\">" . Language::get_instance()->translate(128) . "</a>";
				$html[] = '</li>';
				$html[] = '<li>';
				$html[] = "<a class='general' href=\"" . Url :: create_url(array('page' => 'add_lesson')) . "\" title=\"" . Language::get_instance()->translate(133) . "\">" . Language::get_instance()->translate(129) . "</a>";
				$html[] = '</li>';
				if($this->manager->get_user()->is_admin() || $this->manager->get_user()->get_id() == LessonManager::ID_CHEXXL)
				{
					$html[] = '<li>';
					$html[] = "<a class='general' href=\"" . Url :: create_url(array('page' => 'chexxl_convertor')) . "\" title=\"" . Language::get_instance()->translate(1291) . "\">" . Language::get_instance()->translate(1290) . "</a>";
					$html[] = '</li>';
				}
				$html[] = '<li>';
				$html[] = "<a class='details' href=\"" . Url :: create_url(array('page' => 'view_lesson', 'id' => $id, 'coach' => 1)) . "\" title=\"" . Language::get_instance()->translate(134) . "\">" . Language::get_instance()->translate(130) . "</a>";
				$html[] = '</li>';
				if($object_right == RightManager::UPDATE_RIGHT)
				{
					$html[] = '<li>';
					$html[] = "<a class='details' href=\"" . Url :: create_url(array('page' => 'add_lesson', 'id' => $id)) . "\" title=\"" . Language::get_instance()->translate(56) . "\">" . Language::get_instance()->translate(131) . "</a>";
					$html[] = '</li>';
					$html[] = '<li>';
					$html[] = "<a class='details' href=\"" . Url :: create_url(array('page' => 'add_lesson_page', 'lesson_id' => $id)) . "\" title=\"" . Language::get_instance()->translate(49) . "\">" . Language::get_instance()->translate(132) . "</a>";
					$html[] = '</li>';
				}
			}
			elseif(!Request::get("coach"))
			{
				$html[] = '<li>';
				$html[] = "<a class='general' href=\"" . Url :: create_url(array('page' => 'create_map', 'section' => 8)) . "\" title=\"" . Language::get_instance()->translate(888) . "\">" . Language::get_instance()->translate(888) . "</a>";
				$html[] = '</li>';
				$html[] = '<br/>';
				if(GroupManager::group_is_not_test($this->manager->get_user()->get_group_id()) && !GroupManager::is_free_group($this->manager->get_user()->get_group_id()))
				{
					$html[] = '<li>';
					$html[] = "<a class='general' href=\"" . Url :: create_url(array('page' => 'shop', 'section' => 8)) . "\" title=\"" . Language::get_instance()->translate(815) . "\">" . Language::get_instance()->translate(815) . "</a>";
					$html[] = '</li>';
				}
				$html[] = '<li>';
				$html[] = "<a href=\"" . Url :: create_url(array('page' => 'browse_lessons', 'coach' => 1)) . "\" title=\"" . Language::get_instance()->translate(134) . "\">" . Language::get_instance()->translate(128) . "</a>";
				$html[] = '</li>';
				$html[] = '<li>';
				$html[] = "<a href=\"" . Url :: create_url(array('page' => 'add_lesson')) . "\" title=\"" . Language::get_instance()->translate(49) . "\">" . Language::get_instance()->translate(129) . "</a>";
				$html[] = '</li>';
				if($this->manager->get_user()->is_admin() || $this->manager->get_user()->get_id() == LessonManager::ID_CHEXXL)
				{
					$html[] = '<li>';
					$html[] = "<a class='general' href=\"" . Url :: create_url(array('page' => 'chexxl_convertor')) . "\" title=\"" . Language::get_instance()->translate(1291) . "\">" . Language::get_instance()->translate(1290) . "</a>";
					$html[] = '</li>';
				}
			}
			elseif(Request::get("coach"))
			{
				$html[] = '<li>';
				$html[] = "<a href=\"" . Url :: create_url(array('page' => 'browse_lessons')) . "\" title=\"" . Language::get_instance()->translate(136) . "\">" . Language::get_instance()->translate(135) . "</a>";
				$html[] = '</li>';
			}
		}
		elseif(Alias::instance()->get_alias(Request::get("page")) == LessonManager :: LESSON_EXCERCISE_BROWSER && $write_right)
		{
			$object_right = RightManager::NO_RIGHT;
			$id = Request::get("id");
			if(!is_numeric($id))
				$id = null;
			else
				$object_right = RightManager::instance()->get_right_location_object(RightManager::LESSON_EXCERCISE_LOCATION_ID, $this->manager->get_user(), $id);
			
			if(!is_null($id))
			{
				$html[] = '<li>';
				$html[] = "<a class='general' href=\"" . Url :: create_url(array('page' => 'create_map', 'section' => 8)) . "\" title=\"" . Language::get_instance()->translate(888) . "\">" . Language::get_instance()->translate(888) . "</a>";
				$html[] = '</li>';
				$html[] = '<br/>';
				if(Request::get("shop")==0 && GroupManager::group_is_not_test($this->manager->get_user()->get_group_id()) && !GroupManager::is_free_group($this->manager->get_user()->get_group_id()))
				{
					$html[] = '<li>';
					$html[] = "<a class='general' href=\"" . Url :: create_url(array('page' => 'shop', 'section' => 9)) . "\" title=\"" . Language::get_instance()->translate(823) . "\">" . Language::get_instance()->translate(823) . "</a>";
					$html[] = '</li>';
				}
				$html[] = '<li>';
				$html[] = "<a class='general' href=\"" . Url :: create_url(array('page' => 'browse_excercises', 'coach' => 1)) . "\" title=\"" . Language::get_instance()->translate(134) . "\">" . Language::get_instance()->translate(128) . "</a>";
				$html[] = '</li>';
				$html[] = '<li>';
				$html[] = "<a class='general'  href=\"" . Url :: create_url(array('page' => 'add_excercise')) . "\" title=\"" . Language::get_instance()->translate(49) . "\">" . Language::get_instance()->translate(138) . "</a>";
				$html[] = '</li>';
				$html[] = '<li>';
				$html[] = "<a class='details' href=\"" . Url :: create_url(array('page' => 'view_excercise', 'id' => $id, 'coach' => 1)) . "\" title=\"" . Language::get_instance()->translate(134) . "\">" . Language::get_instance()->translate(1000) . "</a>";
				$html[] = '</li>';
				if($object_right == RightManager::UPDATE_RIGHT)
				{
					$html[] = '<li>';
					$html[] = "<a class='details' href=\"" . Url :: create_url(array('page' => 'add_excercise', 'id' => $id)) . "\" title=\"" . Language::get_instance()->translate(56) . "\">" . Language::get_instance()->translate(169) . "</a>";
					$html[] = '</li>';
					$html[] = '<li>';
					$html[] = "<a class='details' href=\"" . Url :: create_url(array('page' => 'add_excercise_component', 'lesson_excercise_id' => $id)) . "\" title=\"" . Language::get_instance()->translate(49) . "\">" . Language::get_instance()->translate(1001) . "</a>";
					$html[] = '</li>';
				}
			}
			elseif(!Request::get("coach"))
			{
				$html[] = '<li>';
				$html[] = "<a class='general' href=\"" . Url :: create_url(array('page' => 'create_map', 'section' => 9)) . "\" title=\"" . Language::get_instance()->translate(888) . "\">" . Language::get_instance()->translate(888) . "</a>";
				$html[] = '</li>';
				$html[] = '<br/>';
				if(Request::get("shop")==0 && GroupManager::group_is_not_test($this->manager->get_user()->get_group_id()) && !GroupManager::is_free_group($this->manager->get_user()->get_group_id()))
				{
					$html[] = '<li>';
					$html[] = "<a class='general' href=\"" . Url :: create_url(array('page' => 'shop', 'section' => 9)) . "\" title=\"" . Language::get_instance()->translate(823) . "\">" . Language::get_instance()->translate(823) . "</a>";
					$html[] = '</li>';
				}
				$html[] = '<li>';
				$html[] = "<a href=\"" . Url :: create_url(array('page' => 'browse_excercises', 'coach' => 1)) . "\" title=\"" . Language::get_instance()->translate(134) . "\">" . Language::get_instance()->translate(128) . "</a>";
				$html[] = '</li>';
				$html[] = '<li>';
				$html[] = "<a href=\"" . Url :: create_url(array('page' => 'add_excercise')) . "\" title=\"" . Language::get_instance()->translate(49) . "\">" . Language::get_instance()->translate(138) . "</a>";
				$html[] = '</li>';
			}
			elseif(Request::get("coach"))
			{
				$html[] = '<li>';
				$html[] = "<a href=\"" . Url :: create_url(array('page' => 'browse_excercises')) . "\" title=\"" . Language::get_instance()->translate(136) . "\">" . Language::get_instance()->translate(135) . "</a>";
				$html[] = '</li>';
			}
		}
		elseif(Alias::instance()->get_alias(Request::get("page")) == LessonManager :: LESSON_CONTINUATION_AVAILABILITY_BROWSER)
		{
			$html[] = '<li>';
			$html[] = "<a href=\"" . Url :: create_url(array('page' => 'admin_add_lesson_continuations')) . "\" title=\"" . Language::get_instance()->translate(49) . "\">" . Language::get_instance()->translate(1057) . "</a>";
			$html[] = '</li>';
			$html[] = '<li>';
			$html[] = "<a href=\"" . Url :: create_url(array('page' => 'admin_add_excercise_continuations')) . "\" title=\"" . Language::get_instance()->translate(49) . "\">" . Language::get_instance()->translate(1058) . "</a>";
			$html[] = '</li>';
		}
		/*
		elseif(Alias::instance()->get_alias(Request::get("page")) == LessonManager :: LESSON_COURSE_BROWSER)
		{
			if($this->manager->get_user()->is_admin())
			{
				$html[] = '<li>';
				$html[] = "<a href=\"" . Url :: create_url(array('page' => 'add_course')) . "\" title=\"" . Language::get_instance()->translate(49) . "\">" . Language::get_instance()->translate(1224) . "</a>";
				$html[] = '</li>';
				$html[] = '<li>';
				$html[] = "<a href=\"" . Url :: create_url(array('page' => 'add_course_lesson')) . "\" title=\"" . Language::get_instance()->translate(49) . "\">" . Language::get_instance()->translate(1225) . "</a>";
				$html[] = '</li>';
			}
		}
		*/
		$html[] = '</ul>';
		return implode("\n", $html);
	}
	
	public function get_icon()
	{
		return '<img src="' . Path :: get_url_path() . 'layout/images/icons/lesson_icon.png" style="border: 0"/>';
	}

	/*
	public function get_form($article,$submit_value=null)
	{
		$html = array();
		if($submit_value == null)
			$submit_value = Language::get_instance()->translate(49);
	   	$html[] = "<form action='' method='post' enctype=\"multipart/form-data\">";
		$str .= "<h><input type='text' value='".($article!=null?$article->title:"")."' name='title' style='width:300px;'></h4>";
		$str .= "<div id='file_uploader'>";
		if(is_null($article) || is_null($article->attachment) || $article->attachment == "")
		{
			$str .= "<p>Bijlage:\n";
			$str .= "<input name=\"bestand\" type=\"file\" size=\"40\" /></p>\n";
		}
		else
		{
			$str .= "<p>Bijlage:\n".$article->attachment." <img class='unlink_file' src='./img/buttons/delete.png' title='Unlink file' border='0'></p>";
			$str .= "<input type='hidden' name='bestand' value='".$article->attachment."'/>";
		}
		$str .= "</div>";
		$str .= "<input type='hidden' value='".($article!=null?$article->visible:"0")."' name='visible'/>";
		$str .= "<textarea class='mce_editor' name='text' style='width:550px;height:300px;'>".($article!=null?$article->text:"")."</textarea>";
		$str .= "<p>Categorie: ";
		$cm = new CategoryManager($this->manager->get_user());
		$id =  0;
		if(!is_null($article)) $id = $article->category;
		$str .= $cm->get_renderer()->get_selector($id);
		$str .= "</p><p><input type='submit' value='$submit_value'></p></form>";
		return $str;
	}
	*/
	
	public function get_table($right = RightManager :: READ_RIGHT, $form_search = false, $editable = true, $title = true, $map = null)
	{
		$html = array();
		if($form_search)
		{
			$lessons = $this->manager->get_data_manager()->retrieve_lessons_with_search_form($right);
		}
		else
		{
			$lessons = $this->manager->get_data_manager()->retrieve_lessons("", null, $right, $map);
		}
		
		$table = new Table($lessons);
		$table->set_attributes(array("id" => "lessons_table"));
		$table->set_ids("id");
		if($editable)
		{
			$table->set_row_link("browse_lessons", "id");
		}
		else
		{
			$table->set_row_link("view_lesson", "id");
		}
		if($form_search)
		{
			$table->set_no_data_message('<p>' . Language::get_instance()->translate(140) . '</p>');
		}
		else
		{
			$table->set_no_data_message('<p>' . Language::get_instance()->translate(141) . '</p>');
		}
		if($editable)
		{
			$table->set_editable(true);
			$table->set_editable_id("id");
			$table->set_sortable(true);
			$table->set_delete_title(Language::get_instance()->translate(142));
			$table->add_class_to_load(Path::get_path() . 'pages/lesson/lib/lesson.class.php');
			$table->add_language_to_load(Language::LESSON);
			$table->add_hidden_input("save_all_lesson", 1);
			if(is_object($map))
				$table->add_hidden_input("map_id", $map->get_id());
			elseif($map == 'others')
				$table->add_hidden_input("map_id", "others");
			$action = new Action("add_lesson", "id", Language::get_instance()->translate(131));
			$table->add_action($action);
			$action = new Action("change_map&section=8", "id", Language::get_instance()->translate(892), '', 'change_map');
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
	
	public function get_shop_detail($lesson)
	{
		$html = array();
		$html[] = "<div class='record_name'>" . Language::get_instance()->translate(54) . " :</div><div class='record_output'>".$lesson->get_title()."</div><br class='clearfloat'/>";
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(55) . ' :</div><div class="record_output">'.$lesson->get_description().'</div><br class="clearfloat"/>';
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(152) . ' :</div><div class="record_output">'.$lesson->get_themes().'</div><br class="clearfloat"/>';
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(274) . ' :</div><div class="record_output">'.$lesson->get_difficulty().'</div><br class="clearfloat"/>';
		$html[] = "<div>";
		$html[] = "<div class='out_holder'>";
		$html[] = "<div class='record_name out'>" . Language::get_instance()->translate(1152) . " >>></div><br class='clearfloat'/>";
		$html[] = "</div>";
		$html[] = "<div class='in_holder' style='display: none;'>";
		$html[] = "<div class='record_name in'><<< " . Language::get_instance()->translate(817) . " :</div>";
		$html[] = "<div class='record_output'>";
		$html[] = $this->get_relation_table($lesson->get_id(), false, false);
		$html[] = "</div><br class='clearfloat'/>";
		$html[] = "</div>";
		$html[] = "</div>";
		return implode("\n", $html);
	}
	
	public function get_lesson_list($user, $coach = false)
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
				$html = array();
				foreach($parent_users as $parent_user)
				{
					$first = true;
					$maps = RightDataManager::instance(null)->retrieve_location_user_maps(RightManager::LESSON_LOCATION_ID,$parent_user->get_id());
					$lessons_in_maps = array();
					foreach($maps as $map)
					{
						$lessons_in_maps[] = $this->manager->get_data_manager()->retrieve_lessons_by_visibility_and_criteria(true, $parent_user->get_id(), RightManager::READ_RIGHT, $map);
					}
					$lessons_without_map = $this->manager->get_data_manager()->retrieve_lessons_by_visibility_and_criteria(true, $parent_user->get_id(), RightManager::READ_RIGHT, "others");
					if(!$coach)
						LessonDataManager::instance($this->manager)->filter_lessons($lessons_without_map, $lessons_in_maps, $user);
					
					foreach($lessons_in_maps as $index => $lessons)
					{
						if(count($lessons))
						{
							if($first)
								$html[] = "<p class='title'>" . Language::get_instance()->translate(154) . " ".$parent_user->get_name()."</p><br class='clearfloat'/>";
							$html[] = RightRenderer::render_map($this->manager->get_user(), $maps[$index], 8, $this->get_lesson_list_user_lessons($lessons, $coach, $user, $parent_user, $first));
						}
					}
					$lessons = $lessons_without_map;
					if(count($lessons))
					{
						$prev_first = $first;
						if(!$prev_first)
						{
							$html[] = "<h3 class=\"title\">" . Language::get_instance()->translate(902) . ":</h3>";
							$html[] = "<div style='margin-left: 40px'>";
						}
						else
							$html[] = "<p class='title'>" . Language::get_instance()->translate(154) . " ".$parent_user->get_name()."</p><br class='clearfloat'/>";
						
						$html[] = $this->get_lesson_list_user_lessons($lessons, $coach, $user, $parent_user, $first);
						if(!$prev_first)
							$html[] = "</div>";
					}	
				}
				return implode("\n", $html);
			}
		}
		return Language::get_instance()->translate(148);
	}
	
	public function get_lesson_list_user_lessons($lessons, $coach, $user, $parent_user, &$first)
	{
		$html = array();
		$count = 1;
		foreach($lessons as $lesson)
		{
			if($first)
			{
				$first = false;
			}
			
			$html[] = "<div style='margin-left: 40px;" . ((!$lesson->get_visible() && $lesson->get_teaser())?"color: #999999":"") . "'>";
			$html[] = "<p class='medium_title'" . ((!$lesson->get_visible() && $lesson->get_teaser())?" style='color: #999999'":"") . ">" . Language::get_instance()->translate(146) . " ".$count."</p><br class='clearfloat'/>";
			$html[] = "<div class='record_output' style='margin-left: 20px;'><b>" . Language::get_instance()->translate(54) . ": </b>".$lesson->get_title()."</div><br class='clearfloat'/>";
			$html[] = "<div class='record_output' style='margin-left: 20px;'><b>" . Language::get_instance()->translate(55) . ": </b>" . $lesson->get_description()."</div><br class='clearfloat'/>";
			if($lesson->get_visible())
			{
				$url_array = array("page"=>"view_lesson", "id"=>$lesson->get_id());
				if($coach)
				{
					$url_array["coach"] = 1;
				}
				$html[] = "<div class='record_output' style='margin-left: 20px;'><a class='text_link' href='" . Url::create_url($url_array) ."'>" . Language::get_instance()->translate(147) ."</a></div><br class='clearfloat'/>";
			}
			elseif($lesson->get_teaser())
			{
				$html[] = "<div class='record_output' style='margin-left: 20px;'><b>" . Language::get_instance()->translate(949) . ": </b></div><br class='clearfloat'/>";
				if($lesson->get_criteria_lesson_percentage())
	   			{
					$criteria_lesson_id = $lesson->get_criteria_lesson_id();
						
					$c_lesson = $this->manager->get_data_manager()->retrieve_lesson($lesson->get_criteria_lesson_id());
					$html[] = "<div class='record_output' style='margin-left: 50px;'>" . sprintf(Language::get_instance()->translate(968), $lesson->get_criteria_lesson_percentage(), $c_lesson->get_title()) . "</div><br class='clearfloat'/>";
	   			}
	   			
	   			if($lesson->get_criteria_lesson_excercise_percentage())
	   			{
	   				$criteria_lesson_excercise_ids = $lesson->get_criteria_lesson_excercise_ids();
					
					//require_once Path::get_path() . "/pages/puzzle/set/lib/set_data_manager.class.php";
					//require_once Path::get_path() . "/pages/question/question_set/lib/question_set_data_manager.class.php";
					$excs = array();
					foreach($criteria_lesson_excercise_ids as $id)
					{
						$exc = $this->manager->get_lesson_excercise_manager()->get_data_manager()->retrieve_lesson_excercise($id);
						$exc->set_user_id($parent_user->get_id());
						$users = $exc->get_user_ids();
						if(empty($users) || in_array($user->get_id(), $users))
						{
							$excs[] = $exc->get_title();
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
		   				$html[] = "<div class='record_output' style='margin-left: 50px;'>" . sprintf(Language::get_instance()->translate(969), $lesson->get_criteria_lesson_excercise_percentage(), $output) . "</div><br class='clearfloat'/>";
		   			}
	   			}
			}
			$html[] = "<br/><br/></div>";
			$count++;
		}
		return implode("\n", $html);
	}
	
	public function get_new_lesson_list($user)
	{
		$html = array();
		$first = true;
		$lessons = $this->manager->get_data_manager()->retrieve_visible_and_new_lessons_by_user_id($user->get_parent_id());
		$other_parents = $user->get_extra_parent_ids();
		foreach($other_parents as $parent)
		{
			$lessons = array_merge($lessons, $this->manager->get_data_manager()->retrieve_visible_and_new_lessons_by_user_id($parent));	
		}
		foreach($lessons as $lesson)
		{
			$user_ids = $lesson->get_user_ids();
			if(empty($user_ids) || in_array($user->get_id(), $user_ids))
			{
				if($first)
				{	
					$html[] = "<b>" . Language::get_instance()->translate(926) . "</b><br/><br/>";
					$first = false;
				}

				$html[] = "<div class='record_name'>".$lesson->get_title()."</div><div class='record_output'><a class='text_link' href='" . Url::create_url(array("page"=>"view_lesson", "id"=>$lesson->get_id())) ."'>" . Language::get_instance()->translate(927) . "</a></div><br class='clearfloat'/>";
			}
		}
		$html[] = "<br>";
		return implode("\n", $html);
	}
	
	public function get_relation_table($lesson_id, $editable = true, $title = true)
	{
		$html = array();
		if($title)
			$html[] = "<h3 class=\"title\">" . ($editable?Language::get_instance()->translate(157):Language::get_instance()->translate(1150)) . "</h3>";
		$lesson_pages = $this->manager->get_data_manager()->retrieve_lesson_pages_by_lesson_id($lesson_id);
		
		$table = new Table($lesson_pages);
		$table->set_table_id("lesson_relation");
		$table->set_attributes(array("id" => "lessons_relation_table"));
		$table->set_add_header($title);
		if($editable)
		{
			$table->set_ids(array("id", "lesson_id"));
			$table->set_row_link("edit_lesson_page", array("id", "lesson_id"));
			$table->set_editable(true);
			$table->set_editable_id("id");
			$table->set_sortable(true);
			$table->set_delete_title(Language::get_instance()->translate(159));
			$table->add_class_to_load(Path::get_path() . 'pages/lesson/lib/lesson_page.class.php');
		}
		$table->set_no_data_message("<p>" . Language::get_instance()->translate(158) . "</p>");
		
		$columns = array();
		$column = new Column("#", "order");
		$column->set_style_attributes(array("width"=>"50px", "text-align"=>"right"));
		$column->set_order(true);
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(54), "title");
		$column->set_style_attributes(array("width"=>"200px"));
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(161), "type_text");
		$column->set_style_attributes(array("width"=>"75px"));
		$columns[] = $column;
		if($title)
		{
			$column = new Column(Language::get_instance()->translate(160), "next", "boolean");
			$columns[] = $column;
		}
		$table->set_columns($columns);
		$html[] = $table->render_table();
		return implode($html, "\n");
	}
	
	public function get_lesson_form($lesson = null)
	{		
		$html = array();
		$submit = Language::get_instance()->translate(49);
		$title = Language::get_instance()->translate(171);
		$right = RightManager::UPDATE_RIGHT;
		if(!is_null($lesson) && $lesson->get_id() != 0)
		{
			$submit = Language::get_instance()->translate(56);
			$title = Language::get_instance()->translate(172);
			$right = RightManager::instance()->get_right_location_object("Lesson", $this->manager->get_user(), $lesson->get_id());
		}
		$html[] = '<h3 class="title">'.$title.'</h3>';
		$html[] = '<form action="" method="post" id="lesson_creator_form">';
		$html[] = '<div class="record">';
		$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(54) . " :</div>";
		if($right == RightManager::UPDATE_RIGHT)
			$html[] = "<div class='record_input'><input type='text' name='title' style='width:300px;' ".(is_null($lesson)?"":"value='".$lesson->get_title()."'")."></div>";
		else
			$html[] = "<div class='record_output'>".$lesson->get_title()."</div><br class='clearfloat'/>";
			
		$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(55) . " :</div>";
		if($right == RightManager::UPDATE_RIGHT)
			$html[] = "<div class='record_input'><textarea class='limit_textarea' name='description' style='width:434px;height:100px;'>".(is_null($lesson)?"":$lesson->get_description())."</textarea></div>";
		else
			$html[] = "<div class='record_output'>". $lesson->get_description() . "</div><br class='clearfloat'/>";
			
		$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(274) . " :</div>";
		if($right == RightManager::UPDATE_RIGHT)
			$html[] = "<div class='record_input'><input type='text' name='rating' size='5' ".(is_null($lesson)?"":"value='".$lesson->get_rating()."'")."></div><br class='clearfloat'/>";
		else
			$html[] = "<div class='record_output'>". $lesson->get_rating() . "</div><br class='clearfloat'/>";
				
		$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(152) . " :</div>";
		if($right == RightManager::UPDATE_RIGHT)
		{
			$theme_manager = new ThemeManager(null);
			$theme_ids = array();
			if(!is_null($lesson))
				$theme_ids = $lesson->get_theme_ids();
			$html[] = "<div class='record_input'>" . $theme_manager->get_renderer()->get_selector($theme_ids) . "</div><br class='clearfloat'/>";
		}
		else
			$html[] = "<div class='record_output'>". $lesson->get_themes() . "</div><br class='clearfloat'/>";
			
		$html[] = "<div class='record_name'>" . Language::get_instance()->translate(144) . " :</div><div class='record_input'><input type='checkbox' name='visible' ".((!is_null($lesson)&&$lesson->get_visible())?"CHECKED":"")."></div>";
		
		$show_not_visible_box = is_null($lesson) || !$lesson->get_visible();
		//dump($show_not_visible_box);
		$count = 0;
		if(!is_null($lesson))
			$count = count($lesson->get_criteria_lesson_excercise_ids());
		$show_criteria_box = $show_not_visible_box && (($_POST && DataManager :: parse_checkbox_value(Request::post("criteria_visible"))) || (!is_null($lesson) && ($lesson->get_criteria_lesson_id() || $count)));
		$html[] = '<div id="criteria_visible">';
		$html[] = "<div class='record_name'>" . Language::get_instance()->translate(937) . " :</div><div class='record_input'><input type='checkbox' name='criteria_visible' ".($show_criteria_box?"CHECKED":"")."></div><br class='clearfloat'>";
		$html[] = '<div id="criteria_options">';
		$html[] = "<div style='width: 225px;' class='record_name'>" . Language::get_instance()->translate(146) . " :</div>";
		$html[] = "<div class='record_input'>";
		//Crlesson_excercise_idscriteria_lesson_id = 0;
		$criteria_lesson_excercise_ids = array();
		$criteria_lesson_id = 0;
		if(!is_null($lesson))
		{
			$criteria_lesson_id = $lesson->get_criteria_lesson_id();
			$criteria_lesson_excercise_ids = $lesson->get_criteria_lesson_excercise_ids();
		}
		$lessons = $this->manager->get_data_manager()->retrieve_lessons();
		if(count($lessons))
		{
			$html[] = '<table style="border: none" id="lesson_excercise_table">';
			$count = 0;
			foreach($lessons as $lesson_e)
			{
				if(is_null($lesson) || $lesson_e->get_id() != $lesson->get_id())
				{
					$html[] = '<tr>';
					$html[] = '<td style="border: none; padding: 0px;" width="10px"><input type="radio" name="criteria_lesson_id" value="'.$lesson_e->get_id().'" '.($criteria_lesson_id == $lesson_e->get_id()?"CHECKED":"").' /></td>';
					$html[] = '<td style="border: none" width="200px">'.$lesson_e->get_title().'</td>';
					$html[] = '</tr>';
					$count++;
				}
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
		$html[] = "<div class='record_input'><input type='text' name='criteria_lesson_percentage' style='width:30px;' ".(is_null($lesson)?"value='0'":"value='".$lesson->get_criteria_lesson_percentage()."'")."> %</div><br class='clearfloat'>";
		
		$html[] = "<div style='width: 225px;' class='record_name'>" . Language::get_instance()->translate(18) . " :</div>";
		$html[] = "<div class='record_input'>";
		//criteria lesson excercises
		$lesson_excercises = $this->manager->get_lesson_excercise_manager()->get_data_manager()->retrieve_lesson_excercises_by_user_id($this->manager->get_user()->get_id());
		if(count($lesson_excercises))
		{
			
			$html[] = '<table style="border: none" id="lesson_table">';
			foreach($lesson_excercises as $lesson_excercise_e)
			{
				$in_exc_arr = false;
				if(!is_null($lesson))
					$in_exc_arr = in_array($lesson_excercise_e->get_id(),$lesson->get_criteria_lesson_excercise_ids());
				$html[] = '<tr>';
				$html[] = '<td style="border: none; padding: 0px;" width="10px"><input type="checkbox" name="criteria_lesson_excercise_ids[]" value="'.$lesson_excercise_e->get_id().'" '.($in_exc_arr?"CHECKED":"").' /></td>';
				$html[] = '<td style="border: none" width="200px">'.$lesson_excercise_e->get_title().'</td>';
				$html[] = '</tr>';
			}
			$html[] = '</table>';
			
			/*
			$html[] = '<table style="border: none" id="lesson_excercise_table">';
			$html[] = '<tr>';
			$html[] = '<td style="border: none; padding: 0px;"></td>';
			$html[] = '<td style="border: none;">' . Language::get_instance()->translate(146) . '</td>';
			$html[] = '<td style="border: none;">' . Language::get_instance()->translate(164) . '</td>';
			$html[] = '<td style="border: none;">' . Language::get_instance()->translate(163) . '</td>';
			$html[] = '<td style="border: none;">' . Language::get_instance()->translate(143) . '</td>';
			$html[] = '</tr>';
			
			$count = 0;
				
			require_once Path :: get_path() . 'pages/puzzle/set/lib/set_data_manager.class.php';
			require_once Path :: get_path() . 'pages/question/question_set/lib/question_set_data_manager.class.php';
			foreach($lesson_excercises as $lesson_excercise)
			{
				$html[] = '<tr>';
				$in_exc_arr = false;
				if(!is_null($lesson))
					$in_exc_arr = in_array($lesson_excercise->get_id(),$lesson->get_criteria_lesson_excercise_ids());
				$html[] = '<td style="border: none; padding: 0px;" width="10px"><input type="checkbox" name="criteria_lesson_excercise_ids[]" value="'. $lesson_excercise->get_id() . '" '.($in_exc_arr?"CHECKED":"").' /></td>';
				$lesson_e = $this->manager->get_data_manager()->retrieve_lesson($lesson_excercise->get_lesson_id());
				$name = "";
				$title_name = "";
				if(is_null($lesson_e))
				{
					$name = Language::get_instance()->translate(62);
				}
				else
				{
					$title_name = $lesson_e->get_title();
					$name = Utilities::truncate_string($title_name, 20);
				}
				$html[] = '<td style="border: none;" width="120px" ' . ($title_name!=""?'title="' . $title_name . '"':'') . '>' . $name . '</td>';
				
				$name = "";
				$title_name = "";
				if($lesson_excercise->get_question_set_id())
				{
					$title_name = QuestionSetDataManager::instance(null)->retrieve_question_set($lesson_excercise->get_question_set_id())->get_name();
					$name = Utilities::truncate_string($title_name, 20);
				}		
				else
					$name = Language::get_instance()->translate(62);
				$html[] = '<td style="border: none; padding: 0px;" width="120px" ' . ($title_name!=""?'title="' . $title_name . '"':'') . '>' . $name . '</td>';
				$name = "";
				$title_name = "";
				if($lesson_excercise->get_set_id())
				{
					$title_name = SetDataManager::instance(null)->retrieve_set($lesson_excercise->get_set_id())->get_name();
					$name = Utilities::truncate_string($title_name, 20);
				}
				else
					$name = Language::get_instance()->translate(62);
				$html[] = '<td style="border: none;" width="120px" ' . ($title_name!=""?'title="' . $title_name . '"':'') . '>' . $name . '</td>';
				$html[] = '<td style="border: none;" width="150px">';
				$users = $lesson_excercise->get_users(", ");
				$trunc_users = Utilities::truncate_string($users, 25);
				$html[] = '<div class="text" style="width: 150px" title="' . str_replace(", ", "\n",$users) . '">'.$trunc_users.'</div>';
				$html[] = '</td>';
				$html[] = '</tr>';
				$count++;
			}
			
			$html[] = '<tr>';
			$html[] = '</tr>';
			$html[] = '</table>';
			*/
		}
		else
			$html[] = "<p>" . Language::get_instance()->translate(165) . "</p>";
		$html[] = "</div>";
		$html[] = "<div style='width: 225px;' class='record_name'>" . Language::get_instance()->translate(939) . " :</div>";
		$html[] = "<div class='record_input'><input type='text' name='criteria_lesson_excercise_percentage' style='width:30px;' ".(is_null($lesson)?"value='0'":"value='".$lesson->get_criteria_lesson_excercise_percentage()."'")."> %</div><br class='clearfloat'>";
		$html[] = "</div>";
		$html[] = '</div>';
		$html[] = '</div>';
		
		$html[] = "<div class='record_name'>" . Language::get_instance()->translate(920) . " :</div><div class='record_input'><input type='checkbox' name='new' ".((!is_null($lesson)&&$lesson->get_new())?"CHECKED":"")."></div>";
		$lesson_rel = array();
		if(!is_null($lesson))
			$lesson_rel = $this->manager->get_data_manager()->retrieve_lesson_relations_by_lesson_id($lesson->get_id(), $this->manager->get_user()->get_id());
		$html[] = "<div class='record_name'>" . Language::get_instance()->translate(173) . "</div><div class='record_input'><input type='checkbox' name='add_pupils' ".((count($lesson_rel))?"CHECKED":"")."/></div><br class='clearfloat'/>";
		$html[] = "<div class='record_name'></div>";
		$html[] = '<div style="margin-left: 30px" id="pupils_div">';
		$pupils = UserDataManager::instance($this->manager)->retrieve_users_by_parent_id($this->manager->get_user()->get_id());
		$pupils = array_merge($pupils, UserDataManager::instance($this->manager)->retrieve_other_users_by_parent_id($this->manager->get_user()->get_id()));
		if(count($pupils))
		{
			$html[] = '<table style="border: none" id="pupil_lesson_table">';
			$count = 0;
			foreach($pupils as $pupil)
			{
				$html[] = '<tr>';
				$html[] = '<td style="border: none; padding: 0px;"><input type="checkbox" name="user_ids[]" value="'.$pupil->get_id().'" '.((in_array($pupil->get_id(), $lesson_rel))?"CHECKED":"").' /></td>';
				$html[] = '<td style="border: none" width="200px">'.$pupil->get_name().'</td>';
				$html[] = '</tr>';
				$count++;
			}
			$html[] = '</table>';
		}
		else
			$html[] = "<p>" . Language::get_instance()->translate(174) . "</p>";
		$html[] = '</div><br class="clearfloat"/>';
		if(!is_null($lesson))
		{
			$html[] = '<input type="hidden" name="id" value="'.$lesson->get_id().'">';		
			$html[] = "<input type='hidden' name='order' value='".$lesson->get_order()."' />";
			$html[] = "<input type='hidden' name='user_id' value='".$lesson->get_user_id()."' />";
		}
		$html[] = '<div class="record_button"><a id="submit_form" class="link_button" href="javascript:;">'.$submit.'</a></div>';
		$html[] = '</div>';
		$html[] = '</form>';
		return implode("\n", $html);
	}

	public function get_lesson_continuation_table()
	{
		$lesson_continuations = $this->manager->get_data_manager()->retrieve_lesson_continuations_by_user_id($this->manager->get_user()->get_id());
		
		$html[] = '<h3 class="title">'.Language::get_instance()->translate(1078).'</h3>';
		$table = new Table($lesson_continuations);
		$table_id = "lessons_continuations_table";
		$no_data_message = '<p>' . Language::get_instance()->translate(1076) . '</p>';
		$delete_title = Language::get_instance()->translate(1077);
		$classes_to_load = Path::get_path() . 'pages/lesson/lib/lesson_continuation.class.php';

		$table->set_attributes(array("id" => $table_id));
		$table->set_ids("id");
		$table->set_ids(array("id"));
		$table->set_editable(true);
		$table->set_editable_id("id");
		$table->set_no_data_message($no_data_message);
		$table->set_delete_title($delete_title);
		$table->add_class_to_load($classes_to_load);
		
		$columns = array();
		$column = new Column(Language::get_instance()->translate(54), "title_from_message");
		$column->set_style_attribute("width", "100px");
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(1032), "message_from_message", "truncate_string");
		$column->set_style_attribute("width", "150px");
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(1031), "to_user_text");
		$column->set_style_attribute("width", "100px");
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(146), "lesson_text");
		$column->set_style_attributes(array("width"=>"150px"));
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(18), "lesson_excercises_text");
		$column->set_style_attributes(array("width"=>"150px"));
		$columns[] = $column;
		$table->set_columns($columns);
		$html[] = $table->render_table();
		return implode("\n", $html);
	}
	
	public function get_lesson_continuation_request_form()
	{
		$html[] = "<div style='width: 800px; position: relative'>";
		$html[] = "<p class='title'>" . Language::get_instance()->translate(1193) . "</p><br class='clearfloat'/>";
		$html[] = '<form action="" method="post" id="form_shop_continuation">';
		$html[] = '<input type="hidden" name="shop_continuation" value="1">';
		$html[] = '<div class="record" style="padding-left: 40px;">';
		$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(1192) . " :</div>";
		$standard_message = Language::get_instance()->translate(1194);
		$html[] = "<div class='record_input'><textarea name='message_text' style='width:450px;height:200px;padding:3px; 5px;'>" . $standard_message . "</textarea></div><br class='clearfloat'/>";
		$html[] = '<div class="record_button"><a id="submit_form" style="float: right" class="link_button" href="javascript:;">' . Language::get_instance()->translate(762) . '</a></div><br class="clearfloat"/>';
		$html[] = '</div></form>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
	
	public function get_lesson_continuation_shop_form($lesson_continuation)
	{
		$html[] = "<div style='width: 800px; position: relative'>";
		$html[] = "<p class='title'>" . Language::get_instance()->translate(1188) . "</p><br class='clearfloat'/>";
		$html[] = "<p class='medium_title'>" . Language::get_instance()->translate(1081) . " :</p><br class='clearfloat'/>";
		
		$coaches = UserDataManager::instance(null)->retrieve_siblings_by_user($this->manager->get_user());
		$dm = new DifficultyManager($this->manager->get_user());
		$lesson = $this->manager->get_data_manager()->retrieve_lesson($lesson_continuation->get_lesson_id());
		$cluster = $dm->get_data_manager()->retrieve_difficulty_cluster_by_rating($lesson->get_rating());
		$credits_buy = RightManager::instance()->get_data_manager()->retrieve_location_right_meta_data(RightManager::LESSON_LOCATION_ID, "credits_buy_" . $cluster->get_id());
		if(is_null($credits_buy) || !is_numeric($credits_buy) || $credits_buy < 0)
			$credits_buy = 0;
		else
		{
			foreach($coaches as $coach)
			{
				if(RightManager::instance()->get_right_location_object(RightManager::LESSON_LOCATION_ID, $coach, $lesson->get_id(), true) != RightManager::NO_RIGHT)
					$credits_buy = 0;
			}
		}
		$credits_total = $credits_buy;
		$html[] = "<div class='record_output' style='margin-left: 20px;'><b>" . Language::get_instance()->translate(54) . ": </b>".$lesson->get_title()."</div><br class='clearfloat'/>";
		$html[] = "<div class='record_output' style='margin-left: 20px;'><b>" . Language::get_instance()->translate(55) . ": </b>" . $lesson->get_description()."</div><br class='clearfloat'/>";
		$html[] = "<div style='float: right; width: 40px; text-align: right; padding-right: 82px; margin-left: 7px;'>";
		$html[] = $credits_buy;
		$html[] = "</div>";
		$html[] = "<div style='float: right;'>";
		$html[] = Language::get_instance()->translate(835) . " :";
		$html[] = "</div>";
		$html[] = "<br class='clearfloat'/>";
		$html[] = "</br>";
		$exc_count = 1;
		foreach($lesson_continuation->get_lesson_excercise_ids() as $excercise_id)
		{
			$exc = $this->manager->get_lesson_excercise_manager()->get_data_manager()->retrieve_lesson_excercise($excercise_id);
			$cluster = $dm->get_data_manager()->retrieve_difficulty_cluster_by_rating($exc->get_rating());
			$credits_buy = RightManager::instance()->get_data_manager()->retrieve_location_right_meta_data(RightManager::LESSON_EXCERCISE_LOCATION_ID, "credits_buy_" . $cluster->get_id());
			if(is_null($credits_buy) || !is_numeric($credits_buy) || $credits_buy < 0)
				$credits_buy = 0;
			else
			{
				foreach($coaches as $coach)
				{
					if(RightManager::instance()->get_right_location_object(RightManager::LESSON_EXCERCISE_LOCATION_ID, $coach, $excercise_id, true) != RightManager::NO_RIGHT)
						$credits_buy = 0;
				}
			}
			$credits_total += $credits_buy;
			$html[] = "<div class='record_name'>" . Language::get_instance()->translate(149) . " ".$exc_count."</div><br class='clearfloat'/>";
			$html[] = "<div class='record_name'>" . Language::get_instance()->translate(54) . " :</div><div class='record_output'>".$exc->get_title()."</div><br class='clearfloat'/>";
			$html[] = "<div class='record_name'>" . Language::get_instance()->translate(55) . " :</div><div class='record_output'>".$exc->get_description()."</div><br class='clearfloat'/>";
			$html[] = "<div style='float: right; width: 40px; text-align: right; padding-right: 82px; margin-left: 7px;'>";
			$html[] = $credits_buy;
			$html[] = "</div>";
			$html[] = "<div style='float: right;'>";
			$html[] = Language::get_instance()->translate(835) . " :";
			$html[] = "</div>";
			$html[] = "<br class='clearfloat'/>";
			$exc_count++;
		}
		$html[] = "<div>";
		$html[] = "<div style='float: right; width: 40px; text-align: right; padding-right: 82px; padding-left: 7px;'>";
		$html[] = "<div class='horizontal_ruler'></div>";
		$html[] = $credits_total . "<br/>" . $this->manager->get_user()->get_credits(true);
		$html[] = "</div>";
		$html[] = "<div style='float: right; text-align: right;'>";
		$html[] = "<div class='horizontal_ruler'></div>";
		$html[] = Language::get_instance()->translate(838) . " :<br/>" . Language::get_instance()->translate(839) . " :";
		$html[] = "</div>";
		$html[] = "<br class='clearfloat'/>";
		$html[] = "<br/>";
		$html[] = "</div>";
		$html[] = '<form action="" method="post" id="form_shop_continuation">';
		$html[] = '<input type="hidden" name="shop_continuation" value="1">';
		$html[] = '<div class="record" style="padding-left: 40px;">';
		$html[] = '<div class="record_button"><a id="submit_form" style="float: right" class="link_button" href="javascript:;">' . Language::get_instance()->translate(877) . '</a></div><br class="clearfloat"/>';
		$html[] = '</div></form>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
	
	public function get_lesson_continuation_list($coach = false)
	{
		$lesson_continuations = $this->manager->get_data_manager()->retrieve_lesson_continuations_by_user_id($this->manager->get_user()->get_id(), false);
		
		$html[] = '<h3 class="title">'.Language::get_instance()->translate(1082).'</h3>';
		$count = 1;
		foreach($lesson_continuations as $continuation)
		{
			$html[] = "<p class='medium_title'>" . Language::get_instance()->translate(1081) . " ".$count."</p><br class='clearfloat'/>";			
			$html[] = "<div class='click_to_view'>";
			$html[] = "<div class='record_output click_to_view_out' style='margin-left: 20px;'><b>" . sprintf(Language::get_instance()->translate(1083), $continuation->get_from_user_text()) . ": " . $continuation->get_title_from_message() ." >>></b></div><br class='clearfloat'/>";
			$html[] = "<div class='click_to_view_in' style='display: none;'>";
			$html[] = "<div class='record_output' style='margin-left: 20px;'><b><<< " . sprintf(Language::get_instance()->translate(1083), $continuation->get_from_user_text()) . ": " . $continuation->get_title_from_message() ." </b></div><br class='clearfloat'/>";
			$html[] = "<div class='record_output' style='margin-left: 20px; width: 430px;'>" . nl2br($continuation->get_message_from_message()) . "</div><br class='clearfloat'/><br/>";
			$html[] = "</div>";
			$html[] = "</div>";
			
			$lesson = $this->manager->get_data_manager()->retrieve_lesson($continuation->get_lesson_id());
			$html[] = "<div class='record_output' style='margin-left: 20px;'><b>" . Language::get_instance()->translate(54) . ": </b>".$lesson->get_title()."</div><br class='clearfloat'/>";
			$html[] = "<div class='record_output' style='margin-left: 20px;'><b>" . Language::get_instance()->translate(55) . ": </b>" . $lesson->get_description()."</div><br class='clearfloat'/>";
			$url_array = array("page"=>"view_lesson", "id"=>$lesson->get_id());
			$buy_url_array = array("page"=>"buy_continuation", "id"=>$continuation->get_id());
			if($coach)
			{
				$url_array["coach"] = 1;
				$buy_url_array["coach"] = 1;
			}
			if($continuation->get_bought())
				$html[] = "<div class='record_output' style='margin-left: 20px;'><a class='text_link' href='" . Url::create_url($url_array) ."'>" . Language::get_instance()->translate(147) ."</a></div><br class='clearfloat'/>";
			elseif($continuation->get_requested())
				$html[] = "<div class='record_output' style='margin-left: 20px;'><a class='text_link' href='javascript:;'>" . Language::get_instance()->translate(1197) ."</a></div><br class='clearfloat'/>";
			else
				$html[] = "<div class='record_output' style='margin-left: 20px;'><a class='text_link' href='" . Url::create_url($buy_url_array) ."'>" . Language::get_instance()->translate(1188) ."</a></div><br class='clearfloat'/>";
			$html[] = "</br>";
			$exc_count = 1;
			foreach($continuation->get_lesson_excercise_ids() as $excercise_id)
			{
				$exc = $this->manager->get_lesson_excercise_manager()->get_data_manager()->retrieve_lesson_excercise($excercise_id);
				$html[] = "<div class='record_name'>" . Language::get_instance()->translate(149) . " ".$exc_count."</div><br class='clearfloat'/>";
				$html[] = "<div class='record_name'>" . Language::get_instance()->translate(54) . " :</div><div class='record_output'>".$exc->get_title()."</div><br class='clearfloat'/>";
				$html[] = "<div class='record_name'>" . Language::get_instance()->translate(55) . " :</div><div class='record_output'>".$exc->get_description()."</div><br class='clearfloat'/>";
				$url_arr = array("page"=>"view_excercise", "id"=>$exc->get_id());
				if($coach)
					$url_arr["coach"] = 1;
				if($continuation->get_bought())
					$html[] = "<div class='record_name'>&nbsp</div><div class='record_output'><a class='text_link' href='" . Url::create_url($url_arr) ."'>" . Language::get_instance()->translate(153) . "</a></div><br class='clearfloat'/><br/>";
				$exc_count++;
			}
			$count++;
		}
		return implode("\n", $html);
	}
	
	public function get_lesson_continuation_available_table($type_id)
	{
		$objects = $this->manager->get_data_manager()->retrieve_lesson_continuation_available_objects($type_id);
		uasort($objects, array(__class__, 'self::order_by_title'));
		$table = new Table($objects);
		$table_id = "lessons_av_table";
		$no_data_message = '<p>' . Language::get_instance()->translate(141) . '</p>';
		$delete_title = Language::get_instance()->translate(142);
		$classes_to_load = Path::get_path() . 'pages/lesson/lib/lesson.class.php';
		if($type_id == LessonContinuationAvailability::TYPE_LESSON_EXCERCISE)
		{
			$table_id = "lesson_excercise_av_table";
			$no_data_message = '<p>' . Language::get_instance()->translate(165) . '</p>';
			$delete_title = Language::get_instance()->translate(170);
			$classes_to_load = Path::get_path() . 'pages/lesson/lesson_excercise/lib/lesson_excercise.class.php';
		}
		$table->set_table_id($table_id);
		$table->set_attributes(array("id" => $table_id));
		$table->set_ids("id");
		$table->set_ids(array("id"));
		$table->set_editable(true);
		$table->set_editable_id("id");
		$table->set_no_data_message($no_data_message);
		$table->set_delete_title($delete_title);
		$table->add_class_to_load($classes_to_load);
		
		$columns = array();
		$column = new Column(Language::get_instance()->translate(54), "title");
		$column->set_style_attributes(array("width"=>"200px"));
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(55), "description");
		$column->set_style_attributes(array("width"=>"250px", "word-wrap" => "break-word"));
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(152), "themes");
		$column->set_style_attributes(array("width"=>"75px"));
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(274), "difficulty");
		$column->set_style_attributes(array("width"=>"75px"));
		$columns[] = $column;
		$table->set_columns($columns);
		$html[] = $table->render_table();
		return implode("\n", $html);
	}
	
	public function get_lesson_continuation_form($user, $lesson_continuation = null)
	{		
		require_once Path::get_path() . "pages/lesson/lesson_continuation_availability.page.php";
		$html = array();
		
		$html[] = '<h3 class="title">'.Language::get_instance()->translate(1053).'</h3>';
		$html[] = '<form action="" method="post" id="lesson_continuation_form">';
		$html[] = '<div class="record">';
		$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(1054) . " :</div>";
		$html[] = "<div class='record_output'>".$user->get_name()."</div><br class='clearfloat'/>";
		$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(54) . " :</div>";
		$html[] = "<div class='record_input'><input type='text' name='title' style='width:300px;' ".(is_null($lesson_continuation)?"":"value='".$lesson_continuation->get_message()->get_title()."'")."></div>";
		$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(1032) . " :</div>";
		$html[] = "<div class='record_input'><textarea class='limit_textarea' name='message' style='width:434px;height:100px;'>".(is_null($lesson_continuation)?"":$lesson_continuation->get_message()->get_message())."</textarea></div><br/>";


		$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(146) . " :</div>";
		$html[] = "<div class='record_input'>";
		$lesson_id = 0;
		$lesson_excercise_ids = array();
		if(!is_null($lesson_continuation))
		{
			$lesson_id = $lesson_continuation->get_lesson_id();
			$lesson_excercise_ids = $lesson_continuation->get_lesson_excercise_ids();
		}
		$udm = UserDataManager::instance(null);
		$users = $udm->retrieve_users_by_parent_id($udm->retrieve_highest_parent($this->manager->get_user())->get_id());
		$coaches = array();
		foreach($users as $user)
		{
			if($user->get_group_id() == GroupManager::GROUP_COACH_ID || $user->get_group_id() == GroupManager::GROUP_COACH_TEST_ID)
				$coaches[] = $user;
		}
		
		/*
		$standard_lessons = $this->manager->get_data_manager()->retrieve_lesson_continuation_available_objects(LessonContinuationAvailability::TYPE_LESSON);
		$standard_lesson_excercises = $this->manager->get_data_manager()->retrieve_lesson_continuation_available_objects(LessonContinuationAvailability::TYPE_LESSON_EXCERCISE);
		uasort($standard_lessons, array(__class__, 'self::order_by_title'));
		uasort($standard_lesson_excercises, array(__class__, 'self::order_by_title'));
		$standard_lessons[] = "break";
		$standard_lesson_excercises[] = "break";
		*/
		
		$lessons = array();
		$lesson_excercises = array();
		foreach($coaches as $coach)
		{
			$lessons = array_merge($lessons, $this->manager->get_data_manager()->retrieve_lessons('', $coach->get_id()));
			$lesson_excercises = array_merge($lesson_excercises, $this->manager->get_lesson_excercise_manager()->get_data_manager()->retrieve_lesson_excercises_by_user_id($coach->get_id()));
		}
		$filter_lessons = array();
		$filter_lesson_excercises = array();
		foreach($lessons as $index => $lesson)
		{
			if(!in_array($lesson->get_id(), $filter_lessons))
				$filter_lessons[] = $lesson->get_id();
			else
				unset($lessons[$index]);
		}
		foreach($lesson_excercises as $index => $lesson_excercise)
		{
			if(!in_array($lesson_excercise->get_id(), $filter_lesson_excercises))
				$filter_lesson_excercises[] = $lesson_excercise->get_id();
			else
				unset($lesson_excercises[$index]);
		}
		uasort($lessons, array(__class__, 'self::order_by_title'));
		uasort($lesson_excercises, array(__class__, 'self::order_by_title'));
		
		//$lessons = array_merge($standard_lessons, $lessons);
		//$lesson_excercises = array_merge($standard_lesson_excercises, $lesson_excercises);
		
		if(count($lessons))
		{
			$html[] = '<div style="width: 500px; height: 200px; overflow-y: auto; overflow-x: hidden;">';
			$html[] = '<table style="border: none" id="lesson_excercise_table">';
			$count = 0;
			foreach($lessons as $index => $lesson_e)
			{
				/*
				if($lesson_e != "break" && $index == 0)
				{
					$html[] = '<tr>';
					$html[] = '<td style="border: none; font-style: italic;" width="10px" colspan="2">' . Language::get_instance()->translate(1069) . '</td>';
					$html[] = '</tr>';
				}
				elseif($lesson_e == "break")
				{
					$html[] = '<tr>';
					$html[] = '<td style="border: none; font-style: italic" width="10px" colspan="2">' . Language::get_instance()->translate(1070) . '</td>';
					$html[] = '</tr>';
				}
				else
				{
				*/
					$html[] = '<tr>';
					$html[] = '<td style="border: none; padding: 0px;" width="10px"><input type="radio" name="lesson_id" value="'.$lesson_e->get_id().'" '.($lesson_id == $lesson_e->get_id()?"CHECKED":"").' /></td>';
					$html[] = '<td style="border: none" width="200px">'.$lesson_e->get_title().'</td>';
					$html[] = '</tr>';
				//}
				$count++;
			}
			$html[] = '</table>';
			$html[] = '</div>';
		}
		else
			$html[] = "<p>" . Language::get_instance()->translate(141) . "</p>";
		$html[] = "</div><br/>";
		
		$html[] = "<div class='record_name'>" . Language::get_instance()->translate(18) . " :</div>";
		$html[] = "<div class='record_input'>";
		//criteria lesson excercises
		if(count($lesson_excercises))
		{
			$html[] = '<div style="width: 500px; height: 200px; overflow-y: auto; overflow-x: hidden;">';
			$html[] = '<table style="border: none" id="lesson_table">';
			foreach($lesson_excercises as $index => $lesson_excercise_e)
			{
				/*
				if($lesson_excercise_e != "break" && $index == 0)
				{
					$html[] = '<tr>';
					$html[] = '<td style="border: none; font-style: italic" width="10px" colspan="2">' . Language::get_instance()->translate(1069) . '</td>';
					$html[] = '</tr>';
				}
				elseif($lesson_excercise_e == "break")
				{
					$html[] = '<tr>';
					$html[] = '<td style="border: none; font-style: italic" width="10px" colspan="2">' . Language::get_instance()->translate(1070) . '</td>';
					$html[] = '</tr>';
				}
				else
				{
				*/
					$in_exc_arr = false;
					if(!is_null($lesson_continuation))
						$in_exc_arr = in_array($lesson_excercise_e->get_id(),$lesson_continuation->get_lesson_excercise_ids());
					$html[] = '<tr>';
					$html[] = '<td style="border: none; padding: 0px;" width="10px"><input type="checkbox" name="lesson_excercise_ids[]" value="'.$lesson_excercise_e->get_id().'" '.($in_exc_arr?"CHECKED":"").' /></td>';
					$html[] = '<td style="border: none" width="200px">'.$lesson_excercise_e->get_title().'</td>';
					$html[] = '</tr>';
				//}
			}
			$html[] = '</table>';
			$html[] = '</div>';
		}
		else
			$html[] = "<p>" . Language::get_instance()->translate(165) . "</p>";
		$html[] = "</div>";
		$html[] = '<div class="record_button"><a id="submit_form" class="link_button" href="javascript:;">'.Language::get_instance()->translate(1030).'</a></div>';
		$html[] = '</div>';
		$html[] = '</form>';
		return implode("\n", $html);
	}
	
	public function get_lesson_continuation_availability_search($type_id)
	{
		$html = array();
		switch($type_id)
		{
			case LessonContinuationAvailability::ADD_LESSON: 
				$html[] = $this->get_lesson_search(false); break;
			case LessonContinuationAvailability::ADD_LESSON_EXCERCISE:
				$html[] = $this->manager->get_lesson_excercise_manager()->get_renderer()->get_lesson_excercise_search(false); break;
		}
		return implode("\n", $html);
	}
	
	public function get_lesson_page_text_form($lesson_page = null)
	{		
		$lesson_page_text = null;
		if(!is_null($lesson_page))
			$lesson_page_text = $this->manager->get_data_manager()->retrieve_lesson_page_text($lesson_page->get_type_object_id());
		$html = array();
		$html[] = "<input type='hidden' name='object_id' value='" . (is_null($lesson_page_text)?0:$lesson_page_text->get_id()) . "'/>";
		$html[] = "<div class='record_name_required'>Tekst :</div><div class='record_input'></div><br class='clearfloat'/>";
		$html[] = "<p style='margin-left: 20px; float: left;'><textarea class='mce_editor' name='text' style='width:550px;height:300px;'>" . (is_null($lesson_page_text)?"":$lesson_page_text->get_text())."</textarea></p><br class='clearfloat'/>";
		return implode("\n", $html);
	}
	
	public function get_lesson_page_puzzle_form($lesson_page = null, $radio = true)
	{		
		require_once Path :: get_path() . 'pages/puzzle/lib/puzzle_manager.class.php';
		$sm = new PuzzleManager($this->manager->get_user());
		$html = array();
		if(!is_null($lesson_page) && $lesson_page->get_type_object_id() != 0)
		{
			$puzzle = $sm->get_data_manager()->retrieve_puzzle_properties_by_puzzle_id($lesson_page->get_type_object_id());
			$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(187) . " :</div><div class='record_input'>";
			$html[] = '<div style="margin-top: 10px;" >';
			$html[] = '<div style="float: left">';
			$html[] = '<input type="' . ($radio?'radio':'checkbox') . '" name="' . ($radio?'object_id':'object_id[]') . '" value="'.$puzzle->get_puzzle_id().'" CHECKED />';
			$url =  Path::get_url_path() . 'pages/puzzle/images/' . $puzzle->get_puzzle_id() . '.gif';
		    if(!file_exists(Path::get_path() . 'pages/puzzle/images/' . $puzzle->get_puzzle_id() . '.gif'))
				$url = Path::get_url_path() . 'pages/puzzle/ajax/retrieve_puzzle_image.ajax.php?puzzle_id=' . $puzzle->get_puzzle_id();
			$html[] = '<img src="' . $url . '" style="vertical-align: top"/>';
			$html[] = '</div>';
			$html[] = '<div style="float: left">';
			$html[] = $sm->get_renderer()->get_description_puzzle($puzzle);
			$html[] = '</div>';
			$html[] = '</div></div><br class="clearfloat"/><br/>';
		}
		$select = is_null($lesson_page) || $lesson_page->get_type_object_id() == 0?Language::get_instance()->translate(188):Language::get_instance()->translate(189);
		$html[] = "<div class='record_name_required'>" . $select . " :</div>";
		$html[] = "<div class='record_input' id='search_form' style='max-height: 450px; overflow-y: scroll; position: relative;'>";
		$html[] = $sm->get_renderer()->get_puzzle_search(false, false);
		$html[] = "</div>";
		$html[] = "<div class='record_input' id='search_result' style='max-height: 450px; overflow-y: scroll; position: relative; display: none;'>";
		$html[] = "</div>";
		return implode("\n", $html);
	}
	
	public function get_lesson_page_puzzle_form_results($form_search = false, $radio = true)
	{		
		require_once Path :: get_path() . 'pages/puzzle/lib/puzzle_manager.class.php';
		$puzzle_manager = new PuzzleManager($this->manager->get_user());
		return $puzzle_manager->get_renderer()->get_puzzle_detailed_form($form_search, $radio?'object_id':'object_id[]', $radio, false, false, 0, 20);
	}
	
	public function get_lesson_page_set_form($lesson_page = null)
	{		
		require_once Path :: get_path() . 'pages/puzzle/lib/puzzle_manager.class.php';
		$puzzle_manager = new PuzzleManager($this->manager->get_user());
		$sm = $puzzle_manager->get_set_manager();
		$html = array();
		if(!is_null($lesson_page))
		{
			$set = $sm->get_data_manager()->retrieve_set($lesson_page->get_type_object_id());
			$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(190) . " :</div><div class='record_input'>";
			$html[] = '<div style="margin-top: 10px;" >';
			$html[] = '<div style="float: left">';
			$html[] = '<input type="' . ($radio?'radio':'checkbox') . '" name="' . ($radio?'object_id':'object_id[]') . '" value="'.$set->get_id().'" CHECKED />';
			$html[] = '</div>';
			$html[] = '<div style="float: left">';
			$html[] = $sm->get_renderer()->get_description_set($set);
			$html[] = '</div>';
			$html[] = '</div></div><br class="clearfloat"/><br/>';
		}	
		$select = is_null($lesson_page)?Language::get_instance()->translate(191):Language::get_instance()->translate(192);
		$html[] = "<div class='record_name_required'>" . $select . " :</div>";
		$html[] = "<div class='record_input' id='search_form' style='max-height: 450px; overflow-y: scroll;'>";
		$html[] = $sm->get_renderer()->get_set_search(false);
		$html[] = "</div>";
		$html[] = "<div class='record_input' id='search_result' style='max-height: 450px; overflow-y: scroll; position: relative; display: none;'>";
		$html[] = "</div>";
		return implode("\n", $html);
	}
	
	public function get_lesson_page_set_form_results($form_search = false)
	{		
		require_once Path :: get_path() . 'pages/puzzle/lib/puzzle_manager.class.php';
		$puzzle_manager = new PuzzleManager($this->manager->get_user());
		$set_manager = $puzzle_manager->get_set_manager();
		$html = array();
		$sets = null;
		if($form_search)
			$sets = $set_manager->get_data_manager()->retrieve_set_with_search_form(RightManager::READ_RIGHT);
		else
			$sets = $set_manager->get_data_manager()->retrieve_sets(RightManager::READ_RIGHT);
			
		$html[] = '<div class="record">';
		foreach($sets as $set)
		{
			$html[] = '<div style="margin-top: 10px;">';
			$html[] = '<div style="float: left">';
			$html[] = '<input type="radio" name="object_id" value="'.$set->get_id().'"/>';
			$html[] = '</div>';
			$html[] = '<div style="float: left">';
			$html[] = $set_manager->get_renderer()->get_description_set($set);
			$html[] = '</div>';
			$html[] = '<br class="clearfloat" />';
			$html[] = '</div>';
		}
		$html[] = '</div>';
		$html[] = '<br class="clearfloat" />';
		return implode("\n", $html);
	}
	
	public function get_lesson_page_game_form($lesson_page = null)
	{		
		require_once Path :: get_path() . 'pages/game/lib/game_manager.class.php';
		$game_manager = new GameManager($this->manager->get_user());
		$html = array();
		if(!is_null($lesson_page) && $lesson_page->get_type_object_id() != 0)
		{
			$game = $game_manager->get_data_manager()->retrieve_game_properties_by_game_id($lesson_page->get_type_object_id());
			$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(193) . " :</div><div class='record_input'>";
			$html[] = '<div style="margin-top: 10px;" >';
			$html[] = '<div style="float: left">';
			$html[] = '<input type="radio" name="object_id" value="'.$game->get_game_id().'" CHECKED />';
			$html[] = '</div>';
			$html[] = '<div style="float: left">';
			$html[] = $game_manager->get_renderer()->get_description_game($game);
			$html[] = '</div>';
			$html[] = '</div></div><br class="clearfloat"/><br/>';
		}
		$select = is_null($lesson_page)?Language::get_instance()->translate(194):Language::get_instance()->translate(195);
		$html[] = "<div class='record_name_required'>" . $select . " :</div>";
		$html[] = "<div id='search_form' style='max-height: 300px; overflow-y: scroll;'>";
		$html[] = $game_manager->get_renderer()->get_game_search(false);
		$html[] = "</div>";
		$html[] = "<div class='record_input' id='search_result' style='max-height: 450px; overflow-y: scroll; position: relative; display: none;'>";
		$html[] = "</div>";
		return implode("\n", $html);
	}
	
	public function get_lesson_page_game_form_results($form_search = false)
	{		
		require_once Path :: get_path() . 'pages/game/lib/game_manager.class.php';
		$game_manager = new GameManager($this->manager->get_user());
		$html = array();
		$games = null;
		if($form_search)
			$games = $game_manager->get_data_manager()->retrieve_game_properties_with_search_form(RightManager::READ_RIGHT);
		else
			$games = $game_manager->get_data_manager()->retrieve_all_game_properties(RightManager::READ_RIGHT);
			
		$html[] = '<div class="record">';
		foreach($games as $game)
		{
			$html[] = '<div style="margin-top: 10px;">';
			$html[] = '<div style="float: left">';
			$html[] = '<input type="radio" name="object_id" value="'.$game->get_game_id().'"/>';
			$html[] = '</div>';
			$html[] = '<div style="float: left">';
			$html[] = $game_manager->get_renderer()->get_description_game($game);
			$html[] = '</div>';
			$html[] = '<br class="clearfloat" />';
			$html[] = '</div>';
		}
		$html[] = '</div>';
		$html[] = '<br class="clearfloat"/>';
		return implode("\n", $html);
	}
	
	public function get_lesson_page_video_form($lesson_page = null)
	{		
		require_once Path :: get_path() . 'pages/video/lib/video_manager.class.php';
		$video_manager = new VideoManager($this->manager->get_user());
		$html = array();
		if(!is_null($lesson_page) && $lesson_page->get_type_object_id() != 0)
		{
			$video = $video_manager->get_data_manager()->retrieve_video_properties($lesson_page->get_type_object_id());
			if(!is_null($video))
			{
				$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(196) . " :</div><div class='record_input'>";
				$html[] = '<div style="margin-top: 10px;" >';
				$html[] = '<div style="float: left">';
				$html[] = '<input type="radio" name="object_id" value="'.$video->get_id().'" CHECKED />';
				$html[] = '</div>';
				$html[] = '<div style="float: left">';
				$html[] = $video_manager->get_renderer()->get_description_video($video);
				$html[] = '</div>';
				$html[] = '</div></div><br class="clearfloat"/><br/>';
			}
		}
		$select = is_null($lesson_page)?Language::get_instance()->translate(197):Language::get_instance()->translate(198);
		$html[] = "<div class='record_name_required'>" . $select . " :</div>";
		$html[] = "<div id='search_form' style='max-height: 300px; overflow-y: scroll;'>";
		$html[] = $video_manager->get_renderer()->get_video_search(false);
		$html[] = "</div>";
		$html[] = "<div class='record_input' id='search_result' style='max-height: 450px; overflow-y: scroll; position: relative; display: none;'>";
		$html[] = "</div>";
		$checked = 0;
		if(!is_null($lesson_page) && $lesson_page->get_next() == 1)
		{
			$checked = 1;
		}
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(199) . ' :</div><div class="record_input"><input type="checkbox" name="next" '.($checked?"checked='checked'":"").'></div>';		
		return implode("\n", $html);
	}
	
	public function get_lesson_page_video_form_results($form_search = false)
	{		
		require_once Path :: get_path() . 'pages/video/lib/video_manager.class.php';
		$video_manager = new VideoManager($this->manager->get_user());
		$html = array();
		$videos = null;
		if($form_search)
			$videos = $video_manager->get_data_manager()->retrieve_video_properties_with_search_form(RightManager::READ_RIGHT);
		else
			$videos = $video_manager->get_data_manager()->retrieve_all_video_properties(RightManager::READ_RIGHT);
			
		$html[] = '<div class="record">';
		foreach($videos as $video)
		{
			$html[] = '<div style="margin-top: 10px;">';
			$html[] = '<div style="float: left">';
			$html[] = '<input type="radio" name="object_id" value="'.$video->get_id().'"/>';
			$html[] = '</div>';
			$html[] = '<div style="float: left">';
			$html[] = $video_manager->get_renderer()->get_description_video($video);
			$html[] = '</div>';
			$html[] = '<br class="clearfloat" />';
			$html[] = '</div>';
		}
		$html[] = '</div>';
		$html[] = '<br class="clearfloat"/>';
		return implode("\n", $html);
	}
	
	public function get_lesson_page_question_form($lesson_page = null, $radio = true)
	{		
		require_once Path :: get_path() . 'pages/question/lib/question_manager.class.php';
		$qm = new QuestionManager($this->manager->get_user());
		$html = array();
		if(!is_null($lesson_page) && $lesson_page->get_type_object_id() != 0)
		{
			$question = $qm->get_data_manager()->retrieve_question($lesson_page->get_type_object_id());
			$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(200) . " :</div><div class='record_input'>";
			$html[] = '<div style="margin-top: 10px;" >';
			$html[] = '<div style="float: left">';
			$html[] = '<input type="' . ($radio?'radio':'checkbox') . '" name="' . ($radio?'object_id':'object_id[]') . '" value="'.$question->get_id().'" CHECKED />';
			$html[] = '</div>';
			$html[] = '<div style="float: left">';
			$html[] = $qm->get_renderer()->get_description_question($question);
			$html[] = '</div>';
			$html[] = '</div></div><br class="clearfloat"/><br/>';
		}
		$select = is_null($lesson_page) || $lesson_page->get_type_object_id() == 0?Language::get_instance()->translate(201):Language::get_instance()->translate(202);
		$html[] = "<div class='record_name_required'>" . $select . " :</div>";
		$html[] = "<div class='record_input' id='search_form' style='max-height: 450px; overflow-y: scroll;'>";
		$html[] = $qm->get_renderer()->get_question_search(false);
		$html[] = "</div>";
		$html[] = "<div class='record_input' id='search_result' style='max-height: 450px; overflow-y: scroll; position: relative; display: none;'>";
		$html[] = "</div>";
		return implode("\n", $html);
	}
	
	public function get_lesson_page_question_form_results($form_search = false, $radio = true)
	{
		require_once Path :: get_path() . 'pages/question/lib/question_manager.class.php';
		$question_manager = new QuestionManager($this->manager->get_user());
		$html = array();
		$questions = null;
		if($form_search)
			$questions = $question_manager->get_data_manager()->retrieve_questions_with_search_form(RightManager::READ_RIGHT);
		else
			$questions = $question_manager->get_data_manager()->retrieve_questions(RightManager::READ_RIGHT);
			
		$html[] = '<div class="record">';
		foreach($questions as $question)
		{
			$html[] = '<div style="margin-top: 10px;">';
			$html[] = '<div style="float: left">';
			$html[] = '<input type="' . ($radio?'radio':'checkbox') . '" name="' . ($radio?'object_id':'object_id[]') . '" value="'.$question->get_id().'"/>';
			$html[] = '</div>';
			$html[] = '<div style="float: left">';
			$html[] = $question_manager->get_renderer()->get_description_question($question);
			$html[] = '</div>';
			$html[] = '<br class="clearfloat" />';
			$html[] = '</div>';
		}
		$html[] = '</div>';
		$html[] = '<br class="clearfloat" />';
		return implode("\n", $html);
	}
	
	public function get_lesson_page_end_game_form($lesson_page = null)
	{		
		require_once Path :: get_path() . 'pages/game/lib/game_manager.class.php';
		require_once Path :: get_path() . 'pages/game/end_game/lib/end_game_manager.class.php';
		$eg = new EndGameManager($this->manager->get_user(), new GameManager($this->manager->get_user()));
		$html = array();
		if(!is_null($lesson_page) && $lesson_page->get_type_object_id() != 0)
		{
			$end_game = $eg->get_data_manager()->retrieve_end_game($lesson_page->get_type_object_id());
			$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(200) . " :</div><div class='record_input'>";
			$html[] = '<div style="margin-top: 10px;" >';
			$html[] = '<div style="float: left">';
			$html[] = '<input type="radio" name="object_id" value="'.$end_game->get_id().'" CHECKED />';
			$html[] = '</div>';
			$html[] = '<div style="float: left">';
			$html[] = $eg->get_renderer()->get_description_end_game($end_game);
			$html[] = '</div>';
			$html[] = '</div></div><br class="clearfloat"/><br/>';
		}
		$select = is_null($lesson_page) || $lesson_page->get_type_object_id() == 0?Language::get_instance()->translate(201):Language::get_instance()->translate(202);
		$html[] = "<div class='record_name_required'>" . $select . " :</div>";
		$html[] = "<div class='record_input' id='search_form' style='max-height: 450px; overflow-y: scroll;'>";
		$html[] = $eg->get_parent_manager()->get_renderer()->get_game_search(false);
		$html[] = "</div>";
		$html[] = "<div class='record_input' id='search_result' style='max-height: 450px; overflow-y: scroll; position: relative; display: none;'>";
		$html[] = "</div>";
		return implode("\n", $html);
	}
	
	public function get_lesson_page_end_game_form_results($form_search = false)
	{
		require_once Path :: get_path() . 'pages/game/lib/game_manager.class.php';
		require_once Path :: get_path() . 'pages/game/end_game/lib/end_game_manager.class.php';
		$end_game_manager = new EndGameManager($this->manager->get_user(), new GameManager($this->manager->get_user()));
		$html = array();
		$end_games = null;
		if($form_search)
			$end_games = $end_game_manager->get_data_manager()->retrieve_end_game_properties_with_search_form(RightManager::READ_RIGHT);
		else
			$end_games = $end_game_manager->get_data_manager()->retrieve_end_games(RightManager::READ_RIGHT);
			
		$html[] = '<div class="record">';
		foreach($end_games as $end_game)
		{
			$html[] = '<div style="margin-top: 10px;">';
			$html[] = '<div style="float: left">';
			$html[] = '<input type="radio" name="object_id" value="'.$end_game->get_end_game_id().'"/>';
			$html[] = '</div>';
			$html[] = '<div style="float: left">';
			$html[] = $end_game_manager->get_renderer()->get_shop_detail($end_game);
			$html[] = '</div>';
			$html[] = '<br class="clearfloat" />';
			$html[] = '</div>';
		}
		$html[] = '</div>';
		$html[] = '<br class="clearfloat" />';
		return implode("\n", $html);
	}
	
	public function get_lesson_page_selection_form($lesson_page = null, $radio = true)
	{		
		require_once Path :: get_path() . 'pages/selection/lib/selection_manager.class.php';
		$sm = new SelectionManager($this->manager->get_user());
		$html = array();
		if(!is_null($lesson_page) && $lesson_page->get_type_object_id() != 0)
		{
			$selection = $sm->get_data_manager()->retrieve_selection($lesson_page->get_type_object_id());
			$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(200) . " :</div><div class='record_input'>";
			$html[] = '<div style="margin-top: 10px;" >';
			$html[] = '<div style="float: left">';
			$html[] = '<input type="' . ($radio?'radio':'checkbox') . '" name="' . ($radio?'object_id':'object_id[]') . '" value="'.$selection->get_id().'" CHECKED />';
			$html[] = '</div>';
			$html[] = '<div style="float: left">';
			$html[] = $sm->get_renderer()->get_description_selection($selection);
			$html[] = '</div>';
			$html[] = '</div></div><br class="clearfloat"/><br/>';
		}
		$select = is_null($lesson_page) || $lesson_page->get_type_object_id() == 0?Language::get_instance()->translate(201):Language::get_instance()->translate(202);
		$html[] = "<div class='record_name_required'>" . $select . " :</div>";
		$html[] = "<div class='record_input' id='search_form' style='max-height: 450px; overflow-y: scroll;'>";
		$html[] = $sm->get_renderer()->get_selection_search(false);
		$html[] = "</div>";
		$html[] = "<div class='record_input' id='search_result' style='max-height: 450px; overflow-y: scroll; position: relative; display: none;'>";
		$html[] = "</div>";
		return implode("\n", $html);
	}
	
	public function get_lesson_page_selection_form_results($form_search = false, $radio = true)
	{
		require_once Path :: get_path() . 'pages/selection/lib/selection_manager.class.php';
		$selection_manager = new SelectionManager($this->manager->get_user());
		$html = array();
		$selections = null;
		if($form_search)
			$selections = $selection_manager->get_data_manager()->retrieve_selections_with_search_form(RightManager::READ_RIGHT);
		else
			$selections = $selection_manager->get_data_manager()->retrieve_selections(RightManager::READ_RIGHT);
			
		$html[] = '<div class="record">';
		foreach($selections as $selection)
		{
			$html[] = '<div style="margin-top: 10px;">';
			$html[] = '<div style="float: left">';
			$html[] = '<input type="' . ($radio?'radio':'checkbox') . '" name="' . ($radio?'object_id':'object_id[]') . '" value="'.$selection->get_id().'"/>';
			$html[] = '</div>';
			$html[] = '<div style="float: left">';
			$html[] = $selection_manager->get_renderer()->get_description_selection($selection);
			$html[] = '</div>';
			$html[] = '<br class="clearfloat" />';
			$html[] = '</div>';
		}
		$html[] = '</div>';
		$html[] = '<br class="clearfloat" />';
		return implode("\n", $html);
	}
	
	public function get_lesson_search($own_form = true)
	{
		require_once Path :: get_path() . "pages/puzzle/difficulty/lib/difficulty_manager.class.php";
		$html = array();
		if($own_form)
		{
			$html[] = '<form action="" method="post" id="lesson_search_form">';
			$html[] = '<div class="record">';
			$html[] = '<p><h3 class="title">' . Language::get_instance()->translate(59) . ':</h3></p>';
		}
		$theme_manager = new ThemeManager($this->manager->get_user());
		$difficulty_manager = new DifficultyManager($this->manager->get_user());
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(61) . ':</div><div class="record_input"><input type="text" name="keywords"/></div>';								
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(152) . ' :</div><div class="record_input">'.$theme_manager->get_renderer()->get_selector().'</div>';
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(274) . ' :</div><div class="record_input">'.$difficulty_manager->get_renderer()->get_selector().'</div>';
		$html[] = '<div id="object_search"></div>';
		$html[] = '<div class="record_button"><a id="submit_search_form" class="link_button" href="javascript:;">' . Language::get_instance()->translate(63) . '</a></div><br class="clearfloat"/>';
		if($own_form)
			$html[] = '</div></form>';
		return implode("\n", $html);
	}
	
	public function get_course_form($course)
	{
		$html = array();
		$html[] = '<h3 class="title">'.Language::get_instance()->translate(1228).'</h3>';
		$html[] = '<form action="" method="post" id="lesson_course_form">';
		$html[] = '<div class="record">';
		$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(66) . " :</div>";
		$html[] = "<div class='record_input'><input type='text' name='name' style='width:250px;' ".(is_null($lesson_course)?"":"value='".$lesson_course->get_name()."'")."></div>";
		$html[] = "<div class='record_name_required'>" . Language::get_instance()->translate(274) . " :</div>";
		$html[] = "<div class='record_input'><input type='text' name='rating' style='width:250px;'>".(is_null($lesson_course)?"":"value='".$lesson_course->get_rating()."'")."></div><br/>";
		$html[] = '<div class="record_button"><a id="submit_form" class="link_button" href="javascript:;">' . Language::get_instance()->translate(49) . '</a></div><br class="clearfloat"/>';
		$html[] = '</div></form>';
	}
	
	public function get_course_list()
	{
		$user = $this->manager->get_user();
		$parent_user = UserDataManager::instance(null)->retrieve_user(LessonDataManager::LESSON_COURSE_USER_ID);

		$html = array();
				
		$opened_maps = unserialize(Session::retrieve("opened_maps"));
		$first = true;
		$maps = RightDataManager::instance(null)->retrieve_location_user_maps(RightManager::LESSON_LOCATION_ID,$parent_user->get_id());
		$lessons_in_maps = array();
		$new_maps = array();
		foreach($maps as $index => $map)
		{
			$lessons = $this->manager->get_data_manager()->retrieve_lessons_by_visibility_and_criteria(true, $parent_user->get_id(), RightManager::READ_RIGHT, $map);
			if(count($lessons))
			{
				$lessons_in_maps[] = $lessons;
				$new_maps[] = $map;
			}
		}
		$maps = $new_maps;
		$count_maps = 1;
		$counted_maps = count($lessons_in_maps);
		foreach($lessons_in_maps as $index => $lessons)
		{
			if(count($lessons))
			{
				$map = $maps[$index];
				$is_opened = false;
				if(!is_null($opened_maps) && is_array($opened_maps) && in_array($map->get_id(), $opened_maps))
					$is_opened = true;
					
				$percentage = 0;
				$output = $this->get_lesson_course_list_user_lessons($lessons, $user, $parent_user, $first, $percentage, $count_maps);
				
				$html[] = '<div>';
				$html[] = '<div style="margin-left: 40px; margin-top: 10px;" class="map_div_title">';
				$html[] = '<h2 class="title map_title">'.sprintf(Language::get_instance()->translate(1255), $count_maps, $map->get_name()).'</h3>';
			
				if($percentage == 0) $percentage = 1;
				for($i=1;$i<=5;$i++)
				{
					$html[] = "<img title='" . $map->get_name() . "' src='" . Path::get_url_path() . "layout/images/icons/" . $this->get_percentage_picture_b($percentage, $i) . "' style='width: 75px; height: 75px;' >";
				}
				
				$html[] = '</div>';
				$html[] = '<div style="margin-left: 80px; display:' . ($is_opened?'block':'none') . ';" class="map_output">';
				$html[] = "<input type='hidden' value='" . $map->get_id() . "' name='map_id'/>";
				$html[] = $output;
				$html[] = '</div>';
				$html[] = '</div>';
				
				$html[] = "<br class='clearfloat'/>";
				if($index < $counted_maps-1)
					$html[] = '<div class="dashed_div" style="width: 450px;"></div>';
				
				$count_maps++;
			}
		}
		if(!GroupManager::is_free_group($this->manager->get_user()->get_group_id()))
		{
			$lessons_without_map = $this->manager->get_data_manager()->retrieve_lessons_by_visibility_and_criteria(true, $parent_user->get_id(), RightManager::READ_RIGHT, "others");
			$lessons = $lessons_without_map;
			if(count($lessons))
			{
				if(!$first)
					$html[] = '<div class="dashed_div" style="width: 450px;"></div>';
				
				$is_opened = false;
				if(!is_null($opened_maps) && is_array($opened_maps) && in_array(0, $opened_maps))
					$is_opened = true;
				$prev_first = $first;
				
				$percentage = 0;
				$output = $this->get_lesson_course_list_user_lessons($lessons, $user, $parent_user, $first, $percentage);
				
				$html[] = '<div>';
				if(!$prev_first)
				{
					$html[] = '<div class="map_div_title" style="margin-left: 40px; margin-top: 10px;">';
					$html[] = "<h3 class=\"title map_title\">" . Language::get_instance()->translate(902) . "</h3>";
				
					if($percentage == 0) $percentage = 1;
					for($i=1;$i<=5;$i++)
					{
						$html[] = "<img title='" . sprintf(Language::get_instance()->translate(1255), $count_maps, Language::get_instance()->translate(902)) . "' src='" . Path::get_url_path() . "layout/images/icons/" . $this->get_percentage_picture_b($percentage, $i) . "' style='width: 75px; height: 75px;' >";
					}
					
					$html[] = "</div>";
					$html[] = "<div style='margin-left: 80px; display:" . ($is_opened?'block':'none') . "' class='map_output'>";
					$html[] = "<input type='hidden' value='0' name='map_id'/>";
				}
				
				$html[] = $output;
				if(!$prev_first)
					$html[] = "</div>";
				$html[] = "</div>";
			}
		}
		return implode("\n", $html);
	}

	public function get_lesson_course_list_user_lessons($lessons, $user, $parent_user, &$first, &$avg_percentage, $count_maps = 1)
	{		
		$html = array();
		$avg_count = 1;	
		$opened_lessons = unserialize(Session::retrieve("opened_lessons"));
		$free_group = false;
		$group_id = $this->manager->get_user()->get_group_id();
		if(!$first && (GroupManager::is_free_group($group_id) || $group_id == GroupManager::GROUP_GUEST_ID))
			$free_group = true;
		foreach($lessons as $index => $lesson)
		{
			if($first)
			{
				$first = false;
			}
			
			$excercises = $this->manager->get_data_manager()->retrieve_excercise_from_lesson_criteria_lesson($lesson->get_id(), $parent_user->get_id());
			$count_exc = count($excercises);
			$excercise_percentages = array();
			$lesson_percentage = 0;
			
			$is_opened = false;
			if(!is_null($opened_lessons) && is_array($opened_lessons) && in_array($lesson->get_id(), $opened_lessons))
				$is_opened = true;
				
			if(!$free_group || ($free_group && $count_maps == 2 && $index <= 1))
			{
				$this->manager->get_data_manager()->check_criteria($lesson, $user, true, false, false);
			}
			else
			{
				$lesson->set_visible(false);
				$lesson->set_teaser(true);
				$lesson->set_teaser_free(true);
			}
			$html[] = "<div style='" . ((!$lesson->get_visible())?"color: #999999":"") . "'>";
			/*
			$html[] = "<div class='details_to_show' style='display: none;'>";
			$html[] = "<br class='clearfloat'/>";
			$html[] = "</div>";
			*/
			$html[] = "<div style='position: relative; float: left; margin-bottom: 10px; margin-right: 10px;'>";
			if($lesson->get_teaser_free())
			{
				$html[] = "<div style='position: absolute; top: -10px; left: -4px; color: #CC0000; font-size: 14px; font-family: Arial Black'>x</div>";
				$html[] = "<div style='position: absolute; top: -8px; right: 0px; font-size: 10px'><a class='text_link' href='" . Url::create_url(array("page"=>($group_id == GroupManager::GROUP_GUEST_ID?"register":"upgrade"))) . "'>" . ($group_id == GroupManager::GROUP_GUEST_ID?Language::get_instance()->translate(1256):Language::get_instance()->translate(1247)) . "</a></div>";
			}
			$html[] = "<a href='javascript:;' class='show_details'>";
			if($lesson->get_visible())
			{
				$pages = $this->manager->get_data_manager()->count_lesson_pages($lesson->get_id());
				$viewed = StatisticsDataManager::instance($this->manager)->count_statistics_actions_lesson_views($this->manager->get_user()->get_id(), $lesson->get_id(), $pages);
				$percentage = $pages!=0?($viewed/$pages)*100:0;
				$lesson_percentage = $percentage;
				$total = 1;
				if($count_exc)
				{
					if($lesson->get_visible())
					{
						foreach($excercises as $exc)
						{
							$number = $this->manager->get_data_manager()->count_lesson_excercise_components($exc->get_id());
							$correct = StatisticsDataManager::instance($this->manager)->count_statistics_set_succes($this->manager->get_user()->get_id(), $exc->get_id());
							$correct += StatisticsDataManager::instance($this->manager)->count_statistics_question_set_succes($this->manager->get_user()->get_id(), $exc->get_id());
							$correct += StatisticsDataManager::instance($this->manager)->count_statistics_selection_set_succes($this->manager->get_user()->get_id(), $exc->get_id());
							$excercise_percentages[] = $number!=0?($correct/$number)*100:0;
							$percentage += $number!=0?($correct/$number)*100:0;
							$total++;
						}
					}
				}
				$percentage = $percentage/$total;
				if($percentage == 0) $percentage = 1;
				for($i=1;$i<=5;$i++)
				{
					$html[] = "<img title='" . $lesson->get_title() . "' src='" . Path::get_url_path() . "layout/images/icons/" . $this->get_percentage_picture_b($percentage, $i) . "' style='width: 24px; height: 24px;'>";
				}
				
				$avg_count++;
				$avg_percentage += $percentage;
			}
			else
			{
				for($i=1;$i<=5;$i++)
				{
					$html[] = "<img title='" . $lesson->get_title() . "' src='" . Path::get_url_path() . "layout/images/icons/" . $this->get_percentage_picture_b(0, $i) . "' style='width: 24px; height: 24px;'>";
				}
				
				$avg_count++;
			}
			$html[] = "</a>";
			$html[] = "<div style='height: 18px; overflow: hidden;' title='" . $lesson->get_title() . "'><div style='width: 200px;'>" . $lesson->get_title() . "</div></div></div>";
			$html[] = "<div class='details_to_show' " . ($is_opened?"":"style='display: none;'") . ">";
			$html[] = "<input type='hidden' value='" . $lesson->get_id() . "' name='lesson_id'/>";
			$html[] = "<div class='menu_item_sub' style='float: left; margin-left: 20px;'>";
			$html[] = "<p class='small_title'" . ((!$lesson->get_visible())?" style='color: #999999; font-style: normal;'":" style='font-style: normal;'") . ">" . $lesson->get_title()." " . ($lesson->get_visible()?"[".floor($lesson_percentage) . "%]":"") . "</p>";
			$html[] = "<p>" . nl2br($lesson->get_description())."</p>";
			if($lesson->get_visible())
			{
				$url_array = array("page"=>"view_lesson", "id"=>$lesson->get_id());
				$html[] = "<div class='record_output'><a class='text_link' href='" . Url::create_url($url_array) ."'>" . Language::get_instance()->translate(147) ."</a></div><br class='clearfloat'/>";
			}

			if($count_exc)
			{
				$html[] = "</br><div class='record_output' style='margin-left: 20px;'><b>" . Language::get_instance()->translate(18) . ": </b></div><div class='record_output' style='margin-left: 20px;'>";
				foreach($excercises as $index => $excercise)
				{
					$html[] = "<p class='small_title' style='font-style: normal; " . ((!$lesson->get_visible())?"color: #999999'":"'") . ">" . ($lesson->get_visible()?"<a style='font-weight: normal' href='" . Url::create_url(array("page"=>"view_excercise", "id" => $excercise->get_id(), "coach" => 1)) ."'>" . $excercise->get_title()."</a>":$excercise->get_title()) . " " . (isset($excercise_percentages[$index])?"[".floor($excercise_percentages[$index])."%]":"") . "</p>";
				}
				$html[] = "</div><br class='clearfloat'/>";
			}
			
			if(!$lesson->get_visible())
			{
				$count = count($lesson->get_criteria_lesson_excercise_ids());
				if($lesson->get_criteria_lesson_id() || $count)
					$html[] = "<div class='record_output' style='margin-left: 20px;'><b>" . Language::get_instance()->translate(949) . ": </b></div><br class='clearfloat'/>";
				if($lesson->get_criteria_lesson_id())
	   			{
					$criteria_lesson_id = $lesson->get_criteria_lesson_id();
						
					$c_lesson = $this->manager->get_data_manager()->retrieve_lesson($lesson->get_criteria_lesson_id());
					$html[] = "<div class='record_output' style='margin-left: 50px;'>" . sprintf(Language::get_instance()->translate(968), $lesson->get_criteria_lesson_percentage(), $c_lesson->get_title()) . "</div><br class='clearfloat'/>";
	   			}
	   			
	   			if($count)
	   			{
	   				$criteria_lesson_excercise_ids = $lesson->get_criteria_lesson_excercise_ids();
					
					//require_once Path::get_path() . "/pages/puzzle/set/lib/set_data_manager.class.php";
					//require_once Path::get_path() . "/pages/question/question_set/lib/question_set_data_manager.class.php";
					$excs = array();
					foreach($criteria_lesson_excercise_ids as $id)
					{
						$exc = $this->manager->get_lesson_excercise_manager()->get_data_manager()->retrieve_lesson_excercise($id);
						$exc->set_user_id($parent_user->get_id());
						$excs[] = $exc->get_title();
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
		   				$html[] = "<div class='record_output' style='margin-left: 50px;'>" . sprintf(Language::get_instance()->translate(969), $lesson->get_criteria_lesson_excercise_percentage(), $output) . "</div><br class='clearfloat'/>";
		   			}
	   			}
			}
			$html[] = "</br></div>";
			$html[] = "<br class='clearfloat'/></div></div>";
		}
		
		$avg_percentage = $avg_percentage/$avg_count;
		return implode("\n", $html);
	}
	
	/**BACKUP**/
	public function get_lesson_course_list_user_lessons_backup($lessons, $user, $parent_user, &$first)
	{
		$html = array();
		$count = 1;
		foreach($lessons as $lesson)
		{
			if($first)
			{
				$first = false;
			}
			
			$excercises = $this->manager->get_data_manager()->retrieve_excercise_from_lesson_criteria_lesson($lesson->get_id(), $parent_user->get_id());
			$count_exc = count($excercises);
			$excercise_percentages = array();
			$lesson_percentage = 0;
			
			$this->manager->get_data_manager()->check_criteria($lesson, $user, true, false, false);
			$html[] = "<div style='" . ((!$lesson->get_visible())?"color: #999999":"") . "'>";
			$html[] = "<div class='details_to_show' style='display: none;'>";
			$html[] = "<br class='clearfloat'/>";
			$html[] = "</div>";
			$html[] = "<div style='float: left; margin-bottom: 10px; margin-right: 10px;'>";
			$html[] = "<a href='javascript:;' class='show_details'>";
			if($lesson->get_visible())
			{
				$pages = $this->manager->get_data_manager()->count_lesson_pages($lesson->get_id());
				$viewed = StatisticsDataManager::instance($this->manager)->count_statistics_actions_lesson_views($this->manager->get_user()->get_id(), $lesson->get_id(), $pages);
				$percentage = $pages!=0?($viewed/$pages)*100:0;
				$lesson_percentage = $percentage;
				$total = 1;
				if($count_exc)
				{
					if($lesson->get_visible())
					{
						foreach($excercises as $exc)
						{
							$number = $this->manager->get_data_manager()->count_lesson_excercise_components($exc->get_id());
							$correct = StatisticsDataManager::instance($this->manager)->count_statistics_set_succes($this->manager->get_user()->get_id(), $exc->get_id());
							$correct += StatisticsDataManager::instance($this->manager)->count_statistics_question_set_succes($this->manager->get_user()->get_id(), $exc->get_id());
							$correct += StatisticsDataManager::instance($this->manager)->count_statistics_selection_set_succes($this->manager->get_user()->get_id(), $exc->get_id());
							$excercise_percentages[] = $number!=0?($correct/$number)*100:0;
							$percentage += $number!=0?($correct/$number)*100:0;
							$total++;
						}
					}
				}
				$percentage = $percentage/$total;
				$html[] = "<img title='" . $lesson->get_title() . "' src='" . Path::get_url_path() . "layout/images/icons/" . $this->get_percentage_picture($percentage, true) . "' style='width: 100px; height: 100px;' >";
			}
			else
				$html[] = "<img title='" . $lesson->get_title() . "' src='" . Path::get_url_path() . "layout/images/icons/" . $this->get_percentage_picture(0, false) . "' style='width: 100px; height: 100px;' >";
			$html[] = "</a>";
			$html[] = "<div style='width: 100px; height: 18px; overflow: hidden;' title='" . $lesson->get_title() . "'><div style='width: 200px;'>" . $lesson->get_title() . "</div></div></div>";
			$html[] = "<div class='details_to_show' style='display: none;'>";
			$html[] = "<div class='menu_item_sub' style='float: left; margin-left: 20px;'>";
			$html[] = "<p class='small_title'" . ((!$lesson->get_visible())?" style='color: #999999; font-style: normal;'":" style='font-style: normal;'") . ">" . $lesson->get_title()." " . ($lesson->get_visible()?"[".floor($lesson_percentage) . "%]":"") . "</p>";
			$html[] = "<p>" . nl2br($lesson->get_description())."</p>";
			if($lesson->get_visible())
			{
				$url_array = array("page"=>"view_lesson", "id"=>$lesson->get_id());
				$html[] = "<div class='record_output'><a class='text_link' href='" . Url::create_url($url_array) ."'>" . Language::get_instance()->translate(147) ."</a></div><br class='clearfloat'/>";
			}

			if($count_exc)
			{
				$html[] = "</br><div class='record_output' style='margin-left: 20px;'><b>" . Language::get_instance()->translate(18) . ": </b></div><div class='record_output' style='margin-left: 20px;'>";
				foreach($excercises as $index => $excercise)
				{
					$html[] = "<p class='small_title' style='font-style: normal; " . ((!$lesson->get_visible())?"color: #999999'":"'") . ">" . ($lesson->get_visible()?"<a style='font-weight: normal' href='" . Url::create_url(array("page"=>"view_excercise", "id" => $excercise->get_id(), "coach" => 1)) ."'>" . $excercise->get_title()."</a>":$excercise->get_title()) . " " . (isset($excercise_percentages[$index])?"[".floor($excercise_percentages[$index])."%]":"") . "</p>";
				}
				$html[] = "</div><br class='clearfloat'/>";
			}
			
			if(!$lesson->get_visible())
			{
				if($lesson->get_criteria_lesson_percentage() || $lesson->get_criteria_lesson_excercise_percentage())
					$html[] = "<div class='record_output' style='margin-left: 20px;'><b>" . Language::get_instance()->translate(949) . ": </b></div><br class='clearfloat'/>";
				if($lesson->get_criteria_lesson_percentage())
	   			{
					$criteria_lesson_id = $lesson->get_criteria_lesson_id();
						
					$c_lesson = $this->manager->get_data_manager()->retrieve_lesson($lesson->get_criteria_lesson_id());
					$html[] = "<div class='record_output' style='margin-left: 50px;'>" . sprintf(Language::get_instance()->translate(968), $lesson->get_criteria_lesson_percentage(), $c_lesson->get_title()) . "</div><br class='clearfloat'/>";
	   			}
	   			
	   			if($lesson->get_criteria_lesson_excercise_percentage())
	   			{
	   				$criteria_lesson_excercise_ids = $lesson->get_criteria_lesson_excercise_ids();
					
					//require_once Path::get_path() . "/pages/puzzle/set/lib/set_data_manager.class.php";
					//require_once Path::get_path() . "/pages/question/question_set/lib/question_set_data_manager.class.php";
					$excs = array();
					foreach($criteria_lesson_excercise_ids as $id)
					{
						$exc = $this->manager->get_lesson_excercise_manager()->get_data_manager()->retrieve_lesson_excercise($id);
						$exc->set_user_id($parent_user->get_id());
						$excs[] = $exc->get_title();
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
		   				$html[] = "<div class='record_output' style='margin-left: 50px;'>" . sprintf(Language::get_instance()->translate(969), $lesson->get_criteria_lesson_excercise_percentage(), $output) . "</div><br class='clearfloat'/>";
		   			}
	   			}
			}
			$html[] = "</br></div>";
			$html[] = "<br class='clearfloat'/></div></div>";
			$count++;
		}
		return implode("\n", $html);
	}
	
	public function get_chexxl_convertor_form($conversion=null)
	{
		$html = array();
		$html[] = '<div id="chexxl_convertor_feedback"></div>';
		$html[] = '<form id="chexxl_convertor_form" action="" method="post">';
		$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'pages/lesson/javascript/lesson_chexxl_convertor.js"></script>';
		$html[] = '<div class="record">';
		$html[] = "<div class='record_name_required'>".Language::get_instance()->translate(54).":</div><div class='record_input'><input type='text' name='title' value='" . (is_null($conversion)?"":$conversion->get_title()) . "' style='width:300px;'></div>";
		$html[] = "<div class='record_name_required'>".Language::get_instance()->translate(1032).":</div><div class='record_input'></div>";
		$html[] = "<p style='float: left;'><textarea class='mce_editor' name='text' style='width:550px;height:300px;'>" . (is_null($conversion)?"":$conversion->get_text())."</textarea></p><br class='clearfloat'/>";
		$html[] = "<div class='record_name_required'>".Language::get_instance()->translate(1322).":</div><div class='record_input'>";
			$html[] = '<script type="text/javascript">';
			$html[] = '  var appName = "";';
			$html[] = '  var language = "' . Language::get_instance()->get_language() . '";';
			$html[] = '  var userId = ' . $this->manager->get_user()->get_id() . ';';
			$html[] = '  var flashvars = "userId="+userId+"&language="+language;';
			$html[] = '</script>';
			$html[] = '<div style="padding-top: 10px;">';
			include Path::get_path() . "/flash/fileUploader.php";
			$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '<div class="record_button"><a id="submit_chexxl_conversion_form" class="link_button" href="javascript:;">'.Language::get_instance()->translate(49).'</a></div></form>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
	
	public function get_percentage_picture($percentage, $visible)
	{
		if($percentage<25)
		{
			if($visible)
				return "oefening1.png";
			else
				return "oefening1_gr.png"; 
		}
		elseif($percentage<50 && $percentage>=25)
		{
			if($visible)
				return "oefening2.png";
			else
				return "oefening2_gr.png"; 
		}
		elseif($percentage<75 && $percentage>=50)
		{
			if($visible)
				return "oefening3.png";
			else
				return "oefening3_gr.png"; 
		}
		elseif($percentage<100 && $percentage>=75)
		{
			if($visible)
				return "oefening4.png";
			else
				return "oefening4_gr.png"; 
		}
		else
		{
			if($visible)
				return "oefening5.png";
			else
				return "oefening5_gr.png"; 
		}
	}
	
	public function get_percentage_picture_b($percentage, $number)
	{
		switch($number)
		{
			case 1: if($percentage<1){return "oefening1_gr.png";}else{return "oefening1.png";}; break;
			case 2: if($percentage<25){return "oefening2_gr.png";}else{return "oefening2.png";}; break;
			case 3: if($percentage<50){return "oefening3_gr.png";}else{return "oefening3.png";}; break;
			case 4: if($percentage<75){return "oefening4_gr.png";}else{return "oefening4.png";}; break;
			case 5: if($percentage<100){return "oefening5_gr.png";}else{return "oefening5.png";}; break;
		}
	}
	
	public function get_lesson_row_header()
	{
		$html = array();
		$html[] = '<th>#</th>';
		$html[] = '<th>' . Language::get_instance()->translate(54) . '</th>';
		$html[] = '<th>' . Language::get_instance()->translate(55) . '</th>';
		$html[] = '<th>' . Language::get_instance()->translate(143) . '</th>';
		return implode("\n", $html);
	}
	
	public function get_lesson_row_render($lesson)
	{
		$html = array();
		$html[] = '<td>';
		$html[] = $lesson->get_id();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $lesson->get_title();
		$html[] = '</td>';
		$html[] = '<td style="word-wrap: break-word; width: 350px">';
		$html[] = $lesson->get_description();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $lesson->get_users();
		$html[] = '</td>';
		return implode("\n", $html);
	}
	
	public function get_description_lesson($lesson)
	{
		$html = array();
		$html[] = '<div class="record_name"># :</div><div class="record_output">'.$lesson->get_id().'</div><br class="clearfloat"/>';
		$html[] = "<div class='record_name'>" . Language::get_instance()->translate(54) . " :</div><div class='record_output'>".$lesson->get_title()."</div><br class='clearfloat'/>";
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(55) . ' :</div><div class="record_output">'.$lesson->get_description().'</div><br class="clearfloat"/>';
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(143) . ' :</div><div class="record_output">'.$lesson->get_users().'</div><br class="clearfloat"/>';
		return implode("\n", $html);
	}
	
	public function get_type_selector($id = 0, $name='type_id', $no_text=false)
	{
		require_once Path ::get_path() . 'pages/lesson/lib/lesson_page.class.php';
		$html[] = '<select class="input_element" name="'.$name.'" style="min-width: 150px;">';
		$html[] = '<option value="0">' . Language::get_instance()->translate(161) . ':</option>';
		$arr = array();
		if(!$no_text)
			$arr[LessonPage::TEXT_TYPE] = Language::get_instance()->translate(205);
		$arr[LessonPage::PUZZLE_TYPE] = Language::get_instance()->translate(206);
		$arr[LessonPage::GAME_TYPE] = Language::get_instance()->translate(64);
		$arr[LessonPage::VIDEO_TYPE] = Language::get_instance()->translate(207);
		$arr[LessonPage::QUESTION_TYPE] = Language::get_instance()->translate(208);
		$arr[LessonPage::END_GAME_TYPE] = Language::get_instance()->translate(686);
		$arr[LessonPage::SELECTION_TYPE] = Language::get_instance()->translate(1002);
		
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
	
	
	function get_lesson_image($count_pages, $i, $current_page)
	{
		$rect_width = floor((200 - 5*$count_pages)/$count_pages);
		if($rect_width > 18)
			$rect_width = 18;
			
		$image = imagecreate($rect_width + 2, 20);
		$background = imagecolorallocate($image, 255, 255, 255);
		imagecolortransparent($image, $background);
		
		$space_width = 5;
		$group_id = 0;
		if(!is_null($this->manager->get_user()->get_group()))
			$group_id = $this->manager->get_user()->get_group_id();
        if(true /*$group_id==GroupManager::GROUP_PUPIL_ID*/)
        {
			$enabled_colour = imagecolorallocate($image, 150, 191, 13);
			$disabled_colour = imagecolorallocate($image, 204, 213, 153);
        }
        else
        {
			$enabled_colour = imagecolorallocate($image, 173, 71, 14);
			$disabled_colour = imagecolorallocate($image, 201, 120, 60);
        }
		$x = 0;
		$y = 1;
		$cx = $rect_width;
		$cy = 19;
		$rad = floor(($cx-$x)/2);
		$col = $enabled_colour;
		if($i+1>$current_page)
			$col = $disabled_colour;
		$this->image_fill_rounded_rect($image,$x,$y,$cx,$cy,$rad,$col);
		header('Content-type: image/gif');
		imagegif($image);
		imagedestroy($image);
	}

	function image_fill_rounded_rect($im,$x,$y,$cx,$cy,$rad,$col)
	{
	
	// Draw the middle cross shape of the rectangle
	
	    imagefilledrectangle($im,$x,$y+$rad,$cx,$cy-$rad,$col);
	    imagefilledrectangle($im,$x+$rad,$y,$cx-$rad,$cy,$col);
	
	    $dia = $rad*2;
	
	// Now fill in the rounded corners
	
	    imagefilledellipse($im, $x+$rad, $y+$rad, $rad*2, $dia, $col);
	    imagefilledellipse($im, $x+$rad, $cy-$rad, $rad*2, $dia, $col);
	    imagefilledellipse($im, $cx-$rad, $cy-$rad, $rad*2, $dia, $col);
	    imagefilledellipse($im, $cx-$rad, $y+$rad, $rad*2, $dia, $col);
	}

	public static function order_by_title($a, $b)
	{
		return strcmp($a->get_title(), $b->get_title());
	}
}

?>