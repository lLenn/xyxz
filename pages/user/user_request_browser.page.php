<?php

class UserRequestBrowser
{
	private $id;
	private $manager;
	
	function UserRequestBrowser($manager)
	{
		$this->manager = $manager;
		$this->id = Request::get("id");
		if(is_null($this->id) || !is_numeric($this->id))
			$this->id = $this->manager->get_user()->get_id();
	}
	
	private function save_changes()
	{
		$html = array();
		if(!empty($_POST))
		{
			if(!is_null(Request::post('save_all_request_table')))
			{
				$status = true;
				$filter = array();
				$count = $this->manager->get_data_manager()->count_user_requests();
				for($i=1; $i<=$count; $i++)
				{
					if(!is_null(Request::post("id_" . $i)))
						$filter[] = Request::post("id_" . $i);
				}
				$status &= $this->manager->get_data_manager()->delete_other_user_requests($filter);
				if($status) $html[] = '<p class="good">' . Language::get_instance()->translate(480) . '</p>';
				else		$html[] = '<p class="error">' . Language::get_instance()->translate(481) . '</p>';
			}
		}
		return implode("\n", $html);
	}
	
	public function get_html()
	{	
		$html = array();
		if($this->manager->get_user()->is_admin())
		{
			$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'core/javascript/editable.js"></script>';
			$html[] = '<div id="user_info">';
			$html[] = $this->save_changes();
			$html[] = '<p class="title">' . Language::get_instance()->translate(762) . "</p>";
			$html[] = $this->manager->get_renderer()->get_user_requests_table();
			$html[] = '</div>';
		}
		else
		{
			$html[] = '<p class="error">' . Language::get_instance()->translate(85) . '</p>';
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