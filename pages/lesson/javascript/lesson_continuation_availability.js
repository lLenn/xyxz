jQuery(function() 
{
	var start = 0;
	function submit_search_form(event)
	{
		var arr = jQuery("#lesson_continuation_search_form").serialize();
		arr += "&start=" + start;
		jQuery.post("pages/lesson/ajax/retrieve_continuation_form_result.ajax.php", 
			arr, 
			function(data) 
	    	{
				if(start!=0)
				{
					jQuery("#more_block").remove();
					jQuery("#more_record").append(data);
				}
				else
				{
					jQuery("#continuation_search_form").css("display", "none");
					jQuery("#continuation_search_result").empty();
					jQuery("#continuation_search_result").append(data);
					jQuery("#continuation_search_result").css("display", "block");
					//jQuery("#more_search").append(data);
				}
				start += 20;
	        }
		);
	}

	function search_again(event)
	{

		start = 0;
		jQuery("#continuation_search_form").css("display", "block");
		jQuery("#continuation_search_result").css("display", "none");
	}

	jQuery(document).ready(function ()
	{
		jQuery("#submit_search_form").on('click', submit_search_form);
		jQuery("#continuation_search_result").on("click", "#more_results", submit_search_form);
		jQuery("#continuation_search_result").on('click', "#search_again", search_again);
	});

});
