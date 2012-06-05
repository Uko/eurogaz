<?php
	function itemThumbnailInListCartView($pathToThumbnail, $itemId, $itemTitle, $itemPrice, $itemAmount, $urlBeginning = "?")
	{//cart
		global $LOCAL;
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
				<p>{$LOCAL["price"]}: {$itemPrice}</p>
				<p>{$LOCAL["amount"]}: {$itemAmount}</p>
				<p>
					<a href=\"javascript:removeItemFromTheCart('$itemId');\">Видалити з кошика</a>
				</p>
			</td>";
		return $result;
	}
	
	$thisScriptName = thisRealScriptName($_SERVER["SCRIPT_NAME"], $_SERVER["SCRIPT_FILENAME"], __FILE__);
	$thisScriptName = $thisScriptName[0].$thisScriptName[1].$thisScriptName[2];
	$pathToThisScript = substr($thisScriptName, 0, strlen($thisScriptName) - strlen(basename($thisScriptName)));
	$pathToThumbnails = $pathToThisScript . "thumbnails/";
	$urlBeginning = "?page=goods&";
	
	$items = "<table class=\"onlyItems\">";
	foreach($itemsData as $id)
	{
		$items .= "<tr>" . itemThumbnailInListCartView( $pathToThumbnails . $id["thumbnail"],
				$id["id"], $id["name"], $id["price"], $id["amount"], $urlBeginning) . "</tr>";
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
?>
