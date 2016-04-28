<?php

	class ClientSettings
	{
		private $client_number;
		private $receive_promotions;
		private $send_catalogue;
		private $create_webshop_account;
		private $default_language;

		public function ClientSettings($data=null)
		{
			if(!is_null($data))
				$this->fillFromDatabase($data);
		}
		
		public function fillFromDatabase($data)
		{
			$this->user_id=$data->user_id;
			$this->receive_promotions=$data->receive_promotions;
			$this->send_catalogue=$data->send_catalogue;
			$this->create_webshop_account=$data->create_webshop_account;
			$this->default_language=$data->default_language;
		}
		
		public function get_properties()
		{
			return array('user_id' => $this->user_id,
						 'receive_promotions' => $this->receive_promotions,
						 'send_catalogue' => $this->send_catalogue,
						 'create_webshop_account' => $this->create_webshop_account,
						 'default_language' => $this->default_language);
		}
	
		public function get_user_id(){	return $this->user_id; }
		public function set_user_id($user_id){	$this->user_id = $user_id; }
		public function set_receive_promotions($receive_promotions){	$this->receive_promotions=$receive_promotions;	}
		public function get_receive_promotions(){	return $this->receive_promotions;}
		public function get_send_catalogue(){	return $this->send_catalogue;}
		public function set_send_catalogue($send_catalogue){	$this->send_catalogue = $send_catalogue; }
		public function get_create_webshop_account(){ return $this->create_webshop_account; }
		public function set_create_webshop_account($create_webshop_account){	$this->create_webshop_account = $create_webshop_account; }
		public function get_default_language(){ return $this->default_language; }
		public function set_default_language($default_language){	$this->default_language = $default_language; }
	}
	
?>