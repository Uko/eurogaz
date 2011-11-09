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
		<title><?php echo $title; if(($titleLocal != "")&&($title != "")) echo " - "; echo $titleLocal; ?></title>
		<meta http-equiv="content-language" content="ua"/>
		<meta name="keywords" content="<?php echo $keywordsOutput; ?>" />
		<meta name="Description" content="<?php echo $descriptionOutput; ?>" />
		<meta content="all" name="audience"/>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<link type="text/css" rel="stylesheet" href="main.css"/>
		<link type="text/css" rel="stylesheet" href="fonts.css"/>
		<link type="text/css" rel="stylesheet" href="buttons.css"/>
		<link type="text/css" rel="stylesheet" href="engine/buttons_module/buttons_adm.css"/>
		<link type="text/css" rel="stylesheet" href="engine/colorbox/colorbox.css" media="screen"/>
		<link type="text/css" rel="stylesheet" href="engine/jquery-ui-1.8.16/jquery-ui-1.8.16.base.css"/>
		<link type="image/x-icon" rel="shortcut icon" href="/favicon.ico">
		<script type="text/javascript" src="engine/jquery-1.7.min.js" ></script>
		<script type="text/javascript" src="engine/jquery-ui-1.8.16/jquery-ui-1.8.16.min.js"></script>
		<script type="text/javascript" src="engine/getElementsByClassName-1.0.1.js"></script>
		<script type="text/javascript" src="engine/main.js"></script>
		<script type="text/javascript" src="engine/colorbox/jquery.colorbox-min.js"></script>
        	<script type="text/javascript" src="engine/buttons_module/ajaxFuncs.js" ></script>
		<script type="text/javascript">
			$(document).ready(function()
			{
				$("a[rel^='lightbox']").colorbox({current:''});
			});
		</script>
		<?php
			if(!$loggedIn)
				echo $seo;
		?>
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
				<a href="?page=cart">Кошик</a>
				<form action="" id="cse-search-box">
					<div>
						<input type="text" name="q" size="31" />
						<input type="hidden" name="page" value="search" />
    					<input type="hidden" name="cx" value="008098359109678897342:1ci09twrl9g" />
    					<input type="hidden" name="cof" value="FORID:9" />
    					<input type="hidden" name="ie" value="UTF-8" />
    					<input type="submit" name="sa" value="Пошук" />
  					</div>
				</form>
			</div>
			<p id="MainMenu">
               	<a class="MenuItem<?php echo (($PageToLoad == "main") ? " MenuItemSelected" : "");?>" href="?page=main"> Головна </a>
               	<a class="MenuItem<?php echo (($PageToLoad == "services") ? " MenuItemSelected" : "");?>" href="?page=services"> Послуги </a>
				<a class="MenuItem<?php echo (($PageToLoad == "goods") ? " MenuItemSelected" : "");?>" href="?page=goods"> Товари </a>
				<a class="MenuItem<?php echo (($PageToLoad == "results") ? " MenuItemSelected" : "");?>" href="?page=results"> Виконані об'єкти </a>
				<a class="MenuItem<?php echo (($PageToLoad == "license") ? " MenuItemSelected" : "");?>" href="?page=license"> Ліцензія </a>
				<a class="MenuItem<?php echo (($PageToLoad == "contacts") ? " MenuItemSelected" : "");?>" href="?page=contacts"> Контакти </a>
			</p>
		</div>
		<!--<div id="magikStroke"></div>-->
		
		<div id="mainContent" >
				<?php
					if ($PageToLoad=="main")
					{
					echo "
			<div id=\"buttonContent\">";
						if(!$mysqlConnectionLinkID)
							$mysqlConnectionLinkID = openMySQLConnection($mysqlHostname, $mysqlUsername, $mysqlPassword);
						$ButtonData = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreButtons);
						PutHtmlButtons($ButtonData, $loggedIn, "", true);
					echo "
			</div>";
					}
				?>
			<?php if ($PageToLoad=="main") echo "<div id=\"mainContentText\" >"; ?>
        		<?php
					if($mysqlConnectionLinkID)
					{
						mysql_close($mysqlConnectionLinkID);
						unset($mysqlConnectionLinkID);
					}
					echo $output; //main content 
				?> 
			<?php if ($PageToLoad=="main") echo "</div>"; ?>
		</div>
		<div id="footer">
				<p>©2011<?php $date_array = getdate(); if ($date_array[year]>2011) echo ("-".$date_array[year])?> Єврогазприлад</p>
				<?php if (($date_array[year]>2013)||(($date_array[year]>2012)&&($date_array[mon]>6))) echo "
				<div id=\"unikernel\">
				<a href=\"http://www.unikernel.net/\">
					© 2011 UniKernel IT Development Team
				</a>
				</div>" ?>
		</div>
	</div>
	</body>
</html>
