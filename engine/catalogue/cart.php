<?php
	include_once("engine/catalogue.variables.php");

	$thisScriptName = thisRealScriptName($_SERVER["SCRIPT_NAME"], $_SERVER["SCRIPT_FILENAME"], __FILE__);
	$thisScriptName = $thisScriptName[0].$thisScriptName[1].$thisScriptName[2];
	$pathToThisScript = substr($thisScriptName, 0, strlen($thisScriptName) - strlen(basename($thisScriptName)));
	$pathToThumbnails = $pathToThisScript . "thumbnails/";
	$urlBeginning = "?page=goods&";
	
	/*
	 *	Cart cookie format: "id1:amount1,id2:amount2,..."
	 */
	$cart = $_COOKIE["cart"];
	$cart = explode(",", $cart);
	for($i = 0, $n = count($cart); $i < $n; $i++)
	{
		$item = explode(":", $cart[$i]);
		$cart[$item[0]] = $item[1];
		unset($cart[$i]);
	}
	
	//Відкрити з'єдняння для подальших дій
	if(!$mysqlConnectionLinkID)
		$mysqlConnectionLinkID = openMySQLConnection($mysqlHostname, $mysqlUsername, $mysqlPassword);
	
	function itemThumbnailInListCartView($pathToThumbnail, $itemId, $itemTitle, $itemPrice, $urlBeginning = "?")
	{//cart
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
				<p>{$itemPrice} грн.</p>
				<p>
					<a href=\"javascript:removeItemFromTheCart('$itemId');\">Видалити з кошика</a>
				</p>
			</td>";
		return $result;
	}

	function isEmpty($item)
	{
		return !empty($item);
	}
	$cart = array_filter($cart, "isEmpty");

	$items = "<table class=\"onlyItems\">";
	$summ = 0;
	foreach($cart as $id => $amount)
	{
		/*
		 *	завантаження з бази рядка з інформацією яку потрібно показати
		 */
		$itemData = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueItems, "id", $id);
		$itemData = $itemData[0];
		if(!$itemData["thumbnail"])
			$itemData["thumbnail"] = "default.png";
		//$itemsData[] = $itemData;
		$itemFeatures = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueFeatures, "id", $id);
		$itemFeatures = $itemFeatures[0];
		$items .= "<tr>" . itemThumbnailInListCartView( $pathToThumbnails . $itemData["thumbnail"],
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
		<div id=\"cartViewContainer\">
			";
	if(!empty($cart))
	{
		echo "$items
			<div id=\"priceSumm\">	Загальна сума: $summ грн. </div>";
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
