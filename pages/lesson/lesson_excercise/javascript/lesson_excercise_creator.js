jQuery(function() 
{	
	
	function change_checkbox()
	{
		var elem = jQuery("input[name=add_lesson]");
		if(elem.prop("checked"))
			jQuery("#lesson_div").css("display", "block");
		else
			jQuery("#lesson_div").css("display", "none");
	}
	
	function change_pupils_checkbox()
	{
		var elem = jQuery("input[name=add_pupils]");
		if(elem.prop("checked"))
			jQuery("#pupils_div").css("display", "block");
		else
			jQuery("#pupils_div").css("display", "none");
	}
	
	function change_criteria_checkbox()
	{
		var elem = jQuery("input[name=visible]");
		if(elem.prop("checked"))
			jQuery("#criteria_visible").css("display", "none");
		else
			jQuery("#criteria_visible").css("display", "block");
	}
	
	function change_criteria_options_checkbox()
	{
		var elem = jQuery("input[name=criteria_visible]");
		if(elem.prop("checked"))
			jQuery("#criteria_options").css("display", "block");
		else
			jQuery("#criteria_options").css("display", "none");
	}
	
	jQuery(document).ready(function()
	{
		if(jQuery("select[name='theme_id[]']").length > 0)
			jQuery("select[name='theme_id[]']").multiselect2side({
					selectedPosition: 'right',
					moveOptions: false,
					labelsx: '',
					labeldx: ''
					});
		
		if(jQuery("input[name=add_lesson]").length > 0)
		{
			jQuery("input[name=add_lesson]").click(change_checkbox);
			change_checkbox();
		}		
		
		if(jQuery("input[name=add_pupils]").length > 0)
		{
			jQuery("input[name=add_pupils]").click(change_pupils_checkbox);
			change_pupils_checkbox();
		}			
		
		if(jQuery("input[name=visible]").length > 0)
		{
			jQuery("input[name=visible]").click(change_criteria_checkbox);
			change_criteria_checkbox();
		}
		
		if(jQuery("input[name=criteria_visible]").length > 0)
		{
			jQuery("input[name=criteria_visible]").click(change_criteria_options_checkbox);
			change_criteria_options_checkbox();
		}
	});

});