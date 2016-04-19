var chssHelper = {};

chssHelper.loadGameFromJSON = function(data)
{
	if(data.game)
	{
		var game = new chssGame(data.game.fen);
		game.setEdit(true);
		if(data.game.moves)
			chssHelper.parseMoves(data.game.moves, game, (typeof data.game.firstMove !== 'undefined'?data.game.firstMove:null), -1, 0);
		
		if(data.comments)
		{
			for(var i=0; i<data.comments.length; i++)
			{
				game.getPGNFile().getMoves()[data.comments[i].halfMove - 1].setComment((data.comments[i].name?chssHelper.randomCharSet():0), data.comments[i].comment, (data.comments[i].name?data.comments[i].name:""));
			}
		}

		if(data.variations)
		{
			chssHelper.loadVariations(data.variations, 0, game);
		}

		if(data.variation_comments)
		{
			for(var i=0; i<data.variation_comments.length; i++)
			{
				var variationComment = data.variation_comments[i];
				for(var j=0; j<game.getPGNFile().getVariations().length;j++)
				{
					var variation = game.getPGNFile().getVariations()[j];
					if(variation.getVariationId() == variationComment.variationId)
					{
						variation.getMoves()[variationComment.halfMove - 1].setComment((variationComment.name?chssHelper.randomCharSet():0), variationComment.comment, (variationComment.name?variationComment.name:""));
					}
				}
			}
		}

		return game.getPGNFile();
	}
	
	throw new Error("Trying to load invalid game!")
}

chssHelper.loadVariations = function(variations, parentId, game)
{
	for(var i=0; i<variations.length; i++)
	{
		var variation = variations[i];
		if(variation.parentVariationId == parentId)
		{
			chssHelper.parseMoves(variation.moves, game, "", variation.parentVariationId, variation.halfMove);
			game.getPGNFile().getVariations()[game.getPGNFile().getVariations().length-1].setVariationId(variation.variationId);
			if(variation.name)
			{
				game.getPGNFile().getVariations()[game.getPGNFile().getVariations().length-1].setUserId(chssHelper.randomCharSet());
				game.getPGNFile().getVariations()[game.getPGNFile().getVariations().length-1].setUsername(variation.name);
			}
			if(variation.solution)
			{
				game.getPGNFile().getVariations()[game.getPGNFile().getVariations().length-1].setSolution(variation.solution);
			}
			chssHelper.loadVariations(variations, variation.variationId, game);
		}
	}
}

chssHelper.parseMoves = function(moves, game, firstMove, parentVariationId, halfMove)
{
	var indexBack = moves.indexOf("/");
	var variation = false;
	if(parentVariationId == -1 && firstMove != null && firstMove != "" && firstMove != "NaNNaNNaNNaN")
	{
		game.addLastMove(7 - parseInt(firstMove.substr(1, 1)),
						 7 - parseInt(firstMove.substr(0, 1)),
						 7 - parseInt(firstMove.substr(3, 1)),
						 7 - parseInt(firstMove.substr(2, 1)));
	}
	else if(parentVariationId != -1)
	{
		game.setVariationId(parentVariationId);
		game.changeBoard(String(halfMove), false);
		variation = true;
	}
	while(indexBack != -1)
	{
		var move = moves.substr(0, indexBack+1);
		moves = moves.substr(indexBack+1);
		indexBack = moves.indexOf("/");
		var i1 = 7 - parseInt(move.substr(0, 1));
		var j1 = 7 - parseInt(move.substr(1, 1));
		var i2 = 7 - parseInt(move.substr(2, 1));
		var j2 = 7 - parseInt(move.substr(3, 1));
		var promotionPiece = move.substr(4, 1);
		var nag = move.substr(4, 1);
		var nagIndex = 4;
		var val = game.addMove(j1, i1, j2, i2, variation);
		if(promotionPiece!="/" && promotionPiece != "$")
		{
			game.addPromotionPiece(game.active(true), promotionPiece);
			nag = move.substr(5, 1);
			nagIndex = 5;
		}
		if(nag == "$")
		{
			nag = move.substring(nagIndex, move.length - 1);
			game.changeNAG(true, nag);
		}
		variation = false;
	}
}

/*
 * 	Parent: the element in which the text will be appended and which is in some way appended to the body.
 * 			Recommended is a empty element, the returned string will always render taking the parents content in consideration.
 * 	String: the string that needs to be wrapped
 * 	Width: the width where to begin a newline
 * 	brArg: false = remove existing <br/>'s during processing, true = keep existing <br/> tags during processing and add a new one after each word; recommended: false
 *  newLine: is the intended string being used after a newline? true : false
 *  fontSize: the fontsize of the string; default = inherited
 */
chssHelper.wordWrap = function(parent, string, width, brArg, newLine, fontSize)
{
	if(typeof width == "undefined" || !chssHelper.isNumeric(width) || width<=0)
	{
		console.trace();
		throw Error("Argument 'width' invalid! Value: " + width);
	}

	var innerLine = "",
		innerSpan = "",
		output = "",
		subline = "",
		sublines = "",
		br = false,
		word = undefined,
		line = document.createElement("div"),
		span = document.createElement("div"),
		words = string.replace(/<br\/>/g, "<br/> ").split(" ");
	
	if(typeof fontSize === "undefined")
		fontSize = parseFloat(chssHelper.searchFontSize(parent));

	span.style.fontSize = fontSize + "px",
	span.style.display = "inline";
	line.style.fontSize = fontSize + "px";
	line.style.display = "inline";
	
	for(var i=0; i<words.length; i++)
	{
		word = words[i];
		span.innerHTML = word;
		parent.appendChild(line);
		parent.appendChild(span);
		
		if(word.indexOf("<br/>")!=-1 || brArg)
		{
			if(!brArg)
				word = word.substr(0, word.length-5);
			br = true;
		}
		else
			br = false;
		var length = word.length;
		if(span.offsetWidth + line.offsetWidth > width)
		{
			if(length<=6)
			{
				output += innerLine.substr(0, innerLine.length-1) + "<br/>" + (br?word + "<br/>":"");
				innerLine = (br?"":word + " ");
			}
			else
			{
				span.innerHTML = word.substr(0, 3);

				if(span.offsetWidth + line.offsetWidth > width)
				{
					output += innerLine.substr(0, innerLine.length-1) + "<br/>";
					subline = chssHelper.wordWrap(parent, word, width, br, false, fontSize);
				}
				else
				{
					span.innerHTML = word;
					if(newLine || span.offsetWidth > width)
					{
						for(var j=4; j<length; j++)
						{
							span.innerHTML = word.substr(0, j);
							if(span.offsetWidth + line.offsetWidth > width)
							{
								output += innerLine + word.substr(0, j-1) + "<br/>";
								subline = chssHelper.wordWrap(parent, word.substr(j-1), width, br, true, fontSize);
								break;
							}
						}
						if(j==length)
						{
							subline = innerLine.substr(0, innerLine.length-1) + "<br/>" + word;
						}
					}
					else
					{
						output += innerLine + "<br/>";
						subline = chssHelper.wordWrap(parent, word, width, br, true, fontSize);
					}
				}
				sublines = subline.split("<br/>");
				for(var k=0; k<sublines.length; k++)
				{
					if(k!=sublines.length-1)
						output += sublines[k] + "<br/>";
					else
					{
						if(br)
							output += sublines[k] + "<br/>";
						innerLine = (br?"":sublines[k] + " ");
					}
				}
			}
		}
		else
		{
			if(br)
			{
				output += innerLine + word + "<br/>";
				innerLine = "";
			}
			else
				innerLine += word + " ";
		}
		line.innerHTML = innerLine;
		
		parent.removeChild(line);
		parent.removeChild(span);
	}
	output += innerLine + (br?"<br/>":"");
	return output.substr(0, output.length-1).replace(/(<\/br>)+/g, "<br/>");
}

chssHelper.searchFontSize = function(element)
{
	var size = element.style.fontSize;
	if(!element.style.fontSize)
		size = chssHelper.searchFontSize(element.parentElement);

	return size;
}

chssHelper.showScroll = function(element, height)
{
	if(element.scrollHeight>height)
	{
		element.style.height = height + "px";
		element.style.marginRight = "0px";
		element.style.width = element.offsetWidth + 25 + "px";
		element.style.overflowY = "auto";
	}
	else
		element.style.marginRight = "1em";
		
}

chssHelper.array_removeAt = function(array, index)
{
	return array.slice(0, index).concat(array.slice(index+1));
}


chssHelper.array_addAt = function(array, index, item)
{
	var arr = array.slice(0, index);
	arr.push(item);
	return arr.concat(array.slice(index));
}

chssHelper.isNumeric = function(n)
{
	return !isNaN(chssHelper.filterFloat(n)) && isFinite(n);
}

chssHelper.filterFloat = function(value)
{
	if(/^(\-|\+)?([0-9]+(\.[0-9]+)?|Infinity)$/.test(value))
    	return Number(value);
    return NaN;
}

chssHelper.stripHTMLTags = function(text)
{
	var tags = /<p>|<\/p>/gi;
	return text.replace(tags, "");
}

chssHelper.getComputedStyle = function(element, property)
{
	if(window.getComputedStyle)
		return window.getComputedStyle(element).getPropertyValue(property);
	else
	{
		var words = property.split("-");
		property = "";
		for(var i=0;i<words.length;i++)
		{
			var word = words[i];
			property += word.substr(0,1).toUpperCase() + word.substr(1);
		}
		return element.currentStyle[property];
	}
}

chssHelper.getBoardCoordFromEvent = function(event)
{
	var board = chssBoard.board.getBoard(),
		rect = board.getBoundingClientRect();
	
	return {x: Math.floor((event.clientX - rect.left) / (45 * (chssOptions.board_size/360))), y: Math.floor((event.clientY - rect.top) / (45 * (chssOptions.board_size/360)))};
}

chssHelper.getScrollDocument = function()
{
	scroll = {left: window.scrollX || window.pageXOffset ||document.body.scrollLeft + (document.documentElement && document.documentElement.scrollLeft), top: window.scrollY || window.pageYOffset ||document.body.scrollTop + (document.documentElement && document.documentElement.scrollTop)};
	return scroll;
}

chssHelper.randomCharSet = function(length)
{
	var charset, ret, i;
	
	if(typeof length === "undefined")
		length = 62;
	
	charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	ret = "";
	for(i=0;i<length;i++)
	{
		ret += charset.charAt(Math.floor(Math.random() * 61));
	}
	return ret;
}