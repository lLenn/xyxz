<?php

	class ClientUserAccount
	{
		private $id;
		private $username;
		private $password;
		private $client_number;
		private $admin;
		
		private $client = null;
		private $settings = null;

		public function ClientUserAccount($data=null)
		{
			if(!is_null($data))
				$this->fillFromDatabase($data);
		}
		
		public function fillFromDatabase($data)
		{
			$this->id=$data->id;
			$this->username=$data->username;
			$this->password=$data->password;
			$this->client_number=$data->client_number;
			$this->admin=$data->admin;
		}
		
		public function get_properties()
		{
			return array('id' => $this->id,
						 'username' => $this->username,
						 'password' => $this->password,
						 'client_number' => $this->client_number,
						 'admin' => $this->admin);
		}
	
		public function get_id(){	return $this->id; }
		public function set_id($id){	$this->id = $id; }
		public function set_username($username){	$this->username=$username;	}
		public function get_username(){	return $this->username;}
		public function get_password(){	return $this->password;}
		public function set_password($password){	$this->password = $password; }
		public function get_client_number(){ return $this->client_number; }
		public function set_client_number($client_number){	$this->client_number = $client_number; }
		public function is_admin(){	return $this->admin == 1; }
		public function set_admin($admin){ $this->admin = $admin; }
		public function get_client()
		{
			if(is_null($this->client))
			{
				$this->client = ClientDataManager::get_instance()->retrieve_client($this->client_number);
			}
			return $this->client;
		}
		public function set_client($client){ $this->client = $client; }
		public function get_settings()
		{
			if(is_null($this->settings))
			{
				$this->settings = ClientDataManager::get_instance()->retrieve_client_settings($this->client_number);
			}
			return $this->settings;
		}
		public function set_settings($settings){ $this->settings = $settings; }
		
	}
	
?>