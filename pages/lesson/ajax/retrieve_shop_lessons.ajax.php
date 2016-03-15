<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/lesson/lib/lesson_manager.class.php';

if (Session :: get_user_id())
{
    $usermgr = new UserManager(Session :: get_user_id());
    $user = $usermgr->get_user();

    if(Request::post("start") == 0)
    	$form = true;
    else
    	$form = false;
	$gm = new LessonManager($user);
	echo $gm->get_renderer()->get_lesson_shop_list($form, Request::post("start"), 20);
}

?>