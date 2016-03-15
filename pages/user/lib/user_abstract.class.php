<?php
	
	class UserAbstract
	{
		private $id;
		private $email;
		private $firstname;
		private $lastname;
		private $language;
		private $sex;

		public function UserAbstract($data=null)
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
			$this->email=$data->email;
			$this->firstname=$data->firstname;
			$this->lastname=$data->lastname;
			$this->language=$data->language;
			$this->sex=$data->sex;
		}
		
		public function fillFromArray($data)
		{
			$this->email=$data["email"];
			$this->firstname=$data["firstname"];
			$this->lastname=$data["lastname"];
			$this->language=$data["language"];
			$this->sex=$data["sex"];
		}
		
		public function get_properties()
		{
			return array('id' => $this->id,
						 'email' => $this->email,
						 'firstname' => $this->firstname,
						 'lastname' => $this->lastname,
						 'language' => $this->language,
						 'sex' => $this->sex);
		}
		
		public function get_id(){	return $this->id; }
		public function set_id($id){	$this->id = $id; }
		public function get_email(){	return $this->email;	}
		public function set_email($email){	$this->email = $email;	}
		public function get_firstname(){	return $this->firstname;	}
		public function get_lastname(){	return $this->lastname;	}
		public function set_firstname($name){ $this->firstname = $name; }
		public function set_lastname($name){ $this->lastname = $name; }
		public function get_name(){	return $this->firstname." ". $this->lastname; }
		public function get_language(){	return $this->language;	}
		public function set_language($language){	$this->language = $language;	}
		public function get_sex(){	return $this->sex;	}
		public function set_sex($sex){	$this->sex = $sex;	}
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
		
	}
	
?>