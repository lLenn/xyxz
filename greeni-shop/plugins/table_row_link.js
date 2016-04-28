$(function()
{
	$.row_style = 
	{
		defaults: { backgroundColorHover: '#9abfb0' }
	}
	
	$.fn.table_row_link = function(options)
		{
			options = $.extend($.row_style.defaults, options);
			$(this).each(function()
			{
				var elem = $(this);
				var prev_class;
				var id = elem.attr("class").split(" ").slice(-1);
				var parent = elem.parent().parent();
				var location = parent.attr("class").split(" ").slice(-1);
				
				var classes = elem.attr('class').split(" ")
				var index_class = $.inArray("even", classes);
				if(index_class == -1)
					index_class = $.inArray("odd", classes);
	
				elem.css("cursor", "pointer");
				elem.hover(function()
						{
							classes = elem.attr('class').split(" ");
							prev_class = classes[index_class]
							elem.css('class', classes.join(" "));
							elem.css('background', options.backgroundColorHover);
						});
				elem.mouseleave(function()
						{
							classes = elem.attr('class').split(" ");
							classes[index_class] = prev_class;
							elem.attr('class', classes.join(" "));
							var new_styles = new Array();
							var styles = elem.attr('style').split(";");
							$.each(styles, function(index, value)
							{
								var style = $.trim(value.split(":")[0]);
								if(style != "background" && style != "BACKGROUND")
								{
									new_styles.push(value);
								}
							});
							elem.attr('style', new_styles.join(";"));
						});
				
				$.each(elem.children(), function(index, child) 
						{
							var child = $(child);
							child.click(function()
							{
								if(child.attr("class") != "tool_btn")
									window.location = location + "&id=" + id;
							});
						});
			});
		}
});