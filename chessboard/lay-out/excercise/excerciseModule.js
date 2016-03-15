function chssExcerciseModule(args, board, parent)
{
	this._parent = parent;
	this._board = board;

	this._objectSerial = args.objectSerial;
	this._attempt = args.attempt;
	this._previousPage = String(args.previousPage).replace('%26','&');
	
	this._title = undefined;
	this._description = undefined;
	this._excercises = undefined;
	this._pointer = undefined;
	this._version = undefined;
	
	this._activeModules = {};
	this._nextModules = {};
	this._loadedExcercises = new Array();
	this._activeModule = undefined;
	this._nextModule = undefined;
	this._nextLoaded = false;
	this._nextPressed = false;
	
	this._callback = null;
	this._object = null;
	
	this._info = undefined;
	this._start = undefined;
	this._buttonWrapper = undefined;
}
chssExcerciseModule.prototype = Object.create(chssModuleManager.prototype);
chssExcerciseModule.prototype.constructor = chssExcerciseModule;

chssExcerciseModule.prototype = {
		initData: function(callback, object)
		{
			if(callback != null)
			{
				this._callback = callback;
				this._object = object;
			}
			//chssBoard.ajaxRequest.loadData("POST", "ajax/retrieve_data.php", "location=puzzle&object_serial=" + this._objectSerial, this.initPuzzle, this);
			chssBoard.ajaxRequest.loadData("POST", "ajax/retrieve_data.php", "location=excercise&object_serial=" + this._objectSerial, this.initExcercise, this);
		},
		
		initExcercise: function(data)
		{			
			this._objectSerial = data.object_serial;
			this._title = data.excercise.title;
			this._description = data.excercise.description;
			this._excercises = data.components;
			
			this._pointer = -1;
			this._version = 1;

			this._info = new chssExcerciseInfo();
			this._info.getWrapper().style.position = "absolute";
			this._board.append(this._info.getWrapper());

			this._buttonWrapper = document.createElement("div");
			this._buttonWrapper.style.position = "absolute";
			this._buttonWrapper.className = "buttonWrapper";
			
			this._start = new bigButton(chssLanguage.translate(523));
			this._buttonWrapper.appendChild(this._start.getWrapper());

			this._board.append(this._buttonWrapper);
			
			this.initModule(this._excercises[++this._pointer]);
		},
		
		initMistakesExcercise: function(data)
		{
			this._excercises = data.components;
			
			this._loadedExcercises = new Array();
			
			if(this._excercises.length == 0)
				this._start.changeState(chssLanguage.translate(540), this.redirect, this);
		},
		
		initializedExcercise: function()
		{
			this._loadedExcercises[this._pointer] = (this._nextModule==null?this._activeModule.getData():this._nextModule.getData());
			if(this._pointer == 0)
				this.draw();
			else
				this.loadNext(true);
		},
		
		draw: function()
		{
			if(this._pointer == 0)
			{
				var offset = this._board.getBackground().offsetWidth;
				
				this._board.getMovesList().getMoves().style.display = "none";
				this._board.getChange().getWrapper().style.display = "none";

				this._buttonWrapper.style.display = "block";
				this._buttonWrapper.style.left = offset + "px";
				this._buttonWrapper.style.bottom = "0px";
				
				this._info.setInfo(this._title, this._description, this._version, this._excercises.length);
				this._info.getWrapper().style.width = chssOptions.moves_size + "px";
				this._info.getWrapper().style.top = "0px";
				this._info.getWrapper().style.left = offset + "px";
				this._info.getWrapper().style.height = this._board.getWrapper().offsetHeight - this._buttonWrapper.offsetHeight + "px"; 
				this._info.draw();
				
				this._start.setSize(this._board.getButtonWidth(), this._board.getButtonHeight())
				this._start.changeState(chssLanguage.translate(523), this.startExcercises, this);
				
				this._parent.setMode(chssModuleManager.modes.VIEW_MODE);
				this._parent.setSubMode(chssModuleManager.subModes.NOT_ACTIVE);
				chssBoard.board.loadComplete();
			}
			else
			{
				this._buttonWrapper.style.display = "none";
				this._info.getWrapper().style.height = "auto";
				this._info.draw(true);
				this._activeModule.draw(this._info.getWrapper().offsetHeight);
			}
		},
		
		resize: function(diffCoeff)
		{
			
			this._buttonWrapper.style.left = this._board.getBackground().offsetWidth + "px";
			
			this._start.resize(this._board.getButtonWidth(), this._board.getButtonHeight());
			
			this._info.getWrapper().style.width = chssOptions.moves_size + "px";
			this._info.getWrapper().style.top = parseFloat(this._info.getWrapper().style.top) * diffCoeff + "px";
			this._info.getWrapper().style.left = this._board.getBackground().offsetWidth + "px";
			if(this._info.getWrapper().style.height != "auto")
				this._info.getWrapper().style.height = this._board.getWrapper().offsetHeight - this._buttonWrapper.offsetHeight + "px"; 
			this._info.resize();
			
			if(this._activeModule.isDrawn())
				this._activeModule.resize(diffCoeff);
		},
		
		startExcercises: function()
		{
			if(this._pointer != this._excercises.length-1)
				this.initModule(this._excercises[++this._pointer]);
			else
			{
				this._pointer++;
				this.rotateModule(null);
			}
			this._callback.call(this._object);
		},
		
		addSolution: function(solution)
		{
			this._info.addScore(solution, true);
			this._info.draw(true);
		},
		
		loadNext: function(loaded)
		{
			if(typeof loaded == "undefined")
				this._nextPressed = true;
			else
				this._nextLoaded = loaded;
			
			if(this._nextLoaded && this._nextPressed)
			{
				if(this._pointer != this._excercises.length)
					this.startExcercises();
				else
				{
					this._activeModule.collectGarbage();
					this._activeModule = undefined;
					this._nextModule = undefined;
					
					this._pointer = -1;
					this._version += 1;
					
					var correct = this._info.getScore(),
						cnt = 0;
					for(var i=0;i<correct.length;i++)
					{
						if(correct[i])
							cnt++;
					}
					
					this._buttonWrapper.style.display = "block";
					this._start.changeState(chssLanguage.translate(1408), this.loadNext, this);
					
					var result = chssLanguage.translate(515).replace("{0}", Math.round((cnt/correct.length)*100));
					this._info.setInfo(chssLanguage.translate(39), result, 1, this._excercises.length);
					this._info.getWrapper().style.left = this._board.getBackground().offsetWidth + "px";
					this._info.getWrapper().style.height = this._board.getWrapper().offsetHeight - this._buttonWrapper.offsetHeight + "px";
					this._info.reset();
					this._info.draw();
					
					if(this._version == 3)
						chssBoard.ajaxRequest.loadData("POST", "ajax/retrieve_data.php", "location=excercise&prev_mistakes=1&object_serial=" + this._objectSerial, this.initMistakesExcercise, this);	
					else if(this._version == 4)
						this._start.changeState(chssLanguage.translate(540), this.redirect, this);
				}
			}
		},
		
		initModule: function(excercise)
		{
			this._nextLoaded = false;
			this._nextPressed = false;
			
			var nextModule = this.getTempModule(this._nextModules, excercise);
			if(typeof this._activeModule == "undefined")
			{
				this._activeModule = nextModule;
				this.switchStorage();
			}
			else if(typeof this._nextModule == "undefined")
			{
				this._nextModule = nextModule;
			}
			else
			{
				this.rotateModule(nextModule)
			}
			var data = undefined;
			if(typeof this._loadedExcercises[this._pointer] != 'undefined')
			{
				this._nextLoaded = true;
				data = this._loadedExcercises[this._pointer];
			}
			nextModule.customLoad(excercise.typeObjectSerial, this.initializedExcercise, this.addSolution, this.loadNext, this, (this._version!=3?this._objectSerial:null), (this._version!=3?this._attempt:null), data);
		},
		
		rotateModule: function(next)
		{			
			this._activeModule.collectGarbage();
			this._activeModule = this._nextModule;
			this._nextModule = next;
			this.switchStorage();
		},
		
		switchStorage: function()
		{
			var tempStorage = this._activeModules;
			this._activeModules = this._nextModules;
			this._nextModules = tempStorage;
		},
		
		getTempModule: function(storage, excercise)
		{
			var module = null;
			if(!storage[excercise.type])
			{
				switch(excercise.type)
				{
					case 1: module = new chssPuzzleModule({}, this._board, this._parent, true); break;
					case 2: module = new chssMultipleAnswersModule({}, this._board, this._parent, true); break;
					case 3: module = new chssSelectionModule({}, this._board, this._parent, true); break;
				}
				
				storage[excercise.type] = module;
			}
			else
				module = storage[excercise.type];
			return module;
		},
		
		redirect: function()
		{
			document.location.href = "index.php?page=" + this._previousPage;
		},
		
		getInitialMode: function()
		{
			return this._activeModule.getInitialMode();
		},
		
		getPGNFile: function() { return this._activeModule.getPGNFile()},
		processValidation: function() { this._activeModule.processValidation()},
		hide: function() { this._activeModule.hide()},
		show: function() { this._activeModule.show()},
		
		showEngineModule: function(boolean)
		{
			/*
			if(typeof this._activeModule !== 'undefined' && typeof this._activeModule.showEngineModule === 'function')
				this._activeModule.showEngineModule(boolean)
				*/
		},
		
		getChangeAttempts: function()
		{
			if(typeof this._activeModule !== 'undefined' && typeof this._activeModule.getChangeAttempts === 'function')
				return this._activeModule.getChangeAttempts();
		},
		
		processResult: function()
		{
			if(typeof this._activeModule !== 'undefined' && typeof this._activeModule.processResult === 'function')
				this._activeModule.processResult();
		},
			
		removeTemp:  function()
		{
			if(typeof this._activeModule !== 'undefined' && typeof this._activeModule.removeTemp === 'function')
				this._activeModule.removeTemp();
		},
		
		addMarking: function(x, y)
		{
			if(typeof this._activeModule !== 'undefined' && typeof this._activeModule.addMarking === 'function')
				this._activeModule.addMarking(x, y)
		},

		removeMarking: function(x, y)
		{
			if(typeof this._activeModule !== 'undefined' && typeof this._activeModule.removeMarking === 'function')
				this._activeModule.removeMarking(x, y)
		}
}