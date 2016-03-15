var options = {images_url: "assets/images/chessboard",
               board_size: 360}

function pieceElement(chssPiece, x, y)
{
	this._chssPiece = chssPiece;
	this._x = x;
	this._y = y;
	this._piece = document.createElement("div");
	var size = chssOptions.board_size/8 + "px";
	this._piece.style.width = size;
	this._piece.style.height = size;
	this._piece.id = "pieceElement_" + this._x + "_" + this._y;
	this._piece.className = "pieceElement";
	this._piece.ondrop = this.onDrop;
	this._piece.ondragover = function(event){event.preventDefault();}

	if(this._chssPiece != null)
	{
		this._image = chssOptions.images_url;
		if(this._image.charAt(this._image.length-1)!="/") 
			this._image += "/";
		this._image += this._chssPiece.getColor() + this._chssPiece.getPiececode() + ".gif";
		this._piece.style.backgroundImage = "url('" + this._image + "')";
		this._piece.draggable = true;
		this._piece.ondragstart = this.onDragStart;
	}
}

pieceElement.prototype.onDragStart = function(event)
{
	event.dataTransfer.setData("Text", event.target.id);
	event.dataTransfer.effectAllowed = "move";
}

pieceElement.prototype.onDrop = function()
{
	alert(this.id);
}

pieceElement.prototype.getPiece = function()
{
	return this._piece;
}

function rowElement(pieces, y)
{
	this._y = y;
	this._row = document.createElement("div");
	this._row.id = "rowElement_" + this._y;
	this._row.style.width = chssOptions.board_size + "px";
	this._pieces = [null, null, null, null, null, null, null, null];
	for(var i=0;i<8;i++)
	{
		this._pieces[i] = new pieceElement(pieces[i], i, y);
		this._row.appendChild(this._pieces[i].getPiece());
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

function boardElement(parentDiv, manager)
{
	this._parentDiv = parentDiv;
	this._manager = manager;
	this._rows = [null, null, null, null, null, null, null, null];
	this._board;
	this._boardBackground;
}

boardElement.prototype.draw = function()
{
	this._boardBackground = document.createElement("div");
	this._board = document.createElement("div");
	this._board.style.width = chssOptions.board_size + "px";
	this._board.style.height = chssOptions.board_size + "px";
	this._board.style.position = "relative";
	var board = this._manager.getGame().getBoard();
	for(var i=0;i<8;i++)
	{
		this._rows[i] = new rowElement(board[i], i);
		this._board.appendChild(this._rows[i].getRow());
	}
	this._boardBackground.appendChild(this._board);
	this._parentDiv.appendChild(this._boardBackground);
	this.loadBackground(this._boardBackground, this._board)
}

boardElement.prototype.loadBackground = function(parentDiv, board)
{
	var backgroundImage = chssOptions.images_url;
	if(backgroundImage.charAt(backgroundImage.length-1)!="/") 
		backgroundImage += "/";
	backgroundImage += "Board.png";
	
	var background = new Image();
	background.onload = function()
			{
				parentDiv.style.width = this.width + "px";
				parentDiv.style.height = this.height + "px";
				parentDiv.style.backgroundImage = "url('" + this.src + "')";
		
				board.style.top = (this.height - parseInt(board.style.height))/2 + "px";
				board.style.left = (this.width - parseInt(board.style.width))/2 + "px";
			};
	background.src = backgroundImage;
}

function chssBoard(parentDiv, fen)
{
	this._chssGame = new chssGame(fen);
	this._chssGame.init();
	this._board = new boardElement(parentDiv, this);
	this._board.draw();
}

chssBoard.prototype.getGame = function()
{
	return this._chssGame;
}

