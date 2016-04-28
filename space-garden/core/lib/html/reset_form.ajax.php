<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';

if (Session :: get_user_id() && Request :: post("table_id"))
{    
	$user = ClientDataManager::instance()->retrieve_client_user_account(Session :: get_user_id());
	$classes_to_load = unserialize(Session::retrieve("table_model_classes_to_load_" . Request::post("table_id")));
	foreach($classes_to_load as $class)
	{
		require_once $class;
	}
	$table_model = unserialize(Session::retrieve("table_model_" . Request::post("table_id")));
	if(!is_null($table_model))
	{
		echo $table_model->render_table(true);
	}
}

?>