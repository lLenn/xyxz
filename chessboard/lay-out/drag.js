function dragElement()
{
	this._dragElement = document.createElement("div");
	this._dragElement.style.display = "none";
	this._dragElement.style.position = "absolute";
	
	this._dragElement.id = "dragElement";
	this._dragElement.style.zIndex = 1000;
	this._dragElement.style.backgroundSize = "cover";
	this._dragElement.style.fontFamily = "merida";

	this._textPiece = document.createElement("div");
	this._textPiece.style.position = "absolute";
	this._textPiece.style.top = "0px";
	this._textPiece.style.left = "0px";
	
	this._textPieceBackground = document.createElement("div");
	this._textPieceBackground.style.position = "absolute";
	this._textPieceBackground.style.top = "0px";
	this._textPieceBackground.style.left = "0px";
	this._textPieceBackground.style.color = chssOptions.alt_color;
	
	this._dragElement.appendChild(this._textPieceBackground);
	this._dragElement.appendChild(this._textPiece);
}

dragElement.prototype.drawSize = function()
{
	var size = chssOptions.board_size/8 + "px";
	this._dragElement.style.width = size;
	this._dragElement.style.height = size;
	this._textPiece.style.fontSize = size;
	this._textPieceBackground.style.fontSize = size;
}

dragElement.prototype.getWrapper = function()
{
	return this._dragElement;
}

dragElement.prototype.move = function(x1, y1, x2, y2, color, piececode, time, callback)
{
	var rect = getPageOffset(chssBoard.board.getBoard()),
		top1 = rect.top - chss_global_vars.parentTop + (y1 * 45 * (chssOptions.board_size/360)),
		left1 = rect.left - chss_global_vars.parentLeft + (x1 * 45 * (chssOptions.board_size/360)),
		top2 = rect.top - chss_global_vars.parentTop + (y2 * 45 * (chssOptions.board_size/360)),
		left2 = rect.left - chss_global_vars.parentLeft + (x2 * 45 * (chssOptions.board_size/360)),
		interval = Math.ceil((time/1000)*(1000/120)),
		diffTopInterval = (top2 - top1)/interval,
		diffLeftInterval = (left2 - left1)/interval,
		dragElement = this._dragElement;
	
	this._dragElement.style.top = top1 + "px";
	this._dragElement.style.left = left1 + "px";
	this._dragElement.style.display = "block";
	this._dragElement.style.className = "unselectable";
	this.setPiece(color, piececode);
	
	var id = setInterval(function()
		{
			var currentTop = parseFloat(dragElement.style.top),
				currentLeft = parseFloat(dragElement.style.left),
				nextTop = currentTop + diffTopInterval,
				nextLeft = currentLeft + diffLeftInterval,
				call = false;
			
			if((diffTopInterval >= 0 && diffLeftInterval >= 0 && nextTop >= top2 && nextLeft >= left2) ||
				(diffTopInterval >= 0 && diffLeftInterval <= 0 && nextTop >= top2 && nextLeft <= left2) ||
				(diffTopInterval <= 0 && diffLeftInterval >= 0 && nextTop <= top2 && nextLeft >= left2) ||
				(diffTopInterval <= 0 && diffLeftInterval <= 0 && nextTop <= top2 && nextLeft <= left2))
			{
				clearInterval(id);
				dragElement.style.top = top2 + "px";
				dragElement.style.left = left2 + "px";
				call = true;
			}
			else
			{
				dragElement.style.top = nextTop + "px";
				dragElement.style.left = nextLeft + "px";
			}
			
			if(call)
				setTimeout(callback, 1);
		}, interval);
}

dragElement.prototype.setPiece = function(color, piececode)
{
	this._textPiece.innerHTML = chssPiece.piececodeToUnicode(color, piececode);
	this._textPieceBackground.innerHTML = chssPiece.piececodeToUnicodeBackground(piececode);
}