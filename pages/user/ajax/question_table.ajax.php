<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/user/lib/user_manager.class.php';
require_once Path :: get_path() . 'pages/question/lib/question_manager.class.php';

if (Session :: get_user_id() && !is_null(Request::post("user_id")))
{
	$um = new UserManager(Session :: get_user_id());
	if($um->get_user()->is_child(Request::post("user_id")))
	{
		$qm = new QuestionManager($um->get_data_manager()->retrieve_user(Request::post("user_id")));
		echo '<h4 class="small_title"><img id="remove_question_ajax" style="left: -22px; top: 1px; border: 0; position: relative;" src="' . Path::get_url_path() . '/layout/images/buttons/remove.png"/><span style="position: relative; left: -14px;">' . Language::get_instance()->translate(772) . '</span></h4>' . "\n";
		echo $qm->get_renderer()->get_table(RightManager::READ_RIGHT, false, false);
	}
}

?>