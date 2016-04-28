<?

class ProductBrowser
{

	private $manager;

	function ProductBrowser($manager)
	{
		$this->manager = $manager;
	}
	
	public function save_changes()
	{
		if(Request::get("saved"))
		{
			return Display::display_message(Language::get_instance()->translate("success_saving_product"), Display::MESSAGE_SUCCESS);
		}
		elseif(!is_null(Request :: get("delete_product")) && is_numeric(Request :: get("delete_product")))
		{
			$success = $this->manager->get_data_manager()->delete_product(Request :: get("delete_product"));
			if($success)
			{
				return Display::display_message(Language::get_instance()->translate("success_deleting_product"), Display::MESSAGE_SUCCESS);
			}
			else
			{
				return Display::display_message(Language::get_instance()->translate("error_deleting"), Display::MESSAGE_ERROR);
			}
		}
	}
	
	public function get_html()
	{
		if(!is_null(WebApplication::get_user()) && WebApplication::get_user()->is_admin())
		{
			$html = array();
			$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'pages/product/javascript/product_browser.js"></script>';
			$html[] = $this->save_changes();
			$html[] = "<p class='title'>" . Language::get_instance()->translate("manage_products") . "</p>";
			$html[] = "<p class='actions'>" . $this->manager->get_renderer()->get_actions() . "</p>";
			$html[] = "<br class='clear_float'>";
			$html[] = "<p class='feedback'>";
			$html[] = "<span id='sort_info'>" . Language::get_instance()->translate("drag_to_order") . "</span>";
			$html[] = "<span id='sort_saving' style='display: none'>" . Language::get_instance()->translate("saving_order") . "</span>";
			$html[] = "<span id='sort_error' style='display: none'>" . Language::get_instance()->translate("error_saving") . "</span>";
			$html[]  = "</p>";
			$html[] = $this->manager->get_renderer()->get_table();
			return implode("\n", $html);
		}
		else
		{
			throw new Exception(Language :: get_instance()->translate("not_authorized"));
		}
	}
}

?>