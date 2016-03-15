<?php

require_once '../../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/lesson/lib/lesson_manager.class.php';
require_once Path :: get_path() . 'pages/puzzle/lib/puzzle_manager.class.php';

if(Session::get_user_id())
{
    $usermgr = new UserManager(Session::get_user_id());
    $user = $usermgr->get_user();

	$gm = new LessonManager($user);
	$pm = new PuzzleManager($user);
	echo '<a id="search_again" class="link_button" href="javascript:;" style="float: right; margin: 3px;">' . Language::get_instance()->translate(118) . '</a>';								
	echo $gm->get_lesson_excercise_manager()->get_renderer()->get_lesson_excercise_set_form($pm, RightManager :: READ_RIGHT, true);
}

?>