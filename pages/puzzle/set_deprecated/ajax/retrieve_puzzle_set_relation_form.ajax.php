<?php
// Set error reporting.
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once '../../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/puzzle/lib/puzzle_manager.class.php';

if (Session :: get_user_id())
{
    $usermgr = new UserManager(Session :: get_user_id());
    $user = $usermgr->get_user();

    $form_search = false;
    
	$pm = new PuzzleManager($user);
	echo $pm->get_set_manager()->get_renderer()->get_puzzle_set_relation_form(Request::post("set_id"), RightManager::READ_RIGHT, $form_search);
}

?>