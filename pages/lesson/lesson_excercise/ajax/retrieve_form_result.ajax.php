<?php

require_once '../../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/lesson/lib/lesson_manager.class.php';
require_once Path :: get_path() . 'pages/lesson/lesson_excercise/lib/lesson_excercise_component.class.php';

if (Session :: get_user_id())
{
    $usermgr = new UserManager(Session :: get_user_id());
    $user = $usermgr->get_user();

	$gm = new LessonManager($user);
    switch(Request::post("type_id"))
    {
    	case LessonExcerciseComponent::PUZZLE_TYPE : 
    		   						   echo '<a id="search_puzzle_again" class="link_button" href="javascript:;" style="position: absolute; margin: 3px; right: 20px;">' . Language::get_instance()->translate(118) . '</a>'; 		 
						  	    	   echo $gm->get_renderer()->get_lesson_page_puzzle_form_results(true, false);
    							 	   break;
    	case LessonExcerciseComponent::QUESTION_TYPE : 
    		    		   			 echo '<a id="search_question_again" class="link_button" href="javascript:;" style="position: absolute; margin: 3px; right: 20px;">' . Language::get_instance()->translate(118) . '</a>'; 		 
    								 echo $gm->get_renderer()->get_lesson_page_question_form_results(true, false);
    							 	 break;
    	case LessonExcerciseComponent::SELECTION_TYPE : 
    		    		   			 echo '<a id="search_selection_again" class="link_button" href="javascript:;" style="position: absolute; margin: 3px; right: 20px;">' . Language::get_instance()->translate(118) . '</a>'; 		 
    								 echo $gm->get_renderer()->get_lesson_page_selection_form_results(true, false);
    							 	 break;
    }
}

?>