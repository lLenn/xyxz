<?php

class TemplateRotary extends Template
{

	public function get_supplier_id()
	{
		return SupplierManager :: get_supplier_id_by_name("rotary");
	}

	public function get_available_text($acro = true)
	{
		if($acro)
		{
			return "e.a.";
		}
		else
		{
			return "earliest available";
		}
	}

	public function get_legend_text()
	{
		return "Legend";
	}
	
	public function get_days_text()
	{
		return "days";
	}
	
	public function get_contact_text()
	{
		return "Contact us";
	}
	
	public function get_price_text()
	{
		return "Price";
	}
	
	public function get_default_language()
	{
		return "EN";
	}
	
	public function get_default_currency()
	{
		return "";
	}
	
	public function remove_code($dom)
	{
		$remove_2 = $dom->getElementsByTagName("table")->item(1);
		$item_start = $remove_2->getElementsByTagName("tr")->length - 2;
		$remove = $remove_2->getElementsByTagName("tr")->item($item_start);
		$remove->parentNode->removeChild($remove);
		$remove = $remove_2->getElementsByTagName("tr")->item($item_start - 1);
		$remove->parentNode->removeChild($remove);
		
		$remove_2 = $dom->getElementsByTagName("table")->item(2);
		$item_start = $remove_2->getElementsByTagName("tr")->length - 1;
		$remove = $remove_2->getElementsByTagName("tr")->item($item_start);
		$remove->parentNode->removeChild($remove);
		$remove = $remove_2->getElementsByTagName("tr")->item($item_start - 1);
		$remove->parentNode->removeChild($remove);
		$remove = $remove_2->getElementsByTagName("tr")->item($item_start - 2);
		$remove->parentNode->removeChild($remove);
		$remove = $remove_2->getElementsByTagName("tr")->item($item_start - 3);
		$remove->parentNode->removeChild($remove);
		
		$dom->getElementsByTagName("table")->item(2)->setAttribute("style", "margin-top: 75px;");
		$length = $dom->getElementsByTagName("table")->item(2)->getElementsByTagName("tr")->length;
		$dom->getElementsByTagName("table")->item(2)->getElementsByTagName("tr")->item($length-1)->setAttribute("style", "height: 30px;");
		$dom->getElementsByTagName("table")->item(1)->setAttribute("width", "100%");
		
		$node = $dom->getElementsByTagName("table")->item(5);
		$item_start = $node->getElementsByTagName("tr")->length - 1;
		$remove = $node->getElementsByTagName("tr")->item($item_start);
		$remove->parentNode->removeChild($remove);
		
		$remove = $dom->getElementsByTagName("table")->item(0);
		$remove->parentNode->removeChild($remove);
		
		if($dom->getElementsByTagName("table")->length!=7)
		{
			$node = $dom->getElementsByTagName("table")->item(4);
			$item_start = $node->getElementsByTagName("tr")->length - 1;
			$remove = $node->getElementsByTagName("tr")->item($item_start);
			$remove->parentNode->removeChild($remove);
		}
		return $dom;
	}
	
	public function clean_up_javascript($data)
	{
		return $data;
	}
	
	public function add_buy_info($dom)
	{
		foreach($dom->getElementsByTagName("table")->item(1)->getElementsByTagName("tr")->item(1)->getElementsByTagName("*")->item(0)->childNodes as $child)
		{
			$child->nodeValue = str_replace("Rotary ", "", $child->nodeValue);
		}
		if($dom->getElementsByTagName("table")->length==7)
		{
			$dom = $this->add_buy_button_to_table($dom, $dom->getElementsByTagName("table")->item(4), 2, 0, -1, 3, "rotary", "", false, "", true, false, true);
		}
		else
		{
			$article_number = $dom->getElementsByTagName("table")->item(4)->getElementsByTagName("tr")->item(0)->getElementsByTagName("td")->item(1)->nodeValue;
			$article_number = trim(preg_replace("/\D\W/", "", $article_number));
			
			$description = $this->get_innerHTML($dom->getElementsByTagName("table")->item(4)->getElementsByTagName("tr")->item(1)->getElementsByTagName("td")->item(1));
			$description = str_replace(array("\r", "\n"), array("", ""), $description);
			
			$image = $dom->getElementsByTagName("table")->item(3)->getElementsByTagName("tr")->item(1)->getElementsByTagName("td")->item(0)->getElementsByTagName("img")->item(0)->getAttribute("src");
			
			$added = false;
			if($_SESSION["display_price"])
			{
				$price = $this->get_price_article($article_number, "rotary");
				$tr = $dom->createElement("tr");
				$td = $dom->createElement("td");
				$td->setAttribute("class", "label-h");
				$td->setAttribute("style", "font-weight: bold;");
				$td->nodeValue = "Price:";
				$tr->appendChild($td);
			
				$td = $dom->createElement("td");
				$td->setAttribute("class", "cell-h");
				$td->nodeValue = $price;
				$tr->appendChild($td);
				$dom->getElementsByTagName("table")->item(4)->appendChild($tr);
				$added = !$added;
			}
			
			$delivery_date = DeliveryDateManager::retrieve_delivery_date($this->get_supplier_id());
			$time_name = DeliveryDateManager::render_contact($this->get_contact_text());
			if(!is_null($delivery_date))
			{
				$time_name = DeliveryDateManager::get_date_output($delivery_date, $this->get_days_text());
			}
				
			$tr = $dom->createElement("tr");
			$td = $dom->createElement("td");
			$td->setAttribute("class", "label-" . ($added?"r":"h"));
			$td->setAttribute("style", "font-weight: bold;");
			$doc = DOMDocument::loadHTML("<span>" . $this->get_available_text() . "</span>:");
			$imp_node = $dom->importNode($doc->getElementsByTagName("body")->item(0), true);
			$td->appendChild($imp_node);
			$tr->appendChild($td);
			
			$td = $dom->createElement("td");
			$td->setAttribute("class", "cell-" . ($added?"r":"h"));
			$doc = DOMDocument::loadHTML($time_name);
			$imp_node = $dom->importNode($doc->getElementsByTagName("body")->item(0), true);
			$td->appendChild($imp_node);
			$tr->appendChild($td);
			$dom->getElementsByTagName("table")->item(4)->appendChild($tr);
			
			$tr = $dom->createElement("tr");
			$td = $dom->createElement("td");
			$td->setAttribute("class", "label-" . ($added?"h":"r"));
			$td->setAttribute("colspan", "2");
			$td->setAttribute("style", "text-align: right;");
			$td->appendChild($this->create_favorite_button($dom, $article_number, $description, $image));
			$td->appendChild($this->create_buy_button($dom, $article_number, $description, $image));
			$tr->appendChild($td);
			$dom->getElementsByTagName("table")->item(4)->appendChild($tr);
			
		}
		return $dom;
	}
	
	public function add_javascript_login($data)
	{
		$script = "<script type='text/javascript'>
					//<![CDATA[
						document.LANSA.ASTDRENTRY.value=' ' ; HandleEvent('WEBAPPLC','PRODCAT')
					//]]>
					</script>";
		return str_replace("</body>", $script . "\n</body>", $data);
	}
	
	public function add_javascript_overwrite($data)
	{
		return parent::add_javascript_overwrite($data);
	}
	
	public function get_image($article_number)
	{
		return "";
	}
	
	public function translate_exceptions($dom, $to, $translations)
	{
	}
	
	public function get_extra_options($handler)
	{
		curl_setopt($handler, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($handler, CURLOPT_SSL_VERIFYHOST, false);
	}
}