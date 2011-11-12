function changeImageInputType(sahc, pathToUploader)
{
	$("#uploaderWork").slideUp("slow");
	$("#uploaderWork").html("");
	var todo;
	switch($("#imageInputType").val())
	{
		case "byHand":
			$("input#images").parent().slideDown("slow");
			$("#addItemForm_thumbnail").slideDown("slow");
			$("#addItemForm_thumbnail_label").slideDown("slow");
			break;
		case "chooseFromUploaded":
			$("input#images").parent().slideUp("slow");
			break;
		default:
		case "upload":
			$("input#images").parent().slideUp("slow");
			$("#addItemForm_thumbnail").slideUp("slow");
			$("#addItemForm_thumbnail_label").slideUp("slow");
			todo = "showUploader";
			after = "addUploadedFileName";
			break;
	}
	if(todo)
	{
		$.ajax(
		{
			type: "POST",
			url: pathToUploader+"uploader.engine.php",
			data: "todo="+todo+"&sahc="+sahc+"&after="+after,
			beforeSend: function(){
				$("#loading").show("fast");
			}, //show loading just when link is clicked
			complete: function(){
				$("#loading").hide("fast");
			}, //stop showing loading when the process is complete
			success: function(html) //so, if data is retrieved, store it in html
			{
				$("#uploaderWork").html(html); //show the html inside #uploaderWork div
				$("#uploaderWork").show("slow"); //animation
			}
		});
	}
}
//function addUploadedFileName(data)
function addUploadedFileName(imageFileName)
{
//	if(data.substr(0, "File uploaded:".length) == "File uploaded:")
//	{
//		var imageFileName = data.substr("File uploaded:".length, data.length-"File uploaded:".length);
		var prev = $("#images").val();
		if($("#addItemForm_type").val() == "group")
		{
			$("#addItemForm_thumbnail").val("");
		}
		if(prev)
			prev += " ";
		var next = prev + /*"images/" + */imageFileName;
		$("#images").val(next);
		
//	}
}
function removePageClick(what, loc)
{
	if (!window.confirm("Ви дійсно хочете видалити "+what+'?'))
		return;
	window.location = loc;
}
var kr2en = {
	/*kr_str : "АБВГҐДЕЁЖЗИІЇЙКЛМНОПРСТУФХЦЧШЩЪЫЬЭЮЯабвгґдеёжзиіїйклмнопрстуфхцчшщъыьэюя",
	en_str : [	'A','B','V','H','G','D','E','JO','ZH','Z','Y','I','YI','Y','K','L','M','N','O','P','R','S','T',
	'U','F','H','C','CH','SH','SHCH',String.fromCharCode(35),'Y',String.fromCharCode(39),'YE','YU','YA',
				'a','b','v','h','g','d','e','jo','zh','z','y','i','yi','y','k','l','m','n','o','p','r','s','t',
	'u','f','h','c','ch','sh','shch',String.fromCharCode(35),'y',String.fromCharCode(39),'ye','yu','ya'],*/
	kr_str : "АБВГҐДЕЁЖЗИІЇЙКЛМНОПРСТУФХЦЧШЩЫЭЮЯабвгґдеёжзиіїйклмнопрстуфхцчшщыэюя",
	en_str : [	'A','B','V','H','G','D','E','JO','ZH','Z','Y','I','YI','Y','K','L','M','N','O','P','R','S','T',
	'U','F','H','C','CH','SH','SHCH','Y','YE','YU','YA',
				'a','b','v','h','g','d','e','jo','zh','z','y','i','yi','y','k','l','m','n','o','p','r','s','t',
	'u','f','h','c','ch','sh','shch','y','ye','yu','ya'],
	translit : function (inp)
	{
		var a = inp.split("");
		for (var i=0, aL=a.length; i < aL; i++)
		{
			a[i] = kr2en.kr2en[a[i]]
		}
		return a.join("");
	}
}
$(document).ready(function()
{
	kr2en.kr2en = {};
	for(var i = 0, l = kr2en.kr_str.length; i < l; i++)
		kr2en.kr2en[kr2en.kr_str.charAt(i)] = kr2en.en_str[i];
})
function catalogueStartUpTinyMCE()
{
	tinyMCE.init
	({
		// General options
		mode: "textareas",
		language: "uk",
		theme: "advanced",
		editor_selector: "tinymceEditor",
		plugins: "pagebreak,style,layer,table,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",

		//Layout
		height: 460,
		width: 768,
		content_css: "main.css", // Content CSS (should be your site CSS)
		
		// Theme options
		theme_advanced_buttons1 : "newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : false,

		// Drop lists for link/image/media/template dialogs
		template_external_list_url : "lists/template_list.js",
		external_link_list_url : "lists/link_list.js",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",

		//Cleanup/Output
		style_formats : [	// Style formats
			{title : 'Bold text', inline : 'b'},
			{title : 'Red text', inline : 'span', styles : {color : '#ff0000'}},
			{title : 'Red header', block : 'h1', styles : {color : '#ff0000'}},
			{title : 'Example 1', inline : 'span', classes : 'example1'},
			{title : 'Example 2', inline : 'span', classes : 'example2'},
			{title : 'Table styles'},
			{title : 'Table row 1', selector : 'tr', classes : 'tablerow1'}
		]
	});
}
function changeCollectionType(collection)
{
	if(!collection)
		collection = $("input[type = radio][name=collection_type]").filter(":checked").val();
	if(collection)
		collection = collection.replace(' ', '_');
	if(collection != "not_collection")
	{
		$("#features_vs_collections > ul > li").hide();
		
		$("#features_vs_collections > ul > li").each(function(index)
		{
			var jThis = $(this);
			var indOf = jThis.attr('class').indexOf(collection);
			if(indOf != '-1')
				jThis.show();
		});
		//$("#features_vs_collections span ."+collection).fadeIn("fast");
	}
	else
		$("#features_vs_collections > ul > li").show();
}
