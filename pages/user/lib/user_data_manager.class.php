<?php

require_once Path::get_path() . 'pages/user/lib/user_abstract.class.php';
require_once Path::get_path() . 'pages/user/lib/user.class.php';
require_once Path::get_path() . 'pages/user/lib/user_chess_profile.class.php';
require_once Path::get_path() . 'pages/user/lib/user_request.class.php';
require_once Path::get_path() . 'pages/user/lib/club_registration.class.php';
require_once Path::get_path() . 'pages/user/lib/upgrade.class.php';
require_once Path::get_path() . 'pages/user/lib/organisation.class.php';
require_once Path::get_path() . 'pages/user/lib/address.class.php';
require_once Path::get_path() . 'pages/user/lib/province.class.php';
require_once Path::get_path() . 'pages/user/lib/city.class.php';

class UserDataManager extends DataManager
{
	const TABLE_NAME = 'user';
	const TABLE_PARENTS_NAME = 'user_extra_parents';
	const LESSON_TABLE_NAME = 'lesson_relation';
	const LESSON_EXCERCISE_TABLE_NAME = 'lesson_excercise_relation';
	const CLASS_NAME = 'User';
	const REQ_TABLE_NAME = 'user_request';
	const REQ_CLASS_NAME = 'UserRequest';
	const REQC_TABLE_NAME = 'user_request_course';
	
	public static function instance($manager)
	{
		parent::$_instance = new UserDataManager($manager);
		return parent::$_instance;
	}

	public function update_user($user)
	{
		$conditions = 'id="'.$user->get_id().'"';
		$result =  parent::update(self::TABLE_NAME, $user, $conditions);
		if($result && !is_null($user->get_chess_profile()))
		{
			$this->update_user_chess_profile($user->get_chess_profile());
			parent :: delete(self::TABLE_PARENTS_NAME, "user_id = " .$user->get_id());
			foreach($user->get_extra_parent_ids() as $id)
			{
				$properties = new CustomProperties();
				$properties->add_property("user_id", $user->get_id());
				$properties->add_property("parent_id", $id);
				parent :: insert(self :: TABLE_PARENTS_NAME, $properties);		
			}
		}
		return $result;
	}
	
	public function update_user_credits($credits, $user_id)
	{
		$custom = new CustomProperties();
		$custom->add_property("credits", $credits);
		parent::update(self::TABLE_NAME, $custom, "id = " . $user_id);
	}
	
	public function add_user_rights($user)
	{
		RightManager::instance()->delete_location_object_user_rights("User", $user->get_id());
		$parent_user = $this->manager->get_data_manager()->retrieve_user($user->get_parent_id());
		while(!is_null($parent_user))
		{
			RightManager::instance()->add_location_object_user_right("User", $parent_user->get_id(), $user->get_id(), RightManager::UPDATE_RIGHT);
			$parent_user = $this->manager->get_data_manager()->retrieve_user($parent_user->get_parent_id());
		}
		RightManager::instance()->add_location_object_user_right("User", $user->get_id(), $user->get_id(), RightManager::UPDATE_RIGHT);
		$children = $this->retrieve_users_by_parent_id($user->get_id());
		foreach($children as $child)
		{
			$this->add_user_rights($child);
		}
	}
	
	public function delete_user($id)
	{
		$children = $this->retrieve_users_by_parent_id($id);
		$success = false;
		if(count($children)<=0)
		{
			$user = parent::retrieve(self::TABLE_NAME, self::CLASS_NAME, '', self::ONE_RECORD, 'id = ' . $id);
			$success = parent::delete_by_id(self :: TABLE_NAME, $id);
			if($success)
			{
				if($user->get_group_id() == GroupManager::GROUP_COACH_ID)
				{
					$allowed_objects = RightManager::instance()->get_allowed_objects(RightManager::USER_LOCATION_ID, $user);
					$credits = RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "credits_coach") + ($allowed_objects * RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "credits_pupil"));
					$this->update_user_credits($credits, $user->get_parent_id());
				}
				RightManager::instance()->delete_location_user_right("User", $id);
				RightManager::instance()->delete_location_object_user_rights("User", $id);
				$this->delete_user_chess_profile($id);
				parent::delete(self::LESSON_TABLE_NAME, "pupil_id = " . $id);
				parent::delete(self::LESSON_EXCERCISE_TABLE_NAME, "pupil_id = " . $id);
				/*
				$group_manager = new GroupManager($this->manager->get_user());
				$group_manager->get_data_manager()->delete_group_user_relations_by_user_id($id);
				*/
			}
		}
		else
		{
			$success = false;
			Error::get_instance()->set_result(false);
		}
		return $success;
	}

	public function insert_user($user)
	{
		$id = parent :: insert(self :: TABLE_NAME, $user);
		if($id)
		{
			if(!is_null($user->get_chess_profile()))
			{
				$user->get_chess_profile()->set_user_id($id);
				$this->insert_user_chess_profile($user->get_chess_profile());
			}

			foreach($user->get_extra_parent_ids() as $id_p)
			{
				$properties = new CustomProperties();
				$properties->add_property("user_id", $id);
				$properties->add_property("parent_id", $id_p);
				parent :: insert(self :: TABLE_PARENTS_NAME, $properties);
			}
		}
		return $id;
	}

	public function retrieve_user($id)
	{
		return parent::retrieve_by_id(self::TABLE_NAME, self::CLASS_NAME, $id);
	}
	
	public function retrieve_user_by_right($id, $right = RightManager::NO_RIGHT)
	{
		if($this->manager->get_user()->is_admin())
		{
			return parent::retrieve_by_id(self::TABLE_NAME, self::CLASS_NAME, $id);
		}
		else
		{
			$join = array();
			$join[] = new Join(self::TABLE_NAME, "u", "id", Join::MAIN_TABLE);
			$join[] = new Join(RightDataManager::LOC_OBJ_USER_TABLE_NAME, "urt", "object_id", "LEFT JOIN", Join::MAIN_TABLE, "id");
			$conditions = "id = " . $id . " AND location_id = " . RightManager::USER_LOCATION_ID . " AND urt.user_id = " . $this->manager->get_user()->get_id() . " AND urt." . RightDataManager::get_right_conditions($right);
			return parent::retrieve($join,self::CLASS_NAME, 'id', self::ONE_RECORD, $conditions);
		}
	}
	
	public function retrieve_users($right = RightManager::NO_RIGHT)
	{
		if(!$this->manager->get_user()->is_admin())
		{
			$join = array();
			$join[] = new Join(self::TABLE_NAME, "u", "id", Join::MAIN_TABLE);
			$join[] = new Join(RightDataManager::LOC_OBJ_USER_TABLE_NAME, "urt", "object_id", "LEFT JOIN", Join::MAIN_TABLE, "id");
			$conditions = "location_id = " . RightManager::USER_LOCATION_ID . " AND urt.user_id = " . $this->manager->get_user()->get_id() . " AND urt." . RightDataManager::get_right_conditions($right);
			return parent::retrieve($join,self::CLASS_NAME, 'id', self::MANY_RECORDS, $conditions);
		}
		else
		{
			return parent::retrieve(self::TABLE_NAME, self::CLASS_NAME, 'id');
		}
	}
	
	public function retrieve_users_by_group_id($group_id)
	{
		$condition = "group_id = '" . $group_id . "'";
		return parent::retrieve(self::TABLE_NAME,self::CLASS_NAME,'',self::MANY_RECORDS,$condition);
	}
	
	public function retrieve_users_by_parent_id($parent_id)
	{
		$condition = "parent_id = '".$parent_id."' AND id <> '".$parent_id."'";
		return parent::retrieve(self::TABLE_NAME,self::CLASS_NAME,'',self::MANY_RECORDS,$condition);
	}
	
	public function retrieve_other_users_by_parent_id($parent_id)
	{
		$join = array();
		$join[] = new Join(self::TABLE_NAME, "u", "id", Join::MAIN_TABLE);
		$join[] = new Join(self::TABLE_PARENTS_NAME, "ur", "user_id", "LEFT JOIN", Join::MAIN_TABLE, "id");
		$condition = "ur.parent_id = '".$parent_id."' AND id <> '".$parent_id."'";
		return parent::retrieve($join,self::CLASS_NAME,'',self::MANY_RECORDS,$condition);
	}
	
	public function retrieve_children($user_id, $first_level = false)
	{
		$all_children = array();
		$current_children = $this->retrieve_users_by_parent_id($user_id);
		if(!$first_level)
		{
			foreach($current_children as $child)
			{
				$all_children = array_merge($all_children, $this->retrieve_children($child->get_id()));
			}
		}
		$all_children = array_merge($all_children, $current_children);
		return $all_children;
	}
	
	public function retrieve_siblings_by_user($user)
	{
		if(!is_null($user->get_parent_id()) && $user->get_parent_id() != 0)
		{
			return $this->retrieve_users_by_parent_id($user->get_parent_id());
		}
		else
		{
			return array($user);
		}
	}
	
	public function retrieve_highest_parent($user)
	{
		if(!is_null($user->get_parent_id()) && $user->get_parent_id() != 0)
		{
			return $this->retrieve_highest_parent($this->retrieve_user($user->get_parent_id()));
		}
		else
		{
			return $user;
		}
	}
	
	public function retrieve_active_user_by_username($username)
	{
		$condition = 'username = \''.$username.'\' AND activation_code = 1';
		return parent::retrieve(self::TABLE_NAME,self::CLASS_NAME,'',self::ONE_RECORD,$condition);
	}
	
	public function username_exists($username)
	{
		$condition = 'username = \''.$username.'\'';
		$user = parent::retrieve(self::TABLE_NAME,self::CLASS_NAME,'',self::ONE_RECORD,$condition);
		if(is_null($user))
		{
			$user = parent::retrieve(self::CLUB_REG_TABLE_NAME,self::CLUB_REG_CLASS_NAME,'',self::ONE_RECORD,$condition);
			if(is_null($user))
				return false;
		}
		return true;
	}
	
	public function retrieve_extra_parent_ids($id)
	{
		$condition = "user_id = '".$id."'";
		$parents = parent::retrieve(self::TABLE_PARENTS_NAME,null,'',self::MANY_RECORDS,$condition);
		$parent_ids = array();
		foreach ($parents as $id)
		{
			$parent_ids[] = $id->parent_id;
		}
		return $parent_ids;
	}
	
	public function retrieve_user_from_post($user, $parent_user = null)
	{
		//Check if the parent_user can still have children and restrict clubverantwoordelijke to only admins
		//user can only belong to coaches of the clubverantwoordlijke etc
		$data = array();
		$msg = "";
		if(is_null($parent_user))
			$parent_user = $this->manager->get_user();
		
		if(((Request::post("username") == "" || Request::post("pwd")=="") && is_null($user)) || Request::post("email")=="" || Request::post("lastname")=="")
		{
			$msg .= '<p class="error">' . Language::get_instance()->translate(81) . '</p>';
		}
		
		if(!is_null($user))
			$data["id"] = $user->get_id();
		else
			$data["id"] = null;
		
		if(!DataManager::parse_checkbox_value(Request::post("test_account")))
		{
			$data["parent_id"] = Request::post("parent_id");
			if(!is_null($parent_user) && is_null($data["parent_id"]) && $parent_user->get_id() != $user->get_id())
				$data["parent_id"] = $parent_user->get_id();
			elseif(!is_null($parent_user) && is_null($data["parent_id"]) && $parent_user->get_id() == $user->get_id())
				$data["parent_id"] = $user->get_parent_id();
				
			if(!$this->manager->get_user()->is_admin() && (is_null($data["parent_id"]) || ($data["parent_id"] == 0 && (is_null($user) || (!is_null($user) && $user->get_group_id() != GroupManager::GROUP_CLUB_ID)))))
			{
				$msg .= '<p class="error">' . Language::get_instance()->translate(1148) . '</p>';
			}
			$data["extra_parent_ids"] = Request::post("extra_parent_ids");
			if(!is_null($user) && is_null($data["extra_parent_ids"]) && $parent_user->get_group_id() == GroupManager::GROUP_CLUB_ID)
				$data["extra_parent_ids"] = $user->get_extra_parent_ids();
			$data["credits"] = Request::post("credits");
			if(is_null($data["credits"]) && !is_null($user))
				$data["credits"] = $user->get_credits();
			elseif(is_null($data["credits"]) || $data["credits"] == "")
				$data["credits"] = 0;
			if($data["credits"]==Language::get_instance()->translate(380))
				$data["credits"] = -1;
		}
		else
		{
			$data["parent_id"] = 0;
			$data["extra_parent_ids"] = array();
			$data["group_id"] = 0;
			$data["credits"] = 0;
		}
		
		if(is_null($user))
			$data["username"] = addslashes(htmlspecialchars(str_replace(" ", "_" , Request::post("username"))));
		else
			$data["username"] = addslashes($user->get_username());	
			
		if(Request::post("rep_pwd") != Request::post("pwd"))
			$msg .= '<p class="error">' . Language::get_instance()->translate(433) . '</p>';
		if(Request::post("pwd")!="")	
			$data["password"] = Hashing::hash(Request::post("pwd"));
		elseif(!is_null($user))
			$data["password"] = $user->get_password();
		else
			$data["password"] = "";
			
		//$data["admin"] = Request::post("group_id") == GroupManager::GROUP_ADMIN_ID;			
		$data["email"] = Request::post("email");
				
		$data["firstname"] = addslashes(htmlspecialchars(Request::post("firstname")));
		$data["lastname"] = addslashes(htmlspecialchars(Request::post("lastname")));
		//$data["language"] = in_array(Request::post("user_language"), Setting::get_instance()->get_default_setting("supported_languages"))?Request::post("user_language"):Language::get_instance()->get_language();
		$data["language"] = "NL";
		$data["sex"] = Request::post("sex")=="F"?"F":"M";
		$data["avatar"] = Request::post("avatar");	
		$data["address"] = addslashes(htmlspecialchars(Request::post("address")));

		if(!is_null($parent_user) && !is_null($this->manager->get_user()) && $this->manager->get_user()->is_admin())
		{
			$data["activation_code"] = self::parse_checkbox_value(Request::post("activated"));
		}
		else
		{
			$data["activation_code"] = 1;
		}
		
		if(!DataManager::parse_checkbox_value(Request::post("test_account")))
		{
			if(is_null(Request::post("group_id")))
				$data["group_id"] = $user->get_group_id();
			else
				$data["group_id"] = Request::post("group_id");
				
			$groups = GroupDataManager::instance($this->manager)->retrieve_groups_by_right($parent_user->get_group_id());
		
			if(!is_null($data["group_id"]) && $data["group_id"] != GroupManager::GROUP_CLUB_ID && !is_null($user) && $user->get_id() != $this->manager->get_user()->get_id()
				&& !is_null($this->manager->get_user()) && !$this->manager->get_user()->is_admin())
			{
				if(!is_null($this->manager->get_user()) && (is_null($user) || $this->manager->get_user()->get_id() != $user->get_id()))
				{
					$parents = UserDataManager::instance(null)->retrieve_siblings_by_user($parent_user);
				}
				
				$count = count($data["extra_parent_ids"]);
				if(is_array($data["extra_parent_ids"]) && $count)
				{
					$validation = true;
					foreach($data["extra_parent_ids"] as $id)
					{
						$found_parent = false;
						foreach($parents as $parent)
						{
							if($parent->get_id() == $id)
								$found_parent = true;
						}
						if(!$found_parent)
						{
							$validation = false;
							break;
						}
					}
					
					if(!$validation)
					{
						$data["extra_parents_ids"] = array();
						$msg .= '<p class="error">' . Language::get_instance()->translate(93) . '</p>';
					}
				}
			}
			elseif(is_null($this->manager->get_user()) || (!$this->manager->get_user()->is_admin() && (!is_null($user) && $user->get_id() != $this->manager->get_user()->get_id())))
			{	
				$data["group_id"] = 0;
				$msg .= '<p class="error">' . Language::get_instance()->translate(93) . '</p>';
			}
			
			if($msg == "")
			{
				if(!is_null(Request::post("coach_pupils")) && $parent_user->get_group_id() == GroupManager::GROUP_CLUB_ID)
				{
					 $coaches = $this->retrieve_club_registration_coaches($parent_user->get_id(), true);
					 if(!isset($coaches[Request::post("coach_pupils")]))
						$msg .= '<p class="error">' . Language::get_instance()->translate(1138) . '</p>';
				}
				elseif(!is_null(Request::post("pupils")) && $parent_user->get_group_id() == GroupManager::GROUP_CLUB_ID)
				{
					if(Request::post("pupils") == "" || !is_numeric(Request::post("pupils")) || Request::post("pupils") < 0)
						$msg .= '<p class="error">' . Language::get_instance()->translate(1140) . '</p>';
					elseif($parent_user->get_credits() != -1)
					{
						$credits_needed = RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "credits_coach") + (Request::post("pupils") * RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "credits_pupil"));
						if($credits_needed > $parent_user->get_credits())
							$msg .= '<p class="error">' . Language::get_instance()->translate(1139) . '</p>';
					}
				}
			}
		}
		
		
		$user = new User($data);
		if(!is_null(Request :: post("rating")) && !is_null(Request :: post("rd")) && $this->manager->get_user()->is_admin())
		{
			if(is_numeric(Request :: post("rating")) && Request :: post("rating") > 0 && 
			   is_numeric(Request :: post("rd")) && Request :: post("rd") > 0)
			{
				$chess_profile = new UserChessProfile();
				$chess_profile->set_user_id($user->get_id());
				$chess_profile->set_rating(Request :: post("rating"));
				$chess_profile->set_rd(Request :: post("rd"));
				$user->set_chess_profile($chess_profile);
			}
			else
			{
				$msg .= '<p class="error">' . Language::get_instance()->translate(81) . '</p>';
			}
		}
		
		if($msg != "")
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->set_message($msg);
		}
		return $user;
	}
	
	
	public function retrieve_import_list_from_post($coach)
	{
		$arr = array();
		$arr["prefix"] = Request::post("prefix");
		$arr["password"] = Request::post("password");
		$arr["emailaslogin"] = $this->parse_checkbox_value(Request::post("emailaslogin"));
		if(($arr["prefix"] == "" && !$arr["emailaslogin"]) || $arr["password"] == "")
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->set_message(Language::get_instance()->translate(81));
		}
		$file = Request::file("import_user_list");
		if(!is_null($file))
		{
			if(($handle = fopen($file["tmp_name"], "r")) !== FALSE) 
			{
				$file_arr = array();
				$seperator = $this->get_seperator_importlist($handle, $coach);
				if($seperator===false)
				{
					Error::get_instance()->set_result(false);
					Error::get_instance()->set_message(Language::get_instance()->translate(1372));
				}
				else
				{
					$title = fgetcsv($handle, 0, $seperator);
					if(mb_detect_encoding($title[0]) != "UTF-8")
					{
						Error::get_instance()->set_result(false);
						Error::get_instance()->set_message(Language::get_instance()->translate(1371));
					}
					else
					{
						while (($data = fgetcsv($handle, 0, $seperator)) !== FALSE) 
						{
							$file_arr[] = $data;
						}
						$arr["file"] = $file_arr;
					}
				}
				fclose($handle);
			}
			else
			{
				Error::get_instance()->set_result(false);
				Error::get_instance()->set_message(Language::get_instance()->translate(93));
			}
		}
		return $arr;
	}
	
	private function get_seperator_importlist($list, $coach)
	{
		for($i=0;$i<3;$i++)
		{
			switch($i)
			{
				case 0: $seperator = ","; break;
				case 1: $seperator = ";"; break;
				case 2: $seperator = "\t"; break;
			}
			
			$title = fgetcsv($list, 0, $seperator);
			rewind($list);
			$cond = !strcmp(strtolower(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $title[0])), "voornaam") && !strcmp(strtolower($title[1]), "naam") && !strcmp(strtolower($title[2]), "e-mail");
			if(!$coach)
				$cond = $cond && !strcmp(strtolower($title[3]), "coach");
			if($cond)
			{
				return $seperator;
			}
		}
		return false;
	}
	
	 public function retrieve_activation_code()
	 {
	 	do 
	 	{
		 	$code = $this->manager->generate_code(50);
		 	$condition = 'activation_code = \''.$code.'\'';
			$user = parent::retrieve(self::TABLE_NAME,self::CLASS_NAME,'',self::ONE_RECORD,$condition);
	 	}
		while(!is_null($user));
		return $code;
	 }
	 
 	public function retrieve_user_id_by_activation_code($username, $activation_code)
	 {
		$condition = "activation_code = '".$activation_code."' AND username = '".$username."'";
		$user = parent::retrieve(self::TABLE_NAME,self::CLASS_NAME,'',self::ONE_RECORD,$condition);
		if(!is_null($user))
			return $user->get_id();
		else
			return -1;
	 }
	 
	public function activate_account($id)
	 {
	 	$custom = new CustomProperties();
	 	$custom->add_property("activation_code", 1);
		parent::update(self::TABLE_NAME,$custom,"id = " . $id);
	 }
	/* USER_CHESS_PROFILE */
	
	const CHESS_TABLE_NAME = 'user_chess_profile';
	const CHESS_CLASS_NAME = 'UserChessProfile';

	public function update_user_chess_profile($user_chess_profile)
	{
		$conditions = 'user_id="'.$user_chess_profile->get_user_id().'"';
		return parent::update(self::CHESS_TABLE_NAME,$user_chess_profile,$conditions);
	}
	
	public function delete_user_chess_profile($user_id)
	{
		$conditions = 'user_id="'.$user_id.'"';
		return parent::delete(self::CHESS_TABLE_NAME,$conditions);
	}

	public function insert_user_chess_profile($user_chess_profile)
	{
		return parent::insert(self::CHESS_TABLE_NAME,$user_chess_profile);
	}
	
	public function retrieve_user_chess_profile($user_id)
	{
		$conditions = 'user_id="'.$user_id.'"';
		$chess_profile = parent::retrieve(self::CHESS_TABLE_NAME,self::CHESS_CLASS_NAME,'',self::ONE_RECORD,$conditions);
		if(is_null($chess_profile) && !is_null($this->retrieve_user($user_id)))
		{
			$chess_profile = new UserChessProfile();
			$chess_profile->set_user_id($user_id);
			$chess_profile->set_rating(1200);
			$chess_profile->set_rd(350);
			$this->insert_user_chess_profile($chess_profile);
		}
		return $chess_profile;
	}
	
	public function insert_user_request($user_request)
	{
		return parent :: insert(self :: REQ_TABLE_NAME, $user_request);
	}
	
	public function retrieve_user_request($id)
	{
		return parent::retrieve_by_id(self::REQ_TABLE_NAME,self::REQ_CLASS_NAME,$id);
	}
	
	public function retrieve_user_requests()
	{
		return parent::retrieve(self::REQ_TABLE_NAME,self::REQ_CLASS_NAME, 'id DESC', self::MANY_RECORDS);
	}
	
	public function retrieve_user_request_from_post()
	{
		$arr = array();
		$arr['firstname'] = Request :: post("firstname");
		$arr['lastname'] = Request :: post("lastname");
		$arr['language'] = Request :: post("language_req");
		$arr["sex"] = Request::post("sex")=="F"?"F":"M";
		$arr['message'] = addslashes(htmlspecialchars(Request :: post("message")));
		$arr['accounttype'] = Request :: post("type");
		$arr['email'] = Request :: post("email");
		if(is_null($arr['firstname']) || $arr['firstname'] == "" || 
		   is_null($arr['lastname']) || $arr['lastname'] == "" ||
		   is_null($arr['language']) || $arr['language'] == "" ||
		   is_null($arr['email']) || $arr['email'] == "" ||
		   ($arr['accounttype'] != 0 && $arr['accounttype'] != 1))
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->set_message(Language::get_instance()->translate(81));
		}
		return new UserRequest($arr);
	}

	public function delete_other_user_requests($filter)
	{
		$size = count($filter);
		if($size)
		{
			$condition = "`id` NOT IN (";
			$i = 1;
			foreach($filter as $id)
			{
				$condition .= "'" . $id . "'";
				if($i<$size)
					$condition .= ",";
				$i++;
			}
			$condition .= ")";
			return parent::delete(self::REQ_TABLE_NAME,$condition);
		}
		else
		{	
			return parent::delete(self::REQ_TABLE_NAME, '');
		}
	}
	
	public function count_user_requests()
	{
		return parent::count(self::REQ_TABLE_NAME);
	}
	
	public function insert_user_request_course($request)
	{
		$properties = $request->get_properties();
		unset($properties["sex"]);
		$new_properties = new CustomProperties();
		$new_properties->set_properties($properties);
		return parent::insert(self::REQC_TABLE_NAME, $new_properties);
	}
	
	//CLUB REGISTRATION
	const CLUB_REG_TABLE_NAME = 'user_club_registration';
	const CLUB_REG_CLASS_NAME = 'ClubRegistration';
	const CLUB_REG_COACH_TABLE_NAME = 'user_club_registration_coaches';

	public function insert_registration($registration)
	{
		if(!is_null($registration->get_organisation()) && $registration->get_organisation_id() == 0)
		{
			$organisation_id = $this->insert_organisation($registration->get_organisation(), false);
			$registration->set_organisation_id($organisation_id);
		}
		return parent::insert(self::CLUB_REG_TABLE_NAME, $registration);
		/*
		if($id)
		{
			foreach($registration->get_coaches() as $coach => $pupils)
			{
				$custom_properties = new CustomProperties();
				$custom_properties->add_property("club_registration_id", $id);
				$custom_properties->add_property("coach", $coach);
				$custom_properties->add_property("pupils", $pupils);
				$custom_properties->add_property("created", 0);
				parent::insert(self::CLUB_REG_COACH_TABLE_NAME, $custom_properties);
			}
		}
		*/
		//return Error::get_instance()->get_result();
	}
	
	public function update_created_club_registration($id, $created)
	{
		$custom = new CustomProperties();
		$custom->add_property("created", $created);
		return parent::update(self::CLUB_REG_TABLE_NAME, $custom, "id = " . $id);
	}
	
	public function retrieve_club_registration_coaches($club_registration_id, $user_id = false)
	{
		$coaches = array();
		$conditions = "club_registration_id = " . $club_registration_id;
		if($user_id)
		{
			$conditions = "user_id = " . $club_registration_id . " AND created = 0";
		}
		$results = parent::retrieve(self::CLUB_REG_COACH_TABLE_NAME, null, '', self::MANY_RECORDS, $conditions);
		foreach($results as $r)
		{
			$coaches[$r->coach] = $r->pupils;
		}
		return $coaches;
	}
	
	public function update_club_registration_coaches_user_id($club_registration_id, $user_id)
	{
		$custom = new CustomProperties();
		$custom->add_property("user_id", $user_id);
		return parent::update(self::CLUB_REG_COACH_TABLE_NAME, $custom, "club_registration_id = " . $club_registration_id);
	}

	public function update_club_registration_coaches_created_by_user_id($user_id, $coach)
	{
		$custom = new CustomProperties();
		$custom->add_property("created", 1);
		return parent::update(self::CLUB_REG_COACH_TABLE_NAME, $custom, "user_id = " . $user_id . " AND coach = " . $coach);
	}
	
	public function retrieve_club_registrations()
	{
		if($this->manager->get_user()->is_admin())
			return parent::retrieve(self::CLUB_REG_TABLE_NAME, self::CLUB_REG_CLASS_NAME, 'created ASC, id DESC', self::MANY_RECORDS);
		else
			return array();
	}
	
	public function retrieve_club_registration($id)
	{
		return parent::retrieve_by_id(self::CLUB_REG_TABLE_NAME, self::CLUB_REG_CLASS_NAME, $id);
	}
	
	public function is_code($code)
	{
		$condition = 'code = \''.$code.'\'';
		$upg = parent::retrieve(self::CLUB_UPG_TABLE_NAME,self::CLUB_UPG_CLASS_NAME,'',self::ONE_RECORD,$condition);
		if(is_null($upg))
			return false;
		else
			return true;
	}
	
	public function retrieve_club_registration_from_post($request = false)
	{
		$arr = array();
		$arr["id"] = 0;
		if(!$request)
		{
		 	$arr['username'] = addslashes(htmlspecialchars(str_replace(" ", "_" , Request::post("username"))));
			$arr["sex"] = Request::post("sex")=="F"?"F":"M";
			$arr['rating'] = Request :: post("rating");
			$arr['created'] = 0;
			$arr['organisation_id'] = 0;
			$arr['registration_type'] = Request::post("registration_type");
		}
		else 
		{
			$arr["sex"] = "";
		}
		$arr['firstname'] = addslashes(htmlspecialchars(Request :: post("firstname")));
		$arr['lastname'] = addslashes(htmlspecialchars(Request :: post("lastname")));
		$arr['language'] = addslashes(htmlspecialchars(Request :: post("language_req")));
		$arr['email'] = addslashes(htmlspecialchars(Request :: post("email")));
		//$arr['infinite'] = Request::post("price_arrangement");
		//$arr['end_date'] = time();
		//dump($_POST);
		//dump(Request::post("registration_type"));
		/*
		$code = $this->manager->generate_ogm(12);
		while($this->is_code($code))
		{
			$code = $this->manager->generate_ogm(12);
		}
		$arr['code'] = $code;
		if($arr['infinite']==1 || $arr['infinite']==2)
		{
			$arr['price'] = RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "max_price_" . $arr['infinite']);
			$arr['end_date'] = RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "date_max_price_" . $arr['infinite']);
			$arr["coaches"] = array();
		}
		else
		{
			$arr['price'] = GroupDataManager::retrieve_price(GroupManager::GROUP_CLUB_ID);
		
			$count = 1;
			$arr["coaches"] = array();
			$pupils = Request::post("coach" . $count . "_pupils");
			while(!is_null($pupils) && is_numeric($pupils) && $pupils != 0)
			{
				$arr["coaches"][$count] = $pupils;
				$arr['price'] += GroupDataManager::retrieve_price(GroupManager::GROUP_COACH_ID) + ($pupils*GroupDataManager::retrieve_price(GroupManager::GROUP_PUPIL_ID));
				$count++;
				$pupils = Request::post("coach" . $count . "_pupils");
			}
			$arr['end_date'] = RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "date_price_users");
			
			$end_date_1 = RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "date_max_price_1");
			$max_price_1 = RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "max_price_1");
			$max_price_2 = RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "max_price_2");
			if(!is_null($max_price_2) || $max_price_2 != "" || $max_price_2 != 0)
			{
				$end_date_2 = RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "date_max_price_2");
				if($end_date_1 < $end_date_2 && $arr['end_date'] <= $end_date_1)
				{
					$max_price = $max_price_1;
					$end_date = $end_date_1;
					$infinite = 1;
				}
				elseif($end_date_2 < $end_date_1 && $arr['end_date'] <= $end_date_2)
				{
					$max_price = $max_price_2;
					$end_date = $end_date_2;
					$infinite = 2;
				}
				elseif($end_date_1 < $end_date_2)
				{
					$max_price = $max_price_2;
					$end_date = $end_date_2;
					$infinite = 2;
				}
				else
				{
					$max_price = $max_price_1;
					$end_date = $end_date_1;
					$infinite = 1;
				}
			}
			else
			{
				$max_price = $max_price_1;
				$end_date = $end_date_1;
				$infinite = 1;
			}
			
			//dump($arr['price']);
			if($arr['price']>=$max_price)
			{
				$arr['price'] = $max_price;
				$arr['end_date'] = $end_date;
				$arr['infinite'] = $infinite;
				$arr["coaches"] = array();
			}
		}
		*/
		if(!$request)
		{
			if(!is_null(Request::post("pwd")) && Request::post("pwd") != "")
			{
				if(Request::post("rep_pwd") != Request::post("pwd"))
				{
					Error::get_instance()->set_result(false);
					Error::get_instance()->append_message(Language::get_instance()->translate(433));
				}
				$arr["password"] = Hashing::hash(Request::post("pwd"));
			}
			else
				$arr["password"] = "";
			
			if(is_null($arr["rating"]) || !is_numeric($arr["rating"]) || $arr["rating"] < 0)
			{
				$arr["rating"] = 0;
				//Error::get_instance()->set_result(false);
				//Error::get_instance()->append_message(Language::get_instance()->translate(550));
			}
			if(!is_null($arr['username']) && $arr['username'] != "" && $this->username_exists($arr['username']))
			{
				Error::get_instance()->set_result(false);
				Error::get_instance()->append_message(Language::get_instance()->translate(459));
			}
		}

		if((!$request && (is_null($arr['username']) || $arr['username'] == "")) ||
				is_null($arr['firstname']) || $arr['firstname'] == "" ||
				is_null($arr['lastname']) || $arr['lastname'] == "" ||
				is_null($arr['language']) || $arr['language'] == "" ||
				is_null($arr['email']) || $arr['email'] == ""||
				(!$request && (is_null($arr['password']) || $arr['password'] == ""))/* ||
				((empty($arr["coaches"]) || !count($arr["coaches"])) && $arr["infinite"]==0)*/)
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->append_message(Language::get_instance()->translate(81));
		}
		
		//Dat File upload shits
		/*
		$arr["club_file"] = "";
		$arr["club_file_name"] = "";
		if(isset($_FILES["club_file"]))
			$upload_file = $_FILES["club_file"];
		else
			$upload_file = null;
		//dump($_POST);
		$file_types = array('pdf' => 'application/pdf', 'xls' => 'application/vnd.ms-excel', 'ods' => 'application/vnd.oasis.opendocument.spreadsheet');
		if(isset($upload_file) && !empty($upload_file["tmp_name"]))
		{
			if($upload_file['error'] == UPLOAD_ERR_INI_SIZE)
			{
				Error::get_instance()->set_result(false);
				Error::get_instance()->append_message(Language::get_instance()->translate(1209) . ": " . ini_get("upload_max_filesize"));
			}
			if(!in_array($upload_file['type'], $file_types))
			{
				Error::get_instance()->set_result(false);
				$type_txt = "";
				foreach ($file_types as $index => $type)
				{
					$type_txt .= ($type_txt!=""?", ": "") . $index;
				}
				Error::get_instance()->append_message(Language::get_instance()->translate(1210) . ": " . $type_txt);
			}

			if($upload_file['error'] == UPLOAD_ERR_OK && Error::get_instance()->get_result())
			{
				$arr["club_file"] = array("tmp" => $upload_file["tmp_name"], "name" => $upload_file["name"]);
			}
			else if(Error::get_instance()->get_result())
			{
				Error::get_instance()->set_result(false);
				Error::get_instance()->append_message(Language::get_instance()->translate(1211));
			}
		}
		else if((is_null(Request::post("confirmed")) || (!is_null(Request::post("confirmed")) && Request::post("confirmed") == 0)))
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->append_message(Language::get_instance()->translate(1212));
		}
		//dump($_POST);
		//dump($arr);
		//dump($arr);
		//dump($_POST);
		$club_file_name = Request::post("club_file_name");
		if(isset($upload_file) && !empty($upload_file["tmp_name"]) && Error::get_instance()->get_result())
		{
			//dump("fuck this shit!");
			//dump($arr["club_file"]);
			$file_name = $this->get_club_file_name($arr["club_file"]["name"]);
			//dump($file_name);
			//dump($arr);
			move_uploaded_file($arr["club_file"]["tmp"],"files/club_registrations/" . $file_name);
			$arr["club_file_name"] = $arr["club_file"]["name"];
			$arr["club_file"] = $file_name;
		}
		elseif(!is_null($club_file_name))
		{
			$arr["club_file_name"] = Request::post("club_file_name");
			$arr["club_file"] = Request::post("club_file");
		}
		//dump($arr);
		//dump("fuck!");
		//exit;
		//dump(Error::get_instance());
		//exit;
		//DAt end of dat file upload
		 * 
		 */
		return (!$request?new ClubRegistration($arr):new UserAbstract($arr));
	}
	
	/* UPGRADE */
	const CLUB_UPG_TABLE_NAME = 'user_club_upgrade';
	const CLUB_UPG_CLASS_NAME = 'Upgrade';

	public function retrieve_upgrade($id)
	{
		return parent::retrieve_by_id(self::CLUB_UPG_TABLE_NAME, self::CLUB_UPG_CLASS_NAME, $id);
	}
	
	public function insert_upgrade($upgrade)
	{
		parent::insert(self::CLUB_UPG_TABLE_NAME, $upgrade);
		return Error::get_instance()->get_result();
	}
	
	public function retrieve_club_upgrades()
	{
		if($this->manager->get_user()->is_admin())
			return parent::retrieve(self::CLUB_UPG_TABLE_NAME, self::CLUB_UPG_CLASS_NAME, 'upgraded ASC, id DESC', self::MANY_RECORDS);
		else
			return array();
	}
	
	public function retrieve_upgrade_by_user_id($user_id, $upgraded = 0)
	{
		return parent::retrieve(self::CLUB_UPG_TABLE_NAME, self::CLUB_UPG_CLASS_NAME, '', self::ONE_RECORD, 'user_id = ' . $user_id . ' AND upgraded = ' . $upgraded . ' AND end_date >= ' . time());
	}

	public function upgrade_account($upgrade, $upgraded = 0)
	{
		$user = $this->retrieve_user($upgrade->get_user_id());
		if(!is_null($user))
		{
			$custom = new CustomProperties();
			$custom->add_property("id", $upgrade->get_id());
			$custom->add_property("upgraded", $upgraded);
			parent::update(self::CLUB_UPG_TABLE_NAME, $custom, 'id = ' . $upgrade->get_id());
			
			$this->upgrade_user_account($user, $upgraded);
			return Error::get_instance()->get_result();
		}
		return false;
	}
	
	public function upgrade_user_account($user, $upgrade = 0)
	{
		switch ($user->get_group_id())
		{
			case GroupManager::GROUP_FREE_CLUB_ID:
			case GroupManager::GROUP_CLUB_ID: $user->set_group_id($upgrade?GroupManager::GROUP_CLUB_ID:GroupManager::GROUP_FREE_CLUB_ID); break;
			case GroupManager::GROUP_FREE_COACH_ID:
			case GroupManager::GROUP_COACH_ID: $user->set_group_id($upgrade?GroupManager::GROUP_COACH_ID:GroupManager::GROUP_FREE_COACH_ID); break;
			case GroupManager::GROUP_FREE_PUPIL_ID:
			case GroupManager::GROUP_PUPIL_ID: $user->set_group_id($upgrade?GroupManager::GROUP_PUPIL_ID:GroupManager::GROUP_FREE_PUPIL_ID); break;
			case GroupManager::GROUP_FREE_INDIVIDUAL_ID:
			case GroupManager::GROUP_INDIVIDUAL_ID: $user->set_group_id($upgrade?GroupManager::GROUP_INDIVIDUAL_ID:GroupManager::GROUP_FREE_INDIVIDUAL_ID); break; 
		}
		$this->update_user($user);
		
		$children = $this->retrieve_children($user->get_id(), true);
		foreach($children as $child)
		{
			$this->upgrade_user_account($child, $upgrade);
		}
	}
	
	public function retrieve_club_upgrade_from_post()
	{
		$arr = array();
		$arr["id"] = 0;
		$arr["user_id"] = $this->manager->get_user()->get_id();
		$arr["upgraded"] = 0;
		$arr['infinite'] = Request::post("price_arrangement");
		$arr['end_date'] = time();
		$arr['price'] = 0;
		
		$code = $this->manager->generate_ogm(12);
		while($this->is_code($code))
		{
			$code = $this->manager->generate_ogm(12);
		}
		$arr['code'] = $code;
		if($arr['infinite']==1 || $arr['infinite']==2)
		{
			$arr['price'] = RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "max_price_" . $arr['infinite']);
			$arr['end_date'] = RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "date_max_price_" . $arr['infinite']);
		}
		elseif($arr['infinite'] == 3)
		{
			$arr['price'] = RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "price_individual");
			$arr['end_date'] = RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "date_price_users");
		}
		else
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->append_message(Language::get_instance()->translate(1248));
		}
		return new Upgrade($arr);
	}
	
	/*
	public function get_club_file_name($name)
	{
		$directory = Path::get_path().'/files/club_registrations';
		$handler = opendir($directory);
		$return_name = $name;
		$i = 0;
		//dump($name);
		while ($file = readdir($handler))
		{
			//dump("ead file mkay");	
			//dump($file);
			//dump($name);
		    if (!($file != "." && $file != ".." && $file==$name))
		    {
		    	//dump("fal	se");
		    	$return_name = substr($name, 0, strpos($name, ".")) . $i . substr($name, strpos($name, "."));
		    	//dump($return_name);
		    	$i++;	
		    	//return $return_name;
		    }
		}
		//dump("/reeeeeeeeeeeeeeeeettttttttttttttttuuuuuuuuuuuuuuuuuuuurrrrrrrrrrrrrrrrrrrrrrrrnnnnnnnnnnnnnnnnnnnnnnnn!!!!!!!!!!!!!!!!!!!!!!");
		//dump($return_name);
		return $return_name;
	}
	*/
	
	public function retrieve_transfer_credits_from_post()
	{
		$data = new stdClass;
		$data->user_id = Request::post("user_id");
		$data->credits = Request::post("credits");
		if(is_null($data->user_id) || !is_numeric($data->user_id) || $data->user_id <= 0)
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->append_message(Language::get_instance()->translate(1177));
		}
		if(is_null($data->credits) || !is_numeric($data->credits) || $data->credits <= 0)
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->append_message(Language::get_instance()->translate(1176));
		}
		elseif($data->credits > $this->manager->get_user()->get_credits() && $this->manager->get_user()->get_credits() != -1)
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->append_message(Language::get_instance()->translate(1139));
		}		
		return $data;
	}
	
	//Organisations
	const ORG_TABLE_NAME = "organisation";
	const ORG_CLASS_NAME = "Organisation";
	const ADD_TABLE_NAME = "organisation_address";
	const ADD_CLASS_NAME = "Address";
	const CITY_TABLE_NAME = "organisation_address_city";
	const CITY_CLASS_NAME = "City";
	const PROV_TABLE_NAME = "organisation_address_province";
	const PROV_CLASS_NAME = "Province";
	
	public function retrieve_organisations($check_use = false, $in_use = false)
	{
		$conditions = "";
		if($check_use)
			$conditions = "in_use = " . ($in_use?"1":"0");
		return parent::retrieve(self::ORG_TABLE_NAME, self::ORG_CLASS_NAME, 'name', self::MANY_RECORDS, $conditions);
	}
	
	public function retrieve_address($address_id)
	{
		return parent::retrieve_by_id(self::ADD_TABLE_NAME, self::ADD_CLASS_NAME, $address_id);
	}
	
	public function retrieve_addresses()
	{
		return parent::retrieve(self::ADD_TABLE_NAME, self::ADD_CLASS_NAME);
	}
	
	public function update_address($address)
	{
		return parent::update_by_id(self::ADD_TABLE_NAME, $address);
	}
	
	public function insert_organisation($organisation, $check_address = true)
	{
		$address = $organisation->get_address();
		$add = false;
		$address_id = $this->address_exists($address);
		//dump("ho");
		if(is_null($address_id))
			$address_id = 0;
		//dump($address_id);
		if($address_id==0 || $check_address == false)
		{
			//dump("fuck!");
			$add = true;
			$address_id = $this->insert_address($organisation->get_address());
			$organisation->set_address_id($address_id);
		}
		elseif(!$this->organisation_name_exists($organisation->get_name(), $address_id))
		{
			//dump("hey");
			$add = true;
			$organisation->set_address_id($address_id);
		}
		
		if($add)
		{
			$organisation->set_club_manager_id(0);
			return parent::insert(self::ORG_TABLE_NAME, $organisation);
		}
		else
		{
			//dump($organisation);
			Error::get_instance()->set_result(false);
			Error::get_instance()->set_message(Language::get_instance()->translate(1190));
			return 0;
		}
	}
	
	public function retrieve_organisations_by_city_code($city_code, $organisation_type)
	{
		$cities = $this->retrieve_city_by_code_before_name($city_code);
		//dump($cities);
		if($cities != null && !empty($cities))
		{
			$city_cond = "";
			//dump($cities);
			foreach($cities as $index => $city)
			{
				if($index!=0)
					$city_cond .= ", ";
				$city_cond .= $city->get_id();
			}
			
			$conditions = "o.organisation_type = " . $organisation_type . " AND (c.id IN (" . $city_cond . ") OR ( c.city_code = '0' AND c.province_id = " . $city->get_province_id() . "))";
			$select = "o.*, a.street, a.nr, a.bus_nr, a.city_id, c.city_name, c.city_code, c.province_id, p.province_name"; 
			$join = array();
			$join[] = new Join(self::ORG_TABLE_NAME, "o", "id", Join::MAIN_TABLE);
			$join[] = new Join(self::ADD_TABLE_NAME, "a", "id", "LEFT JOIN", Join::MAIN_TABLE, "address_id");
			$join[] = new Join(self::CITY_TABLE_NAME, "c", "id", "LEFT JOIN", self::ADD_TABLE_NAME, "city_id");
			$join[] = new Join(self::PROV_TABLE_NAME, "p", "id", "LEFT JOIN", self::CITY_TABLE_NAME, "province_id");
			$results = parent::retrieve($join, self::ORG_CLASS_NAME, "name", self::MANY_RECORDS, $conditions, '', $select);
			if(count($results))
				return $results;
		}
		
		Error::get_instance()->set_result(false);
		Error::get_instance()->set_message(sprintf(Language::get_instance()->translate(1193), $city_code));
	}
	
	public function retrieve_organisation($id)
	{
		return parent::retrieve_by_id(self::ORG_TABLE_NAME, self::ORG_CLASS_NAME, $id);
	}
	
	public function organisation_in_use($organisation_id)
	{
		$condition = "id = " . $organisation_id . " AND in_use = 1";
		$count = parent::count(self::ORG_TABLE_NAME, $condition);
		return $count?true:false;
	}
	
	public function insert_address($address)
	{
		return parent::insert(self::ADD_TABLE_NAME, $address);
	}
	
	public function insert_province($province)
	{
		return parent::insert(self::PROV_TABLE_NAME, $province);
	}
	
	public function retrieve_province($province_id)
	{
		return parent::retrieve_by_id(self::PROV_TABLE_NAME, self::PROV_CLASS_NAME, $province_id);
	}
	
	public function retrieve_city_by_province_name($name)
	{
		$province = parent::retrieve(self::PROV_TABLE_NAME, self::PROV_CLASS_NAME, "", self::ONE_RECORD, "province_name = '" . addslashes(strtoupper($name)) . "'");
		return parent::retrieve(self::CITY_TABLE_NAME, self::CITY_CLASS_NAME, "", self::ONE_RECORD, "city_code = 0 AND city_name = 0 AND province_id = " . $province->get_id());
	}
	
	public function insert_city($city)
	{
		return parent::insert(self::CITY_TABLE_NAME, $city);
	}
	
	public function retrieve_city_by_code_before_name($code, $name = "", $add_new = false)
	{
		 $results = parent::retrieve(self::CITY_TABLE_NAME, self::CITY_CLASS_NAME, "", self::MANY_RECORDS, "city_code = '" . $code . "'");
		 if(count($results)>=1)
		 {
		 	if($name != "")
		 	{
			 	$result = parent::retrieve(self::CITY_TABLE_NAME, self::CITY_CLASS_NAME, "", self::ONE_RECORD, "city_code = " . $code . " AND city_name = '" . addslashes(strtoupper($name)) . "'");
			 	if(is_null($result) && $add_new)
			 	{
			 		$city = new City();
			 		$city->set_city_code($code);
			 		$city->set_city_name(addslashes(strtoupper($name)));
			 		$city->set_province_id($results[0]->get_province_id());
			 		$id = $this->insert_city($city);
			 		$city->set_id($id);
			 		return $city;
			 	}
			 	else
			 		return $result;
		 	}
			else
				return $results;
		 }
		 else
		 	return $results;
	}
	
	public function retrieve_city($city_id)
	{
		return parent::retrieve_by_id(self::CITY_TABLE_NAME, self::CITY_CLASS_NAME, $city_id);
	}
	
	public function address_exists($address)
	{
		$condition = "street = '" . $address->get_street() . "' AND city_id = '" . $address->get_city_id() . "' AND nr = " . $address->get_nr();
		$result = parent::retrieve(self::ADD_TABLE_NAME, self::ADD_CLASS_NAME, "", self::ONE_RECORD, $condition);
		if(!is_null($result))
			return $result->get_id();
		return 0;
	}
	
	public function organisation_name_exists($name, $address_id)
	{
		$condition = "name LIKE '" . $name . "' AND address_id = " . $address_id;
		$count = parent::retrieve(self::ORG_TABLE_NAME, self::ORG_CLASS_NAME, "", self::ONE_RECORD, $condition, '', '*', true);
		return $count?true:false;
	}
	
	public function retrieve_organisation_from_post()
	{
		$data["id"] = 0;
		$data["name"] = addslashes(htmlspecialchars(Request::post("organisation_name")));
		$data["email"] = addslashes(htmlspecialchars(Request::post("organisation_email")));
		$data["organisation_type"] = Request::post("registration_type") - 2;
		$data["address_id"] = 0;
		$data["in_use"] = 0;
		$data["club_manager_id"] = 0;
		
		$adres["id"] = 0;
		$adres["address_id"] = 0;
		$adres["street"] = addslashes(htmlspecialchars(Request::post("organisation_street")));
		$adres["nr"] = Request::post("organisation_nr");
		$adres["bus_nr"] = Request::post("organisation_bus_nr");
		
		if(is_null($adres["street"]) || $adres["street"] == "" ||
			is_null($adres["nr"]) || !is_numeric($adres["nr"]) || $adres["nr"]<=0 ||
			is_null($data["name"]) || $data["name"] == "" ||
			is_null($data["email"]) || $data["email"] == "")
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->set_message(Language::get_instance()->translate(245));
		}
		
		$city = $this->retrieve_city_by_code_before_name(addslashes(Request::post("organisation_city_code")), Request::post("organisation_city_name"));
		if(is_null($city))
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->set_message(Language::get_instance()->translate(1200));
			$adres["city_id"] = 0;
			$city = new City();
			$city->set_city_code(Request::post("organisation_city_code"));
			$city->set_city_name(Request::post("organisation_city_name"));
		}
		else 
		{
			$adres["city_id"] = $city->get_id();
		}
		
		$address = new Address($adres);
		$address->set_city($city);
		
		if($this->address_exists($address))
		{	
			Error::get_instance()->set_result(false);
			Error::get_instance()->set_message(Language::get_instance()->translate(1190));
		}
		
		$organisation = new Organisation($data);
		$organisation->set_address($address);
		return $organisation;
	}
}

?>