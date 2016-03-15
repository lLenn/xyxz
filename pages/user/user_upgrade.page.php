<?php

class UserUpgrade
{
	private $manager;
	const ACCOUNT_IBAN = "BE60 7370 3936 5370";
	const ACCOUNT_BIC = "KREDBEBB";
	const ADDRESS_NAME= "Schaakschool VZW";
	const ADDRESS_STREET = "Boelare 129";
	const ADDRESS_CITY = "B-9900 Eeklo";
	const MAIL_ADMIN = "info@schaakschool.be";
	
	const COACH_FREE = 4;

	function UserUpgrade($manager)
	{
		$this->manager = $manager;
		$this->page = Request::post("page");
		if(is_null($this->page) || !is_numeric($this->page))
			$this->page = 0;
	}
	
	public function get_html()
	{	
		$html = array();
		$html[] = '<div id="register_container">';
		$group_id = $this->manager->get_user()->get_group_id();
		if($group_id == GroupManager::GROUP_FREE_CLUB_ID || $group_id == GroupManager::GROUP_FREE_INDIVIDUAL_ID)
		{
			//TODO Add Error class validation
			$upgrade = $this->manager->get_data_manager()->retrieve_upgrade_by_user_id($this->manager->get_user()->get_id());
			if(is_null($upgrade))
			{
				if((!empty($_POST) && Request::post("user_reg_form")))
				{
					$upgrade = $this->manager->get_data_manager()->retrieve_club_upgrade_from_post();
				}	
				if(!is_null($upgrade) && Error::get_instance()->get_result() && Request::post("confirm_first"))
				{
					$html[] = "<h2 class='title'>" . Language::get_instance()->translate(1261) . "</h2>";
					$html[] = "<p style='font-style:italic;'></p>";
					$html[] = $this->manager->get_renderer()->get_forms_renderer()->get_upgrade_club_form($upgrade, true);
				}
				elseif(!is_null($upgrade) && Error::get_instance()->get_result() && Request::post("confirmed") && $this->manager->get_data_manager()->insert_upgrade($upgrade))
				{
					$object = new CustomProperties();
					$object->add_property("name", $this->manager->get_user()->get_lastname());
					$object->add_property("price", $upgrade->get_price());
					$object->add_property("code", $upgrade->get_code());
					$object->add_property("account_iban", self::ACCOUNT_IBAN);
					$object->add_property("account_bic", self::ACCOUNT_BIC);
					$object->add_property("address_name", self::ADDRESS_NAME);
					$object->add_property("address_street", self::ADDRESS_STREET);
					$object->add_property("address_city", self::ADDRESS_CITY);
	
					Mail::mail_to_array($this->manager->get_user()->get_email(), Language::get_instance()->translate(1236), "payement_club", $object);
					Mail::mail_to_array(self::MAIL_ADMIN, Language::get_instance()->translate(1239), "payement_admin", $object);
					
					$html[] = "<h2 class='title'>" . Language::get_instance()->translate(1135) . "</h2>";
					
					$html[] = Language::get_instance()->translate(1214). "<br/>";
					$html[] = Language::get_instance()->translate(1215) . "<br/><br/>";
					
					$html[] = '<div class="record_name">' . Language::get_instance()->translate(1216) . "</div><div class='record_input'>" . Language::get_instance()->translate(1222) . " " . self::ACCOUNT_IBAN . "<br/></div>";
					$html[] = '<div class="record_name">&nbsp</div><div class="record_input">' . Language::get_instance()->translate(1217) . " " . self::ACCOUNT_BIC . "<br/></div><br class='cleafloat'/>"; 	
					$html[] = '<div class="record_name">' . Language::get_instance()->translate(1218) . "</div><div class='record_input' style='padding-left: 190px;'>" . self::ADDRESS_NAME . "<br/>" . self::ADDRESS_STREET . "</br>" . self::ADDRESS_CITY . "</div><br class='clearfloat'/></br>";
					$html[] = '<div class="record_name">' . Language::get_instance()->translate(1219) . "</div><div class='record_input'>" . $upgrade->get_price() . " EUR</div>";
					$html[] = '<div class="record_name">' . Language::get_instance()->translate(1220) . "</div><div class='record_input'>" . $upgrade->get_code() . "</div><br/>";
					
					$html[] = Language::get_instance()->translate(1237) . "&nbsp;" . Language::get_instance()->translate(1238) . "<br/>" . Language::get_instance()->translate(1221) . "<br/><br/>";
					$html[] = Language::get_instance()->translate(2) . "<br/><br/><br/><br/>";
				}
				else
				{
					if(!is_null($upgrade) && !Error::get_instance()->get_result())
					{
						$html[] = "<p class='error'>" . Error::get_instance()->get_message() . "</p>";
						//$html[] = "<p class='error'>" . Error::get_instance()->get_debug_message() . "</p>";
					}
					
					$html[] = "<h2 class='title'>" . Language::get_instance()->translate(1249) . "</h2>";
					$html[] = "<p>";
					$html[] = Language::get_instance()->translate(1257). "<br/><br/>";
					$html[] = Language::get_instance()->translate(1258). "<br/><br/>";
					$html[] = Language::get_instance()->translate(1259). "<br/>";
					$html[] = Language::get_instance()->translate(1260). "<br/>";
					$html[] = Language::get_instance()->translate(1288). "</p>";
					$html[] = $this->manager->get_renderer()->get_forms_renderer()->get_upgrade_club_form($upgrade, false);
				}
			}
			else
			{
				$html[] = "<h2 class='title'>" . Language::get_instance()->translate(1135) . "</h2>";
					
				$html[] = Language::get_instance()->translate(1214). "<br/>";
				$html[] = Language::get_instance()->translate(1215) . "<br/><br/>";
				
				$html[] = '<div class="record_name">' . Language::get_instance()->translate(1216) . "</div><div class='record_input'>" . Language::get_instance()->translate(1222) . " " . self::ACCOUNT_IBAN . "<br/></div>";
				$html[] = '<div class="record_name">&nbsp</div><div class="record_input">' . Language::get_instance()->translate(1217) . " " . self::ACCOUNT_BIC . "<br/></div><br class='cleafloat'/>"; 	
				$html[] = '<div class="record_name">' . Language::get_instance()->translate(1218) . "</div><div class='record_input' style='padding-left: 190px;'>" . self::ADDRESS_NAME . "<br/>" . self::ADDRESS_STREET . "</br>" . self::ADDRESS_CITY . "</div><br class='clearfloat'/></br>";
				$html[] = '<div class="record_name">' . Language::get_instance()->translate(1219) . "</div><div class='record_input'>" . $upgrade->get_price() . " EUR</div>";
				$html[] = '<div class="record_name">' . Language::get_instance()->translate(1220) . "</div><div class='record_input'>" . $upgrade->get_code() . "</div><br/>";
				
				$html[] = Language::get_instance()->translate(1221) . "<br/><br/>";
				$html[] = Language::get_instance()->translate(2) . "<br/><br/><br/><br/>";
			
			}
		}
		$html[] = '</div>';
		return implode("\n", $html);
	}
	
	public function get_title()
	{
		return "";
	}
	
	
	public function get_description()
	{
		return '';
	}
}
?>