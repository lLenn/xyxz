buttonElement.constants = {play: "play",
							plus: "plus",
							minus: "minus",
							plusTen: "plusTen",
							minusTen: "minusTen",
							end: "end",
							start: "start",
							rotate: "rotate",
							enlarge: "enlarge"}

function buttonElement(type)
{
	this._type = type;
	this._runnning = false;
	this._enlarged = false;
	this._buttonElement = document.createElement("span");
	this._buttonElement.className = "button unselectable";
	this._buttonElement.style.textAlign = "center";
	this.onClick(this);
}

buttonElement.prototype.drawSize = function(resize)
{
	if(typeof resize == "undefined")
		resize = false;
	
	this._buttonElement.style.height = 20 * (chssOptions.board_size/360) + "px";
	this._buttonElement.style.width = 20 * (chssOptions.moves_size/200) + "px";
	this._buttonElement.style.fontSize = 18 * (chssOptions.board_size/360) + "px";
	
	var innerHtml = "",
		smallPadding = 1 * (chssOptions.moves_size/200),
		normalPadding = 2 * (chssOptions.board_size/360),
		normalExcPadding = 2 * (chssOptions.moves_size/200),
		medPadding = 3 * (chssOptions.moves_size/200),
		medExcPadding = 4 * (chssOptions.moves_size/200),
		excPadding = 5 * (chssOptions.moves_size/200),
		playPadding = (90 - (5 + 2*5 + 20*3)) * (chssOptions.moves_size/200),
		normalWidth = 20 * (chssOptions.board_size/360);
	switch(this._type)
	{
		case buttonElement.constants.play: innerHtml = "&#xE072;"; this._buttonElement.style.padding = normalPadding + "px " + playPadding + "px " + normalPadding + "px " + playPadding + "px"; break;
		case buttonElement.constants.plus: innerHtml = "&#xE250;"; this._buttonElement.style.padding = normalPadding + "px " + normalExcPadding + "px " + normalPadding + "px " + normalExcPadding + "px"; break;
		case buttonElement.constants.minus: innerHtml = "&#xE251;"; this._buttonElement.style.padding = normalPadding + "px " + normalExcPadding + "px " + normalPadding + "px " + normalExcPadding + "px"; break;
		case buttonElement.constants.plusTen: innerHtml = "&#xE075;"; this._buttonElement.style.padding = normalPadding + "px 0px " + normalPadding + "px " + medExcPadding + "px"; break;
		case buttonElement.constants.minusTen: innerHtml = "&#xE071;"; this._buttonElement.style.padding = normalPadding + "px " + medPadding + "px " + normalPadding + "px " + smallPadding + "px"; break;
		case buttonElement.constants.end: innerHtml = "&#xE077;"; this._buttonElement.style.padding = normalPadding + "px " + excPadding + "px " + normalPadding + "px " + normalExcPadding + "px"; break;
		case buttonElement.constants.start: innerHtml = "&#xE069;"; this._buttonElement.style.padding = normalPadding + "px " + normalExcPadding + "px " + normalPadding + "px " + excPadding + "px"; break;
		case buttonElement.constants.rotate: innerHtml = "&#xE031;"; this._buttonElement.style.padding = normalPadding + "px"; this._buttonElement.style.width = normalWidth + "px"; break;
		case buttonElement.constants.enlarge: innerHtml = "&#xE140;"; this._buttonElement.style.padding = normalPadding + "px"; this._buttonElement.style.width = normalWidth + "px"; break;
		default: innerHtml = "!"; this._buttonElement.className = "buttonError"; break;
	}
	if(!resize) 
		this._buttonElement.innerHTML = innerHtml;
}

buttonElement.prototype.onClick = function(parent)
{
	parent.getButtonElement().onclick = function()
	{
		switch(parent.getType())
		{
			case buttonElement.constants.play: parent.playGame(false); break;
			case buttonElement.constants.plus: chssBoard.moduleManager.actionChangeBoard("+1", false); break;
			case buttonElement.constants.minus: chssBoard.moduleManager.actionChangeBoard("-1", false); break;
			case buttonElement.constants.plusTen: chssBoard.moduleManager.actionChangeBoard("+10", false); break;
			case buttonElement.constants.minusTen: chssBoard.moduleManager.actionChangeBoard("-10", false); break;
			case buttonElement.constants.end: chssBoard.moduleManager.actionChangeBoard("End", false); break;
			case buttonElement.constants.start: chssBoard.moduleManager.actionChangeBoard("Start", false); break;
			case buttonElement.constants.rotate: chssBoard.board.rotate(); break;
			case buttonElement.constants.enlarge: parent.enlarge(); break;
			default: innerHtml = "!"; parent.getButtonElement().className = "buttonError unselectable"; throw new Error("Wrong button type!"); break;
		}
		
	}
}

buttonElement.prototype.playGame = function(external)
{
	if(!(chssBoard.engine.isThinking() && chssBoard.moduleManager.getMode() == chssModuleManager.modes.VIEW_MODE) && chssBoard.moduleManager.getMode() != chssModuleManager.modes.PLAY_PUZZLE_MODE)
	{
		if(!this._running)
		{
			this._buttonElement.innerHTML = "&#xE073;";
			if(!external)
				chssBoard.moduleManager.playGame(false, true);
			this._running = true;
		}
		else
		{
			this._buttonElement.innerHTML = "&#xE072;";
			if(!external)
				chssBoard.moduleManager.pausePlaying();
			this._running = false;
		}
	}
}

buttonElement.prototype.playingStopped = function()
{
	this._buttonElement.innerHTML = "&#xE072;";
	this._running = false;
}

buttonElement.prototype.enlarge = function()
{
	if(this._enlarged)
		this._buttonElement.innerHTML = "&#xE140;";
	else
		this._buttonElement.innerHTML = "&#xE097;";

	this._enlarged = !this._enlarged;
	chssBoard.board.fullscreen();
}

buttonElement.prototype.getType = function()
{
	return this._type;
}

buttonElement.prototype.getButtonElement = function()
{
	return this._buttonElement;
}