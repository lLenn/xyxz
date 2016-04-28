function add_arg(name, value)
{
	var split_question = window.location.href.split("?");
	if (split_question.length > 1)
	{
		var new_location = new Array();
		var location = split_question[1].split("&");
		$.each(location, function(index, value)
		{
			var arg = $.trim(value.split("=")[0]);
			if(arg != name)
			{
				new_location.push(value);
			}
		});
		var add = "";
		if(new_location.length>=1)
		{
			add = "&";
		}
		window.location = split_question[0] + "?" + new_location.join("&") + add + name + "=" + value;
	}
	else
	{
		window.location = window.location + "?" + name + "=" + value;
	}
}