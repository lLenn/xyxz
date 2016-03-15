<?php

class DifficultyBrowser
{

	private $manager;
	
	function DifficultyBrowser($manager)
	{
		$this->manager = $manager;
	}
	
	public function save_changes()
	{
		/*
		$html = array();
		if(!empty($_POST))
		{
			if(!is_null(Request::post('save_all')))
			{
				$difficulties_count = $this->manager->get_data_manager()->count_difficulties();
				$status = true;
				$filter = array();
				for($i=1;$i<=$difficulties_count;$i++)
				{
					if(!is_null(Request::post("id_" . $i)))
					{
						$difficulty = $this->manager->get_data_manager()->retrieve_difficulty(Request::post("id_" . $i));
						$difficulty->set_order($i);
						$status &= $this->manager->get_data_manager()->update_difficulty($difficulty);
						$filter[] = $difficulty->get_id();
					}
					else
					{
						$status &= $this->manager->get_data_manager()->delete_other_difficulties($filter);
						break;
					}
				}
				if($status) $html[] = '<p class="good">' . Language::get_instance()->translate(260) . '</p>';
				else		$html[] = '<p class="error">' . Language::get_instance()->translate(256) . '</p>';
			}
			else
			{
				$difficulty = $this->manager->get_data_manager()->retrieve_difficulty_from_post();
				if($difficulty)
				{
					$success = $this->manager->get_data_manager()->insert_difficulty($difficulty);
					if($success)	$html[] = "<p class='good'>" . Language::get_instance()->translate(258) . "</p>";
					else			$html[] = "<p class='error'>" . Language::get_instance()->translate(259) . "</p>";
				}
				else
					$html[] = "<p class='error'>" . Language::get_instance()->translate(81) . "</p>";
			}
		}
		return implode("\n", $html);
		*/
	}
	
	public function get_html()
	{
		$html = array();
		//$html[] = $this->save_changes();
		$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'pages/puzzle/difficulty/javascript/difficulty_browser.js"></script>';
		//$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'core/javascript/editable.js"></script>';
		$html[] = '<div id="difficulty_info">';
		//$html[] = $this->manager->get_renderer()->get_form();
		$html[] = '<div id="difficulty_table">';
		$html[] = $this->manager->get_renderer()->get_table();
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '<br />';
		return implode("\n", $html);
	}

	public function get_description()
	{
		return '<p style="vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(261) . '</p>';
	}
	
	public function get_title()
	{
		return "";
	}
	
}

?>