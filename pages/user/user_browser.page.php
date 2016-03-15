<?php

class UserBrowser
{
	private $id;
	private $manager;
	
	function UserBrowser($manager)
	{
		$this->manager = $manager;
		$this->id = Request::get("id");
		if(is_null($this->id) || !is_numeric($this->id))
			$this->id = $this->manager->get_user()->get_id();
	}
	
	public function get_html()
	{	
		$html = array();	
		$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'pages/user/javascript/user_browser.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'core/javascript/editable.js"></script>';
		
		$html[] = '<div id="user_info">';
		//$html[] = $this->save_changes();
		
		if($this->manager->get_user()->is_admin() != 0)
		{
			$html[] = '<div id="tabs">';
			$html[] = '<ul width="95%">';
			if($this->id != 0)
			{
				$html[] = '<li><a href="#user_details">' .  Language::get_instance()->translate(74) . '</a></li>';
			}
			$html[] = '<li><a href="#general">' .  Language::get_instance()->translate(75) . '</a></li>';
			$html[] = '</ul>';
		}
		
		$html[] = Display::get_message();
		
		if($this->id != 0 && $this->manager->get_user()->is_child($this->id))
		{
			$user = $this->manager->get_data_manager()->retrieve_user($this->id);
			$html[] = '<div id="user_details">';
			if(!is_null($user))
				$html[] = $this->manager->get_renderer()->get_profile_html($user);
			else
				$html[] = "<p class='error'>" . Language::get_instance()->translate(486) . "</p>";
			$html[] = '</div>';
		}
		elseif($this->id != 0)
		{
			return "<p class='error'>" . Language::get_instance()->translate(85) . "</p>";
		}
		if($this->manager->get_user()->is_admin() != 0)
		{
			$html[] = '<div id="general">';
			//$html[] = $this->manager->get_renderer()->get_set_search();
			$html[] = '<div id="search_table">';
			$html[] = $this->manager->get_renderer()->get_users_table(RightManager::UPDATE_RIGHT);
			$html[] = '</div>';
			$html[] = '</div>';
			$html[] = '</div>';
			$html[] = '<script type="text/javascript">';
		    $html[] = '  var tabnumber = 0;';
		    $html[] = '  var user_id = ' . $this->id . ';';
		    $html[] = '</script>';
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
		return '<p style="vertical-align:top;font-style:italic;font-size:11px;">' .  Language::get_instance()->translate(474) . '</p>';
	}
}
?>