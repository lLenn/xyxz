function uploadElement()
{
	this._uploadInput = document.createElement("input");
	this._uploadInput.type = "file";
	this._uploadInput.id = "uploadPGNFile";
	
	this._PGNFiles = new Array();
	
	this._uploadInput.onclick = function(){ this.value = null };
	this.readFile(this, this.loadGame);
}

uploadElement.prototype.readFile = function(parent, callback)
{
	parent.getUploadElement().onchange = function()
	{
		var file = parent.getUploadElement().files[0]; 

	    if (!file) 
	    {
	        alert("Failed to load file");
	    } 
	    else if (!file.name.match('\.pgn'))
	    {
	    	alert(file.name + " is not a valid pgn file.");
	    } 
	    else 
	    {
	    	var reader = new FileReader();
	    	reader.onload = function(event)
	    		{
	    			var fileArr = event.target.result.split(/[\r\n]+/g);
	    			/*
	    			for(var i=0;i<fileArr.length;i++)
	    				{
	    					console.log("consoleLog: " + fileArr[i]);
	    				}
	    			*/
	    			fileArr.push("[");
	    			parent.convertToPGNObjects(fileArr, parent, callback);
	    		}
	    	reader.readAsText(file);
	    }
	}
}

uploadElement.prototype.convertToPGNObjects = function(list, parent, callback)
{
	var files = new Array();
	var tag = true;
	var moves = false;
	var space = 0;
	var pgnfile = new chssPGNFile();
	var movesString = "";
	var firstline = false;
	var lineNumber = 0;
	for(var i=0; i<list.length; i++)
	{
		var line = list[i];
		//Een PGN file kan 0 of meerdere spellen bevatten
		//Een spel bevat 2 secties: de tag sectie(optioneel) en de moves sectie
		//Een spel begint met 0 of meerdere tags
		
		/*Er moet worden gekeken of de tag sectie en de moves sectie goed
		van elkaar gescheiden zijn.*/
		if(line.charAt(0)=="[" && tag == false)
		{
			if(firstline == false && moves == true)
			{
				/* voeg newline characters toe na elke set geneste haakjes om regexp te vergemakkelijken */
				var add_newline = new Array();
				var inComment = false;
				var openParenthesis = false
				var parenthesis = -1;
				for(var k=0;k<movesString.length;k++)
				{
					var char = movesString.charAt(k);
					if(char == "{")
						inComment = true;
					else if(char == "}")
						inComment = false;
					else if(char == "(" && !inComment)
					{
						openParenthesis = true;
						parenthesis++;
						add_newline.push([k, "(" + parenthesis + ";"]);
					}
					else if(char == ")" && !inComment && openParenthesis)
					{
						if(parenthesis == 0)
							openParenthesis = false;
						add_newline.push([k, ";)" + parenthesis]);
						parenthesis--;
					}
				}
				
				var before = "";
				var after = "";
				var indexCorrection = 0;
				for(var k=0; k<add_newline.length; k++)
				{
					var variation = add_newline[k];
					before = movesString.substring(0, variation[0] + indexCorrection);
					after = movesString.substring(variation[0] + indexCorrection + 1, movesString.length + indexCorrection - 1)
					indexCorrection += String(variation[1]).length - 1;
					movesString = before + variation[1] + after;
				}
				
				
				/*De moves sectie kan commentaar en alternatieve bewegingen bevatten.
				Commentaar wordt tussen { en } geschreven 
				of door ; die dan doorgaat tot het einde van de lijn.
				Een variant wordt tussen ( en ) toegevoegd.*/
				//Alert.show(movesString);
				pgnfile = parent.addMovesAndComments(movesString + " ", pgnfile, parent);
				files.push(pgnfile);
				pgnfile = new chssPGNFile();
				movesString = "";
				firstline = true;
				moves = false;
				tag = true;
				lineNumber = 0;
			}
		}
		/*Er wordt eerst gekeken of er een tag sectie in het spel aanwezig is en
		die wordt doorlopen tot er geen tags meer zijn.
		In de tag sectie kunnen er meerdere tags op 1 lijn voorkomen
		Er wordt gecontroleerd of deze aan de juiste format voldoet.
		Er moeten 4 tokens aanwezig zijn. Deze mogen worden gescheiden door 0 of meerdere spaties.
		De tokens zijn [, naam tag, waarde tag met " ", ]*/
		if(line.charAt(0) == "[" && tag == true && line.length > 1)
		{  
			var tagPattern = /( *\[ *[a-zA-Z0-9_]+ *\"[^\"]*\" *\] *)+/;  
			var matches = line.match(tagPattern);
			if(matches != null && matches[0] == line)
			{
				//Eén of meerdere tags werden gevonden.
				//De tags worden van elkaar gescheiden.
				tagPattern = new RegExp(" *\[ *[a-zA-Z0-9_]+ *\"[^\"]+\" *\] *", "g");
				matches = line.match(tagPattern);
				//Elke tag wordt toegevoegd
				for(var j=0;j<matches.length;j++)
				{
					var match = matches[j];
					if(match!="")
					{
						//De tagnaam en waarde worden uit de tag gehaald.
						var indexFirstBracket = match.indexOf("\[");
						var indexFirstQuotation = match.indexOf("\"");
						var indexLastQuotation = match.indexOf("\"",indexFirstQuotation+1);
						var tagName = match.substring(indexFirstBracket+1, indexFirstQuotation).match(/[a-zA-Z0-9_]+/)[0];
						var tagWaarde = match.substring(indexFirstQuotation+1,indexLastQuotation);
						/*Er wordt gecheck of er een dubbel is zoniet wordt de waarde toegevoegd.
						Anders is de tag sectie incorrect en wordt er een error gesmeten.
						Als de tag geen standaard is word ze bij de extratags array toegevoegd.*/
						var dubble = false; 
						switch(tagName)
						{
							case "Event": if(pgnfile.getEvent()==null) pgnfile.setEvent(tagWaarde); else dubble = true; break;
							case "Site": if(pgnfile.getSite()==null) pgnfile.setSite(tagWaarde); else dubble = true; break;
							case "Date": if(pgnfile.getDate()==null) pgnfile.setDate(new Date(tagWaarde)); else dubble = true; break;
							case "White": if(pgnfile.getWhite()==null) pgnfile.setWhite(tagWaarde); else dubble = true; break;
							case "Black": if(pgnfile.getBlack()==null) pgnfile.setBlack(tagWaarde); else dubble = true; break;
							case "Result": if(pgnfile.getResult()==null) pgnfile.setResult(tagWaarde); else dubble = true; break;
							case "ECO": if(pgnfile.getEco()==null) pgnfile.setEco(tagWaarde); else dubble = true; break;
							case "Opening": if(pgnfile.getOpening()==null) pgnfile.setOpening(tagWaarde); else dubble = true; break;
							case "WhiteElo": if(pgnfile.getWhiteElo()==null) pgnfile.setWhiteElo(tagWaarde); else dubble = true; break;
							case "BlackElo": if(pgnfile.getBlackElo()==null) pgnfile.setBlackElo(tagWaarde); else dubble = true; break;
							case "WhiteTitle": if(pgnfile.getWhiteTitle()==null) pgnfile.setBhiteTitle(tagWaarde); else dubble = true; break;
							case "BlackTitle": if(pgnfile.getBlackTitle()==null) pgnfile.setBlackTitle(tagWaarde); else dubble = true; break;
							case "WhiteCountry": if(pgnfile.getWhiteCountry()==null) pgnfile.setWhiteCountry(tagWaarde); else dubble = true; break;
							case "BlackCountry": if(pgnfile.getBlackCountry()==null) pgnfile.setBlackCountry(tagWaarde); else dubble = true; break;
							case "FEN": if(pgnfile.getFen()==null) pgnfile.setFen(tagWaarde); else dubble = true; break;
							case "Annotator": if(pgnfile.getAnnotator()==null) pgnfile.setAnnotator(tagWaarde); else dubble = true; break;
							//default: if(pgnfile.getExtraTags[tagName]==null) pgnfile.extraTags[tagName] = tagWaarde; else dubble = true; break;
						}
						if(dubble)
						{
							//pgnfile.valid = false;
							pgnfile.getErrors().push("Double tag detected at line " + lineNumber);
						}
						
					}		
				}
				firstline = false;
			}
			else
			{
				pgnfile.setValid(false);
				pgnfile.getErrors().push("Tag syntax error at line " + lineNumber);
			}
		}
			/*Nadat de tag sectie werd bekeken wordt de moves sectie bekeken.
			Alle lijnen van de moves sectie worden in 1 string gezet 
			om daarna te worden gecontroleerd op syntax.*/
		else if(firstline != "[")
		{
			var semiColonCheck = line.replace(/(\{[^\}]+\}|\{[^\}{]+)/g,"");
			var indexSemiColon = semiColonCheck.indexOf(";");
			if(indexSemiColon==-1) movesString += line + " ";
			else movesString += line.replace(/\;/,"{")+"} ";
			moves = true;
			tag = false;
			firstline = false;		
		}
		lineNumber++;
	}
	parent.setPGNFiles(files);
	callback(parent);
}

uploadElement.prototype.addMovesAndComments = function(moves, pgnfile, parent)
{
	moves = moves.replace(/\[[^\]]*\]/g,"");
	//Alert.show(moves);
	var movePattern = new RegExp(" *[0-9]+.? *(?:(?:[KNBRQ]?[a-h]?[1-8]?x?[a-h][1-8][=]?[NBRQ]?[!?+#]*|\\$[0-9]+|O-O-O|O-O|[.]{3}|\\{[^\\}]*\\}|\\(0;.*?;\\)0) +)+","g");
	var moveArray = moves.match(movePattern);
	var moveNumber = 1;
	var moveCorrection = 0;
	
	var first_b_comment = moves.indexOf("}");
	var first_comment = moves.indexOf("{");
	var first_move = moves.search(/[0-9]+./g);
	if(first_comment<first_b_comment && first_comment<first_move)
	{
		var comment = moves.substring(first_comment+1, first_b_comment);
		if(comment.substring(comment.length-2, comment.length) == "RP")
		{
			pgnfile.setLastMove(new chssMove(NaN, NaN, NaN, NaN));
			pgnfile.getLastMove().setIsBreak(true);
			pgnfile.getLastMove().setBreakType(1);
			pgnfile.getLastMove().setBreakQuestion(comment.substring(beginIndex,comment.length-3).trim());
		}
		else
		{
			pgnfile.setLastMove(new chssMove(NaN, NaN, NaN, NaN));
			pgnfile.getLastMove().setComment(0, comment.substring(beginIndex,comment.length-1).trim());
		}
		
		moves = moves.substring(first_b_comment+1);
	}
	else if(first_comment>first_b_comment)
		moves = moves.substring(first_b_comment+1);
	
	for(var i=0; i<moveArray.length; i++)
	{
		var move = moveArray[i]; 
		//Alert.show(move);
		var number = parseInt(move.match(/[0-9]+/)[0]);
		var halfMovePattern = new RegExp("(?:[KNBRQ]?[a-h]?[1-8]?x?[a-h][1-8][=]?[NBRQ]?[!?+#]*|\\$[0-9]+|O-O-O|O-O|[.]{3}|\\{[^\\}]*\\}|\\(0;.*?;\\)0)","g");
		var halfMoveArray = move.match(halfMovePattern);
		var halfMoveNumber = 1;
		for(var j=0; j<halfMoveArray.length; j++)
		{
			var halfMove = halfMoveArray[j];
			//Alert.show(halfMove);
			var moveEntry = new chssMove(NaN, NaN, NaN, NaN);
			var firstChar = halfMove.charAt(0);
			if(number == moveNumber || number == moveNumber - 1)
			{
				if(firstChar!="(" && firstChar!="{" && firstChar!="." && firstChar != "$" && halfMoveNumber < 3)
				{
					moveEntry.setNotation(halfMove);
					pgnfile.getMoves().push(moveEntry);
					halfMoveNumber++;
				}
				else if(firstChar=="." && halfMoveNumber <= 2)
				{
					halfMoveNumber++;
				}
				else if(firstChar=="{")
				{
					var beginIndex = 1;
					var endIndex = pgnfile.getMoves().length-1;
					if(halfMove.substring(1,4) == "BRK")
					{
						var prevLang = language_statics.language;
						language_statics.language = "English";
						
						var game = new chssGame(null);
						game.setPGNFile(pgnfile);
						game.initBoard();
						game.loadMoves();
						
						game.changeBoard(String(pgnfile.getMoves().length-1));
						console.log(pgnfile.getMoves().length);
						pgnfile.setFen(game.newFenFromCurrentBoard(false, true, false, false));
						console.log(pgnfile.getFen());
						pgnfile.setStart(number);
						moveCorrection = ((number-1)*2);
						console.log(pgnfile.getMoves().length);
						console.info(pgnfile.getMoves());
						pgnfile.setMoves(pgnfile.getMoves().slice(pgnfile.getMoves().length-1));
						language_statics.language = prevLang;
						beginIndex = 4;
					}
					
					if(halfMove.substring(halfMove.length-3, halfMove.length-1) == "RP")
					{
						moveEntry = pgnfile.getMoves()[pgnfile.getMoves().length-1];
						moveEntry.setIsBreak(true);
						moveEntry.setBreakType(1);
						moveEntry.setBreakQuestion(halfMove.substring(beginIndex,halfMove.length-3).trim());
						pgnfile.getMoves()[pgnfile.getMoves().length-1] == moveEntry;
					}
					else
					{
						moveEntry = pgnfile.getMoves()[pgnfile.getMoves().length-1];
						moveEntry.setComment(0, halfMove.substring(beginIndex,halfMove.length-1).trim());
						pgnfile.getMoves()[pgnfile.getMoves().length-1] = moveEntry;
					}
				}
				else if(firstChar=="(")
				{
					pgnfile = parent.addVariationMovesAndComments(halfMove, pgnfile, ((number-1)*2)+halfMoveNumber-1, moveCorrection, 0, 0, parent);
				}
				else if(firstChar=="$")
				{
					moveEntry = pgnfile.getMoves()[pgnfile.getMoves().length-1];
					moveEntry.setAnnotation(halfMove);
					pgnfile.getMoves()[pgnfile.getMoves().length-1] = moveEntry;
				}
				else
				{
					pgnfile.valid = false;
					pgnfile.getErrors.push("Move syntax error at move " + moveNumber);
				}
			}
		}
		if(number == moveNumber)
		{
			moveNumber++;
		}
	}
	if(!pgnfile.getValid())
	{
		var output = "";
		for(var i=0; i<pgnfile.getErrors(); i++)
		{
			output += pgnfile.getErrors()[i] + "\n";
		}
		console.log(output);
	}
	return pgnfile;
}

uploadElement.prototype.addVariationMovesAndComments = function(moves, pgnfile, moveNumber, moveCorrection, depth, parentVariationId, parent) //default depth=0; parentVariationId = 0
{
	moves = moves.replace(/\[[^\]]*\]/g,"").substring(moves.indexOf(";")+1,moves.lastIndexOf(";")) + " ";
	//Alert.show(moves);
	var movePattern = new RegExp(" *[0-9]+.? *(?:(?:[KNBRQ]?[a-h]?[1-8]?x?[a-h][1-8][=]?[NBRQ]?[!?+#]*|\\$[0-9]+|O-O-O|O-O|[.]{3}|\\{[^\\}]*\\}|\\(" + (depth+1) + ";.*?;\\)" + (depth+1) + ") +)+","g");
	var moveArray = moves.match(movePattern);
	var varList = new chssVariationList();
	varList.setHalfmove(moveNumber-1-moveCorrection);
	moveNumber = Math.ceil(moveNumber/2);
	var id = 1;
	if(parentVariationId != 0)
		id = parentVariationId + 1;
	for(var i=0; i<pgnfile.getVariations().length; i++)
	{
		var variationList = pgnfile.getVariations()[i];
		if(id<=variationList.getVariationId())
			id = variationList.getVariationId()+1;
	}
	varList.setVariationId(id);
	varList.setParentVariationId(parentVariationId);
	
	var rest = varList.getHalfmove()%2;
	if(rest==1)
	{					
		var moveEntryOdd = new chssMove(NaN, NaN, NaN, NaN);
		moveEntryOdd.setNotation("...");
		varList.getMoves().push(moveEntryOdd);
	}
	for(var i=0; i<moveArray.length; i++)
	{
		var move = moveArray[i];
		var number = parseInt(move.match(/[0-9]+/)[0]);
		var halfMovePattern = new RegExp("(?:[KNBRQ]?[a-h]?[1-8]?x?[a-h][1-8][=]?[NBRQ]?[!?+#]*|\\$[0-9]+|O-O-O|O-O|[.]{3}|\\{[^\\}]*\\}|\\(" + (depth+1) + ";.*?;\\)" + (depth+1) + ")","g");
		var halfMoveArray = move.match(halfMovePattern);
		var halfMoveNumber = 1;
		for(var j=0; j<halfMoveArray.length; j++)
		{
			var halfMove = halfMoveArray[j];
			//Alert.show(halfMove);
			var moveEntry = new chssMove(NaN, NaN, NaN, NaN);
			var firstChar = halfMove.charAt(0);
			if(number == moveNumber || number == moveNumber - 1)
			{
				if(firstChar!="(" && firstChar!="{" && firstChar!="." && firstChar != "$" && halfMoveNumber < 3)
				{
					moveEntry.setNotation(halfMove);
					varList.getMoves().push(moveEntry);
					halfMoveNumber++;
				}
				else if(firstChar=="." && halfMoveNumber <= 2)
				{
					halfMoveNumber++;
				}
				else if(firstChar=="{")
				{
					moveEntry = varList.getMoves()[varList.getMoves().length-1];
					moveEntry.setComment(0, halfMove.substring(1,halfMove.length-1));
					//moveEntry.comment = halfMove.substring(1,halfMove.length-1);
					varList.getMoves()[varList.getMoves().length-1] = moveEntry;
				}
				else if(firstChar=="(")
				{
					pgnfile = parent.addVariationMovesAndComments(halfMove, pgnfile, ((number-1)*2)+halfMoveNumber-1, moveCorrection, depth+1, varList.getVariationId(), parent);
				}
				else if(firstChar=="$")
				{
					moveEntry = varList.getMoves()[varList.getMoves().length-1];
					moveEntry.setAnnotation(halfMove);
					varList.getMoves()[varList.getMoves().length-1] = moveEntry;
				}
				else
				{
					pgnfile.setValid(false);
					pgnfile.getErrors().push("Move syntax error in variation move " + moveNumber);
				}
			}
		}
		if(number == moveNumber)
		{
			moveNumber++;
		}
	}
	pgnfile.getVariations().push(varList);
	return pgnfile;
}

uploadElement.prototype.loadGame = function(parent)
{
	//console.trace();
	//console.info(parent.getPGNFiles());
	chssBoard.moduleManager.loadNewGame(parent.getPGNFiles()[0]);
}

uploadElement.prototype.getUploadElement = function()
{
	return this._uploadInput;
}

uploadElement.prototype.setPGNFiles = function(files)
{
	this._PGNFiles = files;
}

uploadElement.prototype.getPGNFiles = function()
{
	return this._PGNFiles;
}