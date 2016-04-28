<?php

class Html
{
	private $attributes = array();
	private $style_attributes = array();

	public function set_attributes($attributes){ $this->attributes = $attributes; }
	public function get_attributes(){ return $this->attributes; }
	public function set_style_attributes($style_attributes){ $this->style_attributes = $style_attributes; }
	public function get_style_attributes(){ return $this->style_attributes; }
	
	public function set_attribute($name, $value){ $this->attributes[$name] = $value; }
	public function get_attribute($name)
	{ 
		if(isset($this->attributes[$name]))
		{
			return $this->attributes[$name]; 
		}
		else
		{
			return "";
		}
	}
	public function remove_attribute($name){ unset($this->attributes[$name]); }
	public function set_style_attribute($name, $value){	$this->style_attributes[$name] = $value; }
	public function get_style_attribute($name)
	{ 
		if(isset($this->style_attributes[$name]))
		{
			return $this->style_attributes[$name]; 
		}
		else
		{
			return "";
		}
	}
	public function remove_style_attribute($name){ unset($this->style_attributes[$name]); }
	
	public function render_attributes()
	{
		$attributes = " ";
		foreach($this->get_attributes() as $name => $value)
		{
			$attributes .= $name . '="' . $value . '" ';
		}
		return $attributes;
	}
	
	public function render_style_attributes()
	{
		$count = count($this->get_style_attributes());
		if($count>0)
		{
			$style_attributes = ' style="';
		}
		else
		{
			return " ";
		}
		$pointer = 1;
		foreach($this->get_style_attributes() as $name => $value)
		{
			$style_attributes .= $name . ': ' . $value . ';';
			if($pointer != $count)
			{
				$style_attributes .= " ";
			}
			$pointer++;
		}
		$style_attributes .= '" ';
		return $style_attributes;
	}
}