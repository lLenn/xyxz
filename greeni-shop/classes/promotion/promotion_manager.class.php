<?php

require_once '/../criteria.class.php';
require_once 'promotion_renderer.class.php';
require_once 'promotion_data_manager.class.php';
require_once 'promotion.class.php';

class PromotionManager
{
	CONST FREE_ARTICLE_PROMO = 1;
	CONST DISCOUNT_EACH_PROMO = 2;
	
	CONST CRITERIA_CONFLICT = 1;
	CONST PROMOTION_TYPE_CONFLICT = 2;
	
	/*
	 * Function calculate_promotions_for_order($order)
	 * Calculates the promotions for an order and adjusts the order accordingly
	 * Arguments: $order, the order from which the promotions are calculated.
	 * Return: $orders_array, the new orders that were calculated from the original order.
	 */
	static function calculate_promotions_for_order($order)
	{
		$now = time();
		// Retrieve the promotions for the order
		$query = "SELECT p.* FROM `promotion` as p 
						   LEFT JOIN `promotion_country` as c on p._id = c._promotion_id
						   LEFT JOIN `orders` as o ON o.artcode = p.article_code  
						   LEFT JOIN `users` as u ON u._id = o._user_id 
						   WHERE p.article_code = '" . $order->get_article_code() . "' AND p.start_date <= '" . $now . "' AND p.end_date >= '" . $now . "' AND c.country = u.country AND u._id = '" . $_SESSION["logged_in"] . "'";
		$results = mysql_query($query); if(mysql_errno()>0) trigger_error("+++ ".mysql_errno().": ".mysql_error()." +++");
		
		$promotions = array();
		while($data = mysql_fetch_object($results))
		{
			$promotions[] = new Promotion($data);
		}
			
		// Sort the promotions by criteria: the criteria with the highest amount first
		uasort($promotions, array(__CLASS__, "promotion_compare_by_criteria"));
		$promotions = array_reverse($promotions);
		
		// Check whether or not the promotion is applicable
		$temp_new_orders = array();
		$prom_copy = $promotions;
		foreach($promotions as $index => $promotion)
		{
			if($order->get_quantity()<=0)
				break;
			
			$amount = Criteria::get_amount_from_criteria($promotion->get_criteria());
			$cond = Criteria::get_condition_from_criteria($promotion->get_criteria());
			if($amount <= $order->get_quantity())
			{
				// If the amount of the promotion is smaller than or equal to the amount of the order then create new orders from the promotion
				// untill the quantity of the order is smaller than the promotion's criteria allows
				// If the condition of the criteria of the order is < or <= then stop the promotionloop
				while($amount <= $order->get_quantity())
				{
					$temp_new_orders = array_merge($temp_new_orders, self::create_new_orders_from_promotion($order, $promotion));
				}
				if(Criteria::get_condition_compare_value($cond)!=Criteria::get_condition_compare_value("="))
						break;
			}
			else
			{
				// If the amount of the promotion is bigger than the amount of the order check if the promotion has the < or <= cond
				// and if there isn't a promotion with a smaller amount in the criteria that is also legit
				// Otherwise skip the promotion.
				if(Criteria::get_condition_compare_value($cond)<Criteria::get_condition_compare_value("="))
				{
					$validation = false;
					$break_promotions = true;
					foreach($prom_copy as $index_2 => $promotion_2)
					{
						// Check if there isn't an other promotion with a smaller amount
						if($validation)
						{
							$cond_2 = Criteria::get_condition_from_criteria($promotion_2->get_criteria());
							$amount_2 = Criteria::get_amount_from_criteria($promotion_2->get_criteria());
							if((Criteria::get_condition_compare_value($cond_2)==Criteria::get_condition_compare_value("<") && $amount_2 > $order->get_quantity()) ||
							   (Criteria::get_condition_compare_value($cond_2)==Criteria::get_condition_compare_value("<=") && $amount_2 >= $order->get_quantity()))
							{
								$break_promotions = false;
								break;
							}
						}
						if($index == $index_2)
							$validation = true;
					}
					
					// If a better promotion wasn't found then create the new orders from the promotion and end the promotionloop
					if($break_promotions)
					{
						$temp_new_orders = array_merge($temp_new_orders, self::create_new_orders_from_promotion($order, $promotion));
						break;
					}
				}
			}
		}
		
		$promotion_order = new PromotionOrder();
		$promotion_order->set_id($order->get_id());
		$promotion_order->set_article_code($order->get_article_code());
		$promotion_order->set_price($order->get_price());
		$promotion_order->set_quantity($order->get_quantity());
		$promotion_order->set_old_price($order->get_price());
		$temp_new_orders[] = $promotion_order;
		
		//group orders of the same price
		$new_orders = array();
		foreach($temp_new_orders as $temp_new_order)
		{
			$validation = false;
			foreach($new_orders as $new_order)
			{
				if($new_order->get_price() == $temp_new_order->get_price() && $new_order->get_article_code() == $temp_new_order->get_article_code())
				{
					$new_order->set_quantity($new_order->get_quantity() + $temp_new_order->get_quantity());
					$validation = true;
				}
			}
			if(!$validation && $temp_new_order->get_quantity() != 0)
				$new_orders[] = $temp_new_order;
		}
		
		return $new_orders;
	}
	
	/*
	 * Function create_new_orders_from_promotion(&$order, $promotion)
	 * Creates new orders from the promotion and adjusts the original order.
	 * Arguments: $order, the original order. Gets adjusts during the runtime of the function to exlude all the new promotions that were created from it.
	 * 			  $promotion, the promotion to calculate the new orders from.
	 * Return: $orders_array, an array with the new orders
	 */
	static function create_new_orders_from_promotion(&$order, $promotion)
	{
		$orders_array = array();
		$amount = Criteria::get_amount_from_criteria($promotion->get_criteria());
		$cond = Criteria::get_condition_from_criteria($promotion->get_criteria());
		switch($cond)
		{
			case "All":
			case ">" :
			case ">=":
			case "<" :
			case "<=": $amount = $order->get_quantity();
					   $order->set_quantity(0);
					   break;
			case "=" : $order->set_quantity($order->get_quantity()-$amount);
					   break;
		}
		foreach($promotion->get_promotion_types() as $promotion_type)
		{
			$new_order = new PromotionOrder();
			$new_order->set_user_id($order->get_user_id());
			$new_order->set_time($order->get_time());
			$new_order->set_old_price($order->get_price());
			$values = $promotion_type->get_values();
			switch($promotion_type->get_type())
			{
				case self::FREE_ARTICLE_PROMO: $new_order->set_article_code($values['article']);
											   $new_order->set_price(0);
											   $new_order->set_quantity($values['quantity']);
											   $new_order->set_promotion_type(self::FREE_ARTICLE_PROMO);
											   break;
				case self::DISCOUNT_EACH_PROMO: $new_order->set_article_code($order->get_article_code());
												$new_order->set_price($order->get_price()-floatval($values['discount']));
												$new_order->set_quantity($amount);
											    $new_order->set_promotion_type(self::DISCOUNT_EACH_PROMO);
												break;
			}
			$orders_array[] = $new_order;
		}
		return $orders_array;
	}
	
	
	static function get_promotion_types()
	{
		$free_article_promo = "Free article(s)";
		if(defined("ID_FREE_ARTICLE_PROMO"))
			$free_article_promo = ID_FREE_ARTICLE_PROMO;
			
		$discount_each_promo = "Discount each";
		if(defined("ID_DISCOUNT_EACH_PROMO"))
			$discount_each_promo = ID_DISCOUNT_EACH_PROMO;

		return array(self::FREE_ARTICLE_PROMO => $free_article_promo,
					 self::DISCOUNT_EACH_PROMO => $discount_each_promo);
	}
	
	
	static function validate_promotion_type($promotion_type, $values = null, $extra_props = array())
	{
		switch($promotion_type)
		{
			case self::FREE_ARTICLE_PROMO :
				if(isset($values["article"]) && $values["article"] != "" && 
				   isset($values["quantity"]) && $values["quantity"] != "" && is_numeric($values["quantity"]) && $values["quantity"] > 0 && floor($values["quantity"]) == $values["quantity"])
				{
					return true;
				}
				else
				{
					return false;
				}
				break;
			case self::DISCOUNT_EACH_PROMO :
				if(isset($values["discount"]) && $values["discount"] != "" && is_numeric($values["discount"]))
				{
					if(!empty($extra_props) && isset($extra_props["countries"]) && is_array($extra_props["countries"]) && isset($extra_props["article_number"]))
					{
						$countries = $extra_props["countries"];
						$country_cond = PromotionDataManager::get_where_condition_for_countries($countries, true);
						$query = "SELECT min(price) as price FROM `articleprices` WHERE articlenumber = '" . $extra_props["article_number"] . "'" . $country_cond;
						$result = mysql_query($query); if(mysql_errno()>0) trigger_error("+++ ".mysql_errno().": ".mysql_error()." +++");
						$price = mysql_fetch_object($result)->price;
						if($price >= $values["discount"])
							return true;
						else
							throw new Exception("The given discount is bigger than the smallest price of the article in the pricelist of the given countries.");
					}
					else
						throw new Exception("Please give the countries and the articlenumber with the extra_props variable to validate the discount each promo.");
				}
				else
					return false;
				break;
			default: throw new Exception("Promotiontype doesn't exist.");
		}
	}
	
	static function is_criteria_valid_for_article($promotion, $update_id = "")
	{
		$country_cond = PromotionDataManager::get_where_condition_for_countries($promotion->get_countries(), true, "c");	
		
		$update_cond = $country_cond;
		if($update_id!="")
		{
			$update_cond .= " AND p._id <> '" . $update_id . "' ";
		}
		
		$criteria_cond = $update_cond . " AND " . Criteria :: get_validate_criteria_condition($promotion->get_criteria(), 'p');
		
		$query = "SELECT DISTINCT _id FROM `promotion` as p LEFT JOIN `promotion_country` as c ON p._id = c._promotion_id 
					WHERE NOT(
								(p.start_date > '" . $promotion->get_start_date() . "' AND p.start_date  > '" . $promotion->get_end_date() . "') ||
								(p.end_date < '" . $promotion->get_start_date() . "' AND p.end_date  < '" . $promotion->get_end_date() . "')
							 )
						  AND article_code = '" . $promotion->get_article_code() . "'" . $criteria_cond;
		$result = mysql_query($query); if(mysql_errno()>0) trigger_error("+++ ".mysql_errno().": ".mysql_error()." +++");
		
		$conflicting_promotions = array();
		while($data = mysql_fetch_object($result))
		{
			$conflicting_promotions[] = $data->_id;
		}

		if(count($conflicting_promotions) == 0)
		{
			return true;
		}
		else
		{
			return $conflicting_promotions;
		}
	}
	
	static function are_promotion_types_valid_for_article($promotion, $update_id = "")
	{
		$country_cond = PromotionDataManager::get_where_condition_for_countries($promotion->get_countries(), true, "c");	
		
		$update_cond = $country_cond;
		if($update_id!="")
		{
			$update_cond .= " AND p._id <> '" . $update_id . "' ";
		}
		
		$query = "SELECT DISTINCT p.* FROM `promotion` as p LEFT JOIN `promotion_country` as c ON p._id = c._promotion_id 
					WHERE NOT(
								(p.start_date > '" . $promotion->get_start_date() . "' AND p.start_date  > '" . $promotion->get_end_date() . "') ||
								(p.end_date < '" . $promotion->get_start_date() . "' AND p.end_date  < '" . $promotion->get_end_date() . "')
							 )
						  AND article_code = '" . $promotion->get_article_code() . "'" . $update_cond;

		$result = mysql_query($query); if(mysql_errno()>0) trigger_error("+++ ".mysql_errno().": ".mysql_error()." +++");
		$promotions = array();
		while($data = mysql_fetch_object($result))
		{
			$promotions[] = new Promotion($data);
		}
		
		$validation = true;
		$promotion_amount = Criteria::get_amount_from_criteria($promotion->get_criteria());
		$conficting_promotions = array();
		foreach($promotions as $promo)
		{
			$promo_amount = Criteria::get_amount_from_criteria($promo->get_criteria());
			if($promo_amount < $promotion_amount)
			{
				if(self::compare_promotions($promo, $promotion) > -1)
				{
					$validation = false;
					$conficting_promotions[] = $promo->get_id();
				}
			}
			elseif($promo_amount > $promotion_amount)
			{
				if(self::compare_promotions($promo, $promotion) < 1)
				{
					$validation = false;
					$conficting_promotions[] = $promo->get_id();
				}
			}
		}
		if($validation)
		{
			return true;
		}
		else
		{
			return $conficting_promotions;
		}
	}
	
	static function compare_promotions($promo_a, $promo_b)
	{
		$score_a = 0;
		$score_b = 0;
		foreach(self::get_promotion_types() as $index => $type)
		{
			$promo_type_a = null;
			$promo_type_b = null;
			for($i = 0; $i <= 1; $i++)
			{
				$type_x = "";
				switch($i)
				{
					case 0: $types_x = $promo_a->get_promotion_types();
							break;
					case 1: $types_x = $promo_b->get_promotion_types();
							break;
				} 
				foreach($types_x as $x)
				{
					if($x->get_type() == $index)
					{
						switch($i)
						{
							case 0: $promo_type_a = $x;
									break;
							case 1: $promo_type_b = $x;
									break;
						} 
					}
				}
			}
			
			switch($index)
			{
				case self::FREE_ARTICLE_PROMO:  $countries = array_intersect($promo_a->get_countries(), $promo_b->get_countries());
												$country_cond = PromotionDataManager::get_where_condition_for_countries($countries, true, "pri");
												if(!is_null($promo_type_a))
												{
													$values = $promo_type_a->get_values();
													$article = preg_replace("/\+\+/", "", $values["article"]);
													$query = "SELECT max(pri.price) as max FROM articleprices AS pri 
																LEFT JOIN catalogue AS cat ON cat.k2_numberOfRef=pri.articlenumber 
																WHERE cat.k2_numberOfRef = '" . $article . "'" . $country_cond;
													$result = mysql_query($query); if(mysql_errno()>0) trigger_error("+++ ".mysql_errno().": ".mysql_error()." +++");
													$score_a += ((mysql_fetch_object($result)->max)*$values["quantity"])/Criteria::get_amount_from_criteria($promo_a->get_criteria());
												}
												if(!is_null($promo_type_b))
												{
													$values = $promo_type_b->get_values();
													$article = preg_replace("/\+\+/", "", $values["article"]);
													$query = "SELECT max(pri.price) as max FROM articleprices AS pri 
																LEFT JOIN catalogue AS cat ON cat.k2_numberOfRef=pri.articlenumber 
																WHERE cat.k2_numberOfRef = '" . $values["article"] . "'" . $country_cond;
													$result = mysql_query($query); if(mysql_errno()>0) trigger_error("+++ ".mysql_errno().": ".mysql_error()." +++");
													$score_b += ((mysql_fetch_object($result)->max)*$values["quantity"])/Criteria::get_amount_from_criteria($promo_b->get_criteria()=="All"?$promo_a->get_criteria():$promo_b->get_criteria());
												}
												break;
				case self::DISCOUNT_EACH_PROMO: if(!is_null($promo_type_a))
												{
													$values = $promo_type_a->get_values();
													$score_a += floatval($values["discount"]);
												}
												if(!is_null($promo_type_b))
												{
													$values = $promo_type_b->get_values();
													$score_b += floatval($values["discount"]);
												}
												break;
			}
		}
		if($score_a == $score_b)
			return 0;
		else
			return $score_a<$score_b?-1:1; 
	}
	
	static function order_by_criteria(&$promotions)
	{
		uasort($promotions, array(__class__, 'self::promotion_compare_by_criteria'));
	}
	
	static function order_by_promotion_type(&$promotions)
	{
		uasort($promotions, array(__class__, 'self::promotion_compare_by_promotion_type'));
	}
	
	private function promotion_compare_by_criteria($a_prom, $b_prom)
	{
		$a = $a_prom->get_criteria();
		$b = $b_prom->get_criteria();
		return Criteria :: compare_by_criteria($a, $b);
	}
	
	private function promotion_compare_by_promotion_type($a_prom, $b_prom)
	{
		$a = $a_prom->get_promotion_type();
		$b = $b_prom->get_promotion_type();
		
		if($a == $b)
			return 0;
		else
			return ($a < $b) ? -1 : 1;
		
		trigger_error("+++ Can't sort promotion by promotiontype +++");
		exit();
	}
}
?>