<?php
// Set error reporting.
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once '../../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/puzzle/lib/puzzle_manager.class.php';
require_once Path :: get_path() . 'pages/puzzle/difficulty/lib/difficulty_data_manager.class.php';

if (Session :: get_user_id())
{
    $usermgr = new UserManager(Session :: get_user_id());
    $user = $usermgr->get_user();
	$start = Request::post("start");
	$form = $start==0;
	$pm = new PuzzleManager($user);
	if(!Request::post("shop"))
		echo $pm->get_set_manager()->get_renderer()->get_set_table(RightManager::WRITE_RIGHT, true);
	else
		echo $pm->get_set_manager()->get_renderer()->get_set_shop_list($form, $start, 20);
}

?>