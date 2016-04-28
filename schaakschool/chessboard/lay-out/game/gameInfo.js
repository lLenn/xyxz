chssGameInfo.SMALL = "1";
chssGameInfo.BIG = "2";

function chssGameInfo(pgnfile)
{
	this._pgnfile = pgnfile;
	this._size = undefined;
	this._minSize = undefined;
	this._maxSize = undefined;
	
	this._wrapper = document.createElement("div");
	this._wrapper.style.display = "relative";
	this._wrapper.style.backgroundColor = chssOptions.background_color;
	
	this._subWrapper = document.createElement("div");
	this._subWrapper.style.overflowY = "auto";
	if(this._pgnfile.getEvent() && this._pgnfile.getEvent() != "" && !chssHelper.isNumeric(this._pgnfile.getEvent()))
	{
		this._event = new chssInfoComponent(chssLanguage.translate(595));
		this._event.setText(this._pgnfile.getEvent(), "right");
		this._subWrapper.appendChild(this._event.getWrapper());
	}
	var eventInfo = "";
	if(this._pgnfile.getDate() && this._pgnfile.getDate() != "" && !chssHelper.isNumeric(this._pgnfile.getDate()))
		eventInfo += (this._pgnfile.getDate().getDate()+1) + "/" + (this._pgnfile.getDate().getMonth()+1) + "/" + this._pgnfile.getDate().getFullYear() + ", ";
	if(this._pgnfile.getSite() && this._pgnfile.getSite() != "" && !chssHelper.isNumeric(this._pgnfile.getSite()))
		eventInfo += this._pgnfile.getSite() + ", ";
	if(eventInfo != "")
	{
		this._site = new chssInfoComponent();
		this._site.setText(eventInfo.substr(0, eventInfo.length-2), "right");
		this._subWrapper.appendChild(this._site.getWrapper());
	}
	
	this._white = new chssInfoComponent(chssLanguage.translate(526));
	if(this._pgnfile.getWhite() && this._pgnfile.getWhite() != "" && !chssHelper.isNumeric(this._pgnfile.getWhite()))
		this._white.setText(this._pgnfile.getWhite(), "right");
	else
		this._white.setText("???", "right");
	this._subWrapper.appendChild(this._white.getWrapper());

	if(this._pgnfile.getWhiteCountry() && this._pgnfile.getWhiteCountry() != "" && !chssHelper.isNumeric(this._pgnfile.getWhiteCountry()))
	{
		this._whiteCountry = new chssInfoComponent();
		this._whiteCountry.setText(this._pgnfile.getWhiteCountry(), "right");
		this._subWrapper.appendChild(this._whiteCountry.getWrapper());
	}

	var whiteInfo = "";
	if(this._pgnfile.getWhiteTitle() && this._pgnfile.getWhiteTitle() != "" && !chssHelper.isNumeric(this._pgnfile.getWhiteTitle()))
		whiteInfo += this._pgnfile.getWhiteTitle() + ", ";
	if(this._pgnfile.getWhiteElo() && this._pgnfile.getWhiteElo() != "" && chssHelper.isNumeric(this._pgnfile.getWhiteElo()))
		whiteInfo += this._pgnfile.getWhiteElo() + ", ";
	if(whiteInfo != "")
	{	
		this._whiteTitle = new chssInfoComponent();
		this._whiteTitle.setText(whiteInfo.substr(0, whiteInfo.length-2), "right");
		this._subWrapper.appendChild(this._whiteTitle.getWrapper());
	}
	
	this._black = new chssInfoComponent(chssLanguage.translate(527));
	if(this._pgnfile.getBlack() && this._pgnfile.getBlack() != "" && !chssHelper.isNumeric(this._pgnfile.getBlack()))
		this._black.setText(this._pgnfile.getBlack(), "right");
	else
		this._black.setText("???", "right");
	this._subWrapper.appendChild(this._black.getWrapper());

	if(this._pgnfile.getBlackCountry() && this._pgnfile.getBlackCountry() != "" && !chssHelper.isNumeric(this._pgnfile.getBlackCountry()))
	{
		this._blackCountry = new chssInfoComponent();
		this._blackCountry.setText(this._pgnfile.getBlackCountry(), "right");
		this._subWrapper.appendChild(this._blackCountry.getWrapper());
	}

	var blackInfo = "";
	if(this._pgnfile.getBlackTitle() && this._pgnfile.getBlackTitle() != "" && !chssHelper.isNumeric(this._pgnfile.getBlackTitle()))
		blackInfo += this._pgnfile.getBlackTitle() + ", ";
	if(this._pgnfile.getBlackElo() && this._pgnfile.getBlackElo() != "" && chssHelper.isNumeric(this._pgnfile.getBlackElo()))
		blackInfo += this._pgnfile.getBlackElo() + ", ";
	if(blackInfo != "")
	{	
		this._blackTitle = new chssInfoComponent();
		this._blackTitle.setText(blackInfo.substr(0, blackInfo.length-2), "right");
		this._subWrapper.appendChild(this._blackTitle.getWrapper());
	}
	
	this._result = new chssInfoComponent(chssLanguage.translate(566));
	if(this._pgnfile.getResult() && this._pgnfile.getResult() != "" && !chssHelper.isNumeric(this._pgnfile.getResult()))
		this._result.setText(this._pgnfile.getResult(), "right");
	else
		this._result.setText("???", "right");
	this._subWrapper.appendChild(this._result.getWrapper());
	
	var ecoInfo = "";
	if(this._pgnfile.getEco() && this._pgnfile.getEco() != "" && !chssHelper.isNumeric(this._pgnfile.getEco()))
		ecoInfo += this._pgnfile.getEco() + ": ";
	if(this._pgnfile.getOpening() && this._pgnfile.getOpening() != "" && !chssHelper.isNumeric(this._pgnfile.getOpening()))
		ecoInfo += this._pgnfile.getOpening() + ", ";
	
	if(ecoInfo != "")
	{
		this._opening = new chssInfoComponent(chssLanguage.translate(60));
		this._opening.setText(ecoInfo.substr(0, ecoInfo.length-2), "right");
		this._subWrapper.appendChild(this._opening.getWrapper());
	}
	
	if(this._pgnfile.getAnnotator() && this._pgnfile.getAnnotator() != "" && !chssHelper.isNumeric(this._pgnfile.getAnnotator()))
	{
		this._annotator = new chssInfoComponent(chssLanguage.translate(601));
		this._annotator.setText(this._pgnfile.getAnnotator(), "right");
		this._subWrapper.appendChild(this._annotator.getWrapper());
	}
	this._wrapper.appendChild(this._subWrapper);
	
	/*
	this._line = document.createElement("div");
	this._line.style.position = "absolute";
	this._line.style.width = (fontSize2 + extra)*3 + "px"
	this._line.style.borderBottom = border + "px solid " + chssOptions.select_color;
	this._line.style.right = (fontSize2 + extra + border*2) + "px";
	//this._line.style.marginTop = extra + "px";
	this._wrapper.appendChild(this._line);
	*/
	
	this._changeView = document.createElement("div");
	this._changeView.className = "smallButton unselectable";
	this._changeView.style.position = "absolute";
	this._changeView.style.top = "0px";
	this._changeView.style.right = "0px";
	this.changeView();
	this._wrapper.appendChild(this._changeView);
	
}

chssGameInfo.prototype = {
		getWrapper: function()
		{
			return this._wrapper;
		},
		
		getSize: function()
		{
			return this._size;
		},

		draw: function(size, minSize, maxSize)
		{
			var border = 1 * (chssOptions.board_size/360),
				fontSize2 = 11 * (chssOptions.board_size/360),
				extra = 4 * (chssOptions.board_size/360),
				fontSize = 16 * (chssOptions.board_size/360),
				padding = 17 * (chssOptions.board_size/360);
			
			this._wrapper.style.height = "";
			this._subWrapper.style.height = "";

			this._wrapper.style.paddingTop = padding + "px";
			this._wrapper.style.fontSize = fontSize + "px";
			
			this._changeView.style.border = border + "px solid " + chssOptions.select_color;
			this._changeView.style.fontSize = fontSize2 + "px";
			this._changeView.style.height = fontSize2 + extra + "px";
			this._changeView.style.width = fontSize2 + extra + "px";
			
			var element = undefined;
			for(var i=0; i<8; i++)
			{
				switch(i)
				{
					case 0: element = this._event; break
					case 1: element = this._site; break
					case 2: element = this._whiteCountry; break
					case 3: element = this._whiteTitle; break
					case 4: element = this._blackCountry; break
					case 5: element = this._blackTitle; break
					case 6: element = this._opening; break
					case 7: element = this._annotator; break
				}
				if(size == chssGameInfo.SMALL && typeof element !== 'undefined')
					element.getWrapper().style.display = "none";
				else if(size == chssGameInfo.BIG && typeof element !== 'undefined')
					element.getWrapper().style.display = "block";
			}
			
			var check = this._subWrapper.offsetHeight + padding*2;
			this._size = size;
			if(typeof minSize !== 'undefined' && minSize < check)
				this._minSize = minSize;
			else
				this._minSize = check;
			if(typeof maxSize !== 'undefined')
				this._maxSize = maxSize;
			
			if(size == chssGameInfo.SMALL)
			{
				this._subWrapper.style.height = (this._minSize - padding) + "px";
				this._wrapper.style.height = (this._minSize - padding)  + "px";
				this._changeView.innerHTML = "+";
				this._changeView.title = chssLanguage.translate(1405);
			}
			else if(size == chssGameInfo.BIG)
			{
				this._subWrapper.style.height = (this._maxSize - padding)  + "px";
				this._wrapper.style.height = (this._maxSize - padding)  + "px";
				this._changeView.innerHTML = "&ndash;";
				this._changeView.title = chssLanguage.translate(1406);
			}
			
			for(var i=0; i<11; i++)
			{
				switch(i)
				{
					case 0: element = this._event; break
					case 1: element = this._site; break
					case 2: element = this._white; break
					case 3: element = this._whiteCountry; break
					case 4: element = this._whiteTitle; break
					case 5: element = this._black; break
					case 6: element = this._blackCountry; break
					case 7: element = this._blackTitle; break
					case 8: element = this._result; break
					case 9: element = this._opening; break
					case 10: element = this._annotator; break
				}
				if(size == chssGameInfo.SMALL && typeof element !== 'undefined')
					element.getTextComponent().style.marginRight = (13 * (chssOptions.moves_size/200)) + "px";
				else if(size == chssGameInfo.BIG && typeof element !== 'undefined' && (this._subWrapper.offsetHeight < this._subWrapper.scrollHeight))
					element.getTextComponent().style.marginRight = (13 * (chssOptions.moves_size/200)) - 17 + "px";
			}
		},
		
		resize: function(diffCoeff)
		{
			this.draw(this._size, this._minSize * diffCoeff, this._maxSize * diffCoeff);
		},
		
		changeView: function()
		{
			var call = this.draw,
				obj = this;
			
			this._changeView.onclick = function()
			{ 
				var size = obj.getSize();
				if(size == chssGameInfo.SMALL)
					call.call(obj, chssGameInfo.BIG); 
				else if(size == chssGameInfo.BIG)
					call.call(obj, chssGameInfo.SMALL);
			}
		}
		
}