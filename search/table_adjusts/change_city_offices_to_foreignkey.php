<?php
// Set error reporting.
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once('core/lib/path.class.php');
require_once('core/lib/global.inc.php');

$dm = new DataManager(null);
$sql_string = "select id, city from `offices`";
$offices = $dm->retrieve_data($sql_string);

$sql_string = "select id, city_name from `city`";
$cities = $dm->retrieve_data($sql_string);

foreach($offices as $office)
{
	$city_id = false;
	foreach($cities as $city)
	{
		if(strtolower($city->city_name) == strtolower($office->city))
		{
			$city_id = $city->id;
			break;
		}		
	}

	if($city_id !== false)
	{
		$sql_string = "update `offices` set `city` = " . $city_id . " WHERE `id` = " . $office->id;
		$dm->execute_sql($sql_string, 'UPDATE');
	}
	else
		echo $office->id . " " . $office->city . "</br>";
}

dump(Error::get_instance());
?>