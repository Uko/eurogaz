<?php
function get_structure()
{
	$server = "localhost";
	$user = "eurogaz";
	$pass = "W5yS.6gx";
	$db = "eurogaz";

	$mysqlConnectionLinkID = mysql_connect($server, $user, $pass);
	mysql_query("SET NAMES utf8", $mysqlConnectionLinkID);
	mysql_select_db($db);

	$result = mysql_query("SHOW TABLES FROM $db", $mysqlConnectionLinkID);
	if (!$result)
	{
		echo "DB Error, could not list tables\n";
		echo 'MySQL Error: ' . mysql_error();
		exit;
	}
	while ($td = mysql_fetch_row($result))
	{
		$table = $td[0];
		$r = mysql_query("SHOW CREATE TABLE `$table`");
		if ($r)
		{
			$insert_sql = "";
			$d = mysql_fetch_array($r);
			$d[1] .= ";";
			$sql[] = $d[1];
			$table_query = mysql_query("SELECT * FROM `$table`");
			$num_fields = mysql_num_fields($table_query);
			while ($fetch_row = mysql_fetch_array($table_query))
			{
				$insert_sql .= "INSERT INTO $table VALUES(";
				for ($n=1;$n<=$num_fields;$n++)
				{
					$m = $n - 1;
					$insert_sql .= "'".mysql_real_escape_string($fetch_row[$m])."', ";
				}
				$insert_sql = substr($insert_sql,0,-2);
				$insert_sql .= ");\n";
			}
			if ($insert_sql!= "")
			{
				$sql[] = $insert_sql;
			}
		}
	}
	return implode("\r", $sql);
}
$dumpFile = fopen("./dump.sql", 'w');
$sql = get_structure();
if($dumpFile)
{
	fwrite($dumpFile, $sql);
}
fclose($dumpFile);
echo "done";
?>
