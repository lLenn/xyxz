<?php
	
	class Upgrade
	{
		private $id;
		private $user_id;
		private $upgraded;
		private $code;
		private $price;
		private $infinite;
		private $end_date;

		public function Upgrade($data=null)
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
			$this->user_id=$data->user_id;
			$this->upgraded=$data->upgraded;
			$this->code=$data->code;
			$this->price=$data->price;
			$this->infinite=$data->infinite;
			$this->end_date=$data->end_date;
		}
		
		public function fillFromArray($data)
		{
			$this->id=$data["id"];
			$this->user_id=$data["user_id"];
			$this->upgraded=$data["upgraded"];
			$this->code=$data["code"];
			$this->price=$data["price"];
			$this->infinite=$data["infinite"];
			$this->end_date=$data["end_date"];
		}
		
		public function get_properties()
		{
			return array('id' => $this->id,
						 'user_id' => $this->user_id,
						 'upgraded' => $this->upgraded,
						 'code' => $this->code,
						 'price' => $this->price,
						 'infinite' => $this->infinite,
						 'end_date' => $this->end_date);
		}
	
		public function get_id(){	return $this->id; }
		public function set_id($id){	$this->id = $id; }
		public function get_user_id(){	return $this->user_id; }
		public function set_user_id($user_id){	$this->user_id = $user_id; }
		public function get_username()
		{
			$user = UserDataManager::instance(null)->retrieve_user($this->user_id);
			if(!is_null($user))
				return $user->get_name();
			else
				return Language::get_instance()->translate(1098);
		}
		public function get_code(){	return $this->code;	}
		public function set_code($code){	$this->code = $code;	}
		public function get_price(){	return $this->price;	}
		public function set_price($price){	$this->price = $price;	}
		public function get_infinite(){	return $this->infinite;	}
		public function set_infinite($infinite){	$this->infinite = $infinite;	}
		public function get_infinite_to_text() 
		{ 
			switch($this->infinite)
			{
				case 1: 
				case 2: return Language::get_instance()->translate(1231) . "/" . Language::get_instance()->translate(1230); break;
				case 3: return Language::get_instance()->translate(1233); break;
			}
		}
		public function get_end_date(){	return $this->end_date;	}
		public function set_end_date($end_date){	$this->end_date = $end_date;	}
		public function get_end_date_to_txt() { return date('d/m/Y', $this->end_date); }
		public function is_upgraded(){	return $this->upgraded==1;	}
		public function set_upgraded(){	$this->upgraded=1;	}
		public function get_upgraded_full() { return $this->upgraded?Language::get_instance()->translate(378):Language::get_instance()->translate(379); }
		
	}
	
?>