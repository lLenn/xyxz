<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/puzzle/lib/puzzle_manager.class.php';

if (Session :: get_user_id())
{
    $usermgr = new UserManager(Session :: get_user_id());
    $user = $usermgr->get_user();

	$pm = new PuzzleManager($user);
	$puzzle = $pm->get_data_manager()->retrieve_puzzle(Request::get('puzzle_id'));
	if(!is_null($puzzle))
	{
		$setup_only = false;
		if(!is_null(Request::get('setup_only')) && Request::get('setup_only') == 1)
			$setup_only = true;
		$pm->get_renderer()->get_puzzle_image($puzzle->fen, $puzzle->moves, Request::get('puzzle_id'), $setup_only);
	}
	else
		echo Language::get_instance()->translate(250);
}

?>