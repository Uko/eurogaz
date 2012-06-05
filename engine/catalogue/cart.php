<?php
	include_once("engine/catalogue.variables.php");
	
	/*
	 *	Cart cookie format: "id1:amount1,id2:amount2,..."
	 */
	$cart = explode(",", $_COOKIE["cart"]);
	for($i = 0, $n = count($cart); $i < $n; $i++)
	{
		$item = explode(":", $cart[$i]);
		$cart[$item[0]] = $item[1];
		unset($cart[$i]);
	}
	//filter out empty values
	$cart = array_filter($cart, function($item) { return !empty($item);});
	
	$itemsData = array();
	$summ = 0;
	//Відкрити з'єдняння для подальших дій
	if(!$mysqlConnectionLinkID)
		$mysqlConnectionLinkID = openMySQLConnection($mysqlHostname, $mysqlUsername, $mysqlPassword);
	foreach($cart as $id => $amount)
	{
		/*
		 *	завантаження з бази рядка з інформацією яку потрібно показати
		 */
		$itemData = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueItems, "id", $id);
		$itemData = $itemData[0];
		if(!$itemData["thumbnail"]) $itemData["thumbnail"] = "default.png";

		$itemFeatures = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueFeatures, "id", $id);
		$itemFeatures = $itemFeatures[0];
		$itemsData[] = array( "id" => $id,
		                      "name" => $itemData["name"],
		                      "price" => $itemFeatures["price"],
		                      "amount" => $amount,
		                      "thumbnail" => $itemData["thumbnail"]);
		$summ += $itemFeatures["price"] * $amount;
	}
	//close connection all things with mysql are done
	if($mysqlConnectionLinkID)
	{
		mysql_close($mysqlConnectionLinkID);
		unset($mysqlConnectionLinkID);
	}

	//show all this stuff to the user
	include_once("cart.view.php");
?>
