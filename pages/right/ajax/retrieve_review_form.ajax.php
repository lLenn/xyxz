<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/right/lib/right_manager.class.php';

if(Session::get_user_id())
{
	$location_id = Request::post("location_id");
	$object_id = Request::post("object_id");
	$update = false;
	
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
			$print_form = true;
	   		$right_manager = new RightManager($user);
			$existing_object_review = $right_manager->get_data_manager()->retrieve_location_object_review($location_id, $object_id, $user->get_id());
			if(Request::post("object_rating") != null)
			{
				$object_review = $right_manager->get_data_manager()->retrieve_location_object_review_from_post();
				if(!Error::get_instance()->get_result())
				{
					$html[] = Error::get_instance()->print_error(true);
				}
				else
				{
					$object_rating = RightManager::instance()->retrieve_location_object_meta_data($location_id, $object_id, "object_rating");
					if($object_rating != null)
						$object_rating = explode("/", $object_rating->get_value());
					else 
						$object_rating = array(0,0);
					if($existing_object_review==null) 
					{
						$object_review->set_added(time());
						$right_manager->get_data_manager()->insert_location_object_review($object_review);
						if($object_review->get_rating() != null && is_numeric($object_review->get_rating()) && $object_review->get_rating() >= 0 && $object_review->get_rating() <= 10)
						{
							$object_rating[0] += $object_review->get_rating();
							$object_rating[1]++;
						}
					}
					else
					{
						$object_review->set_added($existing_object_review->get_added());
						$object_review->set_last_edited(time());
						$right_manager->get_data_manager()->update_location_object_review($object_review);
						if($existing_object_review->get_rating() != null && is_numeric($existing_object_review->get_rating()) && $existing_object_review->get_rating() >= 0 && $existing_object_review->get_rating() <= 10)
						{
							if($object_review->get_rating() != null && is_numeric($object_review->get_rating()) && $object_review->get_rating() >= 0 && $object_review->get_rating() <= 10)
							{
								$object_rating[0] += $object_review->get_rating() - $existing_object_review->get_rating();
							}
							else
							{
								$object_rating[0] -= $existing_object_review->get_rating();
								$object_rating[1]--;
							}
						}
						elseif($object_review->get_rating() != null && is_numeric($object_review->get_rating()) && $object_review->get_rating() >= 0 && $object_review->get_rating() <= 10)
						{
							$object_rating[0] += $object_review->get_rating();
							$object_rating[1]++;
						}
					}
					if(Error::get_instance()->get_result())
					{
						$html[] = "<p class='good' style='width: 400px; margin: 0;'>".Language::get_instance()->translate(1274)."</p>";
						RightManager::instance()->add_location_object_meta_data($location_id, $object_id, "object_rating", implode("/", $object_rating));
					}
					else $html[] = "<p class='error'>".Error::get_instance()->get_message(1275)."</p>";
				}
				$update = true;
				$print_form = Error::get_instance()->get_result()?false:true;
			}
			else
			{
				if($existing_object_review==null)
				{
					$object_review = new LocationObjectReview();
					$object_review->set_location_id($location_id);
					$object_review->set_object_id($object_id);
				}
				else
				{
					$object_review = $existing_object_review;
					$update = true;
				}
			}
		
			if($print_form)
			{
				echo 0;
				$html[] = '<div style="margin-top: 30px;" >';
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
				$html[] = $right_manager->get_renderer()->get_object_review_form($object_review, $update);
			}
			$html[] = '<script type="text/javascript">';
			$html[] = 'jQuery().load_messages();';
			$html[] = '</script>';
			echo implode("\n", $html);
		}
	}
}

?>