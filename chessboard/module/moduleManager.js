chssModuleManager.modes = {VIEW_MODE: "view_mode",
						   PLAY_PUZZLE_MODE: "play_puzzle_mode",
						   GAME_PUZZLE_MODE: "game_puzzle_mode",
						   ADD_MOVES_MODE: "add_moves_mode"}

chssModuleManager.subModes = {NOT_ACTIVE: "not_active",
							  ADD_MARKING: "add_marking",
							  DISABLE_CHANGE: "disable_change",
							  CORRECTION_DISABLED: "correction_disabled",
							  MINUS_ONE_CORRECTION: "minus_one_correction",}

function chssModuleManager(args, board)
{	
	this._mode = chssModuleManager.modes.ADD_MOVES_MODE;
	this._subMode = chssModuleManager.subModes.NOT_ACTIVE;
	this._module = undefined;
	this._moduleDrawn = false;
	this._play_game_timer = null;
	this._timer_paused = false;
	this._prev_mode = null;
	this.initModule(args, board);
	
	this._puzzleNextMove = undefined;
	this._validation = undefined;
	this._alternative = undefined;
	this._variation_promotion = undefined;
	this._alternative_var_id = undefined;
	
	this._changeAttempts = false;
	this._changeHalfmove = false;
	this._changeBack = false;
	
	this._initiated = false;
	this._imagesLoaded = false;
}

chssModuleManager.prototype.initModule = function(args, board)
{
	switch(args.appName)
	{
		case "PuzzleViewer": this._module = new chssPuzzleModule(args, board, this); break;
		case "GameViewer": this._module = new chssGameModule(args, board, this); break;
		case "SelectionViewer": this._module = new chssSelectionModule(args, board, this); break;
		case "ExcerciseViewer": this._module = new chssExcerciseModule(args, board, this); break;
		case "MultipleAnswersViewer": this._module = new chssMultipleAnswersModule(args, board, this); break;
	}
	this._module.initData(this.initiated, this);
}

chssModuleManager.prototype.initiated = function()
{
	var initialMode = this._module.getInitialMode();
	this._mode = initialMode[0];
	this._subMode = initialMode[1];
	chssBoard.engine.setOnline(initialMode[2]);
	if(typeof this._module !== 'undefined' && typeof this._module.getChangeAttempts === 'function')
		this._changeAttempts = this._module.getChangeAttempts();
	chssBoard.moduleManager.loadNewGame(this._module.getPGNFile());
	this.draw();
	this._moduleDrawn = true;
	chssBoard.board.loadComplete();
}

chssModuleManager.prototype.draw = function()
{
	this._module.draw();
}

chssModuleManager.prototype.setMode = function(mode)
{
	this._mode = mode;
}

chssModuleManager.prototype.getMode = function()
{
	return this._mode;
}

chssModuleManager.prototype.setSubMode = function(subMode)
{
	this._subMode = subMode;
}

chssModuleManager.prototype.getSubMode = function()
{
	return this._subMode;
}

chssModuleManager.prototype.getModule = function()
{
	return this._module;
}

chssModuleManager.prototype.loadNewGame = function(pgnfile)
{
	chssBoard.chssGame.setPGNFile(pgnfile);
	chssBoard.chssGame.initBoard();
	chssBoard.chssGame.loadMoves();
	this.redraw(true);
}

chssModuleManager.prototype.redraw = function(forceRedraw)
{
	chss_global_vars.prevCursor = undefined;
	chssBoard.board.removeAvailableMoves();
	chssBoard.board.clearDrag();
	chssBoard.board.redraw();
	chssBoard.board.getMovesList().changeMovesText(forceRedraw);
	chssBoard.board.getMovesList().changeSelectedIndex();
	chssBoard.board.getCommentArea().changeLayout();
	
	if(chssBoard.board.getCommentArea().isVisible() && this._moduleDrawn)
		this._module.hide();
	else if(this._moduleDrawn)
		this._module.show();
	
	//console.log(chssBoard.chssGame.newFenFromCurrentBoard(false, true, false, false));
	//chssBoard.chssGame.getPGNFile().getMovesToString();
	//chssBoard.chssGame.getPGNFile().getVariationsToString();
}

chssModuleManager.prototype.resize = function(diffCoeff)
{
	if(typeof this._module !== "undefined" && this._module != null)
		this._module.resize(diffCoeff);
}

chssModuleManager.prototype.addMove = function(prevSelectedX, prevSelectedY, selectedX, selectedY)
{
	if(selectedX >= 0 && selectedY >= 0 && selectedX <= 7 && selectedY <= 7 && chssBoard.chssGame.checkMove(prevSelectedX, prevSelectedY, selectedX, selectedY))
	{
		var args = [prevSelectedX, prevSelectedY, selectedX, selectedY];
		if(this._subMode != chssModuleManager.subModes.CORRECTION_DISABLED && this._subMode != chssModuleManager.subModes.MINUS_ONE_CORRECTION && chssBoard.chssGame.getCurrentMove() != chssBoard.chssGame.getMovesLength(true, false))
		{
			chssBoard.board.getVariationPopUp().draw(args, this.addVariation, this);
		}
		else
		{
			args.push(false);
			this.addVariation.apply(this, args);
		}
	}
	this.redraw(true);
}
	
chssModuleManager.prototype.addVariation = function(prevSelectedX, prevSelectedY, selectedX, selectedY, variation)
{
	var legit = chssBoard.chssGame.addMove(prevSelectedX, prevSelectedY, selectedX, selectedY, variation)
	if(chssBoard.chssGame.getPromotion())
	{
		chssBoard.board.getPromotionPopUp().draw(chssBoard.chssGame.active(true), chssModuleManager.prototype.addVariationPiece);
	}
	else if(legit)
	{
		chssBoard.engine.think();
	}
	this.resetChange();
	this.redraw(true);
}

chssModuleManager.prototype.resetChange = function()
{
	if(this._changeBack)
		this._changeAttempts--;
	else if(chssBoard.chssGame.getCurrentMove() != this._changeHalfmove)
	{
		if(typeof this._module !== 'undefined' && typeof this._module.getChangeAttempts === 'function')
			this._changeAttempts = this._module.getChangeAttempts();
		this._changeHalfmove = chssBoard.chssGame.getCurrentMove();
	}
	
	this._changeBack = false;
}

chssModuleManager.prototype.addVariationPiece = function(color, piececode)
{
	 chssBoard.chssGame.addPromotionPiece(color, piececode);
}

chssModuleManager.prototype.checkMove = function(x1, y1, x2, y2)
{
	if(chssBoard.chssGame.checkMove(x1, y1, x2, y2))
	{
		if(!chssBoard.engine.getOnline())
		{
			this._puzzleNextMove = chssBoard.chssGame.getNextMove(true);
			this._validation = false;
			this._alternative = false;
			if( this._puzzleNextMove != null &&
				this._puzzleNextMove.getX1() == x1 && this._puzzleNextMove.getY1() == y1 &&
				this._puzzleNextMove.getX2() == x2 && this._puzzleNextMove.getY2() == y2)
			{
				var action_str = "";
				if(this._puzzleNextMove.getPromotionPiece() != null)
				{
					chssBoard.board.getPromotionPopUp().draw(chssBoard.chssGame.active(false), chssBoard.moduleManager.passAlongPromotion);
				}
				else if(chssBoard.moduleManager.getMode() != chssModuleManager.modes.GAME_PUZZLE_MODE)
				{
					this.changeBoard("+1", false);
					chssBoard.board.playNextMove(false, true);
				}
				this._validation = true;
			}
			else
			{
				var varNextMoves = chssBoard.chssGame.getNextVariationMoves();
				var variation = false;
				for(var i=0; i<varNextMoves.length; i++)
				{
					var varList = varNextMoves[i],
						halfmove = varList.getHalfmove(),
						varMove = varList.getMoves()[halfmove%2];

					if( varMove != null &&
						varMove.getX1() == x1 && varMove.getY1() == y1 &&
						varMove.getX2() == x2 && varMove.getY2() == y2 )
					{
						variation = true;
						this._validation = true;
						this._alternative = !varList.getSolution() || this._mode == chssModuleManager.modes.GAME_PUZZLE_MODE;
						if(varList.getSolution())
						{
							chssBoard.chssGame.setVariationId(varList.getVariationId());
							break;
						}
						else
							this._alternative_var_id = varList.getVariationId();
						this._puzzleNextMove = varMove;
					}
				}
				
				if(varMove != null && varMove.getPromotionPiece() != null)
				{
					this._variation_promotion = true;
					chssBoard.board.getPromotionPopUp().draw(chssBoard.chssGame.active(false), chssBoard.moduleManager.passAlongPromotion);
				}
				else
				{
					if(!variation)
					{
						this._validation = false;
					}
					
					if(this._validation && !this._alternative)
					{
						this.changeBoard("+1", false);
						chssBoard.board.playNextMove(false, true);
					}
					else if(this._validation && this._mode != chssModuleManager.modes.GAME_PUZZLE_MODE)
					{
						this.changeBoard("+1", this._alternative_var_id);
					}
					else
					{
						this.drawTempMove();
					}
				}
			}

			if(this._puzzleNextMove.getPromotionPiece() == null || this._validation == false)
			{
				this._module.processValidation();
			}
			
		}
		else
		{
			if(chssBoard.chssGame.addMove(x1, y1, x2, y2, false))
			{
				chssBoard.moduleManager.setMode(chssModuleManager.modes.VIEW_MODE);
				if(!chssBoard.chssGame.getPromotion())
				{
					if(chssBoard.chssGame.getResult() == chssGame.results.NONE)
					{
						chssBoard.engine.think();
					}
					else
					{
						if(typeof this._module !== 'undefined' && typeof this._module.processResult === 'function')
							this._module.processResult();
					}
					chssBoard.moduleManager.redraw(true);
				}
			}
		}
	}
	else
	{
		this.redraw(false);
	}
}

chssModuleManager.prototype.passAlongPromotion = function(color, piececode)
{
	chssBoard.moduleManager.checkPromotion(color, piececode);
}

chssModuleManager.prototype.checkPromotion = function(color, piececode)
{
	var variation = false;
	if(this._puzzleNextMove.getPromotionPiece().getColor() == color && this._puzzleNextMove.getPromotionPiece().getPiececode() == piececode && !this._variation_promotion)
	{
		this._validation = true;
	}
	else
	{
		var varNextMoves = chssBoard.chssGame.getNextVariationMoves();
		this._validation = false;
		variation = true;
		this._variation_promotion = false;
	
		for(var i=0; i<varNextMoves.length; i++)
		{
			var varList = varNextMoves[i]
			var varMove = varList.getMoves()[varList.getHalfmove()%2];
			if( varMove != null && varMove.getPromotionPiece() != null &&
				varMove.getX1() == this._puzzleNextMove.getX1() && varMove.getY1() == this._puzzleNextMove.getY1() &&
				varMove.getX2() == this._puzzleNextMove.getX2() && varMove.getY2() == this._puzzleNextMove.getY2() && 
				varMove.getPromotionPiece().getPiececode() == piececode && varMove.getPromotionPiece().getColor() == color)
			{
				this._validation = true;
				this._alternative = !varList.getSolution();
				if(varList.getSolution())
				{
					chssBoard.chssGame.setVariationid(varList.getVariationId());
					break;
				}
				else
					this._alternative_var_id = varList.getVariationId();
			}
		}
	}
	
	if(this._validation && !this._alternative)
	{
		this.changeBoard("+1", false);
		if(chssBoard.chssGame.getCurrentMove() != chssBoard.chssGame.getMovesLength(true, false))
		{
			chssBoard.board.playNextMove(false, true);
		}
	}
	else if(this._validation)
	{
		this.changeBoard("+1", this._alternative_var_id);
	}
	else
	{
		this.drawTempMove();
	}
	
	this._module.processValidation();
}

chssModuleManager.prototype.drawTempMove = function()
{
	chssBoard.board.setPiece(chss_global_vars.prevDragChssPiece, chssBoard.board.getFlip()?7-chss_global_vars.selectedX:chss_global_vars.selectedX, chssBoard.board.getFlip()?7-chss_global_vars.selectedY:chss_global_vars.selectedY);
	chssBoard.board.setPiece(null, chssBoard.board.getFlip()?7-chss_global_vars.prevSelectedX:chss_global_vars.prevSelectedX, chssBoard.board.getFlip()?7-chss_global_vars.prevSelectedY:chss_global_vars.prevSelectedY);
}

chssModuleManager.prototype.actionChangeBoard = function(action, variationId)//default variationId false
{
	var proceed = true;
	if(this._subMode == chssModuleManager.subModes.DISABLE_CHANGE)
		proceed = false;
	else if(chssBoard.engine.isThinking() && this._mode == chssModuleManager.modes.VIEW_MODE)
		proceed = false;
	
	if(proceed)
		this.changeBoard(action, variationId);
}

chssModuleManager.prototype.changeBoard = function(action, variationId)//default variationId false
{
	if(typeof variationId != 'undefined' && variationId!==false)
	{
		if(variationId!=0)
		{
			chssBoard.chssGame.setVariationId(variationId);
		}
		else
		{
			chssBoard.chssGame.setVariationId(0);
			chssBoard.chssGame.setVariationMove(0);
		}
	}
	if(typeof action != 'undefined')
	{
		chssBoard.chssGame.changeBoard(action, this._mode == chssModuleManager.modes.VIEW_MODE);
		this.redraw(false);
	}
	
	if(this.checkGamePuzzle())
	{
		this._mode = chssModuleManager.modes.GAME_PUZZLE_MODE;
	}
	else if(this._mode == chssModuleManager.modes.GAME_PUZZLE_MODE)
	{
		this._mode = chssModuleManager.modes.VIEW_MODE;	
	}
}
	
chssModuleManager.prototype.checkGamePuzzle = function()
{
	return (((chssBoard.chssGame.getCurrentMove()==0 && chssBoard.chssGame.getMove(0) != null && chssBoard.chssGame.getMove(0).getNotation() != "...") ||
			(chssBoard.chssGame.getCurrentMove()==1 && chssBoard.chssGame.getMove(0) != null && chssBoard.chssGame.getMove(0).getNotation() == "...")) && chssBoard.chssGame.getPGNFile().getLastMove() != null && chssBoard.chssGame.getPGNFile().getLastMove().isBreak()) ||
			(chssBoard.chssGame.getCurrentMove()>0 && chssBoard.chssGame.getMove(chssBoard.chssGame.getCurrentMove()-1).isBreak() && this._mode == chssModuleManager.modes.VIEW_MODE);
}

chssModuleManager.prototype.playGame = function(fromStart, variation) //default: fromStart = false, variation = false
{
	if(chssBoard.moduleManager.getMode() != chssModuleManager.modes.PLAY_PUZZLE_MODE)
	{
	    chssBoard.board.redraw();
		if(fromStart)
		{
			chssBoard.board.playFirstMove();
		}
		this._prev_mode = this._mode;
		this._mode = chssModuleManager.modes.VIEW_MODE;
	    this._paused = false;
	    this.playNextMove(variation);
	}
}

chssModuleManager.prototype.playNextMove = function(variation)
{
	var mode = this._mode;
	this._play_game_timer = setInterval(function()
		{
			if(chssBoard.chssGame.getCurrentMove() != chssBoard.chssGame.getMovesLength(true, mode == chssModuleManager.modes.VIEW_MODE))
		    {
				chssBoard.board.playNextMove(false, variation);
		    }
		    else
		    {
		    	chssBoard.moduleManager.stopPlaying();
		    	chssBoard.board.getChange().playingStopped();
		    	chssBoard.moduleManager.changeBoard();
		    }
		}, 1100);
}

chssModuleManager.prototype.pausePlaying = function()
{
	clearInterval(this._play_game_timer);
	this._mode = this._prev_mode;
	this._paused = true;
}

chssModuleManager.prototype.stopPlaying = function()
{
	clearInterval(this._play_game_timer);
	this._mode = this._prev_mode;
	this._paused = false;
	this._play_game_timer = null;
}

chssModuleManager.prototype.checkEndGame = function()
{
	return chssBoard.chssGame.getCurrentMove() == chssBoard.chssGame.getMovesLength(true, this._mode == chssModuleManager.modes.VIEW_MODE);
}

chssModuleManager.prototype.showEngineModule = function(boolean)
{
	if(typeof this._module !== 'undefined' && this._module != null && typeof this._module.showEngineModule === 'function')
		this._module.showEngineModule(boolean)
}

chssModuleManager.prototype.addedMarking = function(x, y)
{
	if(typeof this._module !== 'undefined' && this._module != null && typeof this._module.addMarking === 'function')
		this._module.addMarking(x, y)
}

chssModuleManager.prototype.removedMarking = function(x, y)
{
	if(typeof this._module !== 'undefined' && this._module != null && typeof this._module.removeMarking === 'function')
		this._module.removeMarking(x, y)
}

chssModuleManager.prototype.getVariableForCommentArea = function(type)
{
	if((chssBoard.chssGame.getCurrentMove(true) == 0 || (chssBoard.chssGame.getCurrentMove(true) == 1 && chssBoard.chssGame.getPGNFile().getMoves().length > 0 && chssBoard.chssGame.getPGNFile().getMoves()[0].getNotation() == "...")) && chssBoard.chssGame.getVariationMove() == 0)
	{
		if(chssBoard.chssGame.getPGNFile().getLastMove() != null && chssBoard.chssGame.getPGNFile().getLastMove().isBreak())
		{
			switch(type)
			{
				case chssCommentArea.IS_BREAK: return true; break;
				case chssCommentArea.GET_COMMENT: return chssBoard.chssGame.getPGNFile().getLastMove().getBreakQuestion(); break;
				case chssCommentArea.GET_COMMENTS: return new Array(); break;
			}
		}
		else
		{
			switch(type)
			{
				case chssCommentArea.IS_BREAK: return false; break;
				case chssCommentArea.GET_COMMENT: return ""; break;
				case chssCommentArea.GET_COMMENTS: return chssBoard.chssGame.getPGNFile().getLastMove().getComments(); break;
			}
		}
	}
	else
	{
		var move = chssBoard.chssGame.getMove(chssBoard.chssGame.getCurrentMove() - 1);
		switch(type)
		{
			case chssCommentArea.IS_BREAK: return move.isBreak(); break;
			case chssCommentArea.GET_COMMENT: return move.isBreak()?move.getBreakQuestion():""; break;
			case chssCommentArea.GET_COMMENTS: return move.isBreak()?new Array():move.getComments(); break;
		}
	}
}

chssModuleManager.prototype.correctionAllowed = function()
{
	if(this.checkEndGame())
		return true;
	else if(this._subMode == chssModuleManager.subModes.MINUS_ONE_CORRECTION && this._changeAttempts != 0 && chssBoard.chssGame.getCurrentMove()+2 == this._changeHalfmove)
	{
		this._changeBack = true;
		return true;
	}
	else if(this._subMode == chssModuleManager.subModes.MINUS_ONE_CORRECTION)
	{
		this._changeBack = false;
		return false;
	}

	return true;
}