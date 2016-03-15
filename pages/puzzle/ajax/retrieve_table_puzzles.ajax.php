<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/puzzle/lib/puzzle_manager.class.php';

if (Session :: get_user_id())
{
    $usermgr = new UserManager(Session :: get_user_id());
    $user = $usermgr->get_user();
    $right = RightManager::UPDATE_RIGHT;
	if(Request::post("all") == true)
	{
    	$right = RightManager::WRITE_RIGHT;
	}
	$pm = new PuzzleManager($user);
	if(!Request::post("shop"))
		echo $pm->get_renderer()->get_puzzle_table($right, Request::post("search")?true:false, true, Request::post("start"), 200);
	else
		echo $pm->get_renderer()->get_puzzle_detailed_form(true, 'puzzle_id', false, true, true, Request::post("start"), 20);
}

?>