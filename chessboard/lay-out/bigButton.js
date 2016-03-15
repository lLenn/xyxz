function bigButton(text)
{
	this._width = undefined;
	this._height = undefined;
	
	this._wrapper = document.createElement("div");
	this._wrapper.className = "bigButton unselectable";

	this._label = document.createElement("div");
	this._label.style.textAlign = "center";
	this._label.innerHTML = text;
	
	this._wrapper.appendChild(this._label);
}

bigButton.prototype = {
		onclick: function(callback, obj)
		{
			this._wrapper.onclick = function(){ callback.call(obj); };
		},
		
		setSize: function(width, height)
		{			
			this._wrapper.style.padding = 0;
			this._wrapper.style.height = "auto";
			this._wrapper.style.width = "auto";
			
			var paddingVer = (height - this._label.offsetHeight)/2 - parseFloat(chssHelper.getComputedStyle(this._wrapper, "border-top-width")),
				paddingHor = (width - Math.floor(this._label.offsetWidth))/2 - parseFloat(chssHelper.getComputedStyle(this._wrapper, "border-top-width"));
			
			this._width = width;
			this._height = height;
			
			this._wrapper.style.padding = paddingVer + "px " + paddingHor + "px";
		},
		
		getWrapper: function()
		{
			return this._wrapper;
		},
		
		setText: function(text)
		{
			var span = document.createElement("span");
			span.innerHTML = text;
			this._wrapper.appendChild(span);
			
			this._label.innerHTML = text;
			this._label.style.width = span.offsetWidth + 1 + "px";
			
			this._wrapper.removeChild(span);
		},
		
		changeState: function(text, callback, obj)
		{			
			this._wrapper.style.padding = 0;
			this._wrapper.style.height = "auto";
			this._wrapper.style.width = "auto";
			
			this.setText(text);
			this.resize(this._width, this._height)
			this.onclick(callback, obj);
		},
		
		resize: function(width, height)
		{
			this.setSize(width, height);
		}
}