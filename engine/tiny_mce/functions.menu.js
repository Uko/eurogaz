/**
 * This is crossbrowser function that set opacity to *nOpacity* for element by id *sElemId*
 */
function setElementOpacity(sElemId, nOpacity)
{
	var opacityProp = getOpacityProperty();
	var elem = document.getElementById(sElemId);
	
	//If there is no element with mentioned id or browser does'nt support any of known ways of adjusting opacity
	if (!elem || !opacityProp)
		return;
	
	if (opacityProp=="filter")  // Internet Exploder 5.5+
	{
		nOpacity *= 100;

		// If opacity is already set then change it through collection filters else add opacity through style.
		var oAlpha = elem.filters['DXImageTransform.Microsoft.alpha'] || elem.filters.alpha;
		if (oAlpha) 
			oAlpha.opacity = nOpacity;
		else 
			elem.style.filter += "progid:DXImageTransform.Microsoft.Alpha(opacity="+nOpacity+")"; // In order not to clear another filters uses "+="
	} 
	else // Another browsers
		elem.style[opacityProp] = nOpacity;
}
/**
 * This function finds what option current browser use for opacity
 */
function getOpacityProperty()
{
	if (typeof document.body.style.opacity == 'string') // CSS3 compliant (Moz 1.7+, Safari 1.2+, Opera 9)
		return 'opacity';
	else if (typeof document.body.style.MozOpacity == 'string') // Mozilla 1.6 and younger, Firefox 0.8 
		return 'MozOpacity';
	else if (typeof document.body.style.KhtmlOpacity == 'string') // Konqueror 3.1, Safari 1.1
		return 'KhtmlOpacity';
	else if (document.body.filters && navigator.appVersion.match(/MSIE ([\d.]+);/)[1]>=5.5) // Internet Exploder 5.5+
		return 'filter';
	
	return false; //no opacity (transparency)
}
/**
 *	This function checks if *childObj* is a child object of *obj*
 */
function isChild(obj, childObj)
{
	if(childObj != null)
		while(childObj = childObj.parentNode)
			if(childObj == obj)
				return true;
	return false;
}
/**
 *	This function prevents from *obj* onMouseOut event appearing
 *	when mouse is under child elements of *obj*
 */
function correctOnMouseOut(obj, evt, code)
{
	var currentTarget = null;
	if(evt.toElement)
		currentTarget  = evt.toElement;
	else if(evt.relatedTarget)
		currentTarget  = evt.relatedTarget;
	if( !isChild(obj, currentTarget) && (obj != currentTarget))
		eval(code);
}
