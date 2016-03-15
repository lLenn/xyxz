function chssSeperator(width)
{
	this._seperator = document.createElement("div");
	this._line = document.createElement("div");
	this._line.style.borderBottom = 1*(chssOptions.board_size/360) + "px solid " + chssOptions.select_color;
	this._seperator.appendChild(this._line);
}

chssSeperator.prototype = {
		getWrapper: function()
		{
			return this._seperator;
		},
		
		draw: function(width)
		{
			this._line.style.width =  width*0.6 + "px";
			this._line.style.margin = 2*(chssOptions.board_size/360) + "px " + width*0.2 + "px";
		}
}

