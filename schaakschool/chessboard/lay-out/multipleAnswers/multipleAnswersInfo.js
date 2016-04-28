function chssMultipleAnswersInfo(question, themes)
{
	this._question = question;
	this._themes = themes;
	this._height = undefined;
	
	this._wrapper = document.createElement("div");
	this._wrapper.className = "wrapper";
	
	this._label = document.createElement("div");
	this._label.className = "elementWrapper";
	this._label.innerHTML = chssLanguage.translate(352) + ":";

	this._textWrapper = document.createElement("div");
	this._textWrapper.style.overflowY = "auto";
	this._textWrapper.style.float = "right";
	this._textWrapper.className = "elementWrapper";
	this._textWrapper.style.padding = "0";
	this._textArea = document.createElement("div");
	this._textWrapper.appendChild(this._textArea);
	
	this._themesElement = new chssInfoComponent(chssLanguage.translate(152));
	
	this._wrapper.appendChild(this._label);
	this._wrapper.appendChild(this._textWrapper);
	this._wrapper.appendChild(this._themesElement.getWrapper());
}

chssMultipleAnswersInfo.prototype = {
		setText: function(question, themes)
		{
			this._question = question;
			this._themes = themes;
		},
		
		getWrapper: function()
		{
			return this._wrapper;
		},

		draw: function()
		{
			this._themesElement.getTextComponent().style.width = "auto";
			this._themesElement.getTextComponent().style.height = "auto";
			
			this._textWrapper.style.width = "auto";			
			this._textWrapper.style.height = "auto";
			
			this._textArea.innerHTML = "";
			var max_width = parseFloat(this._wrapper.offsetWidth) * 0.8;

			var txt = "";
			for(var i=0; i<this._themes.length; i++)
			{
				txt += chssLanguage.translate(this._themes[i]);
				if(i+1!=this._themes.length)
					txt += "<br/>";
			}
			
			if(txt != "")
			{
				this._themesElement.setText(chssHelper.wordWrap(this._themesElement.getTextComponent(), txt, max_width, false, false));
				this._themesElement.setTextHeight((21*2)*(chssOptions.board_size/360));
				this._themesElement.getWrapper().style.display = "display";
			}
			else
			{
				this._themesElement.getWrapper().style.display = "none";
			}
			
			if(typeof this._height === 'undefined' || !chssHelper.isNumeric(this._height) || this._height<=0)
				this._height = this._wrapper.offsetHeight;
			
			var textHeight = this._height - this._label.offsetHeight - this._themesElement.getWrapper().offsetHeight - 
			 				 parseFloat(chssHelper.getComputedStyle(this._label, "margin-top")) -
			 				 parseFloat(chssHelper.getComputedStyle(this._label, "margin-bottom")) - 
							 parseFloat(chssHelper.getComputedStyle(this._textWrapper, "margin-top")) -
							 parseFloat(chssHelper.getComputedStyle(this._textWrapper, "margin-bottom"));
			this._textArea.innerHTML = chssHelper.wordWrap(this._textArea, this._question, max_width, false, false);
			if(textHeight < this._textWrapper.offsetHeight)
				this._textWrapper.style.height = textHeight + "px";

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
		
		setHeight: function(height)
		{
			this._height = height;
		}
}