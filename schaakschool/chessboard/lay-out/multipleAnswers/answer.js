function chssAnswer(id, answer, correct, callback, object)
{	
	var paddingHor1 = 13 * (chssOptions.moves_size/200),
		paddingVer = 1.5 * (chssOptions.board_size/360),
		fontSize = 16 * (chssOptions.board_size/360),
		paddingVer2 = ((fontSize * 1.5) - fontSize)/2;
	
	this._id = id;
	this._answer = answer;
	this._callback = callback;
	this._object = object;
	
	this._wrapper = document.createElement("div");
	this._wrapper.style.padding = paddingVer2 + "px 0px";

	this._answerElement = document.createElement("div");
	this._answerElement.style.float = "left";
	this._answerElement.style.margin = paddingVer + "px " + paddingHor1 + "px " + paddingVer + "px " + paddingHor1 + "px";
	this._answerElement.style.fontSize = fontSize + "px";
	this._answerElement.style.lineHeight = fontSize + "px";

	this._radioElement = new chssRadio("answer", correct);
	this._radioElement.getWrapper().style.float = "right";
	this._radioElement.getWrapper().style.margin = paddingVer + "px " + paddingHor1 + "px " + "0px 0px";
	
	var brk = document.createElement("br");
	brk.className = "clearfloat";
	
	this._wrapper.appendChild(this._answerElement);
	this._wrapper.appendChild(this._radioElement.getWrapper());
	this._wrapper.appendChild(brk);
	
	this.addEvents();
}

chssAnswer.prototype = {
		
		getWrapper: function()
		{
			return this._wrapper;
		},
		
		draw: function()
		{
			this._answerElement.style.width = this._wrapper.clientWidth - this._radioElement.getWrapper().offsetWidth - 51 * (chssOptions.moves_size/200) + "px";
			this._answerElement.innerHTML = chssHelper.wordWrap(this._answerElement, this._answer, parseFloat(this._answerElement.style.width), false, false);
			chssHelper.showScroll(this._answerElement, parseFloat(this._answerElement.style.fontSize)*2);
		},
		
		addEvents: function()
		{
			this._radioElement.onclick(this.radioOnClick, this);
		},
		
		radioOnClick: function(selected, value)
		{
			if(selected)
				this._callback.call(this._object, this._id, value);
			else
				this._callback.call(this._object, -1, false);
		},
		
		validate: function(correct)
		{
			this._radioElement.validate(correct);
		},
		
		reset: function()
		{
			this._radioElement.reset();
		},
		
		hide: function()
		{
			this._radioElement.hide();
		},
		
		getId: function()
		{
			return this._id;
		},
		
		getCorrect: function()
		{
			return this._radioElement.getValue();
		},
		
		getRadioElement: function()
		{
			return this._radioElement.getWrapper();
		},
		
		resize: function()
		{
			this._answerElement.innerHtml = "";
			
			var paddingHor1 = 13 * (chssOptions.moves_size/200),
				paddingVer = 1.5 * (chssOptions.board_size/360),
				fontSize = 16 * (chssOptions.board_size/360),
				paddingVer2 = ((fontSize * 1.5) - fontSize)/2;

			this._wrapper.style.padding = paddingVer2 + "px 0px";

			this._answerElement.style.margin = paddingVer + "px " + paddingHor1 + "px " + paddingVer + "px " + paddingHor1 + "px";
			this._answerElement.style.fontSize = fontSize + "px";
			this._answerElement.style.lineHeight = fontSize + "px";
			this._answerElement.style.width = this._wrapper.clientWidth - this._radioElement.getWrapper().offsetWidth - 51 * (chssOptions.moves_size/200) + "px";
			this._answerElement.innerHTML = chssHelper.wordWrap(this._answerElement, this._answer, parseFloat(this._answerElement.style.width), false, false);
			chssHelper.showScroll(this._answerElement, parseFloat(this._answerElement.style.fontSize)*2);

			this._radioElement.resize();
		}
}