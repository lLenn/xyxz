<?php

class PromotionDataManager
{
	/*
	 * Function retrieve_promotion_from_post()
	 * Retrieves the promotion from the post and validates the input
	 */
	public static function retrieve_promotion_from_post()
	{
		$output = "";
		$skipped_output = "";
		$promotion = new Promotion();
		
		// VALIDATE THE ARTICLE INPUT
		if(isset($_REQUEST["article_code"]) && $_REQUEST["article_code"] != '')
		{
			$promotion->set_article_code($_REQUEST["article_code"]);
		}
		else
		{
			$output .= "Please select a good article.<br/>";
		}

		// VALIDATE THE DATES ( CORRECT SYNTAXT, BEGIN < END )	
		$pattern = '/(19|20)[0-9]{2}[\/]([1-9]|0[1-9]|1[012])[\/](3[01]|0[1-9]|[12][0-9]|[1-9])/';
		$match = preg_match($pattern, $_POST["start_date"], $matches);

		if(!isset($_POST["start_date"]) || !$match || $matches[0] != $_POST["start_date"])
		{
			$output .= "Please enter a correct start date<br/>";
		}
		$promotion->set_start_date(strtotime($_POST["start_date"]));

		$match = preg_match($pattern, $_POST["end_date"], $matches);
		if(!isset($_POST["end_date"]) || !$match || $matches[0] != $_POST["end_date"])
		{
			$output .= "Please enter a correct end date<br/>";
		}
		$promotion->set_end_date(strtotime($_POST["end_date"]));	
			
		if($promotion->get_start_date()>$promotion->get_end_date())
		{
			$output .= "Please make sure the start date is before the end date<br/>";
		}
			
		// VALIDATE THE CRITERIA
		if(!Criteria::validate_criteria($_POST["criteria"]))
		{
			$output .= "Please enter a correct criteria.<br/> Syntax is >, <, >=, <= or = with a number. Example: >= 15<br/>";
		}
		$promotion->set_criteria(preg_replace("/[ ]*/", "", $_POST["criteria"]));

		//VALIDATE COUNTRIES
		$countries = array();
		if(isset($_POST["countries"]) && is_array($_POST["countries"]))
		{
			foreach($_POST["countries"] as $country)
			{
				if(strlen($country) == 2)
				{
					$countries[] = $country;
				}
			}
		}
		if(count($countries)>0)
		{
			$promotion->set_countries($countries);
		}
		else
		{
			$output .= "Please select a country.<br/>";
		}
		
		//VALIDATE PROMOTIONS
		$validation = false;
		$promotion_types = array();
		$types = PromotionManager::get_promotion_types();
		foreach($types as $index => $type)
		{
			$extra_props = array();
			$values = array();
			switch($index)
			{
				case PromotionManager::DISCOUNT_EACH_PROMO: 
					$extra_props = array("countries" => $promotion->get_countries(), "article_number" => $promotion->get_article_code());
					$values = array("discount" => $_POST["type_".$index."_discount"]);
					break;
				case PromotionManager::FREE_ARTICLE_PROMO:
					$values = array("article" => $_POST["type_".$index."_article"], "quantity" => $_POST["type_".$index."_quantity"]);
					break;
			}
			try
			{	
				if(PromotionManager::validate_promotion_type($index, $values, $extra_props))
				{
					$promotion_type = PromotionDataManager::get_promotion_type_from_post($index);
					$validation = true;
					$promotion_types[] = $promotion_type;
				}
			}
			catch(Exception $e)
			{
				$output .= $e->getMessage() . "<br/>";
			}
		}
		if(!$validation)
		{
			$output .= "Please fill in the promotions correctly.<br/>";
		}
		$promotion->set_promotion_types($promotion_types);
		
		$conflict_type = "";
		$conflicts = true;
		if($output == "")
		{	
			$update_id = "";
			if(isset($_GET["update_promotion"]))
			{
				$update_id = $_GET["update_promotion"];
			}
			
			$conflicts = PromotionManager::is_criteria_valid_for_article($promotion, $update_id);
			if($conflicts!==true)
			{
				$output .= "Criteria is in conflict with another promotion for this article from the same country.<br/>
							Check if there's already a criteria with the same number and if <, >, <=, >=, = don't overlap.<br/>
							Example: <= 50 and = 50 will cause conflict, while > 50 and <= 50 will not.<br/>
							Or change the country for which this promotion is applicable.<br/>";
				$conflict_type = PromotionManager::CRITERIA_CONFLICT;
			}
			else
			{
				$conflicts = PromotionManager::are_promotion_types_valid_for_article($promotion, $update_id);
				if($conflicts!==true)
				{
					$output .= "The promotion is in conflict with another promotion for this article from the same country.<br/>
							Check if there's a promotion where the criteria amount is bigger and the promotion advantage is smaller
							or the criteria amount is smaller and the promotion advantage is bigger.<br/>
							Promotion advantage each = <br/>
							(the highest price in the price list of the common countries for a free article * the quantity) / criteria amount<br/>
							+<br/>
							discount<br/>";
				}
				$conflict_type = PromotionManager::PROMOTION_TYPE_CONFLICT;
			}
		}
			
		return array("promotion" => $promotion, "error" => $output, "conflicts" => array("type" => $conflict_type, "data" => $conflicts));
	}
	
	/*
	 * Function save_promotion
	 * Inserts or updates the promotion.
	 */
	public static function save_promotion($promotion)
	{
		//Save promotion properties
		$output = "";
		$query = "";
		if(isset($_GET["add_promotion"]))
			$query = "INSERT INTO `promotion` (article_code, start_date, end_date, criteria) VALUES ('".$promotion->get_article_code()."', '".$promotion->get_start_date()."', '".$promotion->get_end_date()."', '".$promotion->get_criteria()."')";
		elseif(isset($_GET["update_promotion"]))
			$query = "UPDATE `promotion` SET article_code = '".$promotion->get_article_code()."', start_date = '".$promotion->get_start_date()."', end_date = '".$promotion->get_end_date()."', criteria = '".$promotion->get_criteria()."' WHERE _id = '" . $_GET["update_promotion"]."'";
		if($query!="")
		{
			$result = mysql_query($query);
			if($result)
			{
				//Save the promotiontypes
				if(isset($_GET["add_promotion"]))
					$promotion_id = mysql_insert_id();
				elseif(isset($_GET["update_promotion"]))
					$promotion_id = $_GET["update_promotion"];
					
				if(isset($_GET["update_promotion"]))
				{
					$query = "DELETE FROM `promotion_type` WHERE _promotion_id = '" . $promotion_id . "'";
					$result &= mysql_query($query);
				}
				
				if(count($promotion->get_promotion_types()))
				{
					foreach ($promotion->get_promotion_types() as $promotion_type)
					{
						foreach ($promotion_type->get_values() as $index => $value)
						{
							$query = "INSERT INTO `promotion_type` (_promotion_id, type, value_name, value) VALUES ('".$promotion_id."', '".$promotion_type->get_type()."', '".$index."', '".$value."')";
							$result &= mysql_query($query);
						}
					}
				}

				if($result)
				{
					//Save the promotioncountries
					if(isset($_GET["update_promotion"]))
					{
						$query = "DELETE FROM `promotion_country` WHERE _promotion_id = '" . $promotion_id . "'";
						$result &= mysql_query($query);
					}
						
					foreach ($promotion->get_countries() as $country)
					{
						$query = "INSERT INTO `promotion_country` (_promotion_id, country) VALUES ('".$promotion_id."', '".$country."')";
						$result &= mysql_query($query);
					}
					if(!$result)
					{
						$output .= "Failed adding promotioncountries.";
					}
				}
				else
				$output .= "Failed saving promotiontypes.";
			}
			else
				$output .= "Failed saving promotion.";
				
			if(!$result && isset($_GET["add_promotion"]))
			{
				$query = "DELETE FROM `promotion` WHERE _id = '" . $promotion_id . "';";
				$result = mysql_query($query); if(mysql_errno()>0) trigger_error("+++ ".mysql_errno().": ".mysql_error()." +++");
				$query =  "DELETE FROM `promotion_country` WHERE _promotion_id = '" . $promotion_id . "';";
				$result .= mysql_query($query); if(mysql_errno()>0) trigger_error("+++ ".mysql_errno().": ".mysql_error()." +++");
				$query = "DELETE FROM `promotion_type` WHERE _promotion_id = '" . $promotion_id . "';";
				$result .= mysql_query($query); if(mysql_errno()>0) trigger_error("+++ ".mysql_errno().": ".mysql_error()." +++");
				break;
			}
		}
		return array("result"=>$result, "error"=>$output);
	}
	
	/*
	 * Function delete_promotion
	 * Deletes the promotion from all tables.
	 */
	public static function delete_promotion($promotion_id)
	{
		$query = "DELETE FROM `promotion` WHERE _id = '" . $promotion_id . "';";
		$result = mysql_query($query); if(mysql_errno()>0) trigger_error("+++ ".mysql_errno().": ".mysql_error()." +++");
		$query =  "DELETE FROM `promotion_country` WHERE _promotion_id = '" . $promotion_id . "';";
		$result .= mysql_query($query); if(mysql_errno()>0) trigger_error("+++ ".mysql_errno().": ".mysql_error()." +++");
		$query = "DELETE FROM `promotion_type` WHERE _promotion_id = '" . $promotion_id . "';";
		$result .= mysql_query($query); if(mysql_errno()>0) trigger_error("+++ ".mysql_errno().": ".mysql_error()." +++");
		$output = "";
		if(!$result)
			$output = "Deleting promotion failed.";
		return array("result"=>$result, "error"=>$output);
	}
	
	public static function get_promotion_type_from_post($index)
	{
		$promotion_type = new PromotionType();
		$promotion_type->set_type($index);
		switch($index)
		{
			case PromotionManager::DISCOUNT_EACH_PROMO:
					$promotion_type->set_values(array("discount" => $_POST["type_".$index."_discount"]));
					break;
			case PromotionManager::FREE_ARTICLE_PROMO:
					$promotion_type->set_values(array("article" => $_POST["type_".$index."_article"], "quantity" => $_POST["type_".$index."_quantity"]));
					break;
			default: throw new Exception("Promotiontype doesn't exist.");
		}
		return $promotion_type;
	}
	
	public static function get_where_condition_for_countries($countries, $add_and = false, $table_name = "")
	{
		$size = count($countries);
		$country_cond = "";
		if($size)
		{
			$country_cond = ($add_and?" AND ":" ") . ($table_name!=""?$table_name.".":"") . "country IN (";
			$i = 1;
			foreach($countries as $country)
			{
				$country_cond .= "'" . $country . "'";
				if($i<$size)
				$country_cond .= ",";
				$i++;
			}
			$country_cond .= ") ";
		}
		return $country_cond;
	}
	
}

?>