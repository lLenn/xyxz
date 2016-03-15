<?php

require_once Path::get_path() . 'pages/user/lib/user_forms.class.php';

class UserRenderer
{

	private static $data_provider = array();
	private $manager;
	private $forms;
	
	function UserRenderer($manager)
	{
		$this->manager = $manager;
		$this->forms = new UserForms($manager);
	}
	
	public function get_forms_renderer()
	{
		return $this->forms;
	}
	
	public function render_user_manager()
	{
		$user = $this->manager->get_user();
		$group_id = $user->get_group_id();
		
		$highest_user = $user;
		while(!is_null($highest_user->get_parent_id()) && $highest_user->get_parent_id() != 0)
		{
			$highest_user = $this->manager->get_data_manager()->retrieve_user($highest_user->get_parent_id());
		}
		
		$html = array();
		$html[] = "<div style='position: relative'>";
		$html[] = $this->render_children($highest_user);
		$html[] = "</div>";
		$html[] = "<br class='clearfloat'/>";
		$html[] = "<div id='back_up'></div>";
		return implode("\n", $html);
	}
	
	public function render_children($user, $highest_level = true, $level = 0, $height = false)
	{
		$html = array();
		$children = $this->manager->get_data_manager()->retrieve_users_by_parent_id($user->get_id());
		$height_style = "";
		if($height !== false)
		{
			 $height_style = "height: " . ($height+1)*26 . "px;";
		}
		if($level!=2)
			$html[] = "<div style='float:left; margin: 20px 3px 3px 3px; " . $height_style . "' " . ($level==1?"class='drop_zone " . $user->get_id() . "'":"") . ">";
		$html[] = '<div class="user_name ' . ($level!=2?RightManager::instance()->get_allowed_objects("User", $user) . ' ':'') . ($level == 2?$user->get_id() . ' draggable':'') . '" ' . ($highest_level?'style="margin: 0 auto;"':'') . '>';
		$html[] = '<div style="float: left; width: 150px; text-align: center">'.$user->get_name().'</div>';
		$html[] = '<div style="float: right;">';
		$edit_allowed = RightManager::instance()->get_right_location_object("user", $this->manager->get_user(), $user->get_id()) >= RightManager::UPDATE_RIGHT;
		if($level!=2 && $edit_allowed && RightManager::instance()->check_right_location(RightManager::WRITE_RIGHT, "User", $user))
		{
			$html[] = '<img class="add_user ' . $user->get_id() . '" style="border: none" src="' . Path::get_url_path() . 'layout/images/buttons/add.png">';
		}
		if($edit_allowed)
		{
			$html[] = '<img class="edit_user ' . $user->get_id() . '" style="border: none" src="' . Path::get_url_path() . 'layout/images/buttons/edit.png">';
		}
		if(count($children)==0 && $edit_allowed)
		{
			$html[] = '<img class="remove_user ' . $user->get_id() . '" style="border: none" src="' . Path::get_url_path() . 'layout/images/buttons/delete.png">';
		}
		$html[] = '</div>';
		$html[] = '</div>';
		$highest_children = 0;
		
		if($level!=2)
		{
			foreach($children as $child)
			{
				$childs_children = $this->manager->get_data_manager()->retrieve_users_by_parent_id($child->get_id());
				$count = count($childs_children);
				$highest_children = $count>$highest_children?$count:$highest_children;
			}
			
			foreach($children as $child)
			{
				$html[] = $this->render_children($child, false, $level + 1, $highest_children);
			}
			
			if($level!=1)
				$html[] = "<br class='clearfloat'/>";
			$html[] = "</div>";
		}
		if($level == 0)
		{
			$html[] = '<script type="text/javascript">';
			$html[] = '  var drop_zone_height = ' . ($highest_children+1)*26 . ';';
			$html[] = '</script>';
		}
		return implode("\n", $html);
	}
	
	public static function get_menu_profile_html($user)
	{
		$html = array();
		if($user->get_avatar()!="")	$html[] = '<p align="center" style="width: 100px; margin: 10px auto;"><img src="'.$user->get_avatar().'" style="width: 100px"></p>';
		$html[] = '<ul class="menu menu_actions menu_vertical"><li><a href="' . Url :: create_url(array("action" => "browser_members")) . '" style="text-align: center;">'.$user->get_name().'</a><li>';
		$html[] = '<li><a href="' . Url :: create_url(array("action" => "logout")) . '" style="text-align: center;">' . Language::get_instance()->translate(461) . '</a></li></ul>';
		return implode("\n", $html);
	}
	
	public function get_profile_html($user)
	{
		$html = array();

		$html[] = "<p>";
		if($user->get_avatar()!="")	$html[] = "<img src='".$user->get_avatar()."' width='100'>";		
		$str = "<h2 class='title'>";
		if($this->manager->get_user()->get_id()==$user->get_id()) $str .= Language::get_instance()->translate(462);
		else	$str .= sprintf(Language::get_instance()->translate(463),$user->get_firstName());
		$html[] = $str . "</h2></p><br/>";

		$html[] = '<div class="record" style="padding-left: 25px;">';

		$html[] = '<h4 class="small_title">' . Language::get_instance()->translate(437) . '</h4>';
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(438) . ' :</div><div class="record_output">'.$user->get_username().'</div><br class="clearfloat"/>';
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(439) . ' :</div><div class="record_output">'.$user->get_email().'</div><br class="clearfloat"/>';

		$chess_profile = $this->manager->get_data_manager()->retrieve_user_chess_profile($user->get_id());
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(274) . ' :</div><div class="record_output">'.$chess_profile->get_rating().'</div><br class="clearfloat"/>';	
		if($this->manager->get_user()->is_admin())
			$html[] = '<div class="record_name">' . Language::get_instance()->translate(458) . ' :</div><div class="record_output">'.$chess_profile->get_rd().'</div><br class="clearfloat"/>';	

		$group_manager = new GroupManager($this->manager->get_user());
		$groups = $group_manager->get_data_manager()->retrieve_groups_by_right($this->manager->get_user()->get_group_id());
		$count_groups = count($groups);

		if($this->manager->get_user()->is_admin() || $count_groups)
			$html[] = '<p><h4 class="small_title">' . Language::get_instance()->translate(440) . '</h4>';
		if($count_groups)
		{
			$group = 'Geen';
			if(!is_null($user) && !is_null($user->get_group()))
			{
				$group = $user->get_group()->get_name();
			}
			$html[] = '<div class="record_name">' . Language::get_instance()->translate(92) . ' :</div><div class="record_output">'.$group.'</div><br class="clearfloat"/>';
		}
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(441) . ' :</div><div class="record_output">'.$user->get_parent().'</div><br class="clearfloat"/>';
		$first = true;
		foreach($user->get_extra_parent_ids() as $parent_id)
		{
			$html[] = '<div class="record_name">' . ($first?Language::get_instance()->translate(442) . ' :':'') . '</div><div class="record_output">'.$this->manager->get_data_manager()->retrieve_user($parent_id)->get_name().'</div><br class="clearfloat"/>';
			$first = false;
		}	
		if(($this->manager->get_user()->is_admin() || $this->manager->get_user()->get_group_id() == GroupManager::GROUP_COACH_ID || $this->manager->get_user()->get_group_id() == GroupManager::GROUP_CLUB_ID) && $user->get_group_id() != GroupManager::GROUP_PUPIL_ID)
		{
			$html[] = '<div class="record_name">' . Language::get_instance()->translate(793) . ' :</div><div class="record_output">'.$user->get_credits(true).'</div><br class="clearfloat"/>';	
			$html[] = '<div class="record_name">' . Language::get_instance()->translate(389) . ' :</div><div class="record_output">'. count(RightDataManager::instance(null)->retrieve_made_objects($user->get_id(), RightManager::USER_LOCATION_ID)) .'/' . RightManager::instance()->get_allowed_objects(RightManager::USER_LOCATION_ID, $user) . '</div><br class="clearfloat"/>';
		}	
		
		if($this->manager->get_user()->is_admin())
		{
			$activated =  "Vals";
			if($user->get_activation_code() == 1)
				$activated =  "Waar";
			$html[] = '<div class="record_name">' . Language::get_instance()->translate(443) . ' :</div><div class="record_output">'.$activated.'</div><br class="clearfloat"/>';
		}
		
		if(RightManager::instance()->get_right_location_object("user", $this->manager->get_user(), $user->get_id()) >= RightManager::READ_RIGHT)
		{
			$html[] = '<br/>';
			$html[] = '<h4 class="small_title">' . Language::get_instance()->translate(444) . '</h4>';
			$html[] = '<div class="record_name">' . Language::get_instance()->translate(445) . ' :</div><div class="record_output">'.$user->get_firstname().'</div><br class="clearfloat"/>';
			$html[] = '<div class="record_name">' . Language::get_instance()->translate(446) . ' :</div><div class="record_output">'.$user->get_lastname().'</div><br class="clearfloat"/>';			
			$html[] = '<div class="record_name">' . Language::get_instance()->translate(630) . ' :</div><div class="record_output">' .LanguageRenderer::get_full_name($user->get_language()).'</div><br class="clearfloat"/>';									
			$html[] = '<div class="record_name">' . Language::get_instance()->translate(787) . ' :</div><div class="record_output">' . $user->get_sex_full() . '</div><br class="clearfloat"/>';	
			if($user->get_address()!="")	$html[] = '<div class="record_name">' . Language::get_instance()->translate(448) . ' :</div><div class="record_output">'.nl2br($user->get_address()).'</div><br class="clearfloat"/>';
			
			$sub_members = $this->manager->get_data_manager()->retrieve_users_by_parent_id($user->get_id());
			if(count($sub_members))
			{
				$sub_group = $group_manager->get_data_manager()->retrieve_sub_group_by_group_id($user->get_group_id());
				if(!is_null($sub_group))
					$html[] = '<h4 class="small_title">' . $sub_group->get_name() . '</h4>';
				foreach ($sub_members as $sub_member)
				{
					$html[] = '<div class="record_name">&nbsp;</div><div class="record_output"><a class="text_link" style="display: block; width: 150px; text-align: center;" href="' . Url::create_url(array("page"=>"browse_members", "id"=>$sub_member->get_id(), "parent_id"=>$user->get_id())) .'">'.$sub_member->get_name().'</a></div><br class="clearfloat"/>';
				}
			}
			/*
			if(RightManager::instance()->get_right_location_object("user", $this->manager->get_user(), $user->get_id()) >= RightManager::UPDATE_RIGHT)
			{
				$um = new UserManager($user);
				require_once Path::get_path() . 'pages/lesson/lib/lesson_data_manager.class.php';
				require_once Path::get_path() . 'pages/lesson/lesson_excercise/lib/lesson_excercise_data_manager.class.php';
				require_once Path::get_path() . 'pages/puzzle/lib/puzzle_data_manager.class.php';
				require_once Path::get_path() . 'pages/puzzle/set/lib/set_data_manager.class.php';
				require_once Path::get_path() . 'pages/game/lib/game_data_manager.class.php';
				require_once Path::get_path() . 'pages/game/end_game/lib/end_game_data_manager.class.php';
				require_once Path::get_path() . 'pages/question/lib/question_data_manager.class.php';
				require_once Path::get_path() . 'pages/question/question_set/lib/question_set_data_manager.class.php';
				require_once Path::get_path() . 'pages/video/lib/video_data_manager.class.php';
				if(LessonDataManager::instance($um)->count_lessons($user->get_id()))
				{
					$html[] = '<h4 id="lesson_title_ajax" class="small_title"><img id="add_lesson_ajax" style="left: -22px; top: 1px; border: 0; position: relative;" src="' . Path::get_url_path() . '/layout/images/buttons/add.png"/><span style="position: relative; left: -14px;">' . Language::get_instance()->translate(765) . '</span></h4>';
					$html[] = '<div id="lesson_block_ajax">';
					$html[] = '</div>';
				}
				if(LessonExcerciseDataManager::instance($um)->count_lesson_excercises_by_user_id($user->get_id()))
				{
					$html[] = '<h4 id="lesson_excercise_title_ajax" class="small_title"><img id="add_lesson_excercise_ajax" style="left: -22px; top: 1px; border: 0; position: relative;" src="' . Path::get_url_path() . '/layout/images/buttons/add.png"/><span style="position: relative; left: -14px;">' . Language::get_instance()->translate(782) . '</span></h4>';
					$html[] = '<div id="lesson_excercise_block_ajax">';
					$html[] = '</div>';
				}
				if(PuzzleDataManager::instance($um)->count_puzzles())
				{		
					$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'pages/puzzle/javascript/puzzle_browser.js"></script>';
					$html[] = '<h4 id="puzzle_title_ajax" class="small_title"><img id="add_puzzle_ajax" style="left: -22px; top: 1px; border: 0; position: relative;" src="' . Path::get_url_path() . '/layout/images/buttons/add.png"/><span style="position: relative; left: -14px;">' . Language::get_instance()->translate(769) . '</span></h4>';
					$html[] = '<div id="puzzle_block_ajax">';
					$html[] = '</div>';
				}
				if(SetDataManager::instance($um)->count_sets())
				{		
					$html[] = '<h4 id="puzzle_set_title_ajax" class="small_title"><img id="add_puzzle_set_ajax" style="left: -22px; top: 1px; border: 0; position: relative;" src="' . Path::get_url_path() . '/layout/images/buttons/add.png"/><span style="position: relative; left: -14px;">' . Language::get_instance()->translate(770) . '</span></h4>';
					$html[] = '<div id="puzzle_set_block_ajax">';
					$html[] = '</div>';
				}
				if(GameDataManager::instance($um)->count_games())
				{		
					$html[] = '<h4 id="game_title_ajax" class="small_title"><img id="add_game_ajax" style="left: -22px; top: 1px; border: 0; position: relative;" src="' . Path::get_url_path() . '/layout/images/buttons/add.png"/><span style="position: relative; left: -14px;">' . Language::get_instance()->translate(776) . '</span></h4>';
					$html[] = '<div id="game_block_ajax">';
					$html[] = '</div>';
				}
				if(EndGameDataManager::instance($um)->count_end_games())
				{		
					$html[] = '<h4 id="end_game_title_ajax" class="small_title"><img id="add_end_game_ajax" style="left: -22px; top: 1px; border: 0; position: relative;" src="' . Path::get_url_path() . '/layout/images/buttons/add.png"/><span style="position: relative; left: -14px;">' . Language::get_instance()->translate(778) . '</span></h4>';
					$html[] = '<div id="end_game_block_ajax">';
					$html[] = '</div>';
				}
				if(QuestionDataManager::instance($um)->count_questions())
				{		
					$html[] = '<h4 id="question_title_ajax" class="small_title"><img id="add_question_ajax" style="left: -22px; top: 1px; border: 0; position: relative;" src="' . Path::get_url_path() . '/layout/images/buttons/add.png"/><span style="position: relative; left: -14px;">' . Language::get_instance()->translate(773) . '</span></h4>';
					$html[] = '<div id="question_block_ajax">';
					$html[] = '</div>';
				}
				if(QuestionSetDataManager::instance($um)->count_question_sets())
				{		
					$html[] = '<h4 id="question_set_title_ajax" class="small_title"><img id="add_question_set_ajax" style="left: -22px; top: 1px; border: 0; position: relative;" src="' . Path::get_url_path() . '/layout/images/buttons/add.png"/><span style="position: relative; left: -14px;">' . Language::get_instance()->translate(774) . '</span></h4>';
					$html[] = '<div id="question_set_block_ajax">';
					$html[] = '</div>';
				}
				if(VideoDataManager::instance($um)->count_videos())
				{		
					$html[] = '<h4 id="video_title_ajax" class="small_title"><img id="add_video_ajax" style="left: -22px; top: 1px; border: 0; position: relative;" src="' . Path::get_url_path() . '/layout/images/buttons/add.png"/><span style="position: relative; left: -14px;">' . Language::get_instance()->translate(780) . '</span></h4>';
					$html[] = '<div id="video_block_ajax">';
					$html[] = '</div>';
				}
			}
			*/
		}
		
		
		$html[] = '<input type="hidden" name="user_id" value="' . $user->get_id() . '">';
		$html[] = '<br>';
		$html[] = '</div>';
		
		return implode("\n", $html);
	}
	
	public function get_mini_profile($user, $link_page = null, $link_text = null)
	{
		$html = array();
		$html[] = '<div style="float: left; margin-bottom: 10px;">';
		$width = 75;
		if(!is_null($link_page) && !is_array($link_page))
			$link_page = array($link_page);
		if(!is_null($link_text) && !is_array($link_text))
			$link_text = array($link_text);
		$condition = !is_null($link_page) && !is_null($link_text) && count($link_page) == count($link_text);
		if($condition)
		{
			$width = 100;
		}
		$html[] = '<div style="float: left; width: '.$width.'px;">';
		if($user->get_avatar()!="")
		{
			$html[] = "<img src='".$user->get_avatar()."' width='".$width."'>";
		}
		$html[] = '</div>';
		$html[] = '<div style="float: left; width: 400px;">';
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(438) . ' :</div><div class="record_output">'.$user->get_username().'</div><br class="clearfloat"/>';
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(66) . ' :</div><div class="record_output">'.$user->get_name().'</div><br class="clearfloat"/>';
		$chess_profile = $this->manager->get_data_manager()->retrieve_user_chess_profile($user->get_id());
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(274) . ' :</div><div class="record_output">'.$chess_profile->get_rating().'</div><br class="clearfloat"/>';
		$html[] = '<br/>';
		if($condition)
		{
			foreach($link_page as $index => $page)
			{
				$text = $link_text[$index];
				$html[] = '<div class="record_name"><a class="text_link" href="'.Url::create_url(array('page' => $page, 'id' => $user->get_id())).'">'.$text.'</a></div><br class="clearfloat"/>';
			}
		}
		$html[] = '</div>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
	
	public function get_user_requests_table()
	{
		$req = $this->manager->get_data_manager()->retrieve_user_requests();
		$table = new Table($req);
		$table->set_table_id("request_table");
		$table->set_attributes(array("id" => "request_table"));
		$table->set_style_attributes(array("width" => "95%"));
		$table->set_ids(array("id"));
		$table->set_row_link("add_member", "request");
		$table->set_no_data_message(Language::get_instance()->translate(764));
		$table->set_editable(true);
		$table->set_editable_id("id");
		$table->set_delete_title(Language::get_instance()->translate(763));
		
		$columns = array();
		$columns[] = new Column( Language::get_instance()->translate(445), "firstname");
		$columns[] = new Column( Language::get_instance()->translate(446), "lastname");
		$columns[] = new Column( Language::get_instance()->translate(439), "email");
		$columns[] = new Column( Language::get_instance()->translate(630), "language");
		$columns[] = new Column( Language::get_instance()->translate(787), "sex_full");
		$columns[] = new Column( Language::get_instance()->translate(757), "accounttype");
		$column = new Column( Language::get_instance()->translate(449), "message");
		$column->set_style_attribute("width", "200px");
		$columns[] = $column;
		$table->set_columns($columns);
		
		return $table->render_table();
	}
	
	public function get_club_registration_table()
	{
		$req = $this->manager->get_data_manager()->retrieve_club_registrations();
		$table = new Table($req);
		$table->set_table_id("club_registration_table");
		$table->set_attributes(array("id" => "club_registration_table"));
		$table->set_style_attributes(array("width" => "95%"));
		$table->set_ids(array("id"));
		$table->set_row_link("browse_club_registrations", "add_id");
		$table->set_no_data_message(Language::get_instance()->translate(764));
		
		$columns = array();
		$columns[] = new Column( Language::get_instance()->translate(1189), "organisation_to_text");
		$columns[] = new Column( Language::get_instance()->translate(438), "username");
		$columns[] = new Column( Language::get_instance()->translate(445), "firstname");
		$columns[] = new Column( Language::get_instance()->translate(446), "lastname");
		$columns[] = new Column( Language::get_instance()->translate(439), "email");
		$columns[] = new Column( Language::get_instance()->translate(630), "language");
		$columns[] = new Column( Language::get_instance()->translate(787), "sex_full");
		//$columns[] = new Column( Language::get_instance()->translate(642), "coaches_full");
		$columns[] = new Column( Language::get_instance()->translate(1125), "created_full");
		//$columns[] = new Column( Language::get_instance()->translate(1136), "code");
		//$columns[] = new Column( Language::get_instance()->translate(1137), "price");
		//$columns[] = new Column( ucfirst(Language::get_instance()->translate(1223)), "end_date_to_txt");
		$columns[] = new Column( Language::get_instance()->translate(1234), "registration_type_to_text");
		$table->set_columns($columns);
		
		return $table->render_table();
	}
	
	public function get_club_upgrade_table()
	{
		$req = $this->manager->get_data_manager()->retrieve_club_upgrades();
		$table = new Table($req);
		$table->set_table_id("club_upgrade_table");
		$table->set_attributes(array("id" => "club_upgrade_table"));
		$table->set_style_attributes(array("width" => "95%"));
		$table->set_ids(array("id"));
		$table->set_row_link("browse_club_upgrades", "add_id");
		$table->set_no_data_message(Language::get_instance()->translate(764));
		
		$columns = array();
		$columns[] = new Column( Language::get_instance()->translate(438), "username");
		$columns[] = new Column( Language::get_instance()->translate(1234), "infinite_to_text");
		$columns[] = new Column( Language::get_instance()->translate(1125), "upgraded_full");
		$columns[] = new Column( Language::get_instance()->translate(1136), "code");
		$columns[] = new Column( Language::get_instance()->translate(1137), "price");
		$columns[] = new Column( ucfirst(Language::get_instance()->translate(1223)), "end_date_to_txt");
		$table->set_columns($columns);
		
		return $table->render_table();
	}
	
	public function get_icon()
	{
		return '<img src="' . Path :: get_url_path() . 'layout/images/icons/user_icon.png" style="border: 0"/>';
	}
	
	public function get_actions()
	{
		$html = array();
		$html[] = '<ul class="menu menu_actions menu_vertical" id="menu_actions">';
		if(Alias::instance()->get_alias(Request::get("page")) == UserManager::USER_REGISTER && is_null($this->manager->get_user()))
		{
			for($i=1;$i<10;$i++)
				$html[] = '<br/>';
			$html[] = '<li>';
			$html[] = '<a href="' . Url :: create_url(array()) . '" title="' . Language::get_instance()->translate(422) . '">' . Language::get_instance()->translate(422) . '</a>';
			$html[] = '</li>';
		}
		if(Alias::instance()->get_alias(Request::get("page")) == UserManager :: USER_BROWSER)
		{
			if($this->manager->get_user()->is_admin())
			{
				$html[] = '<br/>';
				$html[] = '<li>';
				$html[] = '<a href="' . Url :: create_url(array('page' => 'browse_requests')) . '">' . Language::get_instance()->translate(762) . '</a>';
				$html[] = '</li>';
				$html[] = '<li>';
				$html[] = '<a href="' . Url :: create_url(array('page' => 'browse_club_registrations')) . '">' . Language::get_instance()->translate(1124) . '</a>';
				$html[] = '</li>';
				$html[] = '<li>';
				$html[] = '<a href="' . Url :: create_url(array('page' => 'browse_club_upgrades')) . '">' . Language::get_instance()->translate(1250) . '</a>';
				$html[] = '</li>';
			}
			
			$id = Request::get("id");
			$group_id = $this->manager->get_user()->get_group_id();
			$parent_id = Request::get("parent_id");
			$parent_user = $this->manager->get_user();
			if(!is_null($parent_id) && is_numeric($parent_id))
			{
				$parent_user = $this->manager->get_data_manager()->retrieve_user($parent_id);
			}
			if((is_null($id) || !is_numeric($id)))
			{
				$id = $parent_user->get_id();
			}
			$user = $this->manager->get_data_manager()->retrieve_user($id);
			$exlude_location_right = $this->manager->get_user()->is_admin() || RightManager::instance()->check_right_location(RightManager::WRITE_RIGHT, "user", $user, false, true);
			$location_right = $this->manager->get_user()->is_admin() || RightManager::instance()->check_right_location(RightManager::WRITE_RIGHT, "user", $user);
			$object_right = RightManager::instance()->get_right_location_object("user", $parent_user, $id) >= RightManager::WRITE_RIGHT;
			if($exlude_location_right && GroupManager::group_is_not_test($this->manager->get_user()->get_group_id()))
			{
				$html[] = '<br/>';
				$html[] = '<li>';
				$html[] = '<a href="' . Url :: create_url(array('page' => 'manage_members')) . '" title="' . Language::get_instance()->translate(133) . '">' . Language::get_instance()->translate(464) . '</a>';
				$html[] = '</li>';
				if(!$this->manager->get_user()->is_admin() && !GroupManager::is_free_group($group_id))
				{
					$html[] = '<li>';
					$html[] = '<a href="' . Url :: create_url(array('page' => 'transfer_credits')) . '" title="' . Language::get_instance()->translate(1175) . '">' . Language::get_instance()->translate(1175) . '</a>';
					$html[] = '</li>';
				}
				
			}
			if($object_right)
			{
				if($location_right)
				{
					if($this->manager->get_user()->is_admin() || (($this->manager->get_user()->get_group_id() == GroupManager::GROUP_CLUB_ID) || ($this->manager->get_user()->get_group_id() == GroupManager::GROUP_COACH_ID && (is_null($parent_id) && !is_numeric($parent_id)))))
					{
						$importVars = array('page' => 'import_list');
						if(!is_null($parent_id) && is_numeric($parent_id))
						{
							$importVars['id'] = $id;
						}
						
						$html[] = '<li>';
						$html[] = '<a href="' . Url :: create_url($importVars) . '" title="' . Language::get_instance()->translate(1364) . '">' . Language::get_instance()->translate(1364) . '</a>';
						$html[] = '</li>';
					}
					$member = Language::get_instance()->translate(465);
					if(!$this->manager->get_user()->is_admin() && ($this->manager->get_user()->get_group_id() == GroupManager::GROUP_CLUB_ID || $this->manager->get_user()->get_group_id() == GroupManager::GROUP_FREE_CLUB_ID) && (is_null($parent_id) || !is_numeric($parent_id)))
					{
						$member = Language::get_instance()->translate(466);
					}
					elseif (!$this->manager->get_user()->is_admin())
					{
						$member = Language::get_instance()->translate(467);
					}
					$url_arr = array('page' => 'add_member');
					if(!$this->manager->get_user()->is_admin())
					{
						$url_arr['parent_id'] = $id;
					}
					$html[] = '<li>';
					$html[] = '<a href="' . Url :: create_url($url_arr) . '" title="' . Language::get_instance()->translate(49) . '">' . $member . '</a>';
					$html[] = '</li>';
				}
				$html[] = '<br/>';
				$html[] = '<li>';
				$html[] = '<a href="' . Url :: create_url(array('page' => 'edit_member', 'id' => $id)) . '" title="' . Language::get_instance()->translate(50) . '">' . Language::get_instance()->translate(468) . '</a>';
				$html[] = '</li>';
				if($this->manager->get_user()->get_id() != $id)
				{
					$html[] = '<li>';
					$html[] = "<a href='javascript: confirmation(\"" . Language::get_instance()->translate(470) . "\", \"" . Url :: create_url(array('page' => 'remove_member', 'id' => $id)) . "\")' title='" . Language::get_instance()->translate(51) . "'>" . Language::get_instance()->translate(469) . "</a>";
					$html[] = '</li>';
				}
				if($id != $this->manager->get_user()->get_id())
				{
					$html[] = '<li>';
					$html[] = "<a href='javascript: confirmation(\"" . Language::get_instance()->translate(913) . "\", \"" . Url :: create_url(array('page' => 'reset_password', 'user_id' => $id)) . "\")' title='" . Language::get_instance()->translate(904) . "'>" . Language::get_instance()->translate(904) . "</a>";
					$html[] = '</li>';
				}
			}
		}
		$html[] = '</ul>';
		return implode("\n", $html);
	}
	
	public function get_selector($id=0,$name='user_id',$data_provider = null, $multiple = false, $exclude_self = false, $first = '')
	{
		if(!is_array($id))
		{
			$id = array($id);
		}
		$html[] = '<select class="input_element" name="'.$name.'" style="width: 150px;" ' . ($multiple?'multiple size="5"':'') . '>';
		$html[] = '<option value="0">' . ($first==''?Language::get_instance()->translate(108):$first) . ':</option>';
		if(is_null($data_provider))
		{
			$arr =  $this->manager->get_data_manager()->retrieve_users();
		}
		else
		{
			$arr = $data_provider;
		}
		foreach($arr as $value)
		{
			if($value->get_id() != $this->manager->get_user()->get_id() || !$exclude_self)
			{
				$str = '<option value="'.$value->get_id().'"';
				if(in_array($value->get_id(), $id)) $str .= "selected='selected'";
				$str .= ">".$value->get_name()." - " .$value->get_username()."</option>";
				$html[] = $str;
			}
		}
		$html[] = '</select>';
		return implode("\n", $html);
	}
	
	public function get_selector_organisations($id=0,$name='organisation_id',$data_provider = null)
	{
		if(!is_array($id))
		{
			$id = array($id);
		}
		$html[] = '<select class="input_element" name="'.$name.'" style="width: 300px;" >';
		$html[] = '<option value="0">' . Language::get_instance()->translate(1188) . ':</option>';
		if(is_null($data_provider))
		{
			$arr =  $this->manager->get_data_manager()->retrieve_organisations();
		}
		else
		{
			$arr = $data_provider;
		}
		foreach($arr as $value)
		{
			$str = '<option value="'.$value->get_id().'"';
			if(in_array($value->get_id(), $id)) $str .= "selected='selected'";
			$str .= ">".$value->get_name()."</option>";
			$html[] = $str;
		}
		$html[] = '</select>';
		return implode("\n", $html);
	}
	public function check_image($bool)
	{
		if($bool==true)	
		{
			return "<img src='./img/correct.png' border='0'>";
		}
		else
		{
			return "<img src='./img/error.png' border='0'>";
		}
	}
	
	public function get_users_table($right = RightManager :: READ_RIGHT)
	{
		$html = array();
		$html[] = "<h3 class=\"title\">" . Language::get_instance()->translate(471) . "</h3>";
		$users = $this->manager->get_data_manager()->retrieve_users($right);
				
		$table = new Table($users);
		$table->set_table_id("user_table");
		$table->set_attributes(array("id" => "user_table"));
		$table->set_ids(array("id"));
		if($this->manager->get_user()->is_admin())
		{
			$action = new Action("reset_password", "user_id", Language::get_instance()->translate(904), '', 'buttons/email.png');
			$table->add_action($action);
			$action = new Action("change_member", "user_id", Language::get_instance()->translate(768), '', 'buttons/right.png');
			$table->add_action($action);
		}
		$table->set_row_link("browse_members", array("id"));
		$table->set_no_data_message( Language::get_instance()->translate(472));
		
		$columns = array();
		$column = new Column(Language::get_instance()->translate(66), "name");
		$column->set_attribute("width", "200px");
		$columns[] = $column;
		$columns[] = new Column( Language::get_instance()->translate(438), "username");
		$columns[] = new Column( Language::get_instance()->translate(787), "sex_full");
		$columns[] = new Column( Language::get_instance()->translate(441), "parent");
		$columns[] = new Column( Language::get_instance()->translate(473), "is_admin", "boolean", false);
		$table->set_columns($columns);
		
		$html[] = $table->render_table();
		return implode("\n", $html);
	}
	
	public function get_description_user($user)
	{
		$html = array();
		$html[] = '<div class="record_name"># :</div><div class="record_output">'.$user->get_id().'</div><br class="clearfloat"/>';
		$html[] = '<div class="record_name">Lidbeheerder :</div><div class="record_output">'.$user->get_parent().'</div><br class="clearfloat"/>';
		$html[] = '<div class="record_name">Gebruikersnaam :</div><div class="record_output">'.$user->get_username().'</div><br class="clearfloat"/>';
		$html[] = '<div class="record_name">Naam :</div><div class="record_output">'.$user->get_name().'</div><br class="clearfloat"/>';
		$html[] = '<div class="record_name">Admin :</div><div class="record_output">'.($user->is_admin()?"Waar":"Vals").'</div><br class="clearfloat"/>';
		return implode("\n", $html);
	}
	
	public function get_user_row_header()
	{
		$html = array();
		$html[] = '<th>#</th>';
		$html[] = '<th>Lidbeheerder</th>';
		$html[] = '<th>Gebruikersnaam</th>';
		$html[] = '<th>Naam</th>';
		$html[] = '<th>Admin</th>';
		return implode("\n", $html);
	}

	public function get_user_row_render($user)
	{
		$html = array();
		$html[] = '<td>';
		$html[] = $user->get_id();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $user->get_parent();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $user->get_username();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $user->get_name();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $user->is_admin()?"Waar":"Vals";
		$html[] = '</td>';
		return implode("\n", $html);
	}
}

?>