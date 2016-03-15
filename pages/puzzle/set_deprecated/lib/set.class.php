<?php

class Set
{
	private $id;
	private $name;
	private $description;
	private $theme_ids;
	private $difficulty_id;
	
	function Set($data=null)
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
		$this->name = $data['name'];
		$this->description = $data['description'];
		$this->theme_ids = $data['theme_ids'];
		$this->difficulty_id = $data['difficulty_id'];
	}
	
	public function fill_from_database($data)
	{
		$this->id = $data->id;
		$this->name = $data->name;
		$this->description = $data->description;
		$this->difficulty_id = $data->difficulty_id;
	}
	
	public function get_properties()
	{
		return array('id' => $this->id,
					 'name' => $this->name,
					 'description' => $this->description,
					 'difficulty_id' => $this->difficulty_id);
	}
	
	public function get_id() { return $this->id; }
	public function set_id($id) { $this->id = $id; }
	public function get_name() { return $this->name; }
	public function set_name($name) { $this->name = $name; }
	public function get_description() { return $this->description; }
	public function set_description($description) { $this->description = $description; }	
	public function get_theme_ids() 
	{
		if(is_null($this->theme_ids))
			$this->theme_ids = SetDataManager::instance(null)->retrieve_set_themes($this->id);
		return $this->theme_ids; 
	}	
	public function get_themes() 
	{
		require_once Path :: get_path() . "pages/puzzle/theme/lib/theme_data_manager.class.php";
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
	public function get_difficulty_id() { return $this->difficulty_id; }
	public function set_difficulty_id($difficulty_id) { $this->difficulty_id = $difficulty_id; }
	public function get_difficulty()
	{
		return DifficultyDataManager::instance(null)->retrieve_difficulty($this->difficulty_id)->get_name();
	}

}

?>