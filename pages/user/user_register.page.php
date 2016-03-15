<?php

class UserRegister
{
	private $manager;
	private $club = 1;
	private $registration_type;
	private $registration_choice = -1;
	private $page = 1;
	const ACCOUNT_IBAN = "BE60 7370 3936 5370";
	const ACCOUNT_BIC = "KREDBEBB";
	const ADDRESS_NAME= "Schaakschool VZW";
	const ADDRESS_STREET = "Boelare 129";
	const ADDRESS_CITY = "B-9900 Eeklo";
	const MAIL_ADMIN = "info@schaakschool.be";
	
	const COACH_FREE = 4;

	function UserRegister($manager)
	{
		$this->manager = $manager;
		if(isset($_GET["register_account"]))
			$this->registration_choice = 1;
		elseif(isset($_GET["get_course"]))
			$this->registration_choice = 0;
		elseif(isset($_GET["get_course_sos_fall_2015"]))
			$this->registration_choice = 2;
		else
			$this->registration_choice = -1;
		if(is_null($this->registration_choice) || !is_numeric($this->registration_choice))
			$this->registration_choice = -1;
		$this->page = Request::post("page");
		if(is_null($this->page) || !is_numeric($this->page))
			$this->page = 0;
	}
	
	public function get_html()
	{
		if(is_null($this->manager->get_user()) || $this->manager->get_user()->get_group_id() == GroupManager::GROUP_GUEST_ID)
		{
			//TODO Add Error class validation
			$html = array();
			$html[] = '<div id="register_container">';
			
			if($this->registration_choice === -1)
			{
				$html[] = "<h2 class='title'>" . Language::get_instance()->translate(1232) . "</h2>";
				$html[] = "<br/>";
				$html[] = "<a class='link_button' style='width: 300px;' href='" . Url::create_url(Array("page" => "register", "register_account" => "")) . "'>" . Language::get_instance()->translate(1380) . "</a>";
				$html[] = "<br class='clearfloat'/><br/>";
				$html[] = "<a class='link_button' style='width: 300px;' href='" . Url::create_url(Array("page" => "register", "get_course" => "")) . "'>" . Language::get_instance()->translate(1381) . "</a>";
				$html[] = "<br class='clearfloat'/><br/>";
				$html[] = "<a class='link_button' style='width: 300px;' href='" . Url::create_url(Array("page" => "register", "get_course_sos_fall_2015" => "")) . "'>" . Language::get_instance()->translate(1390) . "</a>";
			
			}
			elseif($this->registration_choice === 0 || $this->registration_choice === 2)
			{
				$request = null;

				$title = "";
				switch($this->registration_choice)
				{
					case 0: $title = Language::get_instance()->translate(1381); break;
					case 2: $title = Language::get_instance()->translate(1390); break;
				}
				
				if(!empty($_POST) && Request::post("request_form"))
				{
					$request = $this->manager->get_data_manager()->retrieve_club_registration_from_post(true);
					
					$mail = "";
					switch($this->registration_choice)
					{
						case 0: $mail = "request_course"; break;
						case 2: $mail = "request_course_sos_fall_2015"; break;
					}
					
					if(Error::get_instance()->get_result() && !Mail::check_language_mail($mail, $request->get_language()))
					{
						Error::get_instance()->set_result(false);
						Error::get_instance()->set_message(Language::get_instance()->translate(1384));
					}
				}
				if(!is_null($request) && Error::get_instance()->get_result() && Request::post("confirm_first"))
				{					
					$html[] = "<h2 class='title'>" . $title. "</h2>";
					$html[] = $this->manager->get_renderer()->get_forms_renderer()->get_course_request_form($request, true);
				}
				elseif(!is_null($request) && Error::get_instance()->get_result() && Request::post("confirmed"))
				{
					$success = $this->manager->get_data_manager()->insert_user_request_course($request);
					if($success)
					{
						$mail_title = "";
						$mail_success = "";
						switch($this->registration_choice)
						{
							case 0: $mail_title = Language::get_instance()->translate(1382);
									$mail_success = Language::get_instance()->translate(1383); break;
							case 2: $mail_title = Language::get_instance()->translate(1392);
									$mail_success = Language::get_instance()->translate(1391); break;
						}
						
						$custom = new CustomProperties();
						$custom->add_property("name", $request->get_firstname());
						$custom->add_property("fullname", $request->get_firstname() . " " .  $request->get_lastname());
						$custom->add_property("email", $request->get_email());
						Mail::mail_to_array($request->get_email(), strtolower($mail_title), $mail . "_" . strtolower($request->get_language()), $custom);
						//Mail::mail_to_array(self::MAIL_ADMIN, Language::get_instance()->translate(1382, "NL"), "request_course_admin", $custom);
						
						$html[] = "<h2 class='title'>" . $title . "</h2>";
						$html[] = $mail_success;
						
					}
					else
					{
						$html[] = "<h2 class='title'>" . Language::get_instance()->translate(1381) . "</h2>";
						$html[] = "<p class='error'>" . Language::get_instance()->translate(93) . "</p>";
					}
				}
				else
				{
					if(!is_null($request) && !Error::get_instance()->get_result())
					{
						$html[] = "<p class='error'>" . Error::get_instance()->get_message() . "</p>";
					}
					
					$html[] = "<h2 class='title'>" . $title . "</h2>";
					$html[] = $this->manager->get_renderer()->get_forms_renderer()->get_course_request_form($request, false);
				}
			}
			elseif($this->registration_choice === 1)
			{
				$organisation_id = null;
				$show_organisation_form = true;
				if(!empty($_POST))
				{
					$this->registration_type = Request::post("registration_type");
					if(is_null($this->registration_type) || !is_numeric($this->registration_type) || $this->registration_type < 1 || $this->registration_type > 3)
					{
						$this->page = 0;
					}
					else
					{
						$this->club = $this->registration_type==1?0:1;
						$this->page = 1;
					}
				}
	
				if($this->club)
				{
					if(!empty($_POST) && !is_null(Request::post('organisation_id')))
					{
						$organisation_id = Request::post('organisation_id');
						if($organisation_id>0)
							$show_organisation_form = false;
						if(is_numeric($organisation_id) && $organisation_id > 0 && $this->manager->get_data_manager()->organisation_in_use($organisation_id))
						{
							$this->page = 1;
							$html[] = "<p class='error'>" . Language::get_instance()->translate(1199) . "</p>";
						}
						else if(!is_null($organisation_id) && is_numeric($organisation_id) && $organisation_id >= 0)
							$this->page = 2;
						else
						{
							$this->page = 1;
							$html[] = "<p class='error'>" . Language::get_instance()->translate(1195) . "</p>";
						}
					}
					
					if($this->page == 0)
					{
						$html[] = "<h2 class='title'>" . Language::get_instance()->translate(1232) . "</h2>";
						$html[] = "<p style='font-style:italic;'>";
						$html[] = Language::get_instance()->translate(1287). "<br/>";
						$html[] = Language::get_instance()->translate(1257). "<br/><br/>";
						$html[] = Language::get_instance()->translate(1258). "<br/><br/>";
						$html[] = Language::get_instance()->translate(1259). "<br/>";
						$html[] = Language::get_instance()->translate(1260). "<br/>";
						$html[] = Language::get_instance()->translate(1288). "</p>";
						$html[] = $this->manager->get_renderer()->get_forms_renderer()->get_club_registration_type();
						
					}
					if($this->page == 1)
					{	
						$html[] = "<h2 class='title'>" . Language::get_instance()->translate(1118) . "</h2>";
						$html[] = "<p style='font-style:italic;'></p>";
						//dump($this->registration_type);
						$html[] = $this->manager->get_renderer()->get_forms_renderer()->get_club_selection_form($this->registration_type);
					}
					if($this->page == 2)
					{
						$new_registration = null;
						$organisation = null;
						if($organisation_id != 0)
							$organisation = $this->manager->get_data_manager()->retrieve_organisation($organisation_id);
						elseif(!empty($_POST) && Request::post("user_reg_form"))
							$organisation = $this->manager->get_data_manager()->retrieve_organisation_from_post();
						if(!empty($_POST) && Request::post("user_reg_form"))
						{
							$new_registration = $this->manager->get_data_manager()->retrieve_club_registration_from_post();
							$new_registration->set_organisation($organisation);
							$new_registration->set_organisation_id($organisation_id);
						}	
						if(!is_null($new_registration) && Error::get_instance()->get_result() && Request::post("confirm_first"))
						{
							$html[] = "<h2 class='title'>" . Language::get_instance()->translate(1118) . "</h2>";
							$html[] = "<p style='font-style:italic;'></p>";
							$html[] = $this->manager->get_renderer()->get_forms_renderer()->get_register_club_form($new_registration, true, $organisation, false, $this->registration_type);
						}
						elseif(!is_null($new_registration) && Error::get_instance()->get_result() && Request::post("confirmed") && $this->manager->get_data_manager()->insert_registration($new_registration))
						{
							$object = new CustomProperties();
							$object->add_property("name", $new_registration->get_lastname());
							//$object->add_property("price", $new_registration->get_price());
							/*$object->add_property("code", $new_registration->get_code());
							$object->add_property("account_iban", self::ACCOUNT_IBAN);
							$object->add_property("account_bic", self::ACCOUNT_BIC);
							$object->add_property("address_name", self::ADDRESS_NAME);
							$object->add_property("address_street", self::ADDRESS_STREET);
							$object->add_property("address_city", self::ADDRESS_CITY);
							*/
							Mail::mail_to_array($new_registration->get_email(), Language::get_instance()->translate(1240), "registration_club", $object);
							Mail::mail_to_array(self::MAIL_ADMIN, Language::get_instance()->translate(1239, "NL"), "registration_admin", $object);
							
							$html[] = "<h2 class='title'>" . Language::get_instance()->translate(1135) . "</h2>";
							$html[] = Language::get_instance()->translate(1240). "<br/><br/>";
							$html[] = Language::get_instance()->translate(1241) . "<br/>";
							$html[] = Language::get_instance()->translate(1242) . "<br/><br/>";
							/*
							$html[] = Language::get_instance()->translate(1214). "<br/>";
							$html[] = Language::get_instance()->translate(1215) . "<br/><br/>";
							
							$html[] = '<div class="record_name">' . Language::get_instance()->translate(1216) . "</div><div class='record_input'>" . Language::get_instance()->translate(1222) . " " . self::ACCOUNT_IBAN . "<br/></div>";
							$html[] = '<div class="record_name">&nbsp</div><div class="record_input">' . Language::get_instance()->translate(1217) . " " . self::ACCOUNT_BIC . "<br/></div><br class='cleafloat'/>"; 	
							$html[] = '<div class="record_name">' . Language::get_instance()->translate(1218) . "</div><div class='record_input' style='padding-left: 190px;'>" . self::ADDRESS_NAME . "<br/>" . self::ADDRESS_STREET . "</br>" . self::ADDRESS_CITY . "</div><br class='clearfloat'/></br>";
							$html[] = '<div class="record_name">' . Language::get_instance()->translate(1219) . "</div><div class='record_input'>" . $new_registration->get_price() . " EUR</div>";
							$html[] = '<div class="record_name">' . Language::get_instance()->translate(1220) . "</div><div class='record_input'>" . $new_registration->get_code() . "</div><br/>";
							
							$html[] = Language::get_instance()->translate(1237) . "&nbsp;" . Language::get_instance()->translate(1238) . "<br/>" . Language::get_instance()->translate(1221) . "<br/><br/>";
							$html[] = Language::get_instance()->translate(2) . "<br/><br/><br/><br/>";
							/*					
							$html[] = sprintf(Language::get_instance()->translate(1134), $new_registration->get_price(), self::ACCOUNT, $new_registration->get_code()) . "<br/>";
							$html[] = Language::get_instance()->translate(760) . "<br/><br/>";
							*/
							$html[] = Language::get_instance()->translate(2);
							
						}
						else
						{
							if(!is_null($new_registration) && !Error::get_instance()->get_result())
							{
								$html[] = "<p class='error'>" . Error::get_instance()->get_message() . "</p>";
							}
							
							$html[] = "<h2 class='title'>" . Language::get_instance()->translate(1118) . "</h2>";
							$html[] = "<p style='font-style:italic;'></p>";
							$html[] = $this->manager->get_renderer()->get_forms_renderer()->get_register_club_form($new_registration, false, $organisation, $show_organisation_form, $this->registration_type);
						}
					}
				}
				else 
				{
					$new_registration = null;
					if(!empty($_POST) && Request::post("user_reg_form"))
					{
						$new_registration = $this->manager->get_data_manager()->retrieve_club_registration_from_post();
					}	
					if(!is_null($new_registration) && Error::get_instance()->get_result() && Request::post("confirm_first"))
					{
						$html[] = "<h2 class='title'>" . Language::get_instance()->translate(1243) . "</h2>";
						$html[] = "<p style='font-style:italic;'></p>";
						$html[] = $this->manager->get_renderer()->get_forms_renderer()->get_register_user_form($new_registration, true);
					}
					elseif(!is_null($new_registration) && Error::get_instance()->get_result() && Request::post("confirmed") && $new_registration_id = $this->manager->get_data_manager()->insert_registration($new_registration))
					{	
						$user = new User();
						$user->set_parent_id(self::COACH_FREE);
						$user->set_username($new_registration->get_username());
						$user->set_firstname($new_registration->get_firstname());
						$user->set_lastname($new_registration->get_lastname());
						$user->set_email($new_registration->get_email());
						$user->set_password($new_registration->get_password());
						$user->set_language($new_registration->get_language());
						$user->set_sex($new_registration->get_sex());
						$user->set_credits(0);
						$user->set_avatar("");
						$user->set_activation_code($this->manager->get_data_manager()->retrieve_activation_code());
						$user->set_address("");
						$user->set_extra_parent_ids(Array());
						$user->set_group_id(GroupManager::GROUP_FREE_INDIVIDUAL_ID);
						
						$user_chess_profile = new UserChessProfile();
						$user_chess_profile->set_user_id(0);
						$user_chess_profile->set_rd(350);
						$user_chess_profile->set_rating($new_registration->get_rating());
						
						$user->set_chess_profile($user_chess_profile);
						$id = $this->manager->get_data_manager()->insert_user($user);
						if($id)
						{
							$user->set_id($id);
							$this->manager->get_data_manager()->update_created_club_registration($new_registration_id, 1);
							$this->manager->get_data_manager()->add_user_rights($user);
							$custom_properties = new CustomProperties();
							$custom_properties->add_property("name", $user->get_name());
							$custom_properties->add_property("username", $user->get_username());
							$custom_properties->add_property("activation_code", $user->get_activation_code());
							$mail = Mail::mail_to_array($user->get_email(), Language::get_instance()->translate(1244), 'registration_pupil', $custom_properties);
							Mail::mail_to_array(self::MAIL_ADMIN, Language::get_instance()->translate(1245, "NL"), "registration_pupil_admin", $custom_properties);
							//TODO: CHECK IF MAIL WAS SENT WITH $mail VAR
							$html[] = "<h2 class='title'>" . Language::get_instance()->translate(1135) . "</h2>";
							$html[] = Language::get_instance()->translate(1240). "<br/><br/>";
							$html[] = Language::get_instance()->translate(1241) . "<br/>";
							$html[] = Language::get_instance()->translate(1242) . "<br/><br/>";
							$html[] = Language::get_instance()->translate(2);
						}
						else
							$html[] = "<p class='error'>" . Language::get_instance()->translate(1130) . "</p>";	
					}
					else
					{
						if(!is_null($new_registration) && !Error::get_instance()->get_result())
						{
							$html[] = "<p class='error'>" . Error::get_instance()->get_message() . "</p>";
						}
						
						$html[] = "<h2 class='title'>" . Language::get_instance()->translate(1243) . "</h2>";
						$html[] = "<p style='font-style:italic;'></p>";
						$html[] = $this->manager->get_renderer()->get_forms_renderer()->get_register_user_form($new_registration, false);
					}
				}
			}
			$html[] = '</div>';
			//	dump(Error::get_instance());
			return implode("\n", $html);
		}
		elseif(is_null($this->manager->get_user()) && !$this->club)
		{
			$html = array();
			$new_user = null;
			$html[] = '<div id="register_container">';
			$html[] = '<div id="register_div">';
			if(!empty($_POST)) $new_user = $this->manager->get_data_manager()->retrieve_user_from_post(null);
			
			if(is_object($new_user) && get_class($new_user) == 'User' && $this->manager->register($new_user))
				$html[] = "<p class='good'>U bent ingeschreven. Gelieve te betalen en dan wordt uw account geactiveerd.</p>";
			else
			{
				if(is_object($new_user) && get_class($new_user) != 'User')
				{
					$html[] = $new_user;
					$new_user = null;
				}
				$html[] = "<h2>Inschrijven</h2>";
				$html[] = "<p style='font-style:italic;'></p>";
				$html[] = $this->manager->get_renderer()->get_forms_renderer()->get_form($new_user);
			}
			$html[] = '</div>';
			$html[] = '</div>';
			return implode("\n", $html);
		}
		else
		{
    		header("Location: " . Url :: create_url(array('page' => 'add_member')));  
			exit;
		}
	}
	
	public function get_title()
	{
		return "<h1>Registreren</h1>";
	}
	
	
	public function get_description()
	{
		return '<p style="vertical-align:top;font-style:italic;font-size:11px;">Vul de gevraagde gegevens juist in en verstuur ze naar ons door.</p>';
	}
}
?>