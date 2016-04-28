<?php

require_once Path::get_path() . 'pages/client/lib/client.class.php';
require_once Path::get_path() . 'pages/client/lib/client_user_account.class.php';
require_once Path::get_path() . 'pages/client/lib/client_settings.class.php';

class ClientDataManager extends DataManager
{
	const TEMP_TABLE_NAME = 'temp_client';
	
	const USER_TABLE_NAME = 'client_user_account';
	const USER_CLASS_NAME = 'ClientUserAccount';
	
	const TABLE_NAME = 'client';
	const CLASS_NAME = 'Client';
	
	const SET_TABLE_NAME = 'client_settings';
	const SET_CLASS_NAME = 'ClientSettings';
	
	public static function instance()
	{
		parent::$_instance = new ClientDataManager();
		return parent::$_instance;
	}
	
	/** CLIENT USER ACCOUNT **/
	
	public function retrieve_client_user_account($id)
	{
		return parent::retrieve_by_id(self::USER_TABLE_NAME,self::USER_CLASS_NAME,$id);
	}
	
	public function retrieve_client_user_account_by_username($username)
	{
		$condition = "username = '" . $username . "'";
		return parent::retrieve(self::USER_TABLE_NAME,self::USER_CLASS_NAME,'',self::ONE_RECORD,$condition);
	}
	
	public function insert_temp_client_user_account($temp)
	{
		$properties = new CustomProperties();
		$properties->add_property("company_name", $temp->get_client()->get_company_name());
		$properties->add_property("contact_person", $temp->get_client()->get_contact_person());
		$properties->add_property("business_activity", $temp->get_client()->get_business_activity());
		$properties->add_property("street", $temp->get_client()->get_street());
		$properties->add_property("number", $temp->get_client()->get_number());
		$properties->add_property("postal_code", $temp->get_client()->get_postal_code());
		$properties->add_property("city", $temp->get_client()->get_city());
		$properties->add_property("country", $temp->get_client()->get_country());
		$properties->add_property("vat", $temp->get_client()->get_vat());
		$properties->add_property("email", $temp->get_client()->get_email());
		$properties->add_property("telephone", $temp->get_client()->get_telephone());
		$properties->add_property("gsm", $temp->get_client()->get_gsm());
		$properties->add_property("fax", $temp->get_client()->get_fax());
		$properties->add_property("receive_promotions", $temp->get_settings()->get_receive_promotions());
		$properties->add_property("send_catalogue", $temp->get_settings()->get_send_catalogue());
		$properties->add_property("create_webshop_account", $temp->get_settings()->get_create_webshop_account());
		return parent::insert(self::TEMP_TABLE_NAME, $properties);
	}
	
	public function retrieve_client_user_account_from_post()
	{
		$output = array();
		$tel = null;
		if(!is_null(Request::post("tel")) && Request::post("tel")!="")
		{
			$tel = preg_replace('/[\/.+-]/', "", Request::post("tel"));
		}
		
		$gsm = null;
		if(!is_null(Request::post("gsm")) && Request::post("gsm")!="")
		{
			$gsm = preg_replace('/[\/.+-]/', "", Request::post("gsm"));
		}
		
		$fax = null;
		if(!is_null(Request::post("fax")) && Request::post("fax")!="")
		{
			$fax = preg_replace('/[\/.+-]/', "", Request::post("fax"));
		}
		
		if( (!is_null($tel) && !is_numeric($tel)) ||
			(!is_null($gsm) && !is_numeric($gsm)) ||
			(!is_null($fax) && !is_numeric($fax)))
		{
			$output[] = Language::get_instance()->translate("fill_in_tel_correct_number") . "<br>";
		}
		
		if(	is_null(Request::post("company_name")) || Request::post("company_name")=="" ||
			is_null(Request::post("contact_person")) || Request::post("contact_person")=="" ||
			is_null(Request::post("business_activity")) || Request::post("business_activity")=="" ||
			is_null(Request::post("street")) || Request::post("street")=="" ||
			is_null(Request::post("number")) || Request::post("number")=="" || !is_numeric(Request::post("number")) ||
			is_null(Request::post("postal_code")) || Request::post("postal_code")=="" || !is_numeric(Request::post("postal_code")) ||
			is_null(Request::post("city")) || Request::post("city")=="" ||
			is_null(Request::post("country")) || Request::post("country")=="" || strlen(Request::post("country")) != 2 ||
			is_null(Request::post("vat")) || Request::post("vat")=="" ||
			is_null(Request::post("email")) || Request::post("email")=="" ||
			is_null($tel) ||
			is_null(Request::post("captcha_code")) || Request::post("captcha_code")=="" || Request::post("captcha_code") != strtoupper(Session::retrieve("captcha_code")))
		{
			$output[] = Language::get_instance()->translate("fill_in_required") . "<br>";
		}

		$client = new Client();
		$client->set_company_name(addslashes(Request::post("company_name")));
		$client->set_contact_person(addslashes(Request::post("contact_person")));
		$client->set_business_activity(addslashes(Request::post("business_activity")));
		$client->set_street(addslashes(Request::post("street")));
		$client->set_number(Request::post("number"));
		$client->set_postal_code(Request::post("postal_code"));
		$client->set_city(addslashes(Request::post("city")));
		$client->set_country(Request::post("country"));
		$client->set_vat(Request::post("vat"));
		$client->set_email(Request::post("email"));
		$client->set_telephone(Request::post("tel"));
		$client->set_gsm(Request::post("gsm"));
		$client->set_fax(Request::post("fax"));
		
		$client_settings = new ClientSettings();
		$client_settings->set_receive_promotions(self::parse_checkbox_value(Request::post("receive_promotions")));
		$client_settings->set_send_catalogue(self::parse_checkbox_value(Request::post("send_catalogue")));
		$client_settings->set_create_webshop_account(self::parse_checkbox_value(Request::post("create_webshop_account")));
		
		$client_user = new ClientUserAccount();
		$client_user->set_client($client);
		$client_user->set_settings($client_settings);
		
		return array("client_user_account" => $client_user, "error" => implode("\n", $output));
	}

}

?>