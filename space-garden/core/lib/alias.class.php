<?php

class Alias
{
	private static $_instance = null;
	private $aliases;
	
	public static function instance()
	{
		if(is_null(self::$_instance)) self::$_instance = new Alias();
		return self::$_instance;
	}

	function __construct()
	{
		$this->aliases = array();
		/** DynamicPage **/
		$this->aliases["browse_pages"] = "DynamicPage_Browser";
		$this->aliases["edit_page"] = "DynamicPage_Editor";
		
		/** CLIENT **/
		$this->aliases["login"] = "Client_Login";
		$this->aliases["logout"] = "Client_Logout";
		$this->aliases["register"] = "Client_Register";
		$this->aliases["client_corner"] = "Client_Corner";
		
		/** PRODUCT **/
		$this->aliases["browse_products"] = "Product_Browser";
		$this->aliases["add_product"] = "Product_Editor";
		$this->aliases["edit_product"] = "Product_Editor";
		$this->aliases["slide_products"] = "Product_Slider";
	}
	
	public function get_alias($key)
	{
		if(isset($this->aliases[$key]))
			return $this->aliases[$key];
		else
			return null;
	}	
}
?>
