function chssVariationList()
{
	this._moves = new Array();
	this._halfmove = undefined;
	this._variationId = undefined;
	this._parentVariationId = undefined;
	this._userId = 0;
	this._username = "";
	this._solution = undefined;
}

chssVariationList.prototype.getMoves = function()
{
	return this._moves;
}

chssVariationList.prototype.setMoves = function(moves)
{
	this._moves = moves;
}

chssVariationList.prototype.getHalfmove = function()
{
	return this._halfmove;
}

chssVariationList.prototype.setHalfmove = function(halfmove)
{
	this._halfmove = halfmove;
}

chssVariationList.prototype.getVariationId = function()
{
	return this._variationId;
}

chssVariationList.prototype.setVariationId = function(variationId)
{
	this._variationId = variationId;
}

chssVariationList.prototype.getParentVariationId = function()
{
	return this._parentVariationId;
}

chssVariationList.prototype.setParentVariationId = function(parentVariationId)
{
	this._parentVariationId = parentVariationId;
}

chssVariationList.prototype.getUserId = function()
{
	return this._userId;
}

chssVariationList.prototype.setUserId = function(userId)
{
	this._userId = userId;
}

chssVariationList.prototype.getUsername = function()
{
	return this._username;
}

chssVariationList.prototype.setUsername = function(username)
{
	this._username = username;
}

chssVariationList.prototype.getSolution = function()
{
	return this._solution;
}

chssVariationList.prototype.setSolution = function(solution)
{
	this._solution = solution;
}

chssVariationList.prototype.getMovesToString = function()
{
	console.log("Moves: ");
	var j=1;
	for(var i=0; i<this._moves.length; i++)
	{
		console.log((Math.floor(i/2) + 1) + ": " + this._moves[i].getNotation() + (this._moves[i+1] !== undefined?" " + this._moves[++i].getNotation():""));
	}
}