<?php

class SupplierRenderer
{
	public function render_import_form()
	{
		$html = array();
		$html[] = '<form name="delivery_date" style="display:inline" method="post">';
		$html[] = "<div class='record_title record'>Supplier_id :</div><div class='record'><input type='text' name='supplier_id' size='3'" . (isset($_POST["supplier_id"])?" value='" . $_POST["supplier_id"]. "'":"") . "></div><br class='clear_float'>";
		$html[] = "<div class='record_title record'>Supplier_name :</div><div class='record'><input type='text' name='supplier_name' " . (isset($_POST["supplier_name"])?" value='" . $_POST["supplier_name"]. "'":"") . "></div><br class='clear_float'>";
		$html[] = "<div class='record_title record'>Embedded supplier?</div><div class='record'><input type='checkbox' name='embedded' " . (isset($_POST["embedded"]) && ($_POST["embedded"] == 'on' || $_POST["embedded"] == 1)?" checked='checked'":"") . "></div><br class='clear_float'>";
		$html[] = "<input type='submit' name='submit_import_supplier' style='font-size: 11px; vertical-align: middle; margin: 3px;' value='Import supplier'/>";
		$html[] = "</form>";
		return implode("\n", $html);
	}
	
	public function render_select_form($supplier_id, $selected_categories)
	{                                 
		$html = array();
		$html[] = '<form id="category_form" name="category_form" method="post">';
        $sel_count = count($selected_categories);
        $count = 0;                 
        while($sel_count > $count)
        {                      
            $count++;                                                   
            $html[] = self :: render_selector($supplier_id, array_slice($selected_categories, 0, $count));             
		}
		$html[] = "</form>";
		return implode("\n", $html);
	}
	
	public function render_selector($supplier_id, $selected_categories, $name = null)
	{                                  
        $count_sel = count($selected_categories) - 1;
		if(is_null($name))
		{
			$name = "supplier_" . $supplier_id . "_field_" . $selected_categories[$count_sel]["category"]->_field_id;
		}
		$values = SupplierDataManager :: retrieve_values_of_category($supplier_id, $selected_categories);
		$html = array();                           
        $html[] = "<select class='category_selector' name='" . $name . "'>";
		$html[] = "<option value='-1'>" . constant("ID_SELECT_" . strtoupper($selected_categories[$count_sel]["category"]->name)) . "</option>";
		foreach($values as $v)
		{
			$str = "<option value='" . $v["meta_key_sub"] . "'";
			if($v["meta_key_sub"] == $selected_categories[$count_sel]["selected_value"])
			{
				$str .= " selected = 'selected'";
			}
			$html[] = $str . ">ID_SUP_GRP_" . strtoupper($v["meta_value"]) . "</option>";
		}
		$html[] = "</select>";   
		return implode("\n", $html);
	}
	
	public function render_table($supplier_id, $selected_categories)
	{                              
		$headers = SupplierDataManager::retrieve_supplier_headers($supplier_id);
		$all_headers = SupplierDataManager::retrieve_supplier_fields();
		$count = count($headers)+3;
		$html = array();
		$html[] = '<table border="0" cellspacing="1" cellpadding="4" width="100%">';
        $html[] = '<tr id="t">';
		//$html[] = '<th colspan="' . $count . '" align="left">'. ID_ORDERS . '</th>';
		$html[] = '</tr>';
		$html[] = '<tr id="t">';
		$html[] = '<th>' . ID_COL_XREF . '</th>';
		$row_headers = array();
		foreach($headers as $header)
		{                                                                                                                                                          
			//$html[] = '<th>ID_COL_' . strtoupper($header) . '</th>';
			foreach($all_headers as $h)
			{
				if(!strcmp($h->name, $header))
				{
					$row_headers[] = $h;    
                    $html[] = '<th>' . strtoupper(substr(constant("ID_" . strtoupper($h->translation_name)),0,1)) . substr(constant("ID_" . strtoupper($h->translation_name)),1) . '</th>';
				}
			}
		}
		$html[] = '<th>' . strtoupper(substr(ID_E_A,0,1)) . substr(ID_E_A,1) . '</th>';
		$html[] = '<th>' . strtoupper(substr(ID_COL_PRICE,0,1)) . substr(ID_COL_PRICE,1) . '</th>';
        $html[] = '<th>&nbsp;</th>';
        $html[] = '<th>&nbsp;</th>';
		$html[] = '</tr>';
				
		$articles = SupplierDataManager :: retrieve_articles_of_category($supplier_id, $selected_categories);
        $delivery_date = DeliveryDateManager::retrieve_delivery_date($supplier_id);
        if(!is_null($delivery_date))
        {
		    $ea = DeliveryDateManager::get_date_output($delivery_date, ID_DAYS);
        }
        else
        {                      
            $ea = DeliveryDateManager::render_contact(ID_CONTACT_US);
        }
        
		if(count($articles) > 0)
		{
			$donker = true;
			foreach($articles as $article_number => $article)
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
				$article["article_price"] = SupplierDataManager::retrieve_price_of_article($article_number, SupplierManager::get_supplier_name($supplier_id));
				$article["delivery_date"] = $ea;
				$html[] = self :: render_article_row($supplier_id, $article_number, $article, $row_headers);
				$html[] = "<tr/>";
			}
		}
		else
		{
			$html[] = "<tr><td colspan='" . $count . "'>" . ID_NO_ARTICLES_FOUND . "</td></tr>";
			$html[] = "<tr id='t'><td colspan='" . $count . "'>&nbsp;</td></tr>";
		}
        
    	$html[] = '</table>';
    	return implode("\n", $html);
	}
	
	public function render_article_row($supplier_id, $article_number, $article, $headers)
	{
		global $connection;
		$html[] = "<td>" . $article_number . "</td>";    
        $description = false;
        $description_index = 0;
		foreach($headers as $h)
		{
			$html[] = "<td " . self :: get_field_align_output($h) . ">" . $article[$h->_id] . "</td>";
            if($h->name == "description")
            {
                $description = true;
                $description_index = $h->_id;
            }
		}
        $html[] = "<td align='center'>" . $article["delivery_date"] . "</td>";
		$html[] = "<td align='right'>" . $article["article_price"] . "</td>";  
        $add_args = "";
        if($description)
        {
            $added_args = "&desc=" . $article[$description_index] . "&image";
        }
        $html[] = "<td align='center'><img style='cursor: pointer; margin: 0px 3px 3px;' onclick='window.location = \"my_favorites.php?itemref=" . $article_number . "&var=" . $supplier_id . $added_args . "\"' src='images/favorites.png'></a></td>";
        $html[] = "<td align='center'><img style='cursor: pointer; margin: 0px 3px 3px;' onclick='window.location = \"my_order.php?itemref=" . $article_number . "&var=" . $supplier_id . $added_args . "\"' src='images/order_2.png'></a></td>";  
		return implode("\n", $html);
	}
    
    public function get_field_align_output($field)
    {
        switch($field->type)
        {
            case "int": return "align='right'";
            case "string": return "align='left'";
            default: return "align='center'";
        }
    }
}

?>