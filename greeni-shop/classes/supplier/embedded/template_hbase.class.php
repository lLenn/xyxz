<?php

class TemplateHbase extends Template
{

	public function get_supplier_id()
	{
		return SupplierManager::get_supplier_id_by_name("hbase");
	}

	public function get_legend_text()
	{
		switch($this->get_root())
		{
			case "http://login.mijngrossier.be/":
					return "Legende";
					break;
			case "http://login.mongrossiste.eu/":
					return "Légende";
					break;
		}
	}

	public function get_contact_text()
	{
		switch($this->get_root())
		{
			case "http://login.mijngrossier.be/":
					return "Contacteer ons";
					break;
			case "http://login.mongrossiste.eu/":
					return "Contactez-nous";
					break;
		}
	}
	
	public function get_available_text($acro = true)
	{
		if($acro)
		{
			switch($this->get_root())
			{
				case "http://login.mijngrossier.be/":
						return "t.v.b.";
						break;
				case "http://login.mongrossiste.eu/":
						return "p.d.";
						break;
			}
		}
		else
		{
			switch($this->get_root())
			{
				case "http://login.mijngrossier.be/":
						return "ten vroegste beschikbaar";
						break;
				case "http://login.mongrossiste.eu/":
						return "plus tôt disponibles";
						break;
			}
		}
	}
	
	public function get_days_text()
	{
		switch($this->get_root())
		{
			case "http://login.mijngrossier.be/":
					return "dagen";
					break;
			case "http://login.mongrossiste.eu/":
					return "jours";
					break;
		}
	}
	
	public function get_price_text()
	{
		switch($this->get_root())
		{
			case "http://login.mijngrossier.be/":
					return "Dealer";
					break;
			case "http://login.mongrossiste.eu/":
					return "Dealer";
					break;
		}
	}

	public function get_default_currency()
	{
		return "EUR";
	}
	
	public function get_default_language()
	{
		switch($this->get_root())
		{
			case "http://login.mijngrossier.be/":
					return "NL";
					break;
			case "http://login.mongrossiste.eu/":
					return "FR";
					break;
		}
	}
	
	public function get_menu_array()
	{
		switch($this->get_root())
		{
			case "http://login.mijngrossier.be/":
					return array("kenteken" => "kentekenbouwjaar.aspx",
						 	 "brandstof" => "brandstof.aspx", 
						 	 "merk" => "merk.aspx", 
						 	 "model" => "model.aspx", 
						 	 "type" => "autotype.aspx", 
						 	 "artikelgroep" => "subgroepen.aspx",
						 	 "artikel" => array("artikelen.aspx", "stuurdelen.aspx", "uitlaat.aspx"), 
						 	 "artikelinfo" => "artikelinfo.aspx");
					break;
			case "http://login.mongrossiste.eu/":
					return array("immatriculation" => "kentekenbouwjaar.aspx",
						 	 "essence" => "brandstof.aspx", 
						 	 "marques" => "merk.aspx", 
						 	 "modéle" => "model.aspx", 
						 	 "type" => "autotype.aspx", 
						 	 "groupes d'articles" => "subgroepen.aspx",
						 	 "article" => array("artikelen.aspx", "stuurdelen.aspx", "uitlaat.aspx"), 
						 	 "article info" => "artikelinfo.aspx");
					break;
		}
	}
	
	public function remove_code($dom)
	{
		$page = explode("?", substr($this->get_page(), 44));
		$page = $page[0];

		if($dom->getElementsByTagName("table")->length > 0)
		{
			$table = $dom->getElementsByTagName("table")->item(0);
			if(!is_null($table))
			{
				$tr = $table->childNodes->item(1);
				$table->removeChild($tr);
			}
			
			$menu = $this->get_menu_array();
			
			$table = $dom->createElement("table");
			$table->setAttribute("style", "border-collapse: collapse; margin-bottom: solid 1px #000000; margin-left: solid 1px #000000;");
			$tr = $dom->createElement("tr");
			$color = "color: #ffffff;";
			$count = 0;
			$four_once = true;
			foreach($menu as $index => $m)
			{
				if(!is_array($m))
				{
					$m = array($m);
				}
				$background = "background: #666666;";
				if(in_array($page, $m))
				{
					$background = "background: #999999;";
				}
				
				$td = $dom->createElement("td");
				
				$cursor = "";
				$validation = false;
				if($color == "color: #ffffff;" && !in_array($page, $m))
				{
					if(!is_null($dom->getElementsByTagName("area")->item(0)))
					{
						$href_child = $dom->getElementsByTagName("area")->item(0);
						$href = $href_child->getAttribute("href");
						foreach($m as $i)
						{
							if(strpos($href, $i) !== false)
								$validation = true;
						}
						if($validation)
						{
							$td->setAttribute("onmouseover", "this.style.background = '#999999'");
							$td->setAttribute("onmouseout", "this.style.background = '#666666'");
							$td->setAttribute("onclick", "window.location = '" . $href . "'");
							$cursor = "cursor: pointer;";
							$href_child->parentNode->removeChild($href_child);
						}
					}
				}
				
				if($validation)
					$td->setAttribute("style", "padding: 5px 9px; font-style: italics; " . $background . " " . $color . " " . $cursor . " border-top: solid 1px #ffffff; border-right: solid 1px #ffffff;");
				else
					$td->setAttribute("style", "padding: 5px 9px; font-style: italics; " . $background . " color: #3b3b3b; " . $cursor . " border-top: solid 1px #ffffff; border-right: solid 1px #ffffff;");
				
				$td->setAttribute("class", "TitelPadding BoldTekst");
				
				$td->nodeValue = $index;
				$tr->appendChild($td);
			
				if(in_array($page, $m))
				{
					$color = "color: #3b3b3b;";
				}
				/*
				if(!(substr($this->get_page(), 44, 9) == "artikelen" && $count == 4 && $four_once))
					$count++;
				else
					$four_once = false;
					*/
			}
			
			$table->appendChild($tr);
			$td_new = $dom->createElement("td");
			$td_new->appendChild($table);
			$tr_new = $dom->createElement("tr");
			$tr_new->appendChild($td_new);
			$clone = $dom->getElementsByTagName("table")->item(3)->getElementsByTagName("tr")->item(0)->cloneNode(true);
			$dom->getElementsByTagName("table")->item(3)->removeChild($dom->getElementsByTagName("table")->item(3)->getElementsByTagName("tr")->item(0));
			$dom->getElementsByTagName("table")->item(3)->removeChild($dom->getElementsByTagName("table")->item(3)->getElementsByTagName("tr")->item(0));
			$dom->getElementsByTagName("table")->item(3)->appendChild($clone);
			$dom->getElementsByTagName("table")->item(3)->appendChild($tr_new);
		}
		return $dom;
	}
	
	public function clean_up_javascript($data)
	{	
		//$data = preg_replace('/Sys.Application.initialize\(\);/', '', $data);
		$data = preg_replace('/if[ ]*\([ ]*top.location[ ]*!=[ ]*location[ ]*\)[ ]*\{top.location.href[ ]*=[ ]*document.location.href[ ]*\;[ ]*\}/', '', $data);
		$match = preg_match('/<[sS][cC][rR][iI][pP][tT].*<\/[sS][cC][rR][iI][pP][tT]>/U',$data, $matches);
		if(count($matches) == 1)
		{
			$match = preg_match('/[sS][rR][cC][ ]*=[ ]*[\"\'][^\"\'\)]*[\"\']/', $matches[0], $matches_2);
			if(count($matches_2 == 1))
			{
				$trimmed_match = preg_replace("/[ ]*/", "", $matches_2[0]);
				$url = substr($trimmed_match, 5, strlen($trimmed_match) - 6);
				if(!strcmp($url, 'http://login.mijngrossier.be/MGLIVE/scripts/include.js.aspx'))
				{
					$curl = new Curl("http://login.mijngrossier.be/");
					$curl->set_page('http://login.mijngrossier.be/MGLIVE/scripts/include.js.aspx');
					$data = str_replace($matches[0], "<script language='javascript' type='text/javascript'>" . $curl->get_data(false) . "</script>", $data);
					//$fh = fopen("classes/curl/html_output_2.txt", "w");
					//fwrite($fh, $data);
					//fclose($fh);
				}
			}
		}
		return $data;
	}
	
	public function add_buy_info($dom)
	{	
		if(substr($this->get_page(), 44, 9) == "artikelen" || substr($this->get_page(), 44, 10) == "stuurdelen" || substr($this->get_page(), 44, 7) == "uitlaat")
		{
			$table = $this->find_node_by_attribute($dom, "id", "gv");
			if(!is_null($table))
			{
				$column_num = 6;
				foreach($table->getElementsByTagName("th") as $index => $th)
				{
					if($th->nodeValue == "Afbeelding" || $th->nodeValue == "illustration")
					{
						$column_num = $index;
						if($_SESSION["display_price"])
						{
							$column_num--;
						}
					}
					if($th->nodeValue == "Bruto" || $th->nodeValue == "brut")
					{
						$bruto_column_num = $index;
					}	
					if($th->nodeValue == "Omschrijving" || $th->nodeValue == "description")
					{
						$descr_column_num = $index;
					}
				}
				$dom = $this->add_buy_button_to_table($dom, "gv", $descr_column_num, 0, $bruto_column_num, ($column_num+1), "hbase", "");
				$node = $this->find_node_by_attribute($dom->getElementsByTagName("body")->item(0), "id", "gv");
				if(!is_null($node))
				{
					$first = true;
					foreach($node->getElementsByTagName("tr") as $tr)
					{
						if($first)
						{
							$column = "th";
							$first = false;
						}
						else
						{
							$column = "td";
						}
						if(!is_null($tr->getElementsByTagName($column)->item($column_num)))
						{
							$clone = $tr->getElementsByTagName($column)->item($column_num)->cloneNode(true);
							if($clone->getElementsByTagName("table")->length > 0)
							{
								$clone->setAttribute("id", $clone->getElementsByTagName("td")->item(0)->getAttribute("id"));
								$clone->removeChild($clone->getElementsByTagName("table")->item(0));
							}
							$tr->appendChild($clone);
							if($image = $tr->getElementsByTagName($column)->item($column_num)->getElementsByTagName("img")->length>0)
							{
								$image = $tr->getElementsByTagName($column)->item($column_num)->getElementsByTagName("img")->item(0)->cloneNode(true);
								$clone->appendChild($image);
							}
							$tr->removeChild($tr->getElementsByTagName($column)->item($column_num));
						}
					}
				}
			}
		}
		return $dom;
	}
	
	public function add_javascript_login($data)
	{
		return $data;
	}
	
	public function add_javascript_overwrite($data)
	{
		return parent::add_javascript_overwrite($data, "hbase");
	}

	public function translate_exceptions(&$dom, $to, $translations)
	{
		$nodes = array();
		$nodes[] = $this->find_node_by_attribute($dom->getElementsByTagName("body")->item(0), "class", "InfoTekst");
		$page = explode("?", substr($this->get_page(), 44));
		$page = $page[0];
		if($page == "kentekenbouwjaar.aspx")
		{
			$nodes[] = $dom->getElementsByTagName("span")->item(1);
		}
		foreach($nodes as $node)
		{
			$node->nodeValue = $this->get_translation($node->nodeValue, $to, $translations, true);
		} 
		$node = $this->find_node_by_attribute($dom->getElementsByTagName("body")->item(0), "id", "gv");
		$title = true;
		$column = 0;
		switch($page)
		{
			case "autotype.aspx": $column = 3;
								  break;
			case "model.aspx": $column = 2;
							   break;
		}
		if(!is_null($node))
		{
			foreach($node->getElementsByTagName("tr") as $tr)
			{
				if(!$title)
				{
					$to_translate = $tr->getElementsByTagName("td")->item($column)->nodeValue;
					$tr->getElementsByTagName("td")->item($column)->nodeValue = $this->get_translation($to_translate, $to, $translations, true);
				}
				else
				{
					$title = false;
				}
			}
		}
	}
	
	public function get_extra_options($handler)
	{
	}
}