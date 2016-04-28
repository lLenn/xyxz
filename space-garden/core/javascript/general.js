function change_language(lang) 
{
	var split_question = window.location.href.split("?");
	if (split_question.length > 1)
	{
		var new_location = new Array();
		var location = split_question[1].split("&");
		$.each(location, function(index, value)
		{
			var arg = $.trim(value.split("=")[0]);
			if(arg != "language")
			{
				new_location.push(value);
			}
		});
		var add = "";
		if(new_location.length>=1)
			add = "&";
		window.location = split_question[0] + "?" + new_location.join("&") + add + "language=" + lang;
	}
	else
		window.location = window.location + "?language=" + lang;
}

$(function() 
{	
	function submit_form(event)
	{
		$(this).parent().parent().parent().submit();
		return false;
	}
	
	$(document).ready(function()
	{
		if($("#submit_form").length > 0)
			$("#submit_form").live('click', submit_form);
		
		isIE = (/MSIE/gi).test(navigator.userAgent) && (/Explorer/gi).test(navigator.appName);
		isIE6 = isIE && /MSIE [56]/.test(navigator.userAgent);
		isIE7 = isIE && /MSIE [7]/.test(navigator.userAgent);
		if(isIE6 || isIE7)
		{
			var ext = "png";
			if(isIE6)
				ext = "gif";
			$('#logo').css("width", "284px");
			$('#logo').attr("src", "layout/images/Space-Garden-1." + ext);
			$('#logo').css("margin-top", "-40px");

			var img = $(document.createElement("IMG"));
			img.load(function()
			{
				img.css("width", "216px");
				img.css("height", "70px");
				img.css("position", "relative");
				img.css("overflow", "visible");
				img.css("bottom", "14px");
				img.css("z-index", "1");
				img.css("margin-left", "-7px");
				$("#logo_header").append(img);
			});
			img.attr("src", "layout/images/Space-Garden-2." + ext);
		}
		if($(".menu").length > 0)
		{
			$(".menu").menu({backgroundColorHover : "#9abfb0"});
		}
	});
});
