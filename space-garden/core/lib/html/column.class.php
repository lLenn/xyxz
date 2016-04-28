<?php

class Column extends Html
{

	private $header = '&nbsp;';
	private $name = null;
	private $order = false;
	private $editable = false;
	private $editable_type = "text";
	private $editable_name = null;
	
	function Column($header, $name)
	{
		$this->header = $header;
		$this->name = $name;
	}
	
	public function set_header($header){ $this->header = $header; }
	public function get_header(){ return $this->header; }
	public function set_name($name){ $this->name = $name; }
	public function get_name(){ return $this->name; }
	public function set_order($order){ $this->order = $order; }
	public function is_order(){ return $this->order; }
	public function set_editable($editable){ $this->editable = $editable; }
	public function is_editable(){ return $this->editable; }
	public function set_editable_type($editable_type)
	{
		$available_types = array("text", "checkbox");
		if(in_array($editable_type, $available_types))
		{
			$this->editable_type = $editable_type; 
		}
		else
		{			
			throw new Exception("Please use a correct editable type");
		}
	}	
	public function get_editable_type(){ return $this->editable_type; }
	public function set_editable_name($editable_name){ $this->editable_name = $editable_name; }
	public function get_editable_name(){ return $this->editable_name; }

}

?>