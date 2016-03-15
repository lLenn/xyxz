<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/user/lib/user_manager.class.php';

if (Session :: get_user_id())
{
	$old_parent = Request::post("old_parent");
	$new_parent = Request::post("new_parent");
	$user_id = Request::post("user_id");
	$um = new UserManager(Session :: get_user_id());
	if(!is_null($old_parent) && is_numeric($old_parent) &&
	   !is_null($new_parent) && is_numeric($new_parent) &&
	   !is_null($user_id) && is_numeric($user_id) &&
	   RightManager::instance()->get_right_location_object("user", $um->get_user(), $user_id) >= RightManager::UPDATE_RIGHT)
	{			
		RightManager::instance()->delete_location_object_user_rights("User", $user_id);
		$parent_user = $um->get_data_manager()->retrieve_user($new_parent);
		while(!is_null($parent_user) && !$parent_user->is_admin())
		{
			RightManager::instance()->add_location_object_user_right("User", $parent_user->get_id(), $user_id, RightManager::UPDATE_RIGHT);
			$parent_user = $um->get_data_manager()->retrieve_user($parent_user->get_parent_id());
		}
		RightManager::instance()->add_location_object_user_right("User", $user_id, $user_id, RightManager::UPDATE_RIGHT);
		$properties = new CustomProperties();
		$properties->add_property("parent_id", $new_parent);
		RightDataManager::instance(null)->update('user', $properties, "id = " . $user_id);
		if(!Error::get_instance()->get_result())
		{
			echo $um->get_renderer()->render_user_manager();
		}
	}
	else
	{
		echo $um->get_renderer()->render_user_manager();
	}
}

?>