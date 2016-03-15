engine.levels = [[1, "N/A"],
                 [2, "N/A"],
                 [3, "N/A"],
                 [4, "N/A"],
                 [5, "N/A"],
                 [6, "N/A"],
                 [7, "N/A"],
                 [8, "N/A"],
                 [9, "N/A"],
                 [10, "N/A"]];

engine.depths = [1, 2, 3, 4, 6, 8, 10, 13, 16, 20];
engine.skills = [1, 2, 3, 4, 6, 8, 10, 13, 16, 20];

engine.errProb = [87.95, 92.4, 96.85, 101.3, 105.75, 110.2, 114.65, 119.1, 123.55, 128];
engine.maxErr = [4.05, 3.60, 3.15, 2.7, 2.25, 1.8, 1.35, 0.9, 0.45, 0];

/*
engine.errProb = [13,7, 26,4, 39,1, 51,8, 64,5, 77,2, 89,9, 102,6, 115,3, 128];
engine.maxErr = [9, 8, 7, 6, 5, 4, 3, 2, 1, 0];
*/

function engine()
{
	this._online = false;
	this._thinking = false;
	this._maxMovetime = 1000 * engine.levels.length;
	this._maxSkill = 20;
	this._level = engine.levels.length;
	this._skill = 20;
	this._depth = 20;
	this._movetime = 8000;
	this._errProb = undefined;
	this._maxErr = undefined;
}

engine.prototype.initiate = function()
{
	this._engine = typeof STOCKFISH === "function" ? STOCKFISH() : new Worker('../Stockfish/stockfish.js');
   	
	this.onMessage(this, this._engine);
	
	this._engine.postMessage("ucinewgame");
	this._engine.postMessage("isready");
}

engine.prototype.onMessage = function(parent, engine)
{
	engine.onmessage = function(event)
	{
		var line;
		if (event && typeof event === "object")
			line = event.data;
		else
			line = event;
		
		//console.log(line);
		if(line == "readyok")
		{
			engine.postMessage('setoption name Skill Level value ' + parent.getSkill());
			chssBoard.moduleManager.showEngineModule(true);
		}
		
		var match = line.match(/^bestmove ([a-h][1-8][a-h][1-8][qrbn]?|\(none\))/);
        if(match)
        {
				//console.log(match[1]);
			parent.bestMove(match[1]);
		    parent.setThinking(false);
        }
	}
}

engine.prototype.think = function(move)
{
	if(this._online)
	{
		chssBoard.moduleManager.setMode(chssModuleManager.modes.VIEW_MODE);
		this._thinking = true;
		
		//console.log("c: position " + chssBoard.chssGame.newFenFromCurrentBoard(true, true, false, true));
		//this._engine.postMessage("position fen " + chssBoard.chssGame.newFenFromCurrentBoard(true, true, false, true));
		this.setPosition();
		//console.log("go movetime " + this._movetime + " depth " + this._depth);
		this._engine.postMessage("go movetime " + this._movetime + " depth " + this._depth);
		//this._engine.postMessage("go movetime " + this._movetime);
		//this._engine.postMessage("go infinite");
	}
}

engine.prototype.setPosition = function()
{
	var tempVarId = chssBoard.chssGame.getVariationId(),
		tempHalfMove = chssBoard.chssGame.getCurrentMove();
	
	chssBoard.chssGame.changeBoard("Start");
	var fen = chssBoard.chssGame.newFenFromCurrentBoard(true, true, true, false);
	chssBoard.chssGame.setVariationId(tempVarId);
	chssBoard.chssGame.changeBoard(tempHalfMove.toString());
	
	var moves = "",
		movesArr = chssBoard.chssGame.getMovesList();
	for(var i=0; i<movesArr.length; i++)
	{
		var move = movesArr[i];
		if(move.getNotation() != "...")
			moves += chssPiece.convertToLetter(move.getX1())+(8-move.getY1())+chssPiece.convertToLetter(move.getX2())+(8-move.getY2()) + (move.getPromotionPiece() != null?move.getPromotionPiece().getPiececode().toLowerCase():"") + " ";
	}
	
	//console.log("position fen " + fen + " moves " + moves);
	this._engine.postMessage("position fen " + fen + " moves " + moves);
}

engine.prototype.bestMove = function(value)
{
	if(value != "(none)")
	{
		var move = new chssMove(chssPiece.convertToNumber(value.charAt(0)),(8-parseInt(value.charAt(1))),chssPiece.convertToNumber(value.charAt(2)),(8-parseInt(value.charAt(3))));
		if(value.length == 5) 
		{
			var color;
			if(parseInt(value.charAt(1)) > 4) {
		    	color = "W";
		    } else {
		    	color = "B";
		    }
			move.setPromotionPiece(chssPiece.Factory(color, chssLanguage.convertPiece(value.charAt(4))));
		}

		chssBoard.chssGame.addMoveWithPromotion(move);
		chssBoard.moduleManager.resetChange();
		chssBoard.moduleManager.redraw();

		if(chssBoard.chssGame.getResult() == chssGame.results.NONE)
		{
			chssBoard.moduleManager.setMode(chssModuleManager.modes.ADD_MOVES_MODE);
		}
		else
		{
			if(typeof chssBoard.moduleManager.getModule() !== 'undefined' && typeof chssBoard.moduleManager.getModule().processResult === 'function')
				chssBoard.moduleManager.getModule().processResult();
		}
	}
	else
	{
		if(typeof chssBoard.moduleManager.getModule() !== 'undefined' && typeof chssBoard.moduleManager.getModule().processResult === 'function')
			chssBoard.moduleManager.getModule().processResult();
	}	
}

engine.prototype.getSkill = function()
{
	return this._skill;
}

engine.prototype.getLevel = function()
{
	return this._level;
}

engine.prototype.setOnline = function(online)
{
	this._online = online;
}

engine.prototype.isThinking = function()
{
	return this._thinking;
}

engine.prototype.setThinking = function(thinking)
{
	this._thinking = thinking;
}

engine.prototype.getOnline = function()
{
	return this._online;
}

engine.prototype.isOnline = function()
{
	return this._online;
}

engine.prototype.setLevel = function(level)
{
	this._level = level;
	this._movetime = this._level * (this._maxMovetime/engine.levels.length); 
	this._skill = engine.skills[this._level-1];
	this._depth = engine.depths[this._level-1];
	
	this._errProb = engine.errProb[this._level-1]; //Math.round(((this._level*2) * 6.35) + 1);
    this._maxErr = engine.maxErr[this._level-1];  //Math.round(((this._level*2) * -0.5) + 10);

	this._engine.postMessage('setoption name Skill Level value ' + this._skill);
    this._engine.postMessage('setoption name Skill Level Maximum Error value ' + this._maxErr);
    this._engine.postMessage('setoption name Skill Level Probability value ' + this._errProb);
	//console.info("movetime: " + this._movetime + " skill: " + this._skill + " depth: " + this._depth);
	
	chssBoard.board.getEngine()._time.value = this._movetime;
	chssBoard.board.getEngine()._skill.value = this._skill;
	chssBoard.board.getEngine()._depth.value = this._depth;
	chssBoard.board.getEngine()._errProb.value = this._errProb;
	chssBoard.board.getEngine()._maxErr.value = this._maxErr;
}

engine.prototype.setTime = function(time)
{
	this._movetime = time;
}

engine.prototype.setDepth = function(depth)
{
	this._depth = depth;
}

engine.prototype.setSkill = function(skill)
{
	this._skill = skill;
	this._engine.postMessage('setoption name Skill Level value ' + this._skill);
}

engine.prototype.setErrProb = function(errProb)
{
	this._errProb = errProb;
    this._engine.postMessage('setoption name Skill Level Probability value ' + this._errProb);
}

engine.prototype.setMaxErr = function(maxErr)
{
	this._maxErr = maxErr;
    this._engine.postMessage('setoption name Skill Level Maximum Error value ' + this._maxErr);
}

