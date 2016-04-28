<?

class DynamicPageBrowser
{

	private $manager; 

	function DynamicPageBrowser($manager)
	{
		$this->manager = $manager;
	}
	
	public function get_html()
	{
		if(!is_null(WebApplication::get_user()) && WebApplication::get_user()->is_admin())
		{
			$html = array();
			$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'pages/dynamic_page/javascript/dynamic_page_browser.js"></script>';
			if(Request::get("save"))
			{
				$html[] = Display::display_message(Language::get_instance()->translate("success_saving"), Display::MESSAGE_SUCCESS);
			}
			$html[] = "<p class='title'>" . Language::get_instance()->translate("available_pages") . "</p>";
			$html[] = "<p class='feedback'>";
			$html[] = "<span id='sort_info'>" . Language::get_instance()->translate("drag_to_order") . "</span>";
			$html[] = "<span id='sort_saving' style='display: none'>" . Language::get_instance()->translate("saving_order") . "</span>";
			$html[] = "<span id='sort_error' style='display: none'>" . Language::get_instance()->translate("error_saving") . "</span>";
			$html[]  = "</p>";
			$html[] = $this->manager->get_renderer()->get_table();
			return implode("\n", $html);
		}
		else
			throw new Exception(Language :: get_instance()->translate("not_authorized"));
	}
}

?>