<?php
require_once Path :: get_path() . "pages/menu/lib/menu_manager.class.php";
//require_once Path :: get_path() . "pages/statistics/statistics_manager.class.php";

class DynamicPageManager
{

	private $page;
	private $user;
	
	function __construct($user)
	{
		$this->setPage();
		$this->user = $user;
		if(!is_null($this->user))
			$this->register_action();
	}
	
	public function render_page()
	{		
		$group_id = 0;
		if(!is_null($this->user) && !is_null($this->user->get_group()))
			$group_id = $this->user->get_group_id();
        echo Display :: get_header($group_id==GroupManager::GROUP_PUPIL_ID);
		$body = $this->render_body();
		/*
		if(!Error::get_instance()->get_result() || Error::get_instance()->get_message() != '' || Error::get_instance()->get_debug_message() != '')
		{
			dump(Error::get_instance()->get_message());
			dump(Error::get_instance()->get_debug_message());
		}
		else
		{
		*/
			echo $body;
		//}
        echo Display :: get_footer();
	}
	
	//-- PRIVATE FUCNTIONS --//
	private function render_body()
	{
		global $start_time;
		$html = array();
		
		$action = Alias::instance()->get_alias($this->page);
		$component = substr($action,0,strpos($action,"_"));
		require_once Path :: get_path() . "pages/".strtolower($component)."/lib/".strtolower($component)."_manager.class.php";
		$class = $component . "Manager";
		$manager = new $class($this->user);
		$page = $manager->factory($action);
		$page_html = $page->get_html();
		
		$html[] = '<div id="main_container">';
		$html[] = '<div id="main_page_container">';
		
		//if(!is_null($this->user))
		//	$menu = new MenuManager($this->user);
		
		$html[] = '<div id="menu_container">';
		$html[] = '<div id="menu_image"></div>';
		/*
		$html[] = '<div style="position: absolute; margin-left: 30px; margin-top: -25px;">';
		$html[] = $manager->get_renderer()->get_icon();
		$html[] = '</div>';
		$html[] = '<div style="position: absolute; margin-left: 125px; margin-top: -25px;">';
		$html[] = $page->get_title();
		$html[] = '</div>';
		*/
		$html[] = '</div>';
		if(!is_null($this->user) && (!is_null($this->user->get_group()) || $this->user->is_admin()))
			$html[] = MenuRenderer::get_prerendered_menu($this->user);
		$html[] = '<br class="clearfloat"/>';
		
		$html[] = '<div id="page_container">';
		
		$html[] = '<div id="sub_menu_container" class="hide_menu_right">';
		if(!is_null($this->user))
			$html[] = UserRenderer::get_menu_profile_html($this->user);
		$html[] = '<br />';
		$html[] = '<br />';
		$html[] = $manager->get_renderer()->get_actions();
		$html[] = '<br />';
		if($this->page != 'welcome_page' && !is_null($this->user))
		{
			$html[] = '<ul class="menu menu_actions menu_vertical"><li><a id="back_button" href="' . Url::get_previous_url() . '">' . Language::get_instance()->translate(422) . '</a></li></ul>';
			$html[] = '<br />';
		}
		$html[] = $page->get_description();
		$html[] = '<br class="clearfloat">';
		$html[] = '</div>';
		
		$html[] = '<div id="sub_page_container">';
		$html[] = '<link rel="stylesheet" type="text/css" href="'.Path::get_url_path().'layout/'.strtolower($component).'_layout.css" />';
		$html[] = $page_html;
		$html[] = '</div>';
		
		$html[] = '</div>';
		
		$html[] = '<br class="clearfloat" />';
		
		$html[] = '<div id="footer" style="text-align: right">';
		$html[] = '<p class="grey_font" style="float: right; margin-right: 50px;">';
		if(!is_null(Session::retrieve("logged_as_admin")))
			$html[] = '<a class="grey_font" style="text-decoration: none; font-weight: bold;" href="' . Url::create_url(array("page"=>"change_member")) . '">' . Language::get_instance()->translate(767) . '</a> - ';
		if(!is_null($this->user) && (is_null($this->user->get_group()) || $this->user->get_group()->get_id() != GroupManager::GROUP_GUEST_ID))
			$html[] = '<a class="grey_font" style="text-decoration: none; font-weight: bold;" href="' . Url::create_url(array("page"=>"browse_help")) . '">' . Language::get_instance()->translate(766) . '</a> - ';
		$html[] = '&copy; 2010 - 2014 ' . Language::get_instance()->translate(502) . '<br/>';
		$end_time = microtime(true);
		$html[] = 'Page generated in ' . number_format(($end_time - $start_time), 4) . 's.';
		$html[] = '</p>';
		$html[] = '</div>';
		
		$html[] = '</div>';
	  	$html[] = '</div>';
		return implode("\n", $html);
	}
	
	private function setPage()
	{
		$this->page = Request::get('page');
		if(is_null($this->page)) $this->page = 'welcome_page';
	}
	
	private function register_action()
	{
		$statistics_manager = new StatisticsManager($this->user);
		$statistics_manager->register_action();
	}
}
?>