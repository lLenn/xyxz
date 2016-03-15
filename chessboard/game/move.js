chssMove.constant = {NO_BREAK: "no_break",
					 PUZZLE_BREAK: "puzzle_break"};

function chssMove(x1, y1, x2, y2)
{
	this._x1 = x1;
	this._y1 = y1;
	this._x2 = x2;
	this._y2 = y2;
	this._notation = "";
	this._annotation = "";
	
	this._isBreak = false;
	this._breakType = 0;
	this._breakQuestion = "";
	this._comments = new Array();
	this._valid = true;
	this._promotionPiece = null;
	this._board = null;
	this._fen = null;
	this._fenClock = 1;
	this._halfmoveClock = 0;
	this._result = chssGame.results.NONE;
	this._enpassent = "-";
	this._castle = null;
	this._promotion = false;
	
	this._boardChanges = new Array();
}

chssMove.prototype.getX1 = function()
{
	return this._x1;
}

chssMove.prototype.setX1 = function(x1)
{
	this._x1 = x1;
}

chssMove.prototype.getY1 = function()
{
	return this._y1;
}

chssMove.prototype.setY1 = function(y1)
{
	this._y1 = y1;
}

chssMove.prototype.getX2 = function()
{
	return this._x2;
}

chssMove.prototype.setX2 = function(x2)
{
	this._x2 = x2;
}

chssMove.prototype.getY2 = function()
{
	return this._y2;
}

chssMove.prototype.setY2 = function(y2)
{
	this._y2 = y2;
}

chssMove.prototype.getNotation = function()
{
	return this._notation;
}

chssMove.prototype.setNotation = function(notation)
{
	this._notation = notation;
}

chssMove.prototype.getAnnotation = function()
{
	return this._annotation;
}

chssMove.prototype.setAnnotation = function(annotation)
{
	this._annotation = annotation;
}

chssMove.prototype.isBreak = function()
{
	return this._isBreak;
}

chssMove.prototype.setIsBreak = function(isBreak)
{
	this._isBreak = isBreak;
}

chssMove.prototype.getBreakType = function()
{
	return this._breakType;
}

chssMove.prototype.setBreakType = function(breakType)
{
	this._breakType = breakType;
}

chssMove.prototype.getBreakQuestion = function()
{
	return this._breakQuestion;
}

chssMove.prototype.setBreakQuestion = function(breakQuestion)
{
	this._breakQuestion = breakQuestion;
}

chssMove.prototype.getComments = function()
{
	return this._comments;
}

chssMove.prototype.setComments = function(comments)
{
	this._comments = comments;
}

chssMove.prototype.getComment = function(userid)
{
	for(var i=0; i<this._comments.length;i++)
	{
		if(this._comments[i].getUserId() == userid)
			return this._comments[i];
	}
	return new Comment(userid, "", "");
}

chssMove.prototype.setComment = function(userid, comment, username)
{
	var validation = false;
	for(var i=0; i<this._comments.length;i++)
	{
		if(this._comments[i].getUserId() == userid)
		{
			this._comments[i].setComment(comment);
			validation = true;
			break;
		}
	}
	if(!validation)	
	{
		this._comments.push(new chssComment(userid, comment, username));
	}
}

chssMove.prototype.getValid = function()
{
	return this._valid;
}

chssMove.prototype.setValid = function(valid)
{
	this._valid = valid;
}

chssMove.prototype.getPromotionPiece = function()
{
	return this._promotionPiece;
}

chssMove.prototype.setPromotionPiece = function(promotionPiece)
{
	this._promotionPiece = promotionPiece;
}

chssMove.prototype.getBoard = function()
{
	return this._board;
}

chssMove.prototype.setBoard = function(board)
{
	this._board = board;
}

chssMove.prototype.getFen = function()
{
	return this._fen;
}

chssMove.prototype.setFen = function(fen)
{
	this._fen = fen;
}

chssMove.prototype.getFenClock = function()
{
	return this._fenClock;
}

chssMove.prototype.setFenClock = function(fenClock)
{
	this._fenClock = fenClock;
}

chssMove.prototype.getHalfmoveClock = function()
{
	return this._halfmoveClock;
}

chssMove.prototype.setHalfmoveClock = function(halfmoveClock)
{
	this._halfmoveClock = halfmoveClock;
}

chssMove.prototype.getResult = function()
{
	return this._result;
}

chssMove.prototype.setResult = function(result)
{
	this._result = result;
}

chssMove.prototype.getEnpassent = function()
{
	return this._enpassent;
}

chssMove.prototype.setEnpassent = function(enpassent)
{
	this._enpassent = enpassent;
}

chssMove.prototype.getCastle = function()
{
	return this._castle;
}

chssMove.prototype.setCastle = function(castle)
{
	this._castle = castle;
}

chssMove.prototype.getPromotion = function()
{
	return this._promotion;
}

chssMove.prototype.setPromotion = function(promotion)
{
	this._promotion = promotion;
}

chssMove.prototype.getBoardChanges = function()
{
	return this._boardChanges;
}

chssMove.prototype.addPiece = function(x, y, piece, halfmove, variationId, variationHalfMove)
{
	var change = new chssBoardChange();
	change.addPiece(x, y, piece, halfmove, variationId, variationHalfMove);
	this._boardChanges.push(change);
}

chssMove.prototype.removePiece = function(x, y, halfmove, variationId, variationHalfMove)
{
	var change = new chssBoardChange();
	change.removePiece(x, y, piece, halfmove, variationId, variationHalfMove);
	this._boardChanges.push(change);
}

chssMove.prototype.addMove = function(x1, y1, x2, y2)
{
	this._x1 = x1;
	this._y1 = y1;
	this._x2 = x2;
	this._y2 = y2;
}

chssMove.prototype.isNull = function()
{
	return isNaN(this._x1) || isNaN(this._y1) || isNaN(this._x2) || isNaN(this._y2);
}