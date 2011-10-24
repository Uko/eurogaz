<?php
	if($loggedIn)
	{
		//here secure ajax file creation
		$secureAjaxFile = fopen("..php", "wt");
		//secure ajax hash code
		$SAHC = md5($username.time().$randomword.$userpassword) . date("B");
		fwrite($secureAjaxFile, "<?php \$SAHC = \"{$SAHC}\"; ?>");
		fclose($secureAjaxFile);
		
		$thisScriptName = thisRealScriptName($_SERVER["SCRIPT_NAME"], $_SERVER["SCRIPT_FILENAME"], __FILE__);
		$thisScriptName = $thisScriptName[0].$thisScriptName[1].$thisScriptName[2];
		$pathToThisScript = substr($thisScriptName, 0, strlen($thisScriptName) - strlen(basename($thisScriptName)));
		
		$urlBeginning = "?";
		if(count($_GET))
			foreach(array_keys($_GET) as $gak)
				if(( $gak != "page")&&($gak != "adm_mode"))
					$urlBeginning .= $gak . "=" . $_GET[$gak] . "&";
		
		$edit = "";
		if(isset($availablePages) && count($availablePages) && (!$availablePages["error"]))
		{
			if ($pageData["nameID"] == "main")
				$edit = "edit(true)";
			else
				$edit = "edit(false)";
			
			$moduleList = "<h4>Модулі:</h4>
			<ul id=\"moduleList\">";
			$pagesList = "<h4>Сторінки:</h4>
			<ul id=\"pagesList\">";
			/**
			 *	Building list of available pages.
			 */
			foreach($availablePages as $ap)
			{
				$apOne = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStorePages, "nameID", $ap);
				$apOne = $apOne[0];
				if($apOne["isModule"])
					$moduleList .= "<li><a href=\"{$urlBeginning}page=$ap\">" . ($apOne["title"] ? $apOne["title"] : $ap) . "</a></li> ";
				else
					$pagesList .= "<li><a href=\"{$urlBeginning}page=$ap\">" . ($apOne["title"] ? $apOne["title"] : $ap) . "</a></li> ";
			}
			$pagesList .= "</ul>";
			$moduleList .= "</ul>";
			
			$removeButton = "<a href=\"javascript:removePage();\" class=\"pageRemover\" id=\"removePageButton\">Видалити сторінку</a>";
		}
		if ($pageData["isModule"] == 0)
		{
			//Form with page data for editing
			$loggedInDataForm = "
							<script type=\"text/javascript\" src=\"{$pathToThisScript}tiny_mce/tiny_mce.js\"></script>
							<script type=\"text/javascript\" src=\"{$pathToThisScript}functions.admin.js\"></script>
							<script type=\"text/javascript\" src=\"{$pathToThisScript}ajaxClientPart.js\"></script>
							<script type=\"text/javascript\">
								$(document).ready(preparePageForEditing);
							</script>
							<form id=\"loggedInDataForm\" action=\"javascript:;\" method=\"post\" enctype=\"application/x-www-form-urlencoded\" accept-charset=\"UTF-8\">
								<input id=\"SAHC\" type=\"hidden\" name=\"sahc\" value=\"{$SAHC}\"/>" .
								//<input id=\"pageId\" type=\"hidden\" name=\"page_id\" value=\"{$pageData["id"]}\"/>
								"<input id=\"pageName\" type=\"hidden\" name=\"page_name\" value=\"{$pageData["nameID"]}\"/>
								<input id=\"pageTitle\" type=\"hidden\" name=\"page_title\" value=\"{$pageData["title"]}\"/>
								<input id=\"pageKeywords\" type=\"hidden\" name=\"page_keywords\" value=\"{$pageData["keywords"]}\"/>
								<input id=\"pageDescription\" type=\"hidden\" name=\"page_description\" value=\"{$pageData["description"]}\"/>
							</form>
							";

			//$menu .= "\t\t\t<li><a id=\"menuButton_addNewPage\" href=\"javascript:showAddPageForm();\">Нова сторінка</a></li>" . $PHP_EOL;
			$menu .= "\t\t\t<li><a id=\"edit_button\" href=\"javascript:{$edit};\">Редагувати сторінку</a></li>";
			//$menu .= ($removeButton) ? "\t\t\t<li>".$removeButton."</li>" : "";
			if($debugMode)
				$menu .= "\t\t\t<li><a id=\"temp\" href=\"javascript:initDBRequest('ajaxResultContainer');\">Initialize</a></li>" . $PHP_EOL;
		}
		if ($pageData["nameID"] == "services")
			$menu .= "\t\t\t<li><a id=\"edit_button\" href=\"?page=services&adm_mode=on\">Редагувати модуль</a></li>";
		$menu .= "\t\t\t<li><a id=\"menuButton_logout\" href=\"editor/logout.php\">Вихід</a></li>" . $PHP_EOL;
	}
?>