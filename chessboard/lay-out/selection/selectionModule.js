function chssSelectionModule(args, board, parent, custom)
{
	if(typeof custom == "undefined")
		custom = false;
	this._parent = parent;
	this._board = board;
	this._custom = custom;

	this._objectSerial = args.objectSerial;
	this._objectCorrect = undefined;
	this._objectWrong = undefined;
	this._random = false;
	this._setId = undefined;
	this._setAttempt = undefined;
	
	this._question = undefined;
	this._description = undefined;
	this._selections = undefined;
	
	this._user_selections = undefined;
	
	this._pgnfile = null;
	this._callback = null;
	this._solutionCallback = undefined;
	this._nextCallback = undefined;
	this._object = null;
	this._data = undefined;
	this._drawn = false;
	
	this._info = undefined;
	this._buttonWrapper = undefined;
	this._solution = undefined;
	this._clear = undefined;
	this._top = undefined;
}
chssSelectionModule.prototype = Object.create(chssModuleManager.prototype);
chssSelectionModule.prototype.constructor = chssSelectionModule;

chssSelectionModule.prototype.customLoad = function(objectSerial, callback, solutionCallback, nextCallback, object, setId, setAttempt, data)
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

chssSelectionModule.prototype.initData = function(callback, object)
{
	if(callback != null)
	{
		this._callback = callback;
		this._object = object;
	}

	//chssBoard.ajaxRequest.loadData("POST", "ajax/retrieve_data.php", "location=selection&object_serial=" + this._objectSerial, this.initSelection, this);
	chssBoard.ajaxRequest.loadData("POST", "ajax/retrieve_data.php", "location=selection&" + (this._random?"random=1":"object_serial=" + this._objectSerial), this.init, this);
}

chssSelectionModule.prototype.init = function(data)
{
	this._data = data;
	
	this._objectSerial = data.object_serial;
	this._objectCorrect = data.object_correct;
	this._objectWrong = data.object_wrong;
	
	this._pgnfile = chssHelper.loadGameFromJSON(data);
	
	this._question = data.selection.question;
	this._description = data.selection.description;
	this._selections = new Array();
	
	this._user_selections = new Array();
	
	var selections = data.selection.selections;
	var indexBack = selections.indexOf("/");
	while(indexBack != -1)
	{
		var selection = selections.substr(0, indexBack+1);
		selections = selections.substr(indexBack+1);
		indexBack = selections.indexOf("/");
		
		this._selections.push(new Array(7 - parseInt(selection.substr(0, 1)), 7 - parseInt(selection.substr(1, 1))));
	}

	this._callback.call(this._object);
}

chssSelectionModule.prototype.sendSelectionSolution = function(validation)
{
	var args = "action=register_selection_statistics";
	if(this._custom)
		args = "action=register_selection_set_statistics";
	args += "&serial=" + this._objectSerial;
	args += "&serialSolution=" + (validation?this._objectCorrect:this._objectWrong);
	if(this._custom && this._setId != null)
	{
		args += "&setId=" + this._setId;
		args += "&setAttempt=" + this._setAttempt;
	}
	chssBoard.ajaxRequest.loadData("POST", "ajax/insert_data.php", args, this.solutionRegistered, this);
}

chssSelectionModule.prototype.solutionRegistered = function()
{
	//console.log("Selection solution registered!");
}

chssSelectionModule.prototype.draw = function(top)
{
	if(this._board.getFlip())
		this._board.rotate()
		
	if(typeof this._info === 'undefined')
	{
		this.addSelectionInfo();
		this.addActions();
		this._drawn = true;
	}
	this.initialDraw(top);
}

chssSelectionModule.prototype.addSelectionInfo = function()
{
	this._info = new chssSelectionInfo();
	this._info.getWrapper().style.position = "absolute";
	this._board.append(this._info.getWrapper());
}

chssSelectionModule.prototype.addActions = function()
{
	this._buttonWrapper = document.createElement("div");
	this._buttonWrapper.style.position = "absolute";
	this._buttonWrapper.className = "buttonWrapper";
	
	this._solution = new bigButton(chssLanguage.translate(979));
	this._buttonWrapper.appendChild(this._solution.getWrapper());
	
	this._buttonWrapper.appendChild(document.createElement("br"));
	
	this._clear = new bigButton(chssLanguage.translate(1407));
	this._buttonWrapper.appendChild(this._clear.getWrapper());
	
	this._board.append(this._buttonWrapper);
}

chssSelectionModule.prototype.initialDraw = function(top)
{
	if(typeof top == "undefined")
		this._top = 0;
	else
		this._top = top;
	
	this.clear(true);
	this._info.setText(this._question, this._description);
	
	this._board.getStatusImage().getImageElement().style.top = this._top + "px";
	this._board.getStatusImage().getImageElement().style.width = chssOptions.moves_size + "px";
	this._board.getStatusImage().getImageElement().style.left = parseFloat(this._board.getBackground().style.width) + "px";
	this._board.getStatusImage().getImageElement().style.display = "none";
	
	this._board.getMovesList().getMoves().style.display = "none";
	this._board.getChange().getWrapper().style.display = "none";
	
	this._buttonWrapper.style.bottom = "0px";
	this._buttonWrapper.style.left = this._board.getBackground().offsetWidth + "px";

	this._clear.getWrapper().style.display = "block";
	
	this._solution.setSize(this._board.getButtonWidth(), this._board.getButtonHeight());
	this._solution.changeState(chssLanguage.translate(979), this.validateSolution, this);
	this._clear.setSize(this._board.getButtonWidth(), this._board.getButtonHeight());
	this._clear.changeState(chssLanguage.translate(1407), this.clear, this);
	
	this._info.getWrapper().style.width = chssOptions.moves_size + "px";
	this._info.getWrapper().style.top = this._top + "px";
	this._info.getWrapper().style.left = this._board.getBackground().offsetWidth + "px";
	this._info.setHeight(chssCommentArea.SMALL, this._board.getWrapper().offsetHeight - this._buttonWrapper.offsetHeight - this._top);
	this._info.draw();
}

chssSelectionModule.prototype.resize = function(diffCoeff)
{
	this._top = this._top * diffCoeff;
	
	this._buttonWrapper.style.bottom = parseFloat(this._buttonWrapper.style.bottom) * diffCoeff + "px";
	this._buttonWrapper.style.left = this._board.getBackground().offsetWidth + "px";
	
	this._solution.setSize(this._board.getButtonWidth(), this._board.getButtonHeight());
	this._clear.setSize(this._board.getButtonWidth(), this._board.getButtonHeight());
	
	this._info.getWrapper().style.width = chssOptions.moves_size + "px";
	this._info.getWrapper().style.top = parseFloat(this._info.getWrapper().style.top) * diffCoeff + "px";
	this._info.getWrapper().style.left = this._board.getBackground().offsetWidth + "px";
	this._info.resize(diffCoeff);
	this._info.setHeight(chssCommentArea.SMALL, this._board.getWrapper().offsetHeight - this._buttonWrapper.offsetHeight - this._top);
	this._info.draw();
}

chssSelectionModule.prototype.validateSolution = function()
{
	this._parent.setSubMode(chssModuleManager.subModes.NOT_ACTIVE);
	
	var solved = true;
	for(var i=0; i<this._selections.length; i++)
	{
		var selection = this._selections[i],
			valid = false;
		for(var j=0; j<this._user_selections.length; j++)
		{
			var uSelection = this._user_selections[j]
			if(selection[0] == uSelection[0] && selection[1] == uSelection[1])
			{
				valid = true;
				this._user_selections = chssHelper.array_removeAt(this._user_selections, j);
				break;
			}
		}
		
		solved &= valid;
		chssBoard.board.validate(selection[0], selection[1], (valid?pieceElement.validation.CORRECT:pieceElement.validation.CAUTION));
	}
	
	if(this._user_selections.length > 0)
	{
		solved = false;
		for(var i=0; i<this._user_selections.length; i++)
		{
			chssBoard.board.validate(this._user_selections[i][0], this._user_selections[i][1], pieceElement.validation.WRONG);
		}
	}
	
	this.clear();

	
	if(this._random || this._custom)
	{
		this._solution.changeState(chssLanguage.translate(541), (!this._custom?this.initData:this._nextCallback), (!this._custom?this:this._object));
		this._clear.getWrapper().style.display = "none";
	}
	else
		this._buttonWrapper.style.display = "none";
	
	this.showResult(solved);
	this.sendSelectionSolution(solved);
	if(typeof this._solutionCallback != "undefined")
		this._solutionCallback.call(this._object, solved);
}

chssSelectionModule.prototype.showResult = function(result)
{	
	this._board.getStatusImage().getImageElement().style.display = "block";
	this._board.getStatusImage().switchStatusImage(result?chssStatusImage.GOOD:chssStatusImage.BAD, this._board.getBackground().offsetHeight*0.6 - this._top);
	this._board.getStatusImage().getImageElement().style.top = this._top + "px"
	
	this._info.getWrapper().style.top = this._board.getStatusImage().getImageElement().offsetHeight + this._top + "px";
	this._info.setHeight(chssCommentArea.SMALL, this._board.getWrapper().offsetHeight - this._buttonWrapper.offsetHeight - this._board.getStatusImage().getImageElement().offsetHeight - this._top);
	this._info.draw(false);
}

chssSelectionModule.prototype.clear = function(complete)
{
	if(typeof complete === 'undefined')
		complete = false;
	for(var i=0; i<8; i++)
	{
		for(var j=0; j<8; j++)
		{
			chssBoard.board.unselect(i, j);
			if(complete)
				chssBoard.board.validate(i, j, pieceElement.validation.NONE);
		}
	}
	this._user_selections = new Array(); 
}

chssSelectionModule.prototype.addMarking = function(x, y)
{
	var added = false;
	for(var i=0; i<this._user_selections.length; i++)
	{
		if(this._user_selections[i][0] == x && this._user_selections[i][1] == y)
			added = true;
	}
	if(!added)
		this._user_selections.push([x, y]);
}

chssSelectionModule.prototype.removeMarking = function(x, y)
{
	for(var i=0; i<this._user_selections.length; i++)
	{
		if(this._user_selections[i][0] == x && this._user_selections[i][1] == y)
		{
			this._user_selections = chssHelper.array_removeAt(this._user_selections, i)
			break;
		}
	}
}

chssSelectionModule.prototype.hide = function(){}
chssSelectionModule.prototype.show = function(){}

chssSelectionModule.prototype.getInitialMode = function()
{
	return [chssModuleManager.modes.VIEW_MODE, chssModuleManager.subModes.ADD_MARKING, false];
}

chssSelectionModule.prototype.getPGNFile = function()
{
	return this._pgnfile;
}

chssSelectionModule.prototype.collectGarbage = function()
{
	if(typeof this._info != "undefined")
	{
		this._board.getWrapper().removeChild(this._info.getWrapper());
		this._info = undefined
		this._board.getWrapper().removeChild(this._buttonWrapper);
		this._buttonWrapper = undefined;
		this.clear(true);
	}
}

chssSelectionModule.prototype.getData = function()
{
	return this._data;
}

chssSelectionModule.prototype.isDrawn = function()
{
	return this._drawn;
}