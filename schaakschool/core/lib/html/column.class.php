<?php

class Column extends Html
{
	private $header = '&nbsp;';
	private $name = null;
	private $title = null;
	private $getter = true;
	private $title_getter = true;
	private $content_type = "string";
	private $order = false;
	private $editable = false;
	private $editable_type = "text";
	private $editable_name = null;
	private $editable_id = null;
	
	function Column($header, $name, $content_type = "string", $getter = true)
	{
		$this->header = $header;
		$this->name = $name;
		$this->getter = $getter;
		$this->content_type = $content_type;
	}
	
	public function set_header($header){ $this->header = $header; }
	public function get_header(){ return $this->header; }
	public function set_name($name, $getter = true){ $this->name = $name; $this->getter = $getter; }
	public function get_name(){ return $this->name; }
	public function set_title($title, $getter = true){ $this->title = $title; $this->title_getter = $getter; }
	public function get_title(){ return $this->title; }
	public function get_getter(){ return $this->getter; }
	public function get_title_getter(){ return $this->title_getter; }
	public function set_order($order){ $this->order = $order; }
	public function is_order(){ return $this->order; }
	public function set_content_type($content_type){ $this->content_type = $content_type; }
	public function get_content_type(){ return $this->content_type; }
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
	public function set_editable_id($editable_id){ $this->editable_id = $editable_id; }
	public function get_editable_id(){ return $this->editable_id; }
}
?>