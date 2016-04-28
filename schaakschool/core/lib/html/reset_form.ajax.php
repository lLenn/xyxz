<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';

if (Session :: get_user_id() && Request :: post("table_id"))
{
    $usermgr = new UserManager(Session :: get_user_id());
    $user = $usermgr->get_user();
	$classes_to_load = unserialize(Session::retrieve("table_model_classes_to_load_" . Request::post("table_id")));
	foreach($classes_to_load as $class)
	{
		require_once $class;
	}
	$languages_to_load = unserialize(Session::retrieve("table_model_languages_to_load_" . Request::post("table_id")));
	foreach($languages_to_load as $section)
	{
		Language::get_instance()->add_section_to_translations($section);
	}
	$table_model = unserialize(Session::retrieve("table_model_" . Request::post("table_id")));
	if(!is_null($table_model))
	{
		echo $table_model->render_table(true);
	}
}

?>