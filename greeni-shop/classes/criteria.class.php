<?php

class Criteria
{
	public static function order_rows_by_criteria(&$rows)
	{
		uasort($rows, array(__class__, 'self::row_compare_by_criteria'));
	}
	
	private function row_compare_by_criteria($a_prom, $b_prom)
	{
		$a = $a_prom->criteria;
		$b = $b_prom->criteria;
		return self::compare_by_criteria($a, $b);
	}
	
	public static function validate_criteria($criteria, $double = false)
	{
		$pattern = '/(?:(' . ($double?'[0-9]+[ ]*->|':'') . '<|>|<=|>=|=)[ ]*[0-9]+|All)/';
		$match = preg_match($pattern, $criteria, $matches);
		if(!isset($criteria) || !$match || $matches[0] != $criteria)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	public static function compare_by_criteria($a, $b)
	{
		if(self::validate_criteria($a, true) && self::validate_criteria($b, true))
		{
			$a_amount = self::get_amount_from_criteria($a);
			$b_amount = self::get_amount_from_criteria($b);
			if($a_amount !== false && $b_amount !== false)
			{
				if(!is_array($a_amount))
				{
					$a_amount = array($a_amount);
				}
				if(!is_array($b_amount))
				{
					$b_amount = array($b_amount);
				}
				if($a_amount[0] == $b_amount[0] || 
				   (count($a_amount) == 2 && $a_amount[1] == $b_amount[0]) || 
				   (count($b_amount) == 2 && $a_amount[0] == $b_amount[1]) || 
				   (count($a_amount) == 2 && count($b_amount) == 2 && ($a_amount[0] == $b_amount[1] || $a_amount[1] == $b_amount[0])))
				{
					$a_cond = self::get_condition_from_criteria($a);
					$b_cond = self::get_condition_from_criteria($b);
					if($a_cond !== false && $b_cond !== false)
				    {
				       	if(self::get_condition_compare_value($a_cond) == self::get_condition_compare_value($b_cond))
				       	{
				       		return 0;
				       	}
				       	else
				       	{
				   			return (self::get_condition_compare_value($a_cond) < self::get_condition_compare_value($b_cond)) ? -1 : 1;
				       	}
				   	}
				}
				else
				{
			    	if((count($a_amount) == 2 && $a_amount[1] < $b_amount[0])
			    		|| $a_amount[0] < $b_amount[0])
			    	{
			    		return -1;
			    	}
			    	else
			    	{
			    		return 1;
			    	}
				}	
			}
		}
		
		trigger_error("+++ Can't sort by criteria +++");
		exit();
	}
	
	public static function get_criteria_from_double($double)
	{
		$criteria = array();
		$pattern = '/[0-9]+[ ]*->[ ]*[0-9]+/';
		$match = preg_match($pattern, $double, $matches);
		if(!isset($double) || !$match || $matches[0] != $double)
		{
			return false;
		}
		else
		{
			$pattern = '/[0-9]+/';
			$match = preg_match_all($pattern, $double, $matches);
			$criteria[] = ">=" . $matches[0][0];
			$criteria[] = "<=" . $matches[0][1];
			return $criteria;
		}
	}
	
	public static function get_double_from_criteria($criteria)
	{
		if(!is_array($criteria) || !self::validate_criteria($criteria[0]) || !self::validate_criteria($criteria[1]))
		{
			return false;
		}
		else
		{
			return self::get_amount_from_criteria($criteria[0]) . "->" . self::get_amount_from_criteria($criteria[1]);
		}
	}
	
	public static function get_amount_from_criteria($criteria)
	{
		$is_double = preg_match("/[0-9]+[ ]*->[ ]*[0-9]+/", $criteria);
		$amount_match = preg_match("/[><=][ ]*[0-9]+/", $criteria, $amount_matches);
		if(count($amount_matches) == 1)
		{
			$amount = intval(substr($amount_matches[0], 1));
			if(!$is_double)
			{
				return $amount;
			}
			else
			{
				$amount_match = preg_match("/[0-9]+[ ]*-/", $criteria, $amount_matches);
				if(count($amount_matches) == 1)
				{
					return array(intval($amount_matches[0]), $amount);
				}
				else
				{
					return false;
				}
			}
		}
		elseif($criteria == 'All')
		{
			return 1;
		}
		else 
		{
			return false;
		}
	}
	
	public static function get_condition_from_criteria($criteria)
	{
		$cond_match = preg_match("/(?:[-]?[<>=][=]?[0-9 ]|All)/", $criteria, $cond_matches);
		if(count($cond_matches) == 1 && $cond_matches[0] != 'All')
		{
			return substr($cond_matches[0], 0, strlen($cond_matches[0])-1);
		}
		elseif(count($cond_matches) == 1 && $cond_matches[0] == 'All')
		{
			return 'All';
		}
		else
		{
			return false;
		}		
	}
	
	public static function get_validate_criteria_condition($criteria, $table_alias, $column_name = 'criteria', $double = false)
	{
		$cond = Criteria::get_condition_from_criteria($criteria);
		$amount = Criteria::get_amount_from_criteria($criteria);

		$criteria_cond = "";
		
		if(!is_array($amount))
		{
			$amount = array($amount);
		}
		
		switch($cond)
		{
			case ">": 	$criteria_cond .= "( " . $table_alias . "." . $column_name . " = '>" . $amount[0] . "' OR " . $table_alias . "." . $column_name . " = '>=" . $amount[0] . "')";
						if($double)
						{
							$criteria_cond .= " OR (SUBSTRING_INDEX(" . $table_alias . "." . $column_name . ", '->', -1) > " . $amount[0] . " AND SUBSTRING_INDEX(" . $table_alias . "." . $column_name . ", '->', 1) <= " . ($amount[0]+1) . ")";
						}
						break;
			case ">=": 	$criteria_cond .= "( " . $table_alias . "." . $column_name . " = '>" . $amount[0] . "' OR " . $table_alias . "." . $column_name . " = '>=" . $amount[0] . "' OR " . $table_alias . "." . $column_name . " = '=" . $amount[0] . "' OR " . $table_alias . "." . $column_name . " = '<=" . $amount[0] . "')";
						if($double)
						{
							$criteria_cond .= " OR (SUBSTRING_INDEX(" . $table_alias . "." . $column_name . ", '->', -1) >= " . $amount[0] . " AND SUBSTRING_INDEX(" . $table_alias . "." . $column_name . ", '->', 1) <= " . $amount[0] . ")";
						}
						break;
			case "=": 	$criteria_cond .= "( " . $table_alias . "." . $column_name . " = '>=" . $amount[0] . "' OR " . $table_alias . "." . $column_name . " = '=" . $amount[0] . "' OR " . $table_alias . "." . $column_name . " = '<=" . $amount[0] . "')";
						if($double)
						{
							$criteria_cond .= " OR (SUBSTRING_INDEX(" . $table_alias . "." . $column_name . ", '->', 1) <= " . $amount[0] . " AND SUBSTRING_INDEX(" . $table_alias . "." . $column_name . ", '->', -1) >= " . $amount[0] . ")";
						}
						break;
			case "<=": 	$criteria_cond .= "( " . $table_alias . "." . $column_name . " = '>=" . $amount[0] . "' OR " . $table_alias . "." . $column_name . " = '=" . $amount[0] . "' OR " . $table_alias . "." . $column_name . " = '<=" . $amount[0] . "' OR " . $table_alias . "." . $column_name . " = '<" . $amount[0] . "')";
						if($double)
						{
							$criteria_cond .= " OR (SUBSTRING_INDEX(" . $table_alias . "." . $column_name . ", '->', 1) <= " . $amount[0] . " AND SUBSTRING_INDEX(" . $table_alias . "." . $column_name . ", '->', -1) >= " . $amount[0] . ")";
						}
						break;
			case "<": 	$criteria_cond .= "( " . $table_alias . "." . $column_name . " = '<=" . $amount[0] . "' OR " . $table_alias . "." . $column_name . " = '<" . $amount[0] . "')";
						if($double)
						{
							$criteria_cond .= " OR (SUBSTRING_INDEX(" . $table_alias . "." . $column_name . ", '->', 1) < " . $amount[0] . " AND SUBSTRING_INDEX(" . $table_alias . "." . $column_name . ", '->',-1) >= " . ($amount[0]-1) . ")";
						}
						break;
			case "->": 	$criteria_cond .= " NOT (SUBSTRING_INDEX(" . $table_alias . "." . $column_name . ", '->', 1) > " . $amount[0] . " AND SUBSTRING_INDEX(" . $table_alias . "." . $column_name . ", '->', 1) > " . $amount[1] .
							   			  " OR SUBSTRING_INDEX(" . $table_alias . "." . $column_name . ", '->', -1) < " . $amount[0] . " AND SUBSTRING_INDEX(" . $table_alias . "." . $column_name . ", '->', -1) < " . $amount[1] . ")";
						break;
			case "All": $criteria_cond .= $table_alias . "." . $column_name . " = 'All'";
						break;
		}
		
		return $criteria_cond;
	}
	
	public static function get_criteria_output($criteria)
	{
		$cond = self::get_condition_from_criteria($criteria);
		$amount = self::get_amount_from_criteria($criteria);
		$criteria_output = "";
		switch($cond)
		{
			case ">" : $criteria_output .= " " . ID_BIGGER;
				break;
			case ">=" : $criteria_output .= " " . ID_BIGGER_EQUAL;
				break;
			case "=<" : $criteria_output .= " " . ID_SMALLER_EQUAL;
				break;
			case "<" : $criteria_output .= " " . ID_SMALLER;
				break;
			case "All": return ID_ALL;
		}
		return $criteria_output . " " . $amount . " " . ID_PIECES;
	}
	
	public function get_condition_compare_value($condition)
	{
		switch($condition)
		{
			case ">": return 50;
					  break;
			case ">=": return 40;
					   break;
			case "=": return 30;
					  break;
			case "->": 	return 30;
					  	break;
			case "<=": return 20;
					   break;
			case "<": return 10;
					  break;
			case "All": return 0;
					  	break;
			default: return 0;
		}
	}
	
	public function add_spaces_to_criteria($criteria)
	{
		$match = preg_match("/All/", $criteria);
		if($match)
		{
			if(defined("ID_ALL"))
			{
				return ID_ALL;
			}
			else
			{
				return "All";
			}
		}
		$match = preg_match_all("/(?:[0-9]*|[-<>=]*)/", $criteria, $matches);
		return implode(" ", $matches[0]);
	}
	
}