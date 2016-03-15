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
    		   						   echo '<a id="search_puzzle_again" class="link_button" href="javascript:;" style="position: absolute; margin: 3px; right: 20px;">' . Language::get_instance()->translate(118) . '</a>'; 		 
						  	    	   echo $gm->get_renderer()->get_lesson_page_puzzle_form_results(true);
    							 	   break;
    	case LessonPage::GAME_TYPE : 
    		    		   			 echo '<a id="search_game_again" class="link_button" href="javascript:;" style="position: absolute; margin: 3px; right: 20px;">' . Language::get_instance()->translate(118) . '</a>'; 		 
    								 echo $gm->get_renderer()->get_lesson_page_game_form_results(true);
    							 	 break;
    	case LessonPage::VIDEO_TYPE : 
    		    		   			 echo '<a id="search_video_again" class="link_button" href="javascript:;" style="position: absolute; margin: 3px; right: 20px;">' . Language::get_instance()->translate(118) . '</a>'; 		 
    								 echo $gm->get_renderer()->get_lesson_page_video_form_results(true);
    							 	 break;
    	case LessonPage::QUESTION_TYPE : 
    		    		   			 echo '<a id="search_question_again" class="link_button" href="javascript:;" style="position: absolute; margin: 3px; right: 20px;">' . Language::get_instance()->translate(118) . '</a>'; 		 
    								 echo $gm->get_renderer()->get_lesson_page_question_form_results(true);
    							 	 break;
    	case LessonPage::END_GAME_TYPE : 
    		    		   			 echo '<a id="search_game_again" class="link_button" href="javascript:;" style="position: absolute; margin: 3px; right: 20px;">' . Language::get_instance()->translate(118) . '</a>'; 		 
    								 echo $gm->get_renderer()->get_lesson_page_end_game_form_results(true);
    							 	 break;
    	case LessonPage::SELECTION_TYPE : 
    		    		   			 echo '<a id="search_selection_again" class="link_button" href="javascript:;" style="position: absolute; margin: 3px; right: 20px;">' . Language::get_instance()->translate(118) . '</a>'; 		 
    								 echo $gm->get_renderer()->get_lesson_page_selection_form_results(true);
    							 	 break;
    }
}

?>