<?php

class UserCreator
{

	private $manager;
	private $parent_id;
	private $prev_page;
	const CLUB_TEST = 3;
	const COACH_TEST = 4;
	
	function UserCreator($manager)
	{
		$this->manager = $manager;
		$this->parent_id = Request::get("parent_id");
		$this->prev_page = Request::get("prev");
	}
	
	public function get_html()
	{
		$html = array();
		//CLUB_DELETES_COACH TRANSFER_COACH_RIGHTS_TO_COACH_THINGIE;
		$parent_user = $this->manager->get_user();
		if($this->parent_id)
			$parent_user = $this->manager->get_data_manager()->retrieve_user($this->parent_id);	
		$is_child = $this->manager->get_user()->is_child($parent_user->get_id());
		if($is_child && RightManager::instance()->check_right_location(RightManager::WRITE_RIGHT, "User", $parent_user))
		{
			$new_user = null;
			$success = false;
			
			$test_account = false;
			//dump($_POST);
			//exit;
			if(!empty($_POST))
			{
				$new_user = $this->manager->get_data_manager()->retrieve_user_from_post(null, $parent_user);
				//dump($new_user);
				//dump(Error::get_instance());
				if(Error::get_instance()->get_result())
				{
					if(!DataManager::parse_checkbox_value(Request::post("test_account")))
					{
						$success = $this->manager->register($new_user, $parent_user);
						if($success && DataManager::parse_checkbox_value(Request::post("send_email")))
						{
							$custom = new CustomProperties();
							$custom->add_property("name", $new_user->get_firstname());
							$custom->add_property("username", $new_user->get_username());
							$custom->add_property("password", Request::post("pwd"));
							Mail::mail_to_array($new_user->get_email(), Language::get_instance()->translate(903), "register", $custom);
						}
					}
					else
					{
						$test_account = true;
						$test_user = clone $new_user;
						switch(Request::post("test_group_id"))
						{
							case GroupManager::GROUP_COACH_TEST_ID:
									$custom = new CustomProperties();
									$custom->add_property("name", $new_user->get_firstname());
									$test_user->set_username($new_user->get_username() . "_coach_test");
									$test_user->set_parent_id(self::CLUB_TEST);
									$test_user->set_id(0);
									$test_user->set_group_id(GroupManager::GROUP_COACH_TEST_ID);
									$success = $this->manager->register($test_user);
									$custom->add_property("username1", $test_user->get_username());
									$custom->add_property("password1", Request::post("pwd"));
									
									//Les dubbelschaak
									RightManager::instance()->add_location_object_user_right(RightManager::LESSON_LOCATION_ID, $test_user->get_id(), 71, RightManager::READ_RIGHT);
									
									$success = true;
									if($success === true)
									{
										$test_user->set_username($new_user->get_username() . "_pupil_test");
										$test_user->set_parent_id($test_user->get_id());
										$test_user->set_id(0);
										$test_user->set_group_id(GroupManager::GROUP_PUPIL_TEST_ID);
										$success = $this->manager->register($test_user);
										$custom->add_property("username2", $test_user->get_username());
										$custom->add_property("password2", Request::post("pwd"));
									}
									if($success && DataManager::parse_checkbox_value(Request::post("send_email")))
										Mail::mail_to_array($new_user->get_email(), Language::get_instance()->translate(810), "register_coach", $custom);
									break;
							case GroupManager::GROUP_PUPIL_TEST_ID:
									$test_user->set_username($new_user->get_username() . "_pupil_test");
									$test_user->set_parent_id(self::COACH_TEST);
									$test_user->set_id(0);
									$test_user->set_group_id(GroupManager::GROUP_PUPIL_TEST_ID);
									$success = $this->manager->register($test_user);
									if($success && DataManager::parse_checkbox_value(Request::post("send_email")))
									{
										$custom = new CustomProperties();
										$custom->add_property("name", $test_user->get_firstname());
										$custom->add_property("username", $test_user->get_username());
										$custom->add_property("password", Request::post("pwd"));
										Mail::mail_to_array($test_user->get_email(), Language::get_instance()->translate(810), "register_pupil", $custom);
									}
									break;
							case GroupManager::GROUP_CLUB_TEST_ID:
									$custom = new CustomProperties();
									$custom->add_property("name", $new_user->get_firstname());
									$test_account = true;
									$test_user = clone $new_user;
									$test_user->set_username($new_user->get_username() . "_club_test");
									$test_user->set_parent_id(0);
									$test_user->set_group_id(GroupManager::GROUP_CLUB_TEST_ID);
									$success = $this->manager->register($test_user);
									$custom->add_property("username1", $test_user->get_username());
									$custom->add_property("password1", Request::post("pwd"));
									if($success === true)
									{
										$test_user->set_username($new_user->get_username() . "_coach_test");
										$test_user->set_parent_id($test_user->get_id());
										$test_user->set_id(0);
										$test_user->set_group_id(GroupManager::GROUP_COACH_TEST_ID);
										$success = $this->manager->register($test_user);
										$custom->add_property("username2", $test_user->get_username());
										$custom->add_property("password2", Request::post("pwd"));
										if($success === true)
										{
											$test_user->set_username($new_user->get_username() . "_pupil_test");
											$test_user->set_parent_id($test_user->get_id());
											$test_user->set_id(0);
											$test_user->set_group_id(GroupManager::GROUP_PUPIL_TEST_ID);
											$success = $this->manager->register($test_user);
											$custom->add_property("username3", $test_user->get_username());
											$custom->add_property("password3", Request::post("pwd"));
										}
									}
									if($success && DataManager::parse_checkbox_value(Request::post("send_email")))
										Mail::mail_to_array($new_user->get_email(), Language::get_instance()->translate(810), "register_club", $custom);
									break;
							default: break;
						}
					}
				}
			}
			else
			{
				if(!is_null(Request::get("request")) && is_numeric(Request::get("request")))
				{
					$request = $this->manager->get_data_manager()->retrieve_user_request(Request::get("request"));
					if(!is_null($request))
					{
						$new_user = new User();
						$new_user->set_username($request->get_firstname() . "_" . $request->get_lastname());
						$new_user->set_firstname($request->get_firstname());
						$new_user->set_lastname($request->get_lastname());
						$new_user->set_email($request->get_email());
						$new_user->set_language($request->get_language());
						$new_user->set_sex($request->get_sex());
						$new_user->set_activated();
						$new_user->set_group_id($request->get_accounttype(false)?GroupManager::GROUP_COACH_TEST_ID:GroupManager::GROUP_PUPIL_TEST_ID);
						$test_account = true;
					}
				}
			}
			
			if($success === true)
			{
				$page = "browse_members";
				if($this->prev_page == "manage")
					$page = "manage_members";
				header("Location: " . Url :: create_url(array("page" => $page, "message" => urlencode(Language::get_instance()->translate(475)), "message_type" => "good")));  
				exit;
			}
			else
			{
				if(!Error::get_instance()->get_result())
				{
					$html[] = Error::get_instance()->get_message();
				}
				if(is_string($success))
					$html[] = $success;
				//dump($new_user);
				//dump(Error::get_instance());
				$html[] = $this->manager->get_renderer()->get_forms_renderer()->get_form($new_user, $parent_user, $test_account);
			}
		}
		else
			$html[] = '<p class="error">' .  Language::get_instance()->translate(85) . '</p>';
		return implode("\n", $html);
	}
	
	public function get_title()
	{
		return "<h1>Gebruiker aanmaken</h1>";
	}
	
	public function get_description()
	{
		return '<p style="vertical-align:top;font-style:italic;font-size:11px;">' .  Language::get_instance()->translate(476) . '</p>';
	}
}
?>