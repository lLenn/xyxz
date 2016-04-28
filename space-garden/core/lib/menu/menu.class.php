<?php

class Menu
{
	private $id;
	private $name;
	private $direction;
	
	private $menu_items = null;
	
	function Menu($data=null)
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
		$this->direction = $data['direction'];
	}
	
	public function fill_from_database($data)
	{
		$this->id = $data->id;
		$this->name = $data->name;
		$this->direction = $data->direction;
	}
	
	public function get_properties()
	{
		return array('id' => $this->id,
					 'name' => $this->name,
					 'direction' => $this->direction);
	}
	
	public function get_id() { return $this->id; }
	public function set_id($id) { $this->id = $id; }
	public function get_name() { return $this->name; }
	public function set_name($name) { $this->name = $name; }
	public function get_direction() { return $this->direction; }
	public function set_direction($direction) { $this->direction = $direction; }
	public function get_menu_items() { return $this->menu_items; }
	public function set_menu_items($menu_items) { $this->menu_items = $menu_items; }
}

?>