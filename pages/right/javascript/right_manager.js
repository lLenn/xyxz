jQuery(function() 
{ 
	
	function submit_form(event)
	{
		jQuery(this).parent().submit();
		return false;
	}

	jQuery(document).ready(function ()
	{
		jQuery(".delete_record").on('click', submit_form);
		jQuery(".delete_record").css('cursor', 'pointer');
		jQuery(".right_record").on('click', submit_form);
		jQuery(".right_record").css('cursor', 'pointer');
	});

});
