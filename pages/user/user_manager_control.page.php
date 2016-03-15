<?php

class UserManagerControl
{
	private $id;
	private $manager;
	
	function UserManagerControl($manager)
	{
		$this->manager = $manager;
	}
	
	public function get_html()
	{	
		$html = array();
		if(RightManager::instance()->check_right_location(RightManager::WRITE_RIGHT, "user", $this->manager->get_user(), false, true) && GroupManager::group_is_not_test($this->manager->get_user()->get_group_id()))
		{
			$html[] = Display::get_message();
			$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'pages/user/javascript/user_manager.js"></script>';
			$html[] = '<div id="manage_div">';
			$html[] = $this->manager->get_renderer()->render_user_manager();
			$html[] = '</div>';
		}
		else
		{
			$html[] = '<p class="error">' . Language::get_instance()->translate(85) . '</p>';
		}
		return implode("\n", $html);
	}
	
	public function get_title()
	{
		return "";
	}
	
	public function get_description()
	{
		return '<p style="vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(485) . '</p>';
	}
}
?>