function chssStandardModule(args, board, parent)
{
	this._parent = parent;
	this._board = board;
	
	this._pgnfile = null;
	this._drawn = false;
	
	this._removedPieces = undefined;
	this._evaluation = undefined;
}
chssStandardModule.prototype = Object.create(chssModuleManager.prototype);
chssStandardModule.prototype.constructor = chssStandardModule;

chssStandardModule.prototype.initData = function(callback, object)
{
	this._pgnfile = new chssPGNFile();
	this._pgnfile.setFen("rn1qkbnr/pp3ppp/4p3/1b1pP3/3P4/8/PP1K1PPP/RNBQ1BNR w - - 0 1");
	
	setTimeout(function(){ callback.call(object); }, 10);
}

chssStandardModule.prototype.draw = function()
{
	this._removedPieces = new chssRemovedPieces([]);
	this._removedPieces.getWrapper().style.position = "absolute";
	this._board.getWrapper().appendChild(this._removedPieces.getWrapper());
	
	this._evaluation = new chssEvaluation();
	this._evaluation.getWrapper().style.position = "absolute";
	this._evaluation.getWrapper().style.display = "none";
	this._board.getWrapper().appendChild(this._evaluation.getWrapper());
	
	this._drawn = true;
	this.initialDraw();
}

chssStandardModule.prototype.initialDraw = function()
{
	this.resize();
}

chssStandardModule.prototype.resize = function()
{
	var top = 0;
	this._board.getCommentArea().getWrapper().style.display = "none";
	
	this._removedPieces.getWrapper().style.top = top + "px";
	this._removedPieces.getWrapper().style.width = chssOptions.moves_size + "px";
	this._removedPieces.getWrapper().style.left = parseFloat(this._board.getBackground().style.width) + "px";
	this._removedPieces.draw();
	top += this._removedPieces.getWrapper().offsetHeight;

	this._evaluation.getWrapper().style.width = chssOptions.moves_size + "px";
	this._evaluation.getWrapper().style.left = parseFloat(this._board.getBackground().style.width) + "px";
	
	if(this._evaluation.getWrapper().style.display == "block")
	{
		this._evaluation.getWrapper().style.top = top + "px";
		this._evaluation.draw();
		top += this._evaluation.getWrapper().offsetHeight;
	}
	
	this._board.redrawMoves(top, this._board.getBackground().offsetHeight);
}

chssStandardModule.prototype.redraw = function()
{
	var move = chssBoard.chssGame.getMove(),
		removedPieces = move!=null?move.getRemovedPieces():[];

	this._removedPieces.setRemovedPieces(removedPieces);
	this._removedPieces.draw();
	
	this._evaluation.redraw();
}

chssStandardModule.prototype.hide = function(){}
chssStandardModule.prototype.show = function(){}

chssStandardModule.prototype.getInitialMode = function()
{
	return [chssModuleManager.modes.ADD_MOVES_MODE, chssModuleManager.subModes.NOT_ACTIVE, false];
}

chssStandardModule.prototype.getPGNFile = function()
{
	return this._pgnfile;
}

chssStandardModule.prototype.showEngineModule = function(show)
{
	if(show)
	{
		this._board.getEngine().getEngineElement().style.display = "block";
		this._board.getEngine().getEngineElement().style.position = "absolute";
		this._board.getEngine().getEngineElement().style.top = this._board.getWrapper().offsetHeight + "px";
		this._board.getWrapper().style.overflow = "visible";
		document.body.style.overflow = "auto";
	}
}

chssStandardModule.prototype.evaluationDraw = function(draw)
{
	if(draw && this._evaluation.getWrapper().style.display == "none")
	{
		this._evaluation.getWrapper().style.display = "block";
		var top = this._removedPieces.getWrapper().offsetHeight;
		this._evaluation.getWrapper().style.top = top + "px";
		this._evaluation.draw();
		top += this._evaluation.getWrapper().offsetHeight;
		
		this._board.redrawMoves(top, this._board.getBackground().offsetHeight);
	}
	else if(draw)
	{
		this._evaluation.redraw();
	}
	else
	{
		if(this._drawn)
		{
			this._evaluation.getWrapper().style.display = "none";
			this._board.redrawMoves(this._removedPieces.getWrapper().offsetHeight, this._board.getBackground().offsetHeight);
		}
	}
}