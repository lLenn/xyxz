function update_sortable_parent(parent)
{
	parent.children('.row').each(
			function (index) 
			{				
				var child = jQuery(this);	
				var classes = child.attr('class').split(" ");
				var index_class = jQuery.inArray("even", classes);
				if(index_class == -1)
					index_class = jQuery.inArray("odd", classes);

				if(index%2 == 0)
					classes[index_class] = "even";
				else
					classes[index_class] = "odd";
				child.attr('class', classes.join(" "));
					
				var order_nr = child.children('.order_cell:nth-child(1)');
					index = index+1;
				order_nr.empty();
				order_nr.append(index);
				child.find(".editable_input").each(function()
						{
							var input = jQuery(this);
							var name = input.attr('name').split("_");
							name.pop();
							name.push(index);
							input.attr('name', name.join("_"));
						});
			});
}

function update_sortable(event, ui)
{
	update_sortable_parent(jQuery(this));
}

jQuery(function() 
{
	jQuery(document).ready(function ()
	{
		if(jQuery(".sortable_table").length > 0)
		{
			$(".sortable_table").sortable({
					update: update_sortable
					});
		}
	});

});
