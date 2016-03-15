<?php

class PuzzleViewer
{	
	private $manager;
	private $puzzle_id;
	
	function PuzzleViewer($manager)
	{
		$this->manager = $manager;
		$this->puzzle_id = Request::get('id');
		$this->random = Request::get('random');
		if(is_null($this->puzzle_id) || !is_numeric($this->puzzle_id))
		{
			$this->puzzle_id = 0;
		}
		if(is_null($this->random) || !is_numeric($this->random))
		{
			$this->random = 0;
		}
	}
	
	public function get_html()
	{
		$html  = array();
		if($this->puzzle_id != 0 || $this->random)
		{
	        $id = html5Helper::storeObject("puzzle", $this->puzzle_id);
	        $group_id = 0;
	        if(!is_null($this->manager->get_user()->get_group()))
	        	$group_id = $this->manager->get_user()->get_group_id();
	        $guest = false;
			if($group_id==GroupManager::GROUP_GUEST_ID)
				$guest = true;
			else
				$guest = false;
        	
			$html[] = HTML5Helper::loadChessboardScripts();
			$html[] = '<div style="padding-top: 10px;" id="PuzzleViewerBoard">';
			$html[] = '</div>';
			$html[] = '<script type="text/javascript">';
			$html[] = '  var appArgs = {centerToScreen: true, mobileFullscreen: true}';
			$html[] = '  var args = {appName: "PuzzleViewer", objectSerial: "' . $id[0] . '", random: ' . $this->random . ', guest: "' . $guest . '"};';
			$html[] = '  var board = new chssBoard(document.getElementById("PuzzleViewerBoard"), appArgs, args);';
			$html[] = ' board.initiate();';
			$html[] = '</script>';
		}
		return implode("\n", $html);
	}
	
	public function get_title()
	{
		return "";
	}
	
	public function get_description()
	{
		return '<p style="vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(344) . '</p>';
	}
}

?>