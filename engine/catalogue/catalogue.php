<?php
	include_once("engine/catalogue.templates.php");
	include_once("engine/catalogue.variables.php");
	include_once "engine/catalogue.functions.php";
	
	
	//Відкрити з'єдняння для подальших дій
	if(!$mysqlConnectionLinkID)
		$mysqlConnectionLinkID = openMySQLConnection($mysqlHostname, $mysqlUsername, $mysqlPassword);
	
	$allCategories = getDBColumnIntoList($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueItems, "id");

	$thisScriptName = thisRealScriptName($_SERVER["SCRIPT_NAME"], $_SERVER["SCRIPT_FILENAME"], __FILE__);
	$thisScriptName = $thisScriptName[0].$thisScriptName[1].$thisScriptName[2];
	$pathToThisScript = substr($thisScriptName, 0, strlen($thisScriptName) - strlen(basename($thisScriptName)));
	$pathToThumbnails = $pathToThisScript . "thumbnails/";
	$pathToImages = $pathToThisScript . "images/";
	$pathToRealSizeImages = $pathToThisScript . "realSizeImages/";
	
	$urlBeginning = "?";
	if(count($_GET))
	{
		foreach(array_keys($_GET) as $gpak)
		{
			if( ($gpak != "catalogue") && ($gpak != "show") )
				$urlBeginning .= $gpak . "=" . $_GET[$gpak] . "&";
		}
	}
	
	/*
	 * id елемента що треба показати
	 */
	$itemToShow = $_GET["show"];
	/*
	 *	завантаження з бази рядка з інформацією яку потрібно показати
	 */
	$itemData = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueItems, "id", $itemToShow);
	$itemData = $itemData[0];
	$breadcrumbIdStack = array(array($itemData["id"]));
	$i = 0;
	if((array_search($itemToShow, $allCategories) === FALSE) || ($itemToShow == 0))
	{
		$itemData["id"] = 0;
		$breadcrumbs = "";
		$itemData["name"] = $LOCAL["first_breadcrumb"];
	}
	else
	{
		$breadcrumbIdStack[$i][] = $itemData["name"];
		$breadcrumbs .= $itemData["name"];
		$breadcrumb = $itemData["parent"];
		$breadcrumbIdStack[++$i] = array($breadcrumb);
		
		while($breadcrumb != 0)
		{
			$breadcrumb = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueItems, "id", $breadcrumb);
			$breadcrumb = $breadcrumb[0];
			$breadcrumbIdStack[$i][] = $breadcrumb["name"];
			$breadcrumbIdStack[++$i] = array($breadcrumb["parent"]);
			$breadcrumbs = breadcrumbsView($breadcrumb["id"], $breadcrumb["name"], $urlBeginning) . $breadcrumbs;
			$breadcrumb = $breadcrumb["parent"];
		}
	}
	$breadcrumbs = breadcrumbsView("", $LOCAL["first_breadcrumb"], $urlBeginning) . $breadcrumbs;
	$breadcrumbIdStackAmount = count($breadcrumbIdStack);
	/*
	 *	How deep we have to dig (main group [or id==0] included).
	 */
	$level = 2;
	/*
	 *	Check for deepness of choosen item/group
	 */
	$level = (($level > $breadcrumbIdStackAmount) ? $breadcrumbIdStackAmount : $level);
	/*
	 *	Start to dig up from there.
	 */
	$catalogueTrackMiniMap = array();
	for($i = $breadcrumbIdStackAmount - $level; $i < $breadcrumbIdStackAmount; $i++)
	{
		$childrenList = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueItems, "parent", $breadcrumbIdStack[$i][0]);
		if(count($childrenList))
		{
			$catalogueTrackMiniMap[$breadcrumbIdStack[$i][0]] = array("name" => $breadcrumbIdStack[$i][1]);
			foreach($childrenList as $clValue)
			{
				if($clValue["id"] != $breadcrumbIdStack[$i][0])
					$catalogueTrackMiniMap[$breadcrumbIdStack[$i][0]][$clValue["id"]] = $clValue["name"];
			}
			/**
			 *	Exclusively for teplocom-m.ru type of catalogue
			 */
			if(($level < $breadcrumbIdStackAmount) && ($i == $breadcrumbIdStackAmount - $level))
				$catalogueTrackMiniMap[$breadcrumbIdStack[$i][0]][$breadcrumbIdStack[$i-1][0]] = $breadcrumbIdStack[$i-1][1];
			/**
			 *	Recursively add previous level of the track.
			 */
			if($i > $breadcrumbIdStackAmount - $level)
			{
				$catalogueTrackMiniMap[$breadcrumbIdStack[$i][0]][$breadcrumbIdStack[$i-1][0]] = $catalogueTrackMiniMap[$breadcrumbIdStack[$i-1][0]];
				unset($catalogueTrackMiniMap[$breadcrumbIdStack[$i-1][0]]);
			}
		}
		else
		{
			$catalogueTrackMiniMap[$breadcrumbIdStack[$i][0]] = array("name" => $breadcrumbIdStack[$i][1]);
		}
	}
	$catalogueTrackMiniMap = $catalogueTrackMiniMap[0];
	unset($catalogueTrackMiniMap["name"]);
	$catalogueTrackMiniMapHTML = recursiveCatalogueTrackToHTML($catalogueTrackMiniMap, $breadcrumbIdStack, $urlBeginning);
	
	//page headers addition
	if ($keywordsLocal)
		$keywordsLocal .= ", ";

	if($itemData["type"] == "group")
	{
		$counter = $itemsPerRowInTheGroupView;
		$itemsFromDB = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueItems, "parent", $itemData["id"]);
		$groupView = false;
		foreach($itemsFromDB as $ifdbValue)
		{
			if($ifdbValue["type"] == "group")
			{
				$groupView = true;
				break;
			}
		}
		if($groupView)
		{
			$items = "<table><tr>";
		}
		else
		{
			$items = "<table class=\"onlyItems\">";
		}
		for($i = 0, $n = count($itemsFromDB); $i < $n; $i++)
		{
			$itemsFromDB[$i]["thumbnail"] = ($itemsFromDB[$i]["thumbnail"]) ? $itemsFromDB[$i]["thumbnail"] : "default.png";
			if($itemsFromDB[$i]["id"] != 0)
			{
				if($itemsFromDB[$i]["name"])
				{
					if($i < $n-1)
						$keywordsLocal .= $itemsFromDB[$i]["name"] . ", ";
					else
						$keywordsLocal .= $itemsFromDB[$i]["name"];
				}
				$itemFromDBChildren = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueItems, "parent", $itemsFromDB[$i]["id"]);
				if($groupView)
				{
					$items .= "<td>".itemThumbnailView( $pathToThumbnails . $itemsFromDB[$i]["thumbnail"], $itemsFromDB[$i]["id"], $itemsFromDB[$i]["name"], $itemFromDBChildren, $urlBeginning)."</td>";
					if((--$counter) == 0)
					{
						$items .= "</tr><tr>";
						$counter = $itemsPerRowInTheGroupView;
					}
				}
				else
				{
					$itemFeatures = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueFeatures, "id", $itemsFromDB[$i]["id"]);
					$itemFeatures = $itemFeatures[0];
					$items .= "<tr>" . itemThumbnailInListView( $pathToThumbnails . $itemsFromDB[$i]["thumbnail"], $itemsFromDB[$i]["id"], $itemsFromDB[$i]["name"], $itemFeatures["price"], $urlBeginning) . "</tr>";
				}
			}
		}
		if($groupView)
		{
			for($i = 0; $i < $counter; $i++)
				$items .= "<td></td>";
			$items .= "</tr></table>";
		}
		else
		{
			$items .= "</table>";
		}		
		$viewContainer = groupView($items, $itemData["description"]);
	}
	elseif($itemData["type"] == "item")
	{
		//get parent features
		$parentFeatures = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueFeatures, "id", $itemData["parent"]);
		$parentFeatures = $parentFeatures[0];
		//get item features
		$features = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueFeatures, "id", $itemData["id"]);
		$features = $features[0];
		
		//get collection types' configurations
		if(!$collectionTypesConfigs)
			$collectionTypesConfigs = getCollectionTypesConfigurations($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableToStoreCatalogueOptions);
		
		//findout what type of collection is this item/group
		//$fak will get item features from that collection
		$i = 0;
		if($parentFeatures["collection_type"])
			$fak = $collectionTypesConfigs[$parentFeatures["collection_type"]];
		elseif($features["collection_type"])
			$fak = $collectionTypesConfigs[$features["collection_type"]];
		else
		{
			$fak = array_keys($features);
			$i = 3;
		}
		//це для того щоб порядок встановлювався в налаштуваннях...
		$features["name"] = $itemData["name"];
		
		for($n = count($fak); $i < $n; $i++)
		{
			//parent features inheritance
			//if(($parentFeatures[$fak[$i]]) && !($features[$fak[$i]]))
			//{
			//	$features[$fak[$i]] = $parentFeatures[$fak[$i]];
			//}
			//+ at place preparing attributes for output
			if(($fak[$i] == "attributes") && ($features["attributes"]))
			{
				$keywordsLocal .= $features["attributes"];
				$features["attributes"] = explode(',', $features["attributes"]);
				for($j = 0, $m = count($features["attributes"]); $j < $m; $j++)
				{
					//here data from outside is needed (address of search page - url, not path to the file)
					$features["attributes"][$j] = 	"<a href=\"?page=search&catalogue=search&attributes[]="
												. urlencode($features["attributes"][$j]) . "#searchResults\">" . ucfirst($features["attributes"][$j]) . "</a>";
				}
				$features["attributes"] = implode(", ", $features["attributes"]);
			}
			
			//+ preparing features view
			if($features[$fak[$i]])
			{
				$featuresView .= itemFeatureTemplate($LOCAL[$fak[$i]], $features[$fak[$i]]);
				$descriptionLocal .= $LOCAL[$fak[$i]] . ":" . strip_tags($features[$fak[$i]]) . ".";
			}	
		}
		
		//check whether there is some images for this item in the db
		if($features["images"])
		{
			$images = explode("\\\\ ", $features["images"]);
			$imList = "";
			$i = 0;
			foreach($images as $im)
				$imList .= itemViewImage($itemData["name"], $pathToImages.$im, $pathToRealSizeImages.$im, ++$i);
		}
		//provide default thumbnail
		else
		{
			$imList = itemViewImage($itemData["name"], $pathToThumbnails."default.png", "", $i);
		}
		
		$viewContainer = itemView($itemData["id"], $itemData["name"], $imList, $featuresView, $itemData["description"]);
	}
	//page head addition
	$titleLocal = $LOCAL["first_breadcrumb"];
	if($LOCAL["first_breadcrumb"] != $itemData["name"])
		$titleLocal .= " - " . $itemData["name"];
	$descriptionLocal = str_replace("\r", "", str_replace("\n", " ", strip_tags($itemData["description"]))) . $descriptionLocal;

	$viewContainer = "
		<p id=\"breadcrumbs\">
			{$breadcrumbs}
		</p>
		<div id=\"catContainer\">
			" . $catalogueTrackMiniMapHTML . $viewContainer . "
		</div>";
	
	include_once("engine/adminpanel.php");
	
	//close connection all things with mysql are done
	if($mysqlConnectionLinkID)
	{
		mysql_close($mysqlConnectionLinkID);
		unset($mysqlConnectionLinkID);
	}
	echo catalogueView($viewContainer, $pathToThisScript, $urlBeginning);
?>
