<?php

require_once '../../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/lesson/lib/lesson_manager.class.php';

if (Session :: get_user_id())
{
    $usermgr = new UserManager(Session :: get_user_id());
    $user = $usermgr->get_user();
	$map = null;
	if(is_numeric(Request::post("map_id")))
		$map = RightDataManager::instance(null)->retrieve_location_user_map_by_id(Request::post("map_id"), $user->get_id());
	elseif(Request::post("map_id")=="others")
		$map = "others";
	$gm = new LessonManager($user);
	echo $gm->get_lesson_excercise_manager()->get_renderer()->get_lesson_excercise_table($user->get_id(), true, false, $map, true);
}

?>