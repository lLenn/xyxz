function chssMultipleAnswersModule(args, board, parent, custom)
{
	if(typeof custom == "undefined")
		custom = false;
	
	this._board = board;
	this._parent = parent;
	this._custom = custom;
	
	this._objectSerial = args.objectSerial;
	this._objectCorrect = undefined;
	this._objectWrong = undefined;
	this._random = false;
	this._setId = undefined;
	this._setAttempt = undefined;
	
	this._question = undefined;
	this._answers = undefined;

	this._pgnfile = null;
	this._callback = null;
	this._solutionCallback = undefined;
	this._nextCallback = undefined;
	this._object = null;
	this._data = undefined;
	this._dranw = false;
	
	this._selectedId = undefined;
	this._selectedSolution = undefined;

	this._info = undefined;
	this._answersWrapper = undefined;
	this._answerElements = undefined;
	this._buttonWrapper = undefined;
	this._solution = undefined;
	this._seperator = undefined;
	this._top = undefined;
}
chssMultipleAnswersModule.prototype = Object.create(chssModuleManager.prototype);
chssMultipleAnswersModule.prototype.constructor = chssMultipleAnswersModule;

chssMultipleAnswersModule.prototype = {

		customLoad: function(objectSerial, callback, solutionCallback, nextCallback, object, setId, setAttempt, data)
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
		},
		
		initData: function(callback, object)
		{
			if(typeof load == "undefined")
				true;
			if(callback != null)
			{
				this._callback = callback;
				this._object = object;
			}
			
			//chssBoard.ajaxRequest.loadData("POST", "ajax/retrieve_data.php", "location=selection&object_serial=" + this._objectSerial, this.initSelection, this);
			chssBoard.ajaxRequest.loadData("POST", "ajax/retrieve_data.php", "location=multipleAnswers&" + (this._random?"random=1":"object_serial=" + this._objectSerial), this.init, this);
		},

		sendMultipleAnswersSolution: function(validation)
		{
			var args = "action=register_question_statistics";
			if(this._custom)
				args = "action=register_question_set_statistics";
			args += "&serial=" + this._objectSerial;
			args += "&serialSolution=" + (validation?this._objectCorrect:this._objectWrong);
			if(this._custom && this._setId != null)
			{
				args += "&setId=" + this._setId;
				args += "&setAttempt=" + this._setAttempt;
			}
			chssBoard.ajaxRequest.loadData("POST", "ajax/insert_data.php", args, this.solutionRegistered, this);
		},
		
		init: function(data)
		{
			this._data = data;
			
			this._objectSerial = data.object_serial;
			this._objectCorrect = data.object_correct;
			this._objectWrong = data.object_wrong;
			
			this._pgnfile = chssHelper.loadGameFromJSON(data);
			if(data.themes)
				this._pgnfile.setThemes(data.themes);
			
			this._question = data.question;
			this._answers = new Array()
			for(var i=0; i<data.question_answers.length; i++)
			{
				this._answers.push({answer: data.question_answers[i].answer, correct: data.question_answers[i].correct?true:false});
			}

			this._callback.call(this._object);
		},
		
		draw: function(top)
		{
			if(typeof this._info == 'undefined')
			{
				this.addQuestionInfo();
				this.addActions();
				this._answersWrapper = document.createElement("div");
				this._answersWrapper.style.position = "absolute";
				this._board.append(this._answersWrapper);
				this._seperator = new chssSeperator();
				this._seperator.getWrapper().style.position = "absolute";
				this._board.append(this._seperator.getWrapper());
				this._drawn = true;
			}
			this.initialDraw(top);
		},
		
		initialDraw: function(top)
		{
			if(typeof top == "undefined")
				this._top = 0;
			else
				this._top = top;
			
			this._selectedId = undefined;
			this._selectedSolution = undefined;
			
			this._info.setText(this._question, this._pgnfile.getThemes());
			
			this._board.getStatusImage().getImageElement().style.top = this._top + "px";
			this._board.getStatusImage().getImageElement().style.width = chssOptions.moves_size + "px";
			this._board.getStatusImage().getImageElement().style.left = parseFloat(this._board.getBackground().style.width) + "px";
			this._board.getStatusImage().getImageElement().style.display = "none";
			
			this._board.getMovesList().getMoves().style.display = "none";
			this._board.getChange().getWrapper().style.display = "none";
			
			this._buttonWrapper.style.bottom = "0px";
			this._buttonWrapper.style.left = this._board.getBackground().offsetWidth + "px";

			this._solution.setSize(this._board.getButtonWidth(), this._board.getButtonHeight());
			this._solution.changeState(chssLanguage.translate(979), this.showSolution, this);

			this._answersWrapper.style.width = chssOptions.moves_size + "px";
			this._answersWrapper.style.left = this._board.getBackground().offsetWidth + "px";
			this._answersWrapper.style.bottom = this._buttonWrapper.offsetHeight + "px";
			this.addAnswers();
			this.adjustAnswersWrapper(this._board.getWrapper().offsetHeight*0.67 - this._buttonWrapper.offsetHeight - this._top);
			
			this._info.getWrapper().style.display = "block";
			this._info.getWrapper().style.width = chssOptions.moves_size + "px";
			this._info.getWrapper().style.top = this._top + "px";
			this._info.getWrapper().style.left = this._board.getBackground().offsetWidth + "px";
			this._info.setHeight(this._board.getWrapper().offsetHeight - this._buttonWrapper.offsetHeight - this._answersWrapper.offsetHeight - this._top);
			this._info.draw();
		},
		
		resize: function(diffCoeff)
		{
			this._top = this._top * diffCoeff;

			this._board.getStatusImage().getImageElement().style.top = this._top + "px";
			
			this._buttonWrapper.style.bottom = parseFloat(this._buttonWrapper.style.bottom) * diffCoeff + "px";
			this._buttonWrapper.style.left = this._board.getBackground().offsetWidth + "px";
			
			this._solution.setSize(this._board.getButtonWidth(), this._board.getButtonHeight());
			
			this._answersWrapper.style.width = chssOptions.moves_size + "px";
			this._answersWrapper.style.left = this._board.getBackground().offsetWidth + "px";
			this._answersWrapper.style.bottom = this._buttonWrapper.offsetHeight + "px";
			
			for(var i=0; i<this._answerElements.length; i++)
				this._answerElements[i].resize();
			
			if(this._info.getWrapper().style.display != "none")
			{
				this.adjustAnswersWrapper(this._board.getWrapper().offsetHeight*0.67 - this._buttonWrapper.offsetHeight - this._top);
				
				this._info.getWrapper().style.width = chssOptions.moves_size + "px";
				this._info.getWrapper().style.top = parseFloat(this._info.getWrapper().style.top) * diffCoeff + "px";
				this._info.getWrapper().style.left = this._board.getBackground().offsetWidth + "px";
				this._info.setHeight(this._board.getWrapper().offsetHeight - this._buttonWrapper.offsetHeight - this._answersWrapper.offsetHeight - this._top);
				this._info.draw();
			}
			else
				this.adjustAnswersWrapper(this._board.getWrapper().offsetHeight - this._buttonWrapper.offsetHeight - this._board.getStatusImage().getImageElement().offsetHeight - this._top);

		},
		
		adjustAnswersWrapper: function(checkHeight)
		{
			if(this._answersWrapper.scrollHeight > checkHeight)
			{
				this._answersWrapper.style.height = checkHeight + "px";
				this._answersWrapper.style.overflowY = "auto";
				for(var i=0;i<this._answerElements.length;i++)
					this._answerElements[i].getRadioElement().style.marginRight = 7 * (chssOptions.moves_size/200) + "px";
			}
			else
			{
				for(var i=0;i<this._answerElements.length;i++)
					this._answerElements[i].getRadioElement().style.marginRight = 13 * (chssOptions.moves_size/200) + "px";
			}
		},
		
		validateSolution: function(id, solution)
		{
			this._selectedId = id;
			this._selectedSolution = solution;
		},
		
		showSolution: function()
		{
			for(var i=0; i<this._answerElements.length; i++)
			{
				if(this._answerElements[i].getId() == this._selectedId)
					this._answerElements[i].validate(this._selectedSolution);
				else if(this._answerElements[i].getCorrect())
					this._answerElements[i].validate(true);
				else
					this._answerElements[i].hide();
			}
			
			if(this._random || this._custom)
			{
				this._solution.changeState(chssLanguage.translate(541), (!this._custom?this.initData:this._nextCallback), (!this._custom?this:this._object));
			}
			else
				this._buttonWrapper.style.display = "none";
			
			this.showResult(this._selectedSolution);
			this.sendMultipleAnswersSolution(this._selectedSolution);
			if(typeof this._solutionCallback != "undefined")
				this._solutionCallback.call(this._object, this._selectedSolution);
		},
		
		solutionRegistered: function()
		{
			console.log("registered question solution!");
		},
		
		showResult: function(result)
		{
			this._board.getStatusImage().getImageElement().style.display = "block";
			this._board.getStatusImage().switchStatusImage(result?chssStatusImage.GOOD:chssStatusImage.BAD, this._board.getBackground().offsetHeight*0.6 - this._top);
			this._board.getStatusImage().getImageElement().style.top = this._top + "px";

			this._info.getWrapper().style.display = "none";
			this.adjustAnswersWrapper(this._board.getWrapper().offsetHeight - this._buttonWrapper.offsetHeight - this._board.getStatusImage().getImageElement().offsetHeight - this._top);
			this._answersWrapper.style.scrollBottom = "0px";
			this._answersWrapper.style.bottom = this._buttonWrapper.offsetHeight + "px";
		},

		hide: function(){},
		show: function(){},

		getInitialMode: function()
		{
			return [chssModuleManager.modes.VIEW_MODE, chssModuleManager.subModes.NOT_ACTIVE, false];
		},

		getPGNFile: function()
		{
			return this._pgnfile;
		},

		addQuestionInfo: function()
		{
			this._info = new chssMultipleAnswersInfo();
			this._info.getWrapper().style.position = "absolute";
			this._board.append(this._info.getWrapper());
		},

		addActions: function()
		{
			this._buttonWrapper = document.createElement("div");
			this._buttonWrapper.style.position = "absolute";
			this._buttonWrapper.className = "buttonWrapper";
			
			this._solution = new bigButton(chssLanguage.translate(979));
			this._buttonWrapper.appendChild(this._solution.getWrapper());
			this._buttonWrapper.appendChild(document.createElement("br"));
			this._board.append(this._buttonWrapper);
		},
		
		addAnswers: function()
		{
			this._answersWrapper.innerHTML = "";
			this._answerElements = new Array();

			for(var i = 0; i<this._answers.length; i++)
			{
				var el = new chssAnswer(i, this._answers[i].answer, this._answers[i].correct, this.validateSolution, this);
				el.getWrapper().className = (i%2?"odd":"");
				this._answerElements.push(el);
				this._answersWrapper.appendChild(el.getWrapper());
				el.draw();
			}
		},

		collectGarbage: function()
		{
			if(typeof this._info != "undefined")
			{
				this._board.getWrapper().removeChild(this._answersWrapper);
				this._answersWrapper = undefined;
				this._board.getWrapper().removeChild(this._seperator.getWrapper());
				this._seperator = undefined;
				this._board.getWrapper().removeChild(this._info.getWrapper());
				this._info = undefined;
				this._board.getWrapper().removeChild(this._buttonWrapper);
				this._buttonWrapper = undefined;
			}
		},
		
		getData: function()
		{
			return this._data;
		},
		
		isDrawn: function()
		{
			return this._drawn;
		}

}