<?php

class ThemeBrowser
{

	private $manager;
	
	function ThemeBrowser($manager)
	{
		$this->manager = $manager;
	}
	
	public function save_changes()
	{
		$html = array();
		if(!empty($_POST))
		{
			if(!is_null(Request::post('save_all')))
			{
				$themes_count = $this->manager->get_data_manager()->count_themes();
				$status = true;
				$filter = array();
				for($i=1;$i<=$themes_count;$i++)
				{
					if(!is_null(Request::post("id_" . $i)))
					{
						$theme = $this->manager->get_data_manager()->retrieve_theme(Request::post("id_" . $i));
						$theme->set_order($i);
						$status &= $this->manager->get_data_manager()->update_theme($theme);
						$filter[] = $theme->get_id();
					}
				}
				if(count($filter) != $themes_count)
				{
					$status &= $this->manager->get_data_manager()->delete_other_themes($filter);
				}
				if($status) $html[] = '<p class="good">' . Language::get_instance()->translate(329) . '</p>';
				else		$html[] = '<p class="error">' . Language::get_instance()->translate(330) . '</p>';
			}
			else
			{
				$theme = $this->manager->get_data_manager()->retrieve_theme_from_post();
				if($theme)
				{
					$success = $this->manager->get_data_manager()->insert_theme($theme);
					if($success)	$html[] = "<p class='good'>" . Language::get_instance()->translate(332) . "</p>";
					else			$html[] = "<p class='error'>" . Language::get_instance()->translate(333) . "</p>";
				}
				else
					$html[] = "<p class='error'>" . Language::get_instance()->translate(81) . "</p>";
			}
		}
		return implode("\n", $html);
	}
	
	public function get_html()
	{
		$html = array();
		$html[] = $this->save_changes();
		$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'pages/puzzle/theme/javascript/theme_browser.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'core/javascript/editable.js"></script>';
		$html[] = '<div id="theme_info">';
		$html[] = $this->manager->get_renderer()->get_form();
		$html[] = '<div id="theme_table">';
		$html[] = $this->manager->get_renderer()->get_table();
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '<br />';
		return implode("\n", $html);
	}
	
	public function get_title()
	{
		return "";
	}

	public function get_description()
	{
		return '<p style="vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(334) . '</p>';
	}
}

?>