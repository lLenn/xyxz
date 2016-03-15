<?php
	
	class User
	{
		private $id;
		private $parent_id;
		private $extra_parent_ids;
		private $username;
		private $password;
		private $email;
		private $firstname;
		private $lastname;
		private $language;
		private $sex;
		private $credits;
		private $avatar;
		private $activation_code;
		private $address;
		private $admin;
		private $group_id;
		private $chess_profile = null;
		private $group = false;

		public function User($data=null)
		{
			if(!is_null($data))
			{
				if(is_array($data))
					$this->fillFromArray($data);
				else
					$this->fillFromDatabase($data);
			}
		}
		
		public function fillFromDatabase($data)
		{
			$this->id=$data->id;
			$this->parent_id=$data->parent_id;
			$this->username=$data->username;
			$this->password=$data->password;
			$this->email=$data->email;
			$this->firstname=$data->firstname;
			$this->lastname=$data->lastname;
			$this->language=$data->language;
			$this->sex=$data->sex;
			$this->credits=$data->credits;
			$this->avatar=$data->avatar;
			if($this->avatar=="")	$this->avatar="./layout/images/standard.png";
			$this->activation_code=$data->activation_code;
			$this->address=$data->address;
			$this->admin=$data->admin;
			$this->group_id=$data->group_id;
		}
		
		public function fillFromArray($data)
		{
			$this->id=$data["id"];
			$this->parent_id=$data["parent_id"];
			$this->extra_parent_ids=$data["extra_parent_ids"];
			$this->username=$data["username"];
			$this->password=$data["password"];
			$this->email=$data["email"];
			$this->firstname=$data["firstname"];
			$this->lastname=$data["lastname"];
			$this->language=$data["language"];
			$this->sex=$data["sex"];
			$this->credits=$data["credits"];
			$this->avatar=$data["avatar"];
			if($this->avatar=="")	$this->avatar="./layout/images/standard.png";
			$this->activation_code=$data["activation_code"];
			$this->address=$data["address"];
			$this->group_id=$data["group_id"];
		}
		
		public function get_properties()
		{
			return array('id' => $this->id,
						 'parent_id' => $this->parent_id,
						 'username' => $this->username,
						 'password' => $this->password,
						 'email' => $this->email,
						 'firstname' => $this->firstname,
						 'lastname' => $this->lastname,
						 'language' => $this->language,
						 'sex' => $this->sex,
						 'credits' => $this->credits,
						 'avatar' => $this->avatar,
						 'activation_code' => $this->activation_code,
						 'address' => $this->address,
						 'group_id' => $this->group_id);
		}
	
		public function get_address(){		return $this->address;	}
		public function set_address($address){	$this->address = $address;	}
		public function get_avatar(){		return $this->avatar;	}
		public function set_avatar($avatar){		$this->avatar = $avatar;	}
		public function get_email(){	return $this->email;	}
		public function set_email($email){	$this->email = $email;	}
		public function set_userName($nm){		$this->username=$nm;	}
		public function get_username(){	return $this->username;}
		public function get_password(){	return $this->password;}
		public function set_password($pwd){	$this->password = $pwd;	}
		public function is_activated(){	return $this->activation_code==1;	}
		public function set_activated(){	$this->activation_code=1;	}
		public function get_id(){	return $this->id; }
		public function set_id($id){	$this->id = $id; }
		public function get_parent_id(){	return $this->parent_id; }
		public function set_parent_id($parent_id){	$this->parent_id = $parent_id; }
		public function get_parent()
		{
			$parent = UserDataManager::instance(null)->retrieve_user($this->get_parent_id());
			return $parent?$parent->get_username():"Geen";
		}
		public function get_extra_parent_ids()
		{
			if(is_null($this->extra_parent_ids))
			{
				$this->extra_parent_ids = UserDataManager::instance(null)->retrieve_extra_parent_ids($this->get_id());
			}
			return $this->extra_parent_ids;
		}
		public function set_extra_parent_ids($extra_parent_ids) { $this->extra_parent_ids = $extra_parent_ids; }
		public function get_name(){	return $this->firstname." ". $this->lastname; }
		public function get_firstName(){	return $this->firstname;	}
		public function get_lastName(){	return $this->lastname;	}
		public function set_firstName($name){ $this->firstname = $name; }
		public function set_lastName($name){ $this->lastname = $name; }
		public function get_language(){	return $this->language;	}
		public function set_language($language){	$this->language = $language;	}
		public function get_sex(){	return $this->sex;	}
		public function set_sex($sex){	$this->sex = $sex;	}
		public function get_credits($str = false)
		{	
			if($str && $this->credits==-1) return Language::get_instance()->translate(380);
			else return $this->credits;
		}
		public function set_credits($credits){	$this->credits = $credits;	}
		public function get_sex_full()
		{	
			switch($this->sex)
			{ 
				case "M": return Language::get_instance()->translate(785);
						  break;
				case "F": return Language::get_instance()->translate(786);
						  break;
			}
		}
		public function get_activation_code(){	return $this->activation_code;	}
		public function set_activation_code($code){	$this->activation_code = $code;	}
		public function is_admin(){	return $this->admin == 1; }
		public function set_admin($admin){ $this->admin = $admin; }
		public function get_group_id(){	return $this->group_id;	}
		public function set_group_id($group_id){	$this->group_id = $group_id;	}
		public function set_chess_profile($chess_profile){ $this->chess_profile = $chess_profile; }
		public function get_chess_profile()
		{
			if(is_null($this->chess_profile))
			{
				$this->chess_profile = UserDataManager::instance(null)->retrieve_user_chess_profile($this->id);
			}
			return $this->chess_profile;
		}
		public function set_group($group){ $this->group = $group; }
		public function get_group()
		{
			if($this->group === false)
			{
				$this->group = GroupDataManager::instance(null)->retrieve_group($this->group_id);
			}
			return $this->group;
		}
		public function is_child($user_id, $first_level = false)
		{
			$is_child = false;
			if($user_id == $this->id || $this->is_admin())
				$is_child = true;
			else
			{
				$children = UserDataManager::instance(null)->retrieve_children($this->id, $first_level);
				foreach($children as $child)
				{
					if($child->get_id() == $user_id)
					{
						$is_child = true;
						break;
					}
				}
			}
			return $is_child;
		}
	}
	
?>