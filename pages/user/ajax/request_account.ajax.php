<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/user/lib/user_manager.class.php';

$um = new UserManager(null);
$request = $um->get_data_manager()->retrieve_user_request_from_post();
if(Error::get_instance()->get_result())
{
	$result = $um->get_data_manager()->insert_user_request($request);
	if($result)
	{
		echo  "<p>";
		echo Language::get_instance()->translate(759) . "<br/>";
		echo Language::get_instance()->translate(760) . "<br/>";
		echo Language::get_instance()->translate(761) . "<br/>";
		echo  "</p>";
	}
	else
	{
		echo Language::get_instance()->translate(93);
		echo $um->get_renderer()->get_forms_renderer()->get_request_form();	
	}
}
else
{
	Error::get_instance()->print_error();
	echo $um->get_renderer()->get_forms_renderer()->get_request_form();
}

?>