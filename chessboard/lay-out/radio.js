chssRadio.groups = {};

function chssRadio(name, value)
{
	if(!chssRadio.groups.hasOwnProperty(name))
		chssRadio.groups[name] = new Array();
	
	this._selected = false;
	this._disabled = false;
	this._id = chssRadio.groups[name].length;
	this._name = name;
	this._value = value;
	
	this._onclickFunction = undefined;
	this._onclickObject = null;
	
	this._wrapper = document.createElement("div")
	this._wrapper.style.cursor = "pointer";
	this._wrapper.style.fontSize = 16 * (chssOptions.board_size/360) + "px";
	
	this._unselectedElement = document.createElement("div");
	this._unselectedElement.style.float = "right";
	this._unselectedElement.style.display = "block";
	this._unselectedElement.className = "validation";
	this._unselectedElement.innerHTML = "&#xe157";
		
	this._selectedElement = document.createElement("div");
	this._selectedElement.style.float = "right";
	this._selectedElement.style.display = "none";
	this._selectedElement.className = "validation";
	this._selectedElement.innerHTML = "&#xe067;";
	
	this._correctElement = document.createElement("div");
	this._correctElement.style.float = "right";
	this._correctElement.style.display = "none";
	this._correctElement.className = "validation correct";
	this._correctElement.innerHTML = "&#xe013;";
	
	this._wrongElement = document.createElement("div");
	this._wrongElement.style.float = "right";
	this._wrongElement.style.display = "none";
	this._wrongElement.className = "validation wrong";
	this._wrongElement.innerHTML = "&#xe014;";
	
	this._wrapper.appendChild(this._selectedElement);
	this._wrapper.appendChild(this._unselectedElement);
	this._wrapper.appendChild(this._wrongElement);
	this._wrapper.appendChild(this._correctElement);
	
	this.addEvents();
	chssRadio.groups[name].push({id: this._id, obj: this});
}

chssRadio.prototype = {
		getWrapper: function()
		{
			return this._wrapper;
		},
		
		addEvents: function()
		{
			var obj = this,
				cb = this.click;
			this._wrapper.onclick = function(){cb.call(obj)};
		},
		
		selected: function()
		{
			return this._selected;
		},
		
		click: function()
		{
			if(!this._disabled)
			{
				this._selected = !this._selected;
				if(this._selected)
				{
					for(var i=0;i<chssRadio.groups[this._name].length;i++)
					{
						if(chssRadio.groups[this._name][i].id != this._id && chssRadio.groups[this._name][i].obj.selected())
							chssRadio.groups[this._name][i].obj.click();
					}
					
					this._selectedElement.style.display = "block";
					this._unselectedElement.style.display = "none";
				}
				else
				{
					this._selectedElement.style.display = "none";
					this._unselectedElement.style.display = "block";
				}				
				this._onclickFunction.call(this._onclickObject, this._selected, this._value);
			}
		},
		
		validate: function(correct)
		{
			this._disabled = true;
			this._unselectedElement.style.display = "none";
			this._selectedElement.style.display = "none";
			if(correct)
			{
				this._correctElement.style.display = "block";
				this._wrongElement.style.display = "none";
			}
			else
			{
				this._correctElement.style.display = "none";
				this._wrongElement.style.display = "block";
			}
		},
		
		reset: function()
		{
			this._disabled = false;
			this._selected = false;
			this._unselectedElement.style.display = "block";
			this._selectedElement.style.display = "none";
			this._correctElement.style.display = "none";
			this._wrongElement.style.display = "none";
		},
		
		hide: function()
		{
			this._unselectedElement.style.display = "none";
			this._selectedElement.style.display = "none";
			this._correctElement.style.display = "none";
			this._wrongElement.style.display = "none";
		},
		
		onclick: function(fnc, obj)
		{
			this._onclickFunction = fnc;
			if(typeof obj != 'undefined')
				this._onclickObject = obj;
			else
				this._onclickObject = null;
		},
		
		getValue: function()
		{
			return this._value;
		},
		
		resize: function()
		{
			this._wrapper.style.fontSize = 16 * (chssOptions.board_size/360) + "px";
		}
}