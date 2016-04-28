<?php

	class Client
	{
		private $client_number;
		private $company_name;
		private $contact_person;
		private $vat;
		private $business_activity;
		private $street;
		private $number;
		private $postal_code;
		private $city;
		private $country;
		private $telephone;
		private $gsm;
		private $fax;
		private $email;

		public function Client($data=null)
		{
			if(!is_null($data))
            {
				$this->fillFromDatabase($data);
            }
		}
		
		public function fillFromDatabase($data)
		{
			$this->client_number=$data->client_number;
			$this->company_name=$data->company_name;
			$this->contact_person=$data->contact_person;
			$this->vat=$data->vat;
			$this->business_activity=$data->business_activity;
			$this->street=$data->street;
			$this->number=$data->number;
			$this->postal_code=$data->postal_code;
			$this->city=$data->city;
			$this->country=$data->country;
			$this->telephone=$data->telephone;
			$this->gsm=$data->gsm;
			$this->fax=$data->fax;
			$this->email=$data->email;
		}
		
		public function get_properties()
		{
			return array('client_number' => $this->client_number,
						 'company_name' => $this->company_name,
						 'contact_person' => $this->contact_person,
						 'vat' => $this->vat,
						 'business_activity' => $this->business_activity,
						 'street' => $this->street,
						 'number' => $this->number,
						 'postal_code' => $this->postal_code,
						 'city' => $this->city,
						 'country' => $this->country,
						 'telephone' => $this->telephone,
						 'gsm' => $this->gsm,
						 'fax' => $this->fax,
						 'email' => $this->email);
		}
	
		public function get_client_number(){	return $this->client_number; }
		public function set_client_number($client_number){	$this->client_number = $client_number; }
		public function set_company_name($company_name){	$this->company_name=$company_name;	}
		public function get_company_name(){	return $this->company_name;}
		public function get_contact_person(){ return $this->contact_person; }
		public function set_contact_person($contact_person){ $this->contact_person = $contact_person; }
		public function get_vat(){	return $this->vat;	}
		public function set_vat($vat){	$this->vat = $vat;	}
		public function get_business_activity(){	return $this->business_activity;	}
		public function set_business_activity($business_activity){ $this->business_activity = $business_activity; }
		public function get_street(){ return $this->street; }
		public function set_street($street){ $this->street = $street; }
		public function get_number(){ return $this->number;	}
		public function set_number($number){ $this->number = $number; }
		public function get_postal_code(){	return $this->postal_code;	}
		public function set_postal_code($postal_code){	$this->postal_code = $postal_code;	}
		public function get_city(){	return $this->city;	}
		public function set_city($city){ $this->city = $city; }
		public function get_country(){ return $this->country; }
		public function set_country($country){ $this->country = $country; }
		public function get_telephone(){ return $this->telephone; }
		public function set_telephone($telephone){ $this->telephone = $telephone; }
		public function get_gsm(){ return $this->gsm; }
		public function set_gsm($gsm){ $this->gsm = $gsm; }
		public function get_fax(){ return $this->fax; }
		public function set_fax($fax){ $this->fax = $fax; }
		public function get_email(){ return $this->email; }
		public function set_email($email){ $this->email = $email; }
	}
	
?>