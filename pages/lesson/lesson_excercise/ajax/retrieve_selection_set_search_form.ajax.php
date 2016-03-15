<?php

require_once '../../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/selection/lib/selection_manager.class.php';

if (Session :: get_user_id())
{
    $usermgr = new UserManager(Session::get_user_id());
    $user = $usermgr->get_user();

	$qm = new SelectionManager($user);
	echo $qm->get_selection_set_manager()->get_renderer()->get_selection_set_search(false, true, "submit_selection_set_search_form");
}

?>