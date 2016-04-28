	$.sliderStyle = 
	{
		defaults: { left: 480, time: 4000, hoverColor: "#9abfb0" }
	}
	
	$.fn.slider = function (options)
	{
		options = $.extend($.sliderStyle.defaults, options);	
		
		var elem = $(this);
		var timer;
		var size = $(".product").length;
		var current = 1;
		
		function slide_next()
		{
			$(".product." + current).animate({left: "-" + options.left}, 500, 
					function()
					{
						$(this).css("display", "none");
						$(this).animate({left: options.left}, 1);
					});
			current++;
			if(current == size+1)
			{
				current = 1;
			}
			$(".product." + current).css("display", "block");
			$(".product." + current).animate({left: "0"}, 500);
		}
		
		$("#product_div .product").each(function(index, value)
		{
			var product = $(value);
			if(product.attr("class").split(" ").slice(-1)[0] == 1)
			{
				product.css("left", "0");
			}
		});
		timer = setInterval(slide_next, options.time);
		
		$(".mini_product").hover(function()
		{
			clearInterval(timer);
			var hover_id = $(this).attr("class").split(" ").slice(-1)[0];
			current = hover_id
			$("#product_div .product").each(function(index, value)
			{
				var product = $(value);
				if(product.attr("class").split(" ").slice(-1)[0] == hover_id)
				{
					$(".product." + current).css("display", "block");
					product.css("left", "0");
				}
				else
				{
					product.css("display", "none");
					product.css("left", options.left);
				}
			});
			$(this).css("background", options.hoverColor);
		}, function()
		{
			if($("#control_image").attr('src').split("/").slice(-1)[0] == "pause.gif")
			{
				timer = setInterval(slide_next, options.time);
			}
			$(this).css("background", "");
		});
		$(".mini_product").css("cursor", "pointer");
		
		$("#control_image").live("click", function()
			{
				var elem = $(this);
				if(elem.attr('src').split("/").slice(-1)[0] == "pause.gif")
				{
					clearInterval(timer);
					elem.attr('src', 'layout/images/buttons/play.gif');
				}
				else
				{
					timer = setInterval(slide_next, options.time);
					elem.attr('src', 'layout/images/buttons/pause.gif');
				}
			});
		$("#control_image").css("cursor", "pointer");
	}

$(function()
{
	$(document).ready(function()
	{
		$("#product_slider").slider({left: _slide_left});
	});
});