<?php
function addItemIntoDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableForTheItem, 
		$itemType, $itemParent, $itemName, $itemThumbnail, $itemDescription,
		$mysqlDBTableForTheFeatures = "", $itemFeatures = "")
{
	$out = "";
	global $debugMode;
	if($mysqlConnectionLinkID != FALSE)
	{
		$mysqlDBName = mysql_real_escape_string($mysqlDBName);
		
		if(mysql_query("USE $mysqlDBName", $mysqlConnectionLinkID))	//select db to use
		{
			$mysqlDBTableForTheItem = mysql_real_escape_string($mysqlDBTableForTheItem);
			
			$itemType = mysql_real_escape_string($itemType);
			$itemParent = mysql_real_escape_string($itemParent);
			$itemName = mysql_real_escape_string($itemName);
			$itemThumbnail = mysql_real_escape_string($itemThumbnail);
			$itemDescription = mysql_real_escape_string($itemDescription);
			//echo "after:".$itemThumbnail;
			
			$sql = "INSERT INTO {$mysqlDBTableForTheItem} (id, type, parent, name, thumbnail, description)
					VALUES ('', '{$itemType}', '{$itemParent}', '{$itemName}', '{$itemThumbnail}', '{$itemDescription}')";
			if(mysql_query($sql, $mysqlConnectionLinkID))
				$out = "Data successfully added.";
			else
			{
				if($debugMode)
					$out = "Error adding data.".mysql_error().$XHTML_EOL;
				else
					$out = "Error adding data.".$XHTML_EOL;
			}
			
			if(is_array($itemFeatures) && count($itemFeatures))
			{
				$mysqlDBTableForTheFeatures = mysql_real_escape_string($mysqlDBTableForTheFeatures);
				$ifak = array_keys($itemFeatures);
				$sql = "INSERT INTO {$mysqlDBTableForTheFeatures} (id";
				for($i = 0, $n = count($ifak); $i < $n; $i++)
				{
					$ifak[$i] =  mysql_real_escape_string($ifak[$i]);
					$sql .= ", " . $ifak[$i];
				}
				//тут треба додати додаткові парметри на зчитування або зчитувани останню айдішку - шо б було найлогічніше,
				//або перед тим додати пошук на пропущену айдішку - записувати в неї і тоді вона вже відома буде тут
				$id = max(getDBColumnIntoList($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableForTheItem, "id"));
				$sql .= ") VALUES ('$id'";
				for($i = 0, $n = count($ifak); $i < $n; $i++)
				{
					//if($ifak[$i] != "attributes")
						$itemFeatures[$ifak[$i]] =  mysql_real_escape_string($itemFeatures[$ifak[$i]]);
					//else
					//	$itemFeatures[$ifak[$i]] = implode(',', $itemFeatures[$ifak[$i]]);
					$sql .= ", '" . $itemFeatures[$ifak[$i]] . '\'';
				}
				$sql .= ')';
				if(mysql_query($sql, $mysqlConnectionLinkID))
					$out = "Data successfully added.";
				else
				{
					if($debugMode)
						$out = "Error adding data.".mysql_error().$XHTML_EOL;
					else
						$out = "Error adding data.".$XHTML_EOL;
				}
			}
		}
		else
		{
			if($debugMode)
				$out .= "addItemIntoDB: ";
			$out .= "Error using DB.";
			if($debugMode)
				$out .= mysql_error();
			$out .= $XHTML_EOL;
		}
	}
	return $out;
}
function saveItemInTheDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableForTheItem,
		$itemID, $itemName, $itemThumbnail, $itemDescription,
		$mysqlDBTableForTheFeatures = "", $itemFeatures = "")
{
	$out = "";
	global $debugMode;
	if($mysqlConnectionLinkID != FALSE)
	{
		$mysqlDBName = mysql_real_escape_string($mysqlDBName);

		if(mysql_query("USE $mysqlDBName", $mysqlConnectionLinkID))	//select db to use
		{
			$mysqlDBTableForTheItem = mysql_real_escape_string($mysqlDBTableForTheItem);

			$itemID = mysql_real_escape_string($itemID);
			$itemName = mysql_real_escape_string($itemName);
			$itemThumbnail = mysql_real_escape_string($itemThumbnail);
			$itemDescription = mysql_real_escape_string($itemDescription);

			$sql = "UPDATE {$mysqlDBTableForTheItem}
					SET name='{$itemName}', thumbnail='{$itemThumbnail}', description='{$itemDescription}'
					WHERE id='{$itemID}'";
			if(mysql_query($sql, $mysqlConnectionLinkID))
				$out = "Data successfully added.";
			else
			{
				if($debugMode)
					$out = "Error adding data.".mysql_error().$XHTML_EOL;
				else
					$out = "Error adding data.".$XHTML_EOL;
			}

			if(is_array($itemFeatures) && count($itemFeatures))
			{
				$mysqlDBTableForTheFeatures = mysql_real_escape_string($mysqlDBTableForTheFeatures);
				$ifak = array_keys($itemFeatures);
				$sql = "UPDATE {$mysqlDBTableForTheFeatures}
						SET ";
				for($i = 0, $n = count($ifak); $i < $n; $i++)
				{
					$ifak[$i] =  mysql_real_escape_string($ifak[$i]);
					$itemFeatures[$ifak[$i]] =  mysql_real_escape_string($itemFeatures[$ifak[$i]]);
					$sql .= "{$ifak[$i]}='{$itemFeatures[$ifak[$i]]}',";
				}
				$sql = substr($sql, 0, strlen($sql)-1);	//cut the last comma

				$sql .= "WHERE id='{$itemID}'";
				if(mysql_query($sql, $mysqlConnectionLinkID))
					$out = "Data successfully added.";
				else
				{
					if($debugMode)
						$out = "Error adding data.".mysql_error().$XHTML_EOL;
					else
						$out = "Error adding data.".$XHTML_EOL;
				}
			}
		}
		else
		{
			if($debugMode)
				$out .= "saveItemInTheDB: ";
			$out .= "Error using DB.";
			if($debugMode)
				$out .= mysql_error();
			$out .= $XHTML_EOL;
		}
	}
	return $out;
}
function removeGroupFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableForTheItem, $mysqlDBTableForTheFeatures, $parentId, $pathToCatalogue)
{
	$childs = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableForTheItem, "parent", $parentId);
	foreach($childs as $child)
	{
		$features = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableForTheFeatures, "id", $child["id"]);
		$features = $features[0];
		if($child["thumbnail"])
			unlink($pathToCatalogue . "thumbnails/" . $child["thumbnail"]);
	
		removeItemFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableForTheFeatures, "id", $child["id"]);
		if($child["type"] == "item")
		{
			if($features["images"])
			{
				$imagelist = explode("\\\\ ", $features["images"]);
				foreach($imagelist as $iml)
				{
					unlink($pathToCatalogue . "realSizeImages/" . $iml);
					unlink($pathToCatalogue . "images/" . $iml);
				}
			}
			removeItemFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableForTheItem, "id", $child["id"]);
		}
		elseif($child["type"] == "group")
			removeGroupFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableForTheItem, $mysqlDBTableForTheFeatures, $child["id"]);
	}
	return removeItemFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTableForTheItem, "id", $parentId);
}
/**
 *	@return: array(	*type* => array(0 => *feature*, 1 => *feature*, ...), 
					*type* => ...)
 */
function getCollectionTypesConfigurations($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTable)
{
	//get collection types' configurations
	$collectionTypesConfigs = getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTable);
	$collectionTypesConfigs = $collectionTypesConfigs[0]["collection_types"];
	$collectionTypesConfigs = explode(';', $collectionTypesConfigs);
	for($n = count($collectionTypesConfigs), $i = 0; $i < $n; $i++)
	{
		$collectionTypesConfigs[$i] = explode(':', $collectionTypesConfigs[$i]);
		$collectionTypesConfigs[$collectionTypesConfigs[$i][0]] = explode(',', $collectionTypesConfigs[$i][1]);
		unset($collectionTypesConfigs[$i]);
	}
	return $collectionTypesConfigs;
}

/**
 *	Multi dimensional array search for $needle presence.
 */
function md_array_search($needle, $haystack)
{
	if (empty($needle) || empty($haystack))
	{
		return false;
	}

	foreach ($haystack as $key => $value)
	{
		if(is_array($value))
		{
			if(md_array_search($needle, $value))
				return true;
		}
		elseif ($value == $needle)
		{
			return true;
		}
	}
	return false;
}
/**
 *	Compilation of Catalogue Track to HTML output string.
 */
function recursiveCatalogueTrackToHTML($trap, $breadcrumbs, $urlBeginning = "", $deep = 0)
{
	if($deep == 0)
		$resultString = "<ul id=\"catalogueTrack2lvlMap\">";
	else
		$resultString = "<ul>";
	foreach($trap as $tKey => $tValue)
	{
		if(md_array_search($tKey, $breadcrumbs))
			$class = " class=\"checked\"";
		else
			unset($class);
		if(is_array($tValue))
		{
			$resultString .= "<li{$class}>";
			$resultString .= "<a href=\"{$urlBeginning}show={$tKey}\">{$tValue["name"]}</a>";
			$resultString .= recursiveCatalogueTrackToHTML($tValue, $breadcrumbs, $urlBeginning, $deep+1);
			$resultString .= "</li>";
		}
		elseif($tKey != "name")
		{
			$resultString .= "<li{$class}>";
			$resultString .= "<a href=\"{$urlBeginning}show={$tKey}\">{$tValue}</a>";
			$resultString .= "</li>";
		}
	}
	$resultString .= "</ul>";
	return $resultString;
}

?>