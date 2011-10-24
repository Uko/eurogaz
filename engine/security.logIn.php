<?php
/**
 *	This piece of code checks if user is logged in or have inputed correct data and 
 *	leave variable $loggedIn filled with true or false for the later use...
 *	and variable $haveLogged sets to true if user have logged in.
 */
	include_once("variables.php");
	include_once("security.isLoggedIn.php");
	$haveLogged = false;
	if(!$loggedIn)
	{
		if(($_POST["userName"] == $username) && ($_POST["userPassword"] == $userpassword))
		{
			if(isset($cookiesPlace))
				setcookie("RaidimAdminPage", md5($_POST["userPassword"].$randomword), 0, $cookiesPlace);
			else
				setcookie("RaidimAdminPage", md5($_POST["userPassword"].$randomword));
			$loggedIn = true;
			$haveLogged = true;
		}
		if(!$loggedIn)
		{
			if(isset($_POST["userName"]) || isset($_POST["userPassword"]))
				echo "Wrong data. Try again...";
			include_once("security.loginPageTemplate.php");
			echo $loginPageTemplate;
		}
		
	}
?>