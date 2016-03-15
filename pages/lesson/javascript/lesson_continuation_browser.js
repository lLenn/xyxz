jQuery(function() 
{
	function show_hide_message()
	{
		var hidden = jQuery(this).children(".click_to_view_out").css("display") == "none";
		if(hidden)
		{
			jQuery(this).children(".click_to_view_in").css("display", "none");
			jQuery(this).children(".click_to_view_out").css("display", "block");
		}
		else
		{
			jQuery(this).children(".click_to_view_out").css("display", "none");
			jQuery(this).children(".click_to_view_in").css("display", "block");
		}
	}
	
	jQuery(document).ready(function ()
	{
		jQuery(".click_to_view").on('click', show_hide_message);
		jQuery(".click_to_view").css('cursor', 'pointer');
	});

});
