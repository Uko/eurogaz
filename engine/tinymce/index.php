<?php
	include_once("engine/variables.php");
	include_once("engine/functions.php");
	include_once("engine/functions.MySQL.php");
	include_once("engine/security.isLoggedIn.php");
	include_once("engine/seo.php");
	include_once("engine/buttons_module/buttons_functions.php");
	$titleLocal = "";
	$descriptionLocal = "";
	$keywordsLocal = "";
	
	//open connection for following operations
	if(!$mysqlConnectionLinkID)
		$mysqlConnectionLinkID = openMySQLConnection($mysqlHostname, $mysqlUsername, $mysqlPassword);
	$availablePages = getDBColumnIntoList($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStorePages, $DBTableColumnWithPageNames);
	//print_r($availablePages);
	if(isset($availablePages) && count($availablePages) && (!$availablePages["error"]))
	{
		//get page to load
		$PageToLoad = $_GET["page"];
		if(array_search($PageToLoad, $availablePages) === FALSE)
		{
			$PageToLoad = "main";
			if(array_search($PageToLoad, $availablePages) === FALSE)
				$PageToLoad = $availablePages[0];
		}
		$pageData = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStorePages, $pageParseTags[0], $PageToLoad, $pageParseTags);
		$pageData = $pageData[0];
		
		//$pageData = getDataFromFile("./pages/" . $PageToLoad . ".txt", $pageParseTags);
	}
	
	include_once "engine/index.admin.php";

	//close connection all things with mysql are done
	if($mysqlConnectionLinkID)
	{
		mysql_close($mysqlConnectionLinkID);
		unset($mysqlConnectionLinkID);
	}
	
	if(isset($availablePages) && count($availablePages) && (!$availablePages["error"]))
	{		
		if($pageData["error"] == "")
		{
			if($pageData["content"] == "")
				$pageData["content"] = $pageData["remained"];
			if($pageData["desription"] == "")
				$pageData["desription"] = trim(strip_tags(substr($pageData["content"], 0, $dotPos = strpos($pageData["content"], '.')===FALSE?$contentLen = strlen($pageData["content"])>56?56:$contentLen:$dotPos)));
				
			$keywordsLocal .= $pageData["keywords"];
			$descriptionLocal .= $pageData["description"];
			$titleLocal = $pageData["title"];
			if ($pageData["isModule"])
			{
				$tmpo = $output;
				ob_start();
				include_once($pageData["content"]);
				$output = $tmpo;
				$output .= ob_get_contents();
				ob_end_clean();
			}
			else
			{
				$output .= $pageData["content"];
			}
			
			$keywordsOutput = trim($keywords);
			if(($keywordsLocal != "")&&($keywords != "")) 
				$keywordsOutput .= ", ";
			$keywordsOutput	.= trim($keywordsLocal);
			$keywordsOutputArray = explode(",", $keywordsOutput);
			$keywordsOutput = trim($keywordsOutputArray[0]);
			for($i = 1, $n = min(count($keywordsOutputArray), 10); $i < $n-1; $i++)
				$keywordsOutput .= ", " . trim($keywordsOutputArray[$i]);
			
			$descriptionOutput = trim($description);
			if(($descriptionLocal != "")&&($description != "")) 
				$descriptionOutput .= " ";
			$descriptionOutput .= trim($descriptionLocal);
			if(strlen($descriptionOutput) > 192)
			{
				$descriptionOutput = substr($descriptionOutput, 0, min(strlen($descriptionOutput), 192));
				$descriptionOutput = substr($descriptionOutput, 0, strrpos($descriptionOutput, " "));
				$descriptionOutput .= " ...";
			}
		}
		else
			$output .= "Error appeared: " . $pageData["error"];
	}
	else
	{
		$output .= $availablePages["error"];
	}
	
	
	echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>$PHP_EOL";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN"
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="uk" lang="uk">
	<head>
		
		<meta http-equiv="content-language" content="ua"/>
		<meta name="keywords" content="<?php echo $keywordsOutput; ?>" />
		<meta name="Description" content="<?php echo $descriptionOutput; ?>" />
		<title><?php echo $title; if(($titleLocal != "")&&($title != "")) echo " - "; echo $titleLocal; ?></title>
		<meta content="all" name="audience"/>
		<link rel="stylesheet" href="main.css" type="text/css"/>
		<link rel="stylesheet" href="fonts.css" type="text/css"/>
		<link rel="stylesheet" href="buttons.css" type="text/css"/>
        <link rel="stylesheet" href="engine/buttons_module/buttons_adm.css" type="text/css"/>
		<link type="text/css" rel="stylesheet" href="engine/slimbox-2.04/slimbox2.css" media="screen"/>
		
		<script type="text/javascript" src="engine/jquery-1.4.2.min.js" ></script>
		<script type="text/javascript" src="engine/getElementsByClassName-1.0.1.js"></script>
		<script type="text/javascript" src="engine/main.js"></script>
		<script type="text/javascript" src="engine/slimbox-2.04/slimbox2.js"></script>
        <script type="text/javascript" src="engine/tinymce/jscripts/tiny_mce/tiny_mce.js"></script>
        <script type="text/javascript" src="engine/buttons_module/ajaxFuncs.js" ></script>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<?php
			if(!$loggedIn)
				echo $seo;
		?>	
        <script type="text/javascript">
			tinyMCE.init(
			{
				theme : "advanced",
        		mode : "exact",
		        elements : "editable_content",
				force_br_newlines : true,
		        force_p_newlines : false,
        		forced_root_block : '' // Needed for 3.x
			});
		</script>	
	</head>
	<body onload="afterAll();">
	<div id="container">
		<?php
			if($loggedIn)
			{
				echo "<div id=\"admin_panel\">".$PHP_EOL;
				echo "<h4>Опції:</h4>
				<ul id=\"control\">$menu</ul>";
				if($pagesList)
					echo "\t\t\t\t\t".$pagesList.$PHP_EOL;
				if($moduleList)
					echo "\t\t\t\t\t".$moduleList.$PHP_EOL;
				echo "<p id=\"ajaxResultContainer\"></p>".$PHP_EOL;
				echo "</div>".$PHP_EOL;
				echo "\t\t\t\t\t".$loggedInDataForm.$PHP_EOL;
			}
		?>
		<div id="head">
			<div id="sausage">
				<a href="">Кошик</a>
				<form action="" method="post">
					<input type="text" name="search" />
					<input type="submit" value="Пошук" />
				</form>
			</div>
			<p id="MainMenu">
               	<a class="MenuItem<?php echo (($PageToLoad == "main") ? " MenuItemSelected" : "");?>" href="?page=main"> Головна </a>
               	<a class="MenuItem<?php echo (($PageToLoad == "services") ? " MenuItemSelected" : "");?>" href="?page=services"> Послуги </a>
				<a class="MenuItem<?php echo (($PageToLoad == "goods") ? " MenuItemSelected" : "");?>" href="?page=goods"> Товари </a>
				<a class="MenuItem<?php echo (($PageToLoad == "results") ? " MenuItemSelected" : "");?>" href="?page=results"> Виконані об'єкти </a>
				<a class="MenuItem<?php echo (($PageToLoad == "license") ? " MenuItemSelected" : "");?>" href="?page=license"> Ліцензія </a>
				<a class="MenuItem<?php echo (($PageToLoad == "recomm") ? " MenuItemSelected" : "");?>" href="?page=recomm"> Рекомендації </a>
			</p>
		</div>
		<!--<div id="magikStroke"></div>-->

		<div id="mainContent">
        		<?php
					if ($PageToLoad=="main")
					{
						if(!$mysqlConnectionLinkID)
							$mysqlConnectionLinkID = openMySQLConnection($mysqlHostname, $mysqlUsername, $mysqlPassword);
						$ButtonData = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreButtons);
						PutHtmlButtons($ButtonData, $loggedIn, "", true);
					}
					if($mysqlConnectionLinkID)
					{
						mysql_close($mysqlConnectionLinkID);
						unset($mysqlConnectionLinkID);
					}
					echo $output; //main content 
				?> 
		</div>

		
		<!--<div style="height:1px;	width:100%; background-color:#730c00; margin:0 auto; margin-top:10px;"></div>
		<div id="footer">
				<p>©2010<?php// $date_array = getdate(); if ($date_array[year]>2010) echo ("-".$date_array[year])?> Єврогазприлад</p>
				<div id="unikernel">
				<a href="http://www.unikernel.net/">
					© 2010 UniKernel IT Development Team
				</a>
				</div>
		</div>-->
	</div>
	</body>
</html>