jQuery(function()
{
	jQuery.row_style = 
	{
		defaults: { backgroundColorHover: '#9abfb0' }
	}
	
	jQuery.fn.table_row_link = function(options)
	{
		options = jQuery.extend(jQuery.row_style.defaults, options);  
		jQuery(this).each(function()
		{
			var elem = jQuery(this);
            var prev_class = "even"; 
			var id_string = String(elem.attr("class").split(" ").slice(-1));
			var ids = id_string.split("&");
			var parent = elem.parent().parent();
			var location = "index.php?page=" + parent.attr("class").split(" ").slice(-2)[0];
			var action_string = String(parent.attr("class").split(" ").slice(-1));
			var actions = action_string.split("&");
				
            var classes = elem.attr('class').split(" ");
            var index_class = jQuery.inArray("even", classes);    
            if(index_class == -1)
            {
                index_class = jQuery.inArray("odd", classes);
                prev_class = "odd";
            }
                               
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
						jQuery.each(styles, function(index, value)
						{
							var style = jQuery.trim(value.split(":")[0]);
							if(style != "background" && style != "BACKGROUND")
							{
								new_styles.push(value);
							}
						});
						elem.attr('style', new_styles.join(";"));
					});
			
			jQuery.each(elem.children(), function(index, child) 
					{
						var child = jQuery(child);
						child.click(function()
						{
							if(child.attr("class").split(" ")[0] != "tool_btn" && child.attr("class").split(" ")[0] != "edit_cell")
							{
								var url = "";
								jQuery.each(actions, function(index, child)
								{
									url = url + "&" + child + "=" + ids[index];
								});
								url = url.substr(1);
								window.location = location + "&" + url;
							}
							else
							{
								if(child.attr("class").split(" ")[1] == "delete")
								{
									var delete_message = child.attr("class").split(" ").slice(4).join(" ");
									confirm(delete_message)
									{
										var url = "";
										var delete_string = String(child.attr("class").split(" ")[2]);
										var delete_actions = delete_string.split("&");
										jQuery.each(delete_actions, function(index, child)
										{
											url = url + "&" + child + "=" + ids[index];
										});
										url = url.substr(1);
										window.location = "index.php?page=" + child.attr("class").split(" ")[3] + "&" + url;
									}
								}
							}
						});
					});
		});
	}

	function change_length()
	{
		var elem = jQuery(this);
		add_arg("limit", elem.val());
	}
	
	function change_start()
	{
		var elem = jQuery(this);
		add_arg("start", elem.attr("class").split(" ")[1]);
	}
	
	jQuery(document).ready(function()
	{		
		if(jQuery(".row_link").length > 0)
		{
			jQuery(".row_link").table_row_link();
		}
		
		if(jQuery("select[name='limit']").length > 0)
		{
			jQuery("select[name='limit']").live("change", change_length);
		}
		
		if(jQuery(".change_start").length > 0)
		{
			jQuery(".change_start").live("click", change_start);
		}
	});
});