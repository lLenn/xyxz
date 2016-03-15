<?php

class SetViewer
{	
	private $manager;
	private $set_id;
	private $previous_page;
	private $attempt;
	private $no_repeat;
	
	function SetViewer($manager)
	{
		$this->manager = $manager;
		$this->set_id = Request::get('id');
		if(is_null($this->set_id) || !is_numeric($this->set_id))
		{
			$this->set_id = 0;
		}
		$this->previous_page = Request::get('previous_page');
		$this->attempt = Request::get('attempt');
		$this->no_repeat = Request::get('no_repeat');
	}
	
	public function get_html()
	{
		$html  = array();
		if($this->set_id != 0)
		{
			if(is_null($this->attempt) || !is_numeric($this->attempt))
				$this->attempt = $this->manager->get_data_manager()->retrieve_set_attempt($this->set_id, $this->manager->get_user()->get_id());
			$group_id = 0;
			if(!is_null($this->manager->get_user()->get_group()))
				$group_id = $this->manager->get_user()->get_group_id();
			if(is_null($this->previous_page))
			{
				$this->previous_page = "browse_puzzle_sets&id=".$this->set_id;
				if($group_id == GroupManager::GROUP_PUPIL_ID)
					$this->previous_page = "browse_excercises";
			}
			if(is_null($this->no_repeat) || !is_numeric($this->no_repeat))
				$this->no_repeat = 0;
        	$pupil = $group_id==GroupManager::GROUP_PUPIL_ID?1:0;
			$html[] = '<script type="text/javascript">';
			$html[] = '  var language = "' . Language::get_instance()->get_language() . '";';
			$html[] = '  var setId = ' . $this->set_id . ';';
			$html[] = '  var userId = ' . $this->manager->get_user()->get_id() . ';';
			$html[] = '  var attempt = ' . $this->attempt . ';';
			$html[] = '  var previousPage = "' . $this->previous_page . '";';
			$html[] = '  var noRepeat = "' . $this->no_repeat . '";';
			$html[] = '  var pupil = ' . 1 . ';';
			$html[] = '</script>';
			$html[] = '<div style="padding-top: 10px;">';
			$html[] = '<link rel="stylesheet" type="text/css" href="' . Path :: get_url_path() . 'flash/history/history.css" />';
			$html[] = '<script src="' . Path :: get_url_path() . 'flash/AC_OETags.js" type="text/javascript"></script>';
			$html[] = '<script src="' . Path :: get_url_path() . 'flash/history/history.js" type="text/javascript"></script>';
			$html[] = '<script src="' . Path :: get_url_path() . 'flash/PuzzleViewer_v1_15.js" type="text/javascript"></script>';
			$html[] = '<noscript>';
			$html[] = '	<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"
								id="PuzzleCreator" width="650" height="530"
								codebase="http://fpdownload.macromedia.com/get/flashplayer/current/swflash.cab">';
			$html[] = '	<param name="movie" value="' . Path :: get_path() . 'flash/PuzzleViewer_v1_15.swf" />';
			$html[] = '	<param name="quality" value="high" />';
			$html[] = '	<param name="bgcolor" value="#869ca7" />';
			$html[] = '	<param name="wmode" value="transparent" />';
			$html[] = '	<param name="allowScriptAccess" value="sameDomain" />';
			$html[] = '	<embed src="' . Path :: get_path() . 'flash/PuzzleViewer_v1_15.swf" quality="high" bgcolor="#869ca7"
							width="650" height="530" name="PuzzleViewer" align="middle"
							play="true"
							loop="false"
							quality="high"
							allowScriptAccess="sameDomain"
							type="application/x-shockwave-flash"
							pluginspage="http://www.adobe.com/go/getflashplayer">';
			$html[] = '	</embed>';
			$html[] = ' </object>';
			$html[] = '</noscript>';
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
		return '<p style="vertical-align:top;font-style:italic;font-size:11px;"></p>';
	}
}

?>