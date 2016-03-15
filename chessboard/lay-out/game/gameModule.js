function chssGameModule(args, board, parent)
{
	this._parent = parent;
	this._board = board;

	this._objectSerial = args.objectSerial;
	this._endGame = true;
	this._allowedChange = "minus_one_correction";
	this._allowedAttempts = 2;
	
	this._title = undefined;
	this._description = undefined;
	
	this._pgnfile = null;
	this._callback = null;
	this._object = null;
	
	this._info = undefined;
	this._seperator = undefined;
	this._endGameInfo = undefined;
	
	chssBoard.chssGame.setEdit(this._endGame);
}
chssGameModule.prototype = Object.create(chssModuleManager.prototype);
chssGameModule.prototype.constructor = chssGameModule;

chssGameModule.prototype.initData = function(callback, object)
{
	this._callback = callback;
	this._object = object;
	chssBoard.ajaxRequest.loadData("POST", "ajax/retrieve_data.php", "location=" + (this._endGame?"end_game":"game") + "&object_serial=" + this._objectSerial, this.initGame, this);
}

chssGameModule.prototype.initGame = function(data)
{
	this._pgnfile = chssHelper.loadGameFromJSON(data);
	if(!this._endGame)
	{
		this.loadMetaData(data)
		if(data.game_breaks)
		{
			for(var i=0; i<data.game_breaks.length; i++)
			{
				var gameBreak = data.game_breaks[i],
					move = null;
				
				if(gameBreak.halfMove != 0)
					move = this._pgnfile.getMoves()[gameBreak.halfMove-1];
				else
				{
					this._pgnfile.lastMove = new chssMove(NaN, NaN, NaN, NaN);
					move = this._pgnfile.getLastMove();
				}
	
				move.setIsBreak(true);	
				move.setBreakType(gameBreak.breakType);
				move.setBreakQuestion(gameBreak.breakQuestion);
			}
		}
	}
	else
	{
		this._title = data.end_game_properties.title;
		this._description = data.end_game_properties.description;
		this._pgnfile.setResult(data.end_game_properties.result);
	}
	this._callback.call(this._object);
}

chssGameModule.prototype.draw = function()
{
	this.addInfo();
	this._seperator = new chssSeperator();
	this._seperator.getWrapper().style.position = "absolute";
	this._board.append(this._seperator.getWrapper());
	this.initialDraw();
}

chssGameModule.prototype.initialDraw = function()
{
	if(!this._endGame)
	{
		this._endGameInfo.getWrapper().style.display = "none";
		this._info.getWrapper().style.display = "block";
		
		this._info.draw(chssGameInfo.SMALL, this._board.getBackground().offsetHeight*0.4, this._board.getWrapper().offsetHeight);
		this._info.getWrapper().style.width = chssOptions.moves_size + "px";
		this._info.getWrapper().style.top = "0px";
		this._info.getWrapper().style.left = this._board.getBackground().offsetWidth + "px";
	
		var height = this._info.getWrapper().offsetHeight,
			heightRest = this._board.getBackground().offsetHeight - height;
		
		this._board.getCommentArea().getWrapper().style.height = height + "px";
		
		this._seperator.getWrapper().style.top = height + "px";
		this._seperator.getWrapper().style.left = this._board.getBackground().offsetWidth + "px";
		this._seperator.draw(chssOptions.moves_size);
		
		height = height + this._seperator.getWrapper().offsetHeight;
		heightRest = heightRest - this._seperator.getWrapper().offsetHeight;
		
		this._board.getMovesList().getMoves().style.top = height + "px";
		this._board.getMovesList().getMoves().style.height = heightRest + "px";
	}
	else
	{
		this._board.getStatusImage().getImageElement().style.top = "0px";
		this._board.getStatusImage().getImageElement().style.width = chssOptions.moves_size + "px";
		this._board.getStatusImage().getImageElement().style.left = parseFloat(this._board.getBackground().style.width) + "px";
		this._board.getStatusImage().getImageElement().style.display = "none";
		
		this._info.getWrapper().style.display = "none";
		this._endGameInfo.getWrapper().style.display = "block";
		
		this._endGameInfo.getWrapper().style.width = chssOptions.moves_size + "px";
		this._endGameInfo.getWrapper().style.top = "0px";
		this._endGameInfo.getWrapper().style.left = this._board.getBackground().offsetWidth + "px";
		this._endGameInfo.draw(this._title, this._description, this._pgnfile.getResult(), this._board.getBackground().offsetHeight * 0.45);
		
		var height = this._endGameInfo.getWrapper().offsetHeight,
			heightRest = this._board.getBackground().offsetHeight - this._endGameInfo.getWrapper().offsetHeight;
		
		this._seperator.getWrapper().style. top = height + "px";
		this._seperator.getWrapper().style.left = this._board.getBackground().offsetWidth + "px";
		this._seperator.draw(chssOptions.moves_size);
		
		height = height + this._seperator.getWrapper().offsetHeight;
		heightRest = heightRest - this._seperator.getWrapper().offsetHeight;
		
		this._board.getMovesList().getMoves().style.top = height + "px";
		this._board.getMovesList().getMoves().style.height = heightRest + "px";
		
		this._board.getCommentArea().getWrapper().style.display = "none";
	}
}

chssGameModule.prototype.hide = function()
{
	if(!this._endGame)
		this._info.getWrapper().style.display = "none";
}

chssGameModule.prototype.show = function()
{
	if(!this._endGame)
		this._info.getWrapper().style.display = "block";
}

chssGameModule.prototype.loadMetaData = function(data)
{
	if(data.game_meta_data)
	{
		for(var i=0; i<data.game_meta_data.length;i++)
		{
			var meta_data = data.game_meta_data[i];
			switch(meta_data.key)
			{
				case "Event": this._pgnfile.setEvent(meta_data.value); break;
				case "Site": this._pgnfile.setSite(meta_data.value); break;
				case "Date": var date = meta_data.value.split(".");
							 this._pgnfile.setDate(new Date(parseInt(date[0]), parseInt(date[1])-1, parseInt(date[2])-1, 0, 0, 0, 0));
							 break;
				case "White": this._pgnfile.setWhite(meta_data.value); break;
				case "Black": this._pgnfile.setBlack(meta_data.value); break;
				case "Result": this._pgnfile.setResult(meta_data.value); break;
				case "ECO": this._pgnfile.setEco(meta_data.value); break;
				case "Opening": this._pgnfile.setOpening(meta_data.value); break;
				case "WhiteElo": this._pgnfile.setWhiteElo(meta_data.value); break;
				case "BlackElo": this._pgnfile.setBlackElo(meta_data.value); break;
				case "WhiteTitle": this._pgnfile.setWhiteTitle(meta_data.value); break;
				case "BlackTitle": this._pgnfile.setBlackTitle(meta_data.value); break;
				case "WhiteCountry": this._pgnfile.setWhiteCountry(meta_data.value); break;
				case "BlackCountry": this._pgnfile.setBlackCountry(meta_data.value); break;
				case "FEN":  this._pgnfile.setFen(meta_data.value); break;
				case "Annotator": this._pgnfile.setAnnotator(meta_data.value); break;
				//default: if(this._pgnfile.extraTags[meta_data.key]==null) this._pgnfile.extraTags[meta_data.key] = meta_data.value; break;
			}
		}
	}
}

chssGameModule.prototype.getInitialMode = function()
{
	if(this._endGame)
		return [chssModuleManager.modes.ADD_MOVES_MODE, this._allowedChange, true];
	else
		return [chssModuleManager.modes.VIEW_MODE, chssModule.subModes.NOT_ACTIVE, false];
}

chssGameModule.prototype.getPGNFile = function()
{
	return this._pgnfile;
}

chssGameModule.prototype.addInfo = function()
{

	this._info = new chssGameInfo(this._pgnfile);
	this._info.getWrapper().style.position = "absolute";
	this._board.append(this._info.getWrapper());
	
	this._endGameInfo = new chssEndGameInfo();
	this._endGameInfo.getWrapper().style.position = "absolute";
	this._board.append(this._endGameInfo.getWrapper());
}

chssGameModule.prototype.processResult = function()
{
	switch(chssBoard.chssGame.getResult())
	{
		case chssGame.results.WHITE: this.showResult(chssBoard.chssGame.getPGNFile().getResult()=="1-0" || chssBoard.chssGame.getPGNFile().getResult()=="1/2-1/2"); break;
		case chssGame.results.BLACK: this.showResult(chssBoard.chssGame.getPGNFile().getResult()=="0-1" || chssBoard.chssGame.getPGNFile().getResult()=="1/2-1/2"); break;
		case chssGame.results.DRAW: this.showResult(chssBoard.chssGame.getPGNFile().getResult()=="1/2-1/2"); break;
	}
}

chssGameModule.prototype.processValidation = function()
{
	if(parent.getMode() == chssModuleManager.modes.GAME_PUZZLE_MODE)
	{
		if(this._parent._validation)
		{
			chssBoard.chssGame.changeBreak(false, "");
			if(this._parent._alternative)
				chssBoard.chssGame.setVariationId(this._parent._alternative_var_id);
			this._parent.changeBoard("+1", false);
			parent.redraw(true);
		}
		else
		{
			parent.setMode(chssModuleManager.modes.GAME_PUZZLE_MODE);
			parent.redraw(false);
			console.log("Break puzzle wrong.");
		}

		//var ev:ViewActionEvent = new ViewActionEvent(ViewActionEvent.DRAW_STATUS_IMAGE, [this._validation]);
		//this.dispatchEvent(ev);
	}
}

chssGameModule.prototype.showResult = function(result)
{
	this._endGameInfo.getWrapper().style.display = "none";
	this._info.getWrapper().style.display = "none";
	this._board.getStatusImage().getImageElement().style.display = "block";
	this._board.getStatusImage().switchStatusImage(result?chssStatusImage.GOOD:chssStatusImage.BAD, this._board.getBackground().offsetHeight*0.6);
		
	this._seperator.getWrapper().style.top = this._board.getStatusImage().getImageElement().offsetHeight + "px";
		
	this._board.getMovesList().getMoves().style.top = this._board.getStatusImage().getImageElement().offsetHeight + this._seperator.getWrapper().offsetHeight + "px";
	this._board.getMovesList().getMoves().style.height = this._board.getWrapper().offsetHeight - this._seperator.getWrapper().offsetHeight - this._board.getStatusImage().getImageElement().offsetHeight + "px";
	parent.setMode(chssModuleManager.modes.VIEW_MODE);
	parent.setSubMode(chssModuleManager.subModes.NOT_ACTIVE);
}

chssGameModule.prototype.showEngineModule = function(boolean)
{
	this._board.getEngine().show(boolean);
}

chssGameModule.prototype.getChangeAttempts = function()
{
	return this._allowedAttempts;
}