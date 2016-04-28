chssPiece.pieceScore = {K: [10, 0],
					 	Q: [9, 0],
					 	R: [5, 0],
					 	B: [3, 1],
					 	N: [3, 0],
					 	_: [1, 0]}

function chssPiece(color, piececode)
{
	this._color = color;
	this._piececode = piececode;
	this._availableMoves = new Array();
}

chssPiece.prototype.getColor = function()
{
	return this._color;
}

chssPiece.prototype.getPiececode = function()
{
	return this._piececode;
}

chssPiece.prototype.addAvailableMove = function(x, y)
{
	this._availableMoves.push([x, y]);
}

chssPiece.prototype.getAvailableMoves = function()
{
	return this._availableMoves;
}

chssPiece.prototype.resetAvailableMoves = function()
{
	this._availableMoves = new Array();
}

chssPiece.prototype.checkAvailableMoves = function(x, y, xdir, ydir, xbound, ybound, color, game, check)
{
	var board = game.getBoard(),
		chkX = x,
		chkY = y;
	
	while(chkX+xdir!=xbound && chkY+ydir!=ybound)
	{
		chkX = chkX+xdir;
		chkY = chkY+ydir;
		
		if(!check && (board[chkY][chkX] == null || board[chkY][chkX].getColor() != color))
		{
			this.addAvailableMove(chkX, chkY);
			if(board[chkY][chkX] != null && board[chkY][chkX].getColor() != color)
				break;
		}
		else if(board[chkY][chkX] != null && board[chkY][chkX].getColor() == color)
		{
			break;
		}
		else
		{
			if(!this.checkCheck(x, y, chkX, chkY, color, board, game, check, false, null))
				return false
		}
	}
	return true;
}

chssPiece.prototype.checkCheck = function(x, y, chkX, chkY, color, board, game, check, pawn)
{
	if(pawn===true)
		var cond = board[chkY][chkX] == null;
	else if(pawn!==false)
		var cond = (board[chkY][chkX] != null && board[chkY][chkX].getColor() != color) || chssPiece.convertToLetter(chkX)+(8-chkY) == pawn;
	else
		var cond = board[chkY][chkX] == null || board[chkY][chkX].getColor() != color;
	
	if(cond)
	{
		if(!check)
		{
			this.addAvailableMove(chkX, chkY);
		}
		else
		{
			return this.checkCheckReturn(x, y, chkX, chkY, board, game);
		}
	}
	return true;
}

chssPiece.prototype.checkCheckReturn = function(x, y, chkX, chkY, board, game)
{
	var backPiece = board[chkY][chkX];
	board[chkY][chkX] = board[y][x];
	board[y][x] = null;
	
	var check = game.checkCheck(false);

	board[y][x] = board[chkY][chkX];
	board[chkY][chkX] = backPiece;
	
	if(!check)
	{
		this.addAvailableMove(chkX, chkY);
	}
	
	return check;
}

chssPiece.comparePieceScore = function(pieceA, pieceB)
{
	if(pieceA.getColor().toUpperCase() != pieceB.getColor().toUpperCase())
		return pieceA.getColor().toUpperCase() == "W"?1:-1;
	
	var scoreA = chssPiece.pieceScore[pieceA.getPiececode()],
		scoreB = chssPiece.pieceScore[pieceB.getPiececode()];
	
	return (scoreA[0] == scoreB[0]?(scoreA[1] == scoreB[1]?0:(scoreA[1] > scoreB[1]?1:-1)):(scoreA[0] > scoreB[0]?1:-1));
}

chssPiece.Factory = function(color, piececode)
{
	switch(piececode.toLowerCase())
	{
		case "k":  return new chssKing(color); break;
		case "q":  return new chssQueen(color); break;
		case "_":  return new chssPawn(color); break;
		case "p":  return new chssPawn(color); break;
		case "r":  return new chssRook(color); break;
		case "b":  return new chssBishop(color); break;
		case "n":  return new chssKnight(color); break;
		default: throw new Error("Incorrect piece string: " + piececode);
	}
}

chssPiece.convertToPiece = function(color, piece)
{
	switch(piece)
	{
		case chssLanguage.convertPiece("K"): return new chssKing(color); break;
		case chssLanguage.convertPiece("Q"): return new chssQueen(color); break;
		case chssLanguage.convertPiece("B"): return new chssBishop(color); break;
		case chssLanguage.convertPiece("N"): return new chssKnight(color); break;
		case chssLanguage.convertPiece("R"): return new chssRook(color); break;
		case "a":
		case "b":
		case "c":
		case "d":
		case "e":
		case "f":
		case "g":
		case "h": return new chssPawn(color); break;
		default: throw new Error("Bad move description: " + piece);
	}
}

chssPiece.piececodeToUnicode = function(color, piececode)
{
	switch(piececode.toLowerCase())
	{
		case 'k': return color.toLowerCase()=="w"?'&#x2654;':'&#x265A;'; break;
		case 'q': return color.toLowerCase()=='w'?'&#x2655;':'&#x265B;'; break;
		case '_': return color.toLowerCase()=='w'?'&#x2659;':'&#x265F;'; break;
		case 'p': return color.toLowerCase()=='w'?'&#x2659;':'&#x265F;'; break;
		case 'r': return color.toLowerCase()=='w'?'&#x2656;':'&#x265C;'; break;
		case 'b': return color.toLowerCase()=='w'?'&#x2657;':'&#x265D;'; break;
		case 'n': return color.toLowerCase()=='w'?'&#x2658;':'&#x265E;'; break;
		default: throw new Error("Incorrect piece string: " + piececode);
	}
}

chssPiece.piececodeToUnicodeBackground = function(piececode)
{
	switch(piececode.toLowerCase())
	{
		case 'k': return '&#xE254'; break;
		case 'q': return '&#xE255'; break;
		case '_': return '&#xE259'; break;
		case 'p': return '&#xE259'; break;
		case 'r': return '&#xE256'; break;
		case 'b': return '&#xE257'; break;
		case 'n': return '&#xE258'; break;
		default: throw new Error("Incorrect piece string: " + piececode);
	}
}

chssPiece.convertToMove = function(move, game)
{
	var color = game.active(false);
	if(move != "O-O" && move != "O-O-O")
	{
		var piece = chssPiece.convertToPiece(color, move.charAt(0));
		var x1 = -1;
		var y1 = -1;
		var x2 = -1;
		var y2 = -1;
		var checkx1 = -1;
		var checky1 = -1;
		var regExpr = new RegExp("[a-h][1-9]|[a-h]|[1-9]","g");
		var hits = move.match(regExpr);
		if(hits.length==1)
		{
			x2 = chssPiece.convertToNumber(String(hits[0]).charAt(0));
			y2 = 8-parseInt(String(hits[0]).charAt(1));
		}
		else if(hits.length==2)
		{
			x2 = chssPiece.convertToNumber(String(hits[1]).charAt(0));
			y2 = 8-parseInt(String(hits[1]).charAt(1));
			if(String(hits[0]).charAt(1)!="")
			{
				checkx1 = chssPiece.convertToNumber(String(hits[0]).charAt(0));
				checky1 = 8-parseInt(String(hits[0]).charAt(1));
			}
			else
			{	
				var first = String(hits[0]).charAt(0);
				if(isNaN(parseInt(first)))
				{
					if(piece.getPiececode() == "_" && piece.getColor() == color)
					{
						if(color == "W")
							checky1 = y2+1;
						else
							checky1 = y2-1;
					}
					checkx1 = chssPiece.convertToNumber(first);
				}
				else
				{
					checky1 = 8-parseInt(first);
				}
			}
		}
		else throw new Error("Wrong move string.");
		var moveCheck = new chssMove(NaN, NaN, NaN, NaN);
		var numberMoves = 0;
		for(var i=0;i<8;i++)
		{
			for(var j=0;j<8;j++)
			{
				if(game.getBoard()[i][j] != null && game.getBoard()[i][j].getPiececode() == piece.getPiececode() && game.getBoard()[i][j].getColor() == piece.getColor())
				{
					moveCheck.addMove(j,i,x2,y2);
					if(piece.checkValidMove(moveCheck,game))
					{
						if(hits.length==1)
						{
							if(numberMoves != 1)
							{
								x1 = j;
								y1 = i;
							}
							numberMoves++;
						}
						else
						{
							if((checkx1 == j && checky1 == i) || ((piece.getPiececode() == "N" || piece.getPiececode() == "R") && piece.getColor() == color && (checkx1 == j || checky1 == i)))
							{
								if(numberMoves != 1)
								{
									x1 = j;
									y1 = i;
								}
								numberMoves++;
							}
						}
						if(x1 != -1 && y1 != -1)
						{
							if((piece.getPiececode() == "N" || piece.getPiececode() == "R") && piece.getColor() == color)
								game.adjustBoard(moveCheck, -1, true);
							else
								game.adjustBoard(new chssMove(x1,y1,x2,y2), -1, true);

							if(game.checkCheck(false) && numberMoves != 2)
							{
								x1 = -1;
								y1 = -1;
								numberMoves--;				
							}
							else if(game.checkCheck(false) && numberMoves == 2)
								numberMoves--;
							
							game.changeBoard(String(game.getCurrentMove()), false);
						}
					}
				}
			}
		}

		if(x1 != -1 && y1 != -1 && x2 != -1 && y2 != -1 && numberMoves == 1)
		{
			var moveRtn = new chssMove(NaN,NaN,NaN,NaN);
			moveRtn.addMove(x1, y1, x2, y2);
			var searchString = "=["+
				chssLanguage.convertPiece("Q")+
				chssLanguage.convertPiece("B")+
				chssLanguage.convertPiece("N")+
				chssLanguage.convertPiece("R")+"]";
			if(piece.getPiececode() == "_" && piece.getColor() == color && (move.search(searchString) == move.length-2 || move.search(searchString) == move.length-3) && move.search(searchString) != -1)
			{
				moveRtn.setPromotionPiece(chssPiece.Factory(color, chssLanguage.convertPieceToEnglish(move.charAt(move.search(searchString)+1))));
				return moveRtn;
			}
			else return moveRtn;
		}
		else
			throw new Error("Wrong move string:" + move);
	}
	else
	{
		var king = new chssKing(color);
		var kingMove = new chssMove(NaN,NaN,NaN,NaN);
		if(move == "O-O")
		{
			if(color == "W")
			{
				kingMove.addMove(4,7,6,7);
				if(king.checkValidMove(kingMove,game))
					return kingMove;
			}
			else
			{
				kingMove.addMove(4,0,6,0);
				if(king.checkValidMove(kingMove,game))
					return kingMove;
			}
		}
		else if(move == "O-O-O")
		{
			if(color == "W")
			{
				kingMove.addMove(4,7,2,7);
				if(king.checkValidMove(kingMove,game))
					return kingMove;
			}
			else
			{
				kingMove.addMove(4,0,2,0);
				if(king.checkValidMove(kingMove,game))
					return kingMove;
			}
		}
		throw new Error("Wrong castle move");
	}
}

chssPiece.convertMoveToPGN = function(move, color, board, enpassent, currentmove)
{
	var piece = chssLanguage.convertPiece(board[move.getY1()][move.getX1()].getPiececode());
	var currentNot = "";
	if(piece!=chssLanguage.convertPiece("_"))
		currentNot += piece;
	switch(piece)
	{
		case chssLanguage.convertPiece("N"): 	
					for(var i=-2;i<=2;i++)
					{
						for(var j=-2;j<=2;j++)
						{
							var iN=-1;
							var jN=-1;
							if(i!=0 && j!=0)
							{
								if(i==2 || i==-2)
								{
									if(j==1 || j==-1)
									{
										iN = move.getY2()+i;
										jN = move.getX2()+j;
									}	
								}
								else
								{
									if(j==2 || j==-2)
									{
										iN = move.getY2()+i;
										jN = move.getX2()+j;
									}
								}
							}
							if(iN>-1 && iN<8 && jN>-1 && jN<8 && board[iN][jN] != null && board[iN][jN].getPiececode() == "N" && board[iN][jN].getColor() == color)
							{
								if((move.getY1() != iN && move.getX1() != jN) || (move.getY1() == iN && move.getX1() != jN))
									currentNot += chssPiece.convertToLetter(move.getX1());
								else if(move.getY1() != iN && move.getX1() == jN)
									currentNot += 8-move.getY1();
							} 
						}
					}
					break;
		case chssLanguage.convertPiece("R"):	
					var idif = move.getY2()-move.getY1();
					for(var k=1;k<=4;k++)
					{
						var idir = 0;
						var jdir = 0;
						switch(k)
						{
							case 1: idir = 0; jdir = 1; break;
							case 2: idir = 0; jdir = -1; break;
							case 3: idir = 1; jdir = 0; break;
							case 4: idir = -1; jdir = 0; break;
						}
						for(var l=1;l<=8;l++)
						{
							var iT = move.getY2() + idir*l;
							var jT = move.getX2() + jdir*l;
							if(iT>-1 && iT<8 && jT>-1 && jT<8 && board[iT][jT] != null && board[iT][jT].getPiececode() == "R" && board[iT][jT].getColor() == color)
							{
								if(!(iT == move.getY1() && jT == move.getX1()))
								{
									if(idif == 0)
										currentNot += chssPiece.convertToLetter(move.getX1());
									else
									{
										if(jT != move.getX1())
											currentNot += chssPiece.convertToLetter(move.getX1());
										else
											currentNot += 8-move.getY1();
									}
								}
								else break;
							}
							else if(iT>-1 && iT<8 && jT>-1 && jT<8 && board[iT][jT] != null)
								break;									
						}
					}
					break;
		case chssLanguage.convertPiece("_"):   
					if(!(move.getX1()==move.getX2()))
					{	
						currentNot += chssPiece.convertToLetter(move.getX1());
						if((move.getX2()-move.getX1())==1 || (move.getX2()-move.getX1())==-1)
						{
							if(board[move.getY2()][move.getX2()] == null)
							{
								var moveLegal = chssPiece.convertToLetter(move.getX2())+(8-move.getY2()) == enpassent;
								if(board[move.getY1()][move.getX1()].getColor()=="W" && move.getY1()==4 && moveLegal) currentNot += "x";
								if(board[move.getY1()][move.getX1()].getColor()=="B" && move.getY1()==3 && moveLegal) currentNot += "x";
							}
						}									
					}
					break;
	}
	if(board[move.getY2()][move.getX2()]!=null) currentNot += "x";
	currentNot += chssPiece.convertToLetter(move.getX2())+""+(8-move.getY2());
	if(piece == chssLanguage.convertPiece("K"))
	{
		if((move.getX2()-move.getX1()) == -2) currentNot = "O-O-O";
		else if((move.getX2()-move.getX1()) == 2) currentNot = "O-O";	
	}
	return currentNot;
}

chssPiece.convertToLetter = function(column)
{
	switch(column)
	{
		case 0: return "a"; break;
		case 1: return "b"; break;
		case 2: return "c"; break;
		case 3: return "d"; break;
		case 4: return "e"; break;
		case 5: return "f"; break;
		case 6: return "g"; break;
		case 7: return "h"; break;
		default: throw new Error("Wrong column.");
	}
}

chssPiece.convertToNumber = function(column)
{
	switch(column)
	{
		case "a": return 0; break;
		case "b": return 1; break;
		case "c": return 2; break;
		case "d": return 3; break;
		case "e": return 4; break;
		case "f": return 5; break;
		case "g": return 6; break;
		case "h": return 7; break;
		default: throw new Error("Wrong column.");
	}
}