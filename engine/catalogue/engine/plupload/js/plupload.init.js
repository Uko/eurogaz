$(document).ready(function()
{
	uploader.bind('FilesAdded', function(up, files)
	{
		for (var i in files)
		{
			$('#filelist').append('<div id="' + files[i].id + '">' + files[i].name + ' (' + plupload.formatSize(files[i].size) + ') <b></b><a href="javascript:;" onClick="new function(){uploader.removeFile(uploader.getFile(\''+files[i].id+'\'));}">X</a></div>');
		}
	});

	uploader.bind('FilesRemoved', function(up, files)
	{
		for (var i in files)
		{
			$('#filelist > div#'+files[i].id).remove();
		}
	});
	
	uploader.bind('UploadProgress', function(up, file)
	{
		$('#filelist > div#'+file.id+' b').html("<span>" + file.percent + "%</span>");
	});
	
	uploader.bind('FileUploaded', function(up, file, responseInfo)
	{
		alert('Все добре:'+responseInfo);
		addUploadedFileName(jQuery.parseJSON(responseInfo.response).result.filename);
//		$('#filelist > div#'+file.id).remove();
	});

	uploader.bind('Error', function(up, error)
	{
		alert('Відбулась помилка завантаження:'+error);
	});
	$('#uploadfiles').click(function()
	{
		uploader.start();
		return false;
	});

	uploader.init();
});
