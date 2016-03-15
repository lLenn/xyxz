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
	$add_new = "";
    switch(Request::post("type_id"))
    {
    	case LessonPage::PUZZLE_TYPE : $add_new = Language::get_instance()->translate(1180);
    							 	   break;
    	case LessonPage::GAME_TYPE : $add_new = Language::get_instance()->translate(1181);
    							 	 break;
    	case LessonPage::VIDEO_TYPE : $add_new = Language::get_instance()->translate(1182);
    							 	 break;
    	case LessonPage::QUESTION_TYPE : $add_new = Language::get_instance()->translate(1183);
    							 	 break;
    	case LessonPage::END_GAME_TYPE : $add_new = Language::get_instance()->translate(1184);
    							 	 break;
    	case LessonPage::SELECTION_TYPE : $add_new = Language::get_instance()->translate(1185);
    							 	 break;
    }
    if(Request::post("type_id")!=LessonPage::TEXT_TYPE)
    {
		$html[] = "<br class='clearfloat'/>";
		$html[] = "<div class='record_name_required'>" . $add_new . " :</div>";
		$html[] = "<div id='create_object_button'>";
		$html[] = '<div class="record_button"><a id="create_object" class="link_button" href="javascript:;"> ' . Language::get_instance()->translate(1186) . '</a></div>';
		$html[] = "</div>";
		$html[] = "<div id='create_object_holder' class='record_input' style='position: relative;'>";
		$html[] = "</div>";
		$html[] = "<br class='clearfloat'/>";
    }
    $html[] = "<div id='search_holder'>";
    switch(Request::post("type_id"))
    {
    	case LessonPage::TEXT_TYPE : $html[] = $lm->get_renderer()->get_lesson_page_text_form();
    							 	 break;
    	case LessonPage::PUZZLE_TYPE : $html[] = $lm->get_renderer()->get_lesson_page_puzzle_form();
    							 	   break;
    	case LessonPage::GAME_TYPE : $html[] = $lm->get_renderer()->get_lesson_page_game_form();
    							 	 break;
    	case LessonPage::VIDEO_TYPE : $html[] = $lm->get_renderer()->get_lesson_page_video_form();
    							 	 break;
    	case LessonPage::QUESTION_TYPE : $html[] = $lm->get_renderer()->get_lesson_page_question_form();
    							 	 break;
    	case LessonPage::END_GAME_TYPE : $html[] = $lm->get_renderer()->get_lesson_page_end_game_form();
    							 	 break;
    	case LessonPage::SELECTION_TYPE : $html[] = $lm->get_renderer()->get_lesson_page_selection_form();
    							 	 break;
    }
    $html[] = "</div>";
    echo implode("\n", $html);
}

?>