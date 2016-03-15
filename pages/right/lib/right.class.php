<?php

class Right
{

	private $read;
	private $write;
	private $update;
	
	function Right($data=null)
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
		$this->read = $data['read'];
		$this->write = $data['write'];
		$this->update = $data['update'];
	}
	
	public function fill_from_database($data)
	{
		$this->read = $data->read;
		$this->write = $data->write;
		$this->update = $data->update;
	}
	
	public function get_properties()
	{
		return array('read' => $this->read,
					 'write' => $this->write,
					 'update' => $this->update);
	}
	
	public function get_read() { return $this->read; }
	public function set_read($read) { $this->read = $read; }
	public function get_write() { return $this->write; }
	public function set_write($write) { $this->write = $write; }
	public function get_update() { return $this->update; }
	public function set_update($update) { $this->update = $update; }
	
}

?>