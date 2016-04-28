$(function()
{	
	function show_language_input()
	{
		var elem = $(this);
		$.each($("#language_div").children(), function(index, value) 
			{ 
			  	var child_elem = $(value);
			  	if(child_elem.attr("id") != "language_" + elem.val())
			  		child_elem.css("display", "none");
			  	else
			  		child_elem.css("display", "block");
			});
	}
	
	$(document).ready(function ()
	{
		$("select[name='language']").change(show_language_input);
	});

});