var language_statics = {language: "English"}

function chssLanguage()
{

}

chssLanguage.convertPiece = function(piece)
{
	switch(language_statics.language)
	{
		case "Dutch": return chssLanguage.convertToDutch(piece);
		case "English": return piece;
		default: return chssLanguage.convertToLanguage(piece);
	}
}

chssLanguage.convertToDutch = function(piece)
{
	switch(piece)
	{
		case "Q": return "D"; break;
		case "B": return "L"; break;
		case "N": return "P"; break;
		case "R": return "T"; break;
		default: return piece;
	}
}

chssLanguage.convertToLanguage = function(piece)
{
	switch(piece)
	{
		case "Q": return chssLanguage.translate(652); break;
		case "B": return chssLanguage.translate(653); break;
		case "N": return chssLanguage.translate(654); break;
		case "R": return chssLanguage.translate(655); break;
		case "K": return chssLanguage.translate(656); break;
		default: return piece;
	}
}

chssLanguage.convertPieceToEnglish = function(piece)
{
	switch(language_statics.language)
	{
		case "Dutch": return chssLanguage.convertFromDutchToEnglish(piece);
		case "English": return piece;
		default: return chssLanguage.convertFromLanguageToEnglish(piece);
	}
}

chssLanguage.convertFromDutchToEnglish = function(piece)
{
	switch(piece)
	{
		case "D": return "Q"; break;
		case "L": return "B"; break;
		case "P": return "N"; break;
		case "T": return "R"; break;
		default: return piece;
	}
}

chssLanguage.convertFromLanguageToEnglish = function(piece)
{
	switch(piece)
	{
		case chssLanguage.translate(652): return "Q"; break;
		case chssLanguage.translate(653): return "B"; break;
		case chssLanguage.translate(654): return "N"; break;
		case chssLanguage.translate(655): return "R"; break;
		case chssLanguage.translate(656): return "K"; break;
		default: return piece;
	}
}

chssLanguage.translate = function(name)
{
	return chssLanguage.languageTranslations[name];
}