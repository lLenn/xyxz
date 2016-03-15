jQuery(function() 
{
	function accept_payment(event)
	{
		jQuery("#confirmed").val(1);
		jQuery("#upgrade_form").submit();
	}
	
	function cancel_payment(event)
	{
		jQuery("#upgrade_form").submit();
	}
	
	jQuery(document).ready(function ()
	{
		if(jQuery("#accept_payment").length)
		{
			jQuery("#accept_payment").click(accept_payment);
			jQuery("#cancel_payment").click(cancel_payment);			
		}
	});

});