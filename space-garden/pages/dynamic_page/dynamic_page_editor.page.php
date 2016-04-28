<?

class DynamicPageEditor
{

	private $manager; 
	private $id;

	function DynamicPageEditor($manager)
	{
		$this->manager = $manager;
		$this->id = Request::get("id");
		if(is_null($this->id))
			$this->id = -1;
	}
	
	public function save_changes()
	{
		$dynamic_page = null;
		$output = array();
    	if($_POST)
    	{
	    	$post_result = $this->manager->get_data_manager()->retrieve_dynamic_page_contents_from_post();
			if(isset($post_result) && $post_result["error"]=="")
			{
				$save_result = $this->manager->get_data_manager()->update_dynamic_page_contents($post_result["dynamic_page"]);
				if($save_result["error"])
				{
					$output[] = Display::display_message($save_result["error"], Display::MESSAGE_ERROR);
				}
				elseif(!$save_result["result"])
				{
					$output[] = Display::display_message(Language::get_instance()->translate("unknown_error_saving"), Display::MESSAGE_ERROR);
				}
				else
				{
					header("location: " . Url::create_url(array("page" => "browse_pages", "saved" => "saved"))); 
					exit();
				}
			}
			else
			{
				$output[] = Display::display_message($post_result["error"], Display::MESSAGE_ERROR);
			}
			$dynamic_page = $post_result["dynamic_page"];
    	}
    	else
    	{
    		$dynamic_page = $this->manager->get_data_manager()->retrieve_dynamic_page($this->id);
    	}
    	return array("dynamic_page" => $dynamic_page, "result" => implode("\n", $output));
	}
	
	public function get_html()
	{
		if(!is_null(WebApplication::get_user()) && WebApplication::get_user()->is_admin())
		{
			$html = array();
			$save_results = $this->save_changes();
			if(!is_null($save_results["dynamic_page"]))
			{
				$html[] = $save_results["result"];
				$html[] = "<p class='title'>" . Language::get_instance()->translate("editing") . " : " . Request::get("id") . "</p>";
				$html[] = $this->manager->get_renderer()->get_form($save_results["dynamic_page"]);
			}
			else
			{
				$html[] = Display::display_message(Language :: get_instance()->translate("page_not_exist"), Display::MESSAGE_ERROR);
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