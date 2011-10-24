function SaveButtonContent(divID, n, tinyMCE)
{
	//alert(tinyMCE.get('editable_content').getContent());
	alert("Починаю збереження...");
	$.ajax(
	{
   		type: "POST",
		url: "engine/buttons_module/buttons_save_content.php",
		data: "content="+tinyMCE.get('editable_content').getContent()+
				"&title="+document.getElementById('editable_header').value+
				"&index="+n,
	    success: function(msg)
						{
					     //alert("Зміни збережено.");
						 alert(msg);
						}
 	});
}

function InitNewMce()
{

			tinyMCE.init(
			{
				theme : "advanced",
        		mode : "exact",
		        elements : "editable_content",
				force_br_newlines : true,
		        force_p_newlines : false,
				height: 500,
        		forced_root_block : '', // Needed for 3.x
				plugins: "pagebreak,style,layer,table,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,advlist,autosave",				
				theme_advanced_buttons1 : "cut,copy,paste,pasteword,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,fontselect,fontsizeselect",
				theme_advanced_buttons2 : "search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,|,inserttime,preview,|,forecolor,backcolor",
				theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr",
				theme_advanced_buttons4 : "print,|,ltr,rtl,|,fullscreen,|,insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft",
				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				theme_advanced_statusbar_location : "bottom",
				theme_advanced_resizing : false,
				// Drop lists for link/image/media/template dialogs
				template_external_list_url : "lists/template_list.js",
				external_link_list_url : "lists/link_list.js",
				external_image_list_url : "lists/image_list.js",
				media_external_list_url : "lists/media_list.js"
			});

}