function chssLoadScreen()
{
	this._progressLeft = undefined;
	this._progressTempLeft = undefined;
	this._progressWidth = undefined;
	this._maxLeft = undefined;
	this._interval = undefined;
	
	this._wrapper = document.createElement("div");
	this._wrapper.style.background = chssOptions.background_color;
	this._wrapper.style.position = "absolute";
	this._wrapper.style.display = "none";
	this._wrapper.style.top = "0px";
	this._wrapper.style.left = "0px";
	this._wrapper.style.bottom = "0px";
	this._wrapper.style.right = "0px";
	this._wrapper.style.zIndex = "1000";
	
	this._loadscreen = document.createElement("div");
	
	this._logo = document.createElement("img");
	this._logo.src = chssOptions.images_url + "logo.png";
	this._logo.style.border = 0;
	
	this._progressWrapper = document.createElement("div");
	this._progressWrapper.style.position = "relative";
	this._progressWrapper.style.background = chssOptions.highlight_color;
	this._progressWrapper.style.overflow = "hidden";
	this._progress = document.createElement("div");
	this._progress.style.background = chssOptions.select_color;
	this._progress.style.position = "absolute";
	
	this._progressTemp = document.createElement("div");
	this._progressTemp.style.background = chssOptions.select_color;
	this._progressTemp.style.position = "absolute";
	this._progressTemp.style.display = "hidden";
	this._progressWrapper.appendChild(this._progress);
	this._progressWrapper.appendChild(this._progressTemp);
	
	this._loadscreen.appendChild(this._logo);
	this._loadscreen.appendChild(this._progressWrapper);
	
	this._wrapper.appendChild(this._loadscreen);
}

chssLoadScreen.prototype = {
		getWrapper: function()
		{
			return this._wrapper;
		},
		
		show: function()
		{
			this._wrapper.style.display = "block";
			this._loadscreen.style.display = "block";
			
			this._loadscreen.style.margin = (this._wrapper.offsetHeight - this._loadscreen.offsetHeight)/2 + "px " + (this._wrapper.offsetWidth - this._loadscreen.offsetWidth)/2 + "px";

			this._logo.style.margin = "0 " + (this._wrapper.offsetWidth - this._logo.offsetWidth)/2 + "px";
			
			this._progressWrapper.style.width = this._logo.offsetWidth * 0.8 + "px";
			this._progressWrapper.style.height = this._loadscreen.offsetHeight * 0.01 + "px";
			//console.log((this._loadscreen.offsetWidth - this._logo.offsetWidth)/2 - (this._logo.offsetWidth * 0.1));
			this._progressWrapper.style.margin = "0 " + ((this._loadscreen.offsetWidth - this._logo.offsetWidth)/2 + (this._logo.offsetWidth * 0.1)) + "px";
			this._maxLeft = this._progressWrapper.offsetWidth;
			this._progressWidth = this._progressWrapper.offsetWidth * 0.20;
			
			this._progress.style.width = this._progressWidth + "px";
			this._progress.style.height = this._loadscreen.offsetHeight * 0.01 + "px";
			this._progress.style.left = -this._progress.offsetWidth + "px";
			this._progressLeft = 0;
			
			this._progressTemp.style.width = this._progressWidth + "px";
			this._progressTemp.style.height = this._loadscreen.offsetHeight * 0.01 + "px";
			this._progressTemp.style.left = -this._progress.offsetWidth + "px";
			this._progressTempLeft = -this._progress.offsetWidth;
			
			var load = this.loading,
				obj = this;
			this._interval = setInterval(function(){load.call(obj)}, 30);
		},
		
		loading: function()
		{
			this._progressLeft += 10;
			if(this._progressLeft > this._maxLeft)
			{
				this._progressLeft = this._progressTempLeft;
				this._progressTempLeft = -this._progressWidth;
				this._progressTemp.style.display = "none";
			}
			if(this._progressLeft + this._progressWidth > this._maxLeft)
			{
				this._progressTempLeft = this._progressLeft + this._progressWidth - this._maxLeft - this._progressWidth;
			}
			this._progress.style.left = this._progressLeft + "px";
			if(this._progressTempLeft != -this._progressWidth)
			{
				this._progressTemp.style.display = "block";
				this._progressTemp.style.left = this._progressTempLeft + "px";
			}
		},
		
		hide: function()
		{
			this._wrapper.style.display = "none";
			clearInterval(this._interval);
		}
}