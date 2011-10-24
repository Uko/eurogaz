<?php
	include_once("../variables.php");
	include_once("../functions.php");
	include_once("../functions.MySQL.php");
	include_once("../security.isLoggedIn.php");
	include_once("../seo.php");
	include_once("buttons_functions.php");
	if(!isset($_POST["index"])||!isset($_POST["content"])||(!$loggedIn))
	{
		echo "Sorry error occured.";
	}
	else
	{
		if(!$mysqlConnectionLinkID)
			$mysqlConnectionLinkID = openMySQLConnection($mysqlHostname, $mysqlUsername, $mysqlPassword);
		$ButtonData = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreButtons);
		SetContent($ButtonData, $_POST["index"],  $_POST["content"], $_POST["title"], $mysqlConnectionLinkID, $mysqlDBTableToStoreButtons);
		if($mysqlConnectionLinkID)
		{
			mysql_close($mysqlConnectionLinkID);
			unset($mysqlConnectionLinkID);
		}
		echo "Зміни збережено.";
	}
?>