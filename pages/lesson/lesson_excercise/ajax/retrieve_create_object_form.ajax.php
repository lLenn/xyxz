<?php

require_once '../../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/lesson/lib/lesson_manager.class.php';
require_once Path :: get_path() . 'pages/lesson/lesson_excercise/lib/lesson_excercise_component.class.php';

if (Session :: get_user_id())
{
    $usermgr = new UserManager(Session :: get_user_id());
    $user = $usermgr->get_user();
	$lm = new LessonManager($user);
    
	$html = array();
   	$html[] = '<a id="cancel_create_object" class="link_button" href="javascript:;" style="position: absolute; margin: 3px; right: 20px; top: -30px;">' . Language::get_instance()->translate(1024) . '</a>';
	switch(Request::post("type_id"))
    {
    	case LessonExcerciseComponent::PUZZLE_TYPE : 	require_once Path::get_path() . 'pages/puzzle/lib/puzzle_manager.class.php';
    								   	$puzzle_manager = new PuzzleManager($user);
    								   	$page = $puzzle_manager->factory(PuzzleManager::PUZZLE_CREATOR);
    								   	$html[] = $page->get_html(true);
    							 	  	break;
    	case LessonExcerciseComponent::QUESTION_TYPE : require_once Path::get_path() . 'pages/question/lib/question_manager.class.php';
    								   	 $question_manager = new QuestionManager($user);
    								   	 $page = $question_manager->factory(QuestionManager::QUESTION_CREATOR);
    								   	 $html[] = $page->get_html(true);
    								   	 break;
    	case LessonExcerciseComponent::SELECTION_TYPE : require_once Path::get_path() . 'pages/selection/lib/selection_manager.class.php';
    								   	  $selection_manager = new SelectionManager($user);
    								   	  $page = $selection_manager->factory(SelectionManager::SELECTION_CREATOR);
    								   	  $html[] = $page->get_html(true);
    								   	  break;
    }
    echo implode("\n", $html);
}

?>