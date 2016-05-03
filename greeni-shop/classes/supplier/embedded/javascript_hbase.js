var is_french = window.location.href.search(/http(?:\:|%3A)(?:\/|%2F){2}login.mongrossiste.eu(?:\/|%2F)/);
var site = "http://login.mijngrossier.be/";
if(is_french != -1)
{
	site = "http://login.mongrossiste.eu/";
}

var is_local = top.location.href.search(/http(?:\:|%3A)(?:\/|%2F){2}localhost(?:\/|%2F)/);
var is_test = top.location.href.search(/http(?:\:|%3A)(?:\/|%2F){2}test.greeni-shop.eu(?:\/|%2F)/);
var url_site = "http://www.greeni-shop.eu/";
if(is_local != -1)
{
	url_site = "http://localhost/greeni-shop/";
}
else if(is_test != -1)
{
	url_site = "http://test.greeni-shop.eu/";
}

function NavigateNaar(sURL)
{
	iMoveGeweest = 0;
	var first_char = sURL.substring(0,1);
	if(first_char == "?")
	{
		var location = urlDecode(parent.document.getElementById("iframe_doc").src).split("=")[2].substring(44,parent.document.getElementById("iframe_doc").src.length);
		var last_char = location.indexOf("?");
		if(last_char != -1)
		{
			location = location.substring(0,last_char);
		}
		sURL = location + sURL;
	}
	parent.document.getElementById("iframe_doc").src = url_site + "get_url.php?root=" + urlEncode(site) + "&page=" + urlEncode(site) + "MGLIVE%2Fcatalog%2F" + urlEncode(sURL);
	if(window.event != undefined) 
	{
		window.event.cancelBubble = true;
		window.event.returnValue = false;
		if(window.event.stopPropagation)
		{
			window.event.stopPropagation();
		}
	}
	return false;
}

function NavigateNaarTarget(sURL,sTarget) 
{
	iMoveGeweest = 0;
	if (sTarget=='_top') 
	{
		top.location = url_site + "get_url.php?root=" + urlEncode(site) + "&page=" + urlEncode(site) + "MGLIVE%2Fcatalog%2F" + urlEncode(sURL);
	} 
	else 
	{
		parent.frames[sTarget+'-0000000001'].location = url_site + "get_url.php?root=" + urlEncode(site) + "&page=" + urlEncode(site) + "MGLIVE%2Fcatalog%2F" + urlEncode(sURL);
	}
	if (window.event) 
	{
		window.event.stopPropagation();
	}
	return false;
}