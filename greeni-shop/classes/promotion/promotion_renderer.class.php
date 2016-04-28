<?php

require_once 'country_select.php';

class PromotionRenderer
{	
	/*
	 * Function render_form($promotion = null)
	 * Renders the form based on the $promotion.
	 * Arguments: $promotion = null, If promoton is null the form is rendered for an insert otherwise for an update.
	 */
	public static function render_form($promotion = null)
	{	
		$html = array();
		
		$html[] = '<form name="add_promotion" method="post">';
		$html[] = '<td valign="top" align="center" width="50px"><a href="javascript: document.forms.add_promotion.submit();"><img src="images/confirm.png" style="border: 0;"></a> <a href="manage_promotions.php"><img src="images/cancel.png" style="border: 0;"></a></td>';
		$html[] = '<td valign="top" align="center">';
		$html[] = "<select name='article_code' style='width: 110px'>";
		$query = "SELECT DISTINCT k2_numberOfRef, namegroup, subgroup, subsubgroup FROM catalogue WHERE lang = 'EN' AND k2_numberOfRef <> '-' ORDER BY k2_numberOfRef";
		$result = mysql_query($query);
		while($row = mysql_fetch_object($result))
		{
			$selected = "";
			if(!is_null($promotion) && $row->k2_numberOfRef == $promotion->get_article_code())
				$selected = " selected='selected'";
					
			$name = $row->k2_numberOfRef;
			if(!is_null($row->namegroup) && $row->namegroup != "" && $row->namegroup != "-")
				$name .= " - " .$row->namegroup;
			if(!is_null($row->subgroup) && $row->subgroup != "" && $row->subgroup != "-")
				$name .= " - " .$row->subgroup;
			if(!is_null($row->subsubgroup) && $row->subsubgroup != "" && $row->subsubgroup != "-")
				$name .= " - " .$row->subsubgroup;
			$html[] = "<option value='".$row->k2_numberOfRef."'".$selected.">".$name."</option>";
		}
		$html[] = "</select>";
		$html[] = "</td>";
		$html[] = '<td valign="top" align="center"><input name="start_date" type="text" size="12" '.(!is_null($promotion)?'value="'.date("Y/m/d",$promotion->get_start_date()).'"':'value="YYYY/MM/DD"').'/></td>';
		$html[] = '<td valign="top" align="center"><input name="end_date" type="text" size="12" '.(!is_null($promotion)?'value="'.date("Y/m/d",$promotion->get_end_date()).'"':'value="YYYY/MM/DD"').'/></td>';
		$html[] = '<td valign="top" align="center"><input name="criteria" type="text" size="12" '.(!is_null($promotion)?'value="'.$promotion->get_criteria().'"':'').'/></td>';
		$html[] = '<td valign="top" style="width: 210px">';
		$types = PromotionManager::get_promotion_types();
		foreach($types as $index => $type)
		{
			$values = null;
			if(!is_null($promotion))
			{ 
				foreach($promotion->get_promotion_types() as $promotion_type)
				{
					if($promotion_type->get_type() == $index)
					{
						$values = $promotion_type->get_values();
						break;
					}
				}
			}
			$html[] = '<div class="record record_title_table">' . $type . ':</div><div class="record">' . self::get_promotion_input($index, $values) . '</div><br class="clear_float">';
		}
		$html[] = '</td>';
		$html[] = '<td valign="top">';
		foreach (CountrySelect::retrieve_important_countries() as $index => $value)
		{		
			$selected = "";
			if(!is_null($promotion) && in_array($index, $promotion->get_countries()))
				$selected = " checked='checked'";
			$html[] = '<input type="checkbox" name="countries[]" value="'.$index.'"'.$selected.'> ' . $value . '<br>';
		}		
		$html[] = '</td>';
		
		$html[] = '</form>';
		return implode("\n", $html);
	}
	
	/*
	 * Function render_table($start = 0, $length = 50, $promotion)
	 * Renders the table with the promotions.
	 * Arguments: $start = 0, where the table begins rendering
	 * 			  $length = 50, how many records are rendered
	 * 			  $promotion, the promotion to be updated
	 */
	public static function render_table($start = 0, $length = 50, $promotion = null, $conflicts = null)
	{
		$html = array();
		
		$html[] = "<div>";
		$html[] = "<div style='float: left;'>Added promotions :</div>";
		$html[] = "<div style='float: right;'>";
		$lengths = array(20,50,100,150);
		$html[] = "<select name='length' style='margin-right: 2px'>";
		foreach($lengths as $val)
		{
			$html[] = "<option value='".$val."'".($length==$val?"selected":"").">".$val."</option>";
		}
		$html[] = '</select>';
		$html[] = '</div>';
		$html[] = '<br class="clear_float"/>';
		$html[] = '<table cellspacing="1" cellpadding="4" width="100%">';
		//HEADER
		$sort = null;
		if(isset($_GET["sort"]))
		{
			$sort = $_GET["sort"];
			$_SESSION["promotion_sort"] = $sort;
		}
		elseif(isset($_SESSION["promotion_sort"]))
		{
			$sort = $_SESSION["promotion_sort"];
		}
		$sort_image = "<img style='margin-left: 5px; margin-bottom: 1px;' src='images/sort.gif'>";
		if(isset($_GET["desc"]))
		{
			$sort_image = "<img style='margin-left: 5px; margin-bottom: 1px;'  src='images/sort_desc.gif'>";
		}
		$html[] = '<thead>';
        $html[] = '<tr id="t">';
        $html[] = '<th align="right"></th>';
        $html[] = '<th><a href="manage_promotions.php?sort=article_code'. ((isset($sort) && $sort=="article_code" && !isset($_GET["desc"]))?"&desc":"") .'">Article code</a>'. (isset($sort) && $sort=="article_code"?" $sort_image":"") .'</th>';
        $html[] = '<th><a href="manage_promotions.php?sort=start_date'. ((((isset($sort) && $sort=="start_date") || !isset($sort)) && !isset($_GET["desc"]))?"&desc":"") .'">Start date</a>'. ((isset($sort) && $sort=="start_date") || !isset($sort)?" $sort_image":"") .'</th>';
        $html[] = '<th><a href="manage_promotions.php?sort=end_date'. ((isset($sort) && $sort=="end_date" && !isset($_GET["desc"]))?"&desc":"") .'">End date</a>'. (isset($sort) && $sort=="end_date"?" $sort_image":"") .'</th>';
        $html[] = '<th><a href="manage_promotions.php?sort=criteria'. ((isset($sort) && $sort=="criteria" && !isset($_GET["desc"]))?"&desc":"") .'">Criteria</a>'. (isset($sort) && $sort=="criteria"?" $sort_image":"") .'</th>';
        $html[] = '<th><a href="manage_promotions.php">Promotions</th>';
        $html[] = '<th><a href="manage_promotions.php?sort=countries">Countries</th>';
        $html[] = '</tr>';
		$html[] = '</thead>';
        
        //TBODY SHOW PROMOTIONS
        $rows = array();
        $conditions = "";
        if(isset($_GET["view_past"]))
       		$conditions = "WHERE end_date < '" . time() . "' ";
        elseif(isset($_GET["view_present"]))
       		$conditions = "WHERE start_date < '" . time() . "' AND end_date > '" . time() . "' ";
        elseif(isset($_GET["view_future"]))
       		$conditions = "WHERE start_date > '" . time() . "' ";
        $query = "SELECT * FROM `promotion` " . $conditions . (isset($sort)?"ORDER BY " . $sort:"ORDER BY start_date") . ((isset($sort) && isset($_GET["desc"]))?" DESC":"") . " LIMIT " . $start . ", " . ($length + 1);
        $result = mysql_query($query);
        $count = 0;
        $next = false;
        $donker = true;
        
        if(!is_null($conflicts) && is_array($conflicts) && isset($conflicts["data"]) && is_array($conflicts["data"]))
			$conflicts_html = self::render_conflict_rows($conflicts);
        
        $html[] = "<tr id='h'>"; 
        if(isset($_GET["add_promotion"]))
        {
			$html[] = self::render_form($promotion);
			if(isset($conflicts_html))
				$html[] = $conflicts_html;
        }
		$html[] = "</tr>";
		
        while($data = mysql_fetch_object($result)) 
        {
        	if($count<$length)
        	{
        		$promotion_obj = new Promotion($data);
	            if ($donker) 
	            	$html[] = "<tr id='d'>"; 
	            else 
	            	$html[] = "<tr id='h'>"; 
	            $donker = !$donker;
		        
				if(isset($_GET["update_promotion"]) && $_GET["update_promotion"] == $promotion_obj->get_id())
		    	{
		    		//RETRIEVE MESSAGE FOR UPDATE
		    		if(is_null($promotion))
		    		{
		    			$promotion = $promotion_obj;
		    		}
		 	 	  	$html[] = self::render_form($promotion);
					if(isset($conflicts_html))
					{
						$html[] = $conflicts_html;
					}
		 	   }
		 	   else
		 	   {
		            $html[] = "<td valign='top' align='center' width='50px'>";
		            $html[] = "<a href='manage_promotions.php?update_promotion=".$promotion_obj->get_id()."'><img src='images/edit.png' border='0' alt='Edit'></a>&nbsp;<a href='Javascript: conf(\"Are you sure you want to delete this promotion?\", \"manage_promotions.php?delete_promotion=".$promotion_obj->get_id()."\");'><img src='images/delete.png' style='border: 0;'></a>";
		            $html[] = "</td>";
		            $html[] = "<td width='200px' align='center' valign='top' style='padding: 3px 7px'>";
		            $html[] = $promotion_obj->get_article_code();
		            $html[] = "</td>";
		            $html[] = "<td valign='top' align='center' style='padding: 3px 7px'>";
		            $html[] = date("Y/m/d", $promotion_obj->get_start_date());
		            $html[] = "</td>";
		            $html[] = "<td valign='top' align='center' style='padding: 3px 7px'>";
		            $html[] = date("Y/m/d", $promotion_obj->get_end_date());
		            $html[] = "</td>";
		            $html[] = "<td valign='top' align='center' style='padding: 3px 7px'>";
		            $html[] = $promotion_obj->get_criteria();
		            $html[] = "</td>";
		            $html[] = "<td valign='top' style='padding: 3px 7px; width: 210px;'>";
		            foreach($promotion_obj->get_promotion_types() as $promotion_type)
		            {
		            	foreach(PromotionManager::get_promotion_types() as $index => $type_string)
		            	{
		            		if($index == $promotion_type->get_type())
		            		{
								$html[] = '<div class="record" style="width: 100px;">' . $type_string . ': </div><br class="clear_float">';
								$html[] = '<div class="record" style="margin-left: 10px;">';
								foreach($promotion_type->get_values() as $index => $value)
								{
									$html[] = strtoupper(substr($index, 0, 1)) . substr($index, 1) . ': ' . $value . '<br>';
								}
								$html[] = '</div><br class="clear_float">';
								break;
		            		}
		            	}
		            }
		            $html[] = "</td>";
		            $html[] = "<td align='right' valign='top' style='padding: 3px 7px'>";
		            foreach($promotion_obj->get_countries() as $country)
		            {
		            	foreach(CountrySelect::retrieve_important_countries() as $c_index => $country_string)
		            	{
		            		if($country == $c_index)
		            		{
								$html[] = $country_string . '<br>';
								break;
		            		}
		            	}
		            }
		            $html[] = "</td>";
		 	   }
	           $html[] = "</tr>";        	
        	}
	        else if($count == $length)
        		$next = true;
        	$count++;
        }
		$html[] = "</table>";
		$html[] = "<br/>";
		if($start - $length >= 0)
			$html[] = "<div style='float: left; margin-left: 1px;'><a href='manage_promotions.php?start=".($start - $length)."'> <<< Previous records</a></div>";
		if($next)
			$html[] = "<div style='float: right; margin-right: 1px;'><a href='manage_promotions.php?start=".($start + $length)."'>Next records >>> </a></div>";
		$html[] = "<br class='clear_float'/>";
		$html[] = "</div>";
		return implode("\n", $html);
	}
	
	/*
	 * Function render_conflict_table()
	 * Renders the table with the promotions.
	 */
	public static function render_conflict_rows($conflicts)
	{
		$html = array();
		
        $html[] = '<tr id="t">';
        $html[] = '<th align="left" colspan="7" style="padding-left:20px">Conflicts</th>';
        $html[] = '</tr>';
        
		$size = count($conflicts["data"]);
		$con_cond = "";
		if($size)
		{
			$con_cond = " _id IN (";
			$i = 1;
			foreach($conflicts["data"] as $con)
			{
				$con_cond .= "'" . $con . "'";
				if($i<$size)
				$con_cond .= ",";
				$i++;
			}
			$con_cond .= ") ";
		}
		
		$query = "SELECT * FROM `promotion` WHERE" . $con_cond;
		$result = mysql_query($query);
		$donker = true;
        while($data = mysql_fetch_object($result)) 
        {
        	$promotion_obj = new Promotion($data);
        	if ($donker)
        		$html[] = "<tr id='d'>";
        	else
        		$html[] = "<tr id='h'>";
        	$donker = !$donker;

        	$html[] = "<td valign='top' align='center' width='50px'>";
        	$html[] = "</td>";
        	$html[] = "<td width='200px' align='center' valign='top' style='padding: 3px 7px'>";
        	$html[] = $promotion_obj->get_article_code();
        	$html[] = "</td>";
        	$html[] = "<td valign='top' align='center' style='padding: 3px 7px'>";
        	$html[] = date("Y/m/d", $promotion_obj->get_start_date());
        	$html[] = "</td>";
        	$html[] = "<td valign='top' align='center' style='padding: 3px 7px'>";
        	$html[] = date("Y/m/d", $promotion_obj->get_end_date());
        	$html[] = "</td>";
        	$html[] = "<td valign='top' align='center' style='padding: 3px 7px;".($conflicts["type"] == PromotionManager::CRITERIA_CONFLICT?"background-color: #fd7152;":"")."'>";
        	$html[] = $promotion_obj->get_criteria();
        	$html[] = "</td>";
        	$html[] = "<td valign='top' style='padding: 3px 7px; width: 210px;".($conflicts["type"] == PromotionManager::PROMOTION_TYPE_CONFLICT?"background-color: #fd7152;":"")."'>";
        	foreach($promotion_obj->get_promotion_types() as $promotion_type)
        	{
        		foreach(PromotionManager::get_promotion_types() as $index => $type_string)
        		{
        			if($index == $promotion_type->get_type())
        			{
        				$html[] = '<div class="record" style="width: 100px;">' . $type_string . ': </div><br class="clear_float">';
						$html[] = '<div class="record" style="margin-left: 10px;">';
        				foreach($promotion_type->get_values() as $index => $value)
        				{
        					$html[] = strtoupper(substr($index, 0, 1)) . substr($index, 1) . ': ' . $value . '<br>';
        				}
        				$html[] = '</div><br class="clear_float">';
        				break;
        			}
        		}
        	}
        	$html[] = "</td>";
        	$html[] = "<td align='right' valign='top' style='padding: 3px 7px'>";
        	foreach($promotion_obj->get_countries() as $country)
        	{
        		foreach(CountrySelect::retrieve_important_countries() as $c_index => $country_string)
        		{
        			if($country == $c_index)
        			{
        				$html[] = $country_string . '<br>';
        				break;
        			}
        		}
        	}
        	$html[] = "</td>";
        	$html[] = "</tr>";
        }
		
        $html[] = '<tr id="t">';
        $html[] = '<th align="right" colspan="7"></th>';
        $html[] = '</tr>';

		return implode("\n", $html);
	}
	
	public static function get_promotion_input($promotion_type, $values = null)
	{
		$html = array();
		switch($promotion_type)
		{
			case PromotionManager::FREE_ARTICLE_PROMO :
				$value_article = "";
				if(isset($_POST["type_".$promotion_type."_article"]))
				{
					$value_article = $_POST["type_".$promotion_type."_article"];
				}
				elseif(!is_null($values) && isset($values["article"]))
				{
					$value_article = $values["article"];
				}
				$html[] = "<select name='type_".$promotion_type."_article' style='width: 110px'>";
				$html[] = "<option></option>";
				$query = "SELECT DISTINCT k2_numberOfRef, namegroup, subgroup, subsubgroup FROM catalogue WHERE lang = 'EN' AND k2_numberOfRef <> '-' ORDER BY k2_numberOfRef";
				$result = mysql_query($query);
				while($row = mysql_fetch_object($result))
				{
					$selected = "";
					if($row->k2_numberOfRef == $value_article)
						$selected = " selected='selected'";
					
					$name = $row->k2_numberOfRef;
					if(!is_null($row->namegroup) && $row->namegroup != "" && $row->namegroup != "-")
						$name .= " - " .$row->namegroup;
					if(!is_null($row->subgroup) && $row->subgroup != "" && $row->subgroup != "-")
						$name .= " - " .$row->subgroup;
					if(!is_null($row->subsubgroup) && $row->subsubgroup != "" && $row->subsubgroup != "-")
						$name .= " - " .$row->subsubgroup;
					$html[] = "<option value='".$row->k2_numberOfRef."'.$selected.'>".$name."</option>";
				}
				$html[] = "</select><br>";
				$value_quantity = "";
				if(isset($_POST["type_".$promotion_type."_quantity"]))
				{
					$value_quantity = " value='" . $_POST["type_".$promotion_type."_quantity"] . "'";
				}
				elseif(!is_null($values) && isset($values["quantity"]))
				{
					$value_quantity = " value='" . $values["quantity"] . "'";
				}
				$html[] = '# <input name="type_'.$promotion_type.'_quantity" type="text" size="4"'.$value_quantity.'/>';
				break;
			case PromotionManager::DISCOUNT_EACH_PROMO :
				$default_value = "";
				if(isset($_POST["type_".$promotion_type."_discount"]))
				{
					$default_value = " value='" . $_POST["type_".$promotion_type."_discount"] . "'";
				}
				elseif(!is_null($values) && isset($values["discount"]))
				{
					$default_value = " value='" . $values["discount"] . "'";
				}
				$html[] = '<input name="type_'.$promotion_type.'_discount" type="text" size="12"'.$default_value.'/>';
				break;
			default: trigger_error("+++ Promotiontype doesn't exist. +++");
		}
		return implode("\n", $html);
	}
	
	public static function get_promotion_output($promotion_type, $values = null, $extra_props = array())
	{
		$html = array();
		$types = PromotionManager::get_promotion_types();
		$name = $types[$promotion_type];
		switch($promotion_type)
		{
			case PromotionManager::FREE_ARTICLE_PROMO :
				if(is_array($values) && isset($values["article"]) && isset($values["quantity"]))
				{
					$query = "SELECT c.k1_artikel_id FROM `catalogue` as c WHERE c.k2_numberOfRef = '" . $values["article"] . "' AND c.lang = '" . $_SESSION["language"] . "'";
					$result = mysql_query($query);
					$data = mysql_fetch_object($result);
					return $name . ": <div style='margin-left: 10px;'>" . $values["quantity"] . " x <br><a href='show.php?goto=" . $data->k1_artikel_id . "'>" . $values["article"] . "</a></div>";
				}
				else
				{
					throw new Exception("Corrupt values for this promotion.");
				}
				break;
			case PromotionManager::DISCOUNT_EACH_PROMO :
				if(is_array($values) && isset($values["discount"]) && is_array($extra_props) && isset($extra_props["article_number"]))
				{
					$query = "SELECT currency FROM `articleprices` WHERE articlenumber = '" . $extra_props["article_number"] . "' AND country = '" . $_SESSION["country"] . "'";
					$result = mysql_query($query);
					$data = mysql_fetch_object($result);
					return $name . ": <div style='margin-left: 20px;'>" . $values["discount"] . " " . $data->currency . "</div>";
				}
				else
				{
					throw new Exception("Corrupt values for this promotion.");
				}
				break;
			default: trigger_error("+++ Promotiontype doesn't exist. +++");
		}
	}
	
	public static function get_promotion_order_price_output($promotion_order)
	{
		$html = array();
		switch($promotion_order->get_promotion_type())
		{
			case PromotionManager::FREE_ARTICLE_PROMO :
				return $promotion_order->get_price();
				break;
			case PromotionManager::DISCOUNT_EACH_PROMO :
				return $promotion_order->get_price() - $promotion_order->get_old_price();
				break;
			default: trigger_error("+++ Promotiontype doesn't exist. +++");
		}
	}
}
?>