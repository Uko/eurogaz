<?php
	include_once("engine/catalogue.variables.php");

	$thisScriptName = thisRealScriptName($_SERVER["SCRIPT_NAME"], $_SERVER["SCRIPT_FILENAME"], __FILE__);
	$thisScriptName = $thisScriptName[0].$thisScriptName[1].$thisScriptName[2];
	$pathToThisScript = substr($thisScriptName, 0, strlen($thisScriptName) - strlen(basename($thisScriptName)));
	$pathToThumbnails = $pathToThisScript . "thumbnails/";
	$urlBeginning = "?page=goods&";
	
	$trash = $_COOKIE["trash"];
	$trash = explode(",", $trash);
	for($i = 0, $n = count($trash); $i < $n; $i++)
	{
		$item = explode(":", $trash[$i]);
		$trash[$item[0]] = $item[1];
		unset($trash[$i]);
	}
	
	//Відкрити з'єдняння для подальших дій
	if(!$mysqlConnectionLinkID)
		$mysqlConnectionLinkID = openMySQLConnection($mysqlHostname, $mysqlUsername, $mysqlPassword);
	
	function itemThumbnailInListTrashView($pathToThumbnail, $itemId, $itemTitle, $itemPrice, $urlBeginning = "?")
	{//trash
		$result =
			"
			<td>
				<a href=\"{$urlBeginning}show=$itemId\">
					<img class=\"catalogueItemPic\" src=\"$pathToThumbnail\" alt=\"{$itemTitle}_thumbnail\"/>
				</a>
			</td>
			<td>
				<div class=\"catalogueItemTitle\"><a href=\"{$urlBeginning}show=$itemId\">$itemTitle</a></div>
			</td>
			<td>
				{$itemPrice} грн.
				<a href=\"javascript:removeItemFromTheTrash('$itemId');\">Видалити з кошика</a>
			</td>";
		return $result;
	}

	function isEmpty($item)
	{
		return !empty($item);
	}
	$trash = array_filter($trash, "isEmpty");

	$items = "<table class=\"onlyItems\">";
	$summ = 0;
	foreach($trash as $id => $amount)
	{
		/*
		 *	завантаження з бази рядка з інформацією яку потрібно показати
		 */
		$itemData = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueItems, "id", $id);
		$itemData = $itemData[0];
		//$itemsData[] = $itemData;
		$itemFeatures = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueFeatures, "id", $id);
		$itemFeatures = $itemFeatures[0];
		$items .= "<tr>" . itemThumbnailInListTrashView( $pathToThumbnails . $itemData["thumbnail"],
				$itemData["id"], $itemData["name"],
				$itemFeatures["price"], $urlBeginning) . "</tr>";
		$summ += $itemFeatures["price"];
	}
	$items .= "</table>";

	echo "
		<script type=\"text/javascript\" src=\"{$pathToThisScript}engine/catalogue.functions.js\"></script>
		<script type=\"text/javascript\" src=\"engine/functions.js\"></script>
		<script type=\"text/javascript\">
			$(document).ready
			(
				function()
				{
					insert_css('{$pathToThisScript}catalogue.css');
				}
			);
		</script>
		<div id=\"trashViewContainer\">
			";
	if(!empty($trash))
	{
		echo "$items
			<div \"priceSumm\">	Загальна сума: $summ грн. </div>";
	}
	else
	{
		echo "Кошик порожній...";
	}
	echo "
		</div>";
	
	//close connection all things with mysql are done
	if($mysqlConnectionLinkID)
	{
		mysql_close($mysqlConnectionLinkID);
		unset($mysqlConnectionLinkID);
	}
?>