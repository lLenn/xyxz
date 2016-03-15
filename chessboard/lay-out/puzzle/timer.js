function chssTimer(seconds)
{	
	this._seconds = seconds;
	
	this._timer = document.createElement("div");
	this._timer.style.backgroundColor = chssOptions.alt_color;
	this._timer.style.position = "absolute";
	this._progress = document.createElement("div");
	this._progress.style.backgroundColor = chssOptions.black_color;
	this._progress.innerHTML = "&nbsp;";
	this._timer.appendChild(this._progress);
	
	this._timerId = undefined;
	this._timeProgressed = 0;
	this._onFinished = null;
}

chssTimer.prototype = {
		initiate: function(width, height, left, top)
		{
			this._timer.style.width = width + "px";
			this._timer.style.height = height + "px";
			this._timer.style.top = top + "px";
			this._timer.style.left = left + "px";
			
			this._progress.style.width = "0px";
			this._progress.style.height = height + "px";
		},
		
		start: function()
		{
			this._timerId = this.setIntervalFunction(this)
		},
		
		stop: function()
		{
			clearInterval(this._timerId);
		},
		
		reset: function()
		{
			this._progress.style.width = "0px";
			this._timeProgressed = 0;
		},
		
		setIntervalFunction: function(parent)
		{
			return setInterval(function()
					{
						parent._timeProgressed += 100;
						if(parent._timeProgressed <= parent._seconds*1000)
						{
							parent._progress.style.width = parseFloat(parent._timer.style.width) * (parent._timeProgressed/(parent._seconds*1000)) + "px"	;
						}
						else
						{
							clearInterval(parent._timerId);
							if(parent._onFinished != null)
								parent._onFinished[0].call(parent._onFinished[1]);
							else
								console.log("Timeout");
						}
					}, 100);
		},
		
		getTimerElement: function()
		{
			return this._timer;
		},
		
		onFinish: function(fnc, obj)
		{
			this._onFinished = [fnc, obj];
		},
		
		getProgressedTime: function()
		{
			return this._timeProgressed;
		}
}