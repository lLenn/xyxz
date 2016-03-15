function chssPGNFile()
{
	this._rating = null;
	this._comment = null;
	this._themes = new Array();
	
	this._event = null;
	this._site = null;
	this._date = null;
	this._white = null;
	this._black = null;
	this._result = null;
	this._eco = null;
	this._opening = null;
	this._whiteElo = null;
	this._blackElo = null;
	this._whiteTitle = null;
	this._blackTitle = null;
	this._whiteCountry = null;
	this._blackCountry = null;
	this._annotator = null;
	this._fen = null;
	this._start = 1;
	this._annotator = null;
	this._extraTags = new Array();
	this._moves = new Array();
	this._variations = new Array();
	this._errors = new Array();
	this._valid = true;
	this._lastMove = new chssMove(NaN, NaN, NaN, NaN);
	this._userId = 0;
}

chssPGNFile.prototype.getRating = function()
{
	return this._rating;
}

chssPGNFile.prototype.setRating = function(rating)
{
	this._rating = rating;
}

chssPGNFile.prototype.getComment = function()
{
	return this._comment;
}

chssPGNFile.prototype.setComment = function(comment)
{
	this._comment = comment;
}

chssPGNFile.prototype.getThemes = function()
{
	return this._themes;
}

chssPGNFile.prototype.setThemes = function(themes)
{
	this._themes = themes;
}

chssPGNFile.prototype.getEvent = function()
{
	return this._event;
}

chssPGNFile.prototype.setEvent = function(event)
{
	this._event = event;
}

chssPGNFile.prototype.getSite = function()
{
	return this._site;
}

chssPGNFile.prototype.setSite = function(site)
{
	this._site = site;
}

chssPGNFile.prototype.getDate = function()
{
	return this._date;
}

chssPGNFile.prototype.setDate = function(date)
{
	this._date = date;
}

chssPGNFile.prototype.getWhite = function()
{
	return this._white;
}

chssPGNFile.prototype.setWhite = function(white)
{
	this._white = white;
}

chssPGNFile.prototype.getBlack = function()
{
	return this._black;
}

chssPGNFile.prototype.setBlack = function(black)
{
	this._black = black;
}

chssPGNFile.prototype.getResult = function()
{
	return this._result;
}

chssPGNFile.prototype.setResult = function(result)
{
	this._result = result;
}

chssPGNFile.prototype.getEco = function()
{
	return this._eco;
}

chssPGNFile.prototype.setEco = function(eco)
{
	this._eco = eco;
}

chssPGNFile.prototype.getOpening = function()
{
	return this._opening;
}

chssPGNFile.prototype.setOpening = function(opening)
{
	this._opening = opening;
}

chssPGNFile.prototype.getWhiteElo = function()
{
	return this._whiteElo;
}

chssPGNFile.prototype.setWhiteElo = function(whiteElo)
{
	this._whiteElo = whiteElo;
}

chssPGNFile.prototype.getBlackElo = function()
{
	return this._blackElo;
}

chssPGNFile.prototype.setBlackElo = function(blackElo)
{
	this._blackElo = blackElo;
}

chssPGNFile.prototype.getWhiteTitle = function()
{
	return this._whiteTitle;
}

chssPGNFile.prototype.setWhiteTitle = function(whiteTitle)
{
	this._whiteTitle = whiteTitle;
}

chssPGNFile.prototype.getBlackTitle = function()
{
	return this._blackTitle;
}

chssPGNFile.prototype.setBlackTitle = function(blackTitle)
{
	this._blackTitle = blackTitle;
}

chssPGNFile.prototype.getWhiteCountry = function()
{
	return this._whiteCountry;
}

chssPGNFile.prototype.setWhiteCountry = function(whiteCountry)
{
	this._whiteCountry = whiteCountry;
}

chssPGNFile.prototype.getBlackCountry = function()
{
	return this._blackCountry;
}

chssPGNFile.prototype.setBlackCountry = function(blackCountry)
{
	this._blackCountry = blackCountry;
}

chssPGNFile.prototype.getFen = function()
{
	return this._fen;
}

chssPGNFile.prototype.setFen = function(fen)
{
	this._fen = fen;
}

chssPGNFile.prototype.getStart = function()
{
	return this._start;
}

chssPGNFile.prototype.setStart = function(start)
{
	this._start = start;
}

chssPGNFile.prototype.getAnnotator = function()
{
	return this._annotator;
}

chssPGNFile.prototype.setAnnotator = function(annotator)
{
	this._annotator = annotator;
}

chssPGNFile.prototype.getExtraTags = function()
{
	return this._extraTags;
}

chssPGNFile.prototype.setExtraTags = function(extraTags)
{
	this._extraTags = extraTags;
}

chssPGNFile.prototype.getMoves = function()
{
	return this._moves;
}

chssPGNFile.prototype.setMoves = function(moves)
{
	this._moves = moves;
}

chssPGNFile.prototype.getVariations = function()
{
	return this._variations;
}

chssPGNFile.prototype.setVariations = function(variations)
{
	this._variations = variations;
}

chssPGNFile.prototype.getErrors = function()
{
	return this._errors;
}

chssPGNFile.prototype.setErrors = function(errors)
{
	this._errors = errors;
}

chssPGNFile.prototype.getValid = function()
{
	return this._valid;
}

chssPGNFile.prototype.setValid = function(valid)
{
	this._valid = valid;
}

chssPGNFile.prototype.getLastMove = function()
{
	return this._lastMove;
}

chssPGNFile.prototype.setLastMove = function(lastMove)
{
	this._lastMove = lastMove;
}

chssPGNFile.prototype.getUserId = function()
{
	return this._userId;
}

chssPGNFile.prototype.setUserId = function(userId)
{
	this._userId = userId;
}

chssPGNFile.prototype.getMovesToString = function()
{
	console.log("Moves: ");
	var j=1;
	for(var i=0; i<this._moves.length; i++)
	{
		console.log((Math.floor(i/2) + 1) + ": " + this._moves[i].getNotation() + (this._moves[i+1] !== undefined?" " + this._moves[++i].getNotation():""));
	}
}

chssPGNFile.prototype.getVariationsToString = function()
{
	console.log("Variations: ");
	var j=1;
	for(var i=0; i<this._variations.length; i++)
	{
		console.info(this._variations[i].getHalfmove());
		this._variations[i].getMovesToString();
	}
}