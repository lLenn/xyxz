function boardElement(parentDiv, centerToScreen)
{
	this._parentDiv = parentDiv;
	this._centerToScreen = centerToScreen;
	if(this._centerToScreen !== true)
		this._centerToScreen = false;

	this._rows = [null, null, null, null, null, null, null, null];
	this._board = undefined;
	this._wrapper = undefined;
	this._buttonWrapper = undefined;
	this._buttonHeight = undefined;
	this._buttonWidth = undefined;
	this._boardBackground = undefined;
	this._dragElement = undefined;
	this._promotionPopUp = undefined;
	this._colorIndicator = undefined;
	this._variationPopUp = undefined;
	this._statusImage = undefined;
	this._moves = undefined;
	this._upload = undefined;
	this._engine = undefined;
	this._actions = undefined;
	this._commentArea = undefined;
	this._change = undefined;
	this._flip = false;
	this._coloredFields = new Array();
	this._selectedFields = new Array();
	this._lastPath = new Array();
	this._load = undefined;
	this._prevPositionBoard = undefined;
	this._enlarged = false;
}

boardElement.prototype.draw = function()
{
	this._parentDiv.id = "parent";
	
	this._wrapper = document.createElement("div")
	this._wrapper.id = "boardWrapper";
	this._wrapper.className = "unselectable";
	this._wrapper.style.fontSize = chssOptions.font_size + "px";
	this._boardBackground = document.createElement("div");
	this.loadBackground();
	
	this._board = document.createElement("div");
	this._board.id = "boardElement";
	this._board.style.position = "relative";
	for(var i=0;i<8;i++)
	{
		this._rows[i] = new rowElement(i);
		this._board.appendChild(this._rows[i].getRow());
	}
	
	this._boardBackground.appendChild(this._board);
	this._wrapper.appendChild(this._boardBackground);

	this.addMovesList();
	this.addActions();
	this.addChange();
	this.addDragElement();
	this.addEngine();
	this.dragging();
	this.addKeyBindings();
	this.addCommentArea();
	this.addColorIndicator();
	this.addPopUps();
	this.addStatusImage();

	this._load = new chssLoadScreen();
	this._wrapper.appendChild(this._load.getWrapper());

	this._parentDiv.appendChild(this._wrapper);

	//this._upload = new uploadElement();
	//document.body.appendChild(this._upload.getUploadElement());
}

boardElement.prototype.resize = function(width, height, beforeLoad, unsize)
{
	if(typeof beforeLoad === "undefined")
		beforeLoad = false;
	
	if(typeof unsize === "undefined")
		unsize = false;
	
	var wrapperVer = 0,
		parentHeight = height;
	
	if(height>width)
	{
		height = this._wrapper.offsetHeight * width/this._wrapper.offsetWidth;
		wrapperVer = (window.innerHeight - height)/2;
	}
	
	//HACK ONLY FUNCTIONAL FOR RESIZE AFTER INITIATION AND FULLSCREEN ON/OFF
	if((height/width)/(this._wrapper.offsetHeight/this._wrapper.offsetWidth) > 1.1 && !unsize)
	{
		this.resize(width, height * this._wrapper.offsetWidth/this._wrapper.offsetHeight, beforeLoad);
		return;
	}
	
	var old_board_size = chssOptions.board_size;
	var verDiff = (this._wrapper.offsetHeight - old_board_size) * (height/this._wrapper.offsetHeight);
	var board_size = Math.floor(height - verDiff);
	chssOptions.board_size = Math.floor(board_size / 8) * 8;
	var paddingVer = (height - (chssOptions.board_size + verDiff))/2;
	if(wrapperVer === 0)
		wrapperVer = paddingVer

	var paddingHor = paddingVer;
	var diffCoeff = chssOptions.board_size/old_board_size;
	chssOptions.moves_size = width - (this._boardBackground.offsetWidth * (diffCoeff)) - paddingVer;
	if(chssOptions.board_size*1.1<chssOptions.moves_size)
	{
		paddingHor = (chssOptions.moves_size + paddingVer - (chssOptions.board_size*1.1))/2;
		chssOptions.moves_size = chssOptions.board_size*1.1;
	}
	this._parentDiv.style.height = parentHeight + "px";
	this._parentDiv.style.width = width + "px";
	this._wrapper.style.marginTop = wrapperVer + "px";
	this._wrapper.style.marginBottom = wrapperVer + "px";
	this._wrapper.style.marginLeft = paddingHor + "px";
	this._wrapper.style.marginRight = paddingHor + "px";
	
	chssOptions.font_size = chssOptions.font_size * diffCoeff;
	this._wrapper.style.fontSize = chssOptions.font_size + "px";
	
	if(!beforeLoad)
		this.drawSize(diffCoeff);
	else
		this.drawWrapper(diffCoeff);
}

boardElement.prototype.drawWrapper = function(resize)
{
	var backgroundImage = chssOptions.images_url + "chessboard/Board" + (this.getFlip()?"_flip":"") + ".png";
	var background = new Image();
	background.src = backgroundImage;
	
	var width = background.width * (chssOptions.board_size/360);
	var height = background.height * (chssOptions.board_size/360);
	
	this._boardBackground.style.width = width + "px";
	this._boardBackground.style.height = height + "px";

	this._moves.getMoves().style.width = chssOptions.moves_size + "px";
	this._moves.getMoves().style.left = width + "px";
	if(resize === false)
	{
		this._moves.getMoves().style.top = (height * 0.6) + "px";
		this._moves.getMoves().style.height = (height * 0.4) + "px";
	}
	else
	{
		this._moves.getMoves().style.top = parseFloat(this._moves.getMoves().style.top) * resize + "px";
		this._moves.getMoves().style.height = parseFloat(this._moves.getMoves().style.height) * resize + "px";
	}

	this._actions.getSubBoardElement().style.width = Math.floor(width * 10) / 10 + "px";
	this._actions.getSubMovesElement().style.width = Math.floor(chssOptions.moves_size * 10) / 10 + "px";
	this._actions.getActionsElement().style.width = width + chssOptions.moves_size + "px";
	this._actions.drawSize(resize);
}

boardElement.prototype.drawSize = function(resize)
{	
	if(typeof resize == "undefined")
		resize = false;
	
	this.drawWrapper(resize);
	
	if(resize === false)
	{
		this.centerToScreen();
		
		if(chssBoard.mobileManager.isMobile())
			this.fullscreen(true);
		this._load.show();
	}

	var backgroundImage = chssOptions.images_url + "chessboard/Board" + (this.getFlip()?"_flip":"") + ".png";
	var background = new Image();
	background.src = backgroundImage;
	
	var width = background.width * (chssOptions.board_size/360);
	var height = background.height * (chssOptions.board_size/360);

	var parentRect = getPageOffset(this._wrapper);
	chss_global_vars.parentTop = parentRect.top;
	chss_global_vars.parentLeft = parentRect.left;
	
	var boardTop = (height - chssOptions.board_size)/2,
		boardLeft = (width - chssOptions.board_size)/2;
	
	this._board.style.width = chssOptions.board_size + "px";
	this._board.style.height = chssOptions.board_size + "px";
	this._board.style.top = boardTop + "px";
	this._board.style.left = boardLeft + "px";
	
	for(var i=0;i<8;i++)
	{
		this._rows[i].drawSize();
	}

	this._commentArea.getWrapper().style.width = chssOptions.moves_size + "px";
	this._commentArea.getWrapper().style.left = width + "px";
	if(resize === false)
	{
		this._commentArea.setHeight(chssCommentArea.SMALL, height * 0.6);
		this._commentArea.draw();
	}
	else
	{
		this._commentArea.resize(resize);
	}
	
	this._change.getWrapper().style.width = Math.floor(chssOptions.moves_size * 10) / 10 + "px";
	if(resize === false)
		this._change.getWrapper().style.top = height + "px";
	else
		this._change.getWrapper().style.top = parseFloat(this._change.getWrapper().style.top) * resize + "px";
	this._change.getWrapper().style.left = width + "px";
	
	var size = 24 * (chssOptions.board_size/360);
	this._colorIndicator.setSize(size);

	this._colorIndicator.getPiece().style.top = height + "px";
	this._colorIndicator.getPiece().style.left = "0px";
	
	this._dragElement.drawSize();
	this._promotionPopUp.drawSize();
	this._variationPopUp.drawSize();
	this._change.drawSize(resize);

	var temp = document.createElement("div");
	temp.className = "buttonWrapper";
	this._wrapper.appendChild(temp);

	this._buttonWidth = chssOptions.moves_size - parseFloat(chssHelper.getComputedStyle(temp, "padding-left")) - parseFloat(chssHelper.getComputedStyle(temp, "padding-right"));
	this._buttonHeight = 40 * (chssOptions.board_size/360);

	this._wrapper.removeChild(temp);
	chssBoard.moduleManager.redraw(true);
	if(resize)
	{
		this._statusImage.getImageElement().style.width = chssOptions.moves_size + "px";
		this._statusImage.getImageElement().style.top = parseFloat(this._statusImage.getImageElement().style.top) * resize + "px";
		this._statusImage.getImageElement().style.left = parseFloat(this._boardBackground.style.width) + "px";
		this._statusImage.resize(resize);
		
		chssBoard.moduleManager.resize(resize);
	}
}

boardElement.prototype.centerToScreen = function()
{	
	if(this._centerToScreen)
	{
	    var wWidth = window.innerWidth,
	    	wHeight = window.innerHeight,
	    	wWidthDiv = wWidth/2,
	    	wHeightDiv = wHeight/2,
	    	parentRect = getPageOffset(this._parentDiv),
	    	bHeight = this._wrapper.offsetHeight,
	    	bWidth = this._wrapper.offsetWidth,
	    	bHeightDiv = bHeight/2,
	    	bWidthDiv = bWidth/2,
	    	bodyHeight = document.body.clientHeight,
	    	bodyWidth = document.body.clientWidth,
	    	marginLeft = 50,
	    	marginRight = 50,
	    	marginTop = 25,
	    	marginBottom = 25;
	    
	    /*
	    if(wHeight > parentRect.top + bHeight + marginTop + marginBottom && bodyHeight <= wHeight && wHeightDiv - bHeightDiv - 50 > parentRect.top)
	    {
	    	marginTop = -(parentRect.top - wHeightDiv) - bHeightDiv - 50;
	    	marginBottom = wHeight - bodyHeight - marginTop;
	    }
	    */
	    
	    if(wWidth > parentRect.left + bWidth + marginLeft + marginRight && bodyWidth <= wWidth && wWidthDiv - bWidthDiv > parentRect.left)
	    {
	    	marginLeft = -(parentRect.left - wWidthDiv) - bWidthDiv;
	    }
	
	    this._parentDiv.style.padding = marginTop + "px " + marginRight + "px " + marginBottom + "px " + marginLeft + "px";
	}
}

boardElement.prototype.fullscreen = function(beforeLoad)
{
	if(typeof beforeLoad === "undefined")
		beforeLoad = false;
	if(!this._enlarged)
	{
		this._prevPositionBoard = [this._centerToScreen, document.body.style.padding, document.body.style.overflow, this._parentDiv.style.position, this._parentDiv.style.top, this._parentDiv.style.right, this._parentDiv.style.bottom, this._parentDiv.style.left, this._parentDiv.style.padding, this._parentDiv.style.background, this._wrapper.offsetWidth, this._wrapper.offsetHeight, this._engine.getEngineElement().style.display];
	
		this._centerToScreen = false;
		document.body.scrollTop = "0";
		document.body.scrollLeft = "0";
		document.documentElement.scrollTop = "0";
		document.documentElement.scrollLeft = "0";
		document.body.style.padding = "0";
		document.body.style.overflow = "hidden";
		this._parentDiv.style.position = "absolute";
		this._parentDiv.style.top = "0";
		this._parentDiv.style.bottom = "0";
		this._parentDiv.style.left = "0";
		this._parentDiv.style.right = "0";
		this._parentDiv.style.padding = "0";
		this._parentDiv.style.background = chssOptions.background_color;
		this._engine.getEngineElement().style.display = "none";
	
		this.resize(window.innerWidth, window.innerHeight, beforeLoad);
		if(!beforeLoad)
			this._load.hide();
		this._enlarged = true;
	}
	else
	{	
		//console.log(this._prevPositionBoard);
		this._centerToScreen = this._prevPositionBoard[0];
		document.body.style.padding = this._prevPositionBoard[1];
		document.body.style.overflow = this._prevPositionBoard[2];
		this._parentDiv.style.position = this._prevPositionBoard[3];
		this._parentDiv.style.top = this._prevPositionBoard[4];
		this._parentDiv.style.right = this._prevPositionBoard[5];
		this._parentDiv.style.bottom = this._prevPositionBoard[6];
		this._parentDiv.style.left = this._prevPositionBoard[7];
		this._parentDiv.style.padding = this._prevPositionBoard[8];
		this._parentDiv.style.background = this._prevPositionBoard[9];
		this._engine.getEngineElement().style.display = this._prevPositionBoard[12];
	
		this.resize(this._prevPositionBoard[10], this._prevPositionBoard[11], false, true);
		this._enlarged = false;
		document.body.scrollTop = getPageOffset(this._parentDiv).top;
		document.documentElement.scrollTop = getPageOffset(this._parentDiv).top;
	}
	this._actions.getEnlargeButton().setEnlarge(this._enlarged);
}

boardElement.prototype.addKeyBindings = function()
{
	this._parentDiv.onclick = function(event)
	{
		event.currentTarget.focus();
	}
	
	this._wrapper.onkeydown = function(event)
	{
		var key = event.which || event.keyCode;
		if(key == 39)
		{
			chssBoard.moduleManager.actionChangeBoard("+1", false);
		}
		else if(key == 37)
		{
			chssBoard.moduleManager.actionChangeBoard("-1", false);
		}
		
		if(key == 39 || key == 37)
			event.preventDefault();
	}
}

boardElement.prototype.redraw = function()
{
	var board = chssBoard.chssGame.getBoard();
	for(var i=0;i<8;i++)
	{
		for(var j=0;j<8;j++)
		{
			var pieceElement = this._rows[i].getPiece(j);
			var k = this._flip?7-i:i;
			var l = this._flip?7-j:j;
			if(
					!(
						pieceElement.getChssPiece() == null && board[k][l] == null
					) 
				&&
					(
							(
								pieceElement.getChssPiece() == null && board[k][l] != null
							)
						||
							(
								pieceElement.getChssPiece() != null && board[k][l] == null
							)
						||
							pieceElement.getChssPiece().getColor() != board[k][l].getColor() 
						|| 
							pieceElement.getChssPiece().getPiececode() != board[k][l].getPiececode()
						||
							pieceElement.getChssPiece().getAvailableMoves().length != board[k][l].getAvailableMoves().length	
					)
				)
			{
				pieceElement.setChssPiece(board[k][l]);
				pieceElement.draw();
			}
		}
	}
	
	//chss_global_vars.localClick = false;
	if(this._colorIndicator)
	{
		this._colorIndicator.draw(chssBoard.chssGame.active(false), "k");
	}
}

boardElement.prototype.redrawMoves = function(top, height)
{
	this._moves.getMoves().style.top = top + "px";
	this._moves.getMoves().style.height = height - top + "px";
	top += this._moves.getMoves().offsetHeight;
	
	this._change.getWrapper().style.top = top + "px";
}

boardElement.prototype.drawAvailableMoves = function(x, y, available)
{
	x = this._flip?7-x:x;
	y = this._flip?7-y:y;
	if(chssOptions.show_selected_piece)
	{
		this._rows[y].getPiece(x).selectedPiece(true, '#479203');
		this._selectedFields.push([x, y]);
	}
	
	if(chssOptions.show_possible_moves)
	{
		for(var i=0; i<available.length; i++)
		{
			x = this._flip?7-available[i][0]:available[i][0];
			y = this._flip?7-available[i][1]:available[i][1];
			this._rows[y].getPiece(x).availableMove(true, '#479203');
			this._coloredFields.push([x, y]);
		}	
	}
}

boardElement.prototype.removeAvailableMoves = function()
{
	while(this._selectedFields.length != 0)
	{
		var arr = this._selectedFields.pop();
		this._rows[arr[1]].getPiece(arr[0]).selectedPiece(false);
	}
	
	while(this._coloredFields.length != 0)
	{
		var arr = this._coloredFields.pop();
		this._rows[arr[1]].getPiece(arr[0]).availableMove(false);		
	}
	
}

boardElement.prototype.flipAvailableMoves = function()
{
	var selectedFields = new Array();
	for(var i=0; i<this._selectedFields.length; i++)
		selectedFields.push([7-this._selectedFields[i][0], 7-this._selectedFields[i][1]]);
	var coloredFields = new Array();
	for(var i=0; i<this._coloredFields.length; i++)
		coloredFields.push([7-this._coloredFields[i][0], 7-this._coloredFields[i][1]]);
	
	this.removeAvailableMoves();
	
	for(var i=0; i<selectedFields.length; i++)
		this._rows[selectedFields[i][1]].getPiece(selectedFields[i][0]).selectedPiece(true);
	for(var i=0; i<coloredFields.length; i++)
		this._rows[coloredFields[i][1]].getPiece(coloredFields[i][0]).availableMove(true);
	this._selectedFields = selectedFields;
	this._coloredFields = coloredFields;
}

boardElement.prototype.dragging = function()
{
	var object = this;
	this._board.onmousemove = function(event)
	{		
		if(chss_global_vars.dragging)
		{
			var dragElement = object.getDragElement().getWrapper();
			
			dragElement.style.top = event.pageY - chss_global_vars.parentTop - (22.5 * (chssOptions.board_size/360)) + "px";
			dragElement.style.left = event.pageX - chss_global_vars.parentLeft - (22.5 * (chssOptions.board_size/360)) + "px";
		}
	};
}

boardElement.prototype.addDragElement = function()
{
	this._dragElement = new dragElement()
	this._wrapper.appendChild(this._dragElement.getWrapper());
}

boardElement.prototype.addColorIndicator = function()
{
	this._colorIndicator = new chssPieceAbstract();
	this._colorIndicator.getPiece().style.position = "absolute";
	this._colorIndicator.getPiece().style.cursor = "pointer";
	this._colorIndicator.getPiece().title = chssLanguage.translate(564);
	this._colorIndicator.draw(chssBoard.chssGame.active(false), "k");
	
	this._wrapper.appendChild(this._colorIndicator.getPiece());
}

boardElement.prototype.addChange = function()
{
	this._change = new chssChange();
	this._change.getWrapper().style.position = "absolute";
	this._wrapper.appendChild(this._change.getWrapper());
}

boardElement.prototype.loadBackground = function()
{
	var backgroundImage = chssOptions.images_url + "chessboard/Board" + (this.getFlip()?"_flip":"") + ".png";
	this._boardBackground.style.backgroundImage = "url('" + backgroundImage + "')";
	this._boardBackground.style.backgroundSize = "cover";
}

boardElement.prototype.addPopUps = function()
{
	this._promotionPopUp = new promotionPopUp();
	this._wrapper.appendChild(this._promotionPopUp.getPromotionPopUp());
	
	this._variationPopUp = new variationPopUp();
	this._wrapper.appendChild(this._variationPopUp.getVariationPopUp());
}

boardElement.prototype.addMovesList = function()
{
	var border = 2 * (chssOptions.board_size/360);
	
	this._moves = new movesElement();
	this._moves.getMoves().style.position = "absolute";
	this._moves.getMoves().style.top = "0px";
	this._moves.getMoves().style.margin = "0";
	this._wrapper.appendChild(this._moves.getMoves());
	this._moves.changeMovesText(true);	
}

boardElement.prototype.playFirstMove = function()
{
	chssBoard.moduleManager.changeBoard("Start");
	var lastMove = chssBoard.chssGame.getPGNFile().getLastMove();
	if(lastMove != null && !isNaN(lastMove.getX1()) && !isNaN(lastMove.getY1()) && !isNaN(lastMove.getX2()) && !isNaN(lastMove.getY2()))
	{
		var x1 = this._flip?7-lastMove.getX1():lastMove.getX1();
		var y1 = this._flip?7-lastMove.getY1():lastMove.getY1();
		var x2 = this._flip?7-lastMove.getX2():lastMove.getX2();
		var y2 = this._flip?7-lastMove.getY2():lastMove.getY2();

		this.setPiece(this.getPiece(x2, y2), x1, y1)
		this.setPiece(null, x2, y2)
		var obj = this;
		setTimeout(function(){ obj.playNextMove(true, false) }, 100);
	}
}

boardElement.prototype.playNextMove = function(firstMove, variation) //default: firstMove = false, variation = false
{
	var nextMove = null;
	var lastMove = chssBoard.chssGame.getPGNFile().getLastMove();
	if(firstMove) nextMove = lastMove;
	if(nextMove == null) nextMove = chssBoard.chssGame.getNextMove(variation);
	if(nextMove != null)
	{
		var x1 = this._flip?7-nextMove.getX1():nextMove.getX1();
		var y1 = this._flip?7-nextMove.getY1():nextMove.getY1();
		var x2 = this._flip?7-nextMove.getX2():nextMove.getX2();
		var y2 = this._flip?7-nextMove.getY2():nextMove.getY2();
		if(this.getPiece(x1, y1) != null)
		{
			this._rows[y1].getPiece(x1).removePieceImage();
			var dragElement = this._dragElement;
			var prevMode = chssBoard.moduleManager.getMode();
			chssBoard.moduleManager.setMode(chssModuleManager.modes.VIEW_MODE);
			dragElement.move(x1, y1, x2, y2, this.getPiece(x1, y1).getColor(), this.getPiece(x1, y1).getPiececode(), 1250, function()
			{
				if(firstMove && lastMove != null)
					chssBoard.moduleManager.changeBoard("Start", false);
				else
					chssBoard.moduleManager.changeBoard("+1", false);

				if(prevMode == chssModuleManager.modes.PLAY_PUZZLE_MODE && chssBoard.moduleManager.checkEndGame())
				{
					chssBoard.moduleManager.setMode(prevMode);
					chssBoard.moduleManager.getModule().processValidation();
				}

				dragElement.getWrapper().style.display = "none";
				chssBoard.moduleManager.setMode(prevMode);
			});
		}
	}
}

boardElement.prototype.drawPath = function(path)
{	
	this.drawPathCoord(path);
}

boardElement.prototype.drawPathCoord = function(path, draw)
{
	if(typeof draw === 'undefined')
		draw = true;

	for(var i=0, len=path.length;i<len;i++)
	{
		var coor = path[i],
			x = (this._flip && draw)?7-coor[0]:coor[0],
			y = (this._flip && draw)?7-coor[1]:coor[1];

		if(i==len-1)
			this._rows[y].getPiece(x).selectedPiece(draw, "#7AA228");
		else
			this._rows[y].getPiece(x).availableMove(draw, "#7AA228");
		
		if(draw)
			this._lastPath.push([x, y]);
	}
}

boardElement.prototype.flipPath = function()
{
	var newPath = new Array();
	for(var i=0, len=this._lastPath.length;i<len;i++)
	{
		var coor = this._lastPath[i],
			x = (this._flip)?coor[0]:7-coor[0],
			y = (this._flip)?coor[1]:7-coor[1];
		newPath.push([x, y]);
	}
	this.removePath()
	this.drawPath(newPath);
}

boardElement.prototype.removePath = function()
{	
	this.drawPathCoord(this._lastPath, false);
	this._lastPath = new Array();
}

boardElement.prototype.addEngine = function()
{
	this._engine = new engineElement();
	this._engine.getEngineElement().style.position = "absolute";
	this._engine.getEngineElement().style.display = "none";
	this._wrapper.appendChild(this._engine.getEngineElement());
}

boardElement.prototype.addCommentArea = function()
{
	this._commentArea = new chssCommentArea();
	this._commentArea.getWrapper().style.position = "absolute";
	this._commentArea.getWrapper().style.top = "0px";
	this._commentArea.getWrapper().style.margin = "0";
	this._wrapper.appendChild(this._commentArea.getWrapper());
}

boardElement.prototype.addActions = function()
{
	this._actions = new actionsElement();
	this._wrapper.appendChild(this._actions.getActionsElement());
}

boardElement.prototype.addStatusImage = function()
{
	this._statusImage = new chssStatusImage();
	this._statusImage.getImageElement().style.position = "absolute";
	this._statusImage.getImageElement().style.display = "none";
	this._wrapper.appendChild(this._statusImage.getImageElement());
}

boardElement.prototype.clearDrag = function()
{
	dragElement = chssBoard.board.getDragElement();
	
	chss_global_vars.dragging = false;
	chss_global_vars.localClick = false;
	chss_global_vars.cancelDrag = false;
	chss_global_vars.prevSelectedX = undefined;
	chss_global_vars.prevSelectedY = undefined;
	chss_global_vars.selectedX = undefined;
	chss_global_vars.selectedY = undefined;
	chss_global_vars.prevScrollTop = undefined;
	chss_global_vars.prevScrollLeft = undefined;
	chss_global_vars.prevDragElement = undefined;
	chss_global_vars.prevDragChssPiece = undefined;
	
	dragElement.getWrapper().style.display = "none";
	dragElement.getWrapper().style.cursor = "auto";
	dragElement.getWrapper().onmousemove = null;
	dragElement.getWrapper().onmouseout = null;
	dragElement.getWrapper().onmouseup = null;
}

boardElement.prototype.getPromotionPopUp = function()
{
	return this._promotionPopUp;
}

boardElement.prototype.getVariationPopUp = function()
{
	return this._variationPopUp;
}

boardElement.prototype.setPromotionPopUp = function(promotionPopUp)
{
	this._promotionPopUp = promotionPopUp;
}

boardElement.prototype.setVariationPopUp = function(variationPopUp)
{
	this._variationPopUp = variationPopUp;
}

boardElement.prototype.getBoard = function()
{
	return this._board;
}

boardElement.prototype.getBackground = function()
{
	return this._boardBackground;
}

boardElement.prototype.getMovesList = function()
{
	return this._moves;
}

boardElement.prototype.getFlip = function()
{
	return this._flip;
}

boardElement.prototype.setFlip = function(flip)
{
	this._flip = flip;
}

boardElement.prototype.rotate = function()
{
	this._flip = !this._flip;
	var backgroundImage = chssOptions.images_url + "chessboard/Board" + (this._flip?"_flip":"") + ".png";
	this._boardBackground.style.backgroundImage = "url('" + backgroundImage + "')";
	chss_global_vars.selectedX = 7-chss_global_vars.selectedX;
	chss_global_vars.selectedY = 7-chss_global_vars.selectedY;
	this.flipAvailableMoves();
	this.flipPath();
	this.redraw();
}

boardElement.prototype.getActions = function()
{
	return this._actions;
}

boardElement.prototype.getPieceElement = function(x,y)
{
	return this._rows[y].getPiece(x).getPiece();
}

boardElement.prototype.getPiece = function(x, y)
{
	return this._rows[y].getPiece(x).getChssPiece();
}

boardElement.prototype.setPiece = function(piece, x, y)
{
	this._rows[y].getPiece(x).setChssPiece(piece);
	this._rows[y].getPiece(x).draw();
}

boardElement.prototype.unselect = function(x, y)
{
	this._rows[y].getPiece(x).availableMove(false);
}

boardElement.prototype.validate = function(x, y, validation)
{
	this._rows[y].getPiece(x).validate(validation);
}

boardElement.prototype.getWrapper = function()
{
	return this._wrapper;
}

boardElement.prototype.getEngine = function()
{
	return this._engine;
}

boardElement.prototype.getCommentArea = function()
{
	return this._commentArea;
}

boardElement.prototype.getDragElement= function()
{
	return this._dragElement;
}

boardElement.prototype.getChange = function()
{
	return this._change;
}

boardElement.prototype.getStatusImage = function()
{
	return this._statusImage;
}

boardElement.prototype.getButtonWidth = function()
{
	return this._buttonWidth;
}

boardElement.prototype.getButtonHeight = function()
{
	return this._buttonHeight;
}

boardElement.prototype.append = function(element)
{
	this._wrapper.insertBefore(element, this._load.getWrapper());
}

boardElement.prototype.appendBefore = function(element, before)
{
	this._wrapper.insertBefore(element, before);
}

boardElement.prototype.loadComplete = function()
{
	this._load.hide();
}

boardElement.prototype.isEnlarged = function()
{
	return this._enlarged;
}