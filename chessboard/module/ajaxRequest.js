function chssAjaxRequest()
{
	this._ajaxConns = new Array();
	this._logout = false;
}

chssAjaxRequest.prototype = 
{
		loadData: function(method, page, vars, callback, object)
		{
			var parent = this,
				ajax = null;
			
			for(var i=0; i<this._ajaxConns.length;i++)	
			{
				if(this._ajaxConns[i].readyState == 4 || this._ajaxConns[i].readyState == 0)
				{
						ajax = this._ajaxConns[i];
						break;
				}
			}
			
			if(i==this._ajaxConns.length)
			{
				this.addConnection();
				ajax = this._ajaxConns[i];
			}
			
			if(chss_global_vars.local)
				vars += "&local=1";
			if(this._logout)
				vars += "&logout=1";

			if(method=="GET")
				page = page + "?" + vars;
			page = chssOptions.root_url + page;

			ajax.open(method, page, true);
			ajax.onreadystatechange = function(event)
			{
				  if(event.currentTarget.readyState == 4 && event.currentTarget.status == 200)
				  {
					  //console.log(event.currentTarget.responseText);
					  try
					  {
						  var data = JSON.parse(event.currentTarget.responseText);
						  if(!data.error)
						  {
							  callback.call(object, data);
						  }
						  else
						  {
							  if(chssHelper.isNumeric(data.error))
								  console.log(chssLanguage.translate(data.error));
							  else
								  console.log("Ajax error: " + data.error);

							  if(chss_global_vars.local)
							  {
								  chssBoard.moduleManager._module = null;
								  chssBoard.board.loadComplete();
								  chssBoard.board._engine.getEngineElement().style.display = "block";
								  chssBoard.board._engine.getEngineElement().style.position = "absolute";
								  chssBoard.board._engine.getEngineElement().style.top = chssBoard.board._wrapper.offsetHeight + "px";
								  chssBoard.board._wrapper.style.overflow = "visible";
							  }
						  }
					  }
					  catch(e)
					  {
						  if(chss_global_vars.local)
						  {
							  chssBoard.moduleManager._module = null;
							  chssBoard.board.loadComplete();
							  chssBoard.board._engine.getEngineElement().style.display = "block";
							  chssBoard.board._engine.getEngineElement().style.position = "absolute";
							  chssBoard.board._engine.getEngineElement().style.top = chssBoard.board._wrapper.offsetHeight + "px";
							  chssBoard.board._wrapper.style.overflow = "visible";
						  }
					  }
					  parent._ajaxConns = chssHelper.array_removeAt(parent._ajaxConns, i);
				  }
			}
			if(method=="POST")
			{
				ajax.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
				ajax.send(vars);
			}
			else
				ajax.send();
		},
		
		addConnection: function()
		{
			if(window.XMLHttpRequest)
				this._ajaxConns.push(new XMLHttpRequest());
			else
				this._ajaxConns.push(new ActiveXObject("microsoft.XMLHTTP"));
		},
		
		setLogout: function(logout)
		{
			this._logout = logout;
		}
}