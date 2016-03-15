<?php
	
	class Organisation
	{
		private $id;
		private $name;
		private $email;
		private $address_id;
		private $club_manager_id;
		private $organisation_type;
		private $in_use;
		
		private $address = null;
		private $club_manager = null;

		public function Organisation($data=null)
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
			$this->name=Utilities::html_special_characters($data->name);
			$this->email=$data->email;
			$this->address_id=$data->address_id;
			$this->in_use=$data->in_use;
			$this->club_manager_id=$data->club_manager_id;
			$this->organisation_type=$data->organisation_type;
			if(isset($data->street) )
			{
				$address = new Address();
				$address->set_id($data->address_id);
				$address->set_street(Utilities::html_special_characters($data->street));
				$address->set_nr($data->nr);
				$address->set_bus_nr($data->bus_nr);
				if(isset($data->city_code))
				{
					$city = new City();
					$city->set_id($data->city_id);
					$city->set_city_code($data->city_code);
					$city->set_city_name(Utilities::html_special_characters($data->city_name));
					if(isset($data->province_name))
					{
						$province = new Province();
						$province->set_id($data->province_id);
						$province->set_province_name($data->province_name);
						$city->set_province($province);
					}
					$address->set_city($city);
				}
				$this->address = $address;
			}
		}
		
		public function fillFromArray($data)
		{
			$this->id=$data["id"];
			$this->name=$data["name"];
			$this->email=$data["email"];
			$this->address_id=$data["address_id"];
			$this->in_use=$data["in_use"];
			$this->club_manager_id=$data["club_manager_id"];
			$this->organisation_type=$data["organisation_type"];
		}
		
		public function get_properties()
		{
			return array('id' => $this->id,
						 'name' => $this->name,
						 'email' => $this->email,
						 'address_id' => $this->address_id,
						 'in_use' => $this->in_use,
						 'club_manager_id' => $this->club_manager_id,
						 'organisation_type' => $this->organisation_type);
		}
				
		public function get_email(){	return $this->email;	}
		public function set_email($email){	$this->email = $email;	}
		public function get_address_id(){	return $this->address_id;	}
		public function set_address_id($address_id){	$this->address_id = $address_id;	}
		public function get_in_use(){	return $this->in_use;	}
		public function set_in_use($in_use){	$this->in_use = $in_use;	}
		public function get_club_manager_id(){	return $this->club_manager_id;	}
		public function set_club_manager_id($club_manager_id){	$this->club_manager_id = $club_manager_id;	}
		public function get_organisation_type(){	return $this->organisation_type;	}
		public function set_organisation_type($organisation_type){	$this->organisation_type = $organisation_type;	}
		public function set_name($nm){		$this->name=$nm;	}
		public function get_name(){	return $this->name;}
		public function get_id(){	return $this->id; }
		public function set_id($id){	$this->id = $id; }
		
		public function set_address($address) { $this->address = $address; }
		public function get_address()
		{
			if(is_null($this->address))
			{
				$this->address = UserDataManager::instance(null)->retrieve_address($this->address_id);
			}
			return $this->address;
		}
	}
	
?>