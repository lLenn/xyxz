<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<link rel="stylesheet" type="text/css" href="assets/style/mainstyle.css" />
<title>Insert title here</title>
</head>
<body>
<div id="board" style="margin-left: 50px; margin-top: 20px;"></div>
</body>
<script type="text/javascript" src="game/pieces/piece.js"></script>
<script type="text/javascript" src="game/pieces/pawn.js"></script>
<script type="text/javascript" src="game/pieces/rook.js"></script>
<script type="text/javascript" src="game/pieces/bishop.js"></script>
<script type="text/javascript" src="game/pieces/knight.js"></script>
<script type="text/javascript" src="game/pieces/queen.js"></script>
<script type="text/javascript" src="game/pieces/king.js"></script>
<script type="text/javascript" src="language/language.js"></script>
<script type="text/javascript" src="assets/languages/language_NL.js"></script>
<script type="text/javascript" src="game/engine.js"></script>
<script type="text/javascript" src="game/boardChange.js"></script>
<script type="text/javascript" src="game/comment.js"></script>
<script type="text/javascript" src="game/pgnfile.js"></script>
<script type="text/javascript" src="game/move.js"></script>
<script type="text/javascript" src="game/variationList.js"></script>
<script type="text/javascript" src="game/game.js"></script>
<script type="text/javascript" src="game/nag.js"></script>
<script type="text/javascript" src="module/moduleManager.js"></script>
<script type="text/javascript" src="lay-out/piece.js"></script>
<script type="text/javascript" src="lay-out/board.js"></script>
<script type="text/javascript" src="lay-out/row.js"></script>
<script type="text/javascript" src="lay-out/promotion.js"></script>
<script type="text/javascript" src="lay-out/moves.js"></script>
<script type="text/javascript" src="lay-out/move.js"></script>
<script type="text/javascript" src="lay-out/variation.js"></script>
<script type="text/javascript" src="lay-out/upload.js"></script>
<script type="text/javascript" src="lay-out/engine.js"></script>
<script type="text/javascript" src="lay-out/button.js"></script>
<script type="text/javascript" src="lay-out/actions.js"></script>
<script type="text/javascript" src="lay-out/drag.js"></script>
<script type="text/javascript" src="chessboard.js"></script>
<script>
<!--
var board = new chssBoard(document.getElementById("board"), "rnbqkbnr/pppppppp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1");
//chssBoard.addMove(0,6,0,5);
//chssBoard.addMove(1,0,2,2);
//chssBoard.addMove(1,1,0,0);
console.log(chssLanguage.translate(305));
console.log(Nag.getNagByCode("$40"));

var wait_for_script = false;
if (!Worker || (location && location.protocol === "file:"))
{
	var script_tag  = document.createElement("script");
	script_tag.type ="text/javascript";
	script_tag.src  = "../Stockfish/stockfish.js";
	script_tag.onload = chssBoard.init_engine;
	document.getElementsByTagName("head")[0].appendChild(script_tag);
	wait_for_script = true;
}

//If we load Stockfish.js via a <script> tag, we need to wait until it loads.'
if (!wait_for_script)
	document.addEventListener("DOMContentLoaded", chssBoard.init_engine);
-->
</script>
</html>