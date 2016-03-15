<?php
	
	class UserRequest extends UserAbstract
	{
		private $accounttype;
		private $message;

		public function UserRequest($data=null)
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
			parent::fillFromDatabas($data);
			$this->accounttype=$data->accounttype;
			$this->message=$data->message;
		}
		
		public function fillFromArray($data)
		{
			parent::fillFromArray($data);	
			$this->accounttype=$data["accounttype"];
			$this->message=$data["message"];
		}
		
		public function get_properties()
		{
			return array_merge(parent::get_properties(),
					array('accounttype' => $this->accounttype,
						  'message' => $this->message));
		}
		
		public function get_accounttype($text = true)
		{	
			if($text)
				return $this->accounttype?Language::get_instance()->translate(796):Language::get_instance()->translate(797);
			else
				return $this->accounttype;	
		}
		public function set_accounttype($accounttype){	$this->accounttype = $accounttype;	}
		public function get_message(){	return $this->message;	}
		public function set_message($message){	$this->message = $message;	}
		
	}
	
?>