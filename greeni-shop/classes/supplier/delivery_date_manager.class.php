<?php

require_once 'embedded/template.class.php';
require_once 'delivery_date.class.php';

class DeliveryDateManager
{
	const ALL_IN_ONE = 1;
	const SPLIT = 2;
	const WITH_NEXT = 3;
	
	private static $delivery_dates = null;
	
	public static function get_delivery_methods()
	{
		return array(self::ALL_IN_ONE => "ALL_IN_ONE", self::SPLIT => "SPLIT", self::WITH_NEXT => "WITH_NEXT");
	}
	
	public static function get_selector($name, $id, $style = "")
	{
		$html = array();		
		$html[] = '<select name="'.$name.'" style="' . $style . '">';
		foreach(self::get_delivery_methods() as $index => $value)
		{
			$str = '<option value="'.$index.'"';
			if($id == $index)
			{
				$str .= " selected='selected'";
			}
			$str .= ">" . constant("ID_" . $value) . "</option>";
			$html[] = $str;
		}
		$html[] = '</select>';
		return implode("\n", $html);
	}
	
	public static function get_delivery_method_output($id, $admin)
	{
		if($admin)
		{
			switch($id)
			{
				case self::ALL_IN_ONE: return "Together with order.";
									   break;
				case self::WITH_NEXT:  return "Send with next order.";
									   break;
				case self::SPLIT:  	   return "Hold untill all articles have arrived.";
									   break;
			}
		}
		elseif($id)
		{
			$delivery_method = DeliveryDateManager::get_delivery_methods();
			return constant("ID_" . $delivery_method[$id]);
		}
	}
	
	public static function render_form($delivery_date = null)
	{
		$html = array();
		$html[] = '<form name="delivery_date" style="display:inline" method="post">';
		$html[] = "<div class='record_title record'>Supplier :</div>";
		$suppliers = SupplierManager :: get_all_suppliers();
		$html[] = "<div class='record'>";
		if(!is_null($delivery_date))
		{
			$html[] = SupplierManager :: get_supplier_name($delivery_date->get_supplier_id());
			$html[] = "<input type='hidden' value='" . $delivery_date->get_supplier_id() . "' name='supplier_id'>";
		}
		else
		{
			$html[] = "<select name='supplier_id'>";
			foreach($suppliers as $index => $supplier)
			{
				$html[] = "<option value = '" . $index . "'>" . $supplier->name . "</option>";
			}
			$html[] = "</select>";
		}
		$html[] = "</div><br class='clear_float'>";
		
		$html[] = '<div class="record_title record">Delivery date</div><div class="record"><input type="radio" name="date_radio" value="date"'.(!is_null($delivery_date) && $delivery_date->get_period()?'':'checked').'/> Specific date?</div><br class="clear_float"/>';
		$html[] = '<div class="record_title record"></div><div class="record"><input type="radio" name="date_radio" value="period"'.(!is_null($delivery_date) && $delivery_date->get_period()?'checked':'').'/> Specific timespan after order?</div><br class="clear_float"/>';
			
		$html[] = '<div id="date" '.(!is_null($delivery_date) && $delivery_date->get_period()?'style="display: none;"':'').'><div class="record_title record">Specify date : </div><div class="record"><input name="date_time" type="text" size="10" ' . (!is_null($delivery_date) && !$delivery_date->get_period()?'value="' . date("Y/m/d", $delivery_date->get_date()) . '"':'') . '/> (YYYY/MM/DD or DD-MM-YYYY)</div></div><br class="clear_float"/>';
		$html[] = '<div id="period" '.(!is_null($delivery_date) && $delivery_date->get_period()?'':'style="display: none;"').'><div class="record_title record"># of days : </div><div class="record"><input name="date_period" type="text" size="10" ' . (!is_null($delivery_date) && $delivery_date->get_period()?'value="' . ($delivery_date->get_date()/24/60/60) . '"':'') . '/></div></div><br class="clear_float"/>';
		
		$html[] = '<input type="submit" name="submit_delivery_date" style="font-size:11px; vertical-align:middle; margin: 3px;" value="'.(is_null($delivery_date)?'Add delivery date':'Update delivery date').'"/>';
		$html[] = '</form>';
		return implode("\n", $html);
	}
	
	public static function render_table()
	{
		$html = array();
		$html[] = "<div>";
		$delivery_dates = self::retrieve_delivery_dates();
		if(count($delivery_dates))
		{
			$html[] = '<table cellspacing="1" cellpadding="4" width="30%" style="margin: 0 auto;">';
			//HEADER
			$html[] = '<thead>';
	        $html[] = '<tr id="t">';
	        $html[] = '<th>&nbsp;</th>';
	        $html[] = '<th>Supplier</th>';
	        $html[] = '<th>Delivery date</th>';
	      	$html[] = '</tr>';
			$html[] = '</thead>';
	
	        //TBODY SHOW MESSAGES
			$donker = false;
	        foreach($delivery_dates as $date) 
	        {
	            if ($donker) 
	            {
	            	$html[] = "<tr id='d'>";
	            }
	            else 
	            {
	            	$html[] = "<tr id='h'>";
	            }
	            $donker = !$donker;
	            $html[] = "<td valign='top' align='center' width='50px'>";
		        $html[] = "<a href='manage_delivery_dates.php?edit_delivery_date=".$date->get_supplier_id()."'><img src='images/edit.png' border='0' alt='Edit'></a>&nbsp;<a href='javascript: conf(\"Are you sure you want to delete this delivery date?\",\"manage_delivery_dates.php?remove_delivery_date=".$date->get_supplier_id()."\");'><img src='images/delete.png' style='border: 0;'></a>";
		        $html[] = "</td>";
	            $html[] = "<td align='center'>";
	            $html[] = SupplierManager :: get_supplier_name($date->get_supplier_id());
	            $html[] = "</td>";
	            $html[] = "<td align='center'>";
	            $html[] = self::get_date_output($date, "days");
	            $html[] = "</td>";
	            $html[] = "</tr>";
	        }
			$html[] = "</table>";
		}
		else
		{
			$html[] = "<p class='information'>No delivery dates have been added yet.</p>";
		}
		$html[] = "</div>";
		return implode("\n", $html);
	}
	
	public static function retrieve_delivery_date_from_post()
	{
		$delivery_date = new DeliveryDate();
		$suppliers = SupplierManager :: get_all_suppliers();
		if(is_null($_POST["supplier_id"]) && !key_exists($_POST["supplier_id"], $suppliers))
		{
			Error::get_instance()->add_result(false);
			Error::get_instance()->append_message("Please select a valid supplier");
		}
		$delivery_date->set_supplier_id($_POST["supplier_id"]);
		
		if(is_null($_POST["date_radio"]) || ($_POST["date_radio"] != "date" && $_POST["date_radio"] != "period"))
		{
			Error::get_instance()->add_result(false);
			Error::get_instance()->append_message("Please select a valid supplier");
		}
		elseif($_POST["date_radio"] == "date")
		{
			$delivery_date->set_period(0);
			if(is_null($_POST["date_time"]) || strtotime($_POST["date_time"]) === false && strtotime($_POST["date_time"]) > time())
			{
				Error::get_instance()->add_result(false);
				Error::get_instance()->append_message("Please enter a valid date.");	
			}
			else
			{
				$delivery_date->set_date(strtotime($_POST["date_time"]));
			}
		}
		elseif($_POST["date_radio"] == "period")
		{
			$delivery_date->set_period(1);
			if(is_null($_POST["date_period"]) || !is_numeric($_POST["date_period"]))
			{
				Error::get_instance()->add_result(false);
				Error::get_instance()->append_message("Please enter a valid period.");	
			}
			else
			{
				$delivery_date->set_date($_POST["date_period"]*24*60*60);
			}
		}
		return $delivery_date;
	}
	
	public static function save_delivery_date($delivery_date)
	{
		global $connection;
		$set_str = "period = '" . $delivery_date->get_period() . "', date = '" . $delivery_date->get_date() . "', _supplier_id = '" . $delivery_date->get_supplier_id() . "'";
		$query = "INSERT INTO `supplier_delivery_date` SET " . $set_str . " ON DUPLICATE KEY UPDATE " . $set_str;
		$id = $connection->execute_sql($query, 'INSERT');
		if(!Error::get_instance()->get_result())
		{
			Error::get_instance()->append_message("Failed saving supplier.");
			return false;
		}
		return true;
	}
	
	public static function retrieve_delivery_dates()
	{
		global $connection;
		$query = "SELECT * FROM `supplier_delivery_date`";
		$data = $connection->execute_sql($query, 'O');
		self::$delivery_dates = array();
		foreach($data as $d)
		{
			self::$delivery_dates[$d->_supplier_id] = new DeliveryDate($d);
		}
		return self::$delivery_dates;
	}

	public static function retrieve_delivery_date($id, $check_date = true)
	{
		global $connection;
		if(is_null(self::$delivery_dates))
		{
			self::$delivery_dates = array();
		}
		
		if(!key_exists($id, self::$delivery_dates))
		{
			$query = "SELECT * FROM `supplier_delivery_date` WHERE _supplier_id = '" . $id . "'";
			$data = $connection->execute_sql($query, 'O');
			if(!empty($data))
			{
				if($check_date && ($data[0]->period == 0 && $data[0]->date < time()))
				{
					self::$delivery_dates[$id] = null;
				}
				else
				{
					self::$delivery_dates[$id] = new DeliveryDate($data[0]);
				}
			}
			else
			{
				self::$delivery_dates[$id] = null;
			} 
		}
		
		return self::$delivery_dates[$id];
	}
	
	public static function remove_delivery_date($id)
	{
		global $connection;
		$query = "DELETE FROM `supplier_delivery_date` WHERE _supplier_id = '" . $id . "'";
		$result = $connection->execute_sql($query, 'DELETE');
		if($result)
		{
			return $result;
		}
		else
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->append_message("Delivery date wasn't deleted.");
			return false;
		}
	}
	
	public static function get_date_output($delivery_date, $suffix = null)
	{
		if($delivery_date->get_period())
		{
			$days = $delivery_date->get_date()/24/60/60;
			if(!is_null($suffix))
			{
				return $days . " <span>" . $suffix . "</span>";
			}
			else
			{
				return  $days . " <span>" . ID_DAYS . "</span>";
			}
		}
		else
		{
			return date("d-m-Y", $delivery_date->get_date());
		}
	}
	
	public static function render_contact($contact_text)
	{
		global $connection;
		$query = "SELECT _value FROM `parameters` WHERE _name = 'contact_suppliers'";
		$data = $connection->execute_sql($query, 'O');
		if(!empty($data) && $data[0]->_value != "")
		{                
			return "<a href='mailto:" . $data[0]->_value . "'>" . $contact_text . "</a>";
		}
		else
		{
			return $contact_text;
		}
	}
	
	public static function import_delivery_dates($file)
	{
		$result = true;
		$doc = new DOMDocument();
		if($doc->load($file))
		{
			$data = $doc->documentElement;
			$users = $data->getElementsByTagName("supplier");
			foreach($users as $row)
			{
				$temp_result = true;
				$temp_output = "";
				$delivery_date = new DeliveryDate();
				$supplier = $row->getElementsByTagName("name")->item(0)->nodeValue;
				try
				{
					$delivery_date->set_supplier_id(SupplierManager :: get_supplier_id_by_name($supplier));
				}
				catch(Exception $e)
				{
					$temp_result = false;
					$temp_output .= "<br/>" . $e->getMessage();
				}
				
				$time = $row->getElementsByTagName("time")->item(0)->nodeValue;
				if($row->getElementsByTagName("period")->item(0)->nodeValue == 0)
				{
					$delivery_date->set_period(0);
					if(is_null($time) || strtotime($time) === false)
					{
						$temp_result = false;
						$temp_output .= "<br/>Please give a valid date.";
					}
					else
					{
						$delivery_date->set_date(strtotime($time));
					}
				}
				else
				{
					$delivery_date->set_period(1);
					if(is_null($time) || !is_numeric($time))
					{
						$temp_result = false;
						$temp_output .= "<br/>Please give a valid period.";
					}
					else
					{
						$delivery_date->set_date($time*24*60*60);
					}
				}
				
				if($temp_result)
				{
					self :: save_delivery_date($delivery_date);
				}
				else
				{
					Error :: get_instance()->append_message($supplier . " not added:" . $temp_output);
					Error :: get_instance()->set_result(false);
				}
			}
		}
		else
		{
			Error :: get_instance()->add_result(false);
			Error :: get_instance()->append_message("Failed loading xml file.");
		}
		return $result;
	}
}

?>