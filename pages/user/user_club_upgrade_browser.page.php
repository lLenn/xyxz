<?php

class UserClubUpgradeBrowser
{
	private $manager;
	private $add_id;
		
	function UserClubUpgradeBrowser($manager)
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
			$html[] = '<p><h4 class="title">' . Language::get_instance()->translate(1250) . '</h4></p>';
			$html[] = $this->manager->get_renderer()->get_club_upgrade_table();
		}
		elseif(!is_null($this->manager->get_user()) && $this->manager->get_user()->is_admin() && $this->add_id)
		{
			$upgrade = $this->manager->get_data_manager()->retrieve_upgrade($this->add_id);
			if($_POST && Request::post("accept") && !is_null($upgrade))
			{
				$user = UserDataManager::instance(null)->retrieve_user($upgrade->get_user_id());
				$this->manager->get_data_manager()->upgrade_account($upgrade, 1);
				$custom_properties = new CustomProperties();
				$custom_properties->add_property("name", $user->get_name());
				$custom_properties->add_property("username", $user->get_username());
				$mail = Mail::mail_to_array($user->get_email(), Language::get_instance()->translate(1133), 'received_payement', $custom_properties);
				if($mail)
					$html[] = "<p class='good'>" . Language::get_instance()->translate(1131) . "</p>";
				else
				{
					$html[] = "<p class='good'>" . Language::get_instance()->translate(1129) . "</p>";
					$html[] = "<p class='error'>" . Language::get_instance()->translate(1132) . "</p>";
				}
			}
			elseif($_POST || is_null($upgrade) || $upgrade->is_upgraded())
			{
	    		header("Location: " . Url :: create_url(array('page' => 'browse_club_upgrades')));  
				exit;
			}
			else
			{
				$user = UserDataManager::instance(null)->retrieve_user($upgrade->get_user_id());
				$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'pages/user/javascript/club_creation_form.js" ></script>';
				$html[] = '<p><h4 class="medium_title">' . sprintf(Language::get_instance()->translate(1251), $user->get_firstname() . " " . $user->get_lastname()) . '</h4></p>';
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