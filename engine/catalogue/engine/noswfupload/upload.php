<?php
	/**
			 * <b>name</b> attribute in HTML <b>input</b> tag of <b>file</b> type.
			 * File specified in this HTML <b>input</b> tag is being uploading.
			 */
			$HTMLFileInputName = "fileToUpload";

			$destination_path = "../.." . DIRECTORY_SEPARATOR . "realSizeImages" . DIRECTORY_SEPARATOR;

			if(isset($_FILES[$HTMLFileInputName]))
			{
				if($_FILES[$HTMLFileInputName]["error"] === 0)
				{
					$namePrefix = $_REQUEST["namePrefix"];
					$fileName = $namePrefix . $_FILES[$HTMLFileInputName]['name'];	//maybe something as basename is needed (basename is locale dependent)

					$target_path = $destination_path . $fileName;
					if(move_uploaded_file($_FILES[$HTMLFileInputName]['tmp_name'], $target_path) ||
					  copy($_FILES[$HTMLFileInputName]['tmp_name'], $target_path))
					{
						// in copy case, Safari 4 beta, files will not be removed, do it manually
						if(file_exists($_FILES[$HTMLFileInputName]['tmp_name']))
							unlink($_FILES[$HTMLFileInputName]['tmp_name']);
						// upload completed
						die("File uploaded:" . $fileName);
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
					$errorsExplained = Array(
						0 => "0: There is no error, the file uploaded with success.",
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
				exit("Error: there is no file...");

				//print_r($_FILES);	//debug
			}
?>
