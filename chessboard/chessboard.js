var chssOptions = {images_url: "assets/images/",
				   root_url: "",
				   url: "",
				   stockfish_url: "../Stockfish/stockfish.js",
	               board_size: 360,
	               moves_size: 300,
	               font_size: 16,
	               show_possible_moves: true,
	               show_selected_piece: true,
	               alt_color: "#FFFFFF",
	               background_color: "#E3EDC4",
	               highlight_color: "#D0DF99",
	               select_color: "#96BF0D",
	               white_color: "#D0DF99",
	               black_color: "#96BF0D",
	               font: "Calibri"}

var chss_global_vars = {prevClientX: undefined,
						prevClientY: undefined,
						parentLeft: undefined,
						parentTop: undefined,
						dragging: false,
						dragOrder: 0,
						prevSelectedX: undefined,
						prevSelectedY: undefined,
						selectedX: undefined,
						selectedY: undefined,
						localClick: false,
						prevCursor: undefined,
						prevDragElement: undefined,
						prevDragChssPiece: undefined,
						local: false,
						cancelDrag: false}

function getPageOffset(element)
{
	var cumulative = {top: 0, left: 0};
	do
	{
		cumulative.top = cumulative.top + element.offsetTop;
		cumulative.left = cumulative.left + element.offsetLeft;
		element = element.offsetParent;
	}
	while(element)
		
	return cumulative;
}

if (typeof Object.create !== 'function') {
    Object.create = function (o) {
        function F() {}
        F.prototype = o;
        return new F();
    };
}

function chssBoard(container, appArgs, args)
{
	this._container = container;
	this._appArgs = appArgs;
	this._args = args;
	this._engineInitiated = false;
	this._width = undefined;
	this._height = undefined;
	this._boardDrawn = false;
	this._imagesLoaded = false;
	
	if(chssOptions.images_url.charAt(chssOptions.images_url.length-1)!="/") 
		chssOptions.images_url += "/";
	if(chssOptions.root_url != "" && chssOptions.root_url.charAt(chssOptions.root_url.length-1)!="/") 
		chssOptions.root_url += "/";

	chssBoard.engine = new engine();
	chssBoard.ajaxRequest = new chssAjaxRequest();
}

chssBoard.prototype = {
		initiate: function()
		{
			chssPreload(this.draw, this.loadComplete, this);
		},
		
		draw: function()
		{
			chssBoard.chssGame = new chssGame();
			//chssBoard.chssGame = new chssGame("rnbqkbnr/ppppppPp/8/8/8/8/PPPPPPPP/RNBQKBNR w KQkq - 0 1");
			//chssBoard.chssGame = new chssGame("rn1q1bnr/pp1k1ppp/4p3/1b1pP3/3P4/2K5/PP3PPP/RNBQ1BNR w - - 0 1");
			chssBoard.chssGame.setEdit(true);
			chssBoard.board = new boardElement(this._container, appArgs.centerToScreen);
			chssBoard.mobileManager = new chssMobileManager();
			chssBoard.board.draw();
			this._boardDrawn = true;
			this.init_engine(false);
			this.loadComplete();
		},
		
		loadComplete: function(imagesLoaded)
		{
			if(typeof imagesLoaded !== 'undefined')
				this._imagesLoaded = imagesLoaded;

			if(this._boardDrawn && this._imagesLoaded)
			{
				chssBoard.moduleManager = new chssModuleManager(this._args, chssBoard.board);
				this.resize(false, false);
			}
		},
		
		resize: function(width, height)
		{
			if(width === false && height === false)
				chssBoard.board.drawSize();
			else if(!(width === false && height === false))
			{
				this._width = width;
				this._height = height;
			}
			
			if(this._boardDrawn && typeof this._width !== 'undefined' && typeof this._height !== 'undefined')
				chssBoard.board.resize(this._width, this._height);
		},
		
		init_engine: function(engineInitiated)
		{
			if(engineInitiated)
				this._engineInitiated = engineInitiated;
			if(this._engineInitiated && this._boardDrawn)
				chssBoard.engine.initiate();
		},
		
		getBoard: function()
		{
			return chssBoard.board;
		}
}