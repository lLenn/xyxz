<?php

class PuzzleValidator
{
	private $manager;
	private $id;
	
	function PuzzleValidator($manager)
	{
		$this->manager = $manager;
	}
	
	public function save_changes()
	{
		if($_POST)
		{
			$ids = Request::post("puzzle_id");
			if(!is_null($ids) && is_array($ids))
			{
				$success = true;
				foreach ($ids as $id)
					$success &= $this->manager->get_data_manager()->update_invalid_puzzle_properties_to_valid($id);
			}
			
			if($success)
			{
				header("Location: " . Url :: create_url(array("page" => "browse_puzzles", "message" => urlencode(Language::get_instance()->translate(341)), "message_type" => "good")));  
				exit;
			}
			else
				$html[] = "<p class='error'>" . Language::get_instance()->translate(342) . "</p>";
		}
	}
	
	public function get_html()
	{
		$html = array();
		$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'pages/puzzle/javascript/puzzle_validator.js"></script>';
		
		$html[] = '<div id="puzzle_info">';
		$html[] = $this->save_changes();

		$html[] = $this->manager->get_renderer()->get_puzzle_validate_form();
		
		$html[] = '</div>';
		return implode("\n", $html);
	}
	
	public function get_title()
	{
		return "";
	}
	
	
	public function get_description()
	{
		return '<p style="vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(343) . '</p>';
	}

}

?>