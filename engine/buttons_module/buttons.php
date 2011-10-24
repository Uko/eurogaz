<?php
	include_once("engine/variables.php");
	include_once("engine/functions.php");
	include_once("engine/functions.MySQL.php");
	include_once("engine/security.isLoggedIn.php");
	include_once("engine/seo.php");
	include_once("buttons_functions.php");
	if(!$mysqlConnectionLinkID)
		$mysqlConnectionLinkID = openMySQLConnection($mysqlHostname, $mysqlUsername, $mysqlPassword);
	$ButtonData = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreButtons);
	//get service name or set first as default
	if(isset($_GET["service_name"]))
	{
		$ServiceID=$_GET["service_name"];
	}
	else
	{
		$ServiceID=$ButtonData[0]["name"];
	}
	$loggedInButtons=true;
	if(($_GET["adm_mode"]!="on")||(!$loggedIn))
	{
		$loggedInButtons=false;
	}
	PutHtmlButtons($ButtonData, $loggedInButtons, $ServiceID, false);
	if($mysqlConnectionLinkID)
	{
		mysql_close($mysqlConnectionLinkID);
		unset($mysqlConnectionLinkID);
	}
?>