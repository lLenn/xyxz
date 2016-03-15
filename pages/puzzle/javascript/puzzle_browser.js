jQuery(function() 
{ 
	jQuery.fn.get_puzzle_thumbs = function()
	{
		return this.each(function()
				{
					var elem = jQuery(this),
						position = elem.offset(),
						puzzle_id =  parseInt(elem.children(":first").text()),
						without = elem.attr("class").split(" ")[1] == "without";
					
					if(elem.find(".puzzle_image").length == 0)
					{
						var	img = jQuery(new Image());
						var setup_only = "";
						if(without)
							setup_only = "&setup_only=1";
						if(elem.attr("class").split(" ").slice(-3)[0] == "create")							
							img.attr("src", "pages/puzzle/ajax/retrieve_puzzle_image.ajax.php?puzzle_id=" + puzzle_id + setup_only);
						else
							img.attr("src", "pages/puzzle/images/" + puzzle_id + ".gif");
						img.attr("class", "puzzle_image");
						img.css("position", "absolute");
						img.css("margin", 0);
						img.css("padding", 0);
						elem.css("cursor", "pointer");
					}
					else
						var img = elem.find(".puzzle_image");
					
						img.css("left", position.left);
						img.css("top", position.top + 23);
						img.css("display", "none");
					

					if(elem.find(".puzzle_image").length == 0)
					{	
						elem.append(img);
						elem.hover(function()
								{
									img.css("display", "block");
									elem.css("background", table_color);
								});
						elem.mouseleave(function()
								{
									img.css("display", "none");
									if(jQuery(this).attr("class").split(" ").slice(-1) == "even")
									{
										elem.css("background", "none");
									}
									else
									{
										elem.css("background", table_odd_color);
									}
								});
						var location = root_url + "index.php?page=browse_puzzles";
						if(without)
						{
							location = root_url + "index.php?page=add_puzzle";
						}
						jQuery.each(elem.children(), function(index, child) 
								{
									var child = jQuery(child);
									child.click(function()
									{
										if(typeof(child.attr("class")) == "undefined" || child.attr("class").split(" ")[0] != "tool_btn")
										{
											window.location = location + "&id=" + puzzle_id;
										}
										else if(child.attr("class") == "tool_btn delete")
										{
											if(confirm(delete_message))
											{
												child.empty();
												child.append("Removing ...");
												jQuery.post("pages/puzzle/ajax/remove_puzzle.ajax.php", 
														{ puzzle_id: puzzle_id}, 
														function(data) 
														{
															window.location = root_url + "index.php?page=browse_puzzles" + data;
														}
												);
											}
										}
										else if(child.attr("class") == "tool_btn edit")
										{
											window.location = root_url + "index.php?page=add_puzzle&id=" + puzzle_id;
										}
									});
								});
					}
					
				});
	}
	
	var start = 0;
	function submit_search_form(event)
	{
		var arr = jQuery("#puzzle_search_form").serialize();
		arr += "&shop=" + shop + "&start=0&search=1";
		jQuery.post("pages/puzzle/ajax/retrieve_table_puzzles.ajax.php", 
			arr, 
			function(data) 
        	{
				jQuery("#search_table").empty();
				jQuery("#search_table").append(data);
				if(!shop)
				{
					jQuery(".puzzle_link").get_puzzle_thumbs();
					jQuery("#explorer").click(function(){
						jQuery(".puzzle_link").get_puzzle_thumbs();
					});
				}
				start = 20;
            }
		);
	}
	
	function get_page(event)
	{
		var search = jQuery("#page_search").val();
		var page = jQuery(this).text();
		var start = jQuery("#page_start_" + page).val();
		var arr = jQuery("#puzzle_search_form").serialize();
		arr += "&shop=0&start=" + start + "&search=" + search;
		jQuery.post("pages/puzzle/ajax/retrieve_table_puzzles.ajax.php", 
			arr, 
			function(data) 
        	{
				jQuery("#search_table").empty();
				jQuery("#search_table").append(data);
				jQuery(".puzzle_link").get_puzzle_thumbs();
				jQuery("#explorer").click(function(){
					jQuery(".puzzle_link").get_puzzle_thumbs();
				});
            }
		);
	}

	function submit_puzzle_shop(event)
	{
		var arr = jQuery("#puzzle_search_form").serialize();
		arr += "&shop=" + shop + "&start=" + start;
		jQuery.post("pages/puzzle/ajax/retrieve_table_puzzles.ajax.php", 
			arr, 
			function(data) 
        	{
				jQuery("#more_puzzles_block").remove();
				jQuery("#more_puzzles_record").append(data);
				start += 20;
            }
		);
	}
	
	jQuery(document).ready(function ()
	{
		//alert("javascript loading");
		jQuery("#submit_search_form").on('click', submit_search_form);
		jQuery(".puzzle_link").get_puzzle_thumbs();
		jQuery("#explorer").click(function(){
			jQuery(".puzzle_link").get_puzzle_thumbs();
		});
		jQuery("#more_puzzles").on("click", submit_puzzle_shop);
		jQuery("#search_table").on('click', ".page_link_btn", get_page);
	});

});
