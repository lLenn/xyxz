<?php

class PuzzleProperties extends LocationObjectUserRight
{
	private $id;
	private $puzzle_id;
	private $valid;
	private $rating;
	private $theme_ids;
	private $number_of_moves;
	private $comment;
	
	function PuzzleProperties($data=null)
	{
		if(!is_null($data))
		{
			if(is_array($data))
				$this->fill_from_array($data);
			else
				$this->fill_from_database($data);
		}
	}

	public function fill_from_array($data)
	{
		$this->id = $data['id'];
		$this->puzzle_id = $data['puzzle_id'];
		$this->valid = $data['valid'];
		$this->theme_ids = $data['theme_ids'];
		$this->rating = $data['rating'];
		$this->number_of_moves = $data['number_of_moves'];
		$this->comment = $data['comment'];
	}
	
	public function fill_from_database($data)
	{
		if(isset($data->object_id))
			parent::fill_from_database($data);
		$this->id = $data->id;
		$this->puzzle_id = $data->puzzle_id;
		$this->valid = $data->valid;
		$this->rating = $data->rating;
		$this->number_of_moves = $data->number_of_moves;
		$this->comment = $data->comment;
	}
	
	public function get_properties()
	{
		return array('id' => $this->id,
					 'puzzle_id' => $this->puzzle_id,
					 'valid' => $this->valid,
					 'rating' => $this->rating,
					 'number_of_moves' => $this->number_of_moves,
					 'comment' => $this->comment);
	}
	
	public function get_id() { return $this->id; }
	public function set_id($id) { $this->id = $id; }
	public function get_puzzle_id() { return $this->puzzle_id; }
	public function set_puzzle_id($puzzle_id) { $this->puzzle_id = $puzzle_id; }
	public function get_valid() { return $this->valid; }
	public function set_valid($valid) { $this->valid = $valid; }
	public function get_rating() { return $this->rating; }
	public function set_rating($rating) { $this->rating = $rating; }
	public function get_theme_ids() 
	{
		if(is_null($this->theme_ids))
			$this->theme_ids = PuzzleDataManager::instance(null)->retrieve_puzzle_themes($this->puzzle_id);
		return $this->theme_ids; 
	}	
	public function get_themes() 
	{
		require_once Path :: get_path() . "pages/puzzle/theme/lib/theme_data_manager.class.php";
		Language::get_instance()->add_section_to_translations(Language::THEME);
		if(!is_null($this->get_theme_ids()) && !empty($this->theme_ids))
		{
			$str = "";
			$size = count($this->theme_ids);
			$i = 1;
			foreach($this->theme_ids as $id)
			{
				$str .= Language::get_instance()->translate(ThemeDataManager::instance(null)->retrieve_theme($id)->get_name());
				if($i < $size)
					$str .= ", ";
				$i++;
			}
			return $str;
		}
		else
		{
			return "-";
		} 
	}
	public function set_theme_ids($theme_ids) { $this->theme_ids = $theme_ids; }
	public function get_number_of_moves() { return $this->number_of_moves; }
	public function set_number_of_moves($number_of_moves) { $this->number_of_moves = $number_of_moves; }
	public function get_difficulty()
	{ 
		require_once Path :: get_path() . "pages/puzzle/difficulty/lib/difficulty_data_manager.class.php";
		$difficulty = DifficultyDataManager::instance(null)->retrieve_difficulty_by_rating($this->rating);
		if(!is_null($difficulty))
		{
			return $difficulty->get_name();
		}
		else
		{
			return "-";
		} 
	}
	public function get_comment() { return $this->comment; }
	public function set_comment($commment) { $this->comment = $comment; }

}

?>