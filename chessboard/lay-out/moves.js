function movesElement()
{
	this._movesDataprovider = new Array();
	this._movesElement = document.createElement("div");
	this._movesElement.style.overflowY = "auto";
	this._movesElement.id = "movesList";
	this._movesElement.style.backgroundColor = chssOptions.background_color;
}

movesElement.prototype.changeSelectedIndex = function()
{
	//var update_right:Boolean = GameService.getInstance().get_game_data(GameData.GET_MODULE_VARIABLE, ["update_right"]);
	var user_id = 0; //update_right?0:GameService.getInstance().get_game_data(GameData.GET_USER_ID);
	var selected = false;
	
	for(var i=0;i<this._movesDataprovider.length;i++)
	{
		var child = this._movesDataprovider[i];
		if(child.getIndex() == chssBoard.chssGame.getSelectedIndex() && child.getVariationId() == chssBoard.chssGame.getVariationId())
		{
			child.selected(true);
			selected = true;
			this.scrollTo(parseInt(child.getMoveElement().style.top))
			/*
			this.tools.visible = this.edit?(child.moveIR.variationId != 0 && (child.moveIR.variationUserId == user_id || (child.moveIR.variationUserId == -1 && user_id == 0))) || this.add_break:false;
			this.var_tools.visible = this.tools.visible?child.moveIR.variationId != 0 && (child.moveIR.variationUserId == user_id || (child.moveIR.variationUserId == -1 && user_id == 0)):false;
			this.change_break.visible = update_right?(child.moveIR.variationId == 0 || isNaN(child.moveIR.variationId)) && !child.moveIR.lastMove:false;
			this.sol_check.selected = child.moveIR.solution;
			this.change_annotation.visible = true;
			
			if(child.moveIR.notation != "...")
			{
				if(child.moveIR.annotation == "")
					change_annotation.label = Language.getInstance().translate(1296);
				else
					change_annotation.label = Language.getInstance().translate(1298);
					
				
				if(child.moveIR.isBreak)
					change_break.label = Language.getInstance().translate(1294);
				else
					change_break.label = Language.getInstance().translate(1293);
			}
			*/
		}
		else
			child.selected(false);
	}
	if((chssBoard.chssGame.getSelectedIndex() == -1 && this._movesDataprovider.length > 0 && this._movesDataprovider[0].getNotation() != "...") || 
	   (chssBoard.chssGame.getSelectedIndex() == 0 && this._movesDataprovider.length > 1 && this._movesDataprovider[0].getNotation() == "..."))
	{
		/*
		this.tools.visible = this.edit?this.add_break:false;
		this.var_tools.visible = false;
		this.change_break.visible = update_right;
		this.change_annotation.visible = false;
		this.sol_check.selected = false;

		if(this.pgnfile.lastMove != null && this.pgnfile.lastMove.isBreak)
			change_break.label = Language.getInstance().translate(1294);
		else
			change_break.label = Language.getInstance().translate(1293);
		*/
	}
	else if(chssBoard.chssGame.getSelectedIndex() == -1 || !selected)
	{
		/*
		this.tools.visible = false;
		this.var_tools.visible = false;
		this.change_break.visible = false;
		this.sol_check.selected = false;
		this.change_annotation.visible = false;
		*/

		if(!selected && chssBoard.chssGame.getSelectedIndex() != -1)
		{
			chssBoard.moduleManager.changeBoard("Start", false);
		}
	}
}

movesElement.prototype.scrollTo = function(top)
{
	var	topGoal = top - parseInt(this._movesElement.style.height) + 21 * (chssOptions.board_size/360),
		bottomGoal = top;
	if(this._movesElement.scrollTop < topGoal)
		this._movesElement.scrollTop = topGoal;
	else if(this._movesElement.scrollTop > bottomGoal)
		this._movesElement.scrollTop = bottomGoal;

}

movesElement.prototype.changeMovesText  = function(force_redraw)//default = false
{
	var halfmove = 0;
	var chkIndex = {number: 0};
	var renderList = {boolean: false};
	var dp = new Array();
	var afterVariation = false;
	var moves = chssBoard.chssGame.getPGNFile().getMoves();
	var moveEl = null;
	for(var i=0;i<moves.length;i++)
	{
		var move = moves[i];
		if(this._movesDataprovider ==  null || 
			this._movesDataprovider.length <= chkIndex.number || 
			this._movesDataprovider[chkIndex.number] == null || 
			this._movesDataprovider[chkIndex.number].getNotation() != move.getNotation() ||
			this._movesDataprovider[chkIndex.number].isBreak() != move.isBreak() || 
			this._movesDataprovider[chkIndex.number].getAnnotation() != move.getAnnotation())
		{
			renderList.boolean = true;
		}
		
		moveEl = new moveElement();
		moveEl.setIndex(halfmove);
		moveEl.setNotation(move.getNotation());
		var annotation = Nag.getNagByCode(move.getAnnotation());
		if(annotation != null)
		{
			moveEl.setAnnotation(annotation[1]);
			moveEl.setAnnotationTooltip(annotation[2]);
		}
		moveEl.setValid(move.getValid());
		moveEl.setBreak(move.isBreak());
		moveEl.setFirstAfterVariation(afterVariation);
		moveEl.setResult(move.getResult());
		afterVariation = false;
		
		dp.push(moveEl);
		var tempArray = this.getVariationsFromHalfmove(0, halfmove, 1, chkIndex, renderList);
		if(tempArray.length > 0)
		{
			dp = dp.concat(tempArray);
			afterVariation = true;
		}
		halfmove++;
		chkIndex.number = chkIndex.number+1;
	}
	if(moveEl != null)
	{
		moveEl.lastMove = true;
		moveEl.draw();
	}
	//console.info(dp);
	if(force_redraw || renderList.boolean || dp.length != this._movesDataprovider.length)
	{
		this._movesDataprovider = dp;
		this.renderMovesList();
	}
}

movesElement.prototype.getVariationsFromHalfmove = function(parentVariationId, halfmove, depth, chkIndex, renderList)
{
	var dp = new Array();
	var countChildren = 0;
	var currentChild = 0;
	var variations = chssBoard.chssGame.getPGNFile().getVariations();

	for(var i=0;i<variations.length;i++)
	{
		var varList = variations[i];
		if(varList.getHalfmove() == halfmove && varList.getParentVariationId() == parentVariationId)
		{
			countChildren++
		}
	}
	for(var i=0;i<variations.length;i++)
	{
		var varList = variations[i];
		if(varList.getHalfmove() == halfmove && varList.getParentVariationId() == parentVariationId)
		{
			var index = 0;
			var varHalfMove = halfmove;
			currentChild++;
			var afterVariation = false;
			var moves = varList.getMoves();
			var moveEl = null;
			
			for(var j=0; j<moves.length; j++)
			{
				var move = moves[j];
				if(move.getNotation() != "...")
				{
					chkIndex.number = chkIndex.number+1;
					
					if(this._movesDataprovider ==  null || 
						this._movesDataprovider.length <= chkIndex.number || 
						this._movesDataprovider[chkIndex.number] == null || 
						this._movesDataprovider[chkIndex.number].getNotation() != move.getNotation() || 
						this._movesDataprovider[chkIndex.number].getAnnotation() != move.getAnnotation())
					{
						renderList.boolean = true;
					}
					
					moveEl = new moveElement();
					moveEl.setIndex(index);
					moveEl.setNotation(move.getNotation());
					var annotation = Nag.getNagByCode(move.getAnnotation());
					if(annotation != null)
					{
						moveEl.setAnnotation(annotation[1]);
						moveEl.setAnnotationTooltip(annotation[2]);
					}
					moveEl.setValid(move.getValid());
					//moveEl.setResult(move.getResult());
					moveEl.setVariationId(varList.getVariationId());
					moveEl.setVariationUserId(varList.getUserId());
					moveEl.setVariationUsername(varList.getUsername());
					moveEl.setVariationHalfmove(varList.getHalfmove());
					moveEl.setSolution(varList.getSolution());
					moveEl.setFirstAfterVariation(afterVariation);
					afterVariation = false;
					
					if(j == varList.getMoves().length-1)
					{
						moveEl.setLastVariationMove(depth);
						var dp_prev = this.getVariationsFromHalfmove(varList.getVariationId(), varHalfMove, depth+1, chkIndex, renderList);
						if(dp_prev.length > 0)
							moveEl.setLastVariationMove(0);
						else if(currentChild != countChildren)
							moveEl.setLastVariationMove(1);
						dp.push(moveEl);
						dp = dp.concat(dp_prev);
					}
					else
					{
						dp.push(moveEl);
						var tempArray = this.getVariationsFromHalfmove(varList.getVariationId(), varHalfMove, depth, chkIndex, renderList);
						if(tempArray.length > 0)
						{
							dp = dp.concat(tempArray);
							afterVariation = true;
						}
					}
					varHalfMove++;
					index++;
					
					moveEl.draw();
				}
			}
		}
	}

	return dp;
}

movesElement.prototype.renderMovesList = function()
{
	//var update_right:Boolean = GameService.getInstance().get_game_data(GameData.GET_MODULE_VARIABLE, ["update_right"]);
	var user_id = 0 //:Number = update_right?0:GameService.getInstance().get_game_data(GameData.GET_USER_ID);
	var pgnfile = chssBoard.chssGame.getPGNFile();
	
	while (this._movesElement.firstChild)
		this._movesElement.removeChild(this._movesElement.firstChild);
	
	if(pgnfile.getLastMove() == null || !pgnfile.getLastMove().isBreak() || chssBoard.chssGame.getEdit())
	{
		var width = 0,
			height = 0,
			index = 0,
			broke = false,
			padding = 1 * (chssOptions.board_size/360),
			added_height_style = 21 * (chssOptions.board_size/360),
			added_width_style = 7 * (chssOptions.board_size/360),
			negative_width = 2 * (chssOptions.board_size/360),
			scroll_diff = 25 *(chssOptions.board_size/360);
		for(var i=0; i<this._movesDataprovider.length; i++)
		{
			var rd = this._movesDataprovider[i];
			if(rd.getIndex() == 0 && rd.getVariationId() != 0)
			{
				var spanElement = document.createElement("span");
				spanElement.style.position = "absolute";

				spanElement.style.padding = "0px " + padding + "px 0px " + padding + "px";
				spanElement.style.fontSize =  16 * (chssOptions.board_size/360) + "px";
				//spanElement.style.fontWeight = "bold";
				spanElement.style.color = "#000000";
				spanElement.innerHTML = "(";
				
				this._movesElement.appendChild(spanElement);
				
				width -= negative_width;
				
				if(added_width_style + width > this._movesElement.offsetWidth - scroll_diff)
				{
					width = 0;
					height += added_height_style;
				}
				
				spanElement.style.left = width + "px";
				spanElement.style.top = height + "px";
				
				width += added_width_style;
				
				if(rd.getVariationUserId() != 0)
				{
					/*
					var name = rd.getVariationUserName();
					if(rd.variationUserId == -1)
						name = chssLanguage.translate(1335);
					else if(rd.variationUserId == user_id)
						name = chssLanguage.translate(1360);
	
					var spanElement = document.createElement("span");
					spanElement.style.position = "absolute";
					spanElement.style.paddingTop = "1px";
					spanElement.style.paddingBottom = "1px";
					spanElement.style.paddingLeft = "1px";
					spanElement.style.paddingRight = "1px";
					//spanElement.style.fontWeight = "bold";
					spanElement.style.color = "#000000";
					spanElementtoolTip = Language.getInstance().translate(1331).replace("%s", name);
					spanElement.text = "*";
					
					this.moves.addChild(lbl);
					
					var added_width:Number = 10;
					width -= 2;
					
					if(added_width + width > this.moves.width - 35)
					{
						width = 0;
						height += 24;
					}
					
					spanElementx = width;
					spanElementy = height;
					
					width += added_width;
					*/
				}
			}
			
			//ADD NOTATION LABEL
			rd.draw()
			this._movesElement.appendChild(rd.getMoveElement());
			
			if(rd.getMoveElement().offsetWidth + width > this._movesElement.offsetWidth - scroll_diff)
			{
				width = 0;
				height += added_height_style;
			}

			rd.getMoveElement().style.left = width + "px";
			rd.getMoveElement().style.top = height + "px";
			
			width += rd.getMoveElement().offsetWidth;
			
			if(rd.getResult() != chssGame.results.NONE)
			{
				var spanElement = document.createElement("span");
				spanElement.style.position = "absolute";

				spanElement.style.padding = "0px " + padding + "px 0px " + padding*5 + "px";
				spanElement.style.fontSize =  16 * (chssOptions.board_size/360) + "px";
				//spanElement.style.fontWeight = "bold";
				spanElement.style.color = "#000000";
				
				switch(rd.getResult())
				{
					case chssGame.results.WHITE: spanElement.innerHTML = "1-0"; break;
					case chssGame.results.BLACK: spanElement.innerHTML = "0-1"; break;
					case chssGame.results.DRAW: spanElement.innerHTML = "1/2-1/2"; break;
				}
				
				this._movesElement.appendChild(spanElement);
				width -= negative_width;
				
				if(spanElement.offsetWidth + width > this._movesElement.offsetWidth - scroll_diff)
				{
					width = 0;
					height += added_height_style;
				}
				
				spanElement.style.top = height + "px";
				spanElement.style.left = width + "px";
				
				width += spanElement.offsetWidth;
			}
			
			
			if(rd.getVariationId() != 0 && rd.getLastVariationMove() != 0)
			{
				for(var j=0; j<rd.getLastVariationMove(); j++)
				{
					var spanElement = document.createElement("span");
					spanElement.style.position = "absolute";

					spanElement.style.padding = "0px " + padding + "px 0px 0px";
					spanElement.style.fontSize =  16 * (chssOptions.board_size/360) + "px";
					//spanElement.style.fontWeight = "bold";
					spanElement.style.color = "#000000";
					spanElement.innerHTML = ")";
					
					this._movesElement.appendChild(spanElement);
					width -= negative_width;
					
					if(added_width_style + width > this._movesElement.offsetWidth - scroll_diff)
					{
						width = 0;
						height += added_height_style;
					}
					
					spanElement.style.top = height + "px";
					spanElement.style.left = width + "px";
					
					width += added_width_style;
				}
			}
			
			if(rd.isBreak() && !chssBoard.chssGame.getEdit())
			{
				broke = true;
				break;
			}
		}
	}
	else if(this._movesDataprovider.length > 0 && this._movesDataprovider[0].getNotation() == "...")
	{
		this._movesDataprovider[0].draw();
		this._movesElement.appendChild(this._movesDataprovider[0].getMoveElement());
	}
}

movesElement.prototype.getMoves = function()
{
	return this._movesElement;
}