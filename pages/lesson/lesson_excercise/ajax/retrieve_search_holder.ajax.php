<?php

require_once '../../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/lesson/lib/lesson_manager.class.php';
require_once Path :: get_path() . 'pages/lesson/lesson_excercise/lib/lesson_excercise_component.class.php';

if (Session :: get_user_id())
{
    $usermgr = new UserManager(Session :: get_user_id());
    $user = $usermgr->get_user();
	$html = array();
    switch(Request::post("type_id"))
    {
    	case LessonExcerciseComponent::PUZZLE_TYPE : require_once Path :: get_path() . 'pages/puzzle/lib/puzzle_manager.class.php';
													$sm = new PuzzleManager($user);
													$puzzle = $sm->get_data_manager()->retrieve_puzzle_properties_by_puzzle_id(Request::post("created_object_id"));
													$html[] = '<div style="margin-top: 10px;" >';
													$html[] = '<div style="float: left">';
													$html[] = '<input type="checkbox" name="object_id[]" value="'.$puzzle->get_puzzle_id().'" CHECKED />';
													$url =  Path::get_url_path() . 'pages/puzzle/images/' . $puzzle->get_puzzle_id() . '.gif';
												    if(!file_exists(Path::get_path() . 'pages/puzzle/images/' . $puzzle->get_puzzle_id() . '.gif'))
														$url = Path::get_url_path() . 'pages/puzzle/ajax/retrieve_puzzle_image.ajax.php?puzzle_id=' . $puzzle->get_puzzle_id();
													$html[] = '<img src="' . $url . '" style="vertical-align: top"/>';
													$html[] = '</div>';
													$html[] = '<div style="float: left">';
													$html[] = $sm->get_renderer()->get_description_puzzle($puzzle);
													$html[] = '</div>';
													$html[] = '</div><br class="clearfloat"/><br/>';
    							 	   				break;
    	case LessonExcerciseComponent::QUESTION_TYPE : require_once Path :: get_path() . 'pages/question/lib/question_manager.class.php';
														$qm = new QuestionManager($user);
														$question = $qm->get_data_manager()->retrieve_question(Request::post("created_object_id"));
														$html[] = '<div style="margin-top: 10px;" >';
														$html[] = '<div style="float: left">';
														$html[] = '<input type="checkbox" name="object_id[]" value="'.$question->get_id().'" CHECKED />';
														$html[] = '</div>';
														$html[] = '<div style="float: left">';
														$html[] = $qm->get_renderer()->get_description_question($question);
														$html[] = '</div>';
														$html[] = '</div><br class="clearfloat"/><br/>';
    							 	 					break;
    	case LessonExcerciseComponent::SELECTION_TYPE : require_once Path :: get_path() . 'pages/selection/lib/selection_manager.class.php';
														$sm = new SelectionManager($user);
														$selection = $sm->get_data_manager()->retrieve_selection(Request::post("created_object_id"));
														$html[] = '<div style="margin-top: 10px;" >';
														$html[] = '<div style="float: left">';
														$html[] = '<input type="checkbox" name="object_id[]" value="'.$selection->get_id().'" CHECKED />';
														$html[] = '</div>';
														$html[] = '<div style="float: left">';
														$html[] = $sm->get_renderer()->get_description_selection($selection);
														$html[] = '</div>';
														$html[] = '</div><br class="clearfloat"/><br/>';
    							 	 					break;
    }
    echo implode("\n", $html);
}
?>