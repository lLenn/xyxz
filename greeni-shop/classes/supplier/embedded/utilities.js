function urlEncodeCharacter(c)
{
	return '%' + c.charCodeAt(0).toString(16);
};

function urlDecodeCharacter(str, c)
{
	return String.fromCharCode(parseInt(c, 16));
};

function urlEncode( s )
{
      return encodeURIComponent( s ).replace( /\%20/g, '+' ).replace( /[!'()*~]/g, urlEncodeCharacter );
};

function urlDecode( s )
{
      return decodeURIComponent(s.replace( /\+/g, '%20' )).replace( /\%([0-9a-f]{2})/g, urlDecodeCharacter);
};

function linkToShop(article, supplier_id, description, image)
{
	top.location = "my_order.php?itemref="+article+"&var="+supplier_id+"&desc="+urlEncode(description)+"&image="+urlEncode(image);
}

function linkToFavs(article, supplier_id, description, image)
{
	top.location = "my_favorites.php?itemref="+article+"&var="+supplier_id+"&desc="+urlEncode(description)+"&image="+urlEncode(image);
}