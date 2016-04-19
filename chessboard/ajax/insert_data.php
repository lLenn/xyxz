<?php
require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/statistics/lib/statistics_manager.class.php';

if(Request::post("logout"))
{
	Session::register_user_id(105);
}
	
if (Session :: get_user_id())
{
	$user = UserDataManager::instance(null)->retrieve_user(Session :: get_user_id());
	$action = Request::post("action");
	$statistics_manager = new StatisticsManager($user);
	$json = "";
	
	if(Request::post("logout"))
	{
		Session :: unregister_user_id();
	}
	
	$valid = true;
	switch($action)
	{
		case "register_new_ratings_set":
			list($set_id) = HTML5Helper::getObject("excercise", Request::post("setId"));
			$attempt = Request::post("setAttempt");
		case "register_new_ratings":
			list($puzzle_id) = HTML5Helper::getObject("puzzle", Request::post("serial"));
			$score = HTML5Helper::getSolutionObject("puzzle", $puzzle_id, Request::post("serialSolution"));
			$total_moves = Request::post("total_moves");
			$time_left = Request::post("time_left");
			if(is_null($puzzle_id) || $score === false || !is_numeric($time_left) || $time_left < 0 || !is_numeric($total_moves) || $total_moves < 0)
				$valid = false;
			break;
		case "register_new_guest_rating":
			list($puzzle_id) = HTML5Helper::getObject("puzzle", Request::post("serial"));
			$score = HTML5Helper::getSolutionObject("puzzle", $puzzle_id, Request::post("serialSolution"));
			$rating = Request::post("rating");
			$email = addslashes(htmlspecialchars(Request::post("email")));
			if(is_null($puzzle_id) || $score === false || !is_numeric($rating))
				$valid = false;
			break;
		case "register_selection_set_statistics":
			list($set_id) = HTML5Helper::getObject("excercise", Request::post("setId"));
			$attempt = Request::post("setAttempt");
		case "register_selection_statistics":
			list($selection_id) = HTML5Helper::getObject("selection", Request::post("serial"));
			$score = HTML5Helper::getSolutionObject("selection", $selection_id, Request::post("serialSolution"));
			if(is_null($selection_id) || $score === false)
				$valid = false;
			break;
		case "register_question_set_statistics":
			list($set_id) = HTML5Helper::getObject("excercise", Request::post("setId"));
			$attempt = Request::post("setAttempt");
		case "register_question_statistics":
			list($question_id) = HTML5Helper::getObject("multipleAnswers", Request::post("serial"));
			$score = HTML5Helper::getSolutionObject("multipleAnswers", $question_id, Request::post("serialSolution"));
			if(is_null($question_id) || $score === false)
				$valid = false;
			break;
		default: $valid = false;
	}
	
	if($valid)
	{
		switch($action)
		{
			case "register_new_ratings": 
				require_once Path :: get_path() . 'pages/puzzle/lib/puzzle_manager.class.php';
				$m = new PuzzleManager($user);
				Request::clear();
				Request::set_get("action", "flash_register_new_ratings");
				Request::set_post("puzzle_id", $puzzle_id);
				Request::set_post("user_id", $user->get_id());
				Request::set_post("score", $score);
				Request::set_post("time_left", $time_left);
				Request::set_post("total_moves", $total_moves);
				$statistics_manager->register_action();
				$json = '{"user": {"rating": ' . $m->get_data_manager()->register_new_ratings($puzzle_id, $score, $time_left, $total_moves) . '}}';
				break;
			case "register_new_ratings_set":
				require_once Path :: get_path() . 'pages/puzzle/lib/puzzle_manager.class.php';
				$m = new PuzzleManager($user);
				Request::clear();
				Request::set_get("action", "flash_register_new_ratings_set");
				Request::set_post("puzzle_id", $puzzle_id);
				Request::set_post("user_id", $user->get_id());
				Request::set_post("score", $score);
				Request::set_post("time_left", $time_left);
				Request::set_post("total_moves", $total_moves);
				Request::set_post("set_id", $set_id);
				Request::set_post("attempt", $attempt);
				$statistics_manager->register_action();
				$json = '{"user": {"rating": ' . $m->get_data_manager()->register_new_ratings($puzzle_id, $score, $time_left, $total_moves, $set_id, $attempt) . '}}';
				break;
			case "register_new_guest_rating":
				require_once Path :: get_path() . 'pages/puzzle/lib/puzzle_manager.class.php';
				$m = new PuzzleManager($user);
				$json = '{"user": {"rating": ' . $m->get_data_manager()->retrieve_new_rating_guest($puzzle_id, $score, $rating, $email) . '}}';
				break;
			case "register_selection_statistics":
				require_once Path :: get_path() . 'pages/selection/lib/selection_manager.class.php';
				$m = new SelectionManager($user);
				Request::clear();
				Request::set_get("action", "flash_register_selection_statistics");
				Request::set_post("selection_id", $selection_id);
				Request::set_post("user_id", $user->get_id());
				Request::set_post("score", $score);
				$statistics_manager->register_action();
				$m->get_data_manager()->register_selection_statistics($selection_id, $score);
				break;
			case "register_selection_set_statistics":
				require_once Path :: get_path() . 'pages/selection/lib/selection_manager.class.php';
				$m = new SelectionManager($user);
				Request::clear();
				Request::set_get("action", "flash_register_selection_set_statistics");
				Request::set_post("selection_id", $selection_id);
				Request::set_post("user_id", $user->get_id());
				Request::set_post("score", $score);
				Request::set_post("set_id", $set_id);
				Request::set_post("attempt", $attempt);
				$statistics_manager->register_action();
				$m->get_data_manager()->register_selection_statistics($selection_id, $score, $set_id, $attempt);
				break;

			case "register_question_statistics":
				require_once Path :: get_path() . 'pages/question/lib/question_manager.class.php';
				$m = new QuestionManager($user);
				Request::clear();
				Request::set_get("action", "flash_register_question_statistics");
				Request::set_post("question_id", $question_id);
				Request::set_post("user_id", $user->get_id());
				Request::set_post("score", $score);
				$statistics_manager->register_action();
				$m->get_data_manager()->register_question_statistics($question_id, $score);
				break;
			case "register_question_set_statistics":
				require_once Path :: get_path() . 'pages/question/lib/question_manager.class.php';
				$m = new QuestionManager($user);
				Request::set_get("action", "flash_register_question_set_statistics");
				Request::set_post("question_id", $question_id);
				Request::set_post("user_id", $user->get_id());
				Request::set_post("score", $score);
				Request::set_post("set_id", $set_id);
				Request::set_post("attempt", $attempt);
				$statistics_manager->register_action();
				$m->get_data_manager()->register_question_statistics($question_id, $score, $set_id, $attempt);
				break;
		}
		echo HTML5Helper::mergeJSON('{"success": 1}', $json);
	}
	else
		echo '{"error": "Unknown error!"}';
}
else 
	echo '{"error": "Unknown error!"}';