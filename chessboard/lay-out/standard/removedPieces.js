function chssRemovedPieces(removedPieces)
{
	this._removedPieces = removedPieces;
	
	this._wrapper = document.createElement("div");
	
	/*
	this._label = document.createElement("div");
	this._label.innerHTML = "[$string.removed_pieces]:";
	*/
	
	this._whiteRow = document.createElement("div");
	this._whiteBr = document.createElement("br");
	this._whiteBr.className = "clearfloat";
	
	this._blackRow = document.createElement("div");
	this._blackBr = document.createElement("br");
	this._blackBr.className = "clearfloat";
	
	//this._wrapper.appendChild(this._label);
	this._wrapper.appendChild(this._whiteRow);
	//this._wrapper.appendChild(this._whiteBr);
	this._wrapper.appendChild(this._blackRow);
	//this._wrapper.appendChild(this._blackBr);
}

chssRemovedPieces.prototype = {
		constructor: chssRemovedPieces,
		getWrapper: function()
		{
			return this._wrapper;
		},
		
		draw: function()
		{
			this._whiteRow.innerHTML = "";
			this._whiteRow.style.height = chssOptions.font_size*1.5 + "px";
			this._blackRow.innerHTML = "";
			this._blackRow.style.height = chssOptions.font_size*1.5 + "px";
			
			for(var i=0, len=this._removedPieces.length; i<len; i++)
			{
				var removedPiece = this._removedPieces[i].piece,
					length = this._removedPieces[i].length,
					color = removedPiece.getColor(),
					piececode = removedPiece.getPiececode(),
					piece = new chssPieceAbstract(); 
				
				if(color.toUpperCase() == "W")
					this._whiteRow.appendChild(this.drawCell(piece, length));
				else
					this._blackRow.appendChild(this.drawCell(piece, length));
				
				piece.draw(color, piececode);
			}
		},
		
		drawCell: function(piece, length)
		{
			var wrapper = document.createElement("div"),
				label = document.createElement("div");
			
			wrapper.style.display = "inline-block";
			piece.getPiece().style.float = "left";
			piece.setSize(chssOptions.font_size*1.5);
			label.style.float = "left";
			label.style.top = "0";
			label.innerHTML = "x" + length;
			
			wrapper.appendChild(piece.getPiece());
			if(length > 1)
				wrapper.appendChild(label);
			return wrapper;
		},
		
		setRemovedPieces: function(removedPieces)
		{
			this._removedPieces = removedPieces;
		}
}