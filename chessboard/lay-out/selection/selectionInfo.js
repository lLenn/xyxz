function chssSelectionInfo(question, description)
{
	this._question = question;
	this._description = description;
	this._height = undefined;
	
	this._paddingVer2 = 2 * (chssOptions.board_size/360);
	
	var paddingHor1 = 10 * (chssOptions.moves_size/200),
		paddingHor2 = 20 * (chssOptions.moves_size/200),
		paddingVer = 3 * (chssOptions.board_size/360),
		fontSize = 16 * (chssOptions.board_size/360);
	
	this._wrapper = document.createElement("div");
	this._wrapper.style.fontSize = fontSize + "px";
	this._wrapper.style.padding = this._paddingVer2 + "px 0px " + this._paddingVer2*2 + "px";
	
	this._label = document.createElement("div");
	this._label.style.margin = "0px " + paddingHor1 + "px " + this._paddingVer2*2 + "px";
	this._label.style.padding = paddingVer + "px 0px";
	this._label.innerHTML = chssLanguage.translate(352) + ":";

	this._textWrapper = document.createElement("div");
	this._textWrapper.style.overflowY = "auto";
	this._textWrapper.style.float = "right";
	this._textWrapper.style.margin = "0px " + paddingHor1 + "px " + "0px " + paddingHor2 + "px";
	this._textArea = document.createElement("div");
	this._textWrapper.appendChild(this._textArea);
	
	this._wrapper.appendChild(this._label);
	this._wrapper.appendChild(this._textWrapper);
}

chssSelectionInfo.prototype = {
		setText: function(question, description)
		{
			this._question = question;
			this._description = description;
		},
		
		getWrapper: function()
		{
			return this._wrapper;
		},

		draw: function(question)
		{
			this._textArea.innerHTML = "";
			var max_width = parseFloat(this._wrapper.offsetWidth) - (25 * (chssOptions.moves_size/200)) - 24;
			
			if(typeof question === 'undefined')
				question = true;

			if(question)
			{
				this._label.style.display = "block";
				this._textArea.innerHTML = chssHelper.wordWrap(this._textArea, this._question, max_width, false, false);
			}
			else
			{
				this._label.style.display = "none";
				this._textArea.innerHTML = chssHelper.wordWrap(this._textArea, this._description, max_width, false, false);
			}
			
			if(typeof this._height != undefined && chssHelper.isNumeric(this._height) && this._height>0)
			{
				if(question)
				{
					this._wrapper.style.padding = this._paddingVer2 + "px 0px " + this._paddingVer2*2 + "px";
					this._textWrapper.style.height = this._height - this._label.offsetHeight - this._paddingVer2*3  + "px";
				}
				else
				{
					this._wrapper.style.padding = this._paddingVer2*3 + "px 0px " + this._paddingVer2*2 + "px";
					this._textWrapper.style.height = this._height - this._paddingVer2*5 + "px";
				}
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
		},
		
		resize: function(diffCoeff)
		{
			this._paddingVer2 = 2 * (chssOptions.board_size/360);
			
			var paddingHor1 = 10 * (chssOptions.moves_size/200),
				paddingHor2 = 20 * (chssOptions.moves_size/200),
				paddingVer = 3 * (chssOptions.board_size/360),
				fontSize = 16 * (chssOptions.board_size/360);

			this._wrapper.style.fontSize = fontSize + "px";
			this._wrapper.style.padding = this._paddingVer2 + "px 0px " + this._paddingVer2*2 + "px";

			this._label.style.margin = "0px " + paddingHor1 + "px " + this._paddingVer2*2 + "px";
			this._label.style.padding = paddingVer + "px 0px";
			
			this._textWrapper.style.margin = "0px " + paddingHor1 + "px " + "0px " + paddingHor2 + "px";
			
			this._textWrapper.style.width = "auto";			
			this._textWrapper.style.height = "auto";
		},
		
		setHeight: function(height)
		{
			this._height = height;
		}
}