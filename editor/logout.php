<?php
	$sahcft = $_POST["sahc"];
	include_once("../engine/security.isLoggedIn.php");
	if($loggedIn)
	{
		include_once("../engine/variables.php");
		//check if there is place to store cookies and delete them
		if(isset($cookiesPlace))
			setcookie ("RaidimAdminPage", "", time() - 1, $cookiesPlace);
		else
			setcookie ("RaidimAdminPage", "", time() - 1);
		
		//delete "../..php" file !important
		$secureAjaxFile = fopen("../..php", "wt");
		fclose($secureAjaxFile);
		@unlink("../..php");
		@unlink("../engine/catalogue/engine/sup.php");
		@unlink("../engine/catalogue/engine/..php");
		
		//redirect user to main page
		include_once("../engine/functions.php");
		redirect($mainURL);
	}
	else
	{echo":p";}
?>