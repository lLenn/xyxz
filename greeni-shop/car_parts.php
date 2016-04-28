<?php
	// Open de database, start sessie, login check, ...
	session_start();
	require_once "classes/data/db_connect.php";
	$db = mysql_connect($host,$user,$password); if(mysql_errno()>0) trigger_error("+++ ".mysql_errno().": ".mysql_error()." +++");
	mysql_select_db($database,$db); if(mysql_errno()>0) trigger_error("+++ ".mysql_errno().": ".mysql_error()." +++");
	date_default_timezone_set("Europe/Brussels");
	if (!isset($_SESSION["logged_in"]) or $_SESSION["logged_in"]<=0 or SITE_ONLINE == 0) header("location: index.php");
	LanguageManager::get_instance()->add_section_to_translations(basename($_SERVER["PHP_SELF"]));
	LanguageManager::get_instance()->add_section_to_translations('legend');
	LanguageManager::get_instance()->add_translations_as_constants();
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title><?=strtoupper(ID_HBASE)?></title>
<link rel="stylesheet" type="text/css" href="greeni.css"/>
<link rel="stylesheet" type="text/css" href="plugins/menu/menu.css"/>
<script src="plugins/jquery-1.4.4.min.js"></script>
<script src="plugins/menu/menu.js"></script>
<script src="functions.js"></script>
</head>

<body oncontextmenu="return false;">
<!-- MENU -->
<? 
	require("menu1.php");
	require("classes/supplier/embedded/conditions.php");
	echo display_conditions();
?>

<!-- FLOATING NAVIGATION BUTTONS -->
<div id="js_float" style="position:absolute; right:20px; top:68px">
	<img src="images/up.gif" alt="to top" onclick="location.href='#'"><br>
	<img src="images/down.gif" alt="to bottom" onclick="location.href='#new_bottom'">
</div>
<script type="text/javascript">KeepInView ("js_float");</script>

<div style="width:800px; position: absolute; left: 50%; margin-left: -400px; border: 0px solid red; margin-top:36px">
<?php 
	if(SITE_ONLINE==1)
		echo "<div style='color:red'>". ID_SITE_ALMOST_OFFLINE."</div>\n";
?>
	<div style="margin-top:54px">
				<p STYLE="text-align: center; font-size: 20px; font-weight: bold;"> <?=strtoupper(ID_HBASE)?></p>
	</div>
    <div style="border:1px solid gray; background-color:white; margin-left: 20px; margin-top:10px; margin-bottom:7px; width:760px; min-height:190px">
    <?php 
    	$src = "get_url.php?root=http%3A%2F%2Flogin.mijngrossier.be%2F&page=http%3A%2F%2Flogin.mijngrossier.be%2Fmglive%2Flogin%2F%3Fappid%3D12%26catalogmode%3DHBASE_BE_NL%26multipleitems%3D0%26cataloglanguage%3DNL%26itemselect%3D0%26aip%3D1%26nt%3D0%26nbo%3D0%26deb%3D0";
    	if($_SESSION["language"] == "FR")
    	{
    		$src = "get_url.php?root=http%3A%2F%2Flogin.mongrossiste.eu%2F&page=http%3A%2F%2Flogin.mongrossiste.eu%2Fmglive%2Flogin%2F%3Fappid%3D12%26catalogmode%3DHBASE_BE_FR%26multipleitems%3D0%26cataloglanguage%3DFR%26itemselect%3D0%26aip%3D1%26nt%3D0%26nbo%3D0%26deb%3D0";
    	}
   		echo "<iframe id='iframe_doc' src='" . $src . "' width='100%' height='600px' style='border: 0;'>";
    	echo "</iframe>";
    ?>
	</div>
</div>
</body>
<script type="text/javascript">
var alltables=document.getElementsByTagName("table")
for (var i=0; i<alltables.length; i++)
disableSelection(alltables[i]) //disable text selection within all tables on the page
</script>
</html>





