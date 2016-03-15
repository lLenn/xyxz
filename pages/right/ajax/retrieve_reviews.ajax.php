<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/right/lib/right_manager.class.php';

if(Session::get_user_id())
{
	$location_id = Request::post("location_id");
	$object_id = Request::post("object_id");
	
	//CHECK IF LOCATION EXISTS AND IF OBJECT IN THAT LOCATION EXISTS (ADD OBJECT EXISTS IN RIGHT SYSTEM)
	if($location_id != null && is_numeric($location_id) && $location_id > 0 &&
		$object_id != null && is_numeric($object_id) && $object_id > 0)
	{
		$usermgr = new UserManager(Session::get_user_id());
	    $user = $usermgr->get_user();
    	
	    switch($location_id)
		{
			case RightManager::PUZZLE_LOCATION_ID: 	
								require_once Path::get_path() . "/pages/puzzle/lib/puzzle_manager.class.php";
								$manager = new PuzzleManager($user);
								$object = $manager->get_data_manager()->retrieve_puzzle_properties_by_puzzle_id($object_id);
								break;
			case RightManager::GAME_LOCATION_ID:
							 	require_once Path::get_path() . "/pages/game/lib/game_manager.class.php";
								$manager = new GameManager($user);
								$object = $manager->get_data_manager()->retrieve_game_properties_by_game_id($object_id);
								break;
			case RightManager::ENDGAME_LOCATION_ID:
							 	require_once Path::get_path() . "/pages/game/lib/game_manager.class.php";
							 	require_once Path::get_path() . "/pages/game/end_game/lib/end_game_manager.class.php";
								$manager = new EndGameManager($user, new GameManager($user));
								$object = $manager->get_data_manager()->retrieve_end_game_properties($object_id);
								break;
			case RightManager::QUESTION_LOCATION_ID: 	
								require_once Path::get_path() . "/pages/question/lib/question_manager.class.php";
								$manager = new QuestionManager($user);
								$object = $manager->get_data_manager()->retrieve_question($object_id);
								break;
			case RightManager::SELECTION_LOCATION_ID:
							 	require_once Path::get_path() . "/pages/selection/lib/selection_manager.class.php";
								$manager = new SelectionManager($user);
								$object = $manager->get_data_manager()->retrieve_selection($object_id);
								break;
			case RightManager::VIDEO_LOCATION_ID:
							 	require_once Path::get_path() . "/pages/video/lib/video_manager.class.php";
								$manager = new VideoManager($user);
								$object = $manager->get_data_manager()->retrieve_video_properties($object_id);
								break;
			case RightManager::LESSON_LOCATION_ID:
							 	require_once Path::get_path() . "/pages/lesson/lib/lesson_manager.class.php";
								$manager = new LessonManager($user);
								$object = $manager->get_data_manager()->retrieve_lesson($object_id);
								break;
			case RightManager::LESSON_EXCERCISE_LOCATION_ID:
								require_once Path::get_path() . "/pages/lesson/lib/lesson_manager.class.php";
								$manager = new LessonManager($user);
								$manager = $manager->get_lesson_excercise_manager();
								$object = $manager->get_data_manager()->retrieve_lesson_excercise($object_id);
								break;
		}
		
		if($object!=null)
		{
			$html = array();
	   		$right_manager = new RightManager($user);
			$html[] = '<div style="margin-top: 30px;">';
			$html[] = '<div style="float: left">';
			if($location_id == RightManager::PUZZLE_LOCATION_ID)
				$html[] = $manager->get_renderer()->get_puzzle_image_html($object);
			$html[] = '</div>';
			$html[] = '<div style="float: left; min-width: 300px;">';
			$creator = $usermgr->get_data_manager()->retrieve_user(RightManager::instance()->get_data_manager()->retrieve_location_object_creator($location_id, $object_id));
			$html[] = '<div class="record_name">' . Language::get_instance()->translate(108) . ' :</div><div class="record_input">'.(!is_null($creator)?$creator->get_name():Language::get_instance()->translate(1098)).'</div><br class="clearfloat"/>';
			$html[] = $manager->get_renderer()->get_shop_detail($object);
			$html[] = '</div>';
			$html[] = '<br class="clearfloat" />';
			$html[] = $right_manager->get_renderer()->get_object_reviews($location_id, $object_id);
			echo implode("\n", $html);
		}
	}
}

?>