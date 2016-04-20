
chssMobileManager.SMALL = 0;
chssMobileManager.MEDIUM = 1;
chssMobileManager.NORMAL = -1;

function chssMobileManager()
{
	this._isMobileWidth = false;
	this._isMobileAgent = false;
	this._mobileType = chssMobileManager.NORMAL;
	
	this._canvasTop = undefined;
	this._canvasWith = undefined;
	this._landscapeTop = undefined;
	this._landscapeWidth = undefined;
	
	this.checkMobile();
	this.addEventListeners();
}

chssMobileManager.prototype = {
		constructor: chssMobileManager,
		checkMobile: function()
		{
			this._isMobileAgent = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)
			if(window.innerHeight > window.innerWidth && typeof window.matchMedia === "function" && !window.matchMedia("(min-width: 1025px)").matches)
			{
				this._isMobile = true;
				this._mobileType = chssModuleManager.SMALL;
			}
			else if(typeof window.matchMedia === "function" && !window.matchMedia("(min-width: 1025px)").matches)
			{
				this._isMobile = true;
				this._mobileType = chssModuleManager.MEDIUM;
			}
			else
				this._isMobile = false;
		},
		
		isMobile: function()
		{
			return this._isMobile && this._isMobileAgent;
		},
		
		addEventListeners: function()
		{
			if(window.matchMedia)
			{
				var parent = this;
				window.matchMedia("(min-width: 1025px)").addListener(function()
						{
							parent.checkMobile();
							if(this._isMobileAgent)
								chssBoard.board.resize(window.innerWidth, window.innerHeight);
						});
			}
		},
		
		
		
		setLandscapePosition: function(width, top)
		{
			this._landscapeWidth = width;
			this._landscapeTop = top;
		},
		
		setPortraitPosition: function(width, top)
		{
			this._portraitWidth = width;
			this._portraitTop = top;
		},
		
		setElementPosition: function(element, extraLeft, extraTop)
		{
			switch(this._mobileType)
			{
				case chssMobileManager.SMALL: 
							element.style.top = this._portraitTop + extraTop + "px";
							element.style.left = this._portraitLeft + extraLeft + "px";
							break;
				case chssMobileManager.MEDIUM:
				case chssMobileManager.NORMAL: 
							element.style.top = this._canvasTop + extraTop + "px";
							element.style.left = this._canvasLeft + extraLeft + "px";
							break;
			}
		}
}