function engineElement()
{
	this._engineElement = document.createElement("div");
	this._engineElement.id = "engineWrapper";
	this.show(false);

	this._new = document.createElement("span");
	this._new.innerHTML = "Nieuw Spel: ";
	this._cbBlack = document.createElement("input");
	this._cbBlack.type = "checkbox";
	this._newBl = document.createElement("span");
	this._newBl.innerHTML = " speel m. Zwart ";
	this._startGame = document.createElement("input");
	this._startGame.type = "button";
	this._startGame.value = "Start Game";
	this.newGame();
	
	this._activate = document.createElement("span");
	this._activate.innerHTML = "Activeer AI: ";
	this._skillLevel = document.createElement("span");
	this._skillLevel.innerHTML = " niveau ";
	this._checkbox = document.createElement("input");
	this._checkbox.type = "checkbox";
	this.onlineChange();

	this._evaluate = document.createElement("span");
	this._evaluate.innerHTML = "Enkel evaluatie: ";
	this._cbEval = document.createElement("input");
	this._cbEval.type = "checkbox";
	this.evalChange();
	
	this._time = document.createElement("input");
	this._time.type = "text";
	this._time.value = "10000";
	this._time.size = "5";
	this._timeText = document.createElement("span");
	this._timeText.innerHTML ="Denktijd: ";
	this._timeInfo = document.createElement("span");
	this._timeInfo.innerHTML =" in milliseconden";
	
	this._depth = document.createElement("input");
	this._depth.type = "text";
	this._depth.value = "20";
	this._depth.size = "3";
	this._depthText = document.createElement("span");
	this._depthText.innerHTML = "Diepte: ";
	this._depthInfo = document.createElement("span");
	this._depthInfo.innerHTML = " 0-20";
	
	this._skill = document.createElement("input");
	this._skill.type = "text";
	this._skill.value = "20";
	this._skill.size = "3";
	this._skillText = document.createElement("span");
	this._skillText.innerHTML = "Skill: ";
	this._skillInfo = document.createElement("span");
	this._skillInfo.innerHTML = " 0-20";

	this._errProb = document.createElement("input");
	this._errProb.type = "text";
	this._errProb.value = "128";
	this._errProb.size = "3";
	this._errProbText = document.createElement("span");
	this._errProbText.innerHTML = "Foutmogelijkheid: ";
	this._errProbInfo = document.createElement("span");
	this._errProbInfo.innerHTML = " 0-1000, hoe hoger hoe kleiner de kans";
	
	this._maxErr = document.createElement("input");
	this._maxErr.type = "text";
	this._maxErr.value = "0";
	this._maxErr.size = "3";
	this._maxErrText = document.createElement("span");
	this._maxErrText.innerHTML = "Foutmarge: ";
	this._maxErrInfo = document.createElement("span");
	this._maxErrInfo.innerHTML = " 0-100, hoe lager hoe kleiner de fout";
	
	this._rating = document.createElement("select");
	for(var i=0; i<engine.levels.length; i++)
	{
		var options = engine.levels[i],
			option = document.createElement("option");
		option.value = options[0];
		option.innerHTML = options[0] + ", " + options[1];
		if(i+1 == engine.levels.length)
			option.selected = true;
		this._rating.appendChild(option);
	}
	this.ratingChange();

	this._engineElement.appendChild(this._new);
	this._engineElement.appendChild(this._cbBlack);
	this._engineElement.appendChild(this._newBl);
	this._engineElement.appendChild(this._startGame);
	this._engineElement.appendChild(document.createElement("br"));
	this._engineElement.appendChild(this._activate);
	this._engineElement.appendChild(this._checkbox);
	this._engineElement.appendChild(this._skillLevel);
	this._engineElement.appendChild(this._rating);
	this._engineElement.appendChild(document.createElement("br"));
	this._engineElement.appendChild(this._evaluate);
	this._engineElement.appendChild(this._cbEval);
	this._engineElement.appendChild(document.createElement("br"));
	this._engineElement.appendChild(this._timeText);
	this._engineElement.appendChild(this._time);
	this._engineElement.appendChild(this._timeInfo);
	this._engineElement.appendChild(document.createElement("br"));
	this._engineElement.appendChild(this._depthText);
	this._engineElement.appendChild(this._depth);
	this._engineElement.appendChild(this._depthInfo);
	this._engineElement.appendChild(document.createElement("br"));
	this._engineElement.appendChild(this._skillText);
	this._engineElement.appendChild(this._skill);
	this._engineElement.appendChild(this._skillInfo);
	this._engineElement.appendChild(document.createElement("br"));
	this._engineElement.appendChild(this._errProbText);
	this._engineElement.appendChild(this._errProb);
	this._engineElement.appendChild(this._errProbInfo);
	this._engineElement.appendChild(document.createElement("br"));
	this._engineElement.appendChild(this._maxErrText);
	this._engineElement.appendChild(this._maxErr);
	this._engineElement.appendChild(this._maxErrInfo);
}

engineElement.prototype.newGame = function()
{
	var val = this._cbBlack;
	this._startGame.onclick = function()
	{
		chssBoard.moduleManager.startNewGame(!val.checked);
	}
}

engineElement.prototype.onlineChange = function()
{
	this._checkbox.onchange = function(event)
	{
		chssBoard.engine.setOnline(event.currentTarget.checked);		
	}
}

engineElement.prototype.evalChange = function()
{
	this._cbEval.onchange = function(event)
	{
		chssBoard.engine.setEvalOnly(event.currentTarget.checked);
	}
}

engineElement.prototype.ratingChange = function()
{
	this._rating.onchange = function(event)
	{
		chssBoard.engine.setLevel(event.currentTarget.value);
	}
	this._time.onchange = function(event)
	{
		chssBoard.engine.setTime(event.currentTarget.value);
	}
	this._depth.onchange = function(event)
	{
		chssBoard.engine.setDepth(event.currentTarget.value);
	}
	this._skill.onchange = function(event)
	{
		chssBoard.engine.setSkill(event.currentTarget.value);
	}
	this._errProb.onchange = function(event)
	{
		chssBoard.engine.setErrProb(event.currentTarget.value);
	}
	this._maxErr.onchange = function(event)
	{
		chssBoard.engine.setMaxErr(event.currentTarget.value);
	}
}

engineElement.prototype.getEngineElement = function()
{
	return this._engineElement;
}

engineElement.prototype.show = function(boolean)
{
	this._engineElement.style.display = (boolean?"block": "none");
}