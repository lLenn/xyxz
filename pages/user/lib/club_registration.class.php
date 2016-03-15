<?php
	
	class ClubRegistration extends UserAbstract
	{
		private $username;
		private $password;
		private $rating;
		private $created;
		/*
		private $code;
		private $price;
		private $infinite;
		private $end_date;
		*/
		private $organisation_id;
		private $registration_type;
		
		//private $coaches = null;
		private $organisation = null;

		public function ClubRegistration($data=null)
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
			parent::fillFromDatabase($data);
			$this->username=$data->username;
			$this->password=$data->password;
			$this->rating=$data->rating;
			$this->created=$data->created;
			//$this->code=$data->code;
			//$this->price=$data->price;
			//$this->infinite=$data->infinite;
			//$this->end_date=$data->end_date;
			//$this->club_file=$data->club_file;
			//$this->club_file_name=$data->club_file_name;
			$this->organisation_id=$data->organisation_id;
			$this->registration_type=$data->registration_type;
		}
		
		public function fillFromArray($data)
		{
			parent::fillFromArray($data);
			$this->username=$data["username"];
			$this->password=$data["password"];
			$this->rating=$data["rating"];
			$this->created=$data["created"];
			//$this->code=$data["code"];
			//$this->price=$data["price"];
			//$this->infinite=$data["infinite"];
			//$this->end_date=$data["end_date"];
			//$this->club_file=$data["club_file"];
			//$this->club_file_name=$data["club_file_name"];
			$this->organisation_id=$data["organisation_id"];
			$this->registration_type=$data["registration_type"];
			//dump($this->registration_type);
			//$this->coaches=$data["coaches"];
		}
		
		public function get_properties()
		{
			return array_merge(parent::get_properties(),
					array('username' => $this->username,
						 'password' => $this->password,
						 'rating' => $this->rating,
						 'created' => $this->created,
						 /*
						 'code' => $this->code,
						 'price' => $this->price,
						 'infinite' => $this->infinite,
						 'end_date' => $this->end_date,*/
						 'organisation_id' => $this->organisation_id,
						 'registration_type' => $this->registration_type));
		}
				
		public function get_address(){		return $this->address;	}
		public function get_rating(){	return $this->rating;	}
		public function set_rating($rating){	$this->rating = $rating;	}
		public function set_username($nm){		$this->username=$nm;	}
		public function get_username(){	return $this->username;}
		public function get_password(){	return $this->password;}
		public function set_password($pwd){	$this->password = $pwd;	}
		public function is_created(){	return $this->created==1;	}
		public function set_created(){	$this->created=1;	}
		/*
		public function get_code(){	return $this->code;	}
		public function set_code($code){	$this->code = $code;	}
		public function get_price(){	return $this->price;	}
		public function set_price($price){	$this->price = $price;	}
		public function get_infinite(){	return $this->infinite;	}
		public function set_infinite($infinite){	$this->infinite = $infinite;	}
		public function get_end_date(){	return $this->end_date;	}
		public function set_end_date($end_date){	$this->end_date = $end_date;	}
		public function get_end_date_to_txt() { return date('d/m/Y', $this->end_date); }
		/*
		public function get_club_file(){	return $this->club_file;	}
		public function set_club_file($club_file){	$this->club_file = $club_file;	}
		public function get_club_file_name(){	return $this->club_file_name;	}
		public function set_club_file_name($club_file_name){	$this->club_file_name = $club_file_name;	}
		*/
		public function get_organisation_id(){	return $this->organisation_id;	}
		public function set_organisation_id($organisation_id){	$this->organisation_id = $organisation_id;	}
		public function get_registration_type(){	return $this->registration_type;	}
		public function set_registration_type($registration_type){	$this->registration_type = $registration_type;	}
		public function get_registration_type_to_text() 
		{ 
			switch($this->registration_type)
			{
				case 1: return Language::get_instance()->translate(1233); break;
				case 2: return Language::get_instance()->translate(1230); break;
				case 3: return Language::get_instance()->translate(1231); break;
			}
		}
		/*
		public function get_coaches()
		{ 
			if(is_null($this->coaches))
			{
				$this->coaches = UserDataManager::instance(null)->retrieve_club_registration_coaches($this->id);
			}
			return $this->coaches; 
		}
		public function get_coaches_full()
		{ 
			if($this->infinite == 1 || $this->infinite == 2)
			{
				return Language::get_instance()->translate(380);
			}
			else
			{
				$html = '';
				foreach($this->get_coaches() as $index => $pupils)
				{
					$html .= Language::get_instance()->translate(796) . ' ' . $index . ': ' . Language::get_instance()->translate(1121) . ': ' . $pupils . '</br>';
				}
				return substr($html, 0, -5); 
			}
		}
		*/
		public function get_created_full() { return $this->created?Language::get_instance()->translate(378):Language::get_instance()->translate(379); }
	
		public function set_organisation($organisation) { $this->organisation = $organisation; }
		public function get_organisation()
		{ 
			if(is_null($this->organisation))
			{
				$this->organisation = UserDataManager::instance(null)->retrieve_organisation($this->organisation_id);
			}
			return $this->organisation; 
		}
		
		public function get_organisation_to_text()
		{
			$txt = "";
			if(!is_null($this->get_organisation()))
			{
				//dump($this->organisation);
				$txt .= $this->organisation->get_name();
				if(!is_null($this->organisation->get_address()))
				{
					$address = $this->organisation->get_address();
					//dump($address);
					if($address->get_street() != "0")
					{
						//dump("ui");
						$txt .= "<br/>" . $address->get_street() . " " . $address->get_nr() . ($address->get_bus_nr()==""?"":"_".$address->get_bus_nr());
					}
					
					$city = $address->get_city();
					//dump($city);
					if(!is_null($city->get_city_name()) && $city->get_city_name() != "" && $city->get_city_name() != "0")
					{
						//dump("u");
						$txt .= "<br/>" . $city->get_city_code() . " " . $city->get_city_name();
					}
					
					$province = $city->get_province();
					$txt .= "<br/>" . $province->get_province_name();
				}
			}
			return $txt;
		}
		
		/*
		public function get_url_of_that_file()
		{
			$txt = "";
			if(!is_null($this->club_file) && $this->club_file != "")
			{
				$txt = "<a href='" . Path::get_url_path() . "files/club_registrations/" . $this->club_file . "'>" . $this->club_file_name . "</a>";
			}
			return $txt;
		}
		*/
	}
	
?>