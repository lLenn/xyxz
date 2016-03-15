<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/lesson/lib/lesson_manager.class.php';
require_once Path :: get_path() . 'pages/lesson/lesson_continuation_availability.page.php';

if (Session :: get_user_id())
{
    $usermgr = new UserManager(Session :: get_user_id());
    $user = $usermgr->get_user();

    if($user->is_admin())
    {
	    $start = Request::post("start");
	    $form = $start==0;
	    $limit = 10;
	    	
		$html = array();
		$limit_qry = "";
		if($limit != 0)
			$limit_qry = $start . ", " . ($limit + 1);
		else if($start != 0)
			$limit_qry = $start;
		
		Language::get_instance()->add_section_to_translations(Language::THEME);
			
		$manager = new LessonManager($user);
	    switch(Request::post("type_id"))
	    {
	    	case LessonContinuationAvailability::ADD_LESSON :
								$objects = $manager->get_data_manager()->retrieve_lessons_with_search_form(RightManager::NO_RIGHT, $limit_qry, false, false, true);
								$no_found = Language::get_instance()->translate(140);
								$more = Language::get_instance()->translate(816);
								$id = "get_id";
								break;
	    	case LessonContinuationAvailability::ADD_LESSON_EXCERCISE :
								$manager = $manager->get_lesson_excercise_manager();
								$objects = $manager->get_data_manager()->retrieve_lesson_excercises_with_search_form(RightManager::NO_RIGHT, $limit_qry, false, false, true);
								$no_found = Language::get_instance()->translate(829);
								$more = Language::get_instance()->translate(824);
								$id = "get_id";
								break;
	    }
		
		$html[] = '<a id="search_again" class="link_button" href="javascript:;" style="float: right; margin: 10px 20px 3px 3px;">' . Language::get_instance()->translate(118) . '</a>'; 		 
	    $obj_count = count($objects);
		if($obj_count)
		{
			if($form && $start == 0)
			{
				$html[] = '<form action="" method="post" id="lesson_continuation_available_form">';
			}
			if($start == 0)
			{
				$html[] = '<div class="record">';
				$html[] = '<div id="more_record">';
			}
			$count = 0;
			foreach($objects as $object)
			{
				$html[] = '<div style="margin-top: 10px;" >';
				$html[] = '<div style="float: left">';
				$html[] = '<input type="checkbox" name="object_id[]" value="'.$object->$id().'"/>';
				$html[] = '</div>';
				$html[] = '<div style="float: left;">';
				$html[] = $manager->get_renderer()->get_shop_detail($object);
				$html[] = '</div>';
				$html[] = '<br class="clearfloat" />';
				$html[] = '</div>';
				$count++;
				if($count == $limit && $limit != 0)
					break;
			}
			if($count == $limit && $limit != 0 && $count + 1 == $obj_count)
				$html[] = '<div id="more_block" class="record_button" style="width: 100%; text-align: center;"><a id="more_results" class="text_link" href="javascript:;">' . $more . '</a></div><br class="clearfloat"/>';
			if($form && $start == 0)
			{
				$html[] = '</div>';
				$html[] = '<div class="record_button"><a id="submit_form" class="link_button" href="javascript:;">' . Language::get_instance()->translate(49) . '</a></div><br class="clearfloat"/>';
			}
			if($start == 0)						
				$html[] = '</div>';
			if($form && $start == 0)
				$html[] = '</form>';
			if($start == 0)
				$html[] = '<br class="clearfloat" />';
		}
		else
			$html[] = '<p>' . $no_found . '</p>';
		echo implode("\n", $html);
	}
}
?>