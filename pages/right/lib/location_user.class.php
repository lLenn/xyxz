<?php

class LocationUser extends Right
{

	private $location_id;
	private $user_id;
	
	function LocationUser($data=null)
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
		$this->user_id = $data['user_id'];
	}
	
	public function fill_from_database($data)
	{
		parent::fill_from_database($data);
		$this->location_id = $data->location_id;
		$this->user_id = $data->user_id;
	}
	
	public function get_properties()
	{
		return array_merge(parent::get_properties(), array('location_id' => $this->location_id,
					 		'user_id' => $this->user_id));
	}
	
	public function get_location_id() { return $this->location_id; }
	public function set_location_id($location_id) { $this->location_id = $location_id; }
	public function get_user_id() { return $this->user_id; }
	public function set_user_id($user_id) { $this->user_id = $user_id; }

}

?>