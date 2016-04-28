chssCommentArea.IS_BREAK = "0";
chssCommentArea.GET_COMMENT = "1";
chssCommentArea.GET_COMMENTS = "2";

chssCommentArea.CREATORS_COMMENT = "3";
chssCommentArea.OTHER_COMMENTS = "4";
chssCommentArea.YOUR_COMMENT = "5";

chssCommentArea.SMALL = "6";
chssCommentArea.BIG = "7";

function chssCommentArea()
{
	var	fontSize = 16 * (chssOptions.board_size/360);
	
	this._comment = undefined;
	this._comments = undefined;
	this._comments_string = undefined;
	this._comment_write = undefined;
	this._visible = false;
	this._edit = false;
	this._smallHeight = undefined;
	this._bigHeight = undefined;
	this._currentSize = chssCommentArea.SMALL;
	this._currentView = chssCommentArea.CREATORS_COMMENT;
	
	this._wrapper = document.createElement("div");
	this._wrapper.style.display = "relative";
	this._wrapper.style.backgroundColor = chssOptions.background_color;
	
	this._subWrapper = document.createElement("div");
	this._menu = document.createElement("div");
	this._creatorComment = new chssLinkButton();
	this._creatorComment.setText(chssLanguage.translate(275));
	this._creatorComment.getWrapper().style.float = "left";
	
	this._otherComments = new chssLinkButton();
	this._otherComments.setText(chssLanguage.translate(1332));
	this._otherComments.getWrapper().style.float = "left";
	/*
	this._yourComment = new chssLinkButton();
	this._yourComment.setText(chssLanguage.translate(1334));
	this._yourComment.getWrapper().style.float = "right";
	*/
	this._menu.appendChild(this._creatorComment.getWrapper());
	this._menu.appendChild(this._otherComments.getWrapper());
	var clear = document.createElement("br");
	clear.className = "clearfloat";
	this._menu.appendChild(clear);
	//this._menu.appendChild(this._yourComment.getWrapper());
	
	this._label = document.createElement("div");
	this._label.innerHTML = chssLanguage.translate(352) + ":";

	this._textWrapper = document.createElement("div");
	this._textWrapper.style.overflowY = "auto";
	this._textWrapper.style.float = "right";
	this._textArea = document.createElement("div");
	this._textWrapper.appendChild(this._textArea);
	
	this._subWrapper.appendChild(this._menu);
	this._subWrapper.appendChild(this._label);
	this._subWrapper.appendChild(this._textWrapper);
	
	this._wrapper.appendChild(this._subWrapper);
	
	this._changeView = document.createElement("div");
	this._changeView.className = "smallButton unselectable";
	this._changeView.style.position = "absolute";
	this._changeView.style.display = "none";
	this._changeView.style.top = "0px";
	this._changeView.style.right = "0px";
	this.addEvents();

	this._wrapper.appendChild(this._changeView);
}

chssCommentArea.prototype = {
		getWrapper: function()
		{
			return this._wrapper;
		},
		
		isVisible: function()
		{
			return this._visible;
		},
		
		setHeight: function(height, small, big)
		{
			if(typeof small !== "undefined")
				this._smallHeight = small;
			if(typeof big !== "undefined")
				this._bigHeight = big;
			if(typeof height === "undefined")
				this._currentSize = chssCommentArea.SMALL;
			else
				this._currentSize = height;

			var isBreak = this._label.style.display != "none";
			
			this._wrapper.style.height = (this._currentSize==chssCommentArea.BIG?this._bigHeight:this._smallHeight) + "px";
			this._textWrapper.style.height = parseFloat(this._wrapper.style.height) - (isBreak?this._label.offsetHeight:this._menu.offsetHeight) - (6 * (chssOptions.board_size/360)) + "px";
			
			if(this._currentSize == chssCommentArea.SMALL)
			{
				this._changeView.innerHTML = "+";
				this._changeView.title = chssLanguage.translate(1425);
			}
			else
			{
				this._changeView.innerHTML = "&ndash;";
				this._changeView.title = chssLanguage.translate(1426);
			}
		},
		
		draw: function()
		{
			var paddingHor1 = 10 * (chssOptions.moves_size/200),
				paddingHor2 = 20 * (chssOptions.moves_size/200),
				paddingVer = 3 * (chssOptions.board_size/360),
				paddingVer2 = 2 * (chssOptions.board_size/360),
				fontSize = 16 * (chssOptions.board_size/360),
				border = 1 * (chssOptions.board_size/360),
				fontSize2 = 11 * (chssOptions.board_size/360),
				extra = 4 * (chssOptions.board_size/360);
			
			this._creatorComment.setFontSize(fontSize + "px");
			this._otherComments.setFontSize(fontSize + "px");

			this._menu.style.padding = "0px 0px " + paddingVer + "px 0px";
			
			this._label.style.fontSize = fontSize + "px";
			this._label.style.margin = -paddingVer2 + "px " + paddingHor1 + "px " + paddingVer2*2 + "px";
			this._label.style.padding = paddingVer + "px 0px";
			
			this._textArea.style.fontSize = fontSize + "px";
			this._textWrapper.style.margin = "0px " + paddingHor1 + "px " + "0px " + paddingHor2 + "px";
			
			this._changeView.style.border = border + "px solid " + chssOptions.select_color;
			this._changeView.style.fontSize = fontSize2 + "px";
			this._changeView.style.height = fontSize2 + extra + "px";
			this._changeView.style.width = fontSize2 + extra + "px";
		},
		
		resize: function(diffCoeff)
		{
			if(typeof this._smallHeight !== "undefined")
				this._smallHeight = this._smallHeight * diffCoeff;
			if(typeof this._bigHeight !== "undefined")
				this._bigHeight = this._bigHeight * diffCoeff;
			this.setHeight(this._currentSize);
			this.draw();
		},
		
		changeLayout: function()
		{
			var update_right = false,
				user_id = 0,
				isBreak = chssBoard.moduleManager.getVariableForCommentArea(chssCommentArea.IS_BREAK),
				comment = chssBoard.moduleManager.getVariableForCommentArea(chssCommentArea.GET_COMMENT),
				comments = this.getComments(),
				max_width = parseFloat(this._wrapper.style.width) - (25 * (chssOptions.moves_size/200)) - 24;
			
			this._comment_write = "";
			this._comments_string = "";
			this._textArea.innerHTML = "";
			
			if(isBreak)
			{
				this._menu.style.display = "none";
				this._label.style.display = "block";
			}
			else
			{
				this._label.style.display = "none";
				this._menu.style.display = "block";
			}
			
			this.switchCommentView(this._edit);
			
			var comments_to_remove = new Array();
			for(var i=0; i<comments.length; i++)
			{
				var comment_temp = comments[i];
				if((comment_temp.getUserId() == 0 || (comment_temp.getUserId() == user_id && this._edit)) && !isBreak)
				{
					if(comment_temp.getUserId() == 0)
						comment = chssHelper.wordWrap(this._textArea, comment_temp.getComment(), max_width, false, false);
					else if(comment_temp.getUserId() == user_id)
						this._comment_write = chssHelper.wordWrap(this._textArea, comment_temp.getComment(), max_width, false, false);
					comments_to_remove.push(i);
				}
				else if(!isBreak)
				{
					var name = comment_temp.getUsername();
					if(comment_temp.getUserId() == -1)
						name = chssLanguage.translate(1335);
					if(this._comments_string != "")
						this._comments_string += "<br style='line-height: 0.5em;'/>";
					this._comments_string += "<div>" + chssHelper.wordWrap(this._textArea, comment_temp.getComment(), max_width, false, false) + "</div>";
					this._comments_string += "<div style='padding-left: 2em; font-size: 0.6em;'>" + chssLanguage.translate(1331).replace("%s", name) + "</div>";
				}
			}
			for(var i=0;i<comments_to_remove.length;i++)
				comments = chssHelper.array_removeAt(comments, comments_to_remove[i]-i);
			
			//var removed_yc = this._yourComment.getWrapper().style.display == "none";
			/*
			if((!this._edit || update_right) && !removed_yc)
			{
				this._yourComment.getWrapper().style.display = "none";
			}
			else if(this.edit && !update_right && removed_yc)
			{
				this._yourComment.getWrapper().style.display != "block";
			}
			*/
			
			if(comment == "")
			{
				this._creatorComment.getWrapper().style.display = "none";
				this._otherComments.setText(chssLanguage.translate(1332));
				this._currentView = chssCommentArea.OTHER_COMMENTS;
			}
			else
			{
				this._creatorComment.getWrapper().style.display = "block";
				this._otherComments.setText(chssLanguage.translate(1427));
			}

			if(comments.length == 0)
			{
				this._otherComments.getWrapper().style.display = "none";
				this._currentView = chssCommentArea.CREATORS_COMMENT;
			}
			else if(comments.length > 0)
			{
				this._otherComments.getWrapper().style.display = "block";
			}
			
			var breakHeight = isBreak?this._label.offsetHeight:this._menu.offsetHeight;
			
			this._visible = !(this._edit == false && this._comments_string == "" && comment == "" || this._wrapper.style.display == "none");
			this._subWrapper.style.display = this._visible?"block":"none";
			this._textWrapper.style.height = parseFloat(this._wrapper.style.height) - breakHeight - (6 * (chssOptions.board_size/360)) + "px";
			
			this._comment = comment;
			this._comments = comments;

			this.changeCommentArea(this._currentView)
		},
		
		changeCommentArea: function(view)
		{
			var update_right = false,
				html = "";
			
			this._currentView = view;
			this.drawMenu();
			switch(view)
			{
				case chssCommentArea.CREATORS_COMMENT:
					this.switchCommentView(update_right);
					html = this._comment;
					break;
				case chssCommentArea.OTHER_COMMENTS:
					this.switchCommentView(false);
					html = this._comments_string;
					break;
				case chssCommentArea.YOUR_COMMENT:
					this.switchCommentView(true);
					html = this._comment_write;
					break;
			}

			this._textArea.innerHTML = html;
			if(this._textWrapper.offsetHeight<this._textArea.offsetHeight)
			{
				this._textWrapper.style.marginRight = "0px";
				this._textWrapper.style.width = parseFloat(this._wrapper.style.width) - (20 * (chssOptions.moves_size/200)) + "px";
				if(typeof this._bigHeight !== "undefined")
					this._changeView.style.display = "block";
			}
			else
			{
				this._textWrapper.style.marginRight = "24px";
				this._textWrapper.style.width = parseFloat(this._wrapper.style.width) - (20 * (chssOptions.moves_size/200)) - 24 + "px";
				if(this._currentSize != chssCommentArea.BIG)
					this._changeView.style.display = "none";
			}
		},
		
		switchCommentView: function(enabled)
		{
			/*
			if(!enabled)
			{
				this.text_comment.visible = false;
				this.text_comment.includeInLayout = false;
				this.edit_view.visible = false;
				this.edit_view.includeInLayout = false;
				this.text_comment_disabled.visible = true;
				this.text_comment_disabled.includeInLayout = true;
				this.edit_view_disabled.visible = true;
				this.edit_view_disabled.includeInLayout = true;
				this.edit_view_disabled.height = this.height - this.tab.height - 8;
			}
			else
			{
				this.text_comment_disabled.visible = false;
				this.text_comment_disabled.includeInLayout = false;
				this.edit_view_disabled.visible = false;
				this.edit_view_disabled.includeInLayout = false;
				this.text_comment.visible = true;
				this.text_comment.includeInLayout = true;
				this.edit_view.visible = true;
				this.edit_view.includeInLayout = true;
			}
			*/
		},
		
		drawMenu: function()
		{
			this._creatorComment.selected(this._currentView == chssCommentArea.CREATORS_COMMENT);
			this._otherComments.selected(this._currentView == chssCommentArea.OTHER_COMMENTS);
		},
		
		getComments: function()
		{
			var comments = new Array(),
				currentComments = chssBoard.moduleManager.getVariableForCommentArea(chssCommentArea.GET_COMMENTS);
			for(var i=0; i<currentComments.length; i++)
				comments.push(new chssComment(currentComments[i].getUserId(), currentComments[i].getComment(), currentComments[i].getUsername()));
			return comments;
		},
		
		getSize: function()
		{
			return this._currentSize;
		},
		
		addEvents: function()
		{
			var obj = this;
			
			this._creatorComment.getWrapper().onclick = function()
			{
				if(!obj._creatorComment.selected())
					obj.changeCommentArea(chssCommentArea.CREATORS_COMMENT);
			}
			
			this._otherComments.getWrapper().onclick = function()
			{
				if(!obj._otherComments.selected())
					obj.changeCommentArea(chssCommentArea.OTHER_COMMENTS);
			}

			this._changeView.onclick = function()
			{ 
				var size = obj.getSize();
				if(size == chssCommentArea.SMALL)
					obj.setHeight(chssCommentArea.BIG);
				else if(size == chssCommentArea.BIG)
					obj.setHeight(chssCommentArea.SMALL);
				obj.changeLayout(obj._currentView);
			}
		}
}