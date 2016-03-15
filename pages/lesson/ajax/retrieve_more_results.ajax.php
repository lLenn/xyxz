<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/lesson/lib/lesson_manager.class.php';

if (Session :: get_user_id())
{
    $usermgr = new UserManager(Session :: get_user_id());
    $user = $usermgr->get_user();

	$gm = new LessonManager($user);
    switch(Request::post("type_id"))
    {
    	case LessonPage::PUZZLE_TYPE : 
									   require_once Path :: get_path() . 'pages/puzzle/lib/puzzle_manager.class.php';
    								   $pm = new PuzzleManager($user);
    		   						   echo $pm->get_renderer()->get_puzzle_detailed_form(true, 'object_id', true, false, false, Request::post("start"), 20);
    							 	   break;
    }
}

?>