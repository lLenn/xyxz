<?php

class PuzzleDubbleRemover
{
	private $manager;
	private $id;
	
	function PuzzleDubbleRemover($manager)
	{
		$this->manager = $manager;
	}
	
	public function save_changes()
	{
		$html = array();
		if(!is_null(Request::post("count")) && is_numeric(Request::post("count")) && Request::post("count")>0 )
		{
			$puzzle_delete = array();
			for($i = 1;$i<=Request::post("count");$i++)
			{
				$success = true;
				foreach(Request::post("puzzles_".$i) as $id)
				{
					if(Request::post("puzzle_superior_".$i) != $id)
					{
						$success &= $this->manager->get_data_manager()->replace_puzzle_id($id, Request::post("puzzle_superior_".$i));
						if($success) $puzzle_delete[] = $id;
						else break;
					}
				}
				if(!$success) break;
			}
			if($success)
			{
				$html[] = "<p class='good'>" . Language::get_instance()->translate(337) . "</p>";
				foreach($puzzle_delete as $delete_id)
				{
					$success &= $this->manager->get_data_manager()->delete_puzzle($delete_id);
				}
				if($success)
					$html[] = "<p class='good'>" . Language::get_instance()->translate(338) . "</p>";
				else 
					$html[] = "<p class='error'>" . Language::get_instance()->translate(339) . "</p>";	
			}
			else 
				$html[] = "<p class='error'>" . Language::get_instance()->translate(340) . "</p>";
		}
		return implode("\n", $html);
	}
	
	public function get_html()
	{
		$html = array();
		//$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'core/javascript/editable.js"></script>';
		//$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'pages/puzzle/javascript/puzzle_browser.js"></script>';
		$html[] = $this->save_changes();
		$html[] = '<div id="puzzle_info">';
		$html[] = $this->manager->get_renderer()->get_dubble_puzzles_form();
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