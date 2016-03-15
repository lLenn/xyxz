<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/puzzle/lib/puzzle_manager.class.php';

if (Session :: get_user_id())
{
	$user = UserDataManager::instance(null)->retrieve_user(Session :: get_user_id());
	if(RightManager::instance()->get_right_location_object("Puzzle", $user, Request::post("puzzle_id")) >= RightManager::READ_RIGHT)
	{
		RightManager::instance()->delete_location_object_user_right(RightManager::PUZZLE_LOCATION_ID, Session :: get_user_id(), Request::post("puzzle_id"));
		if($user->is_admin())
			PuzzleDataManager::instance(null)->delete_puzzle(Request::post("puzzle_id"));
		else
			RightManager::instance()->delete_location_object_user_right(RightManager::PUZZLE_LOCATION_ID, Session :: get_user_id(), Request::post("puzzle_id"));
		
    	if(mysqli_errno(DataManager::get_connection()->get_connection()) == 0)
    		echo "&message=" . Language::get_instance()->translate(247) . "&message_type=good";
    	else
    		echo "&message=" . Language::get_instance()->translate(248) . "&message_type=error";
	} 
	else 	
		echo "&message=" . Language::get_instance()->translate(249) . "&message_type=error";
}

?>