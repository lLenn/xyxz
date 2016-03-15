<?php

class SetBrowser
{

	private $manager;
	private $id;
	private $selected_tab = 0;
	private $object_right = RightManager::NO_RIGHT;
	
	function SetBrowser($manager)
	{
		$this->manager = $manager;
		$this->id = Request::get("id");
		if(is_null($this->id) || !is_numeric($this->id)) 
			$this->id = 0;
		else
			$this->object_right = RightManager::instance()->get_right_location_object(RightManager::SET_LOCATION_ID, $this->manager->get_user(), $this->id);
	}
	
	public function get_html()
	{
		$html = array();
		$user = $this->manager->get_user();
					
		$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'core/javascript/editable.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'pages/puzzle/set/javascript/set_browser.js"></script>';
		
		$html[] = '<div id="set_info">';
		
		$html[] = '<div id="tabs">';
		$html[] = '<ul width="95%">';
		if($this->id != 0)
		{
			$html[] = '<li><a href="#set_details">' . Language::get_instance()->translate(74) . '</a></li>';
		}
		$html[] = '<li><a href="#general">' . Language::get_instance()->translate(75) . '</a></li>';
		$html[] = '</ul>';
		
		$html[] = display::get_message();
		
		if($this->id != 0)
		{
			$html[] = '<div id="set_details">';
			$html[] = '<div id="set_details_info">';
			$html[] = $this->manager->get_renderer()->get_set_info($this->manager->get_data_manager()->retrieve_set($this->id));
			$html[] = '</div>';
			$html[] = '<div id="set_puzzles">';
			$html[] = $this->manager->get_renderer()->get_set_puzzles_info($this->id, $this->object_right);
			$html[] = '</div>';		
			$html[] = '<div style="height: 150px;"></div>';
			$html[] = '</div>';
		}
		$html[] = '<div id="general">';
		$html[] = $this->manager->get_renderer()->get_set_search();
		$html[] = '<div id="search_table">';
		$html[] = $this->manager->get_renderer()->get_set_table(RightManager::READ_RIGHT);
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '<script type="text/javascript">';
	    $html[] = '  var tabnumber = ' . $this->selected_tab . ';';
	    $html[] = '  var set_id = ' . $this->id . ';';
	    $html[] = '</script>';
		$html[] = '</div>';

		return implode("\n",$html);
	}
	
	public function get_title()
	{
		return "<h1></h1>";
	}
	
	public function get_description()
	{
		return '<p style="vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(316) . '</p>';
	}
}

?>