<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/user/lib/user_manager.class.php';

$um = new UserManager(null);
if(!is_null(Request::post("city_code")) && is_numeric(Request::post("city_code")))
{
	echo $um->get_renderer()->get_forms_renderer()->get_organisations_form_by_city_code(Request::post("city_code"), Request::post("organisation_type"));
}

?>