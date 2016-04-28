<?php

class MenuItem
{
	private $id;
	private $name;
	private $url;
	private $order;
	private $parent_id;
	
	private $sub_menu = null;
	
	function MenuItem($data=null)
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
		$this->url = $data['url'];
		$this->parent_id = $data['parent_id'];
		$this->order = $data['order'];
	}
	
	public function fill_from_database($data)
	{
		$this->id = $data->id;
		$this->name = $data->name;
		$this->url = $data->url;
		$this->parent_id = $data->parent_id;
		$this->order = $data->order;
	}
	
	public function get_properties()
	{
		return array('id' => $this->id,
					 'name' => $this->name,
					 'url' => $this->url,
					 'parent_id' => $this->parent_id,
					 'order' => $this->order);
	}
	
	public function get_id() { return $this->id; }
	public function set_id($id) { $this->id = $id; }
	public function get_name() { return $this->name; }
	public function set_name($name) { $this->name = $name; }
	public function get_url() { return $this->url; }
	public function set_url($url) { $this->url = $url; }
	public function get_parent_id() { return $this->parent_id; }
	public function set_parent_id($parent_id) { $this->parent_id = $parent_id; }
	public function get_order() { return $this->order; }
	public function set_order($order) { $this->order = $order; }
	public function get_sub_menu() { return $this->sub_menu; }
	public function set_sub_menu($sub_menu) { $this->sub_menu = $sub_menu; }
}

?>