<?php
	
	class Address
	{
		private $id;
		private $street;
		private $nr;
		private $city_id;
		private $bus_nr;
		
		private $city = null;

		public function Address($data=null)
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
			$this->street=$data->street;
			$this->nr=$data->nr;
			$this->city_id=$data->city_id;
			$this->bus_nr=$data->bus_nr;
		}
		
		public function fillFromArray($data)
		{
			$this->id=$data["id"];
			$this->street=$data["street"];
			$this->nr=$data["nr"];
			$this->city_id=$data["city_id"];
			$this->bus_nr=$data["bus_nr"];
		}
		
		public function get_properties()
		{
			return array('id' => $this->id,
						 'street' => $this->street,
						 'nr' => $this->nr,
						 'city_id' => $this->city_id,
						 'bus_nr' => $this->bus_nr);
		}
				
		public function get_nr(){	return $this->nr;	}
		public function set_nr($nr){	$this->nr = $nr;	}
		public function get_city_id(){	return $this->city_id;	}
		public function set_city_id($city_id){	$this->city_id = $city_id;	}
		public function get_bus_nr(){	return $this->bus_nr;	}
		public function set_bus_nr($bus_nr){	$this->bus_nr = $bus_nr;	}
		public function set_street($street){		$this->street=$street;	}
		public function get_street(){	return $this->street;}
		public function get_id(){	return $this->id; }
		public function set_id($id){	$this->id = $id; }
		public function set_city($city){	$this->city = $city; }
		public function get_city()
		{
			if(is_null($this->city))
			{
				$this->city = UserDataManager::instance(null)->retrieve_city($this->city_id);
			}
			return $this->city;
		}
		
	}
	
?>