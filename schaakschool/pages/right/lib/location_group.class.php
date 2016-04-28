<?php

class LocationGroup extends Right
{

	private $location_id;
	private $group_id;
	
	function LocationGroup($data=null)
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
		parent::fill_from_array($data);
		$this->location_id = $data['location_id'];
		$this->group_id = $data['group_id'];
	}
	
	public function fill_from_database($data)
	{
		parent::fill_from_database($data);
		$this->location_id = $data->location_id;
		$this->group_id = $data->group_id;
	}
	
	public function get_properties()
	{
		return array_merge(parent::get_properties(), array('location_id' => $this->location_id,
					 'group_id' => $this->group_id));
	}
	
	public function get_location_id() { return $this->location_id; }
	public function set_location_id($location_id) { $this->location_id = $location_id; }
	public function get_group_id() { return $this->group_id; }
	public function set_group_id($group_id) { $this->group_id = $group_id; }

}

?>