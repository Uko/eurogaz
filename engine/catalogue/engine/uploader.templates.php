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
function pluploadView($pathToUploader, $maxFileSize = "2")
{
	return "
		<script type=\"text/javascript\">
			// add dedicated css
			insert_css(\"{$pathToUploader}plupload/js/jquery.ui.plupload/css/jquery.ui.plupload.css\");
		</script>
		<script type=\"text/javascript\" src=\"{$pathToUploader}plupload/js/plupload.js\"></script>
		<script type=\"text/javascript\" src=\"{$pathToUploader}plupload/js/plupload.gears.js\"></script>
		<script type=\"text/javascript\" src=\"{$pathToUploader}plupload/js/plupload.silverlight.js\"></script>
		<script type=\"text/javascript\" src=\"{$pathToUploader}plupload/js/plupload.flash.js\"></script>
		<script type=\"text/javascript\" src=\"{$pathToUploader}plupload/js/plupload.browserplus.js\"></script>
		<script type=\"text/javascript\" src=\"{$pathToUploader}plupload/js/plupload.html4.js\"></script>
		<script type=\"text/javascript\" src=\"{$pathToUploader}plupload/js/plupload.html5.js\"></script>
		<script type=\"text/javascript\" src=\"{$pathToUploader}plupload/js/jquery.ui.plupload/jquery.ui.plupload.js\"></script>
		<script type=\"text/javascript\" src=\"{$pathToUploader}plupload/js/i18n/uk.js\"></script>
		<script type=\"text/javascript\">
//			var uploader = new plupload.Uploader(
			$(\"#filelist\").plupload(
			{
				runtimes : 'gears,html5,flash,silverlight,browserplus',
//				browse_button : 'pickfiles',
//				multi_selection: false,
//				container: 'container',
				max_file_size : '{$maxFileSize}b',
				url : '{$pathToUploader}uploader.engine.php',
//				resize : {width : 320, height : 240, quality : 90},
				flash_swf_url : './js/plupload.flash.swf',
				silverlight_xap_url : './js/plupload.silverlight.xap',
				filters : [
					{title : \"Image files\", extensions : \"jpg,gif,png\"}
//					,{title : \"Zip files\", extensions : \"zip\"}
				],
				buttons:{browse:true,start:false,stop:false},
				multipart: true,
				multipart_params: {todo: 'uploadImage'},
				
				// Post init events, bound after the internal events
				init:
				{
					FileUploaded: function(up, file, info)
					{
						addUploadedFileName(jQuery.parseJSON(info.response).result.filename);
					},
					
					// Called when a error has occured
					Error: function(up, args)
					{
						// Handle file specific error and general error
						if (args.file)
						{
							alert('Відбулась помилка:'+args.file);
						}
						else
						{
							alert('Відбулась помилка:'+args);
						}
					}
				}
			});
			
			$('#addItemForm').submit(function(e)
			{
				var uploader = $('#filelist').plupload('getUploader');

				// Files in queue upload them first
//				if (uploader.files.length > 0)
//				{
					// When all files are uploaded submit form
					uploader.bind('StateChanged', function()
					{
						if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed))
						{
							$('#addItemForm')[0].submit();
						}
					});
					
					uploader.start();
//				}
//				else
//					alert('You must at least upload one file.');
				return false;
			});
		</script>
		<script type=\"text/javascript\" src=\"{$pathToUploader}plupload/js/plupload.init.js\"></script>
		<div id=\"filelist\"></div>";
}
?>
