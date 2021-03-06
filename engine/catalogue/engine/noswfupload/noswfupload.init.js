$(document).ready(function(){
	var
	// the input type file to wrap
	//input   = document.getElementsByTagName("input")[0],
	input = document.getElementById("fileToUpload"),

	// the submit button
	//submit  = document.getElementsByTagName("input")[1],
	submit = document.getElementById("fileInputSubmit"),

	// the form
	//form    = document.getElementsByTagName("form")[0],
	form  = document.getElementById("fileInputForm"),

	// the form action to use with noswfupload
	url     = form.getAttribute("action") || form.action,

	// noswfupload wrap Object
	wrap;

	// if we do not need the form ...
	// move inputs outside the form since we do not need it
	with(form.parentNode){
		appendChild(input);
		appendChild(submit);
	}

	// remove the form
	form.parentNode.removeChild(form);

	/**
	 * Type of the thing we want upload picture to.
	 */
	//var type = $("#addItemForm_type").val();
	var date = new Date();
	/**
	 * Prefix for the pictore names to make them unique.
	 */
	var namePrefix = date.getTime();
	//wrap = noswfupload.wrap(input, upload_max_filesize, '{"todo": "uploadImage", "type": "'+type+'", "namePrefix": "'+namePrefix+'"}');
	wrap = noswfupload.wrap(input, upload_max_filesize, '{"todo": "uploadImage", "namePrefix": "'+namePrefix+'"}');

	// form and input are useless now (remove references)
	form = input = null;

	// assign event to the submit button
	noswfupload.event.add(submit, "click", function(e)
	{
		// only if there is at least a file to upload
		if(wrap.files.length)
		{
			submit.setAttribute("disabled", "disabled");
			wrap.upload(
				// it is possible to declare events directly here
				// via Object
				// {onload:function(){ ... }, onerror:function(){ ... }, etc ...}
				// these callbacks will be injected in the wrap object
				// In this case events are implemented manually
			);
		}
		else
			noswfupload.text(wrap.dom.info, "No files selected");
	
		submit.blur();

		// block native events
		return  noswfupload.event.stop(e);
	});

// set wrap object properties and methods (events)

// url to upload files
wrap.url = url;

// accepted file types (filter)
wrap.fileType = "Images (*.jpg, *.jpeg, *.png, *.gif, *.bmp)";
// fileType could contain whatever text but filter checks *.{extension} if present

// handlers
// instantly vefore files are sent
wrap.onloadstart = function(rpe, xhr){

	// we need to show progress bars and disable input file (no choice during upload)
	this.show(0);

	// write something in the span info
	noswfupload.text(this.dom.info, "Preparing for upload ... ");

};

// event called during progress. It could be the real one, if browser supports it, or a simulated one.
wrap.onprogress = function(rpe, xhr){

	// percent for each bar
	this.show((this.sent + rpe.loaded) * 100 / this.total, rpe.loaded * 100 / rpe.total);

	// info to show during upload
	noswfupload.text(this.dom.info, "Uploading: " + this.file.fileName);

	// fileSize is -1 only if browser does not support file info access
	// this if splits recent browsers from others
	if(this.file.fileSize !== -1){

		// simulation property indicates when the progress event is fake
		if(rpe.simulation)
		// in this case sent data is fake but we still have the total so we could show something
			noswfupload.text(this.dom.info,
		"Uploading: " + this.file.fileName,
		"Total Sent: " + noswfupload.size(this.sent + rpe.loaded) + " of " + noswfupload.size(this.total)
	);
		else
		// this is the best case scenario, every information is valid
			noswfupload.text(this.dom.info,
		"Uploading: " + this.file.fileName,
		"Sent: " + noswfupload.size(rpe.loaded) + " of " + noswfupload.size(rpe.total),
		"Total Sent: " + noswfupload.size(this.sent + rpe.loaded) + " of " + noswfupload.size(this.total)
	);
	} else
	// if fileSIze is -1 browser is using an iframe because it does not support
	// files sent via Ajax (XMLHttpRequest)
	// We can still show some information
		noswfupload.text(this.dom.info,
	"Uploading: " + this.file.fileName,
	"Sent: " + (this.sent / 100) + " out of " + (this.total / 100)
);
};

// generated if there is something wrong during upload
wrap.onerror = function(){
	// just inform the user something was wrong
	noswfupload.text(this.dom.info, "WARNING: Unable to upload " + this.file.fileName);
};

// generated when every file has been sent (one or more, it does not matter)
wrap.onload = function(rpe, xhr){
//alert(xhr.responseText);
	addUploadedFileName(xhr.responseText);
	alert(xhr.responseText);
	var self = this;
	// just show everything is fine ...
	noswfupload.text(this.dom.info, "Upload complete");

	// ... and after a second reset the component
	setTimeout(function(){
		self.clean();   // remove files from list
		self.hide();    // hide progress bars and enable input file

		noswfupload.text(self.dom.info, "");

		// enable again the submit button/element
		submit.removeAttribute("disabled");
	}, 1000);
};

});