chssFormComponent.TEXT = "1";

function chssFormComponent(label, type, name, value)
{
	if(typeof value === 'undefined')
		value = '';
	
	this._type = type;
	this._name = name;
	this._defaultValue = value;
	
	this._wrapper = document.createElement("div");

	this._label = document.createElement("div");
	this._label.innerHTML = label;
	this._label.style.float = "left";
	this._label.className = "FormLabel";

	this._text = document.createElement("div");
	this._text.style.float = "right";
	this._text.className = "FormInput";
	
	this._input = this.createInput();
	this._text.appendChild(this._input)
	
	var brk = document.createElement("br");
	brk.className = "clearfloat";
	
	this._wrapper.appendChild(this._label);
	this._wrapper.appendChild(this._text);
	this._wrapper.appendChild(brk);
}

chssFormComponent.prototype = {
		getWrapper: function()
		{
			return this._wrapper;
		},
		
		setLabel: function(label)
		{
			this._label.innerHTML = label;
		},
		
		createInput: function()
		{
			var type = "",
				className = "";
			
			switch(this._type)
			{
				case chssFormComponent.TEXT: type = className = "text"; break;
				default: throw new Error("Wrong input type!"); break;
			}
			
			var input = document.createElement("input");
			input.type = type;
			input.value = this._defaultValue;
			input.name = this._name;
			input.className = "text";
			
			return input;
		},
		
		getValue: function()
		{
			switch(this._type)
			{
				case chssFormComponent.TEXT: return this._input.value;
				default: return undefined;
			}
		}
}