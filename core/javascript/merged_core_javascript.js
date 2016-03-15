var table_color = '#d7954e';
var table_odd_color = '#e6b773';

var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-34676371-1']);
_gaq.push(['_trackPageview']);

/*
(function() {
  var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
  ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
  var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();
*/

var loadImg = new Image();
loadImg.src = "./layout/images/loading.gif";
loadImg.style.border = "none";
loadImg.style.marginLeft = "auto";
loadImg.style.marginRight = "auto";
loadImg.style.marginTop = "20px";
loadImg.style.display = "block";

function checkpwd()
{
	if(document.getElementById("pwd").value == document.getElementById("rep_pwd").value)	
		document.getElementById("res_png").innerHTML="<img style='border: 0px;' src='./layout/images/correct.png'>";
	else	document.getElementById("res_png").innerHTML="<img style='border: 0px;' src='./layout/images/error.png'>";
}

function confirmation(text, location) 
{
	var answer = confirm(text)
	if (answer)
	{
		window.location = location;
	}
}

function setup_tinyMCE()
{
	tinyMCE.init({
		mode : "textareas",
		theme : "advanced",
		editor_selector: "mce_editor",
		plugins : "preview",
		// Theme options
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,image,preview,code,|,forecolor,backcolor",
		theme_advanced_buttons3 : "",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left"
	}); 
}

setup_tinyMCE();

jQuery(function() 
{
	
	function load_menus()
	{
		jQuery(".menu ul").css({display: "none"}); // Opera Fix
		jQuery(".menu li").hover(function()
		{
			jQuery(this).find('ul:first').css({visibility: "visible",display: "none"}).show(400);
		},function()
		{
			jQuery(this).find('ul:first').css({visibility: "hidden"});
		});
	}
	
	function set_width()
	{
		var width = $(window).width();
		width -= 100;
		if(width>1100)
		{
			jQuery("#main_page_container").width(width);
			jQuery("#sub_page_container").width(width-202);
			if(jQuery("#menu_items").length > 0)
			{
				jQuery("#menu_items").width(width-655);
				jQuery(".menu_item_sub").width(width-825);
			}
			jQuery("body").css("overflow-x", "hidden");
		}
		else
		{
			jQuery("#main_page_container").width(1100);
			jQuery("#sub_page_container").width(898);
			if(jQuery("#menu_items").length > 0)
			{
				if(jQuery("#news_items").length > 0)
				{
					jQuery("#menu_items").width(436);
					jQuery(".menu_item_sub").width(266);
				}
				else
				{
					jQuery("#menu_items").width(636);
					jQuery(".menu_item_sub").width(466);
				}
			}
			jQuery("body").css("overflow-x", "auto");
		}
	}
	
	function submit_form(event)
	{
		var elem = jQuery(this);
		while(elem.prop("tagName") != "FORM")
		{
			elem = elem.parent();
		}
		tinyMCE.triggerSave();
		elem.submit();
		return false;
	}
	
	jQuery.fn.message = function()
	{
		return this.each(function()
			{
				var elem = jQuery(this);
				if(elem[0].tagName == "P" && elem.find('#close_img').length == 0)
				{
					var	div_overflow = jQuery(document.createElement('div')),
						div_img = jQuery(document.createElement('div')),
						error_img = jQuery(document.createElement('img')),
						div_break = jQuery(document.createElement('div'));
						elem_content = elem.contents();
	
					div_overflow.css("overflow", "hidden");
					div_img.css("float", "right");
					if(elem.attr("class") == "error")
					{
						error_img.attr("src", "layout/images/buttons/close_error_message.png");
						error_img.attr("id", "close_img");
						//ie hack
						error_img.attr("height", 16);
						error_img.attr("width", 16);
					}
					else if(elem.attr("class") == "good")
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
					div_break.attr("class", "clearfloat");
					
					div_img.append(error_img);
					div_overflow.append(elem_content);
					div_overflow.append(div_img);
					div_overflow.append(div_break);
					elem.empty();
					elem.append(div_overflow);
				}
			});
	}

	jQuery.fn.load_messages = function ()
	{
		jQuery(".error").message();
		jQuery(".good").message();
		jQuery(".info").message();
	}
	
	jQuery.fn.calendar = function()
	{
		var month_days = new Array("31", "29", "31", "30", "31", "30", "31", "31", "30", "31", "30", "31");
		function change_days(elem, month)
		{
			var days = month_days[month-1];
			var current_days = elem.children(".day").children("option").length;
			if(elem.children(".day").children("option:first").val()==0)
			{
				days++;
			}
			if(days<current_days)
			{
				for(i=current_days; i>days; i--)
				{
					elem.children(".day").children("option:nth-child(" + i + ")").remove();
				}
			}
			else if(days>current_days)
			{
				for(i=current_days+1; i<=days; i++)
				{
					var	option = jQuery(document.createElement('option'));
					if(elem.children(".day").children("option:first").val()==0)
					{
						option.text(i-1);
						option.attr("value", i-1);
					}
					else
					{
						option.text(i);
						option.attr("value", i);
					}
					elem.children(".day").append(option);
				}
			}
		}
		return this.each(function()
			{
				var elem = jQuery(this);
				var selected_month = elem.children(".month").val();
				elem.children(".month").click(function()
						{
							var month = $(this).val();
							if(month != selected_month)
							{
								change_days(elem, month);
								selected_month = month;
							}
						});
			});
	}

	
	jQuery(document).ready(function()
	{
		jQuery(document).on('click', "#submit_form", submit_form);
		
		if(jQuery("#page_container").length > 0)
		{
			load_menus();
		}
		
		
		if(jQuery("#main_page_container").length > 0)
		{
			jQuery(window).resize(set_width);
			set_width();
		}
		
		
		jQuery().load_messages();

		jQuery(".calendar_select").calendar();

		if(jQuery("#tabs").length > 0)
		{	
			jQuery("#tabs").tabs();
			jQuery("#tabs").tabs('option', 'active', tabnumber);
		}
	});
});
