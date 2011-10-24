<?php
	include_once("catalogue.templates.php");
	include_once("catalogue.variables.php");
	include_once("catalogue.functions.php");
	include_once("symbols.php");
	
/////////////////////-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
//Functions -- Start -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
	function buildGroupList($arr, $parentId, $mysqlConnectionLinkID = "", $mysqlDBName = "", $mysqlDBTableForFeatures = "", $pref = "")
	{
		$res = array();
		$sel = "";	//набір option елементів для select в формі пошуку
		//Вибрати всі елементи в яких parent співпадає з другим параметром
		foreach(array_keys($arr) as $i)
		{
			if(($arr[$i]["parent"] == $parentId) && ($arr[$i]["id"] != $parentId))
			{
				$res[] = $arr[$i];
				unset($arr[$i]);
			}
		}
		//Для кожного з вибраних елементів рекурсивно повибирати синів
		for($i = 0, $n = count($res); $i < $n; $i++)
		{
			$title = "not_collection";
			if($mysqlDBTableForFeatures)
			{
				$parentFeatures = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableForFeatures, "id", $res[$i]["id"]);
				$parentFeatures = $parentFeatures[0];

				if($parentFeatures["collection_type"])
					$title = $parentFeatures["collection_type"];
			}
			$sel .= "$PHP_EOL" . groupSelectOptionView($res[$i]["id"], $title, $pref.$res[$i]["name"]);
			$res[$i] = array("item" => $res[$i], "children" => buildGroupList(	$arr, $res[$i]["id"], 
																				$mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableForFeatures, 
																				$pref."&nbsp;&nbsp;"));
			$sel .= $res[$i]["children"]["select_options"];
			$res[$i]["children"] = $res[$i]["children"]["list"];
		}
		return array("list" => $res, "select_options" => $sel);
	}
	function getSearchResult($mysqlConnectionLinkID,  $mysqlDBName, $mysqlDBTableForItems, $parent, $mysqlDBTableForFeatures = "", $filter = array())
	{
		$searchResult = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableForItems, "parent", $parent);
		$n = count($searchResult); //because some of the array items can be unsetted
		for($i = 0; $i < $n; $i++)
		{
			if($searchResult[$i]["id"] != $searchResult[$i]["parent"])
			{
				if($searchResult[$i]["type"] == "group")
				{
					$tmp = getSearchResult($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableForItems, $searchResult[$i]["id"], $mysqlDBTableForFeatures, $filter);
					if(count($tmp))
						$searchResult[$i] = $tmp;
					else
						unset($searchResult[$i]);
				}
				else	//type == "item"
				{
					if(is_array($filter) && count($filter))
					{
						//отримати з бази всі характеристики даної речі
						$itemFeatures = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableForFeatures, "id", $searchResult[$i]["id"]);
						if(!$itemFeatures["error"])
							$itemFeatures = $itemFeatures[0];
						//отримати з бази імя даної речі і помістити його в масив характеристик з ключом name - щоб не перевіряти його окремо
						$itemFeatures["name"] = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableForItems, "id", $searchResult[$i]["id"]);
						if(!$itemFeatures["name"]["error"])
							$itemFeatures["name"] = $itemFeatures["name"][0]["name"];
						foreach(array_keys($filter) as $filterKey)
						{
							if($filter[$filterKey]["data"])
							{
								$matches = 0;
								//find out in what way to filter this feature
								switch($filter[$filterKey]["type"])
								{
									case "attributes":
										if($itemFeatures[$filterKey])
										{
											//розділити всі атрибути елемента, що злиті через кому, в вектор
											$itemFeatures[$filterKey] = explode(',', $itemFeatures[$filterKey]);
											//перебрати всі атрибути елемента в векторі
											foreach($itemFeatures[$filterKey] as $ifa)
											{
												//перевірити чи є атрибут елемента в фільтрі атрибутів
												if( !( array_search($ifa, $filter[$filterKey]["data"]) === FALSE ) )
												{
													//++$matches;
													
													//якщо так тоді присвоїти кількості співпадінь - кількість елементів вектора даних
													//щоб даний елемент потрапив у вихідний рядок результатів
													$matches = count($filter[$filterKey]["data"]);
													break;
												}
											}
										}
										else
											$matches = count($filter[$filterKey]["data"]);
										break;
									case "numeric":
										if(!is_array($itemFeatures[$filterKey]))
											$itemFeatures[$filterKey] = explode(',', $itemFeatures[$filterKey]);
										foreach($itemFeatures[$filterKey] as $if)
										{
											$boundaries = explode('-', $if);
											$bc = count($boundaries);
											if($bc == 1)
											{
												if($filter[$filterKey]["data"] == $boundaries[0])
													++$matches;
											}
											elseif($bc == 2)
											{
												if(($filter[$filterKey]["data"] >= $boundaries[0]) && ($filter[$filterKey]["data"] <= $boundaries[1]))
													++$matches;
											}
										}
										break;
									case "size":
									default: //case "name":
										if(!(strpos(lowercase($itemFeatures[$filterKey]), lowercase($filter[$filterKey]["data"])) === FALSE))
											++$matches;
										break;
								}
								if($matches != count($filter[$filterKey]["data"]))
									unset($searchResult[$i]);
							}
						}
					}
				}
			}
			else
				unset($searchResult[$i]);
		}
		return $searchResult;
	}
	function simplify_array($arr, $urlBeginningForResults)
	{
		//is_array($arr) is needed to break loop circle
		while(is_array($arr) && (count($arr) == 1))
		{
			$arrk = array_keys($arr);
			$arr = $arr[$arrk[0]];
		}
		
		if(is_array($arr) && (count($arr) > 1))
		{
			foreach(array_keys($arr) as $i)
			{
				$arr[$i] = simplify_array($arr[$i], $urlBeginningForResults);
				$searchResultString = array_merge((array)$searchResultString, (array)$arr[$i][1]);
				$arr[$i] = $arr[$i][0];
			}
		}
		
		if(is_array($arr) && count($arr) && $arr["id"])
		{
			global $mainURL;
			//вихідний рядочок результатів
			$searchResultString[] = searchResultStringView($urlBeginningForResults, $arr["id"], $arr["name"], $mainURL . "engine/catalogue/thumbnails/" . ($arr["thumbnail"] ? $arr["thumbnail"] : "default.png"));
		}
		
		return array($arr, $searchResultString);
	}
//Functions -- End	 -=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-
/////////////////////-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-

	//Відкрити з'єдняння для подальших дій
	if(!$mysqlConnectionLinkID)
		$mysqlConnectionLinkID = openMySQLConnection($mysqlHostname, $mysqlUsername, $mysqlPassword);
		
	$allGroups = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueItems, "type", "group", "", "", "parent");
	
	for($i = 0, $n = count($allGroups); $i < $n; $i++)
		$allGroupIds[] = $allGroups[$i]["id"];
	
	
	$allGroups = buildGroupList($allGroups, 0, $mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueFeatures);

	$features = describeBDTable($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueFeatures);
	$featuresHead = $features["Field"];
	//дістати всі заголовки в таблиці features -- кінець
	//далі їх обробка...
	
	$urlBeginning = "?";
	if(count($_GET))
	{
		foreach(array_keys($_GET) as $gpak)
		{
			if( ($gpak != "catalogue") && ($gpak != "show") && ($gpak != "searchForm_groupList") && !array_search($gpak, $featuresHead))
				$urlBeginning .= $gpak . "=" . $_GET[$gpak] . "&";
		}
	}
	
	//get collection types' configurations
	if(!$collectionTypesConfigs)
		$collectionTypesConfigs = getCollectionTypesConfigurations($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueOptions);
	
	$featList = "";
	for($i = 1, $n = count($featuresHead); $i < $n; $i++)	//$i з 1 бо поля id виводити непотрібно
	{
		//вивід характеристик у вигляді звичайних полів вводу тексту
		if(!(($featuresHead[$i] == "attributes") || ($featuresHead[$i] == "images") || ($featuresHead[$i] == "collection_type")))
		{
			//визначення до яких колекцій належить дана характеристика і вписати її в class тегу p
			// - щоб потім можна було їх приховувати, при зміні типу колекції користувачем
			$attrCollections = "";
			foreach(array_keys($collectionTypesConfigs) as $akctc)
			{
				if(array_search($featuresHead[$i], $collectionTypesConfigs[$akctc]))
					$attrCollections .= str_replace(' ', '_', $akctc) . ' ';
			}
			if($attrCollections)
				$attrCollections = trim($attrCollections);

			$featList .= inputFeatureView($featuresHead[$i], $LOCAL[$featuresHead[$i]], "", $attrCollections . " el" . (($i+1)%2+1));
		}
	}
	
	//дістати всі можливі атрибути
	$attr = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueOptions, "", "", "", 1);
	$attr = explode(',', $attr[0]["attributes"]);
	$searchForm_addon = "";
	for($j = 0, $m = count($attr); $j < $m; $j++)
		$searchForm_addon .= attributeCheckBoxView("searchForm_attributes_$j", ucfirst($attr[$j]));
	
	$pathToThisFile = thisRealScriptName($_SERVER["SCRIPT_NAME"], $_SERVER["SCRIPT_FILENAME"], __FILE__);
	
	$search_output = "<script type=\"text/javascript\" src=\"" . $pathToThisFile[0].$pathToThisFile[1] . "jquery.imghover.js\" ></script>";
	$search_output .= searchFormView($pathToThisFile[0].$pathToThisFile[1] . "catalogue.functions.js",
									$urlBeginning,
									groupSelectView(groupSelectOptionView(0, "not_collection", "Всі категорії") . $allGroups["select_options"]),
									inputFeaturesBoxView($featList, "Облаштувати у:", $searchForm_addon));
	echo $search_output;

//--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--
//Here search starts
//--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--==--
	if($_GET["catalogue"] == "search")
	{
		$searchForm_groupListVal = $_GET["searchForm_groupList"];
		if(array_search($searchForm_groupListVal, $allGroupIds) === FALSE)
			$searchForm_groupListVal = 0;

		//перед пошуком треба правильно заповнити масив фільтрів - що йде останнім парметров в функцію пошуку
		$filter = array(
			"manufacturer" => array("type" => "text", "data" => $_GET["manufacturer"]),
			"name" => array("type" => "text", "data" => $_GET["collection"]),
			"size" => array("type" => "size", "data" => $_GET["size"]),
			"width" => array("type" => "numeric", "data" => $_GET["width"]),
			"thickness" => array("type" => "numeric", "data" => $_GET["thickness"]),
			"nap_height" => array("type" => "numeric", "data" => $_GET["nap_height"]),
			"nap_composition" => array("type" => "text", "data" => $_GET["nap_composition"]),
			"density" => array("type" => "numeric", "data" => $_GET["density"]),
			"protection_layer" => array("type" => "text", "data" => $_GET["protection_layer"]),
			"class" => array("type" => "text", "data" => $_GET["class"]),
			"pvc" => array("type" => "text", "data" => $_GET["pvc"]),
			"price" => array("type" => "numeric", "data" => $_GET["price"]),
			"attributes" => array("type" => "attributes", "data" => $_GET["attributes"]));

		$searchResult = getSearchResult($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueItems, $searchForm_groupListVal, 
										$mysqlDBTableToStoreCatalogueFeatures, $filter);

		//here data from outside is needed (address of catalogue page - url, not path to the file)
		$urlBeginningForResults = str_replace("page=".$_GET["page"], "page=catalogue", $urlBeginning);
		
		

		$searchResult = simplify_array($searchResult, $urlBeginningForResults);

		if($searchResult[1])
		{
			$searchFinalResult = "";
			$n = count($searchResult[1]);
			for($i = 0; $i < $n; $i++)
			{
				if(($i % 4) == 0)
					$searchFinalResult .= "<tr>";
				$searchFinalResult .= $searchResult[1][$i];
				if(($i % 4) == 3)
					$searchFinalResult .= "</tr>";
			}
			if($m = ($n % 4))
			{
				$m = 4 - $m;
				for($i = 0; $i < $m; $i++)
					$searchFinalResult .= "<td></td>";
				$searchFinalResult .= "</tr>";
			}
			echo searchResultStringsBox($searchFinalResult);
		}
		else
			echo "No result... :(";
	}
	
	
		
	if($mysqlConnectionLinkID)
	{
		mysql_close($mysqlConnectionLinkID);
		unset($mysqlConnectionLinkID);
	}
?>