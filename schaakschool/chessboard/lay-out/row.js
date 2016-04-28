function rowElement(y)
{
	this._y = y;
	this._row = document.createElement("div");
	this._row.id = "rowElement_" + this._y;
	this._row.style.width = chssOptions.board_size + "px";
	this._pieces = [null, null, null, null, null, null, null, null];
	for(var i=0;i<8;i++)
	{
		this._pieces[i] = new pieceElement(i, y);
		this._row.appendChild(this._pieces[i].getPiece());
	}
}

rowElement.prototype.drawSize = function()
{
	this._row.style.width = chssOptions.board_size + "px";
	for(var i=0;i<8;i++)
	{
		this._pieces[i].drawSize();
	}
}

rowElement.prototype.getRow = function()
{
	return this._row;
}

rowElement.prototype.getPiece = function(x)
{
	return this._pieces[x];
}