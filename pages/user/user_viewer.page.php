<?php

class UserViewer
{

	private $manager;
	private $user_id;
	
	function UserViewer($manager)
	{
		$this->manager = $manager;
		$this->user_id = Request::get("id");
	}

	public function get_html()
	{
		if(!is_null($this->manager->get_user()))
		{
			$children = $this->manager->get_data_manager()->retrieve_children($this->manager->get_user()->get_id());
			$is_child = false;
			foreach($children as $child)
			{
				if($child->get_id() == $this->user_id)
				{
					$is_child = true;
					break;
				}
			}
			if($this->manager->get_user()->is_admin() || $is_child)
			{
				$user = $this->manager->get_data_manager()->retrieve_user($this->user_id);
				if(!is_null($user))
					return $this->manager->get_renderer()->get_profile($user);
				else
					return "<p class='error'>" . Language::get_instance()->translate(486) . "</p>";
			}
			else
				return "<p class='error'>" . Language::get_instance()->translate(85) . "</p>";
		}
		return "<p class='error'>" . Language::get_instance()->translate(85) . "</p>";
	}
	
	public function get_title()
	{
		return "";
	}
	
	public function get_description()
	{
		return '<p style="vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(487) . '</p>';
	}
}
?>