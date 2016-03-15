<?php

class LocationUserMapRelation
{

	private $map_id;
	private $object_id;
	
	function LocationUserMapRelation($data=null)
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
		$this->map_id = $data['map_id'];
		$this->object_id = $data['object_id'];
	}
	
	public function fill_from_database($data)
	{
		$this->map_id = $data->map_id;
		$this->object_id = $data->object_id;
	}
	
	public function get_properties()
	{
		return array('map_id' => $this->map_id,
					 'object_id' => $this->object_id);
	}
	
	public function get_map_id() { return $this->map_id; }
	public function set_map_id($map_id) { $this->map_id = $map_id; }
	public function get_object_id() { return $this->object_id; }
	public function set_object_id($object_id) { $this->object_id = $object_id; }
	
}

?>