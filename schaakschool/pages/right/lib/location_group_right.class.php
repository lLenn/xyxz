<?php

class LocationGroupRight extends LocationGroup
{

	private $allowed_objects;
	
	function LocationGroupRight($data=null)
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
		$this->allowed_objects = $data['allowed_objects'];
	}
	
	public function fill_from_database($data)
	{
		parent::fill_from_database($data);
		$this->allowed_objects = $data->allowed_objects;
	}
	
	public function get_properties()
	{
		return array_merge(parent::get_properties(), array('allowed_objects' => $this->allowed_objects));
	}
	
	public function get_allowed_objects() { return $this->allowed_objects; }
	public function set_allowed_objects($allowed_objects) { $this->allowed_objects = $allowed_objects; }
}

?>