<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/puzzle/lib/puzzle_manager.class.php';

if (Session :: get_user_id())
{
    $usermgr = new UserManager(Session::get_user_id());
    $user = $usermgr->get_user();

	$sm = new PuzzleManager($user);
	echo $sm->get_renderer()->get_puzzle_search(false, false);
}

?>