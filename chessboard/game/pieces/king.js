function chssKing(color)
{
	chssPiece.call(this, color, "K");
}
chssKing.prototype = Object.create(chssPiece.prototype);
chssKing.prototype.constructor = chssKing;

chssKing.prototype.checkValidMove = function(move, game)
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

		var game_castle = game.getCastle();
		
		if(xdif<2 && ydif<2 && xdif>-2 && ydif>-2)
		{
			validation = true;
		}
		else if((xdif == 2 || xdif == -2) && ydif == 0 && board[y2][x2] == null)
		{
			if(!game.checkCheck(false) && 
			   ((xdif == 2 && y2 == 7 && game_castle[0] == true && board[y2][x2-1] == null) ||
			   (xdif == -2 && y2 == 7 && game_castle[1] == true && board[y2][x2+1] == null) ||
			   (xdif == 2 && y2 == 0 && game_castle[2] == true && board[y2][x2-1] == null) ||
			   (xdif == -2 && y2 == 0 && game_castle[3] == true && board[y2][x2+1] == null))) validation = true;
		} 
	}
	return validation;
}

chssKing.prototype.addAvailableMoves = function(x, y, color, game, check)
{
	var board = game.getBoard();
	
	if(!check)
	{
		for(var i=0; i<4; i++)
		{
			switch(i)
			{
				case 0: var cond = game.getCastle()[0] && y == 7 && board[y][x+1] == null && board[y][x+2] == null,
							xdiff1 = 1,
							xdiff2 = 2;
							break;
				case 1: var cond = game.getCastle()[1] && y == 7 && board[y][x-1] == null && board[y][x-2] == null,
							xdiff1 = -1,
							xdiff2 = -2;
							break;
				case 2: var cond = game.getCastle()[2] && y == 0 && board[y][x+1] == null && board[y][x+2] == null,
							xdiff1 = 1,
							xdiff2 = 2;
							break;
				case 3: var cond = game.getCastle()[3] && y == 0 && board[y][x-1] == null && board[y][x-2] == null,
							xdiff1 = -1,
							xdiff2 = -2;
							break;
			}
			
			//check color
			if(cond && !this.checkCheckReturn(x, y, x+xdiff1, y, board, game) && !this.checkCheckReturn(x, y, x+xdiff2, y, board, game))
			{
				this.addAvailableMove(x+xdiff2, y);
			}
		}
	}
	
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
		
		var chkX = x,
			chkY = y;
		
		if(chkX+xdir!=xbound && chkY+ydir!=ybound && (board[chkY+ydir][chkX+xdir] == null || board[chkY+ydir][chkX+xdir].getColor() != color) && !this.checkCheckReturn(x, y, chkX+xdir, chkY+ydir, board, game))
		{
			this.checkCheck(x, y, chkX+xdir, chkY+ydir, color, board, game, check, false);
		}
	}
	
	return false;
}