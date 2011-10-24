<?php
	include_once("symbols.php");
	include_once("functions.MySQL.php");
	
function redirect($to, $code=301)
{
	$location = null;
	$sn = $_SERVER['SCRIPT_NAME'];
	$cp = dirname($sn);
	if (substr($to, 0, 4) == 'http')
		$location = $to; // Absolute URL
	else
	{
		$schema = $_SERVER['SERVER_PORT']=='443'?'https':'http';
		$host = strlen($_SERVER['HTTP_HOST'])?$_SERVER['HTTP_HOST']:$_SERVER['SERVER_NAME'];
		if (substr($to,0,1)=='/')
			$location = "$schema://$host$to";
		elseif (substr($to,0,1)=='.') // Relative Path
		{
			$location = "$schema://$host/";
			$pu = parse_url($to);
			$cd = dirname($_SERVER['SCRIPT_FILENAME']).'/';
			$np = realpath($cd.$pu['path']);
			$np = str_replace($_SERVER['DOCUMENT_ROOT'],'',$np);
			$location.= $np;
			if ((isset($pu['query'])) && (strlen($pu['query'])>0)) 
				$location.= '?'.$pu['query'];
		}
	}

	$hs = headers_sent();
	//echo $hs."!".$code;
	if ($hs == false)
	{
		if     ($code==301) header("Location: $location", TRUE, 301);//header("301 Moved Permanently HTTP/1.1"); // Convert to GET
		elseif ($code==302) {header("Location: $location", TRUE, 302); header("Location: $location");}//header("302 Found HTTP/1.1"); // Conform re-POST
		elseif ($code==303) header("Location: $location", TRUE, 303);//header("303 See Other HTTP/1.1"); // dont cache, always use GET
		elseif ($code==304) header("304 Not Modified HTTP/1.1"); // use cache
		elseif ($code==305) header("305 Use Proxy HTTP/1.1");
		elseif ($code==306) header("306 Not Used HTTP/1.1");
		elseif ($code==307) header("Location: $location", TRUE, 307);//header("307 Temporary Redirect HTTP/1.1");
		else 
			trigger_error("Unhandled redirect() HTTP Code: $code", E_USER_ERROR);
		
		if(($code == 304) || ($code == 305) || ($code == 306))
			header("Location: $location");
		header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
	}
	elseif (($hs==true) || ($code==302) || ($code==303))
	{
		// todo: draw some javascript to redirect
		$cover_div_style = 'background-color: #ccc; height: 100%; left: 0px; position: absolute; top: 0px; width: 100%;'; 
		echo "<div style='$cover_div_style'>\n";
		$link_div_style = 'background-color: #fff; border: 2px solid #f00; left: 0px; margin: 5px; padding: 3px; ';
		$link_div_style.= 'position: absolute; text-align: center; top: 0px; width: 95%; z-index: 99;';
		echo "<div style='$link_div_style'>\n";
		echo "<p>Please See: <a href='$to'>".htmlspecialchars($location)."</a></p>\n";
		echo "</div>\n</div>\n";
	}
	exit(0);
}
/**
 * Заповнення контенту
 */
function SetMainContent($name, $Dir)
{
	$file = fopen("./Lang/".$Dir."/".$name.".txt", "r");
	if ($file!=false)
	{
		while(!feof($file))
		{
			echo fgets($file);/*."<br />";*/
		}
	}
	else
	{
		echo "./Lang/".$Dir."/".$name.".txt";
	}
	fclose($file);
}
/**
 * Розбір контенту (Парсування контенту)
 */
function parseInputString($inputString, $tagList)
{	
	$phpVersion = phpversion();
	
	//array for storing parse result data
	$data = array();
	
	//parsing loop...
	foreach($tagList as $tlItem)
	{
		$tagStartLen = strlen("<*".$tlItem."*>");
		$tagEndLen = strlen("<*\\".$tlItem."*>");
		
		//position of startTag
		$tagStartPos = strpos($inputString, "<*".$tlItem."*>");
		
		//if true: there is no start tag in the string OR position == 0
		if($tagStartPos == FALSE)
		{
			//if true: position == 0
			if(!($tagStartPos === FALSE))
			{
				//position of endTag
				$tagEndPos = strpos($inputString, "<*/".$tlItem."*>");
				
				//if true: there is no endTag for this tag - throw exception
				if($tagEndPos === FALSE)
					if($phpVersion[0] >= 5)
						eval("throw new LogicException(\"Page content error. There is no end tag for \".\$tlItem)");
					else
						return array("error" => "Page content error. There is no end tag for ".$tlItem);
				
				
				//if($tagEndPos < $tagStartPos)
				//	throw new LogicException("Page content error. Incorrect sequence of ".$tlItem." tags(end/start).");
				
				//*uncommented* endTag search loop...
				while($inputString[$tagEndPos-1] == '\\')
				{
					
					//remove '\' from beginning of *commented* tags
					$tmpStr = substr($inputString, 0, $tagEndPos - 1);
					$tmpStr .= substr($inputString, $tagEndPos);
					$inputString = $tmpStr;
					//and decrease $tagEndPos
					$tagEndPos -= 1;
					
				
					$tmpPos = $tagEndPos + $tagEndLen;
					$tagEndPos = strpos(substr($inputString, $tagEndPos + $tagEndLen), "<*/".$tlItem."*>");
					
					//if true: there is no *uncommented* endTag for this tag - throw exception
					if($tagEndPos === FALSE)
						if($phpVersion[0] >= 5)
							eval("throw new LogicException(\"Page content error. There is no *uncommented* end tag for '\".\$tlItem.\"' tag.\")");
						else
							return array("error" => "Page content error. There is no *uncommented* end tag for '".$tlItem."' tag.");
					
					$tagEndPos += $tmpPos;
				}
			}
			//there is no such tag in the string
			else
			{
				//throw new LogicException("Page content error. There is no start tag for ".$tlItem);
				continue;
			}
		}
		//position of start tag > 0
		else
		{
			//*uncommented* startTag search loop...
			$isStartTag = true;
			while($inputString[$tagStartPos-1] == '\\')
			{
				/*
				//remove '\' from beginning of *commented* tags
				$tmpStr = substr($inputString, 0, $tagStartPos - 1);
				$tmpStr .= substr($inputString, $tagStartPos);
				$inputString = $tmpStr;
				//and decrease $tagStartPos
				$tagStartPos -= 1;
				*/
			
				$tmpPos = $tagStartPos + $tagStartLen;
				$tagStartPos = strpos(substr($inputString, $tagStartPos + $tagStartLen), "<*".$tlItem."*>");
				
				//if true: there is no *uncommented* startTag for this tag - throw exception
				if($tagStartPos === FALSE)
				{
					//throw new LogicException("Page content error. There is no *uncommented* start tag for ".$tlItem);
					$isStartTag = false;
					break;
				}
					
				$tagStartPos += $tmpPos;
			}
			//if there is no startTag continue to the next tag
			if(!$isStartTag)
				continue;			
			
			//search endTag after appearance of startTag in string
			$tagEndPos = strpos(substr($inputString, $tagStartPos + $tagStartLen), "<*/".$tlItem."*>");
			
			//if true: there is no endTag for this tag - throw exception
			if($tagEndPos === FALSE)
				if($phpVersion[0] >= 5)
					eval("throw new LogicException(\"Page content error. There is no end tag for \".\$tlItem)");
				else
					return array("error" => "Page content error. There is no end tag for ".$tlItem);
				
			$tagEndPos += $tagStartPos + $tagStartLen;
			
			//*uncommented* endTag search loop...
			while($inputString[$tagEndPos-1] == '\\')
			{
				
				//remove '\' from beginning of *commented* tags
				$tmpStr = substr($inputString, 0, $tagEndPos - 1);
				$tmpStr .= substr($inputString, $tagEndPos);
				$inputString = $tmpStr;
				//and decrease $tagEndPos
				$tagEndPos -= 1;
				
			
				$tmpPos = $tagEndPos + $tagEndLen;
				$tagEndPos = strpos(substr($inputString, $tagEndPos + $tagEndLen), "<*/".$tlItem."*>");
				
				//if true: there is no *uncommented* endTag for this tag - throw exception
				if($tagEndPos === FALSE)
					if($phpVersion[0] >= 5)
						eval("throw new LogicException(\"Page content error. There is no *uncommented* end tag for \".\$tlItem)");
					else
						return array("error" => "Page content error. There is no *uncommented* end tag for ".$tlItem);
				
				$tagEndPos += $tmpPos;
			}
		}
		
		//copy data to resulting array
		$data[$tlItem] = substr($inputString, $tagStartPos + $tagStartLen, $tagEndPos - ($tagStartPos + $tagStartLen));
		
		//cut copied data from string AND *used* tags
		$tmpStr = substr($inputString, 0, $tagStartPos);
		$tmpStr .= substr($inputString, $tagEndPos + $tagEndLen);
		$inputString = $tmpStr;
	}
	
	//add remained string to the end od resulting array
	$data["remained"] = $inputString;
	
	return $data;
}
/**
 *	return array with page data ($pageParseTags==>....., remained==>..., error==>...)
 */
function getDataFromFile($fileName, $pageParseTags)
{
	//read entire file content into string variable AND parse it
	$fileContents = @file_get_contents($fileName);
	
	//Turn on output buffering
	ob_start();
	
	//process $fileContents with PHP interpreter and place it to the output
	eval("?>" . $fileContents);
	
	//assign the contents of the output buffer to the $fileContents
	$fileContents = ob_get_contents();
	
	//Clean (erase) the output buffer and turn off output buffering
	ob_end_clean();
	
	//--==--==--==--==--==--==--==--==--==--==--
	//Parsing Content...
	//--==--==--==--==--==--==--==--==--==--==--
	return parseInputString($fileContents, $pageParseTags);
}
/**
 *	return scripts real filename (works on included scripts)
 */
function thisRealScriptName($SCRIPT_NAME = "", $SCRIPT_FILENAME = "", $FILE = __FILE__)
{
	if(!$SCRIPT_NAME)
		$SCRIPT_NAME = $_SERVER["SCRIPT_NAME"];
	if(!$SCRIPT_FILENAME)
		$SCRIPT_FILENAME = $_SERVER["SCRIPT_FILENAME"];
	//імя файла скрипта який виконується - найглибший в стеку підключення (include)
	$scriptBaseName = basename($SCRIPT_NAME);
	//дорога до вищезгаданого файлика скріпта
	$scriptPath = substr($SCRIPT_FILENAME, 0, strlen($SCRIPT_FILENAME) - strlen($scriptBaseName));
	//дорога до цього файлика (в якому зараз читаєте коменти)
	$thisFilePath = str_replace(DIRECTORY_SEPARATOR, "/", $FILE);
	//імя цього фалика
	$thisFileBaseName = basename($thisFilePath);
	$thisFilePath = substr($thisFilePath, 0 , strlen($thisFilePath) - strlen($thisFileBaseName));
	//відноснйи шлях цього файлика від виконуваного скрипта
	$fromScriptToThisFilePath = substr($thisFilePath, strlen($scriptPath), strlen($thisFilePath) - strlen($scriptPath));
	$fromRootToScriptPath = substr($SCRIPT_NAME, 0, strlen($SCRIPT_NAME) - strlen($scriptBaseName));
	
	return Array($fromRootToScriptPath, $fromScriptToThisFilePath, $thisFileBaseName);
}
?>