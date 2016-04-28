<?php

require_once Path::get_path() . 'pages/client/lib/client_data_manager.class.php';
require_once Path::get_path() . 'pages/client/lib/client_renderer.class.php';

class ClientManager
{
	const CLIENT_LOGIN = "Client_Login";
	const CLIENT_LOGOUT = "Client_Logout";
	const CLIENT_REGISTER = "Client_Register";
	const CLIENT_CORNER = "Client_Corner";

	private $user;
	private $renderer;
	
	function ClientManager($user = null)
	{
		$this->user = $user;
		$this->renderer = new ClientRenderer($this);
	}
	
	public function get_data_manager()
	{
		return ClientDataManager::instance();
	}
	
	public function get_renderer()
	{
		return $this->renderer;
	}
	
	public function get_user()
	{
		return $this->user;
	}
	
	public function factory($action)
	{
		Language::get_instance()->add_section_to_translations(Language::CLIENT);
		switch($action)
		{
			case self::CLIENT_LOGIN: 
				require_once("pages/client/client_login.page.php");
				return $this->action_object = new ClientLogin($this);
				break;
			case self::CLIENT_LOGOUT: 
				require_once("pages/client/client_logout.page.php");
				return $this->action_object = new ClientLogout($this);
				break;
			case self::CLIENT_REGISTER: 
				require_once("pages/client/client_register.page.php");
				return $this->action_object = new ClientRegister($this);
				break;
			case self::CLIENT_CORNER: 
				require_once("pages/client/client_corner.page.php");
				return $this->action_object = new ClientCorner($this);
				break;
		}
	}
	
	public function login($username, $password = null)
	{
		$user = self::get_data_manager()->retrieve_client_user_account_by_username($username);
		return self::authenticate_user_account($user, $username, $password);
	}

	
	public function authenticate_user_account($user, $username, $password)
	{
		if (!is_null($user) && $user->get_username() == $username && $user->get_password() == Hashing::hash($password))
        	return $user;
		else
			return new Exception('Failed to log in user');
	}
	
	public function send_registration_mail($new_client_user_account)
	{
		$mail_address = Setting::get_instance()->get_setting("send_registration_mail");
		
		$mail = strtoupper(substr($new_client_user_account->get_client()->get_company_name(),0,1)).substr($new_client_user_account->get_client()->get_company_name(),1). " wants to register:\n\n";
		$mail .= "Details: \n";
		$mail .= "Company name: ".$new_client_user_account->get_client()->get_company_name()."\n";
		$mail .= "Contact person: ".$new_client_user_account->get_client()->get_contact_person()."\n";
		$mail .= "Business activity: ".$new_client_user_account->get_client()->get_business_activity()."\n";
		$mail .= "Street: ".$new_client_user_account->get_client()->get_street()."\n";
		$mail .= "Nr: ".$new_client_user_account->get_client()->get_number()."\n";
		$mail .= "Postal code: ".$new_client_user_account->get_client()->get_postal_code()."\n";
		$mail .= "City: ".$new_client_user_account->get_client()->get_city()."\n";
		$mail .= "Country: ".$new_client_user_account->get_client()->get_country()."\n";
		$mail .= "VAT Number: ".$new_client_user_account->get_client()->get_vat()."\n";
		$mail .= "E-mail: ".$new_client_user_account->get_client()->get_email()."\n";
		$mail .= "Tel. nr.: ".$new_client_user_account->get_client()->get_telephone()."\n";
		$mail .= "Gsm. nr.: ".$new_client_user_account->get_client()->get_gsm()."\n";
		$mail .= "Fax. nr.: ".$new_client_user_account->get_client()->get_fax()."\n";
		$mail .= "Wants to receive promotions: ".($new_client_user_account->get_settings()->get_receive_promotions()?"Yes":"No")."\n";
		$mail .= "Wants to receive a catalogue: ".($new_client_user_account->get_settings()->get_send_catalogue()?"Yes":"No")."\n";
		$mail .= "Wants to receive a webshop account: ".($new_client_user_account->get_settings()->get_create_webshop_account()?"Yes":"No")."\n\n";
		
		$title_mail = strtoupper(substr($new_client_user_account->get_client()->get_company_name(),0,1)).substr($new_client_user_account->get_client()->get_company_name(),1). " wants to register.";
		
		if(mail($mail_address, $title_mail,$mail))
		{		
			return true;
		}
		
		return false;
	}
}

?>