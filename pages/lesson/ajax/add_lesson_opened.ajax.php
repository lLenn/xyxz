<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';

if (Session :: get_user_id())
{
	$lesson_id = Request::post("lesson_id");
	if(!is_null($lesson_id) && is_numeric($lesson_id) && $lesson_id > 0)
    {
    	$opened_lessons = unserialize(Session::retrieve("opened_lessons"));
		if(!is_null($opened_lessons) && is_array($opened_lessons))
		{
			if(in_array($lesson_id, $opened_lessons))
				unset($opened_lessons[$lesson_id]);
			else
				$opened_lessons[$lesson_id] = $lesson_id;
		}
		else
		{
			$opened_lessons = array();
			$opened_lessons[$lesson_id] = $lesson_id;
		}
		Session::register("opened_lessons", serialize($opened_lessons));
    }
    
	$map_id = Request::post("map_id");
	if(!is_null($map_id) && is_numeric($map_id) && $map_id > 0)
    {
    	$opened_maps = unserialize(Session::retrieve("opened_maps"));
		if(!is_null($opened_maps) && is_array($opened_maps))
		{
			if(in_array($map_id, $opened_maps))
				unset($opened_maps[$map_id]);
			else
				$opened_maps[$map_id] = $map_id;
		}
		else
		{
			$opened_maps = array();
			$opened_maps[$map_id] = $map_id;
		}
		Session::register("opened_maps", serialize($opened_maps));
    }
}
?>