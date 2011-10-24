<?php
	include_once("variables.php");
/**
 * Function returns resource with all tables in database by name *$db_name*
 */
function listDBTables($db_name, $mysqlConnectionLinkID)
{
	return mysql_query("SHOW TABLES FROM " . $db_name, $mysqlConnectionLinkID);
}
/**
 * Creates table by name *$table_name* in database by name *$db_name* 
 * $data_string - string of columns data !with brackets!
 */
function createTable($db_name, $table_name, $data_string, $mysqlConnectionLinkID)
{
	$db_name = mysql_real_escape_string($db_name);
	$table_name = mysql_real_escape_string($table_name);
	
	mysql_query("USE {$db_name}", $mysqlConnectionLinkID);
	$sql = "CREATE TABLE {$table_name} 
			{$data_string}
			TYPE MyISAM
			CHARACTER SET utf8
			COLLATE utf8_general_ci";
	
	if (mysql_query($sql, $mysqlConnectionLinkID))
	{
		return "Database created.";
	}
	else
	{
		global $debugMode;
		if($debugMode)
			return "Error creating database: " . mysql_error();
		return "Error creating database.";
	}
}


//#########################-=-=- Functions End. Code Beginning. -=-=-#########################\\
	
	
	@require("../..php");
	$sahcFromHTTP = $_GET["sahc"];
	if($sahcFromHTTP == "")
		$sahcFromHTTP = $_POST["sahc"];	//quaries can be in POST mode too
	$sahcFromHTTP = urldecode($sahcFromHTTP);
	$logFile = fopen("./backups/" . "AdminAJAX.log", 'a');
	if($logFile == FALSE)
		echo "Creating logFile error.";
	fwrite($logFile, "------------------------------------------------------------------------");
	fwrite($logFile, $PHP_EOL . "Date: " . date('Y.m.d H:i:s'));
	fwrite($logFile, $PHP_EOL . "sahcFromFile: " . $SAHC);
	fwrite($logFile, $PHP_EOL . "sahcFromHTTP: " . $sahcFromHTTP);
	if( isset($SAHC) && isset($sahcFromHTTP) && ($SAHC == $sahcFromHTTP))
	{
		$result = "";
		$todo = $_GET["todo"];	//$todo - findout what to do :)
		if($todo == "")
			$todo = $_POST["todo"];	//quaries can be in POST mode too
		fwrite($logFile, $PHP_EOL . "todo: " . $todo);
		if($todo)
		{			
			include_once("functions.php");
			$mysqlConnectionLinkID = openMySQLConnection($mysqlHostname, $mysqlUsername, $mysqlPassword);
			fwrite($logFile, $PHP_EOL . "--openMySQLConnection{$PHP_EOL}Hostname: " . $mysqlHostname . " Username: " . $mysqlUsername . " Password: " . $mysqlPassword);
			$mysqlDBName = mysql_real_escape_string($mysqlDBName);
			$mysqlDBTableToStorePages = mysql_real_escape_string($mysqlDBTableToStorePages);
			if($mysqlConnectionLinkID != FALSE)
			{
				if($todo == "initDB")
				{
					$tablestr = "(	name VARCHAR(64) UNIQUE NOT NULL,
									title TEXT,
									content LONGTEXT,
									keywords TEXT,
									description TEXT,
									pageOrder INT UNSIGNED,
									PRIMARY KEY (name)
								)";
					echo createTable($mysqlDBName, $mysqlDBTableToStorePages, $tablestr, $mysqlConnectionLinkID).$XHTML_EOL;
				}
				elseif($todo == "savePageData")
				{
					$name = mysql_real_escape_string(strip_tags(urldecode($_POST["page_name"])));
					$title = mysql_real_escape_string(strip_tags(urldecode($_POST["page_title"])));
					$content = mysql_real_escape_string(urldecode($_POST["page_content"]));
					$keywords = mysql_real_escape_string(strip_tags(urldecode($_POST["page_keywords"])));
					$description = urldecode($_POST["page_description"]);
					if($description == "")
						$description = trim(strip_tags(substr($content, 0, $dotPos = strpos($content, '.')===FALSE?$contentLen = strlen($content)>56?56:$contentLen:$dotPos)));
					$description = mysql_real_escape_string(strip_tags($description));
						
					if($name)
					{
						$backupFileName = $name . "." . date('Y.m.d.H.i.s') . ".saved.sqlinphp";
						if(mysql_query("USE {$mysqlDBName}", $mysqlConnectionLinkID))
						{
							$setData = "";	//data for SQL request that SETs data into table
							$setData .= "title = '{$title}'";
							$setData .= ", content = '{$content}'";
							$setData .= ", keywords = '{$keywords}'";
							$setData .= ", description = '{$description}' ";
							
							if($setData)
								$sql = "UPDATE {$mysqlDBTableToStorePages} SET " . $setData . "WHERE nameID = '{$name}'";
							
							$backupFile = fopen("./backups/" . $backupFileName, 'w');
							if($backupFile)
							{
								fwrite($logFile, $PHP_EOL . $backupFileName . ": File successfully created.");
								fwrite($backupFile, $sql);
								fwrite($logFile, $PHP_EOL . $backupFileName . ": SQL string written.");
							}
							fclose($backupFile);
							
							if(mysql_query($sql, $mysqlConnectionLinkID))
							{
								fwrite($logFile, $PHP_EOL . $backupFileName . ": Data successfully saved.");
								echo "Data successfully saved.";
							}
							else
							{
								fwrite($logFile, $PHP_EOL . $backupFileName . ": Error saving data. " . mysql_error());
								if($debugMode)
									echo "Error saving data.".mysql_error().$XHTML_EOL;
								else
									echo "Error saving data.".$XHTML_EOL;
							}
						}
						else
						{
							fwrite($logFile, $PHP_EOL . $backupFileName . ": Error using DB. " . mysql_error());
							if($debugMode)
								echo "Error using DB.".mysql_error().$XHTML_EOL;
							else
								echo "Error using DB.".$XHTML_EOL;
						}
					}
				}
				elseif($todo == "addPage")
				{
					if(mysql_query("USE {$mysqlDBName}", $mysqlConnectionLinkID))
					{
						$name = mysql_real_escape_string(strip_tags(urldecode($_POST["page_name"])));
						if($name)
						{
							$title = mysql_real_escape_string(strip_tags(urldecode($_POST["page_title"])));
							$content = mysql_real_escape_string(urldecode($_POST["page_content"]));
							$keywords = mysql_real_escape_string(strip_tags(urldecode($_POST["page_keywords"])));
							$description = urldecode($_POST["page_description"]);
							if($description == "")
								$description = trim(strip_tags(substr($content, 0, $dotPos = strpos($content, '.')===FALSE?$contentLen = strlen($content)>56?56:$contentLen:$dotPos)));
							$description = mysql_real_escape_string(strip_tags($description));
							$sql = "INSERT INTO {$mysqlDBTableToStorePages} (nameID, title, content, isModule, keywords, description) 
									VALUES ('{$name}', '{$title}', '{$content}', '0', '{$keywords}', '{$description}')";
							if(mysql_query($sql, $mysqlConnectionLinkID))
								echo "Data successfully added.";
							else
							{
								if($debugMode)
									echo "Error adding data.".mysql_error().$XHTML_EOL;
								else
									echo "Error adding data.".$XHTML_EOL;
							}
						}
					}
					else
						if($debugMode)
							echo "Error using DB.".mysql_error().$XHTML_EOL;
						else
							echo "Error using DB.".$XHTML_EOL;
				}
				elseif($todo == "removePage")
				{
					$name = mysql_real_escape_string(strip_tags(urldecode($_POST["page_name"])));					
					if($name)
					{
						if(mysql_query("USE {$mysqlDBName}", $mysqlConnectionLinkID))
						{
							$sql = "DELETE FROM {$mysqlDBTableToStorePages} WHERE name = '{$name}'";
							if(mysql_query($sql, $mysqlConnectionLinkID))
								echo "Data successfully removed.";
							else
							{
								if($debugMode)
									echo "Error removing data.".mysql_error().$XHTML_EOL;
								else
									echo "Error removing data.".$XHTML_EOL;
							}
						}
						else
							if($debugMode)
								echo "Error using DB.".mysql_error().$XHTML_EOL;
							else
								echo "Error using DB.".$XHTML_EOL;
					}
				}
			}
			mysql_close($mysqlConnectionLinkID);
			fwrite($logFile, $PHP_EOL . "--mysql_close{$PHP_EOL}mysqlConnectionLinkID: " . $mysqlConnectionLinkID);
		}
	}
	else
	{
		echo "Reload this page please, if does not help relogin." . $XHTML_EOL;
		if($debugMode)
		{
			echo "\$SAHCFromFile(з файлу):" . $SAHC . $XHTML_EOL;
			echo "\$sahcFromHTTP(з запиту):" . $sahcFromHTTP;
		}
	}
	fwrite($logFile, $PHP_EOL);
	fclose($logFile);
?>