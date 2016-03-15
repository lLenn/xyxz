function actionsElement()
{
	this._actionsElement = document.createElement("div");
	this._actionsElement.id = "actionsMenu";
	this._actionsElement.style.backgroundColor = chssOptions.background_color;
	
	this._subBoardElement = document.createElement("div");
	this._subBoardElement.id = "subBoardActionMenu";
	this._subBoardElement.style.padding = "0px";
	this._subBoardElement.style.float = "left";

	this._enlarge = new buttonElement(buttonElement.constants.enlarge);
	this._enlarge.getButtonElement().style.float = "right";
	this._rotate = new buttonElement(buttonElement.constants.rotate);
	this._rotate.getButtonElement().style.float = "right";
	this._subBoardElement.appendChild(this._enlarge.getButtonElement());
	this._subBoardElement.appendChild(this._rotate.getButtonElement());
	
	this._subMovesElement = document.createElement("div");
	this._subMovesElement.id = "subMovesActionMenu";
	this._subMovesElement.style.float = "right";
	this._subMovesElement.style.position = "relative";
	
	this._actionsElement.appendChild(this._subBoardElement);
	this._actionsElement.appendChild(this._subMovesElement);
	var clearfloat = document.createElement("br");
	clearfloat.className = "clearfloat";
	this._actionsElement.appendChild(clearfloat);
}

actionsElement.prototype.drawSize = function(resize)
{
	if(typeof resize == "undefined")
		resize = false;
	
	this._rotate.drawSize(resize);
	this._enlarge.drawSize(resize);
}

actionsElement.prototype.getActionsElement = function()
{
	return this._actionsElement;
}

actionsElement.prototype.getSubBoardElement = function()
{
	return this._subBoardElement;
}

actionsElement.prototype.getSubMovesElement = function()
{
	return this._subMovesElement;
}

actionsElement.prototype.getChangeMovesElement = function()
{
	return this._changeMovesElement;
}