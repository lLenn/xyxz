jQuery(function() 
{ 
	jQuery(document).ready(function ()
	{
		if(jQuery("select[name='theme_id[]']").length > 0)
			jQuery("select[name='theme_id[]']").multiselect2side({
					selectedPosition: 'right',
					moveOptions: false,
					labelsx: '',
					labeldx: ''
					});
	});

});
