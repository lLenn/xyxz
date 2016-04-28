<?php

class LocationUserMap
{

	private $id;
	private $location_id;
	private $user_id;
	private $name;
	private $order;
	
	function LocationUserMap($data=null)
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
		$this->location_id = $data['location_id'];
		$this->user_id = $data['user_id'];
		$this->name = $data['name'];
		$this->order = $data['order'];
	}
	
	public function fill_from_database($data)
	{
		$this->id = $data->id;
		$this->location_id = $data->location_id;
		$this->user_id = $data->user_id;
		$this->name = $data->name;
		$this->order = $data->order;
	}
	
	public function get_properties()
	{
		return array('id' => $this->id,
					 'location_id' => $this->location_id,
					 'user_id' => $this->user_id,
					 'name' => $this->name,
					 'order' => $this->order);
	}
	
	public function get_id() { return $this->id; }
	public function set_id($id) { $this->id = $id; }
	public function get_location_id() { return $this->location_id; }
	public function set_location_id($location_id) { $this->location_id = $location_id; }
	public function get_user_id() { return $this->user_id; }
	public function set_user_id($user_id) { $this->user_id = $user_id; }
	public function get_name() { return $this->name; }
	public function set_name($name) { $this->name = $name; }
	public function get_order() { return $this->order; }
	public function set_order($order) { $this->order = $order; }
	
}

?>