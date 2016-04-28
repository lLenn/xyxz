<?php

class Curl
{

	private $search_url_reg_expr = array(	array("/url[ ]*\([\"\'][^\"\'\)]*[\"\']\)/i", "url('", "')", 5, 2),
											array("/url[ ]*\([^\"\'\)]*\)/i", "url(", ")", 4, 1),
											array("/src[ ]*=[ ]*[\"\'][^\"\'\)]*[\"\']/i", "src='", "'", 5, 1),
											array("/href[ ]*=[ ]*[\"\'][^\"\'\)]*[\"\']/i", "href='", "'", 6, 1),
											array("/background[ ]*=[ ]*[\"\'][^\"\'\)]*[\"\']/i", "background='", "'", 12, 1)
									 	);
	private $search_link_reg_expr = array(	array("/href[ ]*=[ ]*[\"\'][^\"\'\)]*[\"\']/i", "href='", "'", 6, 1),
											array("/url[ ]*=[ ]*[^\"\'\)]*/i", "url=", "", 4, 0),
											array("/action[ ]*=[ ]*[\"\'][^\"\'\)]*[\"\']/i", "action='", "'", 8, 1),
											//array("/location[ ]*=[ ]*[^;\"\']*[;]/", "location='", "';", 9, 1),
											array("/location[ ]*=[ ]*[\"\'][^;]*[\"\'][;]/i", "location='", "';", 10, 2),
											array("/location[ ]*=[ ]*[^\"\'][^;]*[\"\'][;]/i", "location=", "';", 9, 2),
											array("/location[ ]*=[ ]*[\"\'][^;]*[^\"\'][;]/i", "location='", ";", 10, 1),
											array("/location[ ]*=[ ]*[\"][^\"\']*[\"]/i", 'location="', '"', 10, 1),
											array("/location[ ]*=[ ]*[\'][^\"\']*[\']/i", "location='", "'", 10, 1)
										);
	private $environment_scan_url = array(	array(),
											array(),
											array(),
											array("/<link[^>]*href[ ]*=[ ]*[\"\'][^\"\'\)>]*[\"\'][^>]*>/i"),
											array()
										 );
	private $environment_scan_link = array(	array("/<a[^>]*href[ ]*=[ ]*[\"\'][^\"\'\)]*[\"\'][^>]*>/i", "!/href[ ]*=[ ]*[\"\'][ ]*javascript[:]/i"),
											array("/<meta.*url[ ]*=[ ]*[^\"\'\)]*[\"\';]>/is"),
											array(),
											array("/[\<]script.*location[ ]*=[ ]*[\"\'][^;]*[\"\'][;]/is"),
											array("/[\<]script.*location[ ]*=[ ]*[^\"\'][^;]*[\"\'][;]/is"),
											array("/[\<]script.*location[ ]*=[ ]*[\"\'][^;]*[^\"\'][;]/is"),
											array("/[\']javascript[:][^\"\']*location[ ]*=[ ]*[\"][^\"\']*[\"]/i"),
											array("/[\"]javascript[:][^\"\']*location[ ]*=[ ]*[\'][^\"\']*[\']/i")
										 );
	
	private $css = array();
	private $parent = "";
	private static $response_meta_info;
	private $root;
	private $page;
	private $curl_handler;
	private $cookie_file_path = "cookies.txt";
	private $logging_in;
	private $error_message = "The site is not available at the moment. We apologize for the inconvenience.";
	private $currency = null;
	private $template;
	
	function Curl($root)
	{
		if(substr($root,strlen($root)-1,1) != "/")
		{
			$root .= "/";
		}
		$this->root = $root;
		$this->curl_handler = curl_init();
		$this->set_currency();
		$this->set_template();
	}

	public function set_error_message($error_message)
	{
		$this->error_message = $error_message;
	}
	
	public function get_error_message()
	{
		return $this->error_message;
	}

	public function set_parent($parent)
	{
		$this->parent = $parent;
	}
	
	public function get_parent()
	{
		return $this->parent;
	}
	
	public function set_page($page)
	{
		$this->page = $page;
		if(!is_null($this->template))
		{
			$this->template->set_page($this->page);
		}
	}
	
	public function get_page()
	{
		if(!is_null($this->page))
		{
			return $this->page;
		}
		else
		{
			return $this->root;
		}
	}
	
	public function get_root()
	{
		return $this->root;
	}
	
	public function set_currency()
	{
		$query = "SELECT currency FROM `articleprices_pricelists` WHERE pricelist = '" . $_SESSION["country"] . "'";
		$result = mysql_query($query); if(mysql_errno()>0) trigger_error("+++ ".mysql_errno().": ".mysql_error()." +++");
		if(mysql_num_rows($result) > 0)
		{
			$data = mysql_fetch_object($result);
			$this->currency = $data->currency;
		}
		else
		{
			$this->currency = "EUR";
		}
	}
	
	public function set_template()
	{
		require_once "template.class.php";
		switch($this->root)
		{
			case "http://www.hbase.be/":
			case "http://login.mijngrossier.be/":
			case "http://login.mongrossiste.eu/":
					require_once 'template_hbase.class.php';
					$this->template = new TemplateHbase();
					break;
			case "http://ecat.arrowheadep.com/":
					require_once 'template_arrowhead.class.php';
					$this->template = new TemplateArrowhead();
					break;
			case "https://www.rotarycorp.com/":
					require_once 'template_rotary.class.php';
					$this->template = new TemplateRotary();
					break;
			default: return;
		}
		$this->template->set_currency($this->currency);
		$this->template->set_root($this->root);
		$this->template->set_page($this->page);
		$this->template->retrieve_general_translations($_SESSION["language"]);
	}
	
	public function add_post($variables = null)
	{
		if(!is_null($variables))
		{
			curl_setopt($this->curl_handler, CURLOPT_POST, 1);
			curl_setopt($this->curl_handler, CURLOPT_POSTFIELDS, $variables);
		}
		else
		{
			if(!empty($_POST)) 
			{
				if(isset($_POST["get_to_post"]) && $_POST["get_to_post"] == 1)
				{
					unset($_POST["get_to_post"]);
					$get_str = "?";
					foreach($_POST as $index => $value)
					{
						$get_str .= $index . "=" . $value . "&";
					}
					$get_str = substr($get_str, 0, strlen($get_str) - 1);
					$question_pos = strrpos($this->get_page(), "?");
					$slash_pos = strrpos($this->get_page(), "/");
					if($question_pos > $slash_pos)
					{
						$this->page = substr($this->get_page(), 0, $question_pos);
					}
					$this->page = $this->get_page() . $get_str;
				}
				else
				{
					$tmp = file_get_contents('php://input');	
					if(isset($_POST["Login1\$LoginButton"]) && $this->get_root() == "http://ecat.arrowheadep.com/")
					{
						$dir = realpath(dirname(__FILE__));
						$fh = fopen($dir . "/pass_arrowhead.txt", "w");
						fwrite($fh, $tmp);
						fclose($fh);				
					}
					curl_setopt($this->curl_handler, CURLOPT_POST, 1);
					curl_setopt($this->curl_handler, CURLOPT_POSTFIELDS, $tmp);
				}
			}
		}
	}
	
	public function add_proxy()
	{
		/*
		*/
	}
	
	public function login($data, $login_page)
	{
		try 
		{
			$tmp_page = $this->get_page();
			$this->logging_in = true;
			$this->prepare_curl_handler();
			$this->add_proxy();
			$this->add_post($data);
			$this->template->get_extra_options($this->curl_handler);
			$this->set_page($login_page);
			$source_code = $this->set_follow_location(0);
			if(is_null($data))
			{
				$source_code = $this->template->add_javascript_login($source_code);
				$source_code = $this->update_urls($source_code);
				$source_code = $this->update_links($source_code);
				ini_set("display_errors", 0);
				$dom = new DOMDocument();
				$dom->loadHTML($source_code);
				$this->template->remove_all_nodes($dom->getElementsByTagName("body")->item(0), array("table", "td", "tr", "link", "form", "script", "input"));
				ini_set("display_errors", 1);
				echo $dom->saveHTML();
				exit;
			}
			$this->logging_in = false;
			$this->set_page($tmp_page);
		}
		catch(Exception $e)
		{
			echo "<p>" . $this->error_message . "</p>";
			exit;
		}
	}
	
	public function prepare_curl_handler()
	{
	    $user_agent = $_SERVER['HTTP_USER_AGENT'];
        curl_setopt($this->curl_handler, CURLOPT_USERAGENT, $user_agent);
		curl_setopt($this->curl_handler, CURLOPT_RETURNTRANSFER,1);
		
		$dir = realpath(dirname(__FILE__));
		curl_setopt($this->curl_handler, CURLOPT_COOKIEJAR, $dir . "/" . $this->cookie_file_path);
		curl_setopt($this->curl_handler, CURLOPT_COOKIEFILE, $dir . "/" . $this->cookie_file_path);
		if(!is_null($this->template))
		{
			$this->template->get_extra_options($this->curl_handler);
		}
	}
	
	public function set_follow_location($follow)
	{
		curl_setopt($this->curl_handler, CURLOPT_HEADER, 1);
		curl_setopt($this->curl_handler, CURLOPT_HTTPHEADER, array("Content-Type: application/x-www-form-urlencoded"));
		curl_setopt($this->curl_handler, CURLOPT_HEADERFUNCTION, array(__CLASS__,'self::read_header'));
		curl_setopt($this->curl_handler, CURLOPT_AUTOREFERER, true);
		curl_setopt($this->curl_handler, CURLOPT_REFERER, $this->get_root());
		
		$count = 0;
		do
		{
			$url = $this->get_page();
			if(isset(self::$response_meta_info["location"]))
			{
				curl_setopt($this->curl_handler, CURLOPT_HTTPGET, true);
				$this->page = $this->convert_url_to_absolute_url($this->root, self::$response_meta_info["location"]);
				if(!is_null($this->template))
				{
					$this->template->set_page($this->page);
				}
			}
			self::$response_meta_info = array();
			$this->get_page();
			curl_setopt($this->curl_handler, CURLOPT_URL, $this->get_page());
			$data = curl_exec($this->curl_handler);
			$info = curl_getinfo($this->curl_handler);
			$count++;
		}
		while(isset(self::$response_meta_info["location"]) && self::$response_meta_info["location"] != "" && $count<10);
		if(!$follow)
		{
			return substr($data, $info["header_size"]);
		}
		else
		{
			return null;
		}
	}
	
	public function get_source_code($url)
	{
		curl_setopt($this->curl_handler, CURLOPT_HEADER, 0);
		curl_setopt($this->curl_handler, CURLOPT_URL, $url);
		return curl_exec($this->curl_handler);
	}
	
	public function get_data($change_page = true)
	{
		$this->prepare_curl_handler();
		$this->add_proxy();
		$this->add_post();
		$data = $this->set_follow_location(0);
		$meta_info = self::$response_meta_info;
		if(isset($meta_info["proxy_error_no"]) && $meta_info["proxy_error_no"] != 502 && $change_page)
		{
			$data = $this->update_urls($data);
			$data = $this->update_links($data);
			$data = $this->update_posts($data);
			if(!is_null($this->template))
			{
				$data = $this->template->add_javascript_overwrite($data);
			}
			$data = $this->adjust_source_code($data);
		}
		elseif(isset($meta_info["proxy_error_no"]) && $meta_info["proxy_error_no"] == 502)
		{
			$data = "<p>" . $this->error_message . "</p>";
		}
		//dump($this->page);
		return $data;
	}
	
	public function update_urls($data)
	{
		foreach($this->search_url_reg_expr as $index => $reg)
		{
			$matches = $this->get_scan_data("url", $index, $data);
			foreach($matches as $match)
			{
				$trimmed_match = preg_replace("/[ ]*/", "", $match);
				$url = substr($trimmed_match,$reg[3],strlen($trimmed_match)-$reg[3]-$reg[4]);
				$url = $this->convert_url_to_absolute_url($this->get_root_for_link($url),$url);
				$data = str_replace($match, $reg[1] . $url . $reg[2], $data);
			}
		}
		return $data;
	}
	
	public function update_links($data)
	{
		global $cmp_url;
		foreach($this->search_link_reg_expr as $index => $reg)
		{
			$matches = $this->get_scan_data("link", $index, $data);
			foreach($matches as $match)
			{
				$replaced_match = preg_replace("/[ ]*/", "", $match);
				$replaced_match = str_replace('&amp;', '&', $replaced_match);
				$trimmed_match = substr($replaced_match,$reg[3],strlen($replaced_match)-$reg[3]-$reg[4]);
				$exploded_match = explode("/", $trimmed_match);
				$host = "";
				if(count($exploded_match)>=3)
				{
					$host = $exploded_match[0] . "//" . $exploded_match[2] . "/";
				}
				if(strcmp($cmp_url,$host) != 0)
				{
					$pos = strpos($trimmed_match, "'");
					$encoded_match = "";
					$count = 0;
					while($pos !== false && $count < 10)
					{
						$encoded_match .= rawurlencode(substr($trimmed_match, 0, $pos));
						$next_pos = strpos($trimmed_match, "'", $pos + 1);
						
						$sub = substr($trimmed_match, $pos + 1, $next_pos - $pos - 1);
						$encoded_match .= "'" . $sub . "'";
						
						$trimmed_match = substr($trimmed_match, $next_pos + 1);
						$pos = strpos($trimmed_match, "'");
						$count++;
					}
				
					if($trimmed_match == substr($replaced_match,$reg[3],strlen($replaced_match)-$reg[3]-$reg[4]))
					{
						$encoded_match = rawurlencode($trimmed_match);
					}
					elseif($trimmed_match != ";" && $trimmed_match != "'" && substr($encoded_match, strlen($trimmed_match)-1, 1) != "'")
					{
						$encoded_match .= rawurlencode($trimmed_match);
					}
					$url = $this->convert_url_to_absolute_url($this->get_root_for_link($encoded_match), $encoded_match);
					$reg_root = '/http[s]?[:](?:[\/]{2}|[\\\]{2})[\w\d]*[.][\w\d]*[.][\w\d]*(?:\/|\\\)?/';
					preg_match($reg_root, urldecode($url), $root);
					if(count($root) >= 1 && strpos($url, $root[0]) == 0)
					{
						$root = $root[0];
						$page = $url;
					}
					else
					{
						throw new Exception("Invalid url found: " . $match);
					}
					if(strcmp($root, $cmp_url) != 0)
					{
						$prepend = $reg[1];
						if(strtolower(substr($reg[1],0,8)) == "location" && $this->parent != "")
						{
							$prepend = $this->parent . "." . $reg[1];
						}
						$data = str_replace($match, $prepend . $GLOBALS["url"] . "get_url.php?root=" . rawurlencode($root) . "&page=" . $page . $reg[2], $data);
					}
				}
			}
		}
		return $data;
	}
	
	public function update_posts($data)
	{
		preg_match_all("/<form.*method[ ]*=[ ]*[\"\']get[\"\'][^>]*>/is", $data, $matches);
		if(count($matches))
		{
			foreach($matches[0] as $match)
			{
				preg_match("/method[ ]*=[ ]*[\"\']get[\"\']/", $match, $match_get);
				$match_replaced = preg_replace("/" . $match_get[0] . "/", "method='post'", $match);
				$data = str_replace($match, $match_replaced . "\n<input type='hidden' value='1' name='get_to_post'>", $data);
			}
		}
		return $data;
	}
	
	public function adjust_source_code($data)
	{
		ini_set("display_errors", 0);
		if(!is_null($this->template))
		{
			$data = $this->template->clean_up_header($data);
			$data = $this->template->clean_up_javascript($data);
			$dom = new DOMDocument();
			$dom->loadHTML($data);
			$dom = $this->template->remove_code($dom);
			$dom = $this->template->add_legend($dom);
			$dom = $this->template->add_buy_info($dom, $this->root);
			$this->template->translate_page($dom, $_SESSION["language"]);
			ini_set("display_errors", 1);
			$data = $this->template->clean_up_charset($dom->saveHTML());
		}
		return $data;
	}
	
	public function get_scan_data($title, $index, $data)
	{
		$array_name_environment = "environment_scan_" . $title;
		$environment = $this->$array_name_environment;
		$array_name_search = "search_" . $title . "_reg_expr";
		$search = $this->$array_name_search;

		$matches = array();
		$matches[0] = array();
		if(count($environment[$index])!=0)
		{
			foreach($environment[$index] as $env)
			{
				$not = substr($env, 0, 1) == "!";
				if(!$not)
				{
					preg_match_all($env, $data, $matches_temp);
					$matches[0] = array_merge($matches[0], $matches_temp[0]);
				}
				else
				{
					foreach($matches[0] as $index_2 => $match)
					{
						if(preg_match(substr($env, 1), $match))
						{
							unset($matches[0][$index_2]);
						}
					}
				}
			}
		}
		else 
		{
			preg_match_all($search[$index][0], $data, $matches);
		}
		
		$found = array();
		foreach($matches[0] as $match)
		{
			preg_match($search[$index][0], $match, $matches_search);
			$found[] = $matches_search[0];
		}
		return $found;
	}
	
	private function convert_url_to_absolute_url($root, $url)
	{
		$returnUrl = $url;
		if(!is_null($root))
		{
			$is_absolute = preg_match('/http[s]?(?:[:]|%3A)(?:[\/]{2}|[\\\]{2}|%2F%2)[\w\d]*[.][\w\d]*[.][\w\d]*(?:\/|\\\|%2F)?/', $url, $matches);
			if(!$is_absolute)
			{
				$root = rawurldecode($root);
				$tmp_url = rawurldecode($url);
				$first_char_url = substr($tmp_url, 0, 1);
				$first_char_tmp_url = substr($tmp_url, 0, 1);
				$last_char_root = substr($root, strlen($root)-1,1);
				if((($first_char_tmp_url == "/" || $first_char_tmp_url == "\\") && ($last_char_root != "/" && $last_char_root != "\\")) ||
				   (($first_char_tmp_url != "/" && $first_char_tmp_url != "\\") && ($last_char_root == "/" || $last_char_root == "\\")) ||
				   ($first_char_tmp_url == "." && ($last_char_root == "/" || $last_char_root == "\\")))
				{
					$returnUrl = $root . $url;
				}
				elseif(($first_char_tmp_url != "/" && $first_char_tmp_url != "\\" && $last_char_root != "/" && $last_char_root != "\\") ||
					   ($first_char_tmp_url == "." && $last_char_root != "/" && $last_char_root != "\\"))
				{
					$returnUrl = $root . "/" . $url;
				}
				elseif(($first_char_tmp_url == "/" || $first_char_tmp_url == "\\") && ($last_char_root == "/" || $last_char_root == "\\"))
				{
					$returnUrl = substr($root, 0, strlen($root)-1) . $url;
				}
			}
		}

		if(substr($returnUrl, strlen($returnUrl)-3) == "css")
		{
			$this->css[] = $returnUrl;
		}
		
		return $returnUrl;
	}
	
	private function get_root_for_link($url)
	{
		$url = urldecode($url);
		$is_absolute = preg_match('/http[s]?(?:[:]|%3A)[\/]{2}[\w\d]*[.][\w\d]*[.][\w\d]*\/?/', $url, $matches);
		if(!$is_absolute)
		{
			$first_char = substr($url,0,1);
			if($first_char == ".")
			{
				if(substr($this->get_page(), -1) != "/")
				{
					return dirname($this->get_page());
				}
				else
				{
					return $this->get_page();
				}
			}
			elseif($first_char == "/")
			{
				return $this->root;
			}
			else
			{
				if(substr($this->get_page(), -1) != "/")
				{
					return dirname($this->get_page());
				}
				else
				{
					return $this->get_page();
				}
			}
		}
		else
		{
			return null;
		}
	}
	
	private static function read_header($ch, $header) 
	{ 
		$location = self::extract_header('Location:', '\n', $header);
		if ($location)
		{ 
			self::$response_meta_info['location'] = trim($location); 
		}
		
		$error = self::extract_header('HTTP', '\n', $header);
		if ($error)
		{ 
			self::$response_meta_info['proxy_error_no'] = intval(substr(trim($error),5,3)); 
		}
		return strlen($header); 
	}
	
	private static function extract_header($start,$end,$header) 
	{ 
		$pattern = '/'. $start .'(.*?)'. $end .'/'; 
		if (preg_match($pattern, $header, $result)) 
		{ 
			return $result[1]; 
		} 
		else 
		{ 
			return false;
		} 
	}
}
