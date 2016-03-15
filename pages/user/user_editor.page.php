<?php

class UserEditor
{

	private $manager;
	private $user_id;
	private $prev_page;

	function UserEditor($manager)
	{
		$this->manager = $manager;
		$this->user_id = Request::get("id");
		$this->prev_page = Request::get("prev");
	}
	
	public function get_html()
	{
		$html = array();
		if(RightManager::instance()->get_right_location_object("user", $this->manager->get_user(), $this->user_id) >= RightManager::UPDATE_RIGHT)
		{
			$user = $this->manager->get_data_manager()->retrieve_user($this->user_id);
			if(is_null($user))
			{
				header("Location: " . Url :: create_url(array("page" => "browse_members", "message" => urlencode(Language::get_instance()->translate(479)), "message_type" => "error")));  
				exit;
			}
			else
			{	
				$parent_user = $this->manager->get_user();
				if($user->get_parent_id() && !$parent_user->is_admin())
				{
					$parent_user = $this->manager->get_data_manager()->retrieve_user($user->get_parent_id());
				}
				
				$html[] = $this->save_changes($user, $parent_user);
				$html[] = "<h2 class='title'>" . Language::get_instance()->translate(468) . "</h2>";
				$html[] = $this->manager->get_renderer()->get_forms_renderer()->get_form($this->manager->get_data_manager()->retrieve_user($user->get_id()), $parent_user);
			}
		}
		else
		{
			$html[] = '<p class="error">' . Language::get_instance()->translate(85) . '</p>';
		}
		return implode("\n", $html);
	}
	
	public function save_changes($user, $parent_user)
	{
		$html = array();
		$message = "";
		$message_type = "error";
		
		if(!empty($_POST))
		{
			$manager = new UserManager($user);
			$check_user = $manager->login($user->get_username(), Request::post('old_pwd'));		
			$authentication = is_object($check_user) && get_class($check_user) == "User";
			if(($authentication && Request::post("pwd")==Request::post("rep_pwd")) || ((Request::post("old_pwd")=="" || $authentication) && Request::post("pwd")=="" && Request::post("rep_pwd")==""))
			{
				//$mail_validation = Request::post("email")!=$user->get_email();
				$parent_id = $user->get_parent_id();
				$user = $this->manager->get_data_manager()->retrieve_user_from_post($user, $parent_user);
				if(Error::get_instance()->get_result())
				{
					$success = $this->manager->get_data_manager()->update_user($user);
					if($success)
					{
						/*
						if(Request::post("group_id"))
						{
							$group_manager = new GroupManager($this->manager->get_user());
							$data_manager = $group_manager->get_data_manager();
							$group_user_relation = new GroupUserRelation();
							$group_user_relation->set_user_id($user->get_id());
							$group_user_relation->set_group_id(Request::post("group_id"));
							$data_manager->delete_group_user_relations_by_user_id($user->get_id());
							$data_manager->insert_group_user_relation($group_user_relation);
						}
						*/
						if($user->get_parent_id()!=$parent_id)
						{
							$this->manager->get_data_manager()->add_user_rights($user);
						}
						if($user->get_id()==$this->manager->get_user()->get_id())
						{
							Session::register('language', $user->get_language());
							Language::get_instance()->set_language($user->get_language());
							Language::get_instance()->add_section_to_translations(Language::USER);
						}
						/*
						if($mail_validation)
						{
						 	$user->set_activation_code(User::generate_code(40));
							$manager = new UserManager($user);
							$manager->send_activation_mail();
							$message .= "Een activatiemail is gestuurd naar uw e-mailadres.<br>";
						}
						*/
						$message .= urlencode(Language::get_instance()->translate(480));
						$message_type = "good";
					}
					else
						$html[] = "<p class='error'>" . Language::get_instance()->translate(481) . "</p>";
				}
				else
					$html[] = Error::get_instance()->get_message();
			}
			else
			{
				return "<p class='error'>" . Language::get_instance()->translate(482) . "</p>";
			}
		}
		if($message_type == "good")
		{
			$arr = array("message" => $message, "message_type" => $message_type);
			if($this->prev_page == "manage")
			{
				$arr["page"] = "manage_members";
			}
			else
			{
				$arr["page"] = "browse_members";
				$arr["id"] = $this->user_id;
			}
			if($user->get_parent_id())
			{
				$arr["parent_id"] = $user->get_parent_id();
			}
    		header("Location: " . Url :: create_url($arr));  
			exit;
		}
		return implode("\n", $html);
	}
	
	public function get_title()
	{
		return "";
	}
	
	
	public function get_description()
	{
		return '<p style="vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(483) . '</p>';
	}
}
?>