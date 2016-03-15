jQuery(function() 
{
	function add_user(event)
	{
		var elem = jQuery(this);
		var id_string = String(elem.attr("class").split(" ").slice(-1));
		window.location = "index.php?page=add_member&prev=manage&parent_id=" + id_string;
	}
	
	function edit_user(event)
	{
		var elem = jQuery(this);
		var id_string = String(elem.attr("class").split(" ").slice(-1));
		window.location = "index.php?page=edit_member&prev=manage&id=" + id_string;
	}
	
	function remove_user(event)
	{
		var elem = jQuery(this);
		var id_string = String(elem.attr("class").split(" ").slice(-1));

		jQuery.post("pages/user/ajax/remove_user.ajax.php",
					{ user_id : id_string },
					function(data) 
		        	{
						if(data == "")
						{
							elem.parent().parent().remove();
						}
		            });
	}
	
	var start_div = 0;
	function start_drag(event, ui)
	{
		var main_elem = jQuery(this),
			main_y = event.pageY,
			main_x = event.pageX,
			y = main_elem.offset().top,
			x = main_elem.offset().left,
			helper = ui.helper;
		//alert(y);
		//alert(x);
		main_elem.css("z-index", "100");
		//alert(navigator.userAgent.match(/webkit/i));
		//if(!navigator.userAgent.match(/webkit/i))
		main_elem.css("display", "none");
		
		//helper.offset.top = y - main_y;
		//helper.offset.left = x;
		jQuery(".drop_zone").each(function()
				{
					var elem = jQuery(this),
						y = elem.offset().top,
						x = elem.offset().left;
					if(main_y > y && main_x > x && 
					   main_y < y + drop_zone_height && main_x < x + 216)
					{
						start_div = String(elem.attr("class").split(" ").slice(-1));
					}
				});
	}
	
	function stop_drag(event)
	{
		var main_elem = jQuery(this),
			main_y = event.pageY,
			main_x = event.pageX,
			stop_added = false,
			end_div = start_div,
			user_id = String(main_elem.attr("class").split(" ")[1]);
		main_elem.css("z-index", "auto");
		main_elem.css("display", "block");
		main_elem.css("top", "0px");
		main_elem.css("left", "0px");
		if(added)
		{
			temp_div.remove();
			added = false;
		}
		var children = 0;
		jQuery(".drop_zone").each(function()
				{
					var elem = jQuery(this),
						y = elem.offset().top,
						x = elem.offset().left;
					elem.css("margin", "20px 3px 3px 3px");
					elem.css("border", "none");
					if(main_y > y && main_x > x && 
					   main_y < y + drop_zone_height && main_x < x + 216)
					{
						allowed = elem.children(":nth-child(1)").attr("class").split(" ")[1];
						if(elem.children().length-1<allowed || allowed == -1)
						{
							stop_added = true;
							end_div = String(elem.attr("class").split(" ").slice(-1));
							elem.append(main_elem);
						}
					}
					count = elem.children().length;
					if(count>children)
						children = count;
				});
		jQuery(".drop_zone").css("height", children*26);
		drop_zone_height = children*26;
		if(!stop_added)
		{
			jQuery(".drop_zone." + start_div).append(main_elem);
		}
		else
		{
			jQuery.post("pages/user/ajax/change_parent_user.ajax.php",
				{ 	old_parent: start_div,
					new_parent: end_div,
					user_id : user_id },
				function(data) 
	        	{
					if(data != "")
					{
						jQuery("#manage_div").empty();
						jQuery("#manage_div").append(data);
						jQuery(".add_user").css('cursor', 'pointer');
						jQuery(".edit_user").css('cursor', 'pointer');
						jQuery(".remove_user").css('cursor', 'pointer');
						jQuery(".draggable").draggable({
							   helper: 'clone',
							   start: start_drag,
							   drag: dragging,
							   stop: stop_drag
						});
						jQuery(".draggable").css('cursor', 'move');
					}
	            });
			
			if(jQuery(".drop_zone." + start_div).children().length==1)
			{
				var delete_img = jQuery(document.createElement('img'));
				delete_img.attr("src", "layout/images/buttons/delete.png");
				delete_img.attr("class", "remove_user " + start_div);
				delete_img.css('cursor', 'pointer');
				jQuery(".drop_zone." + start_div).children(":nth-child(1)").children(":nth-child(2)").append(delete_img);
			}
			else
			{
				jQuery(".drop_zone." + end_div).children(":nth-child(1)").children(":nth-child(2)").children(".remove_user").remove();
			}
			
			allowed = jQuery(".drop_zone." + start_div).children(":nth-child(1)").attr("class").split(" ")[1];
			if(jQuery(".drop_zone." + start_div).children().length-1 < allowed && jQuery(".drop_zone." + start_div).children(":nth-child(1)").children(":nth-child(2)").children(".add_user").length == 0)
			{
				var add_img = jQuery(document.createElement('img'));
				add_img.attr("src", "layout/images/buttons/add.png");
				add_img.attr("class", "add_user " + start_div);
				add_img.css('cursor', 'pointer');
				jQuery(".drop_zone." + start_div).children(":nth-child(1)").children(":nth-child(2)").prepend(add_img);
			}
			
			allowed = jQuery(".drop_zone." + end_div).children(":nth-child(1)").attr("class").split(" ")[1];
			if(jQuery(".drop_zone." + end_div).children().length-1 >= allowed && allowed != -1)
			{
				jQuery(".drop_zone." + end_div).children(":nth-child(1)").children(":nth-child(2)").children(".add_user").remove();			
			}
		}
	}
	
	var added = false;
	var added_id = 0;
	var temp_div;
	function dragging(event)
	{
		var main_elem = jQuery(this),
			main_y = event.pageY,
			main_x = event.pageX,
			children = 0;
		jQuery(".drop_zone").each(function()
				{
					var elem = jQuery(this),
						y = elem.offset().top,
						x = elem.offset().left,
						id = String(elem.attr("class").split(" ").slice(-1));
					if(main_y > y && main_x > x && 
					   main_y < y + drop_zone_height && main_x < x + 216)
					{
						allowed = elem.children(":nth-child(1)").attr("class").split(" ")[1];
						if(elem.children().length-1 < allowed || allowed == -1)
						{
							elem.css("margin", "17px 0px 0px 0px");
							elem.css("border", "3px dashed #96BF0D");
							if(!added)
							{
								temp_div = jQuery(document.createElement('div'));
								temp_div.attr("class", "temp");
								elem.append(temp_div);
								added = true;
								added_id = id;
							}
							else if(id != added_id)
							{
								temp_div.remove();
								added = false;
							}
						}
						else if(id != added_id)
						{
							elem.css("margin", "17px 0px 0px 0px");
							elem.css("border", "3px dashed #ED1C24");
							if(added)
							{
								temp_div.remove();
								added = false;
							}
						}
					}
					else
					{
						elem.css("margin", "20px 3px 3px 3px");
						elem.css("border", "none");
					}
					count = elem.children().length;
					if(count>children)
						children = count;
				});
		jQuery(".drop_zone").css("height", children*26);
		drop_zone_height = children*26;
	}
	
	jQuery(document).ready(function ()
	{		
		jQuery(".add_user").css('cursor', 'pointer');
		jQuery("#manage_div").on('click', ".add_user", add_user);
		jQuery(".edit_user").css('cursor', 'pointer');
		jQuery("#manage_div").on('click', ".edit_user", edit_user);
		jQuery(".remove_user").css('cursor', 'pointer');
		jQuery("#manage_div").on('click', ".remove_user", remove_user);
		jQuery(".draggable").draggable({
			   helper: 'clone',
			   start: start_drag,
			   drag: dragging,
			   stop: stop_drag
		});
		jQuery(".draggable").css('cursor', 'move');
	});

});
