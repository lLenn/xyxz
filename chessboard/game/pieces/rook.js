function chssRook(color)
{
	chssPiece.call(this, color, "R");
}
chssRook.prototype = Object.create(chssPiece.prototype);
chssRook.prototype.constructor = chssRook;

chssRook.prototype.checkValidMove = function(move, game)
{
	var validation = false;
	var x1 = move.getX1();
	var y1 = move.getY1();
	var x2 = move.getX2();
	var y2 = move.getY2();
	var board = game.getBoard();

	if(board[y2][x2] == null || board[y2][x2].getColor() != board[y1][x1].getColor())
	{
		var xdif= x2-x1;
		var ydif = y2-y1;
		if(xdif == 0 || ydif == 0)
		{
			var xdir = 1;
			var ydir = 1;
			if(xdif==0) xdir = 0;
			else if(Math.abs(xdif)==xdif*-1) xdir = -1;
			if(ydif==0) ydir = 0;
			else if(Math.abs(ydif)==ydif*-1) ydir = -1;
			validation = true;
			while(x1+xdir != x2 || y1+ydir != y2)
			{
				x1 = x1+xdir;
				y1 = y1+ydir;
				if(board[y1][x1] != null) validation = false;
			}					
		}
	}
	return validation;	
}

chssRook.prototype.addAvailableMoves = function(x, y, color, game, check)
{
	var blockedCheck = false;
	for(var i=0; i<4; i++)
	{
		switch(i)
		{
			case 0: var xdir = 0,
						ydir = -1,
						xbound = -1,
						ybound = -1;
						break;
			case 1: var xdir = 1,
						ydir = 0,
						xbound = 8,
						ybound = -1;
						break;
			case 2: var xdir = 0,
						ydir = 1,
						xbound = -1,
						ybound = 8;
						break;
			case 3: var xdir = -1,
						ydir = 0,
						xbound = -1,
						ybound = -1;
						break;
		}

		if(!this.checkAvailableMoves(x, y, xdir, ydir, xbound, ybound, color, game, check) && check)
			blockedCheck = true;
	}
	return blockedCheck;
}