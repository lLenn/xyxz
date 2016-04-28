<?php

// Set error reporting.
error_reporting(E_ALL);
ini_set("display_errors", 1);
$start_time = microtime(true);

require('core/lib/path.class.php');
require('core/lib/global.inc.php');

try
{
	$usermgr = new UserManager(Session :: get_user_id());
	if (!is_null($usermgr->get_user()))
	{
		if($usermgr->get_user()->get_group_id() == GroupManager::GROUP_TRANSLATOR_ID)
			Request::set_get("page", "translate");
		Url::save_current_url();
		Session::clean();
	    $user = $usermgr->get_user();
		if(Session::retrieve("logged_as_admin"))
		{
       		Session :: register('language', $user->get_language());
        	Language :: get_instance()->set_language($user->get_language());
        	Language :: get_instance()->add_section_to_translations(Language::GENERAL, true);
		}
		$message_manager = new MessageManager($user);
		$message_manager->create_location_response_messages();
		$message_manager->create_location_credits_messages();
		$statistics_manager = new StatisticsManager($user);
		$statistics_manager->suggest_continuations();
		$page_manager = new DynamicPageManager($user);
		$page_manager->render_page();
	}
	else
	{
	    $page = UserManager::USER_LOGIN;
	    if(!is_null(Alias::instance()->get_alias(Request::get('page'))) && Alias::instance()->get_alias(Request::get('page')) != $page && (Alias::instance()->get_alias(Request::get('page')) == UserManager::USER_REGISTER || Alias::instance()->get_alias(Request::get('page')) == "Article_Competition"))
	    {
	    	$page = Alias::instance()->get_alias(Request::get('page'));
			$page_manager = new DynamicPageManager(null);
			$page_manager->render_page();
	    }
	    else
	    {
	    	$page = $usermgr->factory($page);
	    	echo Display::get_header(true);
	    	echo $page->get_html();
	    	echo Display::get_footer();
	    }
	    if(!is_null(Request::get("guest")))
	    {
	    	Request::set_get("page", "index.php");
			$sm = new StatisticsManager($usermgr->get_user());
			$sm->get_data_manager()->insert_statistics($sm->get_data_manager()->retrieve_statistics_from_visit());
	    }
	}
}
catch (Exception $exception)
{
	Display :: render_error_page($exception->getMessage());
}

if(!empty($error_html))
{
	require_once Path::get_path() . "/pages/help/lib/help.class.php";
	require_once Path::get_path() . "/pages/help/lib/help_data_manager.class.php";
	$help = new Help();
	$help->set_title("Error found");
	$help->set_text(addslashes(implode("\n", $error_html)));
	$help->set_user_id(0);
	$help->set_date(time());
	$hm = HelpDataManager::instance(null);
	if(!$hm->check_help($help))
		$hm->insert_help($help);
}
?>