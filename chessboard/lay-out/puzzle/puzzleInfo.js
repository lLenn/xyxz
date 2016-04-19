function chssPuzzleInfo()
{	
	this._showRating = false;
	
	this._wrapper = document.createElement("div");
	this._wrapper.style.backgroundColor = chssOptions.background_color;
	this._wrapper.className = "wrapper";
	
	this._rating = new chssInfoComponent(chssLanguage.translate(1411));
	this._comment = new chssInfoComponent(chssLanguage.translate(352));
	this._themes = new chssInfoComponent(chssLanguage.translate(152));
	
	this._wrapper.appendChild(this._rating.getWrapper());
	this._wrapper.appendChild(this._comment.getWrapper());
	this._wrapper.appendChild(this._themes.getWrapper());
}

chssPuzzleInfo.prototype = {
		draw: function(rating, comment, themes, height)
		{
			this._comment.getTextComponent().style.width = "auto";
			this._themes.getTextComponent().style.width = "auto";
			
			this._comment.setText("");
			this._themes.setText("");
			
			var max_width = parseFloat(this._wrapper.offsetWidth)*0.8;
			
			if(!isNaN(rating) && rating>0 && this._showRating)
				this._rating.setText(rating);
			else
			{
				this._rating.getWrapper().style.display = "none";
			}
			if(comment != "")
				this._comment.setText(chssHelper.wordWrap(this._comment.getTextComponent(), comment, max_width, false, false)); 
			else
			{
				this._comment.getWrapper().style.display = "none";
			}
			var txt = "";
			for(var i=0; i<themes.length; i++)
			{
				txt += chssLanguage.translate(themes[i]);
				if(i+1!=themes.length)
					txt += "<br/>";
			}
			if(txt != "")
				this._themes.setText(chssHelper.wordWrap(this._themes.getTextComponent(), txt, max_width, false, false));
			else
			{
				this._themes.getWrapper().style.display = "none";
			}
			
			height = (typeof height === 'undefined')?parseFloat(this._wrapper.offsetHeigth):height;

			if(this._themes.getWrapper().style.display != "none")
			{
				this._themes.setTextHeight((21*3)*(chssOptions.board_size/360));

				height -= this._themes.getWrapper().offsetHeight;
			}
			
			height -= this._rating.getWrapper().offsetHeight;
			
			if(this._comment.getWrapper().style.display != "none")
			{
				var check = parseFloat(chssHelper.getComputedStyle(this._comment.getLabelComponent(), "margin-left")) + 
							parseFloat(chssHelper.getComputedStyle(this._comment.getLabelComponent(), "margin-right")) + 
							parseFloat(chssHelper.getComputedStyle(this._comment.getTextComponent(), "margin-left")) + 
							parseFloat(chssHelper.getComputedStyle(this._comment.getTextComponent(), "margin-right")) + 
							this._comment.getLabelComponent().offsetWidth + this._comment.getTextComponent().offsetWidth;

				if(check > chssOptions.moves_size)
					height -= this._comment.getLabelComponent().offsetHeight + parseFloat(chssHelper.getComputedStyle(this._comment.getLabelComponent(), "margin-top")) + parseFloat(chssHelper.getComputedStyle(this._comment.getLabelComponent(), "margin-bottom"));

				this._comment.setTextHeight(height - parseFloat(chssHelper.getComputedStyle(this._wrapper, "padding-top")) - parseFloat(chssHelper.getComputedStyle(this._wrapper, "padding-bottom"))- parseFloat(chssHelper.getComputedStyle(this._comment.getTextComponent(), "margin-top")) - parseFloat(chssHelper.getComputedStyle(this._comment.getTextComponent(), "margin-bottom")));
			}
		},
		
		getWrapper: function()
		{
			return this._wrapper;
		},
		
		showRating: function(show)
		{
			this._showRating = show;
		}
}