<?php
	include_once("../engine/variables.php");
	include_once("../engine/functions.php");
	include_once("../engine/security.logIn.php");
	//if user isn't logged in the just stop executing script
	if(!$loggedIn)
		exit();
	else
		redirect($mainURL, 301);
	//if user have logged in just now then redirect him to main page
	if($haveLogged)
		redirect($mainURL, 301);
?>