$(function() 
{ 
	$.fn.message = function()
	{
		return this.each(function()
			{
				var elem = $(this);
				if(elem[0].tagName == "P" && elem.find('#close_img').length == 0)
				{
					var	div_overflow = $(document.createElement('div')),
						div_img = $(document.createElement('div')),
						error_img = $(document.createElement('img')),
						br_break = $(document.createElement('br'));
						elem_content = elem.contents();
	
					div_overflow.css("overflow", "hidden");
					div_overflow.css("position", "relative");
					div_overflow.css("padding-right", "20px");
					div_img.css("position", "absolute");
					div_img.css("right", "0px");
					div_img.css("top", "0px");
					
					if(elem.attr("class") == "error")
					{
						error_img.attr("src", "layout/images/buttons/close_error_message.png");
						error_img.attr("id", "close_img");
						//ie hack
						error_img.attr("height", 16);
						error_img.attr("width", 16);
					}
					else if(elem.attr("class") == "success")
					{
						error_img.attr("src", "layout/images/buttons/close_good_message.png");
						error_img.attr("id", "close_img");
					}
					else if(elem.attr("class") == "info")
					{
						error_img.attr("src", "layout/images/buttons/close_info_message.png");
						error_img.attr("id", "close_img");
					}
					error_img.css("border", "0");
					error_img.css("cursor", "pointer");
					error_img.bind('click', function()
						{
							elem.fadeOut('fast', function()
									{
										elem.remove();
									});
							
						});
					br_break.attr("class", "clear_float");
					
					div_img.append(error_img);
					div_overflow.append(elem_content);
					div_overflow.append(div_img);
					div_overflow.append(br_break);
					elem.empty();
					elem.append(div_overflow);
				}
			});
	}

	$.fn.load_messages = function ()
	{
		$(".error").message();
		$(".success").message();
		$(".info").message();
	}

	$(document).ready(function ()
	{
		$().load_messages();
	});

});
