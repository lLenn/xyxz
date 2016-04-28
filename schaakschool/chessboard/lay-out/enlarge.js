function chssEnlargeButton(callback, object)
{
	this._enlarged = false;
	
	this._wrapper = document.createElement("div");
	this._wrapper.style.position = "relative";
	this._wrapper.style.cursor = "pointer";

	this._enlargeTopRightCorner = document.createElement("div");
	this._enlargeTopRightCorner.style.position = "absolute";
	this._enlargeTopRightCorner.style.top = "0";
	this._enlargeTopRightCorner.style.right = "0";
	this._enlargeTopRightCorner.style.borderStyle = "solid";
	this._enlargeTopRightCorner.style.borderColor = "#000000";
	this._enlargeTopRightCorner.style.borderBottom = "0";
	this._enlargeTopRightCorner.style.borderLeft = "0";
	
	this._enlargeTopLeftCorner = document.createElement("div");
	this._enlargeTopLeftCorner.style.position = "absolute";
	this._enlargeTopLeftCorner.style.top = "0";
	this._enlargeTopLeftCorner.style.left = "0";
	this._enlargeTopLeftCorner.style.borderStyle = "solid";
	this._enlargeTopLeftCorner.style.borderColor = "#000000";
	this._enlargeTopLeftCorner.style.borderBottom = "0";
	this._enlargeTopLeftCorner.style.borderRight = "0";
	
	this._enlargeBotRightCorner = document.createElement("div");
	this._enlargeBotRightCorner.style.position = "absolute";
	this._enlargeBotRightCorner.style.bottom = "0";
	this._enlargeBotRightCorner.style.right = "0";
	this._enlargeBotRightCorner.style.borderStyle = "solid";
	this._enlargeBotRightCorner.style.borderColor = "#000000";
	this._enlargeBotRightCorner.style.borderTop = "0";
	this._enlargeBotRightCorner.style.borderLeft = "0";
	
	this._enlargeBotLeftCorner = document.createElement("div");
	this._enlargeBotLeftCorner.style.position = "absolute";
	this._enlargeBotLeftCorner.style.bottom = "0";
	this._enlargeBotLeftCorner.style.left = "0";
	this._enlargeBotLeftCorner.style.borderStyle = "solid";
	this._enlargeBotLeftCorner.style.borderColor = "#000000";
	this._enlargeBotLeftCorner.style.borderTop = "0";
	this._enlargeBotLeftCorner.style.borderRight = "0";
	
	this._enlargeCenter = document.createElement("div");
	this._enlargeCenter.style.position = "absolute";
	this._enlargeCenter.style.background = "#000000";
	
	this._wrapper.appendChild(this._enlargeTopRightCorner);
	this._wrapper.appendChild(this._enlargeTopLeftCorner);
	this._wrapper.appendChild(this._enlargeBotRightCorner);
	this._wrapper.appendChild(this._enlargeBotLeftCorner);
	this._wrapper.appendChild(this._enlargeCenter);
	this.enlargeMouseEvents(callback, object);
	
}

chssEnlargeButton.prototype = {
		getWrapper: function()
		{
			return this._wrapper;
		},
		
		draw: function(height)
		{
			this._wrapper.style.height = height + "px";
			this._wrapper.style.width = height + "px";

			height = height * 0.8;
			
			this._enlargeCenter.style.width = height * 0.3 + "px";
			this._enlargeCenter.style.height = height * 0.3 + "px";
			this._enlargeCenter.style.top = height * 0.35 + "px";
			this._enlargeCenter.style.left = height * 0.35 + "px";
			
			var width = height * 0.2;
			var border = height * 0.1;
			
			this._enlargeTopRightCorner.style.width = width + "px";
			this._enlargeTopRightCorner.style.height = width + "px";
			this._enlargeTopRightCorner.style.marginTop = border + "px";
			this._enlargeTopRightCorner.style.marginRight = border + "px";
			this._enlargeTopRightCorner.style.borderWidth = border + "px";
			
			this._enlargeTopLeftCorner.style.width = width + "px";
			this._enlargeTopLeftCorner.style.height = width + "px";
			this._enlargeTopLeftCorner.style.marginTop = border + "px";
			this._enlargeTopLeftCorner.style.marginLeft = border + "px";
			this._enlargeTopLeftCorner.style.borderWidth = border + "px";
			
			this._enlargeBotLeftCorner.style.width = width + "px";
			this._enlargeBotLeftCorner.style.height = width + "px";
			this._enlargeBotLeftCorner.style.marginBottom = border + "px";
			this._enlargeBotLeftCorner.style.marginLeft = border + "px";
			this._enlargeBotLeftCorner.style.borderWidth = border + "px";
			
			this._enlargeBotRightCorner.style.width = width + "px";
			this._enlargeBotRightCorner.style.height = width + "px";
			this._enlargeBotRightCorner.style.marginBottom = border + "px";
			this._enlargeBotRightCorner.style.marginRight = border + "px";
			this._enlargeBotRightCorner.style.borderWidth = border + "px";
		},
		
		enlargeMouseEvents: function(callback, callbackObject)
		{
			var object = this;
			this._wrapper.onmouseover = function()
			{
				object._wrapper.style.background = chssOptions.select_color;
				object._enlargeTopRightCorner.style.borderColor = chssOptions.alt_color;
				object._enlargeTopLeftCorner.style.borderColor = chssOptions.alt_color;
				object._enlargeBotRightCorner.style.borderColor = chssOptions.alt_color;
				object._enlargeBotLeftCorner.style.borderColor = chssOptions.alt_color;
				object._enlargeCenter.style.background = chssOptions.alt_color;
			}
			
			this._wrapper.onmouseout = function()
			{
				object._wrapper.style.background = chssOptions.background_color;
				object._enlargeTopRightCorner.style.borderColor = "#000000";
				object._enlargeTopLeftCorner.style.borderColor = "#000000";
				object._enlargeBotRightCorner.style.borderColor = "#000000";
				object._enlargeBotLeftCorner.style.borderColor = "#000000";
				object._enlargeCenter.style.background = "#000000";
			}
			
			this._wrapper.onclick = function()
			{
				if(this._enlarged)
				{
					object._enlargeTopRightCorner.style.top = "0";
					object._enlargeTopRightCorner.style.right = "0";
					object._enlargeTopRightCorner.style.bottom = "auto";
					object._enlargeTopRightCorner.style.left = "auto";
					
					object._enlargeTopLeftCorner.style.top = "0";
					object._enlargeTopLeftCorner.style.left = "0";
					object._enlargeTopLeftCorner.style.bottom = "auto";
					object._enlargeTopLeftCorner.style.right = "auto";
					
					object._enlargeBotRightCorner.style.bottom = "0";
					object._enlargeBotRightCorner.style.right = "0";
					object._enlargeBotRightCorner.style.top = "auto";
					object._enlargeBotRightCorner.style.left = "auto";
					
					object._enlargeBotLeftCorner.style.bottom = "0";
					object._enlargeBotLeftCorner.style.left = "0";
					object._enlargeBotLeftCorner.style.top = "auto";
					object._enlargeBotLeftCorner.style.right = "auto";
				}
				else
				{
					object._enlargeTopRightCorner.style.top = "auto";
					object._enlargeTopRightCorner.style.right = "auto";
					object._enlargeTopRightCorner.style.bottom = "0";
					object._enlargeTopRightCorner.style.left = "0";
					
					object._enlargeTopLeftCorner.style.top = "auto";
					object._enlargeTopLeftCorner.style.left = "auto";
					object._enlargeTopLeftCorner.style.bottom = "0";
					object._enlargeTopLeftCorner.style.right = "0";
					
					object._enlargeBotRightCorner.style.bottom = "auto";
					object._enlargeBotRightCorner.style.right = "auto";
					object._enlargeBotRightCorner.style.top = "0";
					object._enlargeBotRightCorner.style.left = "0";
					
					object._enlargeBotLeftCorner.style.bottom = "auto";
					object._enlargeBotLeftCorner.style.left = "auto";
					object._enlargeBotLeftCorner.style.top = "0";
					object._enlargeBotLeftCorner.style.right = "0";
				}
				object._enlarged = !object._enlarged;
				callback.call(callbackObject);
			}
		}
}