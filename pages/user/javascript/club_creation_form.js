jQuery(function() 
{
	function add_club(event)
	{
		jQuery("#accept_hidden").val(1);
		jQuery("#club_accept_form").submit();
	}
	
	jQuery(document).ready(function ()
	{
		jQuery("#add_club").click(add_club);
	});

});