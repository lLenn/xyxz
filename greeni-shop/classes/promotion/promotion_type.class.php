<?php

class PromotionType
{
	
	private $promotion_id;
	private $type;
	
	private $values = null;
	
	function PromotionType($data=null)
	{
		if(!is_null($data))
		{
			$this->fill_from_database($data);
		}
	}
	
	public function fill_from_database($data)
	{
		$this->promotion_id = $data->_promotion_id;
		$this->type = $data->type;
	}
	
	public function get_promotion_id() { return $this->promotion_id; }
	public function set_promotion_id($promotion_id) { $this->promotion_id = $promotion_id; }
	public function get_type() { return $this->type; }
	public function set_type($type) { $this->type = $type; }
	public function get_values()
	{
		if(is_null($this->values) || !is_array($this->values))
		{
			$this->values= array();
			$query = "SELECT value_name, value FROM `promotion_type` WHERE _promotion_id  = '" . $this->get_promotion_id() . "' AND type = '" . $this->type . "'";
			$result = mysql_query($query); if(mysql_errno()>0) trigger_error("+++ ".mysql_errno().": ".mysql_error()." +++");
			while($row = mysql_fetch_object($result))
			{
				$this->values[$row->value_name] = $row->value;
			}
		} 
		return $this->values; 
	}
	public function set_values($values) { $this->values = $values; }

	
}

?>