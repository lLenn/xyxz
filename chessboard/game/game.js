chssGame.results = {WHITE: "white",
					BLACK: "black",
					DRAW: "draw",
					NONE: "none"};

function chssGame(arg)//default
{
	if(arg instanceof chssPGNFile)
		this._pgnfile = arg
	else
	{
		this._pgnfile = new chssPGNFile();
		this._pgnfile.setFen(arg);
	}
	
	this._board = undefined;
	this._castle = undefined;
	this._enpassent = undefined;
	
	this._currentmove = 0;
	this._variationmove = 0;
	this._variationid = NaN;
	this._promotion = undefined;
	this._edit = false;
	
	this.initBoard();
}

chssGame.prototype.initBoard = function()
{
	//try
	//{
		if(this._pgnfile.getFen() == null)
			this.standardBoard();
		else
			this.loadBoard();
		this.addAvailableMoves();
	//}
	//catch(e)
	//{
		//console.info(e);
	//}
}

chssGame.prototype.standardBoard = function()
{
	this._board = new Array(new Array(new chssRook("B") , new chssKnight("B"), new chssBishop("B"), new chssQueen("B"), new chssKing("B"), new chssBishop("B"), new chssKnight("B"), new chssRook("B")),
							new Array(new chssPawn("B"), new chssPawn("B"), new chssPawn("B"), new chssPawn("B"), new chssPawn("B"), new chssPawn("B"), new chssPawn("B"), new chssPawn("B")),
							new Array(null, null, null, null, null, null, null, null),
							new Array(null, null, null, null, null, null, null, null),
							new Array(null, null, null, null, null, null, null, null),
							new Array(null, null, null, null, null, null, null, null),
							new Array(new chssPawn("W"), new chssPawn("W"), new chssPawn("W"), new chssPawn("W"), new chssPawn("W"), new chssPawn("W"), new chssPawn("W"), new chssPawn("W")),
							new Array(new chssRook("W") , new chssKnight("W"), new chssBishop("W"), new chssQueen("W"), new chssKing("W"), new chssBishop("W"), new chssKnight("W"), new chssRook("W")));
	this._castle = [true,true,true,true];
	this._enpassent = "-";	
}

chssGame.prototype.emptyBoard = function()
{
	this._board = [[null, null, null, null, null, null, null, null],
	               [null, null, null, null, null, null, null, null],
	               [null, null, null, null, null, null, null, null],
	               [null, null, null, null, null, null, null, null],
	               [null, null, null, null, null, null, null, null],
	               [null, null, null, null, null, null, null, null],
	               [null, null, null, null, null, null, null, null],
	               [null, null, null, null, null, null, null, null]];
	this._castle = [false, false, false, false];
	this._enpassent = "-";
}

chssGame.prototype.loadBoard = function()
{
	var fen = this._pgnfile.getFen();
	this.emptyBoard();
	for(var i=0; i<8; i++)
	{
		var index = fen.indexOf("/");
		var rowString = null;
		if(index != -1 && i<7)
		{
			rowString = fen.substring(0,index);
			fen = fen.substring(index+1,fen.length);
		}
		else if(index == -1 && i==7)
		{
			index = fen.indexOf(" ");
			if(index != -1)
			{
				rowString = fen.substring(0,index);
				fen = fen.substring(index+1,fen.length)
			}
		}
		if(rowString == null)
			throw new Error("Incorrect FEN string");
		
		var j = 0;
		while(j<8)
		{
			var piece = rowString.substring(0,1);
			var skip = parseInt(piece);
			if(isNaN(skip))
			{
				this._board[i][j] = this.selectPiece(piece);
				j++;
			}
			else
			{
				j = j+skip;
			}
			if(rowString.length > 1 && j<8)
				rowString = rowString.substring(1, rowString.length);
			else if(rowString.length > 1 && j >= 8)
				throw new Error("Incorrect FEN string");
			else if(j != 8)
				throw new Error("Incorrect FEN string");
		}	
	}

	//Check whose turn it is.
	var indexSpace = fen.indexOf(" ");
	if(indexSpace!=-1)
	{
		var turn = fen.substring(0,indexSpace);
		fen = fen.substring(indexSpace+1,fen.length);
		if(turn == "w" || turn == "b")
		{
			if(turn == "b")
			{
				var firstmove = new chssMove(NaN, NaN, NaN, NaN);
				firstmove.setNotation("...");
				if(this._pgnfile.getMoves().length == 0 || (this._pgnfile.getMoves().length != 0 && this._pgnfile.getMoves()[0].getNotation() != "..."))
				{
					this._pgnfile.getMoves().unshift(firstmove);
					this._currentmove++;
				}
			}
		}
		else throw new Error("Incorrect FEN string");
	} 
	else throw new Error("Incorrect FEN string");
	
	//Check castle
	indexSpace = fen.indexOf(" ");
	if(indexSpace!=-1)
	{
		var castleString = fen.substring(0,indexSpace);
		fen = fen.substring(indexSpace+1,fen.length);
		var castleArray = [false,false,false,false];
		var check = castleString.match(/[KQkq-]/);
		if(check != null)
		{
			check = castleString.match(/[K]/);
			if(check != null && check[0]=="K")
				castleArray[0] = true;
			check = castleString.match(/[Q]/);
			if(check != null && check[0]=="Q")
				castleArray[1] = true;
			check = castleString.match(/[k]/);
			if(check != null && check[0]=="k")
				castleArray[2] = true;
			check = castleString.match(/[q]/);
			if(check != null && check[0]=="q")
				castleArray[3] = true;
			this._castle = castleArray;	
		} 
		else throw Error("Incorrect FEN string");				
	} 
	else throw Error("Incorrect FEN string");
	//check enpassent
	indexSpace = fen.indexOf(" ");
	if(indexSpace!=-1)
	{
		var enpassentString = fen.substring(0,indexSpace);
		fen = fen.substring(indexSpace+1,fen.length);
		var checkEnp = enpassentString.match(/[a-h][3,6]|[-]/);
		if(checkEnp != null)
		{
			this._enpassent = checkEnp[0];
		}
		else throw Error("Incorrect FEN string");
	} 
	else throw Error("Incorrect FEN string");
}

chssGame.prototype.loadMoves = function()
{
	//hack!!!!
	if(this._pgnfile == null)
		this._pgnfile = new chssPGNFile();
	//End hack
	if(this._pgnfile.getMoves() == null)
		this._pgnfile.setMoves(new Array());
	this.changeBoard("Start", false);
	var valid = true;
	for(var i=0; i<this._pgnfile.getMoves().length;i++)
	{
		var move = this._pgnfile.getMoves()[i];
		try
		{
			if(move.getNotation() != "..." && valid)
			{
				var moveTemp = chssPiece.convertToMove(move.getNotation(), this);
				move.addMove(moveTemp.getX1(), moveTemp.getY1(), moveTemp.getX2(), moveTemp.getY2());
				move.setPromotionPiece(moveTemp.getPromotionPiece());
				move.setValid(true);
				this._currentmove++;
				this.adjustBoard(moveTemp, this.getCurrentMove()-1, true);
				this.addAvailableMoves();
				move.setBoard(this.copyBoard(null));
				move.setEnpassent(this._enpassent);
				move.setCastle([this._castle[0], this._castle[1], this._castle[2], this._castle[3]]);
				move.setPromotion(this._promotion);
			}
			else if(move.getNotation() != "...")
			{
				move.setValid(false);
			}	
		}
		catch(e)
		{
			console.trace();
			console.info(e);
			move.setValid(false);
			valid = false;
		}
	}
	this._promotion = false;
	this.changeBoard("Start", false);
	this.loadVariations(0);
}

chssGame.prototype.loadVariations = function(parentId) //default = 0
{
	for(var i=0; i<this._pgnfile.getVariations().length;i++)
	{
		var varList = this._pgnfile.getVariations()[i];
		if(varList.getParentVariationId() == parentId)
		{
			this._variationid = varList.getParentVariationId();
			this.changeBoard(String(varList.getHalfmove()), false);
			
			this._variationmove = 0;
			this._variationid = varList.getVariationId();
			
			var valid = true;
			var firstMove = true;
			for(var j=0;j<varList.getMoves().length;j++)
			{
				var move = varList.getMoves()[j];
				try
				{
					if(move.getNotation() != "..." && valid)
					{
						var moveTemp = chssPiece.convertToMove(move.getNotation(), this);
						move.addMove(moveTemp.getX1(), moveTemp.getY1(), moveTemp.getX2(), moveTemp.getY2());
						move.setPromotionPiece(moveTemp.getPromotionPiece());
						move.setValid(true);
						if(firstMove)
						{
							this._variationmove = 0;
							firstMove = false;
						}
						this._variationmove++;
						this._variationid = varList.getVariationId();
						this.adjustBoard(moveTemp, this.getCurrentMove()-1, true);
						this.addAvailableMoves();
						move.setBoard(this.copyBoard(null));
						move.setEnpassent(this._enpassent);
						move.setCastle([this._castle[0], this._castle[1], this._castle[2], this._castle[3]]);
						move.setPromotion(this._promotion);
					}
					else if(move.getNotation() != "...")
					{
						move.setValid(false);
					}
				}
				catch(e)
				{	
					console.info(e);
					move.setValid(false);
					valid = false;
				}
			}
			this.loadVariations(varList.getVariationId());
		}
	}
	this._promotion = false;
	this.changeBoard("Start", false);
}

chssGame.prototype.checkMove = function(x1, y1, x2, y2)
{
	return this.getResult() == chssGame.results.NONE && this.active() == this._board[y1][x1].getColor() && this._board[y1][x1].checkValidMove(new chssMove(x1,y1,x2,y2), this);
}

chssGame.prototype.addMoveWithPromotion = function(move)
{
	var rt = this.addMove(move.getX1(), move.getY1(), move.getX2(), move.getY2());
	
	if(move.getPromotionPiece() != null)
		this.addPromotionPiece(move.getPromotionPiece().getColor(), move.getPromotionPiece().getPiececode());
	
	return rt;
}

chssGame.prototype.addMove = function(x1, y1, x2, y2, variation)
{
	//console.log("bij externe promotie, verplicht het promotiestuk te geven");
	var move = new chssMove(x1,y1,x2,y2),
		validation = this._edit == true && this.checkMove(x1, y1, x2, y2),
		capturedPiece = this._board[y2][x2];
	if(validation)
	{
		var moveNot = chssPiece.convertMoveToPGN(move, this.active(), this._board, this._enpassent, this.getCurrentMove());
		this.adjustBoard(move, -1, true);
		if(this.checkCheck(false))
		{
			this._promotion = false;
			this.rebuildBoard();
		}
		else
		{
			var checkCastleCheck = false;
			if(this._board[y2][x2].getPiececode() == "K")
			{ 	
				if((x2-x1) == -2) this.adjustBoard(new chssMove(x2,y2,x2+1,y2), -1, true);
				else if((x2-x1) == 2) this.adjustBoard(new chssMove(x2,y2,x2-1,y2), -1, true);
				if(this.checkCheck(false))
					checkCastleCheck = true;
				this._promotion = false;
				this.rebuildBoard();
				this.adjustBoard(move, -1, true);	
			}
			if(checkCastleCheck)
			{
				this.rebuildBoard();
			}
			else
			{
				if(this.getCurrentMove() != this.getMovesLength(true, false))
				{
					if(variation == false)
					{	
						this.removeMoves();
					}
					else
					{
						var variationList = new chssVariationList();
						var halfmove = (isNaN(this._variationid) || this._variationid == 0)?this._currentmove:this.getVariationList(NaN).getHalfmove()+this._variationmove;
						variationList.setHalfmove(halfmove);
						var id = 1;
						for(var i=0; i<this._pgnfile.getVariations().length;i++)
						{
							if(id<=this._pgnfile.getVariations()[i].getVariationId())
								id = this._pgnfile.getVariations()[i].getVariationId()+1;
						}
						variationList.setVariationId(id);
						variationList.setUserId(this._pgnfile.getUserId());
						var black = variationList.getHalfmove()%2;
						if(black == 1)
						{
							var firstmove = new chssMove(NaN, NaN, NaN, NaN);
							firstmove.setNotation("...");
							variationList.getMoves().push(firstmove);
						}
						variationList.setParentVariationId((isNaN(this._variationid) || this._variationid == 0)?0:this.getVariationList(NaN).getVariationId());
						this._pgnfile.getVariations().push(variationList);	
						this._variationid = variationList.getVariationId();
						this._variationmove = 0;
					}
				}
				else
				{
					this.removeMoves();
				}
	
				
				if(isNaN(this._variationid) || this._variationid == 0)
				{
					this._pgnfile.getMoves().push(move);
					this._currentmove++;
				}
				else
				{
					for(var i=0;i<this._pgnfile.getVariations().length;i++)
					{
						if(this._pgnfile.getVariations()[i].getVariationId() == this._variationid)
						{
							this._pgnfile.getVariations()[i].getMoves().push(move);
							this._variationmove++;
						}
					}
				}

				this.addAvailableMoves();
				this.addRemovedPieces(move, capturedPiece);
				move.setBoard(this.copyBoard(null));
				//move.setRemovedPiecesFromBoard();
				move.setFen(this.newFenFromCurrentBoard(false, true, false, true));
				move.setEnpassent(this._enpassent);
				move.setCastle([this._castle[0], this._castle[1], this._castle[2], this._castle[3]]);
				move.setPromotion(this._promotion);
				
				var prevMove = this.getMove(this.getCurrentMove()-2);
				if(prevMove != null && prevMove.getNotation() != "...")
				{
					if(prevMove.getBoard()[y2][x2] == null && prevMove.getBoard()[y1][x1].getPiececode()!="_")
					{
						move.setHalfmoveClock(prevMove.getHalfmoveClock()+1);
					}
					var prevMoves = this.getMovesList();
					var count = 1;
					for(var i=0; i<prevMoves.length-1; i++)
					{
						var newMove = prevMoves[i];
						if(newMove.getFen() == move.getFen())
							count++;
					}
					move.setFenClock(count);
				}
				
				if(this.checkMate())
				{
					moveNot+="#";
					move.setResult(this.active(true)=="B"?chssGame.results.BLACK:chssGame.results.WHITE);
				}
				else if(this.checkCheck(false))
				{
					moveNot+="+";
					move.setResult(chssGame.results.NONE);
				}
				else if(this.checkDraw(move))
					move.setResult(chssGame.results.DRAW);
				else
					move.setResult(chssGame.results.NONE);
				move.setNotation(moveNot);

				return true;
			}
		}
	}
	return false;
}

chssGame.prototype.checkDraw = function(move)
{
	if(typeof move === 'undefined')
		move = this.getMovesList(this.getCurrentMove());
	
	if(move.getFenClock() == 3 || move.getHalfmoveClock() == 50)
		return true;
	
	var board = move.getBoard();
	for(var i=0;i<8;i++)
	{
		for(var j=0;j<8;j++)
		{
			if(board[i][j] != null && board[i][j].getColor().toLowerCase() == this.active().toLowerCase() && board[i][j].getAvailableMoves().length>0)
				return false;
		}
	}
	return true;
}

chssGame.prototype.addAvailableMoves = function()
{
	var check = this.checkCheck(false),
		color = this.active();
	for(var i=0; i<=7; i++)
	{
		for(var j=0; j<=7; j++)
		{
			if(this._board[i][j] != null)
			{
				this._board[i][j].resetAvailableMoves();
				if(this._board[i][j].getColor() == color)
				{
					var currentPiece = this._board[i][j],
						blockedCheck = currentPiece.addAvailableMoves(j, i, color, this, check),
						coorBlock = new Array(),
						pieceBlock = null;
					
					if(blockedCheck)
						coorBlock = currentPiece.getAvailableMoves()[0].slice(0);

					this._board[i][j] = null;
					if(blockedCheck)
					{
						pieceBlock = this._board[coorBlock[1]][coorBlock[0]];
						this._board[coorBlock[1]][coorBlock[0]] = currentPiece;
					}
					var coors = this.checkCheck(true);
					if(coors.length>0)
					{
						var valCheck = false;
						for(var k=0; k<coors.length; k++)
						{
							var coor = coors[k];
							if(!this._board[coor[1]][coor[0]].checkValidMove(new chssMove(coor[0],coor[1],j,i),this))
							{
								valCheck = true;
							}
						}
						if(!valCheck)
						{
							currentPiece.resetAvailableMoves();
						}
					}
					this._board[i][j] = currentPiece;
					if(blockedCheck)
						this._board[coorBlock[1]][coorBlock[0]] = pieceBlock;
				}
			}
		}
	}
}

chssGame.prototype.addRemovedPieces = function(move, capturedPiece)
{
	var prev_move = this.getMove(this.getCurrentMove()-2);
	if(prev_move != null && prev_move.getNotation() != "...")
		move.setRemovedPieces(prev_move.getRemovedPieces());
	if(capturedPiece != null)
		move.addRemovedPiece(capturedPiece);
}

chssGame.prototype.addLastMove = function(x1, y1, x2, y2)
{
	var prevEdit = this._edit;
	this._edit = true;
	this.changeColorToMove(this._currentmove == 1);
	this._board[y1][x1] = this._board[y2][x2];
	this._board[y2][x2] = null;
	var prevFen = this._pgnfile.getFen();
	this.newFenFromCurrentBoard(false, false, true, false);
	this._pgnfile.getMoves().push(new chssMove(1,1,1,1));
	var returnVal = this.addMove(x1, y1, x2, y2, true);
	if(returnVal == false)
	{
		this.changeColorToMove(this._currentmove == 1);
	}
	else
	{
		this._pgnfile.setLastMove(this._pgnfile.getVariations()[this._pgnfile.getVariations().length-1].getMoves()[this._currentmove]);
		this._pgnfile.getVariations().pop();
		this._pgnfile.getMoves().pop();
		this.changeColorToMove(this._currentmove == 1);
		this._variationid = NaN;
		this._variationmove = 0;
	}
	this._pgnfile.setFen(prevFen);
	this._edit = prevEdit;
	return returnVal;
}

chssGame.prototype.adjustBoard = function(move, movenumber, checkPromotion) //default: movenumber = -1, checkPromotion = true
{
	this._board[move.getY2()][move.getX2()] = this._board[move.getY1()][move.getX1()];
	this._board[move.getY1()][move.getX1()] = null;
	
	//Enpassant
	
	var enpassentBackup = this._enpassent;
	this._enpassent = "-";
	if(this._board[move.getY2()][move.getX2()] == null)
		var str = "";
	if(this._board[move.getY2()][move.getX2()].getPiececode() == "_")
	{
		//If enpassant: remove the captured piece
		if(chssPiece.convertToLetter(move.getX2())+(8-move.getY2()) == enpassentBackup)
		{
			if(move.getY2() == 2)
				this._board[move.getY2()+1][move.getX2()] = null;
			else	
				this._board[move.getY2()-1][move.getX2()] = null;
		}
		//If pawn 2 move :register enpassantfield
		if((move.getY2() - move.getY1() == 2 && move.getY2() == 3) || (move.getY2() - move.getY1() == -2 && move.getY2() == 4))
		{
			if(move.getY2() == 3)
				this._enpassent = chssPiece.convertToLetter(move.getX2()) + (move.getY2()+3);
			else
				this._enpassent = chssPiece.convertToLetter(move.getX2()) + (move.getY2()-1);
		}
		//If pawn up for promotion: register promotion	
		if((move.getY2() == 0 || move.getY2() == 7) && checkPromotion)
		{
			if(movenumber != -1 && movenumber < this.getMovesList().length)
			{
				if(this.getMove(movenumber).getPromotionPiece() != null) this._board[move.getY2()][move.getX2()] = this.getMove(movenumber).getPromotionPiece();
				else {  this._promotion = true; }
			}
			else { this._promotion = true; }
		}
	}
	//castle
	else if(this._board[move.getY2()][move.getX2()].getPiececode()=="K")
	{ 	
		//Change castle booleans
		if(move.getX1() == 3 && move.getY1() == 7)
		{
			this._castle[0] = false;
			this._castle[1] = false;
		}
		else if(move.getX1() == 3 && move.getY1() == 0)
		{
			this._castle[2] = false;
			this._castle[3] = false;
		}
		//Change position rook
		if((move.getX2()-move.getX1()) == -2) this.adjustBoard(new chssMove(0,move.getY1(),3,move.getY1()), -1, true);
		else if((move.getX2()-move.getX1()) == 2) this.adjustBoard(new chssMove(7, move.getY1(),5,move.getY1()), -1, true);
	}
	else if(this._board[move.getY2()][move.getX2()].getPiececode()=="R")
	{
		if(move.getY1() == 7)
		{
			if(move.getX1() == 7)
				this._castle[0] = false;
			else if(move.getX1() == 0)
				this._castle[1] = false;
		}
		else if(move.getY1() == 0)
		{
			if(move.getX1() == 7)
				this._castle[2] = false;
			else if(move.getX1() == 0)
				this._castle[3] = false;
		}
	}
}

chssGame.prototype.checkCheck = function(rtnCoor)
{
	var kingCoord = this.getKingCoordinates();
	var validation = false;
	var coors = new Array();
	if(kingCoord != null)
	{
		var xk = kingCoord[0];
		var yk = kingCoord[1];
		
		for(var i = 0; i<8; i++)
		{
			for(var j = 0; j<8; j++)
			{
				if(this._board[i][j] != null && this._board[i][j].getColor() == this.active(true) && this._board[i][j].checkValidMove(new chssMove(j,i,xk,yk),this))
				{
					validation = true;
					if(rtnCoor)
						coors.push([j, i]);
				}
			}
		}
	}
	if(!rtnCoor)
		return validation;
	else
		return coors;
}

chssGame.prototype.checkMate = function()
{
	var kingCoord = this.getKingCoordinates();
	var validation = false;
	
	if(kingCoord != null)
	{
		var xk = kingCoord[0];
		var yk = kingCoord[1];
		
		if(this.checkCheck(false))
		{
			validation = true;
			for(var i = 0; i<8; i++)
			{
				for(var j = 0; j<8; j++)
				{
					for(var l = 0; l<8; l++)
					{
						for(var k = 0; k<8; k++)
						{
						
							var move = new chssMove(j,i,k,l);
							if(this._board[i][j] != null && this._board[i][j].getColor() == this.active(false) && this._board[i][j].checkValidMove(move,this))
							{
								this.adjustBoard(move, 0, false);
								if(!this.checkCheck(false))
									validation = false;
								this.rebuildBoard();
							}
						}
					}	
				}
			}
		}
	}
	return validation;
}

chssGame.prototype.getKingCoordinates = function()
{
	var xk = -1;
	var yk = -1;
	for(var l=0; l<8; l++)
	{
		for(var k=0; k<8; k++)
		{
			if(this._board[l][k] != null && this._board[l][k].getPiececode() == "K" && this._board[l][k].getColor() == this.active(false))
			{
				xk = k;
				yk = l;
				break;
			}
		}
	}
	if(xk == -1 || yk == -1)
		return null;
					
	return [xk, yk];
}

chssGame.prototype.active = function(invert) //default: false
{
	var currentmove = this._currentmove;
	if(!isNaN(this._variationid) && this._variationid != 0)
		currentmove = this.getCurrentMove();
	if(currentmove%2 == 0)
	{
		if(!invert) return "W";
		else return "B";
	}
	else 
	{
		if(!invert) return "B";
		else return "W";
	}
}

chssGame.prototype.changeColorToMove = function(white)
{
	if(white)
	{
		if(this._pgnfile.getMoves().length != 0 && this._pgnfile.getMoves()[0].getNotation() == "...")
		{
			this._pgnfile.getMoves().shift();
			this._currentmove--;
		}
	}
	else
	{
		if(this._pgnfile.getMoves().length == 0 || this._pgnfile.getMoves()[0].getNotation() != "...")
		{
			var move2 = new chssMove(NaN, NaN, NaN, NaN);
			move2.setNotation("...");
			this._pgnfile.getMoves().unshift(move2);
			this._currentmove++;
		}
	}
}

chssGame.prototype.getCurrentVariationListMove = function()
{
	var currentVariationList = this.getVariationList(NaN);
	if(currentVariationList != null)
	{
		var move = currentVariationList.getHalfmove() + this._variationmove;
		return move;
	}
	else
		return NaN;
}

chssGame.prototype.getVariationList = function(variationId) //default: NaN
{
	if(isNaN(variationId))
		variationId = this._variationid;
	for(var i=0;i<this._pgnfile.getVariations().length;i++)
	{
		if(this._pgnfile.getVariations()[i].getVariationId() == variationId)
			return this._pgnfile.getVariations()[i];
	}
	return null;
}

chssGame.prototype.getCurrentMove = function(skipVariation) //default = false
{
	if(typeof skipVariation === 'undefined' || skipVariation == null)
		skipVariation = false;
	
	var currentmove = this._currentmove;
	if(!isNaN(this._variationid) && this._variationid != 0 && !skipVariation)
		currentmove = this.getCurrentVariationListMove();
	return currentmove;
}

chssGame.prototype.getNextVariationMoves = function()
{
	var moves = new Array();
	var varId = this._variationid;
	if(isNaN(varId))
		varId = 0;
	for(var i = 0; i<this._pgnfile.getVariations().length; i++)
	{
		var variationList = this._pgnfile.getVariations()[i];
		if(variationList.getParentVariationId() == varId && variationList.getHalfmove() == this.getCurrentMove())
		{
			moves.push(variationList);
		}
	}
	return moves;
}

chssGame.prototype.selectPiece = function(piece)
{
	var color = null;
	var regExprWhite = /[PKQBNR]/;
	var regExprBlack = /[pkqbnr]/;
	if(piece.search(regExprWhite) == 0) color = "W";
	else if(piece.search(regExprBlack) == 0) color = "B";
	if(color == null) throw new Error("Incorrect piece string.");
	
	return chssPiece.Factory(color, piece);
}

chssGame.prototype.rebuildBoard = function()
{
	var currentMove = this.getCurrentMove()-1;
	if(this._pgnfile.getMoves().length > 0 && currentMove >= this.getFirstMove())
	{
		var move = this.getMove(currentMove);
		this._board = this.copyBoard(move.getBoard());
		this._enpassent = move.getEnpassent();
		this._castle = [move.getCastle()[0], move.getCastle()[1], move.getCastle()[2], move.getCastle()[3]];
		this._promotion = move.getPromotion();
		
		for(var i=0; i<move.getBoardChanges().length; i++)
		{
			switch(move.getBoardChanges()[i].action)
			{
				case BoardChange.ADD_PIECE:  this._board[move.getBoardChanges()[i].getY()][move.getBoardChanges()[i].getX()] = move.getBoardChanges()[i].getPiece();
					break;
				case BoardChange.REMOVE_PIECE: 	this._board[move.getBoardChanges()[i].getY()][move.getBoardChanges()[i].getX()] = null;
					break;	
			}
		}
	}
	else
		this.initBoard();
} 

chssGame.prototype.copyBoard = function(board) //default = null
{
	if(board==null)
		board = this._board;
	var newBoard = new Array();
	for(var i=0;i<8;i++)
	{
		for(var j=0;j<8;j++)
		{
			if(j==0)
				newBoard[i] = new Array();
			newBoard[i][j] = this.copyPiece(board[i][j]);
		}
	}
	return newBoard;
}

chssGame.prototype.copyPiece = function(piece)
{
	if(piece != null)
	{	
		var newPiece;
		switch(piece.getPiececode())
		{
			case "_": newPiece = new chssPawn(piece.getColor()); break;
			case "K": newPiece = new chssKing(piece.getColor()); break;
			case "Q": newPiece = new chssQueen(piece.getColor()); break;
			case "B": newPiece = new chssBishop(piece.getColor()); break;
			case "N": newPiece = new chssKnight(piece.getColor()); break;
			case "R": newPiece = new chssRook(piece.getColor()); break;
		}
		
		var availableMoves = piece.getAvailableMoves();
		for(var i=0; i<availableMoves.length; i++)
		{
			var availableMove = availableMoves[i];
			newPiece.addAvailableMove(availableMove[0], availableMove[1]);
		}
		return newPiece;
	}
	return null;
}

chssGame.prototype.getMove = function(movenumber, variationid)
{
	if(typeof movenumber === 'undefined')
		movenumber = this.getCurrentMove() - 1;

	if(typeof variationid === 'undefined')
		variationid = this._variationid;
	
	try
	{
		var movesList = this.getMovesList(variationid);
		return movesList[movenumber];
	}
	catch(e)
	{
		return null;
	}
	return null;
}

chssGame.prototype.getMovesList = function(variationid)
{
	if(typeof variationid === 'undefined')
		variationid = this._variationid;
	
	var variationList = null;
	var index = this._pgnfile.getMoves().length;
	var movesList = new Array();
	var variationMoves = new Array();
	if(!isNaN(variationid) && variationid != 0)
	{
		variationList = this.getVariationList(variationid);
		var parentVariationId = variationList.getParentVariationId();
		var halfmove = variationList.getHalfmove();
		variationMoves = this.sliceVariationMovesArray(variationList.getMoves(), -1);
		while(!isNaN(parentVariationId) && parentVariationId != 0)
		{
			variationList = this.getVariationList(parentVariationId);
			parentVariationId = variationList.getParentVariationId();
			variationMoves = this.sliceVariationMovesArray(variationList.getMoves(), halfmove - variationList.getHalfmove()).concat(variationMoves);
			halfmove = variationList.getHalfmove();
		}
		index = variationList.getHalfmove();
	}
	if(index != -1)
		movesList = this._pgnfile.getMoves().slice(0,index);
	if(this._variationmove != 0)
		movesList = movesList.concat(variationMoves);
	return movesList;
}

chssGame.prototype.getMovesLength = function(variation, checkBreak) //default = variation = false, checkBreak = false
{
	if(checkBreak && this._pgnfile.getLastMove() != null && this._pgnfile.getLastMove().isBreak())
	{
		if(this._pgnfile.getMoves().length > 0 && this._pgnfile.getMoves()[0].getNotation() == "...")
			return 1;
		else
			return 0;
	}

	if(variation)
	{
		if(isNaN(this._variationid) || this._variationid == 0)
			return this.getMovesLength(false, checkBreak);
		else
		{
			var move = null,
				movesLength = this.getVariationList(NaN).getMoves().length;
			for(var i=0;i<movesLength;i++)
			{
				move = this.getVariationList(NaN).getMoves()[i];
				if(!move.getValid() || (checkBreak && move.isBreak()))
					break;
			}
			if(move != null)
			{
				var length = i;
				if(move.getValid() && i!=movesLength)
					length += 1;
			}
			else
			{
				var length = 0;
			}
			var notation = length>0?this.getVariationList(NaN).getMoves()[0].getNotation()=="..."?1:0:0
			return this.getVariationList(NaN).getHalfmove() + length - notation;
		}
	}
	else
	{
		var move = null,
			movesLength = this._pgnfile.getMoves().length;
		for(var i=0;i<movesLength;i++)
		{
			move = this._pgnfile.getMoves()[i];
			if(!move.getValid() || (checkBreak && move.isBreak()))
				break;
		}
		if(move != null)
		{
			var length = i;
			if(move.getValid() && i!=movesLength)
				length += 1;
			return length;
		}
		else
		{
			return 0;
		}
	}
}

chssGame.prototype.removeMoves = function()
{
	if(isNaN(this._variationid) || this._variationid == 0)
	{
		this._pgnfile.setMoves(this._pgnfile.getMoves().slice(0, this._currentmove));
		this.removeVariationLists(0, this._currentmove);
	}
	else
	{
		var currentVariationList = this.getVariationList(NaN);
		var odd = (currentVariationList.getMoves().length!=0 && currentVariationList.getMoves()[0].getNotation()=="...")?1:0;
		//var varlen = this.sliceVariationMovesArray(currentVariationList.getMoves()).length;
		currentVariationList.setMoves(currentVariationList.getMoves().slice(0, this._variationmove + odd));
		this.removeVariationLists(currentVariationList.getVariationId(), currentVariationList.getHalfmove()+this._variationmove);
	}
}

chssGame.prototype.removeVariationLists = function(parentId, halfmove)
{
	for(var i=0; i<this._pgnfile.getVariations().length;i++)
	{
		if(this._pgnfile.getVariations()[i].getParentVariationId() == parentId && this._pgnfile.getVariations()[i].getHalfmove() >= halfmove)
		{
			this.removeVariationLists(this._pgnfile.getVariations()[i].getVariationId(), this._pgnfile.getVariations()[i].getHalfmove());
			this._pgnfile.setVariations(this._pgnfile.getVariations().slice(0,i-1).concat(this._pgnfile.getVariations().slice(i-1)))
		}
	}
}

chssGame.prototype.sliceVariationMovesArray = function(moves, end) //default: end = -1)
{
	if(moves.length > 0 && moves[0].getNotation() == "...")
		return end==-1?moves.slice(1):moves.slice(1, end+1);
	else
		return end==-1?moves:moves.slice(0, end);
}

chssGame.prototype.getFirstMove = function()
{
	var firstMove = 0;
	if(this._pgnfile.getMoves().length != 0 && this._pgnfile.getMoves()[0].getNotation() == "...")
		firstMove = 1;
	return firstMove;
}

chssGame.prototype.addPromotionPiece = function(color, piececode)
{
	var piece = chssPiece.Factory(color, piececode);
	var currentmove = this.getCurrentMove() - 1;
	if(this._promotion)
	{
		var lastMove = this.getMove(currentmove);
		lastMove.setPromotionPiece(piece);
		lastMove.setNotation(lastMove.getNotation() + "=" + chssLanguage.convertPiece(piece.getPiececode()));
		this._board[lastMove.getY2()][lastMove.getX2()] = piece;
		lastMove.setBoard(this.copyBoard(null));
		lastMove.setPromotion(false);
		this._promotion = false;
		if(this.checkMate())
			lastMove.setNotation(lastMove.getNotation() + "#");
		else if(this.checkCheck(false))
			lastMove.setNotation(lastMove.getNotation() + "+");
		return true;	
	}
	else
	{
		return false;
	}
}

chssGame.prototype.changeBoard = function(action, checkBreak)//default checkBreak = false;
{
	var currentVariationList = this.getVariationList(NaN);
	if(action == "Start")
	{
		this._currentmove = this.getFirstMove();
		this._variationmove = 0;
		this._variationid = NaN;
	}
	else if(action == "End")
	{
		this._currentmove = this.getMovesLength(false, checkBreak);
		this._variationmove = 0;
		this._variationid = NaN;
	}
	else if(!isNaN(parseInt(action)))
	{
		if(action.charAt(0) == "+" || action.charAt(0) == "-")
		{
			if(currentVariationList != null)
				this._variationmove += parseInt(action);
			else
				this._currentmove += parseInt(action);
		} 
		else
		{
			if(currentVariationList != null)
				this._variationmove	 = parseInt(action) - currentVariationList.getHalfmove();
			else
			 	this._currentmove = parseInt(action);
		}
		if(currentVariationList != null && this._variationmove >= this.getMovesLength(true, checkBreak) - currentVariationList.getHalfmove())
		{
			this._variationmove = this.getMovesLength(true, checkBreak) - currentVariationList.getHalfmove();
			if(this._variationmove <= 0)
			{
				this._variationid = currentVariationList.getParentVariationId();
				this._variationmove = 0;
				if(currentVariationList.getParentVariationId() == 0)
				{
					this._variationid = NaN;
					this._variationmove = 0;
					this._currentmove = currentVariationList.getHalfmove();
				}
				else
				{
					this._variationid = currentVariationList.getParentVariationId();
					this._variationmove = currentVariationList.getHalfmove() - this.getVariationList().getHalfmove();
				}
			}
		}
		else if(currentVariationList != null && this._variationmove <= 0)
		{
			var parentVariationList = null;
			do
			{
				parentVariationList = this.getVariationList(currentVariationList.getParentVariationId())
				if(isNaN(currentVariationList.getParentVariationId()) || currentVariationList.getParentVariationId() == 0)
				{
					this._currentmove = currentVariationList.getHalfmove() + this._variationmove;
					this._variationid = NaN;
					this._variationmove = 0;
				}
				else if(this.sliceVariationMovesArray(parentVariationList.getMoves(), -1).length > Math.abs(this._variationmove))
				{
					this._variationmove = currentVariationList.getHalfmove() - parentVariationList.getHalfmove() + this._variationmove;
					this._variationid = parentVariationList.getVariationId();
				}
				else
				{
					this._variationmove += this.sliceVariationMovesArray(parentVariationList.getMoves(), -1).length + 1;
				}
				currentVariationList = parentVariationList;
			}
			while(this._variationmove <= 0 && currentVariationList != null)
		}
		var length = this.getMovesLength(false, checkBreak);
		if(this._currentmove > length) this._currentmove = length;
		else if(this._currentmove <= 0) this._currentmove = this.getFirstMove();
	}
	this.rebuildBoard();
}

chssGame.prototype.changeBreak = function(add, question) //default: question=""
{
	var current = this.getCurrentMove();
	if((isNaN(this._variationid) || this._variationid == 0) && current != this._pgnfile.getMoves().length)
	{
		var move = null;
		if(current!=0 && !(current==1 && this._pgnfile.getMoves().length > 0 && this._pgnfile.getMoves()[0].getNotation() == "..."))
		{
			 move = this.getMove(current-1);
		}
		else
		{
			if(this._pgnfile.getLastMove() == null)
				this._pgnfile.setLastMove(new chssMove(NaN, NaN, NaN, NaN));
			move = this._pgnfile.getLastMove();
		}
		if(add)
		{
			move.setIsBreak(true);
			move.setBreakQuestion(question);
			move.setBreakType(chssMove.constant.PUZZLE_BREAK);
		}
		else
		{
			move.setIsBreak(false);
			move.setBreakQuestion("");
			move.setBreakType(chssMove.constant.NO_BREAK);
		}
	}
}

chssGame.prototype.changeNAG = function(add, code) 
{
	if(typeof code === 'undefined')
		code = "";
	
	var move = this.getMove(this.getCurrentMove()-1);
	if(add)
	{
		move.setAnnotation(code);
	}
	else
	{
		move.setAnnotation("");
	}
}

//default: loadmoves = true, returnResult = false, checkfirst = true, revert = false
chssGame.prototype.newFenFromCurrentBoard = function(loadMoves, returnResult, checkFirst, revert)
{
	var fen = "";
	for(var i = 0; i<8; i++)
	{
		var skip = 0;
		for(var j = 0; j<8; j++)
		{
			if(this._board[i][j] == null)
				skip++
			else
			{
				if(skip!=0)
					fen += skip;
				//var piece:String = Language.getInstance().convertPieceToEnglish(this._board[i][j].piececode.charAt(1));
				var piece = this._board[i][j].getPiececode();
				if(piece == "_")
					piece = "P";
				if(this._board[i][j].getColor() == "B")
					piece = piece.toLowerCase();
				fen += piece;
				skip = 0;
			}
		}
		if(skip!=0)
			fen += skip;
		fen += "/";
	}
	fen = fen.substr(0,fen.length-1);
	var movesLength = this._pgnfile.getMoves().length;
	var variableCheck = movesLength % 2 == 1;
	if(revert)
		variableCheck = (movesLength - 1) % 2 == 1;
	//loadmove:false, returnResult:true, checkfirst:true, revert:true
	if(
		(
			(
				!returnResult || (returnResult && checkFirst && !revert)
			) 
			&& 
			(
				movesLength == 0 || this._pgnfile.getMoves()[0].getNotation() != "..."
			)
		) 
		||
		(
			returnResult && checkFirst && revert && !movesLength == 0 && this._pgnfile.getMoves()[0].getNotation() == "..."
		) 
		||
		(
			returnResult && !checkFirst && variableCheck
		)
	  )
		fen += " w";
	else
		fen += " b";
		
	var castleString = "";
	if(this._castle[0])
		castleString += "K";
	if(this._castle[1])
		castleString += "Q";
	if(this._castle[2])
		castleString += "k";
	if(this._castle[3])
		castleString += "q";
	if(castleString!="")
		fen += " " + castleString;
	else
		fen += " -";
		
	fen += " " + this._enpassent + " 0 1";	
	if(returnResult)
		return fen;
	else		
	{
		this._pgnfile.setFen(fen);
		if(loadMoves)
			this.loadMoves();
		return null;
	}
}

chssGame.prototype.getNextMove = function(variation) //default: false
{
	var length = this.getMovesLength(variation, false);
	var currentmove = this.getCurrentMove()+1;
	if(!isNaN(length) && length >= currentmove)
		return this.getMovesList()[this.getCurrentMove()];
	else
		return null;
}

chssGame.prototype.getBoard = function()
{
	return this._board;
}

chssGame.prototype.getCastle = function()
{
	return this._castle;
}

chssGame.prototype.getEnpassent = function()
{
	return this._enpassent;
}

chssGame.prototype.setEdit = function(bool)
{
	this._edit = bool;
}

chssGame.prototype.getEdit = function()
{
	return this._edit;
}

chssGame.prototype.getPGNFile = function()
{
	return this._pgnfile;
}

chssGame.prototype.setPGNFile = function(pgnfile)
{
	this._pgnfile = pgnfile;
}

chssGame.prototype.getPromotion = function()
{
	return this._promotion;
}

chssGame.prototype.setVariationId = function(id)
{
	this._variationid = id;
}

chssGame.prototype.getVariationId = function()
{
	return (isNaN(this._variationid)?0:this._variationid);
}

chssGame.prototype.setVariationMove = function(move)
{
	this._variationmove = move;
}

chssGame.prototype.getVariationMove = function()
{
	return this._variationmove;
}

chssGame.prototype.getSelectedIndex = function()
{
	return (isNaN(this._variationid)||this._variationid==0?this._currentmove-1:this._variationmove-1);	
}

chssGame.prototype.getResult = function()
{
	var move = this.getMove(this.getCurrentMove()-1);
	return move!=null?move.getResult():chssGame.results.NONE;
}