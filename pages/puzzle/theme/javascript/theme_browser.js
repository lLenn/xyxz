function update_theme_sortable(parent)
{
	parent.children('.row').each(
			function (index) 
			{					
				if(index%2 == 0)
					jQuery(this).attr('class', 'row even');
				else
					jQuery(this).attr('class', 'row odd');
			
				var child = jQuery(this),
					order_nr = child.children('.row_cell:nth-child(1)'),
					name_input = child.children('.row_cell:nth-child(2)').children('.input').children('input'),
					id_hidden = child.children(':last'),
					index = index+1;
				order_nr.empty();
				order_nr.append(index);
				name_input.attr('name', 'name_' + index);
				id_hidden.attr('name', 'id_' + index);
			});
}

jQuery(function() 
{ 

	var prev_elem;
	
	function update_sortable(event, ui)
	{
		update_theme_sortable(jQuery(this));
	}

	function reset_theme_form(event)
	{
		jQuery.post("pages/puzzle/theme/ajax/retrieve_table_themes.ajax.php", function(data) 
        	{
				jQuery("#theme_table").empty();
				jQuery("#theme_table").append(data);
				jQuery("#theme_sortable").sortable({
					update: update_sortable
					});
				jQuery(".edit_cell").css('cursor', 'pointer');
				jQuery(".input_element").bind('blur', hide_edit_field);
				jQuery(".delete_record").css('cursor', 'pointer');
            }
		);
	}

	jQuery(document).ready(function ()
	{
		jQuery("#theme_sortable").sortable({
				update: update_sortable
				});
		jQuery("#reset_theme_form").on('click', reset_theme_form);
	});

});
