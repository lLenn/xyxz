jQuery(function() 
{

	function show_details(event)
	{
		var details_to_show = jQuery(this).parent().parent().children(".details_to_show");
		var lesson_id = details_to_show.children("[name='lesson_id']").val();
		var hidden = details_to_show.css("display") == "none";
		if(hidden)
		{
			details_to_show.css("display", "block");
		}
		else
		{
			details_to_show.css("display", "none");
		}
		jQuery.post("pages/lesson/ajax/add_lesson_opened.ajax.php", {lesson_id: lesson_id});
	}
	
	function show_lessons(event)
	{
		var details_to_show = jQuery(this).parent().children(".map_output");
		var map_id = details_to_show.children("[name='map_id']").val();
		var hidden = details_to_show.css("display") == "none";
		if(hidden)
		{
			details_to_show.css("display", "block");
		}
		else
		{
			details_to_show.css("display", "none");
		}
		jQuery.post("pages/lesson/ajax/add_lesson_opened.ajax.php", {map_id: map_id});
	}
	
	jQuery(document).ready(function ()
	{
		jQuery(".show_details").on("click", show_details);
		jQuery(".map_div_title").on("click", show_lessons);
		jQuery(".map_div_title").css("cursor", "pointer");
	});

});
