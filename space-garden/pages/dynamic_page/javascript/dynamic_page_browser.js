$(function()
{
	function update_sortable(event, ui)
	{
		var elem = $(this);
		var post_array = new Array();
		elem.children('.row').each(
			function (index, value) 
			{		
				var child = $(value),
					order_nr = child.children('td:nth-child(1)'),
					id = child.children('td:nth-child(2)').text();

				var classes = child.attr('class').split(" ");
				var index_class = $.inArray("even", classes);
				if(index_class == -1)
					index_class = $.inArray("odd", classes);
				
				if(index%2 == 0)
					classes[index_class] = "even";
				else
					classes[index_class] = "odd";
				child.attr('class', classes.join(" "));
			
				order_nr.empty();
				order_nr.append(index+1);

				post_array.push(new Array(index+1, id))
			});
		$("#sort_info").css("display", "none");
		$("#sort_error").css("display", "none");
		$("#sort_saving").css("display", "inline");
		$.post("pages/dynamic_page/ajax/save_order.ajax.php",
				{pages: serialize(post_array)},
				function(data) 
	        	{
					$("#sort_saving").css("display", "none");
					if(data == 1)
						$("#sort_info").css("display", "inline");
					else
						$("#sort_error").css("display", "inline");
		         }
			);
	}


	$(document).ready(function()
	{
		$("#dp_sortable").sortable({
				update: update_sortable
				});
	});
});