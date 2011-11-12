<?php

if ($loggedIn)
{
	$pathToThisFile = thisRealScriptName($_SERVER["SCRIPT_NAME"], $_SERVER["SCRIPT_FILENAME"], __FILE__);
	$pathToCatalogue = substr($pathToThisFile[1], 0, strlen($pathToThisFile[1]) - 2);
	$pathToCatalogue = substr($pathToCatalogue, 0, strrpos($pathToCatalogue, "/") + 1);
	//$fromScriptToThisFile = substr($pathToThisFile[1], 0, strlen($pathToThisFile[1]) - strlen(basename($pathToThisFile[1])));
	//here secure ajax file creation
	$secureAjaxFile = fopen(str_replace("/", DIRECTORY_SEPARATOR, $pathToThisFile[1]) . "..php", "wt");
	//secure ajax hash code
	$SAHC = md5($username . time() . $randomword . $userpassword) . date("B");
	fwrite($secureAjaxFile, "<?php \$SAHC = \"{$SAHC}\"; ?>");
	fclose($secureAjaxFile);

	include_once "catalogue.functions.php";
	require_once "ThumbLib/ThumbLib.inc.php";
	include_once "catalogue.variables.php";

	$whereToRedirect = $_SERVER["PHP_SELF"] . $urlBeginning . "show=" . $_GET["show"];
	if ($itemData["type"] == "group")
		$whatTo = "цю групу";
	elseif ($itemData["type"] == "item")
		$whatTo = "цей товар";
	$urlBeginningForAdminPanel = $urlBeginning . "show=" . $_GET["show"] . "&catalogue=";
	//$adminPanel = adminPanelView($urlBeginningForAdminPanel, $itemData["type"], $whatTo);
	$scripts = "<script type=\"text/javascript\" src=\"" . $pathToThisFile[0] . $pathToThisFile[1] . "adminpanel.functions.js\" ></script>" .
			"<script type=\"text/javascript\" src=\"engine/tiny_mce/tiny_mce.js\"></script>";
	$allowGroupChildren = true;
	$allowItemChildren = true;
	if($itemData["type"] == "group")
		foreach($itemsFromDB as $ifdbValue)
		{
			if($ifdbValue["type"] == "group")
				$allowItemChildren = false;
			if($ifdbValue["type"] == "item")
				$allowGroupChildren = false;
			if(!$allowItemChildren && !$allowGroupChildren)
				break;
		}
	$viewContainer = $scripts . adminPanelView($urlBeginningForAdminPanel, $itemData["type"], $whatTo, $allowGroupChildren, $allowItemChildren, !$itemData["id"]) . $viewContainer;
	switch ($_GET["catalogue"])
	{
		case "add_group":
		case "add_item":
		case "edit_item":
			$currItemData = array();
			//дістати всі заголовки в таблиці features -- початок
			$features = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueFeatures, $itemData["id"] ? "id" : "", $itemData["id"] ? $itemData["id"] : "", "", 1);
			if ($features)
			{
				$features = $features[0];
				$featuresHead = array_keys($features);
			} else
			{
				$features = describeBDTable($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueFeatures);
				$featuresHead = $features["Field"];
			}
			//дістати всі заголовки в таблиці features -- кінець
			//далі їх обробка...
			//поле вибору методу вводу картинок
			if (!(array_search("images", $featuresHead) === FALSE))
			{
				$addon .= addPicturesBoxView($SAHC, $pathToThisFile[0] . $pathToThisFile[1], inputFeatureView("images", $LOCAL["images"]));
				if ($_GET["catalogue"] == "edit_item")
				{
					$docReady = "\$('input#images').val('{$features["images"]}');";
					$docReady .= "\$('#imageInputType').val('byHand');";
				}
				else
					$docReady = "\$('input#images').val(''); ";
				$docReady .= "changeImageInputType('$SAHC', '" . $pathToThisFile[0] . $pathToThisFile[1] . "');";
			}

			//get collection types' configurations
			if (!$collectionTypesConfigs)
				$collectionTypesConfigs = getCollectionTypesConfigurations($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueOptions);

			//add type changers to the output
			$isDefaultTypeChecked = true;
			for ($ak = array_keys($collectionTypesConfigs), $n = count($ak), $i = 0; $i < $n; $i++)
			{
				if ($features["collection_type"] == $ak[$i])
				{
					$changers .= collectionsChangerRadioBox($i, $ak[$i], $ak[$i], true);
					$isDefaultTypeChecked = false;
				}
				else
					$changers .= collectionsChangerRadioBox($i, $ak[$i], $ak[$i]);
			}
			$changers .= collectionsChangerRadioBox(-1, "not_collection", $LOCAL["not_collection"], $isDefaultTypeChecked);
			//$addon .= collectionsTypesChangersBox($changers);
			//$docReady = "changeCollectionType(); " . $docReady;

			if(!isset($parentFeatures))
			{
				$parentFeatures = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName,
						$mysqlDBTableToStoreCatalogueFeatures, "id",
						(($_GET["catalogue"] == "edit_item") ? $itemData["parent"] : $itemData["id"]));
				$parentFeatures = $parentFeatures[0];
			}

			$featList = "";
			for ($i = 1, $n = count($featuresHead); $i < $n; $i++)
			{ //$i з 1 бо поля id виводити непотрібно
				//вивід характеристик у вигляді звичайних полів вводу тексту
				if (!(($featuresHead[$i] == "attributes") || ($featuresHead[$i] == "images") || ($featuresHead[$i] == "collection_type")))
				{
					//визначення до яких колекцій належить дана характеристика і вписати її в class тегу контейнера
					// - щоб потім можна було їх приховувати, при зміні типу колекції користувачем
					$attrCollections = "";
					foreach (array_keys($collectionTypesConfigs) as $akctc)
					{
						if (!(array_search($featuresHead[$i], $collectionTypesConfigs[$akctc]) === FALSE))
							$attrCollections .= str_replace(' ', '_', $akctc) . ' ';
					}
					if ($attrCollections)
						$attrCollections = trim($attrCollections);

					//if (!$isDefaultTypeChecked) //якшо вибраний певний тип колекції, не тип по замовчуванню
					//{
						if (($featuresHead[$i] == "collection")) //якщо вибрана певна колекція, не тип колекції
						{
							if($_GET["catalogue"] == "edit_item")
								$value = $features[$featuresHead[$i]];
							else
								$value = $itemData["name"];
						}
						//elseif(!($features[$featuresHead[$i]])) //якшо нема власних характеристик - грузити батьківські
						//{
						//	$value = $parentFeatures[$featuresHead[$i]];
						//}
						else
							$value = $features[$featuresHead[$i]];
					//}
					//else
					//	$value = "";
					$featList .= inputFeatureView($featuresHead[$i], $LOCAL[$featuresHead[$i]], $value, $attrCollections);
				}
			}
			//вивід атрибутів (вибірка) у вигляді чекбоксів
			if (!(array_search("attributes", $featuresHead) === FALSE))
			{
				//дістати всі можливі атрибути
				$attr = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueOptions, "", "", "", 1);
				$attr = explode(',', $attr[0]["attributes"]);
				for ($j = 0, $m = count($attr); $j < $m; $j++)
					$attrs .= attributeCheckBoxView("attributes_$j", ucfirst($attr[$j]));
			}
			
			if ((($_GET["catalogue"] == "edit_item") && ($itemData["type"] != "group")) || ($_GET["catalogue"] == "add_item"))
				$addon .= inputFeaturesBoxView($featList, "Рекомендується для:", $attrs);

			$whereToRedirect = str_replace("show=" . $_GET["show"], "show=" . $itemData["id"], $whereToRedirect);

			if ($_GET["catalogue"] == "edit_item")
			{
				$addOrEdit = "edit";
				$currItemData["id"] = $itemData["id"];
				$currItemData["type"] = $itemData["type"];
				$currItemData["name"] = $itemData["name"];
				$currItemData["thumbnail"] = $itemData["thumbnail"];
				$currItemData["description"] = $itemData["description"];
			} else
			{
				$addOrEdit = "add";
				$currItemData["parent"] = $itemData["id"];
				$currItemData["type"] = substr($_GET["catalogue"], 4, strlen($_GET["catalogue"]) - 4);
			}

			$viewContainer = $scripts . addItemView($whereToRedirect, $currItemData, $addon, $docReady, $addOrEdit);

			break;
		case "remove_item":
			if ($_GET["show"])
			{
				$whereToRedirect = str_replace("show=" . $_GET["show"], "show=" . (($itemData["parent"] == "0") ? "" : $itemData["parent"]), $whereToRedirect);
				$features = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueFeatures, "id", $itemData["id"], "", 1);
				$features = $features[0];
				if ($itemData["thumbnail"])
					unlink($pathToCatalogue . "thumbnails/" . $itemData["thumbnail"]);
				switch ($itemData["type"])
				{
					case "item":

						if ($features["images"])
						{
							$imagelist = explode("\\\\ ", $features["images"]);
							foreach ($imagelist as $iml)
							{
								unlink($pathToCatalogue . "realSizeImages/" . $iml);
								unlink($pathToCatalogue . "images/" . $iml);
							}
						}
						removeItemFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueFeatures, "id", $itemData["id"]);
						removeItemFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueItems, "id", $itemData["id"]);
						break;
					case "group":
						removeItemFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueFeatures, "id", $itemData["id"]);
						removeGroupFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueItems, $mysqlDBTableToStoreCatalogueFeatures, $itemData["id"], $pathToCatalogue);
					default: break;
				}
				redirect($whereToRedirect);
			}
		default: break;
	}
	//here getting data from form and adding it to the DB
	//preparation:
	if ($_POST["what"] == "add" || $_POST["what"] == "edit")
	{
		if(!isset($parentFeatures))
		{
			$parentFeatures = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName,
					$mysqlDBTableToStoreCatalogueFeatures, "id",
					(($_POST["what"] == "edit") ? $itemData["parent"] : $itemData["id"]));
			$parentFeatures = $parentFeatures[0];
		}

		$POSTfeatures = array();
		//дістати всі заголовки в таблиці features -- початок
		//це для того щоб в функцію addItemIntoDB передати заголовки характеристик які треба оновити
		$featuresHead = describeBDTable($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueFeatures);
		$featuresHead = $featuresHead["Field"];
		//дістати всі заголовки в таблиці features -- кінець
		//далі їх обробка...
		for ($i = 1, $n = count($featuresHead); $i < $n; $i++) //$i починаючи з одиниці бо нам id непотрібне і ми його не знаєм :)
			if (($_POST[$featuresHead[$i]] !== $parentFeatures[$featuresHead[$i]]) ||	//якщо ці дані не збігаються з батьківськими
						($parentFeatures[$featuresHead[$i]] == "") || 					//якщо батьківські дані порожні
						($featuresHead[$i] == "collection_type"))						//якщо дана характеристика - collection_type
				$POSTfeatures[$featuresHead[$i]] = $_POST[$featuresHead[$i]];
		//якщо поле іконки не ввели і поле картинок заповнене
		// - генерація іконки з першого введеного зображення
		if (!$_POST["thumbnail"] && $POSTfeatures["images"])
		{
			$imagelist = explode(" ", $POSTfeatures["images"]);
			foreach (array_keys($imagelist) as $imlk)
			{
				if (substr($imagelist[$imlk], strpos($imagelist[$imlk], '.'), 4) == ".bmp")
				{
					include_once "bmp.php";
					$im = imagecreatefrombmp($pathToCatalogue . "realSizeImages/" . $imagelist[$imlk]);
					unlink($pathToCatalogue . "realSizeImages/" . $imagelist[$imlk]);
					$tmp = str_replace(".bmp", ".png", $imagelist[$imlk]);
					$POSTfeatures["images"] = str_replace($imagelist[$imlk], $tmp, $POSTfeatures["images"]);
					$imagelist[$imlk] = $tmp;
					imagepng($im, $pathToCatalogue . "realSizeImages/" . $imagelist[$imlk]);
				}
				list($width, $height) = getimagesize($pathToCatalogue . "realSizeImages/" . $imagelist[0]);
				if(($width > 200) || ($height > 300))
				{
					$thumb = PhpThumbFactory::create($pathToCatalogue . "realSizeImages/" . $imagelist[$imlk]);				
					$thumb->resize(200, 300);
					$thumb->save($pathToCatalogue . "images/" . basename($imagelist[$imlk]));
				}
				else
				{
					copy($pathToCatalogue . "realSizeImages/" . $imagelist[0], $pathToCatalogue . "images/" . $imagelist[0]);
				}
			}
			$thumb = PhpThumbFactory::create($pathToCatalogue . "realSizeImages/" . $imagelist[0]);
			//list($width, $height) = getimagesize($pathToCatalogue . "realSizeImages/" . $imagelist[0]);
			//$thumb->cropFromCenter(min($width, $height), min($width, $height));
			$thumb->setOptions(array("resizeUp"=> "true"));
			$thumb->adaptiveResize(120, 180);
			$_POST["thumbnail"] = $pathToCatalogue . "thumbnails/" . basename($imagelist[0]);
			$thumb->save($_POST["thumbnail"]);
			$_POST["thumbnail"] = basename($imagelist[0]);
		}
		if ($POSTfeatures["attributes"])
			$POSTfeatures["attributes"] = implode(',', $POSTfeatures["attributes"]);
	}
	switch ($_POST["what"])
	{
		case "add":
			addItemIntoDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueItems,
					$_POST["type"],
					$_POST["parent"],
					$_POST["name"],
					$_POST["thumbnail"],
					$_POST["description"],
					$mysqlDBTableToStoreCatalogueFeatures,
					$POSTfeatures);
			redirect($whereToRedirect);
			break;
		case "edit":
			echo saveItemInTheDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueItems,
					$_POST["id"],
					$_POST["name"],
					$_POST["thumbnail"],
					$_POST["description"],
					$mysqlDBTableToStoreCatalogueFeatures,
					$POSTfeatures);
			redirect($whereToRedirect);
			break;
		default: break;
	}
}
?>