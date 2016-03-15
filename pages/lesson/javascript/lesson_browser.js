jQuery(function() 
{
	var start = 0;
	function submit_search_form(event)
	{
		start = 0;
		jQuery("#more_lessons_search").empty();
		submit_lesson_shop();
	}
	
	function submit_lesson_shop(event)
	{
		var arr = jQuery("#lesson_search_form").serialize();
		arr += "&start=" + start;
		jQuery.post("pages/lesson/ajax/retrieve_shop_lessons.ajax.php", 
			arr, 
			function(data) 
        	{
				if(start!=0)
				{
					jQuery("#more_lessons_block").remove();
					jQuery("#more_lessons_record").append(data);
				}
				else
				{
					jQuery("#more_lessons_search").append(data);
				}
				start += 20;
            }
		);
	}
	
	function show_details_buttons()
	{
		jQuery(".general").each(function(){jQuery(this).css("display", "none");});
		jQuery(".details").each(function(){jQuery(this).css("display", "block");});
	}
	
	function show_general_buttons()
	{
		jQuery(".details").each(function(){jQuery(this).css("display", "none");});
		jQuery(".general").each(function(){jQuery(this).css("display", "block");});
	}
	
	jQuery(document).ready(function ()
	{
		if(jQuery("#details_tab").length > 0)
		{
			jQuery("#details_tab").click(show_details_buttons);
			jQuery("#general_tab").click(show_general_buttons);
			if(tabnumber == 0)
				show_details_buttons();
			else
				show_general_buttons();	
		}
		else
			show_general_buttons();
		
		jQuery("#submit_search_form").on('click', submit_search_form);
		jQuery("#more_lessons").on("click", submit_lesson_shop);
	});

});
