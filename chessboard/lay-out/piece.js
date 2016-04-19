var proxyDraw = chssPieceAbstract.prototype.draw;

pieceElement.validation = {CORRECT: "correct",
						   CAUTION: "caution",
						   WRONG: "wrong",
						   NONE: "none"}

function pieceElement(x, y)
{
	chssPieceAbstract.call(this);
	
	this._chssPiece = null;
	this._x = x;
	this._y = y;

	this._piece.id = "pieceElement_" + this._x + "_" + this._y;
	this._piece.className = "pieceElement";
	
	this._selected = document.createElement("div");
	this._selected.style.position = "absolute";
	this._selected.style.display = "none";
	this._selected.style.top = "0px";
	this._selected.style.left = "0px";
	
	this._marking = document.createElement("div");
	this._marking.style.position = "absolute";
	this._marking.style.display = "none";
	this._marking.style.backgroundColor = "#479203";
	this._marking.style.top = "0px";
	this._marking.style.left = "0px";
	
	this._correct = document.createElement("div");
	this._correct.style.position = "absolute";
	this._correct.style.display = "none";
	this._correct.style.top = "0px";
	this._correct.style.left = "0px";
	this._correct.className = "validation correct";
	this._correct.innerHTML = "&#xE013;"
	
	this._wrong = document.createElement("div");
	this._wrong.style.position = "absolute";
	this._wrong.style.display = "none";
	this._wrong.style.top = "0px";
	this._wrong.style.left = "0px";
	this._wrong.className = "validation";
	this._wrong.innerHTML = "&#xE014;"
	
	this._piece.appendChild(this._marking);
	this._piece.appendChild(this._selected);
	this._piece.appendChild(this._correct);
	this._piece.appendChild(this._wrong);
	this.dragStart(this, this.onDrag, this.onDrop);
	this.onClick(this);
	this.onMouseOver(this);
	this.draw();
}
pieceElement.prototype = Object.create(chssPieceAbstract.prototype);
pieceElement.prototype.constructor = pieceElement;

pieceElement.prototype.getChssPiece = function()
{
	return this._chssPiece;
}

pieceElement.prototype.setChssPiece = function(chssPiece)
{
	this._chssPiece = chssPiece;
}

pieceElement.prototype.drawSize = function()
{
	var size = Math.floor(chssOptions.board_size/8),
		border = Math.ceil(2 * (chssOptions.board_size/360)),
		margin = Math.ceil(15 * (chssOptions.board_size/360));
	
	this.setSize(size);
	
	this._selected.style.height = (size - border*2) + "px";
	this._selected.style.width = (size - border*2) + "px";
	this._selected.style.border = border + "px solid green";
	
	this._marking.style.margin = margin + "px";
	this._marking.style.height = (size - margin*2) + "px";
	this._marking.style.width = (size - margin*2) + "px";
	
	this._correct.style.fontSize = (size - border*2) + "px";
	this._correct.style.margin = border + "px";
	
	this._wrong.style.fontSize = (size - border*2) + "px";
	this._wrong.style.margin = border + "px";
}

pieceElement.prototype.draw = function()
{
	if(this._chssPiece != null)
	{
		proxyDraw.call(this, this._chssPiece.getColor(), this._chssPiece.getPiececode());
		this._piece.style.cursor = "pointer";
	}
	else
	{
		this.removePieceImage();
	}
}
pieceElement.prototype.onClick = function(element)
{
	element.getPiece().onclick = function(event)
	{
		if(chssBoard.moduleManager.getSubMode() == chssModuleManager.subModes.ADD_MARKING)
		{
			if(element.isMarked())
			{
				element.availableMove(false);
				chssBoard.moduleManager.removedMarking(element.getX(), element.getY());
			}
			else
			{
				element.availableMove(true);
				chssBoard.moduleManager.addedMarking(element.getX(), element.getY());
			}
		}
	}
}

pieceElement.prototype.onMouseOver = function(element)
{
	element.getPiece().onmouseover = function(event)
		{ 
			chss_global_vars.prevCursor = event.currentTarget.style.cursor;
			if(chssBoard.moduleManager.getSubMode() != chssModuleManager.subModes.ADD_MARKING)
			{
				if(chss_global_vars.localClick)
					event.currentTarget.style.cursor = "pointer";
				else if(chssBoard.moduleManager.getMode() == chssModuleManager.modes.VIEW_MODE)
					event.currentTarget.style.cursor = "default";
				else
					event.currentTarget.style.cursor = chss_global_vars.prevCursor;
			}
			else
				event.currentTarget.style.cursor = "pointer";
		}
	element.getPiece().onmouseout = function(event)
	{
		event.currentTarget.style.cursor = chss_global_vars.prevCursor;
	}
}

pieceElement.prototype.dragStart = function(element, callbackDrag, callbackDrop)
{
	element.getPiece().onmousedown = function(event)
	{
		event.preventDefault();
		
		if(chssBoard.moduleManager.getMode() != chssModuleManager.modes.VIEW_MODE && chssBoard.chssGame.getResult() == chssGame.results.NONE && chssBoard.moduleManager.correctionAllowed())
		{
			if(element.getChssPiece() != null && element.getChssPiece().getColor() == chssBoard.chssGame.active())
			{	
				chssBoard.board.clearDrag();
				chssBoard.board.removeAvailableMoves();
				
				var x = chssBoard.board.getFlip()?7-element.getX():element.getX();
				var y = chssBoard.board.getFlip()?7-element.getY():element.getY();		
				
				chssBoard.board.removePath();
				chssBoard.board.drawAvailableMoves(x, y, element.getChssPiece().getAvailableMoves());
				
				chss_global_vars.prevClientX = event.clientX;
				chss_global_vars.prevClientY = event.clientY;
					
				chss_global_vars.prevSelectedX = x;
				chss_global_vars.prevSelectedY = y;
		
				dragElement = chssBoard.board.getDragElement();
		
				dragElement.getWrapper().style.top = event.pageY - chss_global_vars.parentTop - (22.5 * (chssOptions.board_size/360)) + "px";
				dragElement.getWrapper().style.left = event.pageX - chss_global_vars.parentLeft - (22.5 * (chssOptions.board_size/360)) + "px";
				
				dragElement.getWrapper().style.display = "block";
				dragElement.setPiece(element.getChssPiece().getColor(), element.getChssPiece().getPiececode());
				dragElement.getWrapper().style.cursor = "pointer";
				
				chss_global_vars.prevDragElement = element;
				chss_global_vars.prevDragChssPiece = element.getChssPiece();
				element.setChssPiece(null);
				element.draw();
				
				chss_global_vars.dragging = true;
				
				dragElement.getWrapper().onmousemove = callbackDrag;
				dragElement.getWrapper().onmouseout = callbackDrag;
				dragElement.getWrapper().onmouseup = callbackDrop;
			}
			else if(chss_global_vars.localClick)
			{
				chssBoard.board.removeAvailableMoves();
				chss_global_vars.selectedX = chssBoard.board.getFlip()?7-element.getX():element.getX();
				chss_global_vars.selectedY = chssBoard.board.getFlip()?7-element.getY():element.getY();
				
				element.conditionMove(chss_global_vars.prevSelectedX, chss_global_vars.prevSelectedY, chssBoard.board.getFlip()?7-element.getX():element.getX(), chssBoard.board.getFlip()?7-element.getY():element.getY());
				//chssBoard.moduleManager.addMove(chss_global_vars.prevSelectedX, chss_global_vars.prevSelectedY, chssBoard.board.getFlip()?7-element.getX():element.getX(), chssBoard.board.getFlip()?7-element.getY():element.getY());
				chss_global_vars.localClick = false;
				
			}
		}
	}
}

pieceElement.prototype.onDrag = function(event)
{	
	event.preventDefault();
	
	event.currentTarget.style.top = event.currentTarget.offsetTop + (event.clientY - chss_global_vars.prevClientY) + "px";
	event.currentTarget.style.left = event.currentTarget.offsetLeft + (event.clientX - chss_global_vars.prevClientX) + "px";
	
	chss_global_vars.prevClientX = event.clientX;
	chss_global_vars.prevClientY = event.clientY;

	if(!chss_global_vars.cancelDrag)
	{
		var coords = chssHelper.getBoardCoordFromEvent(event);
		if(chss_global_vars.prevSelectedX != coords.x ||
		   chss_global_vars.prevSelectedY != coords.y)
		{
				chss_global_vars.cancelDrag = true;
		}
	}
}

pieceElement.prototype.onDrop = function(event)
{
	event.preventDefault();

	var coords = chssHelper.getBoardCoordFromEvent(event);
	chss_global_vars.selectedX = coords.x
	chss_global_vars.selectedY = coords.y;
	
	if(chssBoard.board.getFlip())
	{
		chss_global_vars.selectedX = 7-chss_global_vars.selectedX;
		chss_global_vars.selectedY = 7-chss_global_vars.selectedY;
	}
	//chss_global_vars.prevDragElement.style.backgroundImage = event.currentTarget.backgroundImage;
	//chss_global_vars.prevDragElement = undefined;
	
	chss_global_vars.dragging = false;
	event.currentTarget.style.display = "none";
	event.currentTarget.style.cursor = "auto";
	event.currentTarget.onmousemove = null;
	event.currentTarget.onmouseout = null;
	event.currentTarget.onmouseup = null;
	
	if(!(chss_global_vars.prevSelectedX == chss_global_vars.selectedX && chss_global_vars.prevSelectedY == chss_global_vars.selectedY))
	{
		chssBoard.board.removeAvailableMoves();
		chss_global_vars.prevDragElement.conditionMove(chss_global_vars.prevSelectedX, chss_global_vars.prevSelectedY, chss_global_vars.selectedX, chss_global_vars.selectedY);
	}
	else
	{
		chss_global_vars.prevDragElement.setChssPiece(chss_global_vars.prevDragChssPiece);
		chss_global_vars.prevDragElement.draw();
		
		if(chss_global_vars.cancelDrag)
		{
			chssBoard.board.clearDrag();
			chssBoard.board.removeAvailableMoves();
		}
		else
		{
			chss_global_vars.localClick = true;
		}
	}
	
	chss_global_vars.cancelDrag = false;
}

pieceElement.prototype.conditionMove = function(x1, y1, x2, y2)
{
	if(chssBoard.moduleManager.getMode() == chssModuleManager.modes.ADD_MOVES_MODE)
		chssBoard.moduleManager.addMove(x1, y1, x2, y2);
	else if(chssBoard.moduleManager.getMode() == chssModuleManager.modes.PLAY_PUZZLE_MODE || chssBoard.moduleManager.getMode() == chssModuleManager.modes.GAME_PUZZLE_MODE)
		chssBoard.moduleManager.checkMove(x1, y1, x2, y2);
}

pieceElement.prototype.removePieceImage = function()
{
	this._textPiece.innerHTML = "";
	this._textPieceBackground.innerHTML = "";
	this._piece.style.cursor = "auto";
}

pieceElement.prototype.availableMove = function(boolean, color)
{
	if(typeof color === "undefined")
		color = "#479203";
	if(boolean)
	{
		this._marking.style.display = "block";
		this._marking.style.backgroundColor = color;
	}
	else
	{
		this._marking.style.display = "none";
	}
}

pieceElement.prototype.selectedPiece = function(boolean, color)
{	
	if(typeof color === "undefined")
		color = "#479203";
	if(boolean)
	{
		this._selected.style.display = "block";
		this._selected.style.borderColor = color;
	}
	else
	{
		this._selected.style.display = "none";
	}
}

pieceElement.prototype.validate = function(validation)
{	
	switch(validation)
	{
		case pieceElement.validation.NONE:
			this._correct.style.display = "none";
			this._wrong.style.display = "none";
			break;
		case pieceElement.validation.CORRECT: 
			this._correct.style.display = "block";
			this._wrong.style.display = "none";
			break;
		case pieceElement.validation.CAUTION:
			this._wrong.className = "validation caution";
			this._correct.style.display = "none";
			this._wrong.style.display = "block";
			break;
		case pieceElement.validation.WRONG:
			this._wrong.className = "validation wrong";
			this._correct.style.display = "none";
			this._wrong.style.display = "block";
			break;
		default:
			this._correct.style.display = "none";
			this._wrong.style.display = "none";
			break;
	}
}

pieceElement.prototype.isMarked = function()
{
	return this._marking.style.display == "block"
}

pieceElement.prototype.getChssPiece = function()
{
	return this._chssPiece;
}

pieceElement.prototype.setChssPiece = function(piece)
{
	this._chssPiece = piece;
}

pieceElement.prototype.getX = function()
{
	return this._x;
}

pieceElement.prototype.getY = function()
{
	return this._y;
}