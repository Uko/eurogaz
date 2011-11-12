/**
 *	@param name - [string] Cookie name.
 *	@param data - [string] Cookie content.
 *	@param lifetime - [int] Cookie lifetime in milliseconds (1000 in the one seconds).
 *	@param domain - [string] Cookie domain.
 *	@param path - [string] Cookie path.
 */
function writeCookie(name, data, lifetime, domain, path)
{
	if(lifetime)
	{
		var date = new Date();
		date.setTime(date.getTime() + lifetime);
		var expires = date.toGMTString();
	}
	document.cookie = 	name + "=" + data + 
						((expires) ? "; expires=" + expires : "") + 
						((domain) ? "; domain=" + domain : "") + 
						((path) ? "; path=" + path : "/");
						
}
function readCookie(name)
{
	var cookies = "" + document.cookie;
	var offset = cookies.indexOf(name);
	if(offset > -1)
	{
		var end = cookies.indexOf(";", offset + name.length);
		if(end == -1)
			end = cookies.length;
		return cookies.substring(offset + name.length + 1, end);
	}
	return null;
}
/*
 *	Cart cookie format: "id1:amount1,id2:amount2,..."
 */
function createCartCookieString(idsArray, amountArray)
{
	var cartCookieString = "";
	for(i = 0, n = idsArray.length; i < n; i++)
	{
		cartCookieString += idsArray[i] + ":" + amountArray[i] + ",";
	}
	return cartCookieString.substr(0, cartCookieString.length - 1);
}
function addItemToTheCart(itemId)
{
	var cookieString = readCookie("cart");
	var itemIdsArray = new Array();
	var itemsAmountArray = new Array();
	
	if(cookieString != null)
	{
		var itemsStringArray = cookieString.split(",");
		for(i = 0, n = itemsStringArray.length; i < n; i++)
		{
			item = itemsStringArray[i].split(":");
			itemIdsArray.push(item[0]);
			itemsAmountArray.push(item[1]);
		}
		for(i = 0, n = itemsStringArray.length; i < n; i++)
		{
			if(itemIdsArray[i] == itemId)
			{
				itemsAmountArray[i] = parseInt(itemsAmountArray[i]) + 1;
				cookieString = "";
				writeCookie("cart", createCartCookieString(itemIdsArray, itemsAmountArray), 1000*60*60*24);
				alert("Товар додано.");
				return;
			}
		}
	}
	itemIdsArray.push(itemId);
	itemsAmountArray.push(1);
	writeCookie("cart", createCartCookieString(itemIdsArray, itemsAmountArray), 1000*60*60*24);
	alert("Товар додано.");
}

function removeItemFromTheCart(itemId)
{
	var cookieString = readCookie("cart");
	if(cookieString != null)
	{
		var itemIdsArray = new Array();
		var itemsAmountArray = new Array();
		var itemsStringArray = cookieString.split(",");
		for(i = 0, n = itemsStringArray.length; i < n; i++)
		{
			item = itemsStringArray[i].split(":");
			if(item[0] != itemId)
			{
				itemIdsArray.push(item[0]);
				itemsAmountArray.push(item[1]);
			}
//			else
//			{
//				if((parseInt(item[1]) - 1) > 0)
//				{
//					itemIdsArray.push(item[0]);
//					itemsAmountArray.push(parseInt(item[1]) - 1);
//				}
//			}
		}
		var lifetime;
		if(itemIdsArray.length == 0)
		{
			cookieString = "";
			lifetime = -1000*60*60*24;
		}
		else
		{
			cookieString = createCartCookieString(itemIdsArray, itemsAmountArray);
			lifetime = 1000*60*60*24;
		}
		writeCookie("cart", cookieString, lifetime);
		window.location.reload(true);
		window.location.replace(window.location.href);
		window.location.href = window.location.href;
	}
}
