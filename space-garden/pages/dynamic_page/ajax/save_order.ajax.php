<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/dynamic_page/lib/dynamic_page_manager.class.php';

if (Session :: get_user_id())
{
	$user = ClientDataManager::instance(null)->retrieve_client_user_account(Session :: get_user_id());
	if($user->is_admin())
	{
		$pages = unserialize(Request::post("pages"));
		$success = true;
		foreach($pages as $page)
		{
			$dynamic_page = new DynamicPage();
			$dynamic_page->set_id(trim($page[1]));
			$dynamic_page->set_order($page[0]);
			$success &= DynamicPageDataManager::instance()->update_dynamic_page_order($dynamic_page);
		}
		echo $success;
		exit;
	}
}

echo 0;

?>