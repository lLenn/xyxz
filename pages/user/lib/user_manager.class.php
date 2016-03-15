<?php

require_once Path::get_path() . 'pages/user/lib/user_data_manager.class.php';
require_once Path::get_path() . 'pages/user/lib/user_renderer.class.php';

class UserManager
{
	const USER_LOGIN = "User_Login";
	const USER_LOGOUT = "User_Logout";
	const USER_VIEWER = "User_Viewer";
	const USER_CREATOR = "User_Creator";
	const USER_IMPORTER = "User_Importer";
	const USER_DELETOR = "User_Deletor";
	const USER_EDITOR = "User_Editor";
	const USER_BROWSER = "User_Browser";
	const USER_REGISTER = "User_Register";
	const USER_ACTIVATOR = "User_Activator";
	const USER_MANAGER_CONTROL = "User_Manager_Control";
	const USER_ACTIVATION_SENDER = "User_Activation_Sender";
	const USER_MEMBERS_BROWSER = "User_Members_Browser";
	const USER_REQUEST_BROWSER = "User_Request_Browser";
	const USER_CHANGER = "User_Changer";
	const USER_TRANSFER_CREDITS = "User_Transfer_Credits";
	const USER_PASSWORD_RESET = "User_Password_Reset";
	const USER_CLUB_REGISTRATION_BROWSER = "User_Club_Registration_Browser";
	const USER_CLUB_UPGRADE_BROWSER = "User_Club_Upgrade_Browser";
	const USER_UPGRADE = "User_Upgrade";

	private $user;
	private $renderer;
	
	function UserManager($user=null)
	{
		Language::get_instance()->add_section_to_translations(Language::USER);
		$this->user = $this->load_user($user);
		$this->renderer = new UserRenderer($this);
	}
	
	public function get_data_manager()
	{
		return UserDataManager::instance($this);
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
		switch($action)
		{
			case self::USER_LOGIN: 
				require_once("pages/user/user_login.page.php");
				return $this->action_object = new UserLogin($this);
				break;
			case self::USER_LOGOUT: 
				require_once("pages/user/user_logout.page.php");
				return $this->action_object = new UserLogout($this);
				break;
			case self::USER_VIEWER: 
				require_once("pages/user/user_viewer.page.php");
				return $this->action_object = new UserViewer($this);
				break;
			case self::USER_CREATOR: 
				require_once("pages/user/user_creator.page.php");
				return $this->action_object = new UserCreator($this);
				break;
			case self::USER_IMPORTER: 
				require_once("pages/user/user_importer.page.php");
				return $this->action_object = new UserImporter($this);
				break;
			case self::USER_DELETOR: 
				require_once("pages/user/user_deletor.page.php");
				return $this->action_object = new UserDeletor($this);
				break;
			case self::USER_EDITOR: 
				require_once("pages/user/user_editor.page.php");
				return $this->action_object = new UserEditor($this);
				break;
			case self::USER_BROWSER: 
				require_once("pages/user/user_browser.page.php");
				return $this->action_object = new UserBrowser($this);
				break;
			case self::USER_REGISTER: 
				require_once("pages/user/user_register.page.php");
				return $this->action_object = new UserRegister($this);
				break;
			case self::USER_ACTIVATOR: 
				require_once("pages/user/user_activator.page.php");
				return $this->action_object = new UserActivator($this);
				break;		
			case self::USER_ACTIVATION_SENDER: 
				require_once("pages/user/user_activation_sender.page.php");
				return $this->action_object = new UserActivationSender($this);
				break;	
			case self::USER_MEMBERS_BROWSER: 
				require_once("pages/user/user_members_browser.page.php");
				return $this->action_object = new UserMembersBrowser($this);
				break;
			case self::USER_REQUEST_BROWSER: 
				require_once("pages/user/user_request_browser.page.php");
				return $this->action_object = new UserRequestBrowser($this);
				break;
			case self::USER_MANAGER_CONTROL: 
				require_once("pages/user/user_manager_control.page.php");
				return $this->action_object = new UserManagerControl($this);
				break;
			case self::USER_CHANGER: 
				require_once("pages/user/user_changer.page.php");
				return $this->action_object = new UserChanger($this);
				break;
			case self::USER_PASSWORD_RESET: 
				require_once("pages/user/user_password_reset.page.php");
				return $this->action_object = new UserPasswordReset($this);
				break;
			case self::USER_CLUB_REGISTRATION_BROWSER: 
				require_once("pages/user/user_club_registration_browser.page.php");
				return $this->action_object = new UserClubRegistrationBrowser($this);
				break;
			case self::USER_CLUB_UPGRADE_BROWSER: 
				require_once("pages/user/user_club_upgrade_browser.page.php");
				return $this->action_object = new UserClubUpgradeBrowser($this);
				break;
			case self::USER_TRANSFER_CREDITS: 
				require_once("pages/user/user_transfer_credits.page.php");
				return $this->action_object = new UserTransferCredits($this);
				break;
			case self::USER_UPGRADE: 
				require_once("pages/user/user_upgrade.page.php");
				return $this->action_object = new UserUpgrade($this);
				break;
		}
	}
	
	public function login($username, $password = null)
	{
		$user = self::get_data_manager()->retrieve_active_user_by_username(addslashes($username));
		return self::authenticate_user($user, $username, $password);
	}
	
	public function send_activation_mail($headers="")
	{
		if($headers == "")
		{
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";			
			$headers .= 'From: Schaakschool <no-reply@schaakschool.be>' . "\r\n";
		}
		mail($this->user->getMailAddress(),"Activatie vereist op www.schaakschool.be","<p>".$this->user->getName().",<br>U bent succesvol ingeschreven op www.schaakschool.be.</p><p>Uw gebruikersnaam is <b>".$this->user->getUsername()."</b>.<p>Uw paswoord is <b>".(!is_null(Request::post('pwd'))?Request::post('pwd'):$this->user->getPassword())."</b>.</p><p>Om te bevestigen dat dit een correct e-mailadres is, dient u op volgende link te klikken:<br><a href=\"http://www.schaakschool.be/page-activate-".$this->user->getID()."-".$this->user->getActivationCode().".html\">http://www.schaakschool.be/page-activate-".$this->user->getID()."-".$this->user->getActivationCode().".html</a></p><p>met vriendelijke groeten,<br>De schaakschool</p>",$headers);		
	}
	
	
	public function register($new_user, $parent_user = null)
	{
		if(is_null($parent_user))
		{
			$parent_user = $this->get_user();
		}
		
		if($this->get_data_manager()->username_exists($new_user->get_username()))
		{
			return "<p class='error'>" . Language::get_instance()->translate(459) . "</p>";
		}
		
		if(!is_null($parent_user))
		{
			$new_user->set_activation_code(1);
		}
		else
		{
			$new_user->set_activation_code($this->generate_code(40));
		}
	
		$id = $this->get_data_manager()->insert_user($new_user);
		if(!$id)
		{
			return "<p class='error'>" . Language::get_instance()->translate(460) . "</p>";
		}
		
		$new_user->set_id($id);
		$this->get_data_manager()->add_user_rights($new_user);

		if(!is_null(Request::post("coach_pupils")) && $parent_user->get_group_id() == GroupManager::GROUP_CLUB_ID)
		{
			 $coaches = $this->get_data_manager()->retrieve_club_registration_coaches($parent_user->get_id(), true);
			 RightManager::instance()->set_allowed_objects_user(RightManager::USER_LOCATION_ID, $id, $coaches[Request::post("coach_pupils")]);
			 $this->get_data_manager()->update_club_registration_coaches_created_by_user_id($parent_user->get_id(), Request::post("coach_pupils"));
		}
		elseif(!is_null(Request::post("pupils")) && $parent_user->get_group_id() == GroupManager::GROUP_CLUB_ID)
		{
			$pupils = Request::post("pupils");
			RightManager::instance()->set_allowed_objects_user(RightManager::USER_LOCATION_ID, $id, $pupils);
			if($parent_user->get_credits()!=-1)
				$this->get_data_manager()->update_user_credits($parent_user->get_credits() - (RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "credits_coach") + ($pupils * RightDataManager::instance(null)->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "credits_pupil"))), $parent_user->get_id());
		}
		
		if(is_null($new_user->get_chess_profile()))
		{
			$user_chess_profile = new UserChessProfile();
			$user_chess_profile->set_user_id($id);
			$user_chess_profile->set_rd(350);
			$user_chess_profile->set_rating(1200);
			$this->get_data_manager()->insert_user_chess_profile($user_chess_profile);
		}
		
		/*
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		$headers .= 'From: MSV Eeklo <karel.boone@gmail.com>' . "\r\n";
		
		mail("karel.boone@gmail.com","Geregistreerde gebruiker op msveeklo.be: ".$new_user->getName(),"<p>Gebruiker: <a href='http://www.msveeklo.be/page-profile-$id.html'>".$new_user->getName()."</a></p><p>Bericht: ".Request::post("msg")."</p>",$headers);
		mail("tverheecke@hotmail.com","Geregistreerde gebruiker op msveeklo.be: ".$new_user->getName(),"<p>Gebruiker: <a href='http://www.msveeklo.be/page-profile-$id.html'>".$new_user->getName()."</a></p><p>Bericht: ".Request::post("msg")."</p>",$headers);
		mail("peter.dhondt3@pandora.be","Geregistreerde gebruiker op msveeklo.be: ".$new_user->getName(),"<p>Gebruiker: <a href='http://www.msveeklo.be/page-profile-$id.html'>".$new_user->getName()."</a></p><p>Bericht: ".Request::post("msg")."</p>",$headers);
		
		$this->user = $new_user;
		$this->sendActivationMail($headers,$id);
		*/
		return true;
	}
	
	public function authenticate_user($user, $username, $password)
	{
		if(is_null($user) || $user->get_username() != $username)
			return Language::get_instance()->translate(935);
		else if($user->get_password() != Hashing::hash($password))
			return Language::get_instance()->translate(936);
		else
			return $user;
		/*
		if ($user != null && $user->get_username() == $username && $user->get_password() == Hashing::hash($password))
        	return $user;
		else
			return 'Failed to log in user';
		*/
	}
	//--- PRIVATE FUNCTIONS --//
	
	//	Returns the user based on the input that was given.
	private function load_user($user)
	{
        if (isset($user))
        {
            if (is_object($user))
            {
                return $user;
            }
            else
            {
                if (! is_null($user))
                {
                    return ($this->retrieve_user($user));
                }
                else
                {
                    return null;
                }
            }
        }
    }
	
	private function retrieve_user($id)
	{
		return $this->get_data_manager()->retrieve_user($id);
	}
	
	public function generate_code($length)
	{
		$charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$ret="";
		for($i=0;$i<$length;$i++)
		{
			$ret .= $charset{rand(0,61)};
		}
		return $ret;
	}
	
	public function generate_ogm()
	{
		$charset = "0123456789";
		$ret="";
		for($i=0;$i<10;$i++)
		{
			$ret .= $charset{rand(0,9)};
		}
		$mod = fmod(floatval($ret), 97);
		if($mod == 0)
			$mod = 97;
		$ret = $ret . ($mod<10?"0".$mod:$mod);
		$ret = substr($ret, 0, 3) . "/" . substr($ret, 3, 4) . "/" . substr($ret, 7);
		return $ret;
	}

}

?>