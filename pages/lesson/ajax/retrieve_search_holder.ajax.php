<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/lesson/lib/lesson_manager.class.php';
require_once Path :: get_path() . 'pages/lesson/lib/lesson_page.class.php';

if (Session :: get_user_id())
{
    $usermgr = new UserManager(Session :: get_user_id());
    $user = $usermgr->get_user();
	$lesson_manager = new LessonManager($user);
    
	$html = array();
	$lesson_page = new LessonPage();
	$lesson_page->set_type_object_id(Request::post("created_object_id"));
	switch(Request::post("type_id"))
    {
    	case LessonPage::PUZZLE_TYPE : $html[] = $lesson_manager->get_renderer()->get_lesson_page_puzzle_form($lesson_page);
    							 	   break;
    	case LessonPage::GAME_TYPE : $html[] = $lesson_manager->get_renderer()->get_lesson_page_game_form($lesson_page);
    							 	 break;
    	case LessonPage::VIDEO_TYPE : $html[] = $lesson_manager->get_renderer()->get_lesson_page_video_form($lesson_page);
    							 	 break;
    	case LessonPage::QUESTION_TYPE : $html[] = $lesson_manager->get_renderer()->get_lesson_page_question_form($lesson_page);
    							 	 break;
    	case LessonPage::END_GAME_TYPE : $html[] = $lesson_manager->get_renderer()->get_lesson_page_end_game_form($lesson_page);
    							 	 break;
    	case LessonPage::SELECTION_TYPE : $html[] = $lesson_manager->get_renderer()->get_lesson_page_selection_form($lesson_page);
    							 	 break;
    }
    echo implode("\n", $html);
}
?>