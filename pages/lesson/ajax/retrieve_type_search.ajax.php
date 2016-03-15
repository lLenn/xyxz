<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/lesson/lib/lesson_manager.class.php';
require_once Path :: get_path() . 'pages/lesson/lib/lesson_page.class.php';

if (Session :: get_user_id())
{
    $usermgr = new UserManager(Session::get_user_id());
    $user = $usermgr->get_user();
    
	$lm = new LessonManager($user);
    switch(Request::post("type_id"))
    {
    	case LessonPage::PUZZLE_TYPE : echo $lm->get_renderer()->get_lesson_page_puzzle_form();
    							 	   break;
    	case LessonPage::GAME_TYPE : echo $lm->get_renderer()->get_lesson_page_game_form();
    							 	 break;
    	case LessonPage::VIDEO_TYPE : echo $lm->get_renderer()->get_lesson_page_video_form();
    							 	 break;
    	case LessonPage::QUESTION_TYPE : echo $lm->get_renderer()->get_lesson_page_question_form();
    							 	 break;
    	case LessonPage::SELECTION_TYPE : echo $lm->get_renderer()->get_lesson_page_selection_form();
    							 	 break;
    }
}

?>