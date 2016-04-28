<?php

class LocationObjectUserRight extends LocationUser
{

	private $object_id;
	
	function LocationObjectUserRight($data=null)
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
		$this->object_id = $data['object_id'];
	}
	
	public function fill_from_database($data)
	{
		parent::fill_from_database($data);
		$this->object_id = $data->object_id;
	}
	
	public function get_properties()
	{
		return array_merge(parent::get_properties(), array('object_id' => $this->object_id));
	}
	
	public function get_object_id() { return $this->object_id; }
	public function set_object_id($object_id) { $this->object_id = $object_id; }
	
}

?>