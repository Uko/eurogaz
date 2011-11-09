/**
 * @description load dedicated CSS (avoid CSS download for browsers with JavaScript disabled)
 * @param css	- String	- css file
 * @return
 */
function insert_css(href)
{
	var head    = document.getElementsByTagName("head")[0] || document.documentElement,
	    style   = document.createElement("link");
	style.setAttribute("rel", "stylesheet");
	style.setAttribute("type", "text/css");
	style.setAttribute("media", "all");
	style.setAttribute("href", href);
	head.insertBefore(style, head.firstChild);
}
