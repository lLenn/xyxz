function chssInfoComponent(label, text)
{
	if(typeof label == 'undefined')
		label = '';
	if(typeof text == 'undefined')
		text = '';
	
	this._wrapper = document.createElement("div");

	this._label = document.createElement("div");
	this._label.innerHTML = label;
	this._label.style.float = "left";
	this._label.className = "infoLabel";
	this._text = document.createElement("div");
	this._text.style.float = "right";
	this._text.className = "infoText";
	
	var brk = document.createElement("br");
	brk.className = "clearfloat";
	
	this._wrapper.appendChild(this._label);
	this._wrapper.appendChild(this._text);
	this._wrapper.appendChild(brk);
}

chssInfoComponent.prototype = {
		getWrapper: function()
		{
			return this._wrapper;
		},
		
		setLabel: function(label)
		{
			this._label.innerHTML = label;
		},
		
		setText: function(text, alignment)
		{
			if(typeof alignment == 'undefined')
				alignment = 'left';
			this._text.innerHTML = text;
			this._text.style.textAlign = alignment;
		},
		
		getLabelComponent: function()
		{
			return this._label;
		},
		
		getTextComponent: function()
		{
			return this._text;
		},
		
		setTextHeight: function(height)
		{
			chssHelper.showScroll(this._text, height);
		}
}