<?php
/**
 *	This piece of code checks if user is logged in or not and 
 *	leave variable $loggedIn filled with true or false
 */
	include_once("variables.php");
	$loggedIn = false;
	if (isset($_COOKIE["RaidimAdminPage"])) 
	{
		if ($_COOKIE["RaidimAdminPage"] == md5($userpassword.$randomword)) 
		{
			$loggedIn = true;
		}
	}
?>