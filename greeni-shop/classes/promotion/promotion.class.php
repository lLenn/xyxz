<?php

require_once "promotion_type.class.php";

class Promotion
{	
	private $id;
	private $article_code;
	private $start_date;
	private $end_date;
	private $criteria;
	
	private $promotion_types = null;
	private $countries = null;
	
	function Promotion($data=null)
	{
		if(!is_null($data))
		{
			$this->fill_from_database($data);
		}
	}
	
	public function fill_from_database($data)
	{
		$this->id = $data->_id;
		$this->article_code = $data->article_code;
		$this->start_date = $data->start_date;
		$this->end_date = $data->end_date;
		$this->criteria = $data->criteria;
	}
	
	public function get_id() { return $this->id; }
	public function set_id($id) { $this->id = $id; }
	public function get_article_code() { return $this->article_code; }
	public function set_article_code($article_code) { $this->article_code = $article_code; }
	public function get_start_date() { return $this->start_date; }
	public function set_start_date($start_date) { $this->start_date = $start_date; }
	public function get_end_date() { return $this->end_date; }
	public function set_end_date($end_date) { $this->end_date = $end_date; }
	public function get_criteria() { return $this->criteria; }
	public function set_criteria($criteria) { $this->criteria = $criteria; }
	public function get_promotion_types() 
	{ 
		if(is_null($this->promotion_types) || !is_array($this->promotion_types))
		{
			$this->promotion_types = array();
			$query = "SELECT DISTINCT _promotion_id, type FROM `promotion_type` WHERE _promotion_id  = '" . $this->get_id() . "'";
			$result = mysql_query($query); if(mysql_errno()>0) trigger_error("+++ ".mysql_errno().": ".mysql_error()." +++");
			while($row = mysql_fetch_object($result))
				$this->promotion_types[] = new PromotionType($row);
		}
		return $this->promotion_types; 
	}
	public function set_promotion_types($promotion_types) { $this->promotion_types = $promotion_types; }
	public function get_countries() 
	{ 
		if(is_null($this->countries) || !is_array($this->countries))
		{
			$this->countries = array();
			$query = "SELECT * FROM `promotion_country` WHERE _promotion_id  = '" . $this->get_id() . "'";
			$result = mysql_query($query); if(mysql_errno()>0) trigger_error("+++ ".mysql_errno().": ".mysql_error()." +++");
			while($row = mysql_fetch_object($result))
				$this->countries[] = $row->country;
		}
		return $this->countries; 
	}
	public function set_countries($countries) { $this->countries = $countries; }
}

?>