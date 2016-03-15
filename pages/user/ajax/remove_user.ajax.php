<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/user/lib/user_manager.class.php';

if (Session :: get_user_id())
{
	$user_id = Request::post("user_id");
	$success = false;
	$user_manager = new UserManager(Session::get_user_id());
	if(!is_null($user_id) && is_numeric($user_id) &&
		RightManager::instance()->get_right_location_object("user", $user_manager->get_user(), $user_id) >= RightManager::UPDATE_RIGHT)
	{
		$user_manager->get_data_manager()->delete_user($user_id);
		if(!Error::get_instance()->get_result())
		{
			echo "Fail!";
		}
	}
	else
	{
		echo "Fail!";
	}
}

?>