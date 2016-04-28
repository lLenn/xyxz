<?php

require_once Path :: get_path() . "pages/dynamic_page/lib/dynamic_page_data_manager.class.php";
require_once Path :: get_path() . "pages/dynamic_page/lib/dynamic_page_renderer.class.php";

class DynamicPageManager
{
	const DYNAMICPAGE_HOME = "DynamicPage_Home";
	const DYNAMICPAGE_BROWSER = "DynamicPage_Browser";
	//const DYNAMICPAGE_CREATOR = "DynamicPage_Creator";
	const DYNAMICPAGE_EDITOR = "DynamicPage_Editor";
	const DYNAMICPAGE_VIEWER = "DynamicPage_Viewer";
	//const DYNAMICPAGE_DELETOR = "DynamicPage_Deletor";
	
	private $renderer;
	
	function DynamicPageManager()
	{
		$this->renderer = new DynamicPageRenderer($this);
	}
	
	public function get_data_manager()
	{
		return DynamicPageDataManager::instance($this);
	}
	
	public function get_renderer()
	{
		return $this->renderer;
	}
	
	public function factory($action)
	{
		Language :: get_instance()->add_section_to_translations(Language::DYNAMIC_PAGE);
		switch($action)
		{
			case self::DYNAMICPAGE_VIEWER: 
				require_once Path :: get_path() . "pages/dynamic_page/dynamic_page_viewer.page.php";
				return $this->action_object = new DynamicPageViewer($this);
				break;
			case self::DYNAMICPAGE_EDITOR: 
				require_once Path :: get_path() . "pages/dynamic_page/dynamic_page_editor.page.php";
				return $this->action_object = new DynamicPageEditor($this);
				break;
			case self::DYNAMICPAGE_BROWSER: 
				require_once Path :: get_path() . "pages/dynamic_page/dynamic_page_browser.page.php";
				return $this->action_object = new DynamicPageBrowser($this);
				break;
			default:
				throw new Exception(Language :: get_instance()->translate("page_not_found"), 404);
				break;
		}
	}
	
}

?>