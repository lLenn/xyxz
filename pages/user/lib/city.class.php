<?php
	
	class City
	{
		private $id;
		private $city_code;
		private $city_name;
		private $province_id;
		
		private $province = null;

		public function City($data=null)
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
			$this->city_code=$data->city_code;
			$this->province_id=$data->province_id;
			$this->city_name=$data->city_name;
		}
		
		public function fillFromArray($data)
		{
			$this->id=$data["id"];
			$this->city_code=$data["city_code"];
			$this->province_id=$data["province_id"];
			$this->city_name=$data["city_name"];
		}
		
		public function get_properties()
		{
			return array('id' => $this->id,
						 'city_code' => $this->city_code,
						 'province_id' => $this->province_id,
						 'city_name' => $this->city_name);
		}
				
		public function get_city_code(){	return $this->city_code;	}
		public function set_city_code($city_code){	$this->city_code = $city_code;	}
		public function get_province_id(){	return $this->province_id;	}
		public function set_province_id($province_id){	$this->province_id = $province_id;	}
		public function get_city_name(){	return $this->city_name;	}
		public function set_city_name($city_name){	$this->city_name = $city_name;	}
		public function get_id(){	return $this->id; }
		public function set_id($id){	$this->id = $id; }
		public function set_province($province){	$this->province = $province; }
		public function get_province()
		{
			if(is_null($this->province))
			{
				$this->province = UserDataManager::instance(null)->retrieve_province($this->province_id);
			}
			return $this->province;
		}
	}
	
?>