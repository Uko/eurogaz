<?php
	include_once 'symbols.php';

/**
 * works for latin filenames, fro cyrillic needs to be tested. I think it'll fail ^_^
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
//--==`--==`--==`--==`--==`--==`--==`--==`--==`--==`--==`--==`--==`--==`--==`--==`--==`--==`--==`--==`--==`--==`--==`--==`--==`
	
	$pathToThisFile = thisRealScriptName($_SERVER["SCRIPT_NAME"], $_SERVER["SCRIPT_FILENAME"], __FILE__);

	// iframe creation, just an empty page
	if(isset($_GET['AjaxUploadFrame']))
		exit;

	// populate in a fast and completely unobtrusive way the super global
	// $_FILES variable if the browser sent file via Ajax and without boundary
	// to use only if you want to support Safari 4 beta
	require "noswfupload".DIRECTORY_SEPARATOR."noswfupload.\$_FILES.simulation.php";

	//echo $HTML_EOL."\$_GET";	print_r($_GET);
	//echo $HTML_EOL."\$_POST";	print_r($_POST);
	//echo $HTML_EOL."\$_FILES"; print_r($_FILES);
	//echo $HTML_EOL."Raw data" . file_get_contents( 'php://input' );
	$todo = isset($_REQUEST["todo"]) ? $_REQUEST["todo"] : "";
	//echo $HTML_EOL."\$todo:$todo";
	switch($todo)
	{
		default:
			break;
		case "showUploader":
			@require("..php");
			$sahcFromThere = urldecode(isset($_REQUEST["sahc"]) ? $_REQUEST["sahc"] : "");	//quaries can be in GET and POST mode
			if(isset($SAHC) && isset($sahcFromThere) && ($SAHC == $sahcFromThere))
			{
				include_once("uploader.templates.php");
				$filesizeToBytes = array('k' => 1024, 'm' => 1024*1024, 'g' => 1024*1024*1024,
					'K' => 1024, 'M' => 1024*1024, 'G' => 1024*1024*1024);
				$upload_max_filesize = ini_get("upload_max_filesize");
				$upload_max_filesize = intval($upload_max_filesize) *
					$filesizeToBytes[substr($upload_max_filesize, strlen($upload_max_filesize) - 1)];
//				echo uploadFormView($pathToThisFile[0].$pathToThisFile[1], 
//									$upload_max_filesize);
				echo pluploadView($pathToThisFile[0].$pathToThisFile[1], 
									$upload_max_filesize);

				//here secure upload file creation
				$secureUploadFile = fopen("sup.php", "wt");
				fwrite($secureUploadFile, "<?php \$uploaderIsShown = true; ?>");
				fclose($secureUploadFile);
			}
			break;
		case "uploadImage":
			@require("sup.php");
			if(!$uploaderIsShown)
				die("Error: Relogin please.");

//			@include_once("noswfupload/upload.php");
			@include_once("plupload/upload.php");
			
			$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
			foreach (array(".php", ".phtml", ".php3", ".php4") as $ext)
			{
				if(preg_match("/$ext\$/i", $fileName))
				{
					@unlink($filePath);
					die("Error: We do not allow uploading PHP files");
				}
			}
			$imageinfo = getimagesize($filePath);
			if(!(	$imageinfo['mime'] == 'image/gif' ||
				$imageinfo['mime'] == 'image/jpeg' ||
				$imageinfo['mime'] == 'image/png' ||
				$imageinfo['mime'] == 'image/bmp'))
			{
				@unlink($filePath);
				die ("Error: We only accept GIF, JPEG, BMP and PNG images");
			}
			// Return JSON-RPC response
			echo('{"jsonrpc" : "2.0", "result" : {"filename": "' . $fileName . '"}, "id" : "id"}');
			break;
	}
?>
