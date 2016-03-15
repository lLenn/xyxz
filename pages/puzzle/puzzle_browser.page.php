<?php

class PuzzleBrowser
{
	private $manager;
	private $id;
	
	function PuzzleBrowser($manager)
	{
		$this->manager = $manager;
		$this->id = Request::get("id");
		if(is_null($this->id) || !is_numeric($this->id))
			$this->id = null;
	}
	
	public function get_html()
	{
		$html = array();
		
		$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'core/javascript/editable.js"></script>';
		$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'pages/puzzle/javascript/puzzle_browser.js"></script>';
		
		$html[] = '<div id="puzzle_info">';
		
		$html[] = '<div id="tabs">';
		$html[] = '<ul width="95%">';
		if($this->id != 0)
		{
			$html[] = '<li><a href="#puzzle_details">' . Language::get_instance()->translate(74) . '</a></li>';
		}
		$html[] = '<li><a href="#general" id="explorer">' . Language::get_instance()->translate(75) . '</a></li>';
		$html[] = '</ul>';
		
		$html[] = Display::get_message();
		
		if($this->id != 0)
		{
			$html[] = '<div id="puzzle_details">';
			$html[] = $this->manager->factory("Puzzle_Viewer")->get_html();
			$html[] = '</div>';
		}
		$html[] = '<div id="general">';
		$html[] = $this->manager->get_renderer()->get_puzzle_search();
		$html[] = '<div id="search_table">';
		$html[] = $this->manager->get_renderer()->get_puzzle_table(RightManager::READ_RIGHT, false, true, 0, 200);
		$html[] = '<div style="height: 150px;"></div>';
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '<script type="text/javascript">';
	    $html[] = '  var tabnumber = 0;';
	    $html[] = '  var shop = 0;';
	    $html[] = '  var delete_message = "' . Language::get_instance()->translate(912) . '";';
	    $html[] = '</script>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
	
	public function get_title()
	{
		return "";
	}
	
	
	public function get_description()
	{
		return '<p style="vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(335) . '</p>';
	}

}

?>