<?php

class CustomProperties
{
	private $properties;

	function CustomProperties()
	{
		$this->properties = array();
	}

	public function get_properties()
	{
		return $this->properties;
	}
	
	public function get_property($key)
	{
		if(isset($this->properties[$key]))
			return $this->properties[$key];
		else
			return null;
	}

	public function set_properties($properties_array)
	{
		$this->properties = $properties_array;
	}

	public function add_property($key, $value)
	{
		$this->properties[$key] = $value;
	}
	
	public function remove_property($key)
	{
		unset($this->properties[$key]);
	}
}

?>