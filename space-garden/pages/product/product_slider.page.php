<?

class ProductSlider
{

	private $manager;

	function ProductSlider($manager)
	{
		$this->manager = $manager;
	}
	
	public function get_html($width = 500)
	{
		$html = array();
		
		$width_product = ($width/6)*4.8;
		$width_menu = ($width/6)*0.86;
		$extra_width_menu = ($width/6)*0.98;
		$height = 470*((1/600)*$width);
		$height_product = 385*((1/470)*$height);
		$extra_height_menu = 64*((1/470)*$height);
		$height_menu = 58*((1/470)*$height);
		
		$html[] = '<link rel="stylesheet" type="text/css" href="'.Path::get_url_path().'layout/product_slider_layout.css" />';
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'pages/product/javascript/product_slider.js"></script>';
		$html[] = "<div id='product_slider' style='width: " . $width . "px; height: " . $height . "px'>";
		$products = ProductDataManager::instance()->retrieve_products();
		$html[] = "<div id='product_div' style='width: " . $width_product . "px; height: " . $height . "px'>";
		foreach($products as $product)
		{
			$html[] = "<div class='product ".$product->get_order()."' style='left: " . $width_product . "px; width: " . $width_product . "px'>";
			$html[] = $this->manager->get_renderer()->get_html($product, $width_product, $height_product);
			$html[] = "</div>";
		}
		$html[] = "</div>";
		$html[] = "<div id='side_product_div' style='width: " . $extra_width_menu . "px'>";
		foreach($products as $product)
		{
			$html[] = "<div class='mini_product ".$product->get_order()."' style='height: " . $extra_height_menu . "px'>";
			$html[] = $this->manager->get_renderer()->get_product_html($product, $width_menu, $height_menu, true);
			$html[] = "</div>";
		}
		$html[] = "</div>";
		$width_control = ($width/600)*32;
		$html[] = "<img id='control_image' src='" . Path::get_url_path() . "layout/images/buttons/pause.gif' style='width: " . $width_control . "px; height: " . $width_control . "px'>";
		
		$html[] = "</div>";
        $html[] = "<script type=\"text/javascript\">
					/* <![CDATA[ */
					var _slide_left = " . $width_product . "
					/* ]]> */
					</script>\n";
		return implode("\n", $html);
	}
}

?>