<?php

class Theme
{

	private $id;
	private $name;
	private $order;
	
	private $translations;
	
	function Theme($data=null)
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
		$this->order = $data['order'];
		$this->translations = $data['translations'];
	}
	
	public function fill_from_database($data)
	{
		$this->id = $data->id;
		$this->name = $data->name;
		$this->order = $data->order;
	}

	public function get_properties()
	{
		return array("id" => $this->id,
					 "name" => $this->name,
					 "order" => $this->order);
	}
	
	public function get_id() { return $this->id; }
	public function set_id($id) { $this->id = $id; }
	public function get_name() { return $this->name; }
	public function set_name($name) { $this->name = $name; }
	public function get_order() { return $this->order; }
	public function set_order($order) { $this->order = $order; }
	public function get_translations() { return $this->translations; }
	public function set_translations($translations) { $this->translations = $translations; }

}
?>