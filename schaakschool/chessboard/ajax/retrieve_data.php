<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';

if(Request::post("logout"))
{
	Session::register_user_id(105);
}

if (Session :: get_user_id())
{
	$json = "";
	$user = UserDataManager::instance(null)->retrieve_user(Session :: get_user_id());
	$location = Request::post("location");
	$valid_location = true;
	
	if(Request::post("local"))
		$object_serial = HTML5Helper::storeObject($location, 332);

	if(Request::post("userRequest"))
	{
		$json = HTML5Helper::convertUserToJSON($user);
	}

	switch($location)
	{
		case "puzzle":
			require_once Path :: get_path() . 'pages/puzzle/lib/puzzle_manager.class.php';
			$manager = new PuzzleManager($user);
			break;
		case "end_game":
		case "game":
			require_once Path :: get_path() . 'pages/game/lib/game_manager.class.php';
			$manager = new GameManager($user);
			break;
		case "selection":
			require_once Path :: get_path() . 'pages/selection/lib/selection_manager.class.php';
			$manager = new SelectionManager($user);
			break;
		case "multipleAnswers":
			require_once Path :: get_path() . 'pages/question/lib/question_manager.class.php';
			$manager = new QuestionManager($user);
			break;
		case "excercise":
			require_once Path :: get_path() . 'pages/lesson/lesson_excercise/lib/lesson_excercise_manager.class.php';
			$manager = new LessonExcerciseManager($user, null);
			break;
		default: $valid_location = false;
	}
	
	if($valid_location)
	{
		if(Request::post("random"))
		{
				switch($location)
				{
					case "puzzle": 
							require_once Path :: get_path() . 'pages/statistics/lib/statistics_manager.class.php';
							$data = $manager->get_data_manager()->retrieve_random_puzzle(Request::post("rating"));
							$id = $data["object"]->id;
							break;
					case "selection": 
							$data = $manager->get_data_manager()->retrieve_random_selection();
							$id = $data->get_id();
							break;
					case "multipleAnswers": 
							$data = $manager->get_data_manager()->retrieve_random_question();
							$id = $data["object"]->id;
							break;
				}
			
			$object_serial = HTML5Helper::storeObject($location, $id);
			switch($location)
			{
				case "puzzle":
				case "multipleAnswers":
					$json = HTML5Helper::mergeJSON($json, HTML5Helper::convertToJSON($data, $object_serial, $location));
					break;
				case "selection":
					$json = HTML5Helper::mergeJSON($json, HTML5Helper::convertSelectionToJSON($data, $object_serial, $location));
					break;
			}
		}
		elseif(Request::post("object_serial"))
		{
			if($location == "excercise" && Request::post("local"))
				list($object_id) = HTML5Helper::getObject($location, $object_serial[0]);
			else
				list($object_id, $object_serial) = HTML5Helper::getObject($location, Request::post("object_serial"));
			if($object_id && $location != "selection" && $location != "excercise")
			{
				switch($location)
				{
					case "puzzle": $data = $manager->get_data_manager()->retrieve_all_data_from_puzzle($object_id); break;
					case "end_game": $data = $manager->get_end_game_manager()->get_data_manager()->retrieve_all_data_from_end_game($object_id); break;
					case "game": $data = $manager->get_data_manager()->retrieve_all_data_from_game($object_id); break;
					case "multipleAnswers": $data = $manager->get_data_manager()->retrieve_all_data_from_question($object_id); break;
				}
				if($data["object"]!=null)
				{
					$json = HTML5Helper::mergeJSON($json, HTML5Helper::convertToJSON($data, $object_serial, $location));
				}
				else
					$json = '{"error": 250}';
			}
			elseif($location == "selection")
			{
				$data = $manager->get_data_manager()->retrieve_selection($object_id);
				if($data!=null)
				{
					$json = HTML5Helper::mergeJSON($json, HTML5Helper::convertSelectionToJSON($data, $object_serial));
				}
				else
					$json = '{"error": 250}';
				
			}
			elseif($location == "excercise" && Request::post("prev_mistakes"))
			{
				$data = $manager->get_data_manager()->retrieve_excercise_components_previous_mistakes($object_id);
				if($data!=null)
				{
					$json = HTML5Helper::mergeJSON($json, HTML5Helper::convertExcerciseMistakesToJSON($data, $object_serial));
				}
				else
					$json = '{"error": 250}';
			}
			elseif($location == "excercise")
			{
				$data = $manager->get_data_manager()->retrieve_all_data_excercise($object_id, Request::post("select_random"));
				if($data!=null)
				{
					$json = HTML5Helper::mergeJSON($json, HTML5Helper::convertExcerciseToJSON($data, $object_serial));
				}
				else
					$json = '{"error": 250}';
			}
			else
				$json = '{"error": "Error 134: Unknown error!"}'; 
		}
		else
			$json = '{"error": "Error 137: Unknown error!"}'; 
	}
	else
		$json = '{"error": "Error 140: Unknown error!"}';
}
else 
	$json = '{"error": "Error 143: Unknown error!"}';

echo $json;

if(Request::post("logout"))
{
	Session :: unregister_user_id();
}