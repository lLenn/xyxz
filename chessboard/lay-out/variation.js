function variationPopUp()
{
	this._variationPopUp = document.createElement("div");
	this._variationPopUp.style.fontFamily = chssOptions.font;
	this._variationPopUp.id = "variationPopUp"
	this._variationPopUp.style.position = "absolute";
	this._variationPopUp.style.backgroundColor = chssOptions.background_color;
	
	this._label = document.createElement("div");
	this._label.innerHTML = chssLanguage.translate(574);
	
	this._variationPopUp.appendChild(this._label);
	
	this._optionsWrapper = document.createElement("div");
	
	this._accept = document.createElement("div");
	this._acceptLbl = document.createElement("span");
	this._acceptLbl.innerHTML = chssLanguage.translate(575);
	this._accept.style.backgroundColor = chssOptions.black_color;
	this._accept.style.color = chssOptions.alt_color;
	this._accept.style.display = "inline";
	this._accept.style.cursor = "pointer";
	this._accept.appendChild(this._acceptLbl);
	this._optionsWrapper.appendChild(this._accept);
	
	this._deny = document.createElement("div");
	this._denyLbl = document.createElement("span");
	this._denyLbl.innerHTML = chssLanguage.translate(576);
	
	this._deny.style.backgroundColor = chssOptions.black_color;
	this._deny.style.color = chssOptions.alt_color;
	this._deny.style.display = "inline";
	this._deny.style.cursor = "pointer";
	this._deny.appendChild(this._denyLbl);
	this._optionsWrapper.appendChild(this._deny);
	this._variationPopUp.appendChild(this._optionsWrapper);
	
}

variationPopUp.prototype.drawSize = function()
{
	this.resize();
	this._variationPopUp.style.display = "none";
}

variationPopUp.prototype.resize = function()
{
	var border = 2 * (chssOptions.board_size/360),
		padding = 12 * (chssOptions.board_size/360),
		radius = 5 * (chssOptions.board_size/360),
		marginLeft = 7 * (chssOptions.board_size/360),
		marginTop = 7 * (chssOptions.board_size/360),
		btnHorPadding = 8 * (chssOptions.board_size/360),
		btnVerPadding = 1 * (chssOptions.board_size/360);

	this._variationPopUp.style.border = "solid " + border + "px " + chssOptions.black_color;
	this._variationPopUp.style.padding = padding + "px";
	
	this._optionsWrapper.style.marginTop = marginTop + "px";
	
	this._accept.style.border = "solid " + border + "px " + chssOptions.black_color;
	this._accept.style.padding = btnVerPadding + "px " + btnHorPadding + "px";
	this._accept.style.MozBorderRadius = radius + "px";
	this._accept.style.borderRadius = radius + "px";
	
	this._deny.style.border = "solid " + border + "px " + chssOptions.black_color;
	this._deny.style.padding = btnVerPadding + "px " + btnHorPadding + "px";
	this._deny.style.marginLeft = marginLeft + "px";
	this._deny.style.MozBorderRadius = radius + "px";
	this._deny.style.borderRadius = radius + "px";
	
	var board = chssBoard.board.getBackground();
	this._accept.style.width = this._acceptLbl.offsetWidth + (btnHorPadding) + "px";
	this._deny.style.width = this._denyLbl.offsetWidth + (btnHorPadding) + "px";
	this._optionsWrapper.style.marginLeft = (this._variationPopUp.offsetWidth - this._optionsWrapper.offsetWidth)/2 + "px";
	this._variationPopUp.style.top = (board.offsetHeight - this._variationPopUp.offsetHeight)/2 + "px";
	this._variationPopUp.style.left = (board.offsetWidth - this._variationPopUp.offsetWidth)/2 + "px";
}

variationPopUp.prototype.getVariationPopUp = function()
{
	return this._variationPopUp;
}

variationPopUp.prototype.draw = function(args, callback, obj)
{
	chssBoard.board.getBackground().style.opacity = "0.6";
	chssBoard.board.getBackground().style.filter = "alpha(opacity=60)";
	chssBoard.board.getBoard().style.opacity = "0.6";
	chssBoard.board.getBoard().style.filter = "alpha(opacity=60)";
	this._variationPopUp.style.display = "block";
	
	this.onSelect(this._accept, args, callback, obj, true, this);
	this.onSelect(this._deny, args, callback, obj, false, this);
}

variationPopUp.prototype.onSelect = function(element, args, callback, obj, select, parent)
{
	element.onclick = function(event)
	{
		chssBoard.board.getBackground().style.opacity = "1";
		chssBoard.board.getBackground().style.filter = "alpha(opacity=100)";
		chssBoard.board.getBoard().style.opacity = "1";
		chssBoard.board.getBoard().style.filter = "alpha(opacity=100)";
		parent.getVariationPopUp().style.display = "none";
		
		args.push(select);
		callback.apply(obj, args);
	}
}