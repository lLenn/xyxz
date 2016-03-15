function chssPuzzleActions()
{
	var paddingHor = 10 * (chssOptions.moves_size/200),
		fontSize = 16 * (chssOptions.board_size/360);
	
	this._wrapper = document.createElement("div");
	this._wrapper.style.fontSize = fontSize + "px";
	this._wrapper.style.padding = "0px " + paddingHor + "px";
	
	this._leftWrapper = new chssLinkButton();
	this._leftWrapper.getWrapper().style.float = "left";
	this._leftWrapper.getWrapper().style.display = "none";
	this._rightWrapper = new chssLinkButton()
	this._rightWrapper.getWrapper().style.float = "right";
	this._rightWrapper.getWrapper().style.display = "none";
	
	this._wrapper.appendChild(this._leftWrapper.getWrapper());
	this._wrapper.appendChild(this._rightWrapper.getWrapper());
}

chssPuzzleActions.prototype = {
		getActions: function(){ return this._wrapper; },
		changeAction: function(direction, visible, translation, callback, object)
		{
			var element = undefined;
			if(direction=="left")
				element = this._leftWrapper;
			else if(direction=="right")
				element = this._rightWrapper;
			
			element.setText(translation);
			element.getWrapper().style.display = (visible?"block":"none");
			element.getWrapper().onclick = function(){ callback.call(object) };
		}
}