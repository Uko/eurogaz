<?php
	include_once("symbols.php");
	
	//in this order: "nameID", "title", "content", "isModule", "keywords", "description", "if_news". It's important for work with DB
	$pageParseTags = array("nameID", "title", "content", "isModule", "keywords", "description");
	
	//General
	$mainURL = "http://basement.22web.net/eurogas/";
	
	//global things
	$title = "Єврогазприлад";
	$output = "";
	$keywords = "";
	$description = "";
	$news_toshow = 10;		//max number of news to show on "news" page
	
	//MySql
	$mysqlHostname = "sql101.byethost8.com";
	$mysqlUsername = "b8_5210590";
	$mysqlPassword = "stz87djxvwr0"; 
	$mysqlDBName = "b8_5210590_basement";
	$mysqlDBTableToStorePages = "eurogas_pages";
	$DBTableColumnWithPageNames = "nameID";
	$DBTableColumnWithPageTitles = "title";
	$mysqlDBTableToStoreButtons = "eurogas_buttons_module";
	
	//admin page login data
	$username = "tezh";
	$userpassword = "ta1k3by!i";
	$randomword = "allibabahabbibibiandthreehungrycookiesoO";
	$cookiesPlace = "/";	//domain or directory where to set cookies, if you don't understand it leave it in piece
	
	//miscellaneous
	$debugMode = true;
?>
