<?php

require_once '../../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/puzzle/lib/puzzle_manager.class.php';

if (Session :: get_user_id())
{
    $usermgr = new UserManager(Session :: get_user_id());
    $user = $usermgr->get_user();

	$tm = new PuzzleManager($user);
	echo $tm->get_theme_manager()->get_renderer()->get_table();
}

?>