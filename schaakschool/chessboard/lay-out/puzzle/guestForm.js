function chssGuestForm(callback, object)
{	
	this._callback = callback;
	this._object = object;
	
	this._rating = 0;
	this._email = "";
	
	this._wrapper = document.createElement("div");
	this._wrapper.style.backgroundColor = chssOptions.background_color;
	this._wrapper.className = "wrapper";

	this._error = document.createElement("div");
	this._error.className = "error";
	this._error.innerHTML = chssLanguage.translate(998);
	this._error.style.display = "none";
	
	this._label = document.createElement("div");
	this._label.innerHTML = chssLanguage.translate(995);
	this._ratingInput = new chssFormComponent(chssLanguage.translate(996), chssFormComponent.TEXT, "rating_guest", "");
	this._emailInput = new chssFormComponent(chssLanguage.translate(997), chssFormComponent.TEXT, "email_guest", "");
	
	this._confirm = document.createElement("div");
	this._confirmLbl = document.createElement("span");
	this._confirmLbl.innerHTML = chssLanguage.translate(999);
	this._confirm.style.backgroundColor = chssOptions.black_color;
	this._confirm.style.color = chssOptions.alt_color;
	this._confirm.style.display = "inline";
	this._confirm.style.cursor = "pointer";
	this._confirm.appendChild(this._confirmLbl);
	
	this._wrapper.appendChild(this._error);
	this._wrapper.appendChild(this._label);
	this._wrapper.appendChild(this._ratingInput.getWrapper());
	this._wrapper.appendChild(this._emailInput.getWrapper());
	this._wrapper.appendChild(this._confirm);
	
	this.addEvents();
}

chssGuestForm.prototype = {
		draw: function()
		{
			var border = 1 * (chssOptions.board_size/360),
				fontSize = 16 * (chssOptions.board_size/360)
				radius = 5 * (chssOptions.board_size/360),
				padding = 12 * (chssOptions.board_size/360),
				marginLeft = 7 * (chssOptions.board_size/360),
				marginHor = 7 * (chssOptions.board_size/360),
				marginHor2 = 3 * (chssOptions.board_size/360),
				btnHorPadding = 8 * (chssOptions.board_size/360),
				btnVerPadding = 1 * (chssOptions.board_size/360);

			this._wrapper.style.border = "solid " + border + "px " + chssOptions.black_color;
			this._wrapper.style.padding = padding + "px";
			
			this._error.style.borderWidth = border + "px";
			this._error.style.padding = border + "px " + marginHor2 + "px";
			this._error.style.marginBottom = marginHor + "px";
			//this._confirm.setFontSize(fontSize);
			
			this._ratingInput.getWrapper().style.marginTop = marginHor + "px";
			this._emailInput.getWrapper().style.marginTop = marginHor2 + "px";
			this._emailInput.getWrapper().style.marginBottom = marginHor + "px";
			
			this._confirm.style.marginLeft = marginLeft + "px";
			this._confirm.style.border = "solid " + border + "px " + chssOptions.black_color;
			this._confirm.style.padding = btnVerPadding + "px " + btnHorPadding + "px";
			this._confirm.style.MozBorderRadius = radius + "px";
			this._confirm.style.borderRadius = radius + "px";
		},
		
		getWrapper: function()
		{
			return this._wrapper;
		},
		
		addEvents: function()
		{
			var obj = this;
			
			this._confirm.onclick = function()
			{
				if(obj.validateForm())
				{
					obj._callback.call(obj._object);
				}
			}
		},
		
		validateForm: function()
		{
			this._rating = parseInt(this._ratingInput.getValue());
			this._email = this._emailInput.getValue();
			
			if(isNaN(this._rating) || this._rating < 300 || this._rating > 2700)
			{
				this._error.style.display = "block";
				return false;
			}
			else
				return true;
		},
		
		getRating: function()
		{
			return this._rating;
		},
		
		setRating: function(rating)
		{
			this._rating = rating;
		},
		
		getEmail: function()
		{
			return this._email;
		}
}