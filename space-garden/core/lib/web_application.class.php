<?php
	
class WebApplication
{
	private $page;
	private $action;
	private static $user = null;

	function __construct($user)
	{
		$this->set_page();
		self::$user = $user;
	}
	
	public function render_page()
	{
		$display_array["head"] = Display::get_header();
        $display_array["language_chooser"] = $this->render_language_chooser();
        $display_array["menu"] = $this->render_menu();
        $display_array["client_menu"] = $this->render_client_menu();
        $display_array["footer"] = $this->render_footer();
        $component = substr($this->action,0,strpos($this->action,"_"));
        $css = '';
        if(file_exists(Path::get_path() . 'layout/' . strtolower($component) . '_layout.css'))
        {
        	$css = '<link rel="stylesheet" type="text/css" href="' . Path::get_url_path() . 'layout/' . strtolower($component) . '_layout.css"/>';
        }
        $display_array["body"] = $css . $this->page->get_html();
		Display :: render_page($display_array);
	}
	
	public function render_language_chooser()
	{
		$html = array();
		$languages = LanguageDataManager::instance()->retrieve_all_languages();
		$count = count($languages);
		$html[] = '<div class="float_container" style="width: ' . $count*75 . 'px;">';
		foreach ($languages as $language)
		{
			$html[] = '<div class="language_float"'.($language->language == Language::get_instance()->get_language()?'style="background-color: #9abfb0;"':'').'>';
			$html[] = '<a href="Javascript:change_language(\'' . $language->language . '\');" class="language_link">' . Utilities::html_special_characters($language->full_name) . '</a>';
			$html[] = '</div>';
		}
		$html[] = '<br class="clear_float">';
		$html[] = '</div>';
		return implode("\n", $html);
	}
	
	public function render_footer()
	{
		return '<div class="float_container"><p style="margin: 4px; padding-right: 10px; float: right;">' . Language :: get_instance()->translate("developed_by") . ' Tristan Verheecke</p><br class="clear_float"></div>';
	}
	
	public function render_menu()
	{
		return MenuRenderer::get_menu();
	}
	
	public function render_client_menu()
	{
		return MenuRenderer::get_client_menu();
	}
	
	public static function get_user()
	{
		return self::$user;
	}
	
	private function set_page()
	{
		$page = REQUEST::get('page');
		if(is_null($page)) $page = 'home';

		$dynamic_page = DynamicPageDataManager::instance()->retrieve_dynamic_page($page);
		if(is_null($dynamic_page))
		{
			$this->action = Alias::instance()->get_alias($page);
			$component = substr($this->action,0,strpos($this->action,"_"));
			$component_camel = Utilities::camelcase_to_underscores($component);
			if(file_exists(Path :: get_path() . "pages/".strtolower($component_camel)."/lib/".strtolower($component_camel)."_manager.class.php"))
			{
				require_once Path :: get_path() . "pages/".strtolower($component_camel)."/lib/".strtolower($component_camel)."_manager.class.php";
				$class = $component . "Manager";
				$manager = new $class(self::$user);
				$this->page = $manager->factory($this->action);
			}
		}
		else
		{
			$manager = new DynamicPageManager();
			$this->page = $manager->factory(DynamicPageManager::DYNAMICPAGE_VIEWER);
			$this->page->set_id($page);
		}	
		if(is_null($this->page))
		{
			throw new Exception(Language :: get_instance()->translate("page_not_found"), 404);
		}
	}

}

?>