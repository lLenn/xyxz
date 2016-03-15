<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/lesson/lib/lesson_manager.class.php';
require_once Path :: get_path() . 'pages/lesson/lib/lesson_page.class.php';

if (Session :: get_user_id())
{
    $usermgr = new UserManager(Session :: get_user_id());
    $user = $usermgr->get_user();
	$lm = new LessonManager($user);
    
	$html = array();
	$type_id = Request::post("type_id");
    $object_id = Request::post("created_object_id");
	
    $title = null;
   	$description = null;
    if(!is_null(Request::post("title_lesson")))
    {
    	$title = Request::post("title_lesson");
    	$description = Request::post("description_lesson");
    }
    Request::clear_post();
	switch($type_id)
    {
    	case LessonPage::END_GAME_TYPE :
    	case LessonPage::GAME_TYPE : 	require_once Path::get_path() . 'pages/game/lib/game_manager.class.php';
    									Request::set_get("page_nr", 2);
    									Request::set_get("id", $object_id);
    								   	if($type_id==LessonPage::END_GAME_TYPE)
    								   	 	Request::set_get("end_game", 1);
    									if(!is_null($title))
    									{
    										Request::set_post("title", $title);
    										Request::set_post("description", $description);
    									}
    								   	$game_manager = new GameManager($user);
    								   	$page = $game_manager->factory(GameManager::GAME_CREATOR);
    								   	$html[] = $page->get_html(true);
    							 	  	break;
    	/*
    	case LessonPage::VIDEO_TYPE : $add_new = Language::get_instance()->translate(1182);
    							 	 break;
    	case LessonPage::QUESTION_TYPE : $add_new = Language::get_instance()->translate(1183);
    							 	 break;
    							 	 break;
    	case LessonPage::SELECTION_TYPE : $add_new = Language::get_instance()->translate(1185);
    							 	 break;
    	*/
    }
    echo implode("\n", $html);
}

?>