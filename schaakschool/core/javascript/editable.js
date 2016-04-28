
var prev_elem = null;
var prev_elem_changed = true;

jQuery(function() 
{
	function show_edit_field(event)
	{
		if(prev_elem_changed)
		{
			var elem = jQuery(event.target).parent(),
				text = elem.children('.text'),
				edit_field = elem.children('.input');
			
			if(edit_field.css('display')=='none')
			{
				text.text('');
				edit_field.css('display','block');
				input_element = edit_field.children('.input_element');
				input_element.focus();
			}
			prev_elem_changed = false;
			prev_elem = elem;
		}
		else
		{
			prev_text = prev_elem.children('.text'),
			prev_edit_field = prev_elem.children('.input');
			if(prev_edit_field.css('display')=='block')
			{
				prev_edit_field.css('display','none');
				prev_edit_input = prev_edit_field.children('.input_element');
				if(prev_edit_input.children(':selected').length > 0)
					prev_edit_text = prev_edit_input.children(':selected').text();
				else if(prev_edit_input.attr("type") == "checkbox")
					prev_edit_text = (prev_edit_input.is(':checked'))?"Waar":"Vals";
				else
					prev_edit_text = prev_edit_input.val();
				prev_text.text(prev_edit_text);
			}
			prev_elem_changed = true;
			if(jQuery(this).children('.input').css('display')=='none')
				show_edit_field(event);
		}
	}

	function delete_record(event)
	{
		var elem = jQuery(this),
			parent_row = elem.parent().parent().parent().parent(),
			parent_div = parent_row.parent();
			
		parent_row.remove();
		if(typeof(parent_div.attr("class")) != "undefined" && parent_div.attr("class").split(" ")[0] == "sortable_table")
			window["update_sortable_parent"](parent_div);
	}
	
	function reset_form(event)
	{
		var elem = jQuery(this);
		var table_id = elem.attr("class").split(" ").slice(-1)[0];
		jQuery.post("core/lib/html/reset_form.ajax.php", {table_id: table_id}, function(data) 
        	{
				jQuery("#table_" + table_id).empty();
				jQuery("#table_" + table_id).append(data);
				if(jQuery("#sortable_" + table_id).length > 0)
				{
					jQuery("#sortable_" + table_id).sortable({
							update: update_sortable
							});
				}
				if(jQuery("#table_" + table_id).find(".row_link").length > 0)
				{
					jQuery("#table_" + table_id).find(".row_link").table_row_link();
				}
				jQuery("#table_" + table_id).find(".edit_cell").css('cursor', 'pointer');
				//jQuery("#table_" + table_id).find(".input_element").bind('blur', hide_edit_field);
				jQuery("#table_" + table_id).find(".delete_record").css('cursor', 'pointer');
				jQuery("#reset_form").on('click', reset_form);
            }
		);
	}
	
	jQuery(document).ready(function ()
	{
		if(jQuery(".edit_cell").length > 0)
		{
			jQuery(document).on('click', ".edit_cell", show_edit_field);
			jQuery(".edit_cell").css('cursor', 'pointer');
		}
		/*
		if(jQuery(".input_element").length > 0)
			jQuery(".input_element").bind('blur', hide_edit_field);
			*/
		if(jQuery(".delete_record").length > 0)
		{
			jQuery(document).on('click', ".delete_record", delete_record);
			jQuery(".delete_record").css('cursor', 'pointer');
		}
		if(jQuery("#reset_form").length > 0)
		{	
			jQuery("#reset_form").on('click', reset_form);
		}
	});

});
