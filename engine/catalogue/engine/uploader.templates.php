<?php
function uploadFormView($pathToUploader, $maxFileSize = "")
{
	return "
		<script type=\"text/javascript\" src=\"{$pathToUploader}noswfupload/crossbrowserSaveJSONParse.js\"></script>
		<script type=\"text/javascript\" src=\"{$pathToUploader}noswfupload/noswfupload.js\"></script>
		<script type=\"text/javascript\">
			// add dedicated css
			noswfupload.css(\"{$pathToUploader}noswfupload/css/noswfupload.css\");
			noswfupload.css(\"{$pathToUploader}noswfupload/css/noswfupload-icons.css\");
		</script>
		<script type=\"text/javascript\">
			var upload_max_filesize  = $maxFileSize;
		</script>
		<script type=\"text/javascript\" src=\"{$pathToUploader}noswfupload/noswfupload.init.js\"></script>

		<div class=\"form\">
			<form id=\"fileInputForm\" method=\"post\" action=\"{$pathToUploader}uploader.engine.php\" enctype=\"multipart/form-data\">
				<div>
					<input type=\"file\" name=\"fileToUpload\" id=\"fileToUpload\" />
					<input class=\"submit\" type=\"submit\" value=\"Upload File\" id=\"fileInputSubmit\" />
				</div>
			</form>
		</div>";
}
?>