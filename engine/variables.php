<?php
	include_once("symbols.php");
	
	//in this order: "nameID", "title", "content", "isModule", "keywords", "description", "if_news". It's important for work with DB
	$pageParseTags = array("nameID", "title", "content", "isModule", "keywords", "description");
	
	//General
	$mainURL = "http://eurogaz.com.ua/";
	
	//global things
	$title = "Єврогазприлад";
	$output = "";
	$keywords = "опалення, сантехніка, водопостачання, каналізація";
	$description = "Газопровідні роботи. Монтаж ситеми опалення, вентиляції та кондинціонування повітря. Водопровідні, каналізаційні та протипожежні роботи. Оптова торгівля будівельними матеріалами. Оптова торгівля залізними виробами, водопровідним та опалювальним устаткуванням.";
	$news_toshow = 10;		//max number of news to show on "news" page
	
	//MySql
	$mysqlHostname = "localhost";
	$mysqlUsername = "eurogaz";
	$mysqlPassword = "W5yS.6gx"; 
	$mysqlDBName = "eurogaz";
	$mysqlDBTableToStorePages = "eurogas_pages";
	$DBTableColumnWithPageNames = "nameID";
	$DBTableColumnWithPageTitles = "title";
	$mysqlDBTableToStoreButtons = "eurogas_buttons_module";
	
	//admin page login data
	$username = "locker";
	$userpassword = "noSOUP4u";
	$randomword = "allibabahabbibibiandthreehungrycookiesoO";
	$cookiesPlace = "/";	//domain or directory where to set cookies, if you don't understand it leave it in piece
	
	//miscellaneous
	$debugMode = false;
?>
