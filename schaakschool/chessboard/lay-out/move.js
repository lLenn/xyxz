function moveElement()
{
	this._moveElement = document.createElement("span");
	this._moveElement.style.position = "absolute";
	//this._moveElement.style.fontWeight = "bold";
	this._moveElement.style.paddingRight = 2 * (chssOptions.board_size/360) + "px";
	this._moveElement.style.paddingLeft = 2 * (chssOptions.board_size/360) + "px";
	this._moveElement.style.fontSize =  16 * (chssOptions.board_size/360) + "px";
	this._moveElement.style.cursor = "pointer";
	this.hover(this._moveElement, this);
	this.onClick(this);
	
	this._index = NaN;
	this._notation = "";
	this._annotation = "";
	this._annotationTooltip = "";
	this._valid = true;
	this._break = false;
	this._variationId = 0;
	this._variationUserId = 0;
	this._variationUsername = "";
	this._solution = false;
	this._result = chssGame.results.NONE;
	this._variationHalfmove = 0;
	this._lastVariationMove = 0;
	this._firstAfterVariation = false;
	this._lastMove = false;
	
	this._prevBackgroundColor = undefined;
	this._prevColor = undefined;
}

moveElement.prototype.getIndex = function()
{
	return this._index;
}

moveElement.prototype.setIndex = function(index)
{
	this._index = index;
}

moveElement.prototype.getNotation = function()
{
	return this._notation;
}

moveElement.prototype.setNotation = function(notation)
{
	this._notation = notation;
}

moveElement.prototype.getAnnotation = function()
{
	return this._annotation;
}

moveElement.prototype.setAnnotation = function(annotation)
{
	this._annotation = annotation;
}

moveElement.prototype.getAnnotationTooltip = function()
{
	return this._annotationTooltip;
}

moveElement.prototype.setAnnotationTooltip = function(annotationTooltip)
{
	this._annotationTooltip = annotationTooltip;
}

moveElement.prototype.getValid = function()
{
	return this._valid;
}

moveElement.prototype.setValid = function(valid)
{
	this._valid = valid;
}

moveElement.prototype.isBreak = function()
{
	return this._break;
}

moveElement.prototype.setBreak = function(breakVar)
{
	this._break = breakVar;
}

moveElement.prototype.getVariationId = function()
{
	return this._variationId;
}

moveElement.prototype.setVariationId = function(variationId)
{
	this._variationId = variationId;
}

moveElement.prototype.getVariationUserId = function()
{
	return this._variationUserId;
}

moveElement.prototype.setVariationUserId = function(variationUserId)
{
	this._variationUserId = variationUserId;
}

moveElement.prototype.getVariationUsername = function()
{
	return this._variationUsername;
}

moveElement.prototype.setVariationUsername = function(variationUsername)
{
	this._variationUsername = variationUsername;
}

moveElement.prototype.getSolution = function()
{
	return this._solution;
}

moveElement.prototype.setSolution = function(solution)
{
	this._solution = solution;
}

moveElement.prototype.getResult = function()
{
	return this._result;
}

moveElement.prototype.setResult = function(result)
{
	this._result = result;
}

moveElement.prototype.getVariationHalfmove = function()
{
	return this._variationHalfmove;
}

moveElement.prototype.setVariationHalfmove = function(variationHalfmove)
{
	this._variationHalfmove = variationHalfmove;
}

moveElement.prototype.getLastVariationMove = function()
{
	return this._lastVariationMove;
}

moveElement.prototype.setLastVariationMove = function(lastVariationMove)
{
	this._lastVariationMove = lastVariationMove;
}

moveElement.prototype.getFirstAfterVariation = function()
{
	return this._firstAfterVariation;
}

moveElement.prototype.setFirstAfterVariation = function(firstAfterVariation)
{
	this._firstAfterVariation = firstAfterVariation;
}

moveElement.prototype.getLastMove = function()
{
	return this._lastMove;
}

moveElement.prototype.setLastMove = function(lastMove)
{
	this._lastMove = lastMove;
}

moveElement.prototype.getMoveElement = function()
{
	return this._moveElement;
}

moveElement.prototype.setMoveElement = function(moveElement)
{
	this._moveElement = moveElement;
}

moveElement.prototype.getPrevBackgroundColor = function()
{
	return this._prevBackgroundColor;
}

moveElement.prototype.setPrevBackgroundColor = function(prevBackgroundColor)
{
	this._prevBackgroundColor = prevBackgroundColor;
}

moveElement.prototype.getPrevColor = function()
{
	return this._prevColor;
}

moveElement.prototype.setPrevColor = function(prevColor)
{
	this._prevColor = prevColor;
}

moveElement.prototype.draw = function()
{
	if(this._valid)
		this._moveElement.style.color = "#000000";
	else
		this._moveElement.style.color = "#FF0000";
	var innerHtml = "";
	
	var start = chssBoard.chssGame.getPGNFile().getStart();
	if((this._index + this._variationHalfmove)%2==0)
	{
		innerHtml = innerHtml + ((this._index + this._variationHalfmove)/2 + start) + ". ";
	}
	else if((this._index == 0 && this._variationId != 0 && (this._index + this._variationHalfmove)%2 == 1) || this.firstAfterVariation)
	{
		innerHtml = innerHtml + ((this._index + this._variationHalfmove - 1)/2 + start) + "... ";
	}
	
	innerHtml = innerHtml + this._notation;
	
	if(this._annotation != "")
	{
		innerHtml = innerHtml + this._annotation;
		this._moveElement.title = this._annotationTooltip;
	}
	
	if(this._break && chssBoard.chssGame.getEdit())
	{
		innerHtml = innerHtml + " BRK";
	}
	
	this._moveElement.innerHTML = innerHtml;
}

moveElement.prototype.hover = function(element, parent)
{
	element.onmouseenter = function()
	{
		parent.setPrevBackgroundColor(element.style.backgroundColor);
		parent.setPrevColor(element.style.color);
		element.style.backgroundColor = chssOptions.select_color;
		element.style.color = chssOptions.alt_color;
	}
	element.onmouseout = function()
	{
		element.style.backgroundColor = parent.getPrevBackgroundColor();
		element.style.color = parent.getPrevColor();
	}
}

moveElement.prototype.onClick = function(element)
{
	element.getMoveElement().onclick = function()
	{
		var index = element.getIndex() + element.getVariationHalfmove() + 1;
		var variation_id = isNaN(element.getVariationId())?0:element.getVariationId();
		chssBoard.moduleManager.actionChangeBoard(String(index), variation_id);
	}
}

moveElement.prototype.selected = function(bool)
{
	if(bool)
	{
		this._prevBackgroundColor = chssOptions.select_color;
		this._prevColor = chssOptions.alt_color;
		this._moveElement.style.backgroundColor = chssOptions.select_color;
		this._moveElement.style.color = chssOptions.alt_color;
	}
	else
	{
		this._moveElement.style.backgroundColor = "transparent";
		if(this._valid)
			this._moveElement.style.color = "#000000";
		else
			this._moveElement.style.color = "#FF0000";
	}
}