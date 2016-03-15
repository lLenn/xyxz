<?php

class UserForms
{

	private $manager;
	
	function UserForms($manager)
	{
		$this->manager = $manager;
	}
	
	public function get_transfer_credits_form($transfer = null)
	{
		if($this->manager->get_user()->get_group_id() != GroupManager::GROUP_CLUB_ID)
			$users = array_merge(array($this->manager->get_data_manager()->retrieve_highest_parent($this->manager->get_user())), $this->manager->get_data_manager()->retrieve_siblings_by_user($this->manager->get_user()));
		else
			$users = $this->manager->get_data_manager()->retrieve_children($this->manager->get_user()->get_id(), true);
		$user_id = 0;
		$credits = 0;
		if(!is_null($transfer))
		{
			$user_id = $transfer->user_id;
			$credits = $transfer->credits;
		}
		$html[] = '<p class="title">' . Language::get_instance()->translate(1175) . '</p>';
		$html[] = '<form id="transfer_form" action="" method="post">';
		$html[] = '<div class="record"'>
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(108) . ' :</div><div class="record_input">' . $this->manager->get_renderer()->get_selector($user_id, 'user_id', $users, false, true) . '</div>';
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(793) . ' :</div><div class="record_input"><input type="text" name="credits" value="'.$credits.'"></div>';			
		$html[] = '<div class="record_button"><a id="submit_form" class="link_button" href="javascript:;">' . Language::get_instance()->translate(452) . '</a></div>';
		$html[] = '</div>';
		$html[] = '</form>';
		return implode("\n", $html);
	}

	public static function get_login_form()
	{
		$html = array();
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'pages/user/javascript/user_form.js" ></script>';
		$html[] = '<form id="login_form" action="" method="post">';
		$html[] = '<div class="record">';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(108) . ' :</div><div class="record_input"><input type="text" name="login" style="width:235px;" value="'. Cookie::retrieve(Cookie::LOGIN) .'"/></div>';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(434) . ' :</div><div class="record_input"><input type="password" name="password" style="width:235px;" value="'. Cookie::retrieve(Cookie::PWD) .'"/></div>';
		$html[] = '<br class="clearfloat"/>';
		//<div class="record_button"><a class="link_button" href="'.Url::create_url(array('page' => 'register')).'">Inschrijven</a></div>';
		$html[] = '<div class="record_button_right"><a id="submit_form" class="link_button" href="javascript:;">' . Language::get_instance()->translate(436) . '</a></div>';
		/*
		$str = '<div style="float: right; margin-top: 12px;"><input class="styled" type="checkbox" name="save" value="1" ';
		if(Cookie::is_set(Cookie::LOGIN) && Cookie::is_set(Cookie::PWD)) $str .= "CHECKED";	
		$html[] = $str . '/><div class="option_info">' . Language::get_instance()->translate(435) . '</div></div>';
		*/
		$html[] = '</div>';
		$html[] = '</form>';
		return implode("\n", $html);
	}
	
	public static function get_club_registration_type()
	{
		$html = array();
		$html[] = '<form id="request_form" action="" method="post">';
		$html[] = '<div class="record"></br></br>';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(1232) . ' :</div><div class="record_input"><input type="radio" name="registration_type" value="1"/>' . Language::get_instance()->translate(1233) . '</div>';
		$html[] = '<div class="record_name_required">&nbsp</div><div class="record_input"><input type="radio" name="registration_type" value="2"/>' . Language::get_instance()->translate(1230) . '</div>';
		$html[] = '<div class="record_name_required">&nbsp</div><div class="record_input"><input type="radio" name="registration_type" value="3"/>' . Language::get_instance()->translate(1231) . '</div>';
		//<div class="record_button"><a class="link_button" href="'.Url::create_url(array('page' => 'register')).'">Inschrijven</a></div>';
		$html[] = '<br/><div class="record_button"><a id="submit_form" class="link_button" href="javascript:;">' . Language::get_instance()->translate(541) . '</a></div>';
		$html[] = '<div class="record_button">';
		$html[] = '<a class="link_button" href="' . Url::create_url(array('page'=>'register')) . '">'.Language::get_instance()->translate(1024).'</a>';
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '</form>';
		return implode("\n", $html);
	}
	
	public static function get_request_form()
	{
		$html = array();
		$html[] = '<form id="request_form" action="" method="post">';
		$html[] = '<div class="record">';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(445) . ' :</div><div class="record_input"><input type="text" name="firstname" style="width:235px;"/></div>';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(446) . ' :</div><div class="record_input"><input type="text" name="lastname" style="width:235px;"/></div>';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(439) . ' :</div><div class="record_input"><input type="text" name="email" style="width:235px;"/></div>';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(630) . ' :</div><div class="record_input">' . LanguageRenderer::get_selector(Language::get_instance()->get_language(), "language_req") . '</div>';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(787) . ' :</div><div class="record_input">';
		$html[] = '<select class="input_element" name="sex" style="width: 150px;">';
		$html[] = '<option value="M">' . Language::get_instance()->translate(785) . '</option>';
		$html[] = '<option value="F">' . Language::get_instance()->translate(786) . '</option>';
		$html[] = '</select>';
		$html[] = '</div>';	
		
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(757) . ' :</div><div class="record_input" style="padding-top: 4px"><input class="styled" type="radio" name="type" value="1"/></div><div class="record_input">' . Language::get_instance()->translate(796) . '</div><br class="clearfloat"/>';
		$html[] = '<div class="record_name"></div><div class="record_input"><input class="styled" type="radio" name="type" value="0"/></div><div class="record_input" style="padding-top: 3px">' . Language::get_instance()->translate(797) . '</div><br class="clearfloat"/>';

		$html[] = '<div class="record_name">' . Language::get_instance()->translate(449) . ' :</div><div class="record_input"><textarea name="message" style="width:235px; height: 75px;"></textarea></div>';
		$html[] = '<br class="clearfloat"/>';
		//<div class="record_button"><a class="link_button" href="'.Url::create_url(array('page' => 'register')).'">Inschrijven</a></div>';
		$html[] = '<div class="record_button_right"><a id="submit_request_form" class="link_button" href="javascript:;">' . Language::get_instance()->translate(758) . '</a></div>';
		$html[] = '</div>';
		$html[] = '</form>';
		return implode("\n", $html);
	}
	
	public function get_import_list_form($coach)
	{
		$html = array();
		$html[] = '<p class="title">' . Language::get_instance()->translate($coach?1366:1401) . '</p>';
		$html[] = '<p style="vertical-align:top;font-style:italic;font-size:11px;">';
		$html[] = Language::get_instance()->translate(1367) . '<br/>';
		$html[] = Language::get_instance()->translate(1368) . '<br/>';
		$html[] = Language::get_instance()->translate($coach?1369:1402) . '<br/>';
		$html[] = '</p>';
		$html[] = '<form id="request_form" action="" method="post" enctype="multipart/form-data">';
		$html[] = '<div class="record">';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(1373) . ' :</div><div class="record_input"><input type="text" name="prefix"/></div>';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(1399) . ' </div><div class="record_input"><input type="checkbox" name="emailaslogin" style="float: left; margin-top: 5px"/><div style="float: left; margin-top: 4px;">&nbsp;' . Language::get_instance()->translate(1400) . '</div><br class="clearfloat"/></div>';
		$html[] = '<div class="record_output" style="vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(1374) . ' </div>';
		$html[] = '<br class="clearfloat"/>';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(434) . ' :</div><div class="record_input"><input type="text" name="password"/></div>';
		$html[] = '<div class="record_output" style="vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(1375) . ' </div>';
		$html[] = '<br class="clearfloat"/>';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(1365) . ' :</div><div class="record_input"><input type="file" name="import_user_list"/></div>';
		$html[] = '<input type="hidden" name="uploaded" value="1"/>';
		$html[] = '<br class="clearfloat"/>';
		$html[] = '<div class="record_button"><a id="submit_form" class="link_button" href="javascript:;">' . Language::get_instance()->translate(1370) . '</a></div>';
		$html[] = '</div>';
		$html[] = '</form>';
		return implode("\n", $html);
	}
	
	public function get_form($user=null, $parent_user=null, $test_account = false)
	{
		if(!is_null($user) && !is_object($user))
		{
			$user = null;
		}
		
		if(is_null($parent_user))
		{
			$parent_user = $this->manager->get_user();
		}
		
		$html = array();
		$html[] = '<link rel="stylesheet" href="'.Path::get_url_path().'plugins/jquery.multiselect2side/css/jquery.multiselect2side.css" type="text/css" media="screen" />';
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'plugins/jquery.multiselect2side/js/jquery.multiselect2side.js" ></script>';
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'pages/user/javascript/user_form.js" ></script>';
		$html[] = '<form action="" method="post" id="user_creator_form">';
		$html[] = '<div class="record">';
		$html[] = '<p><h4 class="title">' . Language::get_instance()->translate(437) . '</h4></p>';
		
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(438) . '* :</div>';
		$str = '<div class="record_input">';
		if(is_null($user) || $user->get_id() == 0 || Request::get('page') == 'register')	
		{
			$str .= '<input type="text" name="username" value="' . (!is_null($user)?$user->get_username():"") .'">';
		}
		if(!is_null($user) && $user->get_id() != 0)
		{
			$str = '<div class="record_output">' . $user->get_username();
		}
		$html[] = $str . '</div><br class="clearfloar"/><br class="clearfloat"/>';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(439) . ' :</div><div class="record_input"><input type="text" name="email" value="'.(!is_null($user)?$user->get_email():'').'"></div>';
		if(is_null($user) || $test_account)
		{
			$html[] = '<div class="record_name_required"></div><div class="record_input">' . Language::get_instance()->translate(798) . ' <input type="checkbox" name="send_email"></div>';
		}
	
		if(!is_null($this->manager->get_user()) && $this->manager->get_user()->is_admin())
		{
			$html[] = $this->get_rating_form($user);
		}
		
		if(is_null($user) || $user->get_id() == 0 || $user->get_id() == $this->manager->get_user()->get_id())
		{
			$html[] = $this->get_pwd_form($user);
		}
		
		$group_manager = new GroupManager($this->manager->get_user());
		$groups = $group_manager->get_data_manager()->retrieve_groups_by_right($parent_user->get_group_id());
		$count_groups = count($groups);
		
		$user_manager = new UserManager($this->manager->get_user());
		$parents = array();
		$ext_parents = $parents;
		if(!is_null($this->manager->get_user()) && $this->manager->get_user()->is_admin())
		{
			$parents = $user_manager->get_data_manager()->retrieve_users();
			$ext_parents = $parents;
		}
		elseif(!is_null($this->manager->get_user()) && ((is_null($user) && !is_null($this->manager->get_user())) || $this->manager->get_user()->get_id() != $user->get_id()))
		{
			if(GroupManager::group_is_not_test($parent_user->get_group_id()))
			{
				if($parent_user->get_group_id() != GroupManager::GROUP_CLUB_ID)
				{
					$parents = $user_manager->get_data_manager()->retrieve_siblings_by_user($parent_user);
					$ext_parents = $parents;
				}
				else
				{
					$parents = $user_manager->get_data_manager()->retrieve_siblings_by_user($parent_user);
					$ext_parents = $user_manager->get_data_manager()->retrieve_children($parent_user->get_id(), true);
				}
			}
			else
			{
				$parents = array($parent_user);
				$ext_parents = $parents;
			}
		}
		
		// RIGHTS
		$count_parents = count($parents);
		if($count_groups && $count_parents)
		{
			$html[] = '<p><h4 class="title">' . Language::get_instance()->translate(440) . '</h4></p>';
			if($this->manager->get_user()->is_admin() && (is_null($user) || $test_account))
			{
				$html[] = '<div class="record_name">' . Language::get_instance()->translate(784) . ' :</div>';
				$html[] = '<div class="record_input"><input id="test_account" type="checkbox" name="test_account" '.($test_account?"checked='checked'":"").'></div>';
				$html[] = '<div id="test_account_group_block" ' . ($test_account?'':'style="display: none;"') . '>';
				$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(92) . ' :</div><div class="record_input">'.$group_manager->get_renderer()->get_selector(!is_null($user)?$user->get_group_id():0, 'test_group_id', false, true).'</div>';
				$html[] = '</div>';
				$html[] = '<div id="test_account_block" ' . ($test_account?'style="display: none;"':'') . '>';
			}
			$id = 0;
			if(!is_null($user) && !is_null($user->get_group()))
			{
				$id = $user->get_group_id();
			}
			else
			{
				$id = $groups[0]->get_id();
			}
			if(is_null($user) || (!is_null($user) && !$user->is_admin()))
			{
				if($count_groups != 1)
				{
					$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(92) . ' :</div><div class="record_input">'.$group_manager->get_renderer()->get_selector($id).'</div>';
				}
				else
				{
					$group = $groups[0];
					$html[] = '<input type="hidden" name="group_id" value="'.$group->get_id().'">';
					$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(92) . ' :</div><div class="record_output">'.$group->get_name().'</div><br class="clearfloat">';
				}
			}
			else
			{
				$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(92) . ' :</div><div class="record_output">'.Language::get_instance()->translate(640).'</div><br class="clearfloat">';
			}
			
			$parent_id = 0;
			$extra_parent_ids = array();
			if(!is_null($user))
			{
				$parent_id = $user->get_parent_id();
				$extra_parent_ids = $user->get_extra_parent_ids();
			}
			elseif(!is_null($parent_user))
			{
				$parent_id = $parent_user->get_id();
			}
			$html[] = '<div class="record_name">' . Language::get_instance()->translate(441) . ' :</div><div class="record_input">' . $this->manager->get_renderer()->get_selector($parent_id, 'parent_id', $parents) . '</div>';
			if($parent_user->get_group_id() == GroupManager::GROUP_COACH_ID || $this->manager->get_user()->is_admin())
				$html[] = '<div class="record_name">' . Language::get_instance()->translate(442) . ' :</div><div class="record_input">' . $this->manager->get_renderer()->get_selector($extra_parent_ids, 'extra_parent_ids[]', $ext_parents, true, true) . '</div><br class="clearfloat"/>';
			if($parent_user->get_group_id() == GroupManager::GROUP_CLUB_ID)
			{
				$creatable_coaches = $this->manager->get_data_manager()->retrieve_club_registration_coaches($this->manager->get_user()->get_id(), true);
				$html[] = '<div class="record_name">' . Language::get_instance()->translate(643) . ' :</div><div class="record_input">';
				if(count($creatable_coaches))
				{
					//ERROR BIJ PUPIL AANMAKEN VOOR COACH VIA CLUB
					$selected_coach = 0;
					if(!is_null(Request::post("coach_pupils")))
						$selected_coach = Request::post("coach_pupils");
					$html[] = '<select class="input_element" name="coach_pupils" style="width: 150px;">';
					foreach($creatable_coaches as $coach => $pupils)
					{
						$html[] = '<option value="' . $coach . '"' . ($selected_coach==$coach?" SELECTED":"") . '>' . $pupils . '</option>';
					}
					$html[] = '</select>';
				}
				else
				{
					$pupils = "";
					if(!is_null(Request::post("pupils")))
						$pupils = Request::post("pupils");
					$html[] = '<input type="text" name="pupils" size="3" value="' . $pupils . '"/>';
				}
				$html[] = '</div>';	
			}
			if($this->manager->get_user()->is_admin())
				$html[] = '<div class="record_name">' . Language::get_instance()->translate(793) . ' :</div><div class="record_input"><input type="text" name="credits" value="'.(!is_null($user)?$user->get_credits(true):'').'"></div>';			
			$checked = 0;
			if(!is_null($user) && $user->get_activation_code() == 1)
			{
				$checked = 1;
			}
			$html[] = '<div class="record_name">' . Language::get_instance()->translate(443) . ' :</div><div class="record_input"><input type="checkbox" name="activated" '.($checked?"checked='checked'":"").'></div>';
			if($this->manager->get_user()->is_admin() && (is_null($user) || $test_account))
				$html[] = '</div>';
		}
		
		$html[] = '<p><h4 class="title">' . Language::get_instance()->translate(444) . '</h4></p>';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(445) . ' :</div><div class="record_input"><input type="text" name="firstname" value="'.(!is_null($user)?$user->get_firstname():'').'"></div>';			
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(446) . ' :</div><div class="record_input"><input type="text" name="lastname" value="'.(!is_null($user)?$user->get_lastname():'').'"></div>';		
		//$html[] = '<div class="record_name">' . Language::get_instance()->translate(630) . ' :</div><div class="record_input">' . LanguageRenderer::get_selector((!is_null($user)?$user->get_language():Language::get_instance()->get_language()), 'user_language').'</div>';
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(787) . ' :</div><div class="record_input">';
		$html[] = '<select class="input_element" name="sex" style="width: 150px;">';
		$html[] = '<option value="M">' . Language::get_instance()->translate(785) . '</option>';
		$html[] = '<option value="F"' . (!is_null($user)&&$user->get_sex()=="F"?'selected="selected"':'') . '>' . Language::get_instance()->translate(786) . '</option>';
		$html[] = '</select>';
		$html[] = '</div>';										
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(447) . '** :</div><div class="record_input"><input type="text" name="avatar" value="'.(!is_null($user)?$user->get_avatar():'').'"></div>';									
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(448) . ' :</div><div class="record_input"><textarea style="width:250px;height:50px;" name="address">'.(!is_null($user)?$user->get_address():'').'</textarea></div>';								

		if(is_null($user) && Request::get('page') == 'register') $html[] = '<div class="record_name">' . Language::get_instance()->translate(449) . ' :</div><div class="record_input"><textarea style="width:250px;height:50px;" name="message"></textarea></div>';
			
		$html[] = '<p style="font-style:italic;font-size:11px;">* ' . Language::get_instance()->translate(450) . '<br />** ' . Language::get_instance()->translate(451) . '</p>';
		$submit = 'Opslaan';
		if(Request::get('page') == 'register') $submit = Language::get_instance()->translate(452);
		elseif(!is_null($user)) $submit = Language::get_instance()->translate(56);
		$html[] = '<div class="record_button"><a id="submit_form" class="link_button" href="javascript:;">'.$submit.'</a></div></form>';
		$html[] = '</div>';
		
		return implode("\n", $html);	
	}
	
	public function get_club_selection_form($registration_type)
	{
		$html = array();
		$html[] = '<div style="width: 800px;">';
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'pages/user/javascript/register_club_form.js" ></script>';
		$html[] = '<form id="request_form" action="" method="post">';
		$html[] = '<div class="record">';
		$html[] = '<p><h4 class="medium_title">' . Language::get_instance()->translate(1192) . '</h4></p>';
		$html[] = '<input type="hidden" name="registration_type" value="' . $registration_type . '"/>';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(1191) . ' :</div><div class="record_input"><input type="text" name="city_code" style="width:235px;"/></div>';
		$html[] = '<div id="result_clubs">';
		$html[] = '<div class="record_button">';
		$html[] = '<br/>';
		$html[] = '<a class="link_button" href="' . Url::create_url(array('page'=>'register', 'register_account' => '')) . '">'.Language::get_instance()->translate(1024).'</a>';
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '</form>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
	
	public function get_organisations_form_by_city_code($code, $organisation_type)
	{
		$html = array();
		$organisations = $this->manager->get_data_manager()->retrieve_organisations_by_city_code($code, $organisation_type);
		//$html[] = "<input type='hidden' name='registration_type' value='" . $organisation_type . "'/>";
		//dump($organisations);
		if(!Error::get_instance()->get_result())
		{
			$html[] = '<p><h4 class="medium_title">' . Error::get_instance()->get_message() . '</h4></p>';
		}
		else
		{
			$html[] = '<p><h4 class="medium_title">' . Language::get_instance()->translate(1188) . '</h4></p>';
			$html[] = '<div style="max-height: 300px; overflow-y: auto;">';
			foreach($organisations as $organisation)
			{
				$html[] = '<div style="margin-top: 10px;" >';
					$html[] = '<div style="float: left; width: 20px;">';
					$html[] = '<input type="radio" name="organisation_id" value="'.$organisation->get_id().'"/>';
					$html[] = '</div>';
				
					$html[] = '<div style="float: left">';
						$html[] = '<div class="record_name">' . Language::get_instance()->translate(66) . ' :</div><div class="record_output">' . $organisation->get_name() . '</div><br class="clearfloat"/>';
						$html[] = '<div class="record_name">' . Language::get_instance()->translate(448) . ' :</div>';
						$str = "";
						$address = $organisation->get_address();
						if($address->get_street() != '0' && $address->get_street() != "")
						{
							$str .= $address->get_street() . " " . $address->get_nr() . ($address->get_bus_nr()==0?"":"_".$address->get_bus_nr());
						}
						$city = $address->get_city();
						if($city->get_city_code() != 0)
						{
							$str .= ($str==""?"":"<br/>") . $city->get_city_code() . " " . $city->get_city_name();
						}
						$province = $city->get_province();
						$str .= ($str==""?"":"<br/>") . $province->get_province_name();
						$html[] = '<div class="record_output">' . $str . '</div>';
					$html[] = '</div>';
				$html[] = '</div><br class="clearfloat"/>';
			}
			
			$html[] = '</div><br/><br/>';
		}
		$html[] = '<div style="float: left; width: 20px;">';
		$html[] = '<input type="radio" name="organisation_id" value="0"/>';
		$html[] = '</div>';
		$html[] = '<div style="float: left">';
		$html[] = '<div class="record_name"></div>';
		$html[] = '<div class="record_output">' . Language::get_instance()->translate(1194) . '</div>';
		$html[] = '</div><br class="clearfloat"/>';
		$html[] = '</div><br/><br/>';
		$html[] = '<div class="record_button">';
		$html[] = '<a id="submit_form" class="link_button" href="javascript:;">'.Language::get_instance()->translate(541).'</a>';
		$html[] = '</div>';		
		$html[] = '<div class="record_button">';
		$html[] = '<a class="link_button" href="' . Url::create_url(array('page'=>'register', 'register_account' => '')) . '">'.Language::get_instance()->translate(1024).'</a>';
		$html[] = '</div>';
	
		return implode("\n", $html);
	}
	
	public function get_register_user_form($registration = null, $confirm = false)
	{
		$html = array();
		$html[] = '<div style="width: 800px;">';
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'pages/user/javascript/register_club_form.js" ></script>';
		$html[] = '<form id="request_form" action="" method="post"/>';
		$html[] = '<div class="record">';
		$html[] = '<input type="hidden" name="registration_type" value="1"/>';
		$html[] = $this->get_register_form($registration, $confirm);
		$html[] = '</div>';	
		$html[] = '</br>';
		$html[] = '<div style="width: 500px;">';
		$html[] = '</div>';
		if(!$confirm)
		{
			$html[] = '<input type="hidden" name="confirm_first" value="1"/>';
		}
		else
		{
			$html[] = '<input type="hidden" name="confirmed" id="confirmed" value="0"/>';
		}
		$html[] = '<input type="hidden" name="user_reg_form" value="1"/>';
		if(!$confirm)
		{
			$html[] = '<div class="record_button"><a id="submit_form" class="link_button" href="javascript:;">'.Language::get_instance()->translate(452).'</a></div>';	
			$html[] = '<div class="record_button">';
			$html[] = '<a class="link_button" href="' . Url::create_url(array('page'=>'register', 'register_account' => '')) . '">'.Language::get_instance()->translate(1024).'</a>';
			$html[] = '</div>';
		}
		else
		{
			$html[] = "<br class='clearfloat'/>";
			$html[] = '<div class="record_button">';
			$html[] = '<a id="accept_payment" class="link_button" href="javascript:;">'.Language::get_instance()->translate(1127).'</a>';
			$html[] = '</div>';
			$html[] = '<div class="record_button">';
			$html[] = '<a id="cancel_payment" class="link_button" href="javascript:;">'.Language::get_instance()->translate(1024).'</a>';
			$html[] = '</div>';
			$html[] = '</br></br>';
		}
		$html[] = '</div>';
		$html[] = '</form>';
	    $html[] = '</div>';
		return implode("\n", $html);
	}
	
	public function get_course_request_form($request = null, $confirm = false)
	{
		$html = array();
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'pages/user/javascript/register_club_form.js" ></script>';
		$html[] = '<form id="request_form" action="" method="post"/>';
		
		$html[] = '<input type="hidden" name="request_form" value="1"/>';
		$html[] = $this->get_register_form($request, $confirm, true);

		if(!$confirm)
		{
			$html[] = '<input type="hidden" name="confirm_first" value="1"/>';
		}
		else
		{
			$html[] = '<input type="hidden" name="confirmed" id="confirmed" value="0"/>';
		}

		$html[] = '<br/>';
		if(!$confirm)
		{
			$html[] = '<div class="record_button"><a id="submit_form" class="link_button" href="javascript:;">'.Language::get_instance()->translate(762).'</a></div>';
			$html[] = '<div class="record_button">';
			$html[] = '<a class="link_button" href="' . Url::create_url(array('page'=>'register')) . '">'.Language::get_instance()->translate(1024).'</a>';
			$html[] = '</div>';
		}
		else
		{
			$html[] = '<div class="record_button">';
			$html[] = '<a id="accept_payment" class="link_button" href="javascript:;">'.Language::get_instance()->translate(999).'</a>';
			$html[] = '</div>';
			$html[] = '<div class="record_button">';
			$html[] = '<a id="cancel_payment" class="link_button" href="javascript:;">'.Language::get_instance()->translate(1024).'</a>';
			$html[] = '</div>';
		}
		
		$html[] = '</form>';
		return implode("\n", $html);
	}
	
	public function get_upgrade_club_form($upgrade = null, $confirm = false)
	{
		$html = array();
		$html[] = '<div style="width: 800px;">';
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'pages/user/javascript/upgrade_club_form.js" ></script>';
		$html[] = '<form id="upgrade_form" action="" method="post"/>';
		
		$html[] = '<div style="width: 500px;">';
		$html[] = '</div>';
				
		$html[] = '<h4 class="medium_title">' . Language::get_instance()->translate(1137) . '</h4>';
		
		if(!$confirm)
		{
			$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(1202) . ' :</div>';
			$html[] = '<div class="record_input" style="padding-left: 200px">';
			if($this->manager->get_user()->get_group_id() != GroupManager::GROUP_FREE_INDIVIDUAL_ID)
			{
				$html[] = '<input type="radio" name="price_arrangement" value="1" ' . (!is_null($upgrade) && $upgrade->get_infinite()==1?'CHECKED':'') . '/>' . Language::get_instance()->translate(1203) . ' ' . Language::get_instance()->translate(1223) . ' ' . date("d/m/Y", RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "date_max_price_1")) . ': <span style="color: green">' . (RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "max_price_1") + 50) . '&euro;</span>  <span style="color: red;">- 50 &euro; steun door Vlaamse schaakfederatie</span>' . '<br/>';
				$max_price_2 = RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "max_price_2");
				if(!is_null($max_price_2) || $max_price_2 != "" || $max_price_2 != 0)
				{
					$html[] = '<input type="radio" name="price_arrangement" value="2" ' . (!is_null($upgrade) && $upgrade->get_infinite()==2?'CHECKED':'') . '/>' . Language::get_instance()->translate(1203) . ' ' . Language::get_instance()->translate(1223) . ' ' . date("d/m/Y", RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "date_max_price_2")) . ': <span style="color: green">' . ($max_price_2 + 50) . '&euro;</span>  <span style="color: red;">- 50 &euro; steun door Vlaamse schaakfederatie</span>' . '<br/>';
				}
			}
			else
				$html[] = '<input type="radio" name="price_arrangement" value="3" ' . (!is_null($upgrade) && $upgrade->get_infinite()==3?'CHECKED':'') . '/>' . Language::get_instance()->translate(1233) . ' ' . Language::get_instance()->translate(1223) . ' ' . date("d/m/Y", RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "date_price_users")) . ': <span style="color: green">' . (RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "price_individual")) . '&euro;</span><br/>';
			$html[] = '</div>';
		}
		else
		{
			$html[] = '<input type="hidden" name="price_arrangement" value="' . $upgrade->get_infinite() . '"/>';
			$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(1202) . ' :</div>';
			if($upgrade->get_infinite()==3)
				$html[] = '<div class="record_output">' . Language::get_instance()->translate(1233) . ' ' . Language::get_instance()->translate(1223) . ' ' . date("d/m/Y", RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "date_price_users")) . '</div><br class="clearfloat"/>';
			else
				$html[] = '<div class="record_output">' . Language::get_instance()->translate(1203) . ' ' . Language::get_instance()->translate(1223) . ' ' . date("d/m/Y", RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "date_max_price_" . $upgrade->get_infinite())) . '</div><br class="clearfloat"/>';
		}
		
		$html[] = '<br/>';
		if(!$confirm)
		{
			$html[] = '<input type="hidden" name="confirm_first" value="1"/>';
		}
		else
		{
			$html[] = '<input type="hidden" name="confirmed" id="confirmed" value="0"/>';
		}
		
		$html[] = '<input type="hidden" name="user_reg_form" value="1"/>';
		if(!$confirm)
		{
			$html[] = '<div class="record_button"><a id="submit_form" class="link_button" href="javascript:;">'.Language::get_instance()->translate(452).'</a></div>';	
			$html[] = '<div class="record_button">';
			$html[] = '<a class="link_button" href="' . Url::create_url(array('page'=>'upgrade')) . '">'.Language::get_instance()->translate(1024).'</a>';
			$html[] = '</div>';
		}
		else
		{
			$html[] = "<div style='float: right; width: 100px; text-align: right; padding-right: 325px; padding-left: 7px;'>";
			$html[] = "<div class='horizontal_ruler'></div>";
			$html[] = "= " . $upgrade->get_price() . "&euro;";
			$html[] = "</div>";
			
			$html[] = "<br class='clearfloat'/>";
			$html[] = '<div class="record_button">';
			$html[] = '<a id="accept_payment" class="link_button" href="javascript:;">'.Language::get_instance()->translate(1127).'</a>';
			$html[] = '</div>';
			$html[] = '<div class="record_button">';
			$html[] = '<a id="cancel_payment" class="link_button" href="javascript:;">'.Language::get_instance()->translate(1024).'</a>';
			$html[] = '</div>';
			$html[] = '</br></br>';
		}
		$html[] = '</div>';
		$html[] = '</form>';
	    $html[] = '</div>';
		return implode("\n", $html);
	}
		
	public function get_register_club_form($registration = null, $confirm = false, $organisation = null, $show_organisation_form = true, $registration_type = 1)
	{
		$html = array();
		$html[] = '<div style="width: 800px;">';
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'pages/user/javascript/register_club_form.js" ></script>';
		$html[] = '<form id="request_form" action="" method="post"/>';
		$html[] = '<div class="record">';
		$html[] = '<input type="hidden" name="registration_type" value="' . $registration_type . '"/>';
		$html[] = '<input type="hidden" name="organisation_id" value="' . (is_null($organisation)?0:$organisation->get_id()) . '"/>';
		$html[] = '<p><h4 class="medium_title">' . Language::get_instance()->translate(1189) . '</h4></p>';
		//dump($organisation);
		if(is_null($organisation) || $show_organisation_form)
		{
			$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(66) . ' :</div><div class="record_input"><input type="text" name="organisation_name" style="width:235px;" ' . (is_null($organisation)?'':'value="' . $organisation->get_name() . '"') . '/></div>';
			$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(997) . ' :</div><div class="record_input"><input type="text" name="organisation_email" style="width:235px;" ' . (is_null($organisation)?'':'value="' . $organisation->get_email(). '"') . '/></div>';
			$address = null;
			if(!is_null($organisation))
				$address = $organisation->get_address();
			$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(448) . ' : </div><div class="record_input" style="padding-left: 200px; line-height: 23px;">'.Language::get_instance()->translate(1196).': <input type="text" name="organisation_street" style="width:235px;" ' . (is_null($organisation)?'':'value="' . $address->get_street() . '"') . '/>&nbsp';
			$html[] = Language::get_instance()->translate(68).': <input type="text" name="organisation_nr" style="width:20px;" ' . (is_null($organisation)?'':'value="' . $address->get_nr() . '"') . '/>&nbsp';
			$html[] = Language::get_instance()->translate(1197). ': <input type="text" name="organisation_bus_nr" style="width:20px;" ' . (is_null($organisation)?'':'value="' . $address->get_bus_nr() . '"') . '/><br/>';
			$city = null;
			if(!is_null($address))
				$city = $address->get_city();
			if(!is_null($city))
			{
				$html[] = Language::get_instance()->translate(1191).': <input type="text" name="organisation_city_code" style="width:70px;" ' . (is_null($organisation)?'':'value="' . $city->get_city_code() . '"') . '/>&nbsp';
				$html[] = Language::get_instance()->translate(1198).': <input type="text" name="organisation_city_name" style="width:150px;" ' . (is_null($organisation)?'':'value="' . $city->get_city_name() . '"') . '/></div>';
			}
		}
		else
		{
			if($confirm && $organisation->get_id() == 0)
			{
				$html[] = '<input type="hidden" name="organisation_name" value="' . $organisation->get_name() . '"/>';
				$html[] = '<input type="hidden" name="organisation_email" value="' . $organisation->get_email(). '"/>';
				$address = null;
				if(!is_null($organisation))
					$address = $organisation->get_address();
				$html[] = '<input type="hidden" name="organisation_street" value="' . $address->get_street() . '"/>';
				$html[] = '<input type="hidden" name="organisation_nr" value="' . $address->get_nr() . '"/>';
				$html[] = '<input type="hidden" name="organisation_bus_nr" value="' . $address->get_bus_nr() . '"/>';
				$city = null;
				if(!is_null($address))
					$city = $address->get_city();
				$html[] = '<input type="hidden" name="organisation_city_code" value="' . $city->get_city_code() . '"/>';
				$html[] = '<input type="hidden" name="organisation_city_name" value="' . $city->get_city_name() . '"/>';
			}
			$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(66) . ' :</div><div class="record_output">' . $organisation->get_name() . '</div><br class="clearfloat"/>';
			$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(448) . ' :</div>';
			$str = "";
			$address = $organisation->get_address();
			if($address->get_street() != '0' && $address->get_street() != "")
			{
				$str .= $address->get_street() . " " . $address->get_nr() . ($address->get_bus_nr()==0?"":"_".$address->get_bus_nr());
			}
			$city = $address->get_city();
			if($city->get_city_code() != 0)
			{
				$str .= ($str==""?"":"<br/>") . $city->get_city_code() . " " . $city->get_city_name();
			}
			$province = $city->get_province();
			$str .= ($str==""?"":"<br/>") . $province->get_province_name();
			$html[] = '<div class="record_output">' . $str . '</div><br class="clearfloat"/>';
		}
			
			
		$html[] = $this->get_register_form($registration, $confirm);
		$html[] = '</div>';	
		$html[] = '</br>';
		/*
		if($confirm && !is_null($registration) && !$registration->get_infinite())
		{
			$html[] = "<div style='float: right; width: 40px; text-align: right; padding-right: 20px; margin-left: 7px;'>";
			$html[] = GroupDataManager::retrieve_price(GroupManager::GROUP_CLUB_ID) . "&euro;";
			$html[] = "</div>";
			$html[] = "<br class='clearfloat'/>";
		}
		*/
		$html[] = '<div style="width: 500px;">';
		$html[] = '</div>';
				
		//$html[] = '<h4 class="medium_title">' . Language::get_instance()->translate(1137) . '</h4>';
		/*
		get_data_manager()->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "price_coach") . '"/>&euro;<br/>';
			$html[] = Language::get_instance()->translate(1137) . " " . Language::get_instance()->translate(643) . ': <input type="text" name="price_pupil" style="width: 3px; height: 14px; font-size: 11px" value="' . $this->manager->get_data_manager()->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "price_pupil") . '"/>&euro;<br/>';
			$html[] = Language::get_instance()->translate(1201) . ': <input type="text" name="max_price" style="width: 30px; height: 14px; font-size: 11px" value="' . $*/
		/*
		if(!$confirm)
		{
			$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(1202) . ' :</div>';
			$html[] = '<div class="record_input" style="padding-left: 200px"><input type="radio" name="price_arrangement" value="1" ' . (!is_null($registration) && $registration->get_infinite()==1?'CHECKED':'') . '/>' . Language::get_instance()->translate(1203) . ' ' . Language::get_instance()->translate(1223) . ' ' . date("d/m/Y", RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "date_max_price_1")) . ' (' . (RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "max_price_1") + 50) . '&euro; ' <span style="color: red;">- 50 &euro; steun door Vlaamse schaakfederatie</span>' . ')<br/>';
			$max_price_2 = RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "max_price_2");
			if(!is_null($max_price_2) || $max_price_2 != "" || $max_price_2 != 0)
			{
				$html[] = '<input type="radio" name="price_arrangement" value="2" ' . (!is_null($registration) && $registration->get_infinite()==2?'CHECKED':'') . '/>' . Language::get_instance()->translate(1203) . ' ' . Language::get_instance()->translate(1223) . ' ' . date("d/m/Y", RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "date_max_price_2")) . ' (' . ($max_price_2 + 50) . '&euro; ' <span style="color: red;">- 50 &euro; steun door Vlaamse schaakfederatie</span>' . ')<br/>';
			}
			$html[] = '<input type="radio" name="price_arrangement" value="0" ' . (!is_null($registration) && $registration->get_infinite()==0?'CHECKED':'') . '/>' . Language::get_instance()->translate(1204) . ' ' . Language::get_instance()->translate(1223) . ' ' . date("d/m/Y", RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "date_price_users")) . ' (' . sprintf(Language::get_instance()->translate(1205), RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "price_coach")) . ')<br class="clearfloat"/>';
			$html[] = '</div>';
		}
		else
		{
			$html[] = '<input type="hidden" name="price_arrangement" value="' . $registration->get_infinite() . '"/>';
			$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(1202) . ' :</div>';
			$html[] = '<div class="record_output">' . ($registration->get_infinite()?Language::get_instance()->translate(1203) . ' ' . Language::get_instance()->translate(1223) . ' ' . date("d/m/Y", RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "date_max_price_" . $registration->get_infinite())) : Language::get_instance()->translate(1204) . ' ' . Language::get_instance()->translate(1223) . ' ' . date("d/m/Y", RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "date_price_users"))) . '</div><br class="clearfloat"/>';
		}
		
		
		$html[] = '<div id="coaches_div" ' . (is_null($registration)||$registration->get_infinite()?'style="display: none;"':'') . '>';
		if(!$confirm)
			$html[] = '<div style="margin-top: 13px; float: right;"><img id="add_coach" src="' . Path::get_url_path() . 'layout/images/buttons/add.png" style="border: none" title="' . Language::get_instance()->translate(466) . '"/>&nbsp<img id="remove_coach" src="' . Path::get_url_path() . 'layout/images/buttons/remove.png" style="border: none" title="' . Language::get_instance()->translate(1122) . '"/></div>';
		$html[] = '<h4 class="medium_title">' . Language::get_instance()->translate(1120) . '</h4>';
		
		if(is_null($registration) || is_null($registration->get_coaches()) || count($registration->get_coaches())==0)
		{
			$html[] = '<div id="coach_div_1" class="coach_div">';
			$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(796) . ' 1:</div><br class="clearfloat"/>';
			$html[] = '<div class="record_name_required" style="width: 200px">' . Language::get_instance()->translate(1121) . ' :</div><div class="record_input"><input type="text" name="coach1_pupils" size="2"/></div>';
			$html[] = '</div>';
			$html[] = '<div id="coach_div_2" class="coach_div">';
			$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(796) . ' 2:</div><br class="clearfloat"/>';
			$html[] = '<div class="record_name_required" style="width: 200px">' . Language::get_instance()->translate(1121) . ' :</div><div class="record_input"><input type="text" name="coach2_pupils" size="2"/></div>';
			$html[] = '</div>';
		}
		else
		{	
			foreach($registration->get_coaches() as $index => $pupils)
			{
				if(!$confirm)
				{
					$html[] = '<div id="coach_div_' . $index . '" class="coach_div">';
					$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(796) . ' ' . $index . ':</div><br class="clearfloat"/>';
					$html[] = '<div class="record_name_required" style="width: 200px">' . Language::get_instance()->translate(1121) . ' :</div><div class="record_input"><input type="text" name="coach' . $index . '_pupils" size="2" value="' . $pupils . '"/></div>';
					$html[] = '</div>';
				}
				else
				{
					$html[] = "<div style='float: right; width: 100px; text-align: right; padding-right: 20px; margin-left: 7px; margin-top: 20px;'>";
					$html[] = "+ " . GroupDataManager::retrieve_price(GroupManager::GROUP_COACH_ID) . "&euro;</br>";
					$html[] = "+ " . GroupDataManager::retrieve_price(GroupManager::GROUP_PUPIL_ID) . " x " .  $pupils . " = " . GroupDataManager::retrieve_price(GroupManager::GROUP_PUPIL_ID) * $pupils . "&euro;";
					$html[] = "</div>";
					$html[] = '<div id="coach_div_' . $index . '" class="coach_div" style="float: left;">';
					$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(796) . ' ' . $index . ':</div><br class="clearfloat"/>';
					$html[] = '<div class="record_name_required" style="width: 200px">' . Language::get_instance()->translate(1121) . ' :</div><div class="record_output"><input type="hidden" name="coach' . $index . '_pupils" value="' . $pupils . '"/>'. $pupils . '</div><br class="clearfloat"/>';
					$html[] = '</div>';
					$html[] = '<br class="clearfloat"/>';
				}
			}
		}
		$html[] = '</div><br/>';
		*/
		//That damn file uplaod is rigth here!
		/*
		$html[] = '<h4 class="medium_title">' . Language::get_instance()->translate(1208) . '</h4>';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(1206) . ($confirm?'':'*') . ' :</div><div class="record_input">';
		if(!$confirm)
		{
			$html[] = '<input name="club_file" type="file" style="border: none; width: 300px;"/> (xls, ods ' . Language::get_instance()->translate(1213) . ' pdf)';
		}
		else
		{
			$html[] = '<input type="hidden" name="club_file_name" value="' . $registration->get_club_file_name() . '"/>';
			$html[] = '<input type="hidden" name="club_file" value="' . $registration->get_club_file() . '"/>';
			$html[] = $registration->get_club_file_name();
		}
		$html[] = '</div>';
		$html[] = '<br/>';
		*/
		if(!$confirm)
		{
			$html[] = '<input type="hidden" name="confirm_first" value="1"/>';
		}
		else
		{
			$html[] = '<input type="hidden" name="confirmed" id="confirmed" value="0"/>';
		}
		$html[] = '<input type="hidden" name="user_reg_form" value="1"/>';
		if(!$confirm)
		{
			/*$html[] = 'div class="record_name_required">'*/
			//$html[] = '<p style="vertical-align:top;font-style:italic;font-size:11px;">*' . Language::get_instance()->translate(1207) . '</p><br class="clearfloat"/>';
			$html[] = '<div class="record_button"><a id="submit_form" class="link_button" href="javascript:;">'.Language::get_instance()->translate(452).'</a></div>';	
			$html[] = '<div class="record_button">';
			$html[] = '<a class="link_button" href="' . Url::create_url(array('page'=>'register', 'register_account' => '')) . '">'.Language::get_instance()->translate(1024).'</a>';
			$html[] = '</div>';
		}
		else
		{
			/*
			$html[] = "<div style='float: right; width: 100px; text-align: right; padding-right: 20px; padding-left: 7px;'>";
			$html[] = "<div class='horizontal_ruler'></div>";
			$html[] = "= " . $registration->get_price() . "&euro;";
			$html[] = "</div>";
			*/
			$html[] = "<br class='clearfloat'/>";
			$html[] = '<div class="record_button">';
			$html[] = '<a id="accept_payment" class="link_button" href="javascript:;">'.Language::get_instance()->translate(1127).'</a>';
			$html[] = '</div>';
			$html[] = '<div class="record_button">';
			$html[] = '<a id="cancel_payment" class="link_button" href="javascript:;">'.Language::get_instance()->translate(1024).'</a>';
			$html[] = '</div>';
			$html[] = '</br></br>';
		}
		$html[] = '</div>';
		$html[] = '</form>';
		$html[] = '<script type="text/javascript">';
	    $html[] = '  var coach_text = "' . Language::get_instance()->translate(796) . '";';
	    $html[] = '  var pupil_text = "' . Language::get_instance()->translate(1121) . '";';
	    $html[] = '</script>';
	    $html[] = '</div>';
		return implode("\n", $html);
	}
	
	public function get_register_form($registration, $confirm, $mail = false)
	{
		$html = array();
		$html[] = '<p><h4 class="medium_title">' . Language::get_instance()->translate(1119) . '</h4></p>';
		if(!$confirm || is_null($registration))
		{
			if(!$mail)
			{
				$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(438) . ' :</div><div class="record_input"><input type="text" name="username" style="width:235px;" ' . (is_null($registration)?'':'value="' . $registration->get_username() . '"') . '/></div>';
				$html[] = $this->get_pwd_form(null, false, false);
			}
			$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(445) . ' :</div><div class="record_input"><input type="text" name="firstname" style="width:235px;" ' . (is_null($registration)?'':'value="' . $registration->get_firstname() . '"') . '/></div>';
			$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(446) . ' :</div><div class="record_input"><input type="text" name="lastname" style="width:235px;" ' . (is_null($registration)?'':'value="' . $registration->get_lastname(). '"') . '/></div>';
			$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(439) . ' :</div><div class="record_input"><input type="text" name="email" style="width:235px;" ' . (is_null($registration)?'':'value="' . $registration->get_email() . '"') . '/></div>';
			$html[] = (!$mail?'<div class="record_name_required">' . Language::get_instance()->translate(274) . ' :</div><div class="record_input"><input type="text" name="rating" ' . (is_null($registration)?'':'value="' . $registration->get_rating() . '"') . ' size="4"/></div>':'');
			$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(630) . ' :</div><div class="record_input">' . LanguageRenderer::get_selector((is_null($registration)?Language::get_instance()->get_language():$registration->get_language()), "language_req") . '</div>';
			if(!$mail)
			{
				$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(787) . ' :</div><div class="record_input">';
				$html[] = '<select class="input_element" name="sex" style="width: 150px;">';
				$html[] = '<option value="M" ' . (!is_null($registration) && $registration->get_sex()=="M"?'SELECTED':'') . '>' . Language::get_instance()->translate(785) . '</option>';
				$html[] = '<option value="F" ' . (!is_null($registration) && $registration->get_sex()=="F"?'SELECTED':'') . '>' . Language::get_instance()->translate(786) . '</option>';
				$html[] = '</select>';
			}
		}
		else
		{
			if(!$mail)
			{
				$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(438) . ' :</div><div class="record_output"><input type="hidden" name="username" value="' . $registration->get_username() . '"/>' . $registration->get_username() . '</div><br class="clearfloat"/>';
				$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(434) . ' :</div><div class="record_output"><input type="hidden" name="pwd" value="' . Request::post('pwd'). '"/><input type="hidden" name="rep_pwd" value="' . Request::post('pwd'). '"/>***</div><br class="clearfloat"/>';
			}
			$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(445) . ' :</div><div class="record_output"><input type="hidden" name="firstname" value="' . $registration->get_firstname() . '"/>' . $registration->get_firstname() . '</div><br class="clearfloat"/>';
			$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(446) . ' :</div><div class="record_output"><input type="hidden" name="lastname" value="' . $registration->get_lastname(). '"/>' . $registration->get_lastname(). '</div><br class="clearfloat"/>';
			$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(439) . ' :</div><div class="record_input"><input type="hidden" name="email" value="' . $registration->get_email() . '"/>' . $registration->get_email() . '</div><br class="clearfloat"/>';
			$html[] = (!$mail?'<div class="record_name_required">' . Language::get_instance()->translate(274) . ' :</div><div class="record_input"><input type="hidden" name="rating" value="' . $registration->get_rating() . '"/>' . $registration->get_rating() . '</div><br class="clearfloat"/>':'');
			$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(630) . ' :</div><div class="record_input"><input type="hidden" name="language_req" value="' . $registration->get_language() . '"/>' . $registration->get_language() . '</div><br class="clearfloat"/>';
			$html[] = (!$mail?'<div class="record_name_required">' . Language::get_instance()->translate(787) . ' :</div><div class="record_input"><input type="hidden" name="sex" value="' . $registration->get_sex() . '"/>' . $registration->get_sex() . '</div><br class="clearfloat"/>':'');
		}
		return implode("\n", $html);
	}
	
	public function get_pwd_form($user, $own_form=false, $title = true)
	{
		$html = array();
		if($own_form)
		{
			$html[] = '<form action="" method="post">';
		}
		
		if($title)
			$html[] = '<p><h4 class="title">' . Language::get_instance()->translate(434) . '</h4></p>';
		
		if(!is_null($user) && $user->get_id() != 0 && Request::get('page')!='register')
		{
			$html[] = '<p style="height:25px;vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(453) . '</p>';			
			$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(454) . ' :</div><div class="record_input"><input type="password" name="old_pwd"></div>';
		}
		$str = '<div class="record_name_required">';
		if(!is_null($user))
		{
			$str .= Language::get_instance()->translate(455);
		}
		else
		{
			$str .= Language::get_instance()->translate(434);
		}
		$html[] = $str . ' :</div><div class="record_input"><input id="pwd" type="password" name="pwd" onBlur="checkpwd();"></div>';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(456) . ' :</div><div class="record_input"><input id="rep_pwd" type="password" name="rep_pwd" onBlur="checkpwd();"><div style="float:left; margin:-24px 160px;" id="res_png">&nbsp;</div></div>';			
		if($own_form)
		{
			$html[] = "</form>";
		}
		
		return implode("\n", $html);
	}
	
	public function get_rating_form($user, $own_form = false)
	{
		$html = array();
		
		if($own_form)
		{
			$str .= '<form action="" method="post">';
		}
		
		$html[] = '<p><h4 class="title">' . Language::get_instance()->translate(457) . '</h4></p>';
		
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(274) . ' :</div><div class="record_input"><input type="text" name="rating" size="4" value="' .(!is_null($user)&&!is_null($user->get_chess_profile())?$user->get_chess_profile()->get_rating():'') . '"></div>';			
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(458) . ' :</div><div class="record_input"><input type="text" name="rd" size="2" value="' .(!is_null($user)&&!is_null($user->get_chess_profile())?$user->get_chess_profile()->get_rd():'') . '"></div>';			
		if($own_form)
		{
			$html[] = "</form>";
		}
		
		return implode("\n", $html);
	}
}


?>
