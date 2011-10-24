<?php
/**
 *	Function that creates connection to MySQL server and returns connection ID
 */
function openMySQLConnection($mysqlHostname, $mysqlUsername, $mysqlPassword)
{
	$mysqlConnectionLinkID = mysql_connect($mysqlHostname, $mysqlUsername, $mysqlPassword);
	mysql_query("SET NAMES utf8", $mysqlConnectionLinkID);
	
	return $mysqlConnectionLinkID;
}
/**
 *	returns an array with page data (id==>..., name==>..., $columnList==>....., error==>...)
 */
function getDataFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTable, $dataField = "", $dataValue = "", $columnList = "", $limit = "", $orderColumn = "")
{
	$data = array();
	global $debugMode;
	if($mysqlConnectionLinkID != FALSE)
	{
		$mysqlDBName = mysql_real_escape_string($mysqlDBName);
		
		if(mysql_query("USE $mysqlDBName", $mysqlConnectionLinkID))	//select db to use
		{
			$sql = "SELECT * FROM " . mysql_real_escape_string($mysqlDBTable);
			if($dataField)
				$sql .= " WHERE " . mysql_real_escape_string($dataField) . "='" . mysql_real_escape_string($dataValue) . "'";
			if($limit)
				$sql .= " LIMIT " . mysql_real_escape_string($limit);
			if($orderColumn)
				$sql .= " ORDER BY " . mysql_real_escape_string($orderColumn);
			
			if($result = mysql_query($sql, $mysqlConnectionLinkID))
			{
				$i = 0;
				while($row = mysql_fetch_assoc($result))
				{
					if($columnList)
						foreach($columnList as $cl)
						{
							//$row[$cl] = stripslashes($row[$cl]);
							$data[$i][$cl] = stripslashes($row[$cl]);
						}
					else
					{
						foreach(array_keys($row) as $rowAK)
							$data[$i][$rowAK] = stripslashes($row[$rowAK]);
					}
					$i++;
				}
			}
			else
			{
				if($debugMode)
					$data["error"] .= "getDataFromDB: ";
				$data["error"] .= "Error getting row from table. ";
				if($debugMode)
					$data["error"] .= mysql_error() . "|\$sql:" . $sql;
				$data["error"] .= $XHTML_EOL;
			}
		}
		else
		{
			if($debugMode)
				$data["error"] .= "getDataFromDB: ";
			$data["error"] .= "Error using DB.";
			if($debugMode)
				$data["error"] .= mysql_error() . "|\$sql:" . $sql;
			$data["error"] .= $XHTML_EOL;
		}
	}
	return $data;
}
function getDBColumnIntoList($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTable, $column, $orderBy = "")
{
	$data = array();
	global $debugMode;
	if($mysqlConnectionLinkID != FALSE)
	{
		$mysqlDBName = mysql_real_escape_string($mysqlDBName);
		
		if(mysql_query("USE {$mysqlDBName}", $mysqlConnectionLinkID))	//select db to use
		{
			$sql = "SELECT " . mysql_real_escape_string($column) . " FROM " . mysql_real_escape_string($mysqlDBTable);
			if($orderBy)
				$sql .= " ORDER BY " . mysql_real_escape_string($orderBy);

			if($result = mysql_query($sql, $mysqlConnectionLinkID))
			{
				$i = 0;
				while($row = mysql_fetch_row($result))
					$data[$i++] = stripslashes($row[0]);
			}
			else
			{
				if($debugMode)
					$data["error"] .= "getDBColumnIntoList: ";
				$data["error"] .= "Error getting row from table. ";
				if($debugMode)
					$data["error"] .= mysql_error() . $PHP_EOL . "sql:" . $sql;
				$data["error"] .= $XHTML_EOL;
			}
		}
		else
		{
			if($debugMode)
				$data["error"] .= "getDBColumnIntoList: ";
			$data["error"] .= "Error using DB.";
			if($debugMode)
				$data["error"] .= mysql_error();
			$data["error"] .= $XHTML_EOL;
		}
	}
	return $data;
}
function removeItemFromDB($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTable, $dataField, $dataValue)
{
	global $debugMode;
	if(($mysqlConnectionLinkID != FALSE) && ($dataField))
	{
		$mysqlDBName = mysql_real_escape_string($mysqlDBName);
		
		if(mysql_query("USE {$mysqlDBName}", $mysqlConnectionLinkID))
		{
			$mysqlDBTable = mysql_real_escape_string($mysqlDBTable);
			$dataField = mysql_real_escape_string($dataField);
			$dataValue = mysql_real_escape_string($dataValue);
			
			$sql = "DELETE FROM {$mysqlDBTable} WHERE $dataField = '{$dataValue}'";
		
			if(mysql_query($sql, $mysqlConnectionLinkID))
				$out = "Data successfully removed.";
			else
			{
				if($debugMode)
					$out = "Error removing data.".mysql_error().$XHTML_EOL;
				else
					$out = "Error removing data.".$XHTML_EOL;
			}
		}
		else
		{
			if($debugMode)
				$out .= "removeItemFromDB: ";
			$out .= "Error using DB.";
			if($debugMode)
				$out .= mysql_error();
			$out .= $XHTML_EOL;
		}
	}
	return $out;
}
function describeBDTable($mysqlConnectionLinkID, $mysqlDBName, $mysqlDBTable)
{
	global $debugMode;
	if($mysqlConnectionLinkID != FALSE)
	{
		$sql = "DESCRIBE " . mysql_real_escape_string($mysqlDBTable);
		if($result = mysql_query($sql, $mysqlConnectionLinkID))
		{
			while($row = mysql_fetch_assoc($result))
				foreach(array_keys($row) as $r)
					$out[$r][] = $row[$r];
		}
		else
		{
			if($debugMode)
				$out .= "describeBDTable: ";
			$out .= "Error describing DB table.";
			if($debugMode)
				$out .= mysql_error();
			$out .= $XHTML_EOL;
		}
	}
	return $out;
}
?>