<?php
	
	class Province
	{
		private $id;
		private $province_name;

		public function Province($data=null)
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
			$this->province_name=$data->province_name;
		}
		
		public function fillFromArray($data)
		{
			$this->id=$data["id"];
			$this->province_name=$data["province_name"];
		}
		
		public function get_properties()
		{
			return array('id' => $this->id,
						 'province_name' => $this->province_name);
		}
		
		public function set_province_name($province_name){		$this->province_name=$province_name;	}
		public function get_province_name(){	return $this->province_name;}
		public function get_id(){	return $this->id; }
		public function set_id($id){	$this->id = $id; }
	}
	
?>