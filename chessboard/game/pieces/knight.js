function chssKnight(color)
{
	chssPiece.call(this, color, "N");
}
chssKnight.prototype = Object.create(chssPiece.prototype);
chssKnight.prototype.constructor = chssKnight;

chssKnight.prototype.checkValidMove = function(move, game)
{
	var validation = false;
	var x1 = move.getX1();
	var y1 = move.getY1();
	var x2 = move.getX2();
	var y2 = move.getY2();
	var board = game.getBoard();

	if(board[y2][x2] == null || board[y2][x2].getColor() != board[y1][x1].getColor())
	{
		if(    (x2+2 == x1 && (y2+1 == y1 || y2-1 == y1)) 
			|| (x2+1 == x1 && (y2+2 == y1 || y2-2 == y1)) 
			|| (x2-2 == x1 && (y2+1 == y1 || y2-1 == y1)) 
			|| (x2-1 == x1 && (y2+2 == y1 || y2-2 == y1)))	
			validation = true;
	}
	return validation;
}

chssKnight.prototype.addAvailableMoves = function(x, y, color, game, check)
{
	var blockedCheck = false;
	for(var i=0; i<8; i++)
	{
		var board = game.getBoard();
		
		switch(i)
		{
			case 0: var chkX = x-2,
						chkY = y+1,
						cond = chkX>-1 && chkY<8;
						break;
			case 1: var chkX = x-2,
						chkY = y-1,
						cond = chkX>-1 && chkY>-1;
						break;
			case 2: var chkX = x-1,
						chkY = y-2,
						cond = chkX>-1 && chkY>-1;
						break;
			case 3: var chkX = x+1,
						chkY = y-2,
						cond = chkX<8 && chkY>-1;
						break;
			case 4: var chkX = x+2,
						chkY = y-1,
						cond = chkX<8 && chkY>-1;
						break;
			case 5: var chkX = x+2,
						chkY = y+1,
						cond = chkX<8 && chkY<8;
						break;
			case 6: var chkX = x+1,
						chkY = y+2,
						cond = chkX<8 && chkY<8;
						break;
			case 7: var chkX = x-1,
						chkY = y+2,
						cond = chkX>-1 && chkY<8;
						break;
		}
		
		if(cond)
		{
			if(!this.checkCheck(x, y, chkX, chkY, color, board, game, check, false) && check)
				blockedCheck = true;
		}
	}
	return blockedCheck;
}

chssKnight.prototype.getMovePath = function(x1, y1, x2, y2)
{
	var diffX = x2 - x1,
		diffY = y2 - y1,
		x3 = x1 + (diffX/Math.abs(diffX)) * (Math.abs(diffX)==2?1:0),
		y3 = y1 + (diffY/Math.abs(diffY)) * (Math.abs(diffY)==2?1:0);
	return [[x1, y1], [x3, y3], [x2, y2]];
}
