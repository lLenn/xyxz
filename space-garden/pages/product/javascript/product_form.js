$(function()
{	
	function show_media_type()
	{
		var elem = $(this);
		switch(elem.val())
		{
			case "image":  $(".video_type").css("display", "none");
								$(".image_type").css("display", "block");
								break;
			case "video":  $(".image_type").css("display", "none");
								$(".video_type").css("display", "block");
								break;
		}
	}
	
	$(document).ready(function ()
	{
		$("input[name=media_type]").click(show_media_type);
	});

});