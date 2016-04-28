<?php

require_once Path :: get_path() . "pages/product/lib/product_data_manager.class.php";
require_once Path :: get_path() . "pages/product/lib/product_renderer.class.php";

class ProductManager
{
	const PRODUCT_BROWSER = "Product_Browser";
	const PRODUCT_EDITOR = "Product_Editor";
	const PRODUCT_SLIDER = "Product_Slider";
	const PRODUCT_DELETOR = "Product_Deletor";
	
	private $renderer;
	
	function ProductManager()
	{
		$this->renderer = new ProductRenderer($this);
	}
	
	public function get_data_manager()
	{
		return ProductDataManager::instance($this);
	}
	
	public function get_renderer()
	{
		return $this->renderer;
	}
	
	public function factory($action)
	{
		Language :: get_instance()->add_section_to_translations(LANGUAGE :: PRODUCT);
		switch($action)
		{
			case self::PRODUCT_SLIDER: 
				require_once Path :: get_path() . "pages/product/product_slider.page.php";
				return $this->action_object = new ProductSlider($this);
				break;
			case self::PRODUCT_EDITOR: 
				require_once Path :: get_path() . "pages/product/product_editor.page.php";
				return $this->action_object = new ProductEditor($this);
				break;
			case self::PRODUCT_BROWSER: 
				require_once Path :: get_path() . "pages/product/product_browser.page.php";
				return $this->action_object = new ProductBrowser($this);
				break;
			default:
				throw new Exception(Language :: get_instance()->translate("page_not_found"), 404);
				break;
		}
	}
	
}

?>