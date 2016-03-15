var boardChange_consts = {ADD_PIECE: "add_piece", REMOVE_PIECE: "remove_piece"};

function chssBoardChange()
{
	this._piece = undefined;
	this._action = undefined;
	this._x = undefined;
	this._y = undefined;
	this._halfMove = undefined;
	this._variationId = undefined;
	this._variationHalfMove = undefined;
}

chssBoardChange.prototype.getPiece = function()
{
	return this._piece;
}

chssBoardChange.prototype.setPiece = function(piece)
{
	this._piece = piece;
}

chssBoardChange.prototype.getAction = function()
{
	return this._action;
}

chssBoardChange.prototype.setAction = function(action)
{
	this._action = action;
}

chssBoardChange.prototype.getX = function()
{
	return this._x;
}

chssBoardChange.prototype.setX = function(x)
{
	this._x = x;
}

chssBoardChange.prototype.getY = function()
{
	return this._y;
}

chssBoardChange.prototype.setY = function(y)
{
	this._y = y;
}

chssBoardChange.prototype.getHalfMove = function()
{
	return this._halfMove;
}

chssBoardChange.prototype.setHalfMove = function(halfMove)
{
	this._halfMove = halfMove;
}

chssBoardChange.prototype.getVariationId = function()
{
	return this._variationId;
}

chssBoardChange.prototype.setVariationId = function(variationId)
{
	this._variationId = variationId;
}

chssBoardChange.prototype.getVariationHalfMove = function()
{
	return this._variationHalfMove;
}

chssBoardChange.prototype.setVariationHalfMove = function(variationHalfMove)
{
	this._variationHalfMove = variationHalfMove;
}

chssBoardChange.prototype.addPiece = function(x, y, piece, halfmove, variationId, variationHalfMove)
{
	this._action = boardChange_consts.ADD_PIECE;
	this._x = x;
	this._y = y;
	this._piece = piece;
	this._halfMove = halfMove;
	this._variationId = variationId;
	this._variationHalfMove = variationHalfMove;
}

chssBoardChange.prototype.removePiece = function(x, y, halfmove, variationId, variationHalfMove)
{
	this._action = boardChange_consts.REMOVE_PIECE;
	this._x = x;
	this._y = y;
	this._halfMove = halfMove;
	this._variationId = variationId;
	this._variationHalfMove = variationHalfMove;
}