<?php

class ClientForms
{

	private $manager;
	
	function ClientForms($manager)
	{
		$this->manager = $manager;
	}

	public static function get_login_form()
	{
		$html = array();
		$html[] = '<div id="login_div">';
		$html[] = '<form id="login_form" action="" method="post">';
		$html[] = '<div class="record">';
		$html[] = '<div class="record_name_required">' . Language :: get_instance()->translate("form_username") . ' :</div><div class="record_input"><input type="text" name="login" value="'. Cookie::retrieve(Cookie::LOGIN) .'"/></div><br class="clear_float">';
		$html[] = '<div class="record_name_required">' . Language :: get_instance()->translate("form_password") . ' :</div><div class="record_input"><input type="password" name="password" value="'. Cookie::retrieve(Cookie::PWD) .'"/></div>';
		$html[] = '<br class="clear_float"/>';
		$str = '<div class="record_name"><input class="checkbox" type="checkbox" name="save" value="1" ';
		if(Cookie::is_set(Cookie::LOGIN) && Cookie::is_set(Cookie::PWD)) $str .= "CHECKED";	
		$html[] = $str . '/> ' . Language :: get_instance()->translate("form_save") . '</div>';
		$html[] = '<br class="clear_float"/>';
		$html[] = '<div class="record_button"><a id="submit_form" class="link_button" href="javascript:;">' . Language :: get_instance()->translate("login") . '</a></div>';
		$html[] = '</div>';
		$html[] = '</form>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
	
	public function get_form()
	{
		$html = array();
		$html[] = '<div id="register_div">';
		$html[] = '<form id="register_form" action="" method="post">';
		$html[] = '<div class="record">';
		$html[] = '<div class="record_name_required">' . Language :: get_instance()->translate("company_name") . ' :</div><div class="record_input" style="width: 180px;"><input type="text" name="company_name" size="25" value="' . (!is_null(Request::post("company_name"))?Request::post("company_name"):'') . '"></div>';
		$html[] = '<div class="record_name_required">' . Language :: get_instance()->translate("contact_person") . ' :</div><div class="record_input"><input type="text" name="contact_person" size="25" value="' . (!is_null(Request::post("contact_person"))?Request::post("contact_person"):'') . '"></div><br class="clear_float"/>';
		$html[] = '<div class="record_name_required">' . Language :: get_instance()->translate("business_activity") . ' :</div><div class="record_input" style="width: 180px;"><input type="text" name="business_activity" size="25" value="' . (!is_null(Request::post("business_activity"))?Request::post("business_activity"):'') . '"></div>';
		$html[] = '<div class="record_name_required">' . Language :: get_instance()->translate("street") . ' :</div><div class="record_input"><input type="text" name="street" size="25" value="' . (!is_null(Request::post("street"))?Request::post("street"):'') . '"></div><br class="clear_float"/>';
		$html[] = '<div class="record_name_required">' . Language :: get_instance()->translate("number") . ' :</div><div class="record_input" style="width: 180px;"><input type="text" name="number" size="6" value="' . (!is_null(Request::post("number"))?Request::post("number"):'') . '"></div>';
		$html[] = '<div class="record_name_required">' . Language :: get_instance()->translate("postal_code") . ' :</div><div class="record_input"><input type="text" name="postal_code" size="25" value="' . (!is_null(Request::post("postal_code"))?Request::post("postal_code"):'') . '"></div><br class="clear_float"/>';
		$html[] = '<div class="record_name_required">' . Language :: get_instance()->translate("city") . ' :</div><div class="record_input" style="width: 180px;"><input type="text" name="city" size="25" value="' . (!is_null(Request::post("city"))?Request::post("city"):'') . '"></div>';
		$selected_country = "FR";
    	if(!is_null(Request::post('country')))
    	{
        	$selected_country = Request::post('country');
    	}
		$html[] = '<div class="record_name_required">' . Language :: get_instance()->translate("country") . ' :</div><div class="record_input">' . Country::render_country_select('country', $selected_country) . '</div><br class="clear_float"/>';
		$html[] = '<div class="record_name_required">' . Language :: get_instance()->translate("vat") . ' :</div><div class="record_input" style="width: 180px;"><input type="text" name="vat" size="25" value="' . (!is_null(Request::post("vat"))?Request::post("vat"):'') . '"></div>';
		$html[] = '<div class="record_name_required">' . Language :: get_instance()->translate("email") . ' :</div><div class="record_input"><input type="text" name="email" size="25" value="' . (!is_null(Request::post("email"))?Request::post("email"):'') . '"></div><br class="clear_float"/>';
		$html[] = '<div class="record_name_required">' . Language :: get_instance()->translate("tel") . ' :</div><div class="record_input" style="width: 180px;"><input type="text" name="tel" size="25" value="' . (!is_null(Request::post("tel"))?Request::post("tel"):'') . '"></div>';
		$html[] = '<div class="record_name">' . Language :: get_instance()->translate("gsm") . ' :</div><div class="record_input"><input type="text" name="gsm" size="25" value="' . (!is_null(Request::post("gsm"))?Request::post("gsm"):'') . '"></div><br class="clear_float"/>';
		$html[] = '<div class="record_name">' . Language :: get_instance()->translate("fax") . ' :</div><div class="record_input" style="width: 180px;"><input type="text" name="fax" size="25" value="' . (!is_null(Request::post("fax"))?Request::post("fax"):'') . '"></div><br class="clear_float"/>';
		$html[] = '<div style="float: left; width: 355px">';
		$html[] = '<div class="record_name_question">' . Language :: get_instance()->translate("receive_promotions") . '</div><div class="record_input"><input class="checkbox" type="checkbox" name="receive_promotions" size="25"'. (DataManager::parse_checkbox_value(Request::post("receive_promotions"))?'checked="checked"':'') . '"></div><br class="clear_float"/>';
		$html[] = '<div class="record_name_question">' . Language :: get_instance()->translate("send_catalogue") . '</div><div class="record_input"><input class="checkbox" type="checkbox" name="send_catalogue" size="25"' . (DataManager::parse_checkbox_value(Request::post("send_catalogue"))?'checked="checked"':'') . '"></div><br class="clear_float"/>';
		$html[] = '<div class="record_name_question">' . Language :: get_instance()->translate("create_webshop_account") . '</div><div class="record_input"><input class="checkbox" type="checkbox" name="create_webshop_account" size="25"' . (DataManager::parse_checkbox_value(Request::post("create_webshop_account"))?'checked="checked"':'') . '"></div><br class="clear_float"/>';
		$html[] = '</div>';
		$html[] = '<div style="float: left">';
		$html[] = '<div class="record_name_required">' . Language :: get_instance()->translate("copy_code") . ' :</div><div class="record_input"><img src="' . Path::get_url_path() . 'core/lib/captcha.php" alt=""></div><br class="clear_float"/>';
		$html[] = '<div class="record_name"></div><div class="record_input"><p class="feedback">' . Language :: get_instance()->translate("captcha_caps") . '</p></div><br class="clear_float"/>';
		$html[] = '<div class="record_name"></div><div class="record_input"><input type="text" name="captcha_code" size="6"></div><br class="clear_float"/>';
		$html[] = '</div>';
		$html[] = '<br class="clear_float">';
		$html[] = '<br>';
		$html[] = '<div class="record_button_aligned"><a id="submit_form" class="link_button" href="javascript:;">' . Language :: get_instance()->translate("form_submit") . '</a></div><br class="clear_float"/>';
		$html[] = '</div>';
		$html[] = '</form>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
}


?>
