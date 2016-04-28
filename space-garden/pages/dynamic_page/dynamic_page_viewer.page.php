<?

class DynamicPageViewer
{

	private $manager; 
	private $id;

	function DynamicPageViewer($manager)
	{
		$this->manager = $manager;
		$this->id = Request::get("id");
		if(is_null($this->id))
			$this->id = -1;
	}
	
	public function get_html()
	{
    	$dynamic_page = $this->manager->get_data_manager()->retrieve_dynamic_page($this->id);
    	$content = $dynamic_page->get_content_by_language(Language::get_instance()->get_language());
		if(!is_null($content))
		{
			$html = array();
			$page_content = $content->get_page_content();
			if($this->id == 'products')
			{
				$slider = preg_match("/{product_slider,[ ]*[0-9]*}/", $page_content, $slider_matches);
				if($slider != false)
				{
					require_once Path :: get_path() . "/pages/product/lib/product_manager.class.php";
					$width = intval(substr($slider_matches[0], 16, strlen($slider_matches[0])-16));
					$product_manager = new ProductManager();
					$page = $product_manager->factory(ProductManager :: PRODUCT_SLIDER);
					$html_slider = $page->get_html($width);
					$page_content = preg_replace("/".$slider_matches[0]."/", $html_slider, $page_content);	
				}
			}
			$html[] = "<div style='position: relative;'>" . $page_content . "</div>";
			if($this->id == 'contact_data')
			{
				$html[] = '<link rel="stylesheet" type="text/css" href="' . Path::get_url_path() . 'layout/client_layout.css"/>';
				$client_manager = new ClientManager();
				$page = $client_manager->factory(ClientManager::CLIENT_REGISTER);
				$html[] = '<br/>';
				$html[] = $page->get_html(false);
			}
			return implode("\n", $html);
		}
		else
		{
            if($this->id == "home")
            {
                header("location: index.php?page=login");
                exit;
            }
			throw new Exception(Language :: get_instance()->translate("page_has_no_content"), 404);
		}
	}
	
	public function set_id($id)
	{
		$this->id = $id;
	}
}

?>