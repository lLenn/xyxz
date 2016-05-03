<?php

require_once "classes/supplier/delivery_date_manager.class.php";
require_once "classes/supplier/delivery_date.class.php";
require_once "classes/supplier/supplier_manager.class.php";

class Template
{
	private $currency = null;
	private $page = null;
	private $root = null;
	private $translations = array();
	static $general_translations = array();
	static $add_slashes = array('t/m');
	
	public function set_currency($currency)
	{
		$this->currency = $currency;
	}
	
	public function get_currency()
	{
		return $this->currency;
	}
	
	public function set_root($root)
	{
		$this->root = $root;
	}
	
	public function get_root()
	{
		return $this->root;
	}
	
	public function set_page($page)
	{
		$this->page = $page;
		$this->retrieve_translations($_SESSION["language"]);
	}
	
	public function get_page()
	{
		return $this->page;
	}
	
	protected function add_buy_button_to_table($dom, $table, $description_column_number, $reference_column_number = -1, $price_column_number = -1, $image_column_number = -1, $table_name = "", $root = "", $article_in_link = false, $request_term = "", $image_url = true, $remove_doubles = false, $delivery_in_description = false)
	{
		if(!is_array($description_column_number))
		{
			$description_column_number = array($description_column_number);
		}
		if(is_string($table))
		{
			$node = $this->find_node_by_attribute($dom->getElementsByTagName("body")->item(0), "id", $table);
		}
		else
		{
			$node = $table;
		}
		if(!is_null($node))
		{
			$title = true;
			$passed_articles = array();
			$remove_indices = array();
			foreach($node->childNodes as $index => $tr)
			{
				$exception = $tr->nodeType == "tr" &&
							  ((!is_null($tr->childNodes->item(0)->getAttribute("colspan")) &&
							  $tr->childNodes->item(0)->getAttribute("colspan") > 1) ||
							  $tr->getElementsByTagName("td")->length == 1);
				if(strtolower(get_class($tr)) == "domelement" && strtolower($tr->tagName) == "tr" && !$exception)
				{
					$article_number = -1;
					if($reference_column_number > -1)
					{
						if(!$article_in_link)
						{
							$value = $tr->getElementsByTagName("td")->item($reference_column_number)->nodeValue;
							$value = htmlentities($value, ENT_COMPAT, "UTF-8");
							$value = str_replace("&nbsp;", "", $value);
							$value = html_entity_decode($value, ENT_COMPAT, "UTF-8");
							$value = preg_replace("/[ \r\t\n]+/", " ", $value);
							$article_number = trim($value);
						}
						else
						{
							if(!is_null($tr->getElementsByTagName("td")->item($reference_column_number)) && !is_null($tr->getElementsByTagName("td")->item($reference_column_number)->getElementsByTagName("a")->item(0)))
							{
								$article_request = rawurldecode($tr->getElementsByTagName("td")->item($reference_column_number)->getElementsByTagName("a")->item(0)->getAttribute("href"));
								$article_request = preg_split("/[&?]/", $article_request);
								$article_request = explode("=", $article_request[count($article_request)-1]);
								if($article_request[0] == $request_term)
								{
									$article_number = $article_request[1];
								}
							}
							else
							{
								$article_number = "none";
							}
						}
					}
					if(!in_array($article_number, $passed_articles) || $article_number == -1 || !$remove_doubles)
					{
						if($article_number > -1 && $_SESSION["display_price"])
						{
							//ADD PRICE
							$price =  $this->get_price_article($article_number, $table_name);
							if($price_column_number > -1)
							{
								$bruto_td = $tr->childNodes->item($price_column_number);
								if(strtolower($bruto_td->tagName) == "td")
								{
									$bruto_td->nodeValue = $bruto_td->nodeValue . " " . $this->get_default_currency();
								}
								else
								{
									$bruto_td->nodeValue = "Publiek";
								}
							}
								
							$table_cell = $dom->createElement("td");
							$table_cell->setAttribute("class", $tr->childNodes->item(0)->getAttribute("class"));
							$table_cell->setAttribute("style", "text-align: center;");
							if($index > 0)
							{
								$table_cell->nodeValue = $price;
							}
							else
							{
								$table_cell->nodeValue = $this->get_price_text();
							}
								
							if($price_column_number > -1)
							{
								$tr->appendChild($bruto_td);
							}
							$tr->appendChild($table_cell);
						}
						
						$delivery_date = DeliveryDateManager::retrieve_delivery_date($this->get_supplier_id());
						//ADD DELIVERY TIME
						$table_cell = $dom->createElement("td");
						$table_cell->setAttribute("class", $tr->childNodes->item(0)->getAttribute("class"));
						$table_cell->setAttribute("style", "text-align: center;");
						if(!$title)
						{
							// GET DESCRIPTION BEFORE IT GETS ALTERED
							$description = "";
							foreach($description_column_number as $col)
							{
								$description .= "<br/>" . str_replace(array("\r", "\n"), array("", ""), $this->get_innerHTML($tr->getElementsByTagName("td")->item($col)));
							}
							$description = substr($description, 5);
						
							if(!$delivery_in_description)
							{
                                $delivery_text = "";
								if(!is_null($delivery_date))
								{
                                    $delivery_text = DeliveryDateManager::get_date_output($delivery_date, $this->get_days_text());                              
								}
								else
								{
									$delivery_text = DeliveryDateManager::render_contact($this->get_contact_text());
								}
                                $doc = DOMDocument::loadHTML($delivery_text);
                                $node = $dom->importNode($doc->getElementsByTagName("body")->item(0), true);
                                $table_cell->appendChild($node);
								$tr->appendChild($table_cell);
							}
							else
							{
								$delivery_text = "<span>" . $this->get_available_text() . "</span>: ";
								if(!is_null($delivery_date))
								{
									$delivery_text .= DeliveryDateManager::get_date_output($delivery_date, $this->get_days_text());
								}
								else
								{
									$delivery_text .= "<span>" . DeliveryDateManager::render_contact($this->get_contact_text()) . "</span>";
								}
								$doc = DOMDocument::loadHTML("<br><br>" . $delivery_text);
								$node = $dom->importNode($doc->getElementsByTagName("body")->item(0), true);
								$tr->getElementsByTagName("td")->item($description_column_number[0])->appendChild($node);
							}
						}
						elseif(!$delivery_in_description)
						{
							$table_cell->nodeValue = $this->get_available_text();
							$tr->appendChild($table_cell);
						}
					
						//PREPARE BUY BUTTON AND FAV LINK
						$table_cell = $dom->createElement("td");
						$table_cell->setAttribute("class", "button " . $tr->childNodes->item(0)->getAttribute("class"));
						$table_cell->setAttribute("style", "text-align: center;");
						
						$table_cell_fav = $dom->createElement("td");						
						$table_cell_fav->setAttribute("class", "button " . $tr->childNodes->item(0)->getAttribute("class"));
						$table_cell_fav->setAttribute("style", "text-align: center;");
						
						//ADD PARENT ONCLICK TO CHILDREN SO BUTTONS CAN BE ACCESSED WITHOUT HINDRANCE
						if($tr->getAttribute("onclick") != "")
						{
							$onclick = str_replace("this", "this.parentNode", $tr->getAttribute("onclick"));
							$tr->setAttribute("onclick", "");
							foreach($tr->childNodes as $child)
							{
								if(strtolower(get_class($child)) == "domelement" && $child->tagName == "td")
								{
									$child->setAttribute("onclick", $onclick);
								}
							}
						}
						//ADD LINK TO OUR SITE IF ARTICLE IS IN OUR DATABASE
						if(!$title)
						{
							$our_article = $this->get_article($article_number, $table_name);
							if(!is_null($our_article))
							{
								$our_article_number = $our_article->articlenumber;
								if(!is_null($our_article_number) && $our_article_number != "")
								{
									foreach($tr->getElementsByTagName("td") as $child)
									{
										if(!is_null($child->getAttribute("onclick")) && $child->getAttribute("onclick") != "")
										{
											$child->setAttribute("onclick", "Javascript: ShowDetails('".$our_article_number."', true);");
										}
										if($child->getElementsByTagName("a")->length == 1)
										{
											$child->getElementsByTagName("a")->item(0)->setAttribute("href", "Javascript:;");
											$child->getElementsByTagName("a")->item(0)->setAttribute("onclick", "Javascript: ShowDetails('".$our_article_number."', true);");
										}
									}
								}
							}
						}
						//ADD BUY BUTTON AND FAV LINK
						if(!$title)
						{	
							$image_src = "";
							if($image_column_number > -1 && $image_url)
							{
								if(!is_null($tr->getElementsByTagName("td")->item($image_column_number)->getElementsByTagName("img")->item(0)))
								{
									$image_src = $tr->getElementsByTagName("td")->item($image_column_number)->getElementsByTagName("img")->item(0)->getAttribute("src");
									$tr->getElementsByTagName("td")->item($image_column_number)->getElementsByTagName("img")->item(0)->setAttribute("width", "70px");
								}
							}
							elseif($image_column_number > -1)
							{
								$image_id = $tr->getElementsByTagName("td")->item($image_column_number)->nodeValue;
								$image_src = $this->get_image($image_id);
							}
	
							$button = $this->create_buy_button($dom, $article_number, $description, $image_src);
							$favorite = $this->create_favorite_button($dom, $article_number, $description, $image_src);
							
							$table_cell->appendChild($button);
							$table_cell_fav->appendChild($favorite);
						}
						else
						{
							$title = false;
						}
						$tr->appendChild($table_cell_fav);
						$tr->appendChild($table_cell);
						$passed_articles[] = $article_number;
					}
					else
					{
						$remove_indices[] = $index;
					}
				}	
			}
			
			if($remove_doubles)
			{
				for($i = (count($remove_indices)-1); $i >= 0; $i--)
				{
					$node->removeChild($node->childNodes->item($remove_indices[$i]));
				}
			}
		}
		return $dom;
	}
	
	public function remove_all_nodes(&$parent_node, $filter)
	{
		for($i = ($parent_node->childNodes->length - 1) ; $i >= 0; $i--)
		{			
			$child = $parent_node->childNodes->item($i);
			if(strtolower(get_class($child)) == "domelement")
			{
				if(!in_array($child->tagName,$filter))
				{
					$parent_node->removeChild($child);
   				}
   				else
   				{
   					$child->setAttribute("style", "background: none !important;");
   					if($child->hasChildNodes())
   					{
   						$this->remove_all_nodes($child, $filter);
   					}
   				}
			}
			else
			{
				if(!(in_array("script", $filter) && $parent_node->tagName == "script"))
				{
					$parent_node->removeChild($child);
				}
			}
		}
	}
	
	protected function find_node_by_attribute($node, $attribute, $value)
	{
		foreach($node->childNodes as $child)
		{
			if(strtolower(get_class($child)) == "domelement")
			{
   				if($child->getAttribute($attribute) == $value)
   				{
   					return $child;
   				}
   				elseif($child->hasChildNodes())
   				{
   					$returnValue = $this->find_node_by_attribute($child, $attribute, $value);
   					if(!is_null($returnValue))
   					{
   						return $returnValue;
   					}
   				}
			}
		}
		return null;
	}
	
	protected function remove_nodes_by_attribute($dom, $attrs)
	{
		foreach($attrs as $attr => $values)
		{
			foreach ($values as $value)
			{
				$node = $this->find_node_by_attribute($dom->getElementsByTagName("body")->item(0), $attr, $value);
				if(!is_null($node))
				{
					$node->parentNode->removeChild($node);
				}
			}
		}
		return $dom;
	}
	
	/*
	 * Get's the article from the database.
	 * Also retrieves the information from the articlecompetitorsnumbers to check if the article is in the catalogue
	 */
	protected function get_article($article_number, $supplier)
	{
		return SupplierDataManager::retrieve_article($article_number, $supplier);
	}
	
	/*
	 * Get's the price from the article
	 * If the article in the catalogue the price is retrieved from articleprices.
	 * otherwise the price in the price from the supplier is taken.
	 * 
	 * If the article is not in the supplier tables, we check if the article is in the articlecompetitorsnumbers 
	 * just in case the article is present there.
	 */
	protected function get_price_article($article_number, $supplier)
	{
		return SupplierDataManager::retrieve_price_of_article($article_number, $supplier);
	}
	
	protected function get_innerHTML($node)
	{
		$doc = new DOMDocument();
		$doc->appendChild($doc->importNode($node,true));
		$innerHTML = $doc->saveHTML();
		preg_match_all("/<[^>]*>/", $innerHTML, $matches);
		$innerHTML = str_replace(array($matches[0][0],$matches[0][(count($matches[0])-1)], "<b>", "</b>", "<i>", "</i>"), "", $innerHTML);
		return $innerHTML;
	}
	
	public function clean_up_header($data)
	{
		$pos = strpos($data, "<!DOCTYPE");
		if($pos != -1)
		{
			return substr($data,$pos);
		}
		else
		{
			return $data;
		}
	}
	
	public function clean_up_charset($data)
	{
		return str_replace('&Atilde;&copy;', '&eacute;', $data);
	}

	public function add_javascript_overwrite($data, $site = "")
	{
		global $url;
		preg_match("/<body[^>]*>/i",$data,$matches);
		$script = "\r\n<script src='" . $url . "plugins/jquery-1.4.4.min.js' type='text/javascript'></script>
				   \r\n<script src='" . $url . "functions.js' type='text/javascript'></script>
				   \r\n<script src='" . $url . "classes/supplier/embedded/utilities.js' type='text/javascript'></script>";
	
		if($site != "")
		{
			$script .= "\r\n<script src='" . $url . "classes/supplier/embedded/javascript_" . $site . ".js' type='text/javascript'></script>";
		}
				  
		if(count($matches)>0)
		{
			$data = str_replace($matches[0], $matches[0] . $script, $data);
		}
		return $data;
	}
	
	public function add_legend($dom)
	{
		$div = $dom->createElement("div");
		$div->setAttribute("style", "position: absolute; top: 0; left: 0; background-color: #fee641; font-size: 10px; padding: 3px 7px; font-family: Verdana,Arial,Helvetica,sans-serif;");
		
		$doc = DOMDocument::loadHTML("<span>" . $this->get_legend_text() . "</span>: <span>" . $this->get_available_text() . "</span> = <span>" . strtolower($this->get_available_text(false)) . "</span>");
		$node = $dom->importNode($doc->getElementsByTagName("body")->item(0), true);
		$div->appendChild($node);
		
		$dom->getElementsByTagName("body")->item(0)->appendChild($div);
		return $dom;
	}
	
	public function translate_page(&$dom, $to)
	{
		$translations = array_merge($this->translations, self :: $general_translations);
		uasort($translations, array($this, "sort_by_length"));
		$this->translate($dom->getElementsByTagName("body")->item(0), $to, $translations);
		$this->translate_exceptions($dom, $to, $translations);
	}
	
	public function translate(&$node, $to, $translations)
	{
		if(!is_null($node) && !is_null($node->childNodes))
		{
			foreach($node->childNodes as $child)
			{
				if(strtolower(get_class($child)) == "domtext")
				{
					$child->nodeValue = $this->get_translation($child->nodeValue, $to, $translations);
				}
				else
				{
					if( strtolower(get_class($child)) == "domelement" && 
					   ($child->getAttribute("type") == "button" || $child->getAttribute("type") == "submit") &&
					    $child->getAttribute("value") != "")
					{
						$child->setAttribute("value", $this->get_translation($child->getAttribute("value"), $to, $translations));
					}
					$this->translate($child, $to, $translations);
				}
			}
		}
	}
	
	protected function get_translation($value, $to, $translations, $partly = false)
	{
		$orig_value = $value;
		$value = htmlentities($value, ENT_COMPAT, "UTF-8");
		$value = str_replace("&nbsp;", "", $value);
		$value = html_entity_decode($value, ENT_COMPAT, "UTF-8");
		$value = preg_replace("/[ \r\t\n]+/", " ", $value);
		$value = trim($value);
		$from = $this->get_default_language();
		foreach($translations as $translation)
		{
			if(in_array($translation->$from, self::$add_slashes))
			{
				$from_rep = str_replace("/", "\/", $translation->$from);
			}
			else
			{
				$from_rep = $translation->$from;
			}
			$from_rep = trim($from_rep);
			if($partly)
			{
				if(preg_match("/[ ]+" . strtoupper(substr($from_rep,0,1)) . substr($from_rep,1) . "[ ]+/", " " . $value. " ", $match))
				{
					$trans = " " . strtoupper(substr($translation->$to,0,1)).substr($translation->$to,1) . " ";
					$value = str_replace($match[0], $trans, " " . $value . " ");
				}
				elseif(preg_match("/[ ]+" . $from_rep . "[ ]+/", " " . $value. " ", $match))
				{
					$trans = " " . $translation->$to . " ";
					$value = str_replace($match[0], $trans, " " . $value . " ");
				}
			}
			else
			{                  
				if(!strcmp(strtoupper(substr($from_rep,0,1)).substr($from_rep,1), $value))
				{
					$trans = strtoupper(substr($translation->$to,0,1)).substr($translation->$to,1);
					$orig_value = str_replace(strtoupper(substr($from_rep,0,1)).substr($from_rep,1), $trans, $orig_value);
				}
				elseif(!strcmp($from_rep, $value))
				{
					$trans = $translation->$to;
					$orig_value = str_replace($from_rep, $trans, $orig_value);
				}
			}
		}
		if($partly)
		{
			$orig_value = $value;
		}
		return $orig_value;
	}
	
	public function retrieve_translations($to)
	{
		if(!is_array($this->translations))
		{
			$this->translations = array();
		}
		
		$webpage = explode("?", $this->page);
		$webpage = explode("/", $webpage[0]);
		$webpage = explode(".", $webpage[count($webpage)-1]);
		$webpage = $webpage[0];
		$query = "SELECT f.translation as " . $this->get_default_language() .  ", t.translation as " . $to .  " FROM `language_translation` as f
					LEFT JOIN `language_translation` as t ON f.name = t.name
					LEFT JOIN `language_translation_section` as s ON s.name = t.name 
					WHERE s.section = '" . SupplierManager :: get_supplier_name($this->get_supplier_id()) . "_" . $webpage . "' AND f.language = '" . $this->get_default_language() .  "' AND t.language = '" . $to .  "'";

		$result = mysql_query($query);
		while($data = mysql_fetch_object($result))
		{
			$validation = true;
			foreach($this->translations as $t)
			{
				$from = $this->get_default_language();
				if(!strcmp($t->$to, $data->$to) && !strcmp($t->$from, $data->$from))
				{
					$validation = false;
				}
			}
			if($validation)
			{
				$this->translations[] = $data;
			}
		}
	}
	
	public function retrieve_general_translations($to)
	{
		self::$general_translations = array();
		$query = "SELECT f.translation as " . $this->get_default_language() .  ", t.translation as " . $to .  " FROM `language_translation` as f
					LEFT JOIN `language_translation` as t ON f.name = t.name
					LEFT JOIN `language_translation_section` as s ON s.name = t.name 
					WHERE s.section = 'curl_general' AND f.language = '" . $this->get_default_language() .  "' AND t.language = '" . $to .  "'";
		$result = mysql_query($query);
		while($data = mysql_fetch_object($result))
		{
			$validation = true;
			foreach(self::$general_translations as $t)
			{
				$from = $this->get_default_language();
				if(!strcmp($t->$to, $data->$to) && !strcmp($t->$from, $data->$from))
				{
					$validation = false;
				}
			}
			if($validation)
			{
				self::$general_translations[] = $data;
			}
		}
	}
	
	protected function create_buy_button($dom, $article_number, $description, $image_src)
	{
		$button = $dom->createElement("img");
		$button->setAttribute("src", "images/order_2.png");
		$button->setAttribute("onclick", "Javascript: linkToShop('" . $article_number . "', '" . $this->get_supplier_id() . "', '" . urlencode($description) . "', '" . $image_src . "');");
		$button->setAttribute("style", "cursor: pointer; margin: 0 3px;");
		return $button;
	}
	
	protected function create_favorite_button($dom, $article_number, $description, $image_src)
	{
		$button = $dom->createElement("img");
		$button->setAttribute("src", "images/favorites.png");
		$button->setAttribute("onclick", "Javascript: linkToFavs('" . $article_number . "', '" . $this->get_supplier_id() . "', '" . urlencode($description) . "', '" . $image_src . "');");
		$button->setAttribute("style", "cursor: pointer; margin: 0px 3px 3px 3px;");
		return $button;
	}
	
	private function sort_by_length($a, $b)
	{
		$from = $this->get_default_language();
		$len_a = strlen($a->$from);
		$len_b = strlen($b->$from);
		
		if($len_a > $len_b)
		{
			return -1;
		}
		else if($len_a < $len_b)
		{
			return 1;
		}
		else
		{
			$len_a = strlen($a->$_SESSION['language']);
			$len_b = strlen($b->$_SESSION['language']);
			if($len_a > $len_b)
			{
				return -1;
			}
			else if($len_a < $len_b)
			{
				return 1;
			}
		}
		return 0;
	}

}
