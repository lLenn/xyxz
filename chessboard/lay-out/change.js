function chssChange()
{
	this._wrapper = document.createElement("div");
	
	this._changeMovesElement = document.createElement("div");
	this._changeMovesElement.style.position = "relative";
	
	this._start = new buttonElement(buttonElement.constants.start);
	this._start.getButtonElement().style.float = "left";
	this._changeMovesElement.appendChild(this._start.getButtonElement());
	
	this._minusTen = new buttonElement(buttonElement.constants.minusTen);
	this._minusTen.getButtonElement().style.float = "left";
	this._changeMovesElement.appendChild(this._minusTen.getButtonElement());
	
	this._minus = new buttonElement(buttonElement.constants.minus);
	this._minus.getButtonElement().style.float = "left";
	this._changeMovesElement.appendChild(this._minus.getButtonElement());

	this._play = new buttonElement(buttonElement.constants.play);
	this._play.getButtonElement().style.position = "absolute";
	this._changeMovesElement.appendChild(this._play.getButtonElement());

	this._end = new buttonElement(buttonElement.constants.end);
	this._end.getButtonElement().style.float = "right";
	this._changeMovesElement.appendChild(this._end.getButtonElement());
	
	this._plusTen = new buttonElement(buttonElement.constants.plusTen);
	this._plusTen.getButtonElement().style.float = "right";
	this._changeMovesElement.appendChild(this._plusTen.getButtonElement());
	
	this._plus = new buttonElement(buttonElement.constants.plus);
	this._plus.getButtonElement().style.float = "right";
	this._changeMovesElement.appendChild(this._plus.getButtonElement());
	
	this._wrapper.appendChild(this._changeMovesElement);
}

chssChange.prototype = {
		getWrapper: function()
		{
			return this._wrapper;
		},
		
		drawSize: function(resize)
		{
			if(typeof resize == "undefined")
				resize = false;
			
			this._start.drawSize(resize);
			this._minusTen.drawSize(resize);
			this._minus.drawSize(resize);
			this._play.drawSize(resize);
			this._end.drawSize(resize);
			this._plusTen.drawSize(resize);
			this._plus.drawSize(resize);
			this._play.getButtonElement().style.left = (parseFloat(this._wrapper.style.width)/2 - parseFloat(this._play.getButtonElement().style.width)/2 - parseFloat(this._play.getButtonElement().style.paddingLeft)) + "px";
		},
		
		playingStopped: function()
		{
			this._play.playingStopped();
		},

		playStarted: function()
		{
			this._play.playGame(true);
		}
}