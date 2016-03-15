function chssPuzzleModule(args, board, parent, custom)
{
	if(typeof custom == "undefined")
		custom = false;
	
	this._parent = parent;
	this._board = board;
	this._custom = custom;
	
	this._temp = false;
	this._neutral = false;
	this._attemptsLeft = undefined;
	
	this._objectSerial = args.objectSerial;
	this._guest = args.guest;
	this._random = args.random;
	this._attempts = 2; //args.attempts;
	this._seconds = 120; //args.seconds;
	
	this._objectCorrect = undefined;
	this._objectWrong = undefined;
	this._setAttempt = undefined;
	
	this._pgnfile = null;
	this._callback = null;
	this._solutionCallback = undefined;
	this._nextCallback = undefined;
	this._object = null;
	this._data = undefined;
	this._drawn = false;
	
	this._timer = undefined;
	this._info = undefined;
	this._action = undefined;
	this._buttonWrapper = undefined;
	this._maxHeight = undefined;
	this._top = undefined;
}
chssPuzzleModule.prototype = Object.create(chssModuleManager.prototype);
chssPuzzleModule.prototype.constructor = chssPuzzleModule;

chssPuzzleModule.prototype.customLoad = function(objectSerial, callback, solutionCallback, nextCallback, object, setId, setAttempt, data)
{
	this._objectSerial = objectSerial;
	this._setId = setId;
	this._setAttempt = setAttempt;
	this._solutionCallback = solutionCallback;
	this._nextCallback = nextCallback;
	if(typeof data == "undefined")
		this.initData(callback, object);
	else
	{
		this._callback = callback;
		this._object = object;
		this.init(data);
	}
}

chssPuzzleModule.prototype.initData = function(callback, object)
{
	if(callback != null)
	{
		this._callback = callback;
		this._object = object;
	}
	//chssBoard.ajaxRequest.loadData("POST", "ajax/retrieve_data.php", "location=puzzle&object_serial=" + this._objectSerial, this.initPuzzle, this);
	chssBoard.ajaxRequest.loadData("POST", "ajax/retrieve_data.php", "location=puzzle&" + (this._random?"random=1":"object_serial=" + this._objectSerial), this.init, this);
}

chssPuzzleModule.prototype.init = function(data)
{
	this._data = data;
	
	this._objectSerial = data.object_serial;
	this._objectCorrect = data.object_correct;
	this._objectWrong = data.object_wrong;
	
	this._pgnfile = chssHelper.loadGameFromJSON(data);
	if(data.puzzle_properties)
	{
		this._pgnfile.setRating(data.puzzle_properties.rating);
		this._pgnfile.setComment(data.puzzle_properties.comment);
		this._pgnfile.setThemes(data.themes);
	}
	
	this._attemptsLeft = this._attempts;
	this._callback.call(this._object);
}

chssPuzzleModule.prototype.sendPuzzleSolution = function(validation, moves, time_left, attempt, custom)
{
	var args = "action=register_new_ratings";
	if(this._custom)
		args = "action=register_new_ratings_set";
	args += "&serial=" + this._objectSerial;
	args += "&serialSolution=" + (validation?this._objectCorrect:this._objectWrong);
	args += "&total_moves=" + moves;
	args += "&time_left=" + time_left;
	if(this._custom && this._setId != null)
	{
		args += "&setId=" + this._setId;
		args += "&setAttempt=" + this._setAttempt;
	}
	chssBoard.ajaxRequest.loadData("POST", "ajax/insert_data.php", args, this.ratingsChanged, this);
}

chssPuzzleModule.prototype.ratingsChanged = function()
{
	console.log("ratings changed");
}

chssPuzzleModule.prototype.getInitialMode = function()
{
	return [chssModuleManager.modes.PLAY_PUZZLE_MODE, chssModuleManager.subModes.DISABLE_CHANGE, false];
}

chssPuzzleModule.prototype.getPGNFile = function()
{
	return this._pgnfile;
}

chssPuzzleModule.prototype.draw = function(top)
{
	if(this._pgnfile.getMoves().length != 0 && 
			(this._pgnfile.getMoves()[0].getNotation() != "..." && this._board.getFlip()) ||
			(this._pgnfile.getMoves()[0].getNotation() == "..." && !this._board.getFlip()))
		this._board.rotate()
	
	if(typeof this._info == 'undefined')
	{
		this.addInfo();
		this.addTimer();
		this.addAction();
		this._drawn = true;
	}
	
	this.initialDraw(top);
	chssBoard.board.playFirstMove();
	this._timer.start();
}

chssPuzzleModule.prototype.initialDraw = function(top)
{
	var offset = this._board.getBackground().offsetWidth,
		moves_size = chssOptions.moves_size;
	
	this._attemptLefts = this._attempts;

	if(typeof top == "undefined")
		this._top = 0;
	else
		this._top = top;

	this._board.getMovesList().getMoves().style.display = "none";
	this._board.getChange().getWrapper().style.display = "none";
	
	this._board.getStatusImage().getImageElement().style.top = this._top + "px";
	this._board.getStatusImage().getImageElement().style.width = moves_size + "px";
	this._board.getStatusImage().getImageElement().style.left = offset + "px";
	this._board.getStatusImage().getImageElement().style.display = "none";
	
	this._buttonWrapper.style.display = "block";
	this._buttonWrapper.style.left = offset + "px";
	this._buttonWrapper.style.bottom = "0px";

	this._action.setSize(this._board.getButtonWidth(), this._board.getButtonHeight());
	this._action.changeState(chssLanguage.translate(539), this.puzzleSolved, this);

	this._maxHeight = this._board.getWrapper().offsetHeight - this._buttonWrapper.offsetHeight - this._top;
	
	this._info.getWrapper().style.width = moves_size + "px";
	this._info.getWrapper().style.display = "block";
	this._info.getWrapper().style.top = this._top + "px";
	this._info.getWrapper().style.left = offset + "px";
	this._info.getWrapper().style.height = offset - this._top + "px";
	this._info.draw(this._pgnfile.getRating(), this._pgnfile.getComment(), this._pgnfile.getThemes(), this._maxHeight);

	this._timer.stop();
	this._timer.reset();
	this.initiateTimer();
	this._timer.onFinish(this.puzzleSolved, this);
}

chssPuzzleModule.prototype.resize = function(diffCoeff)
{
	var prevInfoDisplay = this._info.getWrapper().style.display;
	
	this._buttonWrapper.style.left = parseFloat(this._board.getBackground().style.width) + "px";
	this._buttonWrapper.style.width = chssOptions.moves_size + "px";
	this._action.resize(this._board.getButtonWidth(), this._board.getButtonHeight());
	
	this._top = this._top * diffCoeff;
	this._maxHeight = this._board.getWrapper().offsetHeight - this._buttonWrapper.offsetHeight - this._top;

	this._info.getWrapper().style.display = "block";
	this._info.getWrapper().style.width = chssOptions.moves_size + "px";
	this._info.getWrapper().style.top = this._top + "px";
	this._info.getWrapper().style.left = parseFloat(this._board.getBackground().style.width) + "px";
	this._info.getWrapper().style.height = parseFloat(this._board.getBackground().style.height) - this._top + "px";
	this._info.draw(this._pgnfile.getRating(), this._pgnfile.getComment(), this._pgnfile.getThemes(), this._maxHeight);
	this._info.getWrapper().style.display = prevInfoDisplay;
	
	this.initiateTimer()
}

chssPuzzleModule.prototype.initiateTimer = function()
{
	var leftNegative = 40 * (chssOptions.board_size/360),
		rightNegative = 60 * (chssOptions.board_size/360),
		topNegative = 5 * (chssOptions.board_size/360),
		width = parseFloat(this._board.getBackground().style.width) - leftNegative - rightNegative,
		height = (24 * (chssOptions.board_size/360)) - topNegative*2,
		top = parseFloat(this._board.getBackground().style.height) + topNegative,
		left = leftNegative;
	
	this._timer.initiate(width, height, left, top, this._seconds);

}

chssPuzzleModule.prototype.show = function()
{
	this._board.getStatusImage().getImageElement().style.display = "block";
	this._board.getCommentArea().getWrapper().display = "none";
}

chssPuzzleModule.prototype.hide = function()
{
	this._board.getStatusImage().getImageElement().style.display = "none";
	this._board.getCommentArea().getWrapper().display = "block";
}

chssPuzzleModule.prototype.addAction = function()
{	
	this._buttonWrapper = document.createElement("div");
	this._buttonWrapper.style.position = "absolute";
	this._buttonWrapper.className = "buttonWrapper";
	
	this._action = new bigButton(chssLanguage.translate(524));
	this._buttonWrapper.appendChild(this._action.getWrapper());
	
	this._board.append(this._buttonWrapper);
}

chssPuzzleModule.prototype.showResult = function(result)
{	
	var check = this._maxHeight*0.6;
	
	this._board.getStatusImage().getImageElement().style.display = "block";
	this._board.getStatusImage().switchStatusImage(result?chssStatusImage.GOOD:chssStatusImage.BAD, check);
	this._board.getStatusImage().getImageElement().style.top = this._top + "px";
	this._board.getCommentArea().getWrapper().style.top = this._top + "px";
	this._board.getCommentArea().getWrapper().style.display = "none";
	this._temp = true;
	
	this._board.getMovesList().getMoves().style.top = check + this._top + "px";
	this._board.getMovesList().getMoves().style.height = this._maxHeight * 0.4 + "px";
	
	this._board.getCommentArea().getWrapper().display = "none";
	this._board.getCommentArea().getWrapper().style.top = this._top + "px";
	this._board.getCommentArea().getWrapper().style.display = "block";
	this._board.getCommentArea().getWrapper().style.height = (this._maxHeight * 0.6) + "px";
	this._info.getWrapper().style.display = "none";
	this._parent.setMode(chssModuleManager.modes.VIEW_MODE);
}

chssPuzzleModule.prototype.showStatus = function(status)
{	
	var check = this._maxHeight*0.6;
	
	this._board.getStatusImage().getImageElement().style.display = "block";
	this._board.getStatusImage().switchStatusImage(status, check);
	this._board.getStatusImage().getImageElement().style.top = this._top + "px";
	
	this._info.getWrapper().style.display = "none";
	this._parent.setMode(chssModuleManager.modes.VIEW_MODE);
}

chssPuzzleModule.prototype.addTimer = function()
{
	this._timer = new chssTimer(this._seconds);
	this._board.append(this._timer.getTimerElement());
}

chssPuzzleModule.prototype.addInfo = function()
{
	this._info = new chssPuzzleInfo();
	this._info.getWrapper().style.position = "absolute";
	this._board.append(this._info.getWrapper());
}

chssPuzzleModule.prototype.validatePuzzleSolution = function(boolean)
{
	this._board.getMovesList().getMoves().style.display = "block";
	this._board.getChange().getWrapper().style.display = "block";
	this._parent.redraw(true);
	if(this._random || this._custom)
	{
		this._action.changeState((this._custom?chssLanguage.translate(541):chssLanguage.translate(524)), (this._custom?this._nextCallback:this.initData), (this._custom?this._object:this));
		this._maxHeight = this._maxHeight - this._board.getChange().getWrapper().offsetHeight;
		this._board.getChange().getWrapper().style.top = this._maxHeight + this._top + "px";
	}
	else
		this._buttonWrapper.style.display = "none";
	this.showResult(boolean);
	this._neutral = false;
}

chssPuzzleModule.prototype.showInformation = function()
{
	this._board.getMovesList().getMoves().style.display = "none";
	this._board.getChange().getWrapper().style.display = "none";
	this._info.getWrapper().style.display = "block";
	this._action.changeState(chssLanguage.translate(539), this.puzzleSolved, this);
	this._parent.setMode(chssModuleManager.modes.PLAY_PUZZLE_MODE);
	if(this._neutral)
	{
		this.changeBoard("-1", false);
	}
	this._neutral = false;
	this.redraw(false);
}

chssPuzzleModule.prototype.again = function()
{
	this._action.changeState(chssLanguage.translate(525), this.showInformation, this);
	this.showStatus(chssStatusImage.AGAIN);
	this._board.getStatusImage().getImageElement().style.height = this._board.getBackground().style.height - this._top;
}

chssPuzzleModule.prototype.neutral = function()
{
	this._action.changeState(chssLanguage.translate(525), this.showInformation, this);
	this.showStatus(chssStatusImage.NEUTRAL);
	this._board.getCommentArea().getWrapper().style.top = this._board.getStatusImage().getImageElement().style.height + this._top;
	this._board.getCommentArea().setHeight(this._maxHeight - parseFloat(this._board.getStatusImage().getImageElement().style.height));
	this._board.getCommentArea().getWrapper().style.display = "block";
	this._neutral = true;
}

chssPuzzleModule.prototype.processValidation = function()
{
	var trigger_event = false;
	if(this._parent.getMode() == chssModuleManager.modes.PLAY_PUZZLE_MODE)
	{
		if(this._parent._validation && !this._parent._alternative)
		{
			if(chssBoard.chssGame.getCurrentMove() == chssBoard.chssGame.getMovesLength(true))
			{
				this._parent.setMode(chssModuleManager.modes.VIEW_MODE);
				trigger_event = true;
			}
		}
		else if(this._parent._validation && this._parent._alternative)
		{
			this.neutral();
		}
		else
		{
			if(this._attemptsLeft == 0)
			{
				this._parent.setMode(chssModuleManager.modes.VIEW_MODE);
				trigger_event = true;
			}
			else
			{
				this._attemptsLeft--;
				this._parent.redraw(false);
				this.again();
			}
		}
	}
	
	if(trigger_event)
	{
		this.puzzleSolved(this._parent._validation);
	}
}

chssPuzzleModule.prototype.puzzleSolved =  function(validation)
{
	if(typeof validation == 'undefined')
		validation = false;
	this.sendPuzzleSolution(validation, Math.floor(chssBoard.chssGame.getCurrentMove(false)/2), Math.round(this._timer.getProgressedTime()/1000), this._setAttempt);
	this.validatePuzzleSolution(validation);
	this._timer.stop();
	this._parent.setSubMode(chssModuleManager.subModes.NOT_ACTIVE);
	if(typeof this._solutionCallback != "undefined")
		this._solutionCallback.call(this._object, validation);
}

chssPuzzleModule.prototype.collectGarbage = function()
{
	if(typeof this._info != "undefined")
	{
		this._board.getWrapper().removeChild(this._buttonWrapper);
		this._buttonWrapper = undefined;
		this._board.getWrapper().removeChild(this._timer.getTimerElement());
		this._timer = undefined;
		this._board.getWrapper().removeChild(this._info.getWrapper());
		this._info = undefined;
	}
}

chssPuzzleModule.prototype.top = function(top)
{
	this._top = top;
}

chssPuzzleModule.prototype.getData = function()
{
	return this._data;
}

chssPuzzleModule.prototype.isDrawn = function()
{
	return this._drawn;
}