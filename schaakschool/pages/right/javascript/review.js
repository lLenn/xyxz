jQuery(function() 
{ 
	var scrollTop = 0;
	var object_id = 0;
	var location_id = 0;
	var prev_back_url = "";
	var prev_history_url = "";
	
	function show_review_form(event)
	{
		var className = jQuery(this).parent().attr("class").split(" ");
		object_id = className[0];
		location_id = className[1];
		arr = "object_id=" + object_id + "&location_id=" + location_id;
		scrollTop = jQuery(document).scrollTop();
		post_review_form(arr);
	}
	
	function show_reviews(event)
	{
		var className = jQuery(this).parent().attr("class").split(" ");
		object_id = className[0];
		location_id = className[1];
		arr = "object_id=" + object_id + "&location_id=" + location_id;
		scrollTop = jQuery(document).scrollTop();
		post_reviews(arr);
	}
	
	function submit_review_form(event)
	{
		post_review_form(jQuery(this).parent().parent().parent().serialize());
	}

	function cancel_review_form(event)
	{
		create_review_div();
		jQuery("#created_review_div").empty();
		jQuery("#created_review_div").css("display", "none");
		jQuery("#div_content").css("display", "block");
		jQuery(document).scrollTop(scrollTop);
		remove_back_events();
		event.preventDefault();
		event.stopPropagation();
	}
	
	function post_review_form(arr)
	{
		jQuery.post("pages/right/ajax/retrieve_review_form.ajax.php", 
				arr, 
				function(data) 
	        	{
					if(data!="")
					{
						create_review_div();
						if(data.charAt(0) == "0")
						{
							data = data.slice(1);
							jQuery("#created_review_div").empty();
							jQuery("#created_review_div").append(data);
							jQuery("#created_review_div").css("display", "block");
							jQuery("#div_content").css("display", "none");
							jQuery(document).scrollTop(jQuery("#sub_page_container").offset().top);
							add_back_events();
						}
						else
						{
							jQuery("#created_review_div").empty();
							jQuery("#created_review_div").css("display", "none");
							jQuery("#div_content").css("display", "block");
							jQuery("#review_form_" + object_id + "_" + location_id).empty();
							jQuery("#review_form_" + object_id + "_" + location_id).append(data);
							jQuery(document).scrollTop(scrollTop);
							remove_back_events()
						}
					}
	            }
			);
	}
	
	function post_reviews(arr)
	{
		jQuery.post("pages/right/ajax/retrieve_reviews.ajax.php", 
				arr, 
				function(data) 
	        	{
					if(data!="")
					{
						create_review_div();
						jQuery("#created_review_div").empty();
						jQuery("#created_review_div").append(data);
						jQuery("#created_review_div").css("display", "block");
						jQuery("#div_content").css("display", "none");
						jQuery(document).scrollTop(jQuery("#sub_page_container").offset().top);
						add_back_events();
					}
	            }
			);
	}
	
	function add_back_events()
	{
		prev_back_url = jQuery("#back_button").attr("href");
		jQuery("#back_button").attr("href", "javascript:;");
		jQuery("#back_button").on("click", cancel_review_form);
		history.pushState(null, null, Location.href);
		jQuery(window).on("popstate", cancel_review_form);
	}
	
	function remove_back_events()
	{
		jQuery("#back_button").off("click", cancel_review_form);
		jQuery("#back_button").attr("href", prev_back_url);
		jQuery(window).off("popstate", cancel_review_form);
	}
	
	function create_review_div()
	{
		if(jQuery("#created_review_div").length == 0)
		{
			var link  = jQuery(document.createElement('link'));
			link.attr('rel', 'stylesheet');
			link.attr('type', 'text/css');
			link.attr('href', 'layout/news_layout.css');
			jQuery('head').append(link);
			    
			var	div_review = jQuery(document.createElement('div'));
			div_review.attr("id", "created_review_div");
			div_review.css("display", "none");
			
			var	div_content = jQuery(document.createElement('div'));
			div_content.attr("id", "div_content");
			div_content.css("display", "none");
			
			div_content.html(jQuery("#sub_page_container").html());
			jQuery("#sub_page_container").empty();
			jQuery("#sub_page_container").append(div_review);
			jQuery("#sub_page_container").append(div_content);
		}
	}
	
	jQuery(document).ready(function ()
	{
		jQuery(document).on("click", "#submit_review_form", submit_review_form);
		jQuery(document).on("click", "#cancel_review_form", cancel_review_form);
		jQuery(document).on("click", ".show_review_form", show_review_form);
		jQuery(document).on("click", ".show_reviews", show_reviews);
	});

});
