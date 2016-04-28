<?php

class LocationRight
{

	private $id;
	private $parent_id;
	private $location;
	private $function_all;
	private $function_one;
	private $primary_key;
	private $credits_buy;
	private $credits_sell;
	private $credits_accepted;
	private $row_header;
	private $row_renderer;
	private $description;
	
	function LocationRight($data=null)
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
		$this->parent_id = $data['parent_id'];
		$this->location = $data['location'];
		$this->function_all = $data['function_all'];
		$this->function_one = $data['function_one'];
		$this->primary_key = $data['primary_key'];
		$this->row_header = $data['row_header'];
		$this->row_renderer = $data['row_renderer'];
		$this->description = $data['description'];
	}
	
	public function fill_from_database($data)
	{
		$this->id = $data->id;
		$this->parent_id = $data->parent_id;
		$this->location = $data->location;
		$this->function_all = $data->function_all;
		$this->function_one = $data->function_one;
		$this->primary_key = $data->primary_key;
		$this->row_header = $data->row_header;
		$this->row_renderer = $data->row_renderer;
		$this->description = $data->description;
		if(isset($data->credits_buy))
		{
			$this->credits_buy = $data->credits_buy;
			$this->credits_sell = $data->credits_sell;
			$this->credits_accepted = $data->credits_accepted;
		}
	}
	
	public function get_properties()
	{
		return array('id' => $this->id,
					 'parent_id' => $this->parent_id,
					 'location' => $this->location,
					 'function_all' => $this->function_all,
					 'function_one' => $this->function_one,
					 'primary_key' => $this->primary_key,
					 'row_header' => $this->row_header,
					 'row_renderer' => $this->row_renderer,
					 'description' => $this->description);
	}
	
	public function get_id() { return $this->id; }
	public function set_id($id) { $this->id = $id; }
	public function get_parent_id() { return $this->parent_id; }
	public function set_parent_id($parent_id) { $this->parent_id = $parent_id; }
	public function get_location() { return $this->location; }
	public function set_location($location) { $this->location = $location; }
	public function get_function_all() { return $this->function_all; }
	public function set_function_all($function_all) { $this->function_all = $function_all; }
	public function get_function_one() { return $this->function_one; }
	public function set_function_one($function_one) { $this->function_one = $function_one; }
	public function get_primary_key() { return $this->primary_key; }
	public function set_primary_key($primary_key) { $this->primary_key = $primary_key; }
	public function get_row_header() { return $this->row_header; }
	public function set_row_header($row_header) { $this->row_header = $row_header; }
	public function get_row_renderer() { return $this->row_renderer; }
	public function set_row_renderer($row_renderer) { $this->row_renderer = $row_renderer; }
	public function get_description() { return $this->description; }
	public function set_description($description) { $this->description = $description; }
	public function get_credits_buy() { return $this->credits_buy; }
	public function set_credits_buy($credits_buy) { $this->credits_buy = $credits_buy; }
	public function get_credits_sell() { return $this->credits_sell; }
	public function set_credits_sell($credits_sell) { $this->credits_sell = $credits_sell; }
	public function get_credits_accepted() { return $this->credits_accepted; }
	public function set_credits_accepted($credits_accepted) { $this->credits_accepted = $credits_accepted; }
	
}

?>