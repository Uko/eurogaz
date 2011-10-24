var xmlHttp;
var url;
var params;

function setDataToDefault()
{
	url = "engine/ajaxServerPart.php";
	params = "sahc=" + encodeURIComponent($("#SAHC").val()) + '&';
}
/**
 * Crossbrowser getting of XmlHttp object
 */
function GetXmlHttpObject()
{
	var objXMLHttp = null;
	if (window.XMLHttpRequest)
	{
		objXMLHttp = new XMLHttpRequest()
	}
	else if (window.ActiveXObject)
	{
		objXMLHttp = new ActiveXObject("Microsoft.XMLHTTP");
	}
	return objXMLHttp;
}
/**
 *	Functions that sends request
 */
function sendRequest(method)
{
	if(method == "GET")
	{
		url += '?' + params;
		xmlHttp.open("GET", url, true);
		xmlHttp.send(null);
	}
	else if(method == "POST")
	{
		xmlHttp.open("POST", url, true);
		xmlHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
		xmlHttp.setRequestHeader("Content-length", params.length);
		xmlHttp.setRequestHeader("Connection", "close");

		xmlHttp.send(params);
	}
}
function initDBRequest(resultContainerId)
{
	xmlHttp = GetXmlHttpObject();
	if (xmlHttp == null)
	{
		alert ("Browser does not support HTTP requests.");
		return;
	}
	params += "todo=initDB";
	xmlHttp.onreadystatechange = function(){getXmlHttpResponse(resultContainerId);};	/*run function "getXmlHttpResponse("DBTables")" after changes take place*/
	sendRequest("GET");
}
/**
 *	HTTP request to save page data into db
 */
function savePageDataRequest(pageName, pageTitle, pageContent, pageKeywords, pageDescription, resultContainerId)
{
	xmlHttp = GetXmlHttpObject();
	if (xmlHttp == null)
	{
		alert ("Browser does not support HTTP requests.");
		return;
	}
	pageName = encodeURIComponent(pageName);
	pageTitle = encodeURIComponent(pageTitle);
	pageContent = encodeURIComponent(pageContent);
	pageKeywords = encodeURIComponent(pageKeywords);
	pageDescription = encodeURIComponent(pageDescription);

	params += "todo=savePageData";
	//if(name)
		params += "&page_name="+pageName;
	//if(pageTitle)
		params += "&page_title="+pageTitle;
	//if(pageContent)
		params += "&page_content="+pageContent;
	//if(pageKeywords)
		params += "&page_keywords="+pageKeywords;
	//if(pageDescription)
		params += "&page_description="+pageDescription;
	xmlHttp.onreadystatechange = function(){getXmlHttpResponse(resultContainerId);};
	sendRequest("POST");
}
/**
 * Function that gets response from script which was startet by xmlHttp.send function
 * and inserts it into object by id "containerId"
 */
function getXmlHttpResponse(containerId, functionToLoadAfter) 
{
	if ((xmlHttp.readyState == 4 && xmlHttp.status == 200) || xmlHttp.readyState == "complete")
	{ 
		if(containerId)
			$("#"+containerId).html(xmlHttp.responseText);
		if(functionToLoadAfter)
			functionToLoadAfter();
		setDataToDefault();
		return xmlHttp.responseText;
	}
}
function addPageRequest(pageName, pageTitle, pageContent, pageKeywords, pageDescription, resultContainerId)
{
	xmlHttp = GetXmlHttpObject();
	if (xmlHttp == null)
	{
		alert ("Browser does not support HTTP requests.");
		return;
	}
	pageName = encodeURIComponent(pageName);
	pageTitle = encodeURIComponent(pageTitle);
	pageContent = encodeURIComponent(pageContent);
	pageKeywords = encodeURIComponent(pageKeywords);
	pageDescription = encodeURIComponent(pageDescription);
	
	params += "todo=addPage";
	//if(pageName)
		params += "&page_name="+pageName;
	if(pageTitle)
		params += "&page_title="+pageTitle;
	if(pageContent)
		params += "&page_content="+pageContent;
	if(pageKeywords)
		params += "&page_keywords="+pageKeywords;
	if(pageDescription)
		params += "&page_description="+pageDescription;
	xmlHttp.onreadystatechange = function(){getXmlHttpResponse(resultContainerId);};
	sendRequest("POST");
}
function removePageRequest(pageName, resultContainerId, functionToLoadAfter)
{
	xmlHttp = GetXmlHttpObject();
	if (xmlHttp == null)
	{
		alert ("Browser does not support HTTP requests.");
		return;
	}
	pageName = encodeURIComponent(pageName);
	params += "todo=removePage";
	if(pageName)
		params += "&page_name="+pageName;
	xmlHttp.onreadystatechange = function(){getXmlHttpResponse(resultContainerId, functionToLoadAfter);};
	
	sendRequest("POST");
}