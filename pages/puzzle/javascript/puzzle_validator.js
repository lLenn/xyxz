jQuery(function() 
{	
	jQuery(document).ready(function ()
	{
		jQuery("input[name=select]").on('click', function()
				{
					var elem = $(this);
					if(elem.prop("checked"))
					{
						$("input[name='puzzle_id[]']").prop("checked", true);
					}
					else
					{
						$("input[name='puzzle_id[]']").prop("checked", false);
					}
				});
	});

});
