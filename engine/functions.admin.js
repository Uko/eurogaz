/**
 *	id of the content container that will be hidded and edited.
 */
var contentContainer = "mainContent";
/**
 *	max element of the array
 */
Array.max = function(array)
{
	return Math.max.apply(Math, array);
};
/**
 *	Function that shows up barrier in front of content of the element which id have passed as a parameter
 */
function setUpBarrier(elementId, barrierInnerHTML)
{
	if((jElem = $("#"+elementId)).length)
	{
		//findout margin-top css value of element
		var elMarginTop = parseInt(jElem.css("margin-top"));
		//compute top css value of barrier
		var barrierPositionTop = elMarginTop + parseInt(jElem.position().top);
		//findout margin-left css value of element
		var elMarginLeft = parseInt(jElem.css("margin-left"));
		//compute left css value of barrier
		var barrierPositionLeft = elMarginLeft + parseInt(jElem.position().left);
		//compute barrier width = element outerWidth minus right margin (only innerWidth was too low, I don't know why)
		var barrierWidth = parseInt(jElem.outerWidth(true)) - parseInt(jElem.css("margin-right"));
		
		if(!barrierInnerHTML)
			barrierInnerHTML = "Wait Please. Loading...";
			
		var barrier = "	<div id=\""+elementId+"_barrier\" " +
							"style=\"position: absolute; " +
							"z-index: 1; " +
							//"background-color: #4CAB45; " +
							"background-color: #fff; " +
							"top: "+barrierPositionTop+"px; " +
							"left: "+barrierPositionLeft+"px; " +
							"width: "+barrierWidth+"px; " +
							"height: "+jElem.innerHeight()+"px;\">" +
							barrierInnerHTML +
						"</div>";
		//if there is no any barrier for this element set it
		if(!$("#"+elementId+"_barrier").length)
			jElem.parent().append(barrier);
	}
	else
		alert("There are no element with such id: "+elementId+".");
}
/**
 *	Function that removes barrier of the element which id have passed as a parameter
 */
function setDownBarrier(elementId)
{
	//get all barriers for this element
	var barrierCollection = $("#"+elementId+"_barrier");
	//if there are some barriers - remove them
	if(barrierCollection.length)
	{
		barrierCollection.remove();
	}
}
function disableEditorModeButtons()
{
	//document.getElementById("edit_button").onmousedown = function(){return false;};
	//$("#edit_button").attr("href", "javascript:;");
	//$("#edit_button").css("cursor", "default");
	//$("#menuButton_addNewPage").attr("href", "javascript:;");
	//$("#removePageButton").attr("title", $("#removePageButton").attr("href"));
	//$("#removePageButton").attr("href", "javascript:;");
	$("#edit_button").parent().hide();
	$("#menuButton_addNewPage").parent().hide();
	$("#removePageButton").parent().hide();
}
function enableEditorModeButtons()
{
	//document.getElementById("edit_button").onmousedown = edit;
	//$("#edit_button").attr("href", "javascript:edit();");
	//$("#edit_button").css("cursor", "pointer");
	//$("#menuButton_addNewPage").attr("href", "javascript:showAddPageForm();");
	//$("#removePageButton").attr("href", $("#removePageButton").attr("title"));
	//$("#removePageButton").attr("title", "");
	$("#edit_button").parent().show();
	$("#menuButton_addNewPage").parent().show();
	$("#removePageButton").parent().show();
}
function editorIsReady()
{
	setDownBarrier(contentContainer);
}
function startUpTinyMCE()
{
	tinyMCE.init
	({
		// General options
		mode: "textareas",
		language: "uk",
		theme: "advanced",
		editor_selector: "tinymceEditor",
		plugins: "pagebreak,style,layer,table,advhr,advimage,advlink,emotions,iespell,imagemanager,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",

		//Callbaks
		oninit: editorIsReady,
		
		//Layout
		height: 460,
		width: $("#"+contentContainer).width(),
		content_css: "main.css", // Content CSS (should be your site CSS)
		
		// Theme options
		theme_advanced_buttons1 : "cut,copy,paste,pasteword,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,|,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr",
		theme_advanced_buttons4 : "print,|,ltr,rtl,|,fullscreen,|,insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft,insertimage",
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
/**
 *	Function that have to change title every where it appears on the page
 *	*Fucntion almost for those who designs page*
 */
function changeTitleEverywhereOnThePage(pageName, newTitle)
{
	//get global title (20 = length of "Заголовок сторінки: ")
	var titlePrefix = $("[id $= '_title_label']").html().substring("Заголовок сторінки: ".length, $("[id $= '_title_label']").html().length);
	
	//change editor form data for next edits
	$("#pageTitle").val(newTitle);
	//change page title (<title> tag)
	parent.document.title = titlePrefix + newTitle;
}
/**
 *	Function that starts WYSIWIG editor to edit page data
 */
 var editingNow = false;
function edit(isMain)
{
	if (isMain)
		contentContainer = "mainContentText";
	if(!editingNow)
	{
		editingNow = true;
		disableEditorModeButtons();
		$("#edit_button").slideUp('slow');
		
		var pageTitle = $("#pageTitle").val();
		var globalTitlePrefix = document.title.substring(0, document.title.length - pageTitle.length);
		var pageContent = $("#"+contentContainer).html();
		var pageKeywords = $("#pageKeywords").val();
		var pageDescription = $("#pageDescription").val();
		var pageId = $("#pageId").val();
		
		var newPageContent = "\n<form id=\"form_pageEditor\" class=\"form_pageEditor\" action=\"javascript:savePageData();\" method=\"post\" enctype=\"application/x-www-form-urlencoded\" accept-charset=\"UTF-8\">";
		newPageContent += "\n<p><label id=\"page_title_label\" for=\"page_title\">Заголовок сторінки: " + globalTitlePrefix + "</label>";
		newPageContent += "\n<input id=\"page_title\" class=\"pageEditor_title\" type=\"text\" name=\"page_title\" value=\"" + pageTitle + "\"/></p>";
		newPageContent += "\n<div><textarea name=\"page_content\" id=\"page_content\" class=\"tinymceEditor\" cols=\"20\" rows=\"6\">" + pageContent + "</textarea></div>";
		newPageContent += "\n<p><label for=\"page_keywords\">Ключові слова (вводити через кому):</label>";
		newPageContent += "\n<input id=\"page_keywords\" class=\"pageEditor_keywords\" type=\"text\" name=\"page_keywords\" value=\"" + pageKeywords + "\"/></p>";
		newPageContent += "\n<br/>";
		newPageContent += "\n<p><label for=\"page_description\">Короткий опис сторінки:</label>";
		newPageContent += "\n<input id=\"page_description\" class=\"pageEditor_description\" type=\"text\" name=\"page_description\" value=\"" + pageDescription + "\"/></p>";
		newPageContent += "\n<br/>";
		newPageContent += "\n<p><input id=\"page_id\" type=\"hidden\" name=\"page_id\" value=\"" + pageId + "\" /></p>";
		newPageContent += "\n<p><input id=\"save_pageData_button\" type=\"submit\" value=\"Зберегти\"/></p>";
		newPageContent += "\n</form>";
		$("#"+contentContainer).html(newPageContent);
		setUpBarrier(contentContainer);
		
		startUpTinyMCE();
	}
}
/**
 *	Function that sends request to save data into DB, close WYSIWIG editor, show result to user
 */
function savePageData()
{
	//hide all doings behind the scene (barrier)
	setUpBarrier(contentContainer);
	
	//get contents of WYSIWIG editor (because content of texarea still is untouched even if editor have been closed one line before)
	var newPageContent = tinyMCE.get('page_content').getContent();
	//get value of keywords and description fields into variables because there was a trouble filling it into loggedInDataForm
	var newPageKeywords = $("#page_keywords").val();
	var newPageDescription = $("#page_description").val();
	
	//send request to save data
	savePageDataRequest($("#pageName").val(), $("#page_title").val(), newPageContent, newPageKeywords, newPageDescription, "ajaxResultContainer");
	
	//close WYSIWIG editor
	tinyMCE.execCommand("mceRemoveControl", false, "page_content");
	
	changeTitleEverywhereOnThePage($("#pageName").val(), $('#page_title').val());
	//fill content div with new data
	$("#"+contentContainer).html(newPageContent);
	//filling loggedInDataForm
	$("#pageKeywords").val(newPageKeywords);	//keywords
	$("#pageDescription").val(newPageDescription);	//description
	
	//unhide edit button, unhide page, enable buttons
	$('#edit_button').slideDown('slow');
	setDownBarrier(contentContainer);
	enableEditorModeButtons();
	
	editingNow = false;
}
function showAddPageForm()
{
	disableEditorModeButtons();
	
	var pageTitle = $("#pageTitle").val();
	var globalTitlePrefix = document.title.substring(0, document.title.length - pageTitle.length);
	
	var createNewPageForm = "\n<form id=\"form_addPage\" class=\"form_pageEditor\" action=\"javascript:addPage();\" method=\"post\" enctype=\"application/x-www-form-urlencoded\" accept-charset=\"UTF-8\">";
	createNewPageForm += "\n<p><label id=\"addPage_name_label\" for=\"addPage_name\">Введіть ім'я сторінки (не заголовок).</label>";
	createNewPageForm += "\n<input id=\"addPage_name\" type=\"text\" name=\"addPage_name\"/>";
	createNewPageForm += "\n<span style=\"float: left; font-size: 11px; color: grey; font-style: italic; margin-bottom: 10px;\">Одне слово англійською мовою яке найбільше відповідатиме вмісту сторінки.</span></p>";
	createNewPageForm += "\n<p><label id=\"addPage_title_label\" for=\"addPage_title\">Заголовок сторінки: " + globalTitlePrefix + "</label>";
	createNewPageForm += "\n<input id=\"addPage_title\" class=\"pageEditor_title\" type=\"text\" name=\"addPage_title\" value=\"\"/></p>";
	createNewPageForm += "\n<div><textarea name=\"addPage_content\" id=\"addPage_content\" class=\"tinymceEditor\" cols=\"20\" rows=\"6\"></textarea></div>";
	createNewPageForm += "\n<p><label for=\"addPage_keywords\">Ключові слова (вводити через кому):</label>";
	createNewPageForm += "\n<input id=\"addPage_keywords\" class=\"pageEditor_keywords\" type=\"text\" name=\"addPage_keywords\" value=\"\"/></p>";
	createNewPageForm += "\n<br/>";
	createNewPageForm += "\n<p><label for=\"addPage_description\">Короткий опис сторінки:</label>";
	createNewPageForm += "\n<input id=\"addPage_description\" class=\"pageEditor_description\" type=\"text\" name=\"addPage_description\" value=\"\"/></p>";
	createNewPageForm += "\n<br/>";
	createNewPageForm += "\n<p><input id=\"addPage_submit\" type=\"submit\" value=\"Створити\"/></p>";
	createNewPageForm += "\n</form>";
	
	$("#"+contentContainer).html(createNewPageForm);
	$("#form_addPage").bind('submit', function(event)	//check if user have inputted *name* of the page and is this name _correct_
	{
		event.preventDefault();
		check_form_addPage_name_entireValue();
		return false;
	});
	$("#addPage_name").bind("keypress", function(event)
	{
		var allowedChar = true;
		if(parseInt(event.which) >= 32)
			if(!isThisCharacterAllowed(parseInt(event.which)))
			{
				allowedChar = false;
				$("#addPage_name_label").css("font-size", "large");
				$("#addPage_name").css("background-color", "red");
				if($("#addPage_name_errorString").length)
				{
					$("#addPage_name_errorString").html("This symbol is not allowed.");
				}
				else
				{
					$("#addPage_name").after("<p id=\"addPage_name_errorString\" class=\"form_addPage_errorString\">This symbol is not allowed.</p>");
				}
				event.preventDefault();
			}
		
		if(allowedChar)
		{
			$("#addPage_name_label").css("font-size", "14px");
			$("#addPage_name").css("background-color", "white");
			$("#addPage_name_errorString").remove();
		}
	});
	
	setUpBarrier(contentContainer);	
	startUpTinyMCE();
}
function isThisCharacterAllowed(charCode)
{
	if(	(charCode == 43) ||//=
		(charCode == 45) ||//-
		((charCode >= 48) && (charCode <= 57)) ||//0-9
		((charCode >= 65) && (charCode <= 90)) ||//A-Z
		(charCode == 95) ||//_
		((charCode >= 97) &&	(charCode <= 122)))//a-z
		return true;
	
	return false;
}
function check_form_addPage_name_entireValue()
{
	var isCorrect = false;
	var errorMessage = "";
	var addPage_name_value;
	var alreadyExists = false;
	
	if(addPage_name_value = $("#addPage_name").val())
	{
		
		//the already existing check need to be rewrited to suit kylymok's web site
		$("#pagesList a").each(function(index)
		{
			var name = $(this).attr("href");
			if(name.substring(name.lastIndexOf("page=") + 5, name.length) == addPage_name_value)
				alreadyExists = true;
		});
		if(alreadyExists)
			errorMessage = "The page with the same name already exists."
		else
		{
			isCorrect = true;
			if(addPage_name_value.length < 64)
			{
				var i;
				for(i = 0; i < addPage_name_value.length; i++)
				{
					var charCode = addPage_name_value.charCodeAt(i);
					if(!isThisCharacterAllowed(charCode))
					{
						isCorrect = false;
						break;
					}
				}
				
				if(!isCorrect)
					errorMessage = "The page name is incorrect at least at "+(i+1)+" character.";
			}
			else
				errorMessage = "The page name is too long.";
		}
	}
	else
		errorMessage = "The page name is empty.";

	if(isCorrect && (errorMessage == "") && !alreadyExists)
	{
		$("#form_addPage").unbind('submit');
		$("#form_addPage").bind('submit', function(event)
		{ 
			event.preventDefault(); 
			addPage(); 
			return false;
		});
		$("#form_addPage").trigger('submit');
	}
	else
	{
		$("#addPage_name_label").css("font-size", "large");
		$("#addPage_name").css("background-color", "red");
		$("#addPage_name").after("<p id=\"addPage_name_errorString\" class=\"form_addPage_errorString\">"+errorMessage+"</p>");
		$("#addPage_name").bind('focus', function(event)
		{
			$("#addPage_name_label").css("font-size", "14px");
			$("#addPage_name").css("background-color", "white");
			$("#addPage_name_errorString").remove();
		});
	}
}
function addPage()
{
	setUpBarrier(contentContainer);
	
	var addPageName = $("#addPage_name").val();
	//get contents of WYSIWIG editor (because content of texarea still is untouched even if editor have been closed one line before)
	var addPageContent = tinyMCE.get('addPage_content').getContent();
	var addPageKeywords = $("#addPage_keywords").val();
	var addPageDescription = $("#addPage_description").val();
	
	addPageRequest(addPageName, $("#addPage_title").val(), addPageContent, addPageKeywords, addPageDescription, "ajaxResultContainer");

	//close WYSIWIG editor
	tinyMCE.execCommand("mceRemoveControl", false, "addPage_content");
	
	changeTitleEverywhereOnThePage($("#addPage_name").val(), $('#addPage_title').val());
	//fill content div with new data
	$("#"+contentContainer).html(addPageContent);
	//set name of the page to the form (from that form edit() takes name of page to save data)
	$("#pageName").val(addPageName);
	//keywords
	$("#pageKeywords").val(addPageKeywords);
	//description
	$("#pageDescription").val(addPageDescription);
	
	setDownBarrier(contentContainer);
	enableEditorModeButtons();
}
function removePage()
{
	if (!window.confirm("Ви дійсно хочете видалити сторінку: \""+$("#pageName").val()+"\"?"))
		return;

	removePageRequest($("#pageName").val(), "ajaxResultContainer", function(){location.reload(true);});
}
/**
 *	Function that have to be started after page gets loaded to enable normal work of editor mode
 */
function preparePageForEditing()
{
	setDataToDefault();
}
