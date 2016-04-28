<?

class ProductEditor
{

	private $manager; 
	private $id;

	function ProductEditor($manager)
	{
		$this->manager = $manager;
		$this->id = Request::get("id");
		if(is_null($this->id))
			$this->id = -1;
	}
	
	public function save_changes()
	{
		$product = null;
		$output = array();
    	if($_POST)
    	{
	    	$post_result = $this->manager->get_data_manager()->retrieve_product_from_post();
			if(isset($post_result) && $post_result["error"]=="")
			{
				if($this->id != -1)
					$save_result = $this->manager->get_data_manager()->update_product($post_result["product"]);
				else
					$save_result = $this->manager->get_data_manager()->insert_product($post_result["product"]);
				if(!$save_result)
					$output[] = Display::display_message(Language::get_instance()->translate("error_saving"), Display::MESSAGE_ERROR);
				else
				{
					header("location: " . Url::create_url(array("page" => "browse_products", "saved" => "saved"))); 
					exit();
				}
			}
			else
			{
				$output[] = Display::display_message($post_result["error"], Display::MESSAGE_ERROR);
			}
			$product = $post_result["product"];
    	}
    	else
    	{
    		$product = $this->manager->get_data_manager()->retrieve_product($this->id);
    	}
    	return array("product" => $product, "result" => implode("\n", $output));
	}
	
	public function get_html()
	{
		if(!is_null(WebApplication::get_user()) && WebApplication::get_user()->is_admin())
		{
			$html = array();
			$save_results = $this->save_changes();
			if(!is_null($save_results["product"]))
			{
				$html[] = $save_results["result"];
				$html[] = "<p class='title'>" . Language::get_instance()->translate("editing_product") . " : " . Request::get("id") . "</p>";
				$html[] = $this->manager->get_renderer()->get_form($save_results["product"]);
			}
			else
			{
				$html[] = "<p class='title'>" . Language::get_instance()->translate("adding_product") . " :</p>";
				$html[] = $this->manager->get_renderer()->get_form($save_results["product"]);
			}
			return implode("\n", $html);
		}
		else
		{
			throw new Exception(Language :: get_instance()->translate("not_authorized"));
		}
	}
}

?>