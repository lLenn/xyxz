function object_added(id)
{
	var arr = jQuery("#lesson_excercise_component_creator_form").serialize();
	arr += "&created_object_id=" + id;
	jQuery.post("pages/lesson/lesson_excercise/ajax/retrieve_search_holder.ajax.php", 
		arr, 
		function(data) 
    	{
			jQuery("#create_object_button").css("display", "block");
			jQuery("#create_object_holder").empty();
			jQuery("#create_object_holder").css("display", "none");
			jQuery("#search_holder").css("display", "block");
			jQuery("#created_objects_holder").css("display", "block");
			jQuery("#created_objects_input").append(data);
        }
	);
}

jQuery(function() 
{
	
	function retrieve_type_form(event)
	{
		var elem = jQuery(this),
			type_id = parseInt(elem.val());
		
        if(type_id == current_type_id)
                return;

		jQuery.post("pages/lesson/lesson_excercise/ajax/retrieve_type_form.ajax.php",
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

	function submit_search_form(event)
	{
		jQuery.post("pages/lesson/lesson_excercise/ajax/retrieve_form_result.ajax.php", 
			jQuery("#lesson_excercise_component_creator_form").serialize(), 
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
		jQuery.post("pages/lesson/lesson_excercise/ajax/retrieve_create_object_form.ajax.php", 
				jQuery("#lesson_excercise_component_creator_form").serialize(), 
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
	
	var start = 0;
	function submit_more(event)
	{
		var arr = jQuery("#lesson_excercise_component_creator_form").serialize();
		arr += "&start=" + start;
		jQuery.post("pages/lesson/lesson_excercise/ajax/retrieve_more_results.ajax.php", 
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
		jQuery("#type_form").on('click', "#submit_search_form", submit_search_form);
		jQuery("#type_form").on('click', "#search_puzzle_again", search_again);
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
	});

});
