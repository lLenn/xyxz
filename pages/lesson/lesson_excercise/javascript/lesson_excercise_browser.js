/*
function update_lesson_excercise_sortable(parent)
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
					cb_input = child.children('.row_cell:nth-child(4)').children('.input').children('input'),
					id_hidden = child.children(':last'),
					index = index+1;
				order_nr.empty();
				order_nr.append(index);
				cb_input.attr('name', 'visible_' + index);
				id_hidden.attr('name', 'id_' + index);
			});
}
*/

jQuery(function() 
{ 

	var start = 0;
	function submit_search_form(event)
	{
		start = 0;
		jQuery("#more_lesson_excercises_search").empty();
		submit_lesson_excercise_shop();
	}
	
	function submit_lesson_excercise_shop(event)
	{
		var arr = jQuery("#lesson_excercise_search_form").serialize();
		arr += "&start=" + start;
		jQuery.post("pages/lesson/lesson_excercise/ajax/retrieve_shop_lesson_excercises.ajax.php", 
			arr, 
			function(data) 
        	{
				if(start!=0)
				{
					jQuery("#more_lesson_excercises_block").remove();
					jQuery("#more_lesson_excercises_record").append(data);
				}
				else
				{
					jQuery("#more_lesson_excercises_search").append(data);
				}
				start += 20;
            }
		);
	}
	/*
	var prev_elem;
	
	function update_sortable(event, ui)
	{
		update_lesson_excercise_sortable(jQuery(this));
	}
	
	function delete_lesson_excercise_record(event)
	{
		var elem = jQuery(this),
			parent_row = elem.parent().parent(),
			parent_div = parent_row.parent();
			
		parent_row.remove();
		if(parent_div.attr("class").split(" ")[0] == "sortable_table")
			update_lesson_excercise_sortable(parent_div);
	}
	
	function reset_lesson_excercise_form(event)
	{
		var elem = jQuery(this);
		var form = elem.parent().parent().parent();
		var return_div = form.parent();
		jQuery.post("pages/lesson/lesson_excercise/ajax/retrieve_table_lesson_excercises.ajax.php", 
			form.serialize(),
			function(data) 
        	{
				return_div.empty();
				return_div.append(data);
				jQuery(".lesson_excercise_sortable").sortable({
					update: update_sortable
					});
				jQuery(".edit_cell").css('cursor', 'pointer');
				jQuery(".input_element").bind('blur', hide_edit_field);
				jQuery(".delete_record").css('cursor', 'pointer');
            }
		);
	}
	*/

	function show_details_buttons()
	{
		jQuery(".general").each(function(){jQuery(this).css("display", "none");});
		jQuery(".details").each(function(){jQuery(this).css("display", "block");});
	}
	
	function show_general_buttons()
	{
		jQuery(".details").each(function(){jQuery(this).css("display", "none");});
		jQuery(".general").each(function(){jQuery(this).css("display", "block");});
	}
	
	jQuery(document).ready(function ()
	{
		/*
		jQuery(".lesson_excercise_sortable").sortable({
				update: update_sortable
				});
		jQuery(".reset_lesson_excercise_form").on('click', reset_lesson_excercise_form);
		if(jQuery(".lesson_excercise_delete_record").length > 0)
		{
			jQuery(".lesson_excercise_delete_record").on('click', delete_lesson_excercise_record);
			jQuery(".lesson_excercise_delete_record").css('cursor', 'pointer');
		}
		*/
		if(jQuery("#details_tab").length > 0)
		{
			jQuery("#details_tab").click(show_details_buttons);
			jQuery("#general_tab").click(show_general_buttons);
			if(tabnumber == 0)
				show_details_buttons();
			else
				show_general_buttons();	
		}
		else
			show_general_buttons();
		
		jQuery("#submit_search_form").on('click', submit_search_form);
		jQuery("#more_lesson_excercises").on("click", submit_lesson_excercise_shop);
	});

});
