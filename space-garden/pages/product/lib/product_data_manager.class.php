<?php

require_once Path :: get_path() . 'pages/product/lib/product.class.php';

class ProductDataManager extends DataManager
{
	const TABLE_NAME = 'product';
	const CLASS_NAME = 'Product';
	
	public static function instance()
	{
		parent::$_instance = new ProductDataManager();
		return parent::$_instance;
	}

	function insert_product($product)
	{
		return self::insert(self::TABLE_NAME, $product);
	}
	
	function update_product($product)
	{
		return self::update_by_id(self::TABLE_NAME, $product);
	}
	
	function delete_product($product_id)
	{
		return self::delete_by_id(self::TABLE_NAME, $product_id);
	}
	
	public function update_product_order($product)
	{
		$condition = "id = '" . $product->get_id() . "'";
		$properties = new CustomProperties();
		$properties->add_property("order", $product->get_order());
		return parent::update(self::TABLE_NAME, $properties, $condition);
	}
	
	public function retrieve_product($id)
	{
		return parent::retrieve_by_id(self::TABLE_NAME,self::CLASS_NAME,$id);
	}
	
	public function retrieve_products()
	{
		$order = "`order`";
		return parent::retrieve(self::TABLE_NAME,self::CLASS_NAME,$order);
	}
	
	public function get_next_product_order()
	{
		$order = self::count(self::TABLE_NAME);
		return $order + 1;
	}
	
	/*
	 * Function retrieve_product_from_post()
	 * Retrieves the product from the post and validates the input
	 */
	function retrieve_product_from_post()
	{
		$output = "";
		if(is_null(Request::post("name")) || Request::post("name") == '' ||
		   is_null(Request::post("description")) || Request::post("description") == '' ||
		   (Request::post("media_type") == "image" && (is_null(Request::post("image_url")) || Request::post("image_url") == '')) ||
		   (Request::post("media_type") == "video" && (is_null(Request::post("video_embed")) || Request::post("video_embed") == '')))
		{
			$output .= Language::get_instance()->translate("fill_in_required") . "<br/>";
		}
	
		$product = $this->retrieve_product(Request::get('id'));
		if($product == null)
		{
			$product = new Product();
			$product->set_order($this->get_next_product_order());
		}
		
		$product->set_name(addslashes(Request::post("name")));
		$product->set_description(addslashes(Request::post("description")));
		if(Request::post("media_type") == "image")
		{
			$product->set_image(addslashes(Request::post("image_url")));
		}
		if(Request::post("media_type") == "video")
		{
			$product->set_video(addslashes(Request::post("video_embed")));
		}
		
		return array("product" => $product, "error" => $output);
	}
}

?>