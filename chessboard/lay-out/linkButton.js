function chssLinkButton()
{
	var paddingHor = 10 * (chssOptions.moves_size/200),
		paddingVer = 2 * (chssOptions.board_size/360);
	
	this._linkWrapper= document.createElement("div");
	this._linkButton = document.createElement("div");
	this._linkButton.style.margin = -paddingVer + "px " + paddingHor + "px " + paddingVer*2 + "px";
	this._linkWrapper.appendChild(this._linkButton);
	this.selected(false);
}

chssLinkButton.prototype = {
		setText: function(text)
		{
			this._linkButton.innerHTML = text;
		},
		setFontSize: function(fontSize)
		{
			this._linkButton.style.fontSize = fontSize;
		},
		getWrapper: function()
		{
			return this._linkWrapper;
		},
		onMouseOver: function(element)
		{
			element.onmouseover = function()
			{
				element.style.backgroundColor = chssOptions.select_color;
				element.children[0].style.color = chssOptions.alt_color;
			}
		},
		onMouseOut: function(element)
		{
			element.onmouseout = function()
			{
				element.style.backgroundColor = chssOptions.background_color;
				element.children[0].style.color = chssOptions.select_color;
			}
		},
		selected: function(selected)
		{
			if(!selected)
			{
				this._linkWrapper.style.cursor = "pointer";
				this._linkButton.style.color = chssOptions.select_color;
				this._linkButton.style.borderBottom = 2 * (chssOptions.board_size/360) + "px dashed";
				this.onMouseOver(this._linkWrapper);
				this.onMouseOut(this._linkWrapper);
			}
			else
			{
				this._linkWrapper.style.cursor = "auto";
				this._linkButton.style.color = "#000000";
				this._linkButton.style.border = "none";
				this._linkWrapper.onmouseover = null;
				this._linkWrapper.onmouseout = null;
			}
		}
}