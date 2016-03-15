<?php

class LocationObjectMetaData
{
	private $location_id;
	private $object_id;
	private $key;
	private $value;
	
	function LocationObjectMetaData($data=null)
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
		$this->location_id = $data['location_id'];
		$this->object_id = $data['object_id'];
		$this->key = $data['key'];
		$this->value = $data['value'];
	}
	
	public function fill_from_database($data)
	{
		$this->location_id = $data->location_id;
		$this->object_id = $data->object_id;
		$this->key = $data->meta_key;
		$this->value = $data->meta_value;
	}
	
	public function get_properties()
	{
		return array('location_id' => $this->location_id,
					 'object_id' => $this->object_id,
					 'meta_key' => $this->key,
					 'meta_value' => $this->value);
	}
	
	public function get_location_id() { return $this->location_id; }
	public function set_location_id($location_id) { $this->location_id = $location_id; }
	public function get_object_id() { return $this->object_id; }
	public function set_object_id($object_id) { $this->object_id = $object_id; }
	public function get_key() { return $this->key; }
	public function set_key($key) { $this->key = $key; }
	public function get_value() { return $this->value; }
	public function set_value($value) { $this->value = $value; }
}

?>