<?php

class PuzzleCreator
{
	
	private $manager;
	private $puzzle_id;
	private $page;
	
	function PuzzleCreator($manager)
	{
		$this->manager = $manager;
		$this->puzzle_id = Request::get('id');
		$this->page = Request::get('page_nr');
		if(is_null($this->puzzle_id) || (!is_numeric($this->puzzle_id) && !is_array($this->puzzle_id)))
		{
			$this->puzzle_id = 0;
			$this->page = 1;
		}
		if(is_null($this->page) || !is_numeric($this->page))
			$this->page = 1;
	}
	
	public function get_html($lesson = false)
	{
		$html  = array();
		if($this->page == 1)
		{
			$group_id = 0;
			if(!is_null($this->manager->get_user()->get_group()))
				$group_id = $this->manager->get_user()->get_group_id();
        	$pupil = $group_id==GroupManager::GROUP_PUPIL_ID?1:0;
        	
			$html[] = '<script type="text/javascript">';
			$html[] = '  var appName = "PuzzleCreator";';
			$html[] = '  var language = "' . Language::get_instance()->get_language() . '";';
			$html[] = '  var puzzleId = ' . $this->puzzle_id . ';';
			$html[] = '  var userId = ' . $this->manager->get_user()->get_id() . ';';
			$html[] = '  var pupil = ' . 1 . ';';
			$html[] = '  var lesson = ' . ($lesson?1:0) . ';';
			$html[] = '  var flashvars = "appName="+appName+"&puzzleId="+puzzleId+"&userId="+userId+"&pupil="+pupil+"&language="+language+"&lesson="+lesson;';
			$html[] = '</script>';
			$html[] = '<div style="padding-top: 10px;">';
			include Path::get_path() . "/flash/main.php";
			$html[] = '</div>';
		}
		return implode("\n", $html);
	}
	
	public function get_title()
	{
		return "";
	}
	
	
	public function get_description()
	{
		return '<p style="vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(336) . '</p>';
	}
}

?>