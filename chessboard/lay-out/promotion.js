function promotionPopUp()
{
	this._color = undefined;
	this._callback = undefined;
		
	this._promotionPopUp = document.createElement("div");
	this._promotionPopUp.style.display = "none";
	this._promotionPopUp.style.position = "absolute";
	this._promotionPopUp.style.backgroundImage = "url('" + chssOptions.images_url + "chessboard/BoardPromotion.png')";
	this._promotionPopUp.style.backgroundSize = "cover";

	this._queen = new chssPieceAbstract();
	this._queen.getPiece().style.position = "absolute";
	this._queen.getPiece().style.cursor = "pointer";
	this.onClick(this._queen, "Q", this);

	this._rook  = new chssPieceAbstract();
	this._rook.getPiece().style.position = "absolute";
	this._rook.getPiece().style.cursor = "pointer";
	this.onClick(this._rook, "R", this);

	this._bishop = new chssPieceAbstract();
	this._bishop.getPiece().style.position = "absolute";
	this._bishop.getPiece().style.cursor = "pointer";
	this.onClick(this._bishop, "B", this);

	this._knight = new chssPieceAbstract();
	this._knight.getPiece().style.position = "absolute";
	this._knight.getPiece().style.cursor = "pointer";
	this.onClick(this._knight, "N", this);
	
	this._promotionPopUp.appendChild(this._queen.getPiece());
	this._promotionPopUp.appendChild(this._rook.getPiece());
	this._promotionPopUp.appendChild(this._bishop.getPiece());
	this._promotionPopUp.appendChild(this._knight.getPiece());
}

promotionPopUp.prototype.drawSize = function()
{
	var dim = 130*(chssOptions.board_size/360),
		dimMarg = 20*(chssOptions.board_size/360);

	this._promotionPopUp.style.width = dim + "px";
	this._promotionPopUp.style.height = dim + "px";
	this._promotionPopUp.style.top = ((180-45)*(chssOptions.board_size/360)) + "px";
	this._promotionPopUp.style.left = ((180-45)*(chssOptions.board_size/360)) + "px";
	
	dim = 45*(chssOptions.board_size/360);
	this._queen.setSize(dim);
	this._queen.getPiece().style.top = dimMarg + "px";
	this._queen.getPiece().style.left = dimMarg + "px";

	this._rook.setSize(dim);
	this._rook.getPiece().style.top = dimMarg + "px";
	this._rook.getPiece().style.left = dimMarg + dim + "px";

	this._bishop.setSize(dim);
	this._bishop.getPiece().style.top = dimMarg + dim + "px";
	this._bishop.getPiece().style.left = dimMarg + "px";

	this._knight.setSize(dim);
	this._knight.getPiece().style.top = dimMarg + dim + "px";
	this._knight.getPiece().style.left = dimMarg + dim + "px";
}

promotionPopUp.prototype.getPromotionPopUp = function()
{
	return this._promotionPopUp;
}

promotionPopUp.prototype.draw = function(color, callback)
{
	chssBoard.board.getBackground().style.opacity = "0.6";
	chssBoard.board.getBackground().style.filter = "alpha(opacity=60)";
	chssBoard.board.getBoard().style.opacity = "0.6";
	chssBoard.board.getBoard().style.filter = "alpha(opacity=60)";
	this._promotionPopUp.style.display = "block";
	
	this._queen.draw(color, "Q");
	this._rook.draw(color, "R");
	this._bishop.draw(color, "B");
	this._knight.draw(color, "N");
	
	this._color = color;
	this._callback = callback;
}

promotionPopUp.prototype.onClick = function(element, piececode, parent)
{
	element.getPiece().onclick = function()
	{
		parent.onSelect(parent.getColor(), piececode);
	}
}

promotionPopUp.prototype.onSelect = function(color, piececode)
{
	chssBoard.board.getBackground().style.opacity = "1";
	chssBoard.board.getBackground().style.filter = "alpha(opacity=100)";
	chssBoard.board.getBoard().style.opacity = "1";
	chssBoard.board.getBoard().style.filter = "alpha(opacity=100)";
	this._promotionPopUp.style.display = "none";
	
	this._callback(color, piececode);
	chssBoard.engine.think();
	chssBoard.moduleManager.redraw();
}

promotionPopUp.prototype.getColor = function()
{
	return this._color;
}