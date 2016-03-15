function Nag()
{
	
}

Nag.nags = new Array(new Array(new Array("$1"), "!", chssLanguage.translate(1299)),
		   new Array(new Array("$2"), "?", chssLanguage.translate(1300)),
		   new Array(new Array("$5"), "!?", chssLanguage.translate(1301)),
		   new Array(new Array("$6"), "?!", chssLanguage.translate(1302)),
		   new Array(new Array("$3"), "!!", chssLanguage.translate(1303)),
		   new Array(new Array("$4"), "??", chssLanguage.translate(1304)),
		   //new Array("$22", "=", "Zugzwang", 1305),
		   new Array(new Array("$7", "$8"), String.fromCharCode(0x25A1), chssLanguage.translate(1306)),
		   new Array(new Array("$18"), "+-", chssLanguage.translate(1307)),
		   new Array(new Array("$16"), "+/-", chssLanguage.translate(1308)),
		   new Array(new Array("$14"), "+/=", chssLanguage.translate(1309)),
		   new Array(new Array("$10", "$11", "$12"), "=", chssLanguage.translate(1310)),
		   //new Array("$13", String.fromCharCode(0x221E), "Unclear position", chssLanguage.translate(1311)),
		   new Array(new Array("$15"), "=/+", chssLanguage.translate(1312)),
		   new Array(new Array("$17"), "-/+", chssLanguage.translate(1313)),
		   new Array(new Array("$19"), "-+", chssLanguage.translate(1314)),
		   //new Array("$44", "/*??*/", chssLanguage.translate(1314)),
		   new Array(new Array("$40", "$41"), String.fromCharCode(0x2192), chssLanguage.translate(1315)),
		   new Array(new Array("$36", "$37", "$38", "$39"), String.fromCharCode(0x2191), chssLanguage.translate(1316)),
		   //new Array(new Array("$132", "$133", "$134", "$135"), String.fromCharCode(0x21C6), chssLanguage.translate(1317)),
		   //new Array(new Array("$136", "$137", "$138", "$139"), "/*??*/", chssLanguage.translate(1318)),
		   new Array(new Array("$30", "$31", "$32", "$33", "$34", "$35"), String.fromCharCode(0x2191)+String.fromCharCode(0x2191), chssLanguage.translate(1319)),
		   new Array(new Array("$146"), "N", chssLanguage.translate(1320)));

Nag.getNagByCode = function(code)
{
	for(var i=0; i<Nag.nags.length; i++)
	{
		for(var j=0; j<Nag.nags[i].length; j++)
		{
			if(Nag.nags[i][j][0] == code)
				return Nag.nags[i];
		}
	}
	return null;
}