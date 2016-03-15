function chssPawn(color)
{
	chssPiece.call(this, color, "_");
}
chssPawn.prototype = Object.create(chssPiece.prototype);
chssPawn.prototype.constructor = chssPawn;

chssPawn.prototype.checkValidMove = function(move, game)
{
	var validation = false;
	var x1 = move.getX1();
	var y1 = move.getY1();
	var x2 = move.getX2();
	var y2 = move.getY2();
	var board = game.getBoard();
	
	if(board[y2][x2] == null || (board[y2][x2].getColor() != board[y1][x1].getColor()))
	{
		if(x1==x2 && board[y2][x2] == null)
		{
			if(board[y1][x1].getColor()=="B" && ((y2-y1)==1 || (y1==1 && (y2-y1)==2) && board[y2-1][x2] == null)) validation = true;
			if(board[y1][x1].getColor()=="W" && ((y1-y2)==1 || (y1==6 && (y1-y2)==2) && board[y2+1][x2] == null)) validation = true;
		}
		else
		{
			if((x2-x1)==1 || (x2-x1)==-1)
			{
				if(board[y2][x2] != null)
				{
					if((board[y1][x1].getColor()=="B" && (y2-y1)==1) || (board[y1][x1].getColor()=="W" && (y1-y2)==1))
					    validation = true;
				}
				else
				{
					if(chssPiece.convertToLetter(x2)+(8-y2) == game.getEnpassent() && 
					   ((board[y1][x1].getColor()=="B" && (y2-y1)==1) || (board[y1][x1].getColor()=="W" && (y1-y2)==1)))
					   validation = true;
				}
			}
		}
	}
	return validation;
}

chssPawn.prototype.addAvailableMoves = function(x, y, color, game, check)
{	
	var board = game.getBoard(),
		ydif = undefined,
		blockedCheck = false;
	if((y==1 && color=="B") || (y==6 && color=="W"))
	{
		ydif = color=="W"?-2:2;
		if(!this.checkCheck(x, y, x, y+ydif, color, board, game, check, true) && check)
			blockedCheck = true;
	}
	
	var ybound = color=="W"?-1:8;
	ydif = color=="W"?-1:1;
	
	if(y+ydif != ybound)
	{
		if(!this.checkCheck(x, y, x, y+ydif, color, board, game, check, true) && check)
			blockedCheck = true;
	}
	
	for(var k=0; k<2; k++)
	{
		switch(k)
		{
			case 0: var xdif = -1; 
						xbound = -1;
						break;
			case 1: var xdif = 1,
						xbound = 8;
						break;
		}
		
		if(x+xdif != xbound && y+ydif != ybound)
			this.checkCheck(x, y, x+xdif, y+ydif, color, board, game, check, game.getEnpassent());
	}
	return blockedCheck;
}