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
	$todo = $_GET["todo"];
	if(!$todo)
		$todo = $_POST["todo"];
	//echo $HTML_EOL."\$todo:$todo";
	switch($todo)
	{
		default:
			break;
		case "showUploader":
			@require("..php");
			$sahcFromThere = $_GET["sahc"];
			if($sahcFromThere == "")
				$sahcFromThere = $_POST["sahc"];	//quaries can be in POST mode too
			$sahcFromThere = urldecode($sahcFromThere);
			if( isset($SAHC) && isset($sahcFromThere) && ($SAHC == $sahcFromThere))
			{
				include_once("uploader.templates.php");
				$filesizeToBytes = array('k' => 1024, 'm' => 1024*1024, 'g' => 1024*1024*1024,
					'K' => 1024, 'M' => 1024*1024, 'G' => 1024*1024*1024);
				$upload_max_filesize = ini_get("upload_max_filesize");
				$upload_max_filesize = intval($upload_max_filesize) *
					$filesizeToBytes[substr($upload_max_filesize, strlen($upload_max_filesize) - 1)];
				echo uploadFormView($pathToThisFile[0].$pathToThisFile[1], 
									$upload_max_filesize);

				//here secure upload file creation
				$secureUploadFile = fopen("sup.php", "wt");
				fwrite($secureUploadFile, "<?php \$uploaderIsShown = true; ?>");
				fclose($secureUploadFile);
			}
			break;
		case "uploadImage":
			@require("sup.php");
			
			/**
			 * <b>name</b> attribute in HTML <b>input</b> tag of <b>file</b> type.
			 * File specified in this HTML <b>input</b> tag is being uploading.
			 */
			$HTMLFileInputName = "fileToUpload";

			$destination_path = ".." . DIRECTORY_SEPARATOR . "realSizeImages" . DIRECTORY_SEPARATOR;

			if($uploaderIsShown && isset($_FILES[$HTMLFileInputName]))
			{
				if($_FILES[$HTMLFileInputName]["error"] === 0)
				{
					$blacklist = array(".php", ".phtml", ".php3", ".php4");
					foreach ($blacklist as $item)
					{
						if(preg_match("/$item\$/i", $_FILES[$HTMLFileInputName]['name']))
						{
							echo "Error: We do not allow uploading PHP files\n";
							exit;
						}
					}
					$imageinfo = getimagesize($_FILES[$HTMLFileInputName]['tmp_name']);
					if	( !($imageinfo['mime'] == 'image/gif' ||
							$imageinfo['mime'] == 'image/jpeg' ||
							$imageinfo['mime'] == 'image/png' ||
							$imageinfo['mime'] == 'image/bmp')
						)
					{
						echo "Error: We only accept GIF, JPEG, BMP and PNG images\n";
						exit;
					}
					
					$namePrefix = $_GET["namePrefix"];
					if(!$namePrefix)
						$namePrefix = $_POST["namePrefix"];
					$newFileName = $namePrefix . $_FILES[$HTMLFileInputName]['name'];	//maybe something as basename is needed (basename is locale dependent)

					$target_path = $destination_path . $newFileName;
					if(move_uploaded_file($_FILES[$HTMLFileInputName]['tmp_name'], $target_path) ||
					  copy($_FILES[$HTMLFileInputName]['tmp_name'], $target_path))
					{
						// in copy case, Safari 4 beta, files will not be removed, do it manually
						if(file_exists($_FILES[$HTMLFileInputName]['tmp_name']))
							unlink($_FILES[$HTMLFileInputName]['tmp_name']);
						// upload completed
						echo "File uploaded:" . $newFileName;
						//exit('OK');
					}
					else
					{
						// if something was wrong ... should generate onerror event
						header('HTTP/1.1 500 Internal Server Error');
					}
				}
				else
				{
					$errorsExplained = Array(	0 => "0: There is no error, the file uploaded with success.",
												1 => "1: The uploaded file exceeds the upload maximum file size.",//for end user there is no difference
												2 => "2: The uploaded file exceeds the upload maximum file size.",//between these two errors
												3 => "3: The uploaded file was only partially uploaded.",
												4 => "4: No file was uploaded.",
												6 => "6: Missing a temporary folder.",
												7 => "7: Failed to write file to disk.",
												8 => "8: A PHP extension stopped the file upload."
											);
					echo "Error: " . $errorsExplained[$_FILES[$HTMLFileInputName]["error"]] . "<br/>";
				}
			}
			else
			{
				echo "Error: there is no file...";

				//print_r($_FILES);	//debug
			}
			break;
	}
?>