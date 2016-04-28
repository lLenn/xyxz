<?php
	session_start();
	require_once "classes/data/db_connect.php";
	$db = mysql_connect($host,$user,$password); if(mysql_errno()>0) trigger_error("+++ ".mysql_errno().": ".mysql_error()." +++");
	mysql_select_db($database,$db); if(mysql_errno()>0) trigger_error("+++ ".mysql_errno().": ".mysql_error()." +++");
	date_default_timezone_set("Europe/Brussels");
	if(!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"]<=0 || SITE_ONLINE == 0)
	{
		
		echo   "<script type='text/javascript'>
				//<![CDATA[
					parent.location = 'index.php';
				//]]>
				</script>";
		exit;
	}
	
	$query = "SET NAMES 'utf8'";
	mysql_query($query); if(mysql_errno()>0) trigger_error("+++ ".mysql_errno().": ".mysql_error()." +++");
	
	require_once 'classes/supplier/embedded/curl.class.php';
	
	$curl = new Curl(rawurldecode($_GET["root"]));
	if(isset($_GET["page"]))
	{
		$curl->set_page(rawurldecode($_GET["page"]));
	}
	
	if($curl->get_root(rawurldecode($_GET["root"])) == "http://ecat.arrowheadep.com/")
	{
		$data = file_get_contents("classes/supplier/embedded/pass_arrowhead.txt");
		$curl->login($data, "http://ecat.arrowheadep.com/login.aspx");
	}
	elseif($curl->get_root(rawurldecode($_GET["root"])) == "https://www.rotarycorp.com/" && 
		   $curl->get_page() == "https://www.rotarycorp.com/CGI-BIN/LANSAWEB?PROCFUN+WEBAPPLC+HOMPAGE+CEP")
	{
		$curl->login(null, "https://www.rotarycorp.com/CGI-BIN/LANSAWEB?PROCFUN+WEBAPPLC+HOMPAGE+CEP");
	}
	$curl->get_parent("iframe_doc");
	//$curl = new Curl("https://www.rotarycorp.com/");
	//$curl->set_page("https://www.rotarycorp.com/CGI-BIN/LANSAWEB?WEBEVENT+LB57B866B54836201F6B100S+CEP+ENG");
	echo $curl->get_data();

 ?>