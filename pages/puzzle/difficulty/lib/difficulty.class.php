<?php

class Difficulty
{

	private $id;
	private $bottom_rating;
	private $top_rating;
	private $name_male;
	private $name_female;
	private $order;
	
	private $translations;
	
	function Difficulty($data=null)
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
		$this->id = $data['id'];
		$this->bottom_rating = $data['bottom_rating'];
		$this->top_rating = $data['top_rating'];
		$this->name_male = $data['name_male'];
		$this->name_female = $data['name_female'];
		$this->order = $data['order'];
		//$this->translations = $data['translations'];
	}
	
	public function fill_from_database($data)
	{
		$this->id = $data->id;
		$this->bottom_rating = $data->bottom_rating;
		$this->top_rating = $data->top_rating;
		$this->name_male = $data->name_male;
		$this->name_female = $data->name_female;
		$this->order = $data->order;
	}

	public function get_properties()
	{
		return array("id" => $this->id,
					 "bottom_rating" => $this->bottom_rating,
					 "top_rating" => $this->top_rating,
					 "name_male" => $this->name_male,
					 "name_female" => $this->name_female,
					 "order" => $this->order);
	}
	
	public function get_id() { return $this->id; }
	public function set_id($id) { $this->id = $id; }
	public function get_bottom_rating() { return $this->bottom_rating; }
	public function get_bottom_rating_text(){ return is_null($this->bottom_rating)?"<":$this->bottom_rating; }
	public function set_bottom_rating($bottom_rating) { $this->bottom_rating = $bottom_rating; }
	public function get_top_rating() { return $this->top_rating; }
	public function set_top_rating($top_rating) { $this->top_rating = $top_rating; }
	public function get_top_rating_text(){ return is_null($this->top_rating)?"<":$this->top_rating; }
	public function get_name_male() { return $this->name_male; }
	public function set_name_male($name_male) { $this->name_male = $name_male; }
	public function get_name_female() { return $this->name_female; }
	public function set_name_female($name_female) { $this->name_female = $name_female; }
	public function get_name() { return Language::get_instance()->translate($this->get_name_male()) . ($this->get_name_male()!=$this->get_name_female()?"/" . Language::get_instance()->translate($this->get_name_female()):"");}
	public function get_order() { return $this->order; }
	public function set_order($order) { $this->order = $order; }
	public function get_translations() { return $this->translations; }
	public function set_translations($translations) { $this->translations = $translations; }
	

}
?>