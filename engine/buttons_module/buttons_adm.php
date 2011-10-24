<?php
	include_once("../variables.php");
	include_once("../functions.php");
	include_once("../functions.MySQL.php");
	include_once("../security.isLoggedIn.php");
	include_once("../seo.php");
	include_once("buttons_functions.php");
	if(!isset($_GET["action"])||(!$loggedIn))
	{
		echo "Sorry error occured.";
	}
	else
	{
		if(!$mysqlConnectionLinkID)
		$mysqlConnectionLinkID = openMySQLConnection($mysqlHostname, $mysqlUsername, $mysqlPassword);
			$ButtonData = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreButtons);
		switch ($_GET["action"]) 
		{
		    case "moveup":
				$ButtonData = MoveUpItem($ButtonData,$_GET["curr"]);
				UpdateDatabase($ButtonData, $mysqlConnectionLinkID, $mysqlDBTableToStoreButtons, $mainURL, $mysqlDBTableToStoreButtons);
        		break;
    		case "movedown":
	        	$ButtonData = MoveDownItem($ButtonData,$_GET["curr"]);
				UpdateDatabase($ButtonData, $mysqlConnectionLinkID, $mysqlDBTableToStoreButtons, $mainURL, $mysqlDBTableToStoreButtons);
    	    	break;
    		case "remove":
	        	RemoveElement($ButtonData, $_GET["curr"], $mysqlConnectionLinkID, $mysqlDBTableToStoreButtons, $mainURL, $mysqlDBTableToStoreButtons);
    	    	break;
			case "add":
	        	CreateNewElement($ButtonData, $mysqlConnectionLinkID, $mysqlDBTableToStoreButtons, $mainURL, $mysqlDBTableToStoreButtons);
    	    	break;
		}
		if($mysqlConnectionLinkID)
		{
			mysql_close($mysqlConnectionLinkID);
			unset($mysqlConnectionLinkID);
		}
	}
?>