function chssExcerciseInfo()
{
	this._title = undefined;
	this._description = undefined;
	this._version = undefined;
	this._total = undefined;

	this._score = new Array();
	this._pointer = 0;
	
	this._wrapper = document.createElement("div");
	this._wrapper.style.backgroundColor = chssOptions.background_color;
	this._wrapper.className = "wrapper";
	//this._wrapper.style.zIndex = 1;
	
	this._titleElement = document.createElement("div")
	this._titleElement.style.fontWeight = "bold";
	this._titleElement.className = "elementWrapper";
	
	this._descriptionElement = document.createElement("div")
	this._descriptionElement.className = "elementWrapper";
	
	this._progressWrapper = document.createElement("div");
	this._progressWrapper.id = "progressWrapper";
	
	this._wrapper.appendChild(this._titleElement);
	this._wrapper.appendChild(this._descriptionElement);
	this._wrapper.appendChild(this._progressWrapper);
}

chssExcerciseInfo.prototype = {
		getWrapper: function()
		{
			return this._wrapper;
		},

		setInfo: function(title, description, version, total)
		{
			this._title = title;
			this._description = description;
			this._version = version;
			this._total = total;
		},
		
		addScore: function(score, pointer)
		{
			if(typeof pointer == "undefined")
				pointer = false;
			if(pointer)
				this._pointer++;
			this._score.push(score);
		},
		
		setScore: function(score)
		{
			this._score = score;
		},
		
		setPointer: function(pointer)
		{
			this._pointer = pointer;
		},
		
		draw: function(small)
		{
			if(typeof small == "undefined")
				small = false;
			
			var title = "",
				description = "";
			this._titleElement.innerHTML = "";
			this._descriptionElement.innerHTML = "";
			
			switch(this._version)
         	{
         		case 1: title = this._title;
         				description = this._description;
         				break;
         		case 2: title = chssLanguage.translate(516);
 						description = chssLanguage.translate(1409);
 						break;
         		case 3: title = chssLanguage.translate(517);
 						description = chssLanguage.translate(1410);
         				break;
         	}
			
			this._titleElement.innerHTML = chssHelper.wordWrap(this._titleElement, title, this._titleElement.clientWidth, false, false);
			chssHelper.showScroll(this._titleElement, this._wrapper.clientWidth);
			
			if(small)
			{
				this._descriptionElement.style.display = "none";
				this._progressWrapper.style.display = "block";
				this.drawProgress();
			}
			else
			{
				this._progressWrapper.style.display = "none";
				this._descriptionElement.style.display = "block";
				this._descriptionElement.innerHTML = chssHelper.wordWrap(this._descriptionElement, description, this._descriptionElement.clientWidth, false, false);
				chssHelper.showScroll(this._descriptionElement, this._wrapper.clientWidth);
			}
		},
		
		drawProgress: function()
		{
			var rect_width = Math.floor((this._progressWrapper.clientWidth - 1)/this._total);
			
			var enabled_fault_colour = "#F71F06";
			var enabled_correct_colour = "#479203";
			var disabled_colour = chssOptions.highlight_color;

			this._progressWrapper.innerHTML = "";
			for(var i= 0; i<this._total; i++)
			{
				var col = enabled_correct_colour;
				if(i>=this._pointer)
					col = disabled_colour;
				else if(i+1 <= this._pointer && i+1 <= this._score.length && !this._score[i])
					col = enabled_fault_colour;
				var box = document.createElement("div");
				box.style.display = "inline-block" 
				box.style.backgroundColor = col;
				box.style.width = rect_width + "px";
				box.style.height = 3 * (chssOptions.board_size/360) + "px";
				this._progressWrapper.appendChild(box);
			}
		},
		
		resize: function()
		{
			this.drawProgress();
			
			var title = "",
				description = "";
			this._titleElement.innerHTML = "";
			this._descriptionElement.innerHTML = "";
			
			switch(this._version)
	     	{
	     		case 1: title = this._title;
	     				description = this._description;
	     				break;
	     		case 2: title = chssLanguage.translate(516);
							description = chssLanguage.translate(1409);
							break;
	     		case 3: title = chssLanguage.translate(517);
							description = chssLanguage.translate(1410);
	     				break;
	     	}
			
			this._titleElement.innerHTML = chssHelper.wordWrap(this._titleElement, title, this._titleElement.offsetWidth, false, false);
			chssHelper.showScroll(this._titleElement, this._wrapper.clientWidth);
			
			if(this._descriptionElement.style.display != "none")
			{
				this._descriptionElement.innerHTML = chssHelper.wordWrap(this._descriptionElement, description, this._descriptionElement.offsetWidth, false, false);
				chssHelper.showScroll(this._descriptionElement, this._wrapper.clientWidth);
			}
		},
		
		reset: function()
		{
			this._score = new Array();
			this._pointer = 0;
		},
		
		getScore: function()
		{
			return this._score;
		}
}