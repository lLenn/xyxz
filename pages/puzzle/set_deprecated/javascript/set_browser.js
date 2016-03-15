jQuery(function() 
{ 
	
	var set_puzzle = 0;
	
	function add_set_puzzles(event)
	{
		if(set_puzzle == 0)
		{
			jQuery.post("pages/puzzle/set/ajax/retrieve_puzzle_set_relation_form.ajax.php",{set_id: set_id},
					function(data) 
		        	{
						jQuery("#set_details_info").css("display", "none");
						jQuery("#set_puzzles").empty();
						jQuery("#set_puzzles").append(data);
						set_puzzle = 1;
		            }
				);
		}
		else if(set_puzzle == 1)
		{
			jQuery.post("pages/puzzle/set/ajax/add_puzzle_set_relations.ajax.php",jQuery("#set_relation_form").serialize(),
					function(data) 
		        	{
						jQuery("#set_details_info").css("display", "inline");
						jQuery("#set_puzzles").empty();
						jQuery("#set_puzzles").append(data);
						jQuery(".remove_puzzle_set_relation").css('cursor', 'pointer');
						set_puzzle = 0;
						jQuery().load_messages();
		            }
				);
		}
	}

	var start = 0;
	var shop = 0;
	function submit_search_form(event)
	{
		var arr = jQuery("#set_search_form").serialize();
		arr += "&shop=" + shop + "&start=0";
		jQuery.post("pages/puzzle/set/ajax/retrieve_table_sets.ajax.php", 
			arr, 
			function(data) 
        	{
				jQuery("#search_table").empty();
				jQuery("#search_table").append(data);
				start = 20;
            }
		);
	}

	function submit_set_shop(event)
	{
		var arr = jQuery("#set_search_form").serialize();
		arr += "&shop=" + shop + "&start=" + start;
		jQuery.post("pages/puzzle/set/ajax/retrieve_table_sets.ajax.php", 
			arr, 
			function(data) 
        	{
				jQuery("#more_sets_block").remove();
				jQuery("#more_sets_record").append(data);
				start += 20;
            }
		);
	}
	
	function remove_puzzle_set_relation(event)
	{
		var parent = jQuery(this).parent().parent(),
			puzzle_id = parseInt(parent.children(":first").text());
		jQuery.post("pages/puzzle/set/ajax/remove_puzzle_set_relation.ajax.php", 
			{ set_id: set_id,
			  puzzle_id: puzzle_id}, 
			function(data) 
        	{
				jQuery("#set_puzzles").empty();
				jQuery("#set_puzzles").append(data);
				jQuery(".remove_puzzle_set_relation").css('cursor', 'pointer');
				jQuery().load_messages();
            }
		);
	}
	
	jQuery.fn.get_puzzle_images = function()
	{
		return this.each(function()
				{
					var elem = jQuery(this),
						position = elem.offset();
					
					if(elem.find(".puzzle_image").length == 0)
					{
						var	img = jQuery(new Image()),
							puzzle_id =  parseInt(elem.children(":first").text());
						
						img.attr("src", "pages/puzzle/ajax/retrieve_puzzle_image.ajax.php?puzzle_id=" + puzzle_id);
						img.attr("class", "puzzle_image")
						img.css("position", "absolute");
						img.css("margin", 0);
						img.css("padding", 0);
						elem.css("cursor", "pointer");
					}
					else
						var img = elem.find(".puzzle_image");
					
						img.css("left", position.left - 1);
						img.css("top", position.top + 23);
						img.css("display", "none");

					if(elem.find(".puzzle_image").length == 0)
					{	

						var classes = elem.attr('class').split(" ")
						var index_class = $.inArray("even", classes);
						if(index_class == -1)
						{
							index_class = $.inArray("odd", classes);
						}
						
						elem.append(img);
						elem.hover(function()
								{
									img.css('display', 'block');
									classes = elem.attr('class').split(" ");
									prev_class = classes[index_class]
									elem.css('class', classes.join(" "));
									elem.css('background', table_color);
								});
						elem.mouseleave(function()
								{
									img.css('display', 'none');
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
					}
					
				});
	}
	
	jQuery(document).ready(function ()
	{		
		jQuery(".puzzle_image_row").get_puzzle_images();
		jQuery("#submit_search_form").on('click', submit_search_form);
		jQuery("#add_set_puzzles").on('click', add_set_puzzles);
		jQuery("#submit_rel_form").on('click', add_set_puzzles);
		jQuery(".remove_puzzle_set_relation").on('click', remove_puzzle_set_relation);
		if(jQuery(".remove_puzzle_set_relation").length > 0)
			jQuery(".remove_puzzle_set_relation").css('cursor', 'pointer');
		jQuery("#more_sets").on("click", submit_set_shop);
	});

});
