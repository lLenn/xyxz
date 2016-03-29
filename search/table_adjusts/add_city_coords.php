<?php
// Set error reporting.
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once('core/lib/path.class.php');
require_once('core/lib/global.inc.php');

$dm = new DataManager(null);
$sql_string = "select id, city_name, city_latitude from `city`";
$result = $dm->retrieve_data($sql_string);
foreach($result as $r)
{
	if($r->city_latitude == 0)
	{
		$url = "http://maps.google.com/maps/api/geocode/json?"."address=" . urlencode(strtolower(Utilities::html_special_characters($r->city_name)) . ", belgie");
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_PROXYPORT, 3128);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);
		$response_a = json_decode($response);
		if(count($response_a->results) >= 1)
		{
			$lat = $response_a->results[0]->geometry->location->lat;
			$long = $response_a->results[0]->geometry->location->lng;
		
			$sql_string = "update `city` set `city_longitude` = " . $long . ", `city_latitude` = " . $lat . " WHERE `id` = " . $r->id;
			$dm->execute_sql($sql_string, 'UPDATE');
		}
		else
			echo $r->city_name . "</br>";
	}
}

dump(Error::get_instance());
?>