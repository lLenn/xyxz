<?php

// Set error reporting.
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once('core/lib/path.class.php');
require_once('core/lib/global.inc.php');

Language::get_instance()->add_section_to_translations(LANGUAGE :: GENERAL);

$user = null;
if (Session :: get_user_id())
{
    $user = ClientDataManager::instance()->retrieve_client_user_account(Session :: get_user_id());
}

try
{
    $app = new WebApplication($user);
    $app->render_page();
}
catch (Exception $exception)
{
	if(Path::get_url_path() == Path :: LOCALHOST || $exception->getCode() == 404)
	{
		Display :: render_error_page($exception->getMessage());
	}
	else
	{
		Display :: render_error_page(Language::get_instance()->translate("error_occurred"));
	}
}                  
?>