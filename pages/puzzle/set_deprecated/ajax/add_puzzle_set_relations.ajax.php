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
	if(Request::post('puzzle_id'))
	{
		$success = $pm->get_set_manager()->get_data_manager()->insert_set_puzzles(Request::post('set_id'), Request::post('puzzle_id'));
		if($success == 0) echo '<p class="good">' . Language::get_instance()->translate(301) . '</p>';
		else echo '<p class="error">' . Language::get_instance()->translate(302) . '</p>';
	}
	$object_right = RightManager::instance()->get_right_location_object(RightManager::SET_LOCATION_ID, $usermgr->get_user(), Request::post('set_id'));
	echo $pm->get_set_manager()->get_renderer()->get_set_puzzles_info(Request::post('set_id'), $object_right);
}

?>