function chssQueen(color)
{
	chssPiece.call(this, color, "Q");
}
chssQueen.prototype = Object.create(chssPiece.prototype);
chssQueen.prototype.constructor = chssQueen;

chssQueen.prototype.checkValidMove = function(move, game)
{
	var validation = false;
	var x1 = move.getX1();
	var y1 = move.getY1();
	var x2 = move.getX2();
	var y2 = move.getY2();
	var board = game.getBoard();
	
	if(board[y2][x2] == null || board[y2][x2].getColor() != board[y1][x1].getColor())
	{
		var xdif = x2-x1;
		var ydif = y2-y1;
		if(Math.abs(xdif) == Math.abs(ydif))
		{
			var xdir = 1;
			var ydir = 1;
			if(Math.abs(xdif)==xdif*-1) xdir = -1;
			if(Math.abs(ydif)==ydif*-1) ydir = -1;
			validation = true;
			while(x1+xdir != x2 && y1+ydir != y2)
			{
				x1 = x1+xdir;
				y1 = y1+ydir;
				if(board[y1][x1] != null) validation = false;
			}					
		}
		else if(xdif == 0 || ydif == 0)
		{
			xdir = 1;
			ydir = 1;
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

chssQueen.prototype.addAvailableMoves = function(x, y, color, game, check)
{
	var blockedCheck = false;
	for(var i=0; i<8; i++)
	{
		switch(i)
		{
			case 0: var xdir = -1,
						ydir = -1,
						xbound = -1,
						ybound = -1;
						break;
			case 1: var xdir = 1,
						ydir = -1,
						xbound = 8,
						ybound = -1;
						break;
			case 2: var xdir = 1,
						ydir = 1,
						xbound = 8,
						ybound = 8;
						break;
			case 3: var xdir = -1,
						ydir = 1,
						xbound = -1,
						ybound = 8;
						break;
			case 4: var xdir = 0,
						ydir = -1,
						xbound = -1,
						ybound = -1;
						break;
			case 5: var xdir = 1,
						ydir = 0,
						xbound = 8,
						ybound = -1;
						break;
			case 6: var xdir = 0,
						ydir = 1,
						xbound = -1,
						ybound = 8;
						break;
			case 7: var xdir = -1,
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

chssQueen.prototype.getMovePath = function(x1, y1, x2, y2)
{
	var dirY = (y2 - y1!=0)?(y2 - y1)/Math.abs(y2 - y1):0,
		dirX = (x2 - x1!=0)?(x2 - x1)/Math.abs(x2 - x1):0,
		path = [[x1, y1]],
		x3 = x1,
		y3 = y1;
		
	do
	{
		x3 = x3 + dirX;
		y3 = y3 + dirY;
		path.push([x3, y3])
	}
	while(x3 != x2 || y3 != y2);
		
	return path;
}
