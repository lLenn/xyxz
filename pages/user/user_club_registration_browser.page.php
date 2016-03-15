<?php

class UserClubRegistrationBrowser
{
	private $manager;
	private $add_id;
		
	function UserClubRegistrationBrowser($manager)
	{
		$this->manager = $manager;
		$this->add_id = Request::get("add_id");
		if(is_null($this->add_id) || !is_numeric($this->add_id))
			$this->add_id = 0;
	}
	
	public function save_changes()
	{
		
	}
	
	public function get_html()
	{	
		$html = array();
		if(!is_null($this->manager->get_user()) && $this->manager->get_user()->is_admin() && $this->add_id == 0)
		{
			$html[] = '<p><h4 class="title">' . Language::get_instance()->translate(1126) . '</h4></p>';
			$html[] = $this->manager->get_renderer()->get_club_registration_table();
		}
		elseif(!is_null($this->manager->get_user()) && $this->manager->get_user()->is_admin() && $this->add_id)
		{
			$club_registration = $this->manager->get_data_manager()->retrieve_club_registration($this->add_id);
			if($_POST && Request::post("accept") && !is_null($club_registration))
			{
				$user = new User();
				$user->set_parent_id(0);
				$user->set_username($club_registration->get_username());
				$user->set_firstname($club_registration->get_firstname());
				$user->set_lastname($club_registration->get_lastname());
				$user->set_email($club_registration->get_email());
				$user->set_password($club_registration->get_password());
				$user->set_language($club_registration->get_language());
				$user->set_sex($club_registration->get_sex());
				//$user->set_credits($club_registration->get_infinite()?-1:0);
				$user->set_credits(-1);
				$user->set_avatar("");
				$user->set_activation_code(1);
				$user->set_address("");
				$user->set_group_id(GroupManager::GROUP_FREE_CLUB_ID);
				
				$user_chess_profile = new UserChessProfile();
				$user_chess_profile->set_user_id(0);
				$user_chess_profile->set_rd(350);
				$user_chess_profile->set_rating($club_registration->get_rating());
				
				$user->set_chess_profile($user_chess_profile);
				$id = $this->manager->get_data_manager()->insert_user($user);
				if($id)
				{
					$this->manager->get_data_manager()->update_created_club_registration($club_registration->get_id(), 1);
					RightManager::instance()->add_location_object_user_right(RightManager::USER_LOCATION_ID, $id, $id, RightManager::UPDATE_RIGHT);
					//$allowed_objects = ($club_registration->get_infinite()==1?-1:count($club_registration->get_coaches()));
					$allowed_objects = -1;
					RightManager::instance()->set_allowed_objects_user(RightManager::USER_LOCATION_ID, $id, $allowed_objects);
					//$this->manager->get_data_manager()->update_club_registration_coaches_user_id($club_registration->get_id(), $id);
					$custom_properties = new CustomProperties();
					$custom_properties->add_property("name", $user->get_name());
					$custom_properties->add_property("username", $user->get_username());
					$mail = Mail::mail_to_array($user->get_email(), Language::get_instance()->translate(1240), 'registration_club_accepted', $custom_properties);
					if($mail)
						$html[] = "<p class='good'>" . Language::get_instance()->translate(1131) . "</p>";
					else
					{
						$html[] = "<p class='good'>" . Language::get_instance()->translate(1129) . "</p>";
						$html[] = "<p class='error'>" . Language::get_instance()->translate(1132) . "</p>";
					}
				}
				else
					$html[] = "<p class='error'>" . Language::get_instance()->translate(1130) . "</p>";
			}
			elseif($_POST || is_null($club_registration))
			{
	    		header("Location: " . Url :: create_url(array('page' => 'browse_club_registrations')));  
				exit;
			}
			else
			{
				$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'pages/user/javascript/club_creation_form.js" ></script>';
				$html[] = '<p><h4 class="medium_title">' . sprintf(Language::get_instance()->translate(1128), $club_registration->get_firstname() . " " . $club_registration->get_lastname()) . '</h4></p>';
				$html[] = '<form id="club_accept_form" action="" method="post">';
				$html[] = '<div class="record">';
				$html[] = '<input type="hidden" id="accept_hidden" name="accept" value="0"/>';
				$html[] = '<div class="record_button">';
				$html[] = '<a id="add_club" class="link_button" href="javascript:;">'.Language::get_instance()->translate(1127).'</a>';
				$html[] = '</div>';
				$html[] = '<div class="record_button">';
				$html[] = '<a id="submit_form" class="link_button" href="javascript:;">'.Language::get_instance()->translate(1024).'</a>';
				$html[] = '</div>';
				$html[] = '</div>';
				$html[] = '</form>';
			}
		}
		else
			$html[] = "<p class='error'>" . Language::get_instance()->translate(85) . "</p>";
		//dump(Error::get_instance());
		return implode("\n", $html);
	}
	
	public function get_title()
	{
		return '';
	}
	
	
	public function get_description()
	{
		return '';
	}
}
?>