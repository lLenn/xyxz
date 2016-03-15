function chssPieceAbstract()
{
	this._piece = document.createElement("div");
	this._piece.style.position = "relative";
	this._piece.style.fontFamily = "merida";
	this._piece.className = "unselectable";
	
	this._textPiece = document.createElement("div");
	this._textPiece.style.position = "absolute";
	this._textPiece.style.top = "0px";
	this._textPiece.style.left = "0px";
	
	this._textPieceBackground = document.createElement("div");
	this._textPieceBackground.style.position = "absolute";
	this._textPieceBackground.style.top = "0px";
	this._textPieceBackground.style.left = "0px";
	this._textPieceBackground.style.color = chssOptions.alt_color;

	this._piece.appendChild(this._textPieceBackground);
	this._piece.appendChild(this._textPiece);
}

chssPieceAbstract.prototype = {
		setSize: function(size)
		{
			this._piece.style.width = size + "px";
			this._piece.style.height = size + "px";
			this._piece.style.fontSize = size + "px";
		},
		
		draw: function(color, piececode)
		{
			this._textPiece.innerHTML = chssPiece.piececodeToUnicode(color, piececode);
			this._textPieceBackground.innerHTML = chssPiece.piececodeToUnicodeBackground(piececode);
		},

		getPiece: function()
		{
			return this._piece;
		}
}