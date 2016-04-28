(function($)
{
	$.menuStyle = 
	{
		defaults: { backgroundColorHover: '#bbb' }
	}
	
	$.fn.menu = function (options)
	{
		options = $.extend($.menuStyle.defaults, options);
		
		var timers = new Object();
		var backgrounds = new Object();
		var set = new Object();
		
		$(this).each(function()
		{
			var id;
			do
			{
				id = String(Math.ceil(Math.random()*1000000000));
			}while(timers[id] == "Set");
			timers[id] = "Set";
			set[id] = false;
			set[id+"_hover"] = false;
			
			var elem = $(this);
			elem.attr("id", "menu_" + id);
			
			$("#menu_" + id).children("li").click(function()
					{
						window.location = $(this).children("a:first").attr("href");
					});
			$("#menu_" + id).children("li").css("cursor", "pointer");
			$("#menu_" + id + " .sub_menu").children("li").click(function()
					{
						window.location = $(this).children("a:first").attr("href");
						return false;
					});
			$("#menu_" + id + " .sub_menu").children("li").css("cursor", "pointer");
			$("#menu_" + id).children("li").hover(function()
			{
				$(this).children("ul").each(function(index, value)
				{
					setTimeout('$("#menu_' + id + ' li ul").css("display", "block");', 500);
				});
				if(set[id] == false)
				{
					set[id] = true;
					backgrounds[id] = $(this).css("background-color");
				}
				$(this).css("background-color", options.backgroundColorHover);
			},
			function()
			{
				$(this).children("ul").each(function(index, value)
				{
					timers[id] = setTimeout('$("#menu_' + id + ' li ul").css("display", "none");', 500);
				});
				set[id] = false;
				$(this).css("background-color", backgrounds[id]);
			});
			$("#menu_" + id).children("li").mouseover(function(){clearTimeout(timers[id]);});
			$("#menu_" + id + " .sub_menu li").hover(function()
				{
					$(this).parent().css("display", "block");
					clearTimeout(timers[id]);
					if(set[id+"_hover"] == false)
					{
						set[id+"_hover"] = true;
						backgrounds[id+"_hover"] = $(this).css("background-color");
					}
					$(this).css("background-color", options.backgroundColorHover);
				},
				function()
				{
					timers[id] = setTimeout('sub_menu_hover = false; $("#menu_' + id + ' .sub_menu li").parent().css("display", "none");', 500);
					set[id+"_hover"] = false;
					$(this).css("background-color", backgrounds[id+"_hover"]);
				});
			$("#menu_" + id + " .sub_menu li").each(function(index, value)
				{
					$(value).css("top", 25*index);
				});
		});
	};
})(jQuery);