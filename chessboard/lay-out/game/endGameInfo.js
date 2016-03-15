function chssEndGameInfo()
{	
	var paddingHor1 = 13 * (chssOptions.moves_size/200),
		paddingVer = 3 * (chssOptions.moves_size/200),
		fontSize = 16 * (chssOptions.board_size/360);

	this._paddingVer2 = 2 * (chssOptions.board_size/360);
	
	this._wrapper = document.createElement("div");
	this._wrapper.style.backgroundColor = chssOptions.background_color;
	this._wrapper.style.padding = this._paddingVer2 + "px 0px " + this._paddingVer2*2 + "px";
	this._wrapper.style.fontSize = fontSize + "px";
	
	this._title = document.createElement("div")
	this._title.style.margin = "0px " + paddingHor1 + "px " + this._paddingVer2*2 + "px";
	this._title.style.padding = paddingVer + "px 0px";
	
	this._textWrapper = document.createElement("div");
	this._textWrapper.style.overflowY = "auto";
	this._textWrapper.style.float = "right";
	this._textWrapper.style.margin = "0px " + paddingHor1 + "px " + "0px " + this._paddingVer2*2 + "px";
	this._textArea = document.createElement("div");
	this._textWrapper.appendChild(this._textArea);
	
	var brk = document.createElement("br");
	brk.className = "clearfloat";
	
	this._result = new chssInfoComponent(chssLanguage.translate(566));
	
	this._wrapper.appendChild(this._title);
	this._wrapper.appendChild(this._textWrapper);
	this._wrapper.appendChild(brk);
	this._wrapper.appendChild(this._result.getWrapper());
	
}

chssEndGameInfo.prototype = {
		getWrapper: function()
		{
			return this._wrapper;
		},

		draw: function(title, description, result, maxHeight)
		{
			this._title.innerHTML = "";
			this._textArea.innerHTML = "";
			var max_width = parseFloat(this._wrapper.offsetWidth) - (20 * (chssOptions.moves_size/200)) - 24;
			
			this._title.innerHTML = chssHelper.wordWrap(this._title, title, max_width, false, false);

			if(result != "" && !chssHelper.isNumeric(result))
				this._result.setText(result, "right");
			else
				this._result.setText("???", "right");
			
			var maxTextHeight = maxHeight - this._paddingVer2*7 - this._title.offsetHeight - this._result.getWrapper().offsetHeight;
			
			if(description != "")
			{
				this._result.getWrapper().style.marginTop = this._paddingVer2*2 + "px";
				this._textArea.innerHTML = chssHelper.wordWrap(this._textArea, chssHelper.stripHTMLTags(description), max_width, false, false);
				if(this._textWrapper.offsetHeigh > maxTextHeight)
					this._textWrapper.style.height = maxTextHeight + "px";
			}
			else
			{
				this._result.getWrapper().style.marginTop = "0px";
			}
			
			if(this._textWrapper.offsetHeight<this._textArea.offsetHeight)
			{
				this._textWrapper.style.marginRight = "0px";
				this._textWrapper.style.width = parseFloat(this._wrapper.style.width) - (20 * (chssOptions.moves_size/200)) + "px";
			}
			else
			{
				this._textWrapper.style.marginRight = 10 * (chssOptions.moves_size/200) + "px";
				this._textWrapper.style.width = parseFloat(this._wrapper.style.width) - (30 * (chssOptions.moves_size/200)) + "px";
			}
		}
		
}