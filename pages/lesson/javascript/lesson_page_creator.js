var created_object_id = 0;

function object_added(id)
{
	var type_id = parseInt(jQuery("select[name='type_id']").val());
	var arr = jQuery("#lesson_page_creator_form").serialize();
	arr += "&created_object_id=" + id;
	created_object_id = id;
	if(type_id!=3 && type_id!=6)
	{
		jQuery.post("pages/lesson/ajax/retrieve_search_holder.ajax.php", 
			arr, 
			function(data) 
	    	{
				jQuery("#create_object_button").css("display", "block");
				jQuery("#create_object_holder").empty();
				jQuery("#create_object_holder").css("display", "none");
				jQuery("#search_holder").empty();
				jQuery("#search_holder").append(data);
				jQuery("#search_holder").css("display", "block");
	        }
		);
	}
	else
	{	
		jQuery.post("pages/lesson/ajax/retrieve_second_create_object_form.ajax.php", 
			arr, 
			function(data) 
	    	{
				jQuery("#create_object_holder").empty();
				jQuery("#create_object_holder").append(data);
				switch(type_id)
				{
					case 3:
					case 6: setup_tinyMCE();
							break;
				}
	    	}
		);
	}
}
	
jQuery(function() 
{
	function retrieve_type_form(event)
	{
		var elem = jQuery(this),
			type_id = parseInt(elem.val());
		
        if(type_id == current_type_id)
                return;

		jQuery.post("pages/lesson/ajax/retrieve_type_form.ajax.php",
					{ type_id : type_id },
					function(data) 
		        	{
						jQuery("#type_form").empty();
						jQuery("#type_form").append(data);
						jQuery("#submit_form").css("display", "block");
						current_type_id = type_id;
						switch(type_id)
						{
							case 1: setup_tinyMCE();
									break;
						}
		            });
	}
	
	var start = 0;
	function submit_more(event)
	{
		var arr = jQuery("#lesson_page_creator_form").serialize();
		arr += "&start=" + start;
		jQuery.post("pages/lesson/ajax/retrieve_more_results.ajax.php", 
			arr, 
			function(data) 
        	{
				jQuery("#more_puzzles_block").remove();
				jQuery("#more_puzzles_record").append(data);
				start += 20;
            }
		);
	}

	function submit_search_form(event)
	{
		jQuery.post("pages/lesson/ajax/retrieve_form_result.ajax.php", 
			jQuery("#lesson_page_creator_form").serialize(), 
			function(data) 
        	{
				jQuery("#search_form").css("display", "none");
				jQuery("#search_result").empty();
				jQuery("#search_result").append(data);
				jQuery("#search_result").css("display", "block");
				start = 20;
            }
		);
	}

	function search_again(event)
	{
		jQuery("#search_form").css("display", "block");
		jQuery("#search_result").css("display", "none");
	}

	function cancel_create_object(event)
	{
		jQuery("#create_object_button").css("display", "block");
		jQuery("#create_object_holder").empty();
		jQuery("#create_object_holder").css("display", "none");
		jQuery("#search_holder").css("display", "block");
	}
	
	function create_object(event)
	{
		jQuery.post("pages/lesson/ajax/retrieve_create_object_form.ajax.php", 
				jQuery("#lesson_page_creator_form").serialize(), 
				function(data) 
	        	{
					jQuery("#create_object_button").css("display", "none");
					jQuery("#create_object_holder").empty();
					jQuery("#create_object_holder").append(data);
					jQuery("#create_object_holder").css("display", "block");
					jQuery("#search_holder").css("display", "none");
	            }
			);
	}
	
	function submit_second_create_object_form(event)
	{
		var type_id = parseInt(jQuery("select[name='type_id']").val());
		switch(type_id)
		{
			case 3:
			case 6: jQuery("textarea[name='description_lesson']").val(tinyMCE.get('description_lesson').getContent());
					break;
		}
		var arr = jQuery("#lesson_page_creator_form").serialize();
		arr += "&created_object_id=" + created_object_id;
		jQuery.post("pages/lesson/ajax/retrieve_second_create_object_form.ajax.php", 
			arr, 
			function(data) 
		   	{
				if(parseInt(data)!=created_object_id)
				{
					jQuery("#create_object_holder").empty();
					jQuery("#create_object_holder").append(data);
					switch(type_id)
					{
						case 3:
						case 6: setup_tinyMCE();
								break;
					}
				}
				else
				{
					jQuery.post("pages/lesson/ajax/retrieve_search_holder.ajax.php", 
						arr, 
						function(data) 
				    	{
							jQuery("#create_object_button").css("display", "block");
							jQuery("#create_object_holder").empty();
							jQuery("#create_object_holder").css("display", "none");
							jQuery("#search_holder").empty();
							jQuery("#search_holder").append(data);
							jQuery("#search_holder").css("display", "block");
				        }
					);
				}
		   	}
		);
	}
	
	jQuery(document).ready(function ()
	{
		jQuery("#type_form").on('click', "#submit_search_form", submit_search_form);
		jQuery("#type_form").on('click', "#search_puzzle_again", search_again);
		jQuery("#type_form").on('click', "#search_game_again", search_again);
		jQuery("#type_form").on('click', "#search_video_again", search_again);
		jQuery("#type_form").on('click', "#search_question_again", search_again);
		jQuery("#type_form").on('click', "#search_selection_again", search_again);
		if(navigator.userAgent.toLowerCase().indexOf('chrome') > -1)
		{
			jQuery("select[name|=type_id]").on('change', retrieve_type_form);
		}
		else
		{
			jQuery("select[name|=type_id]").on('click', retrieve_type_form);
		}
		jQuery("#type_form").on("click", "#more_puzzles", submit_more);
		jQuery("#type_form").on("click", "#create_object", create_object);
		jQuery("#type_form").on("click", "#cancel_create_object", cancel_create_object);
		jQuery("#type_form").on("click", "#submit_form_lesson", submit_second_create_object_form);
	});

});
