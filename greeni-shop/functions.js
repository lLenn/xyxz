// JavaScript Document
function addslashes(str) 
{
	str=str.replace(/\\/g,'\\\\');
	str=str.replace(/\'/g,'\\\'');
	str=str.replace(/\"/g,'\\"');
	str=str.replace(/\0/g,'\\0');
	return str;
}
// Postion on item when url select_item parameter is present
function jumpto(color) 
{
	var url = document.URL;
	var para = "?select_item=";
	
	pos = url.indexOf(para);
	if (pos>0) 
	{
		ele = url.substring(pos+para.length);
		obj = document.getElementById(ele);
		if (obj) 
		{
			obj.bgColor = color;
			Ypos = findPos(obj)-200;
		} 
		else 
		{
			Ypos = 0;
		}
		return Ypos
	}
}

// find Y posistion of a html element
function findPos(obj) 
{
	var curtop = 0;
	
	if(obj.offsetParent)
	{		
		// if browser supports offsetParent
		do 
		{
			curtop += obj.offsetTop;
		} while (obj = obj.offsetParent);
	}
	return curtop;
}

// keep navigation buttons in view
function KeepInView(id) {
	var el = document.getElementById(id); if (el==null) return;
	el.startPageTop = -el.offsetTop;
	el.currentX = el.offsetLeft;
	el.currentY = el.offsetTop;
	el.floatInView = function() {
		var targetY = (document.documentElement.scrollTop>this.startPageTop) ? (document.documentElement.scrollTop-this.startPageTop) : 0;
		this.currentY += (-this.currentY)/4;
		this.style.top = targetY + "px";
	}
	setInterval('document.getElementById("'+id+'").floatInView()',40);
}

// open detail information in a new window
function ShowDetails(artid)
{
    //window.open("../detail.php?item="+art_id,"Detailed Product Info","width=350,height=600,screenX=50,left=50,screenY=50,top=50,status=yes,menubar=yes");
	window.open(root + "detail.php?item="+artid,null,"width=750,height=800,status=yes,menubar=yes,scrollbars=yes");
}

function AlertStock(artid)
{
	window.open(root + "alert.php?item="+artid,null,"width=750,height=800,status=yes,menubar=yes,scrollbars=yes");
}

function ShowRelated(artid)
{
	window.open(root + "related.php?item="+artid,null,"width=750,height=800,status=yes,menubar=yes,scrollbars=yes");
}

function ShowReplacements(artid)
{
	window.open(root + "replacements.php?item="+artid,null,"width=750,height=800,status=yes,menubar=yes,scrollbars=yes");
}

//confirm message and browse to url
function conf(message,url)
{
	if(confirm(message))	
		window.location = url;
}

function popup(id)
{
    var centerWidth = (window.screen.width - 600) / 2;
    var centerHeight = (window.screen.height - 500) / 2;
	window.open('message_popup.php?message_id=' + id,'','width=600,height=500,left=' + centerWidth + ',top=' + centerHeight);
}

//  disable selecting text/images
function disableSelection(target)
{
	if (typeof target.onselectstart!="undefined") //IE route
		target.onselectstart=function(){return false}
	else if (typeof target.style.MozUserSelect!="undefined") //Firefox route
		target.style.MozUserSelect="none"
	else //All other route (ie: Opera)
		target.onmousedown=function(){return false}
	target.style.cursor = "default";
}
//Sample usages
//disableSelection(document.body) //Disable text selection on entire body
//disableSelection(document.getElementById("mydiv")) //Disable text selection on element with id="mydiv"

//next methods use jQuery: jQuery.com 
//load article pictures
var root = "";
var tag = 0;

function showImage(id, url, width, height)
{
	var elem = $("."+id);
	var img = $(document.createElement("img"));
	img.attr("src", root+"PICTURES/"+url);
	img.load(function(){
		img.attr("width", width);
		img.attr("height", height);
		elem.empty();
		elem.append(img);
	});
}

function show_all()
{
	$(".load_image_a").each(function()
			{
				var elem = $(this);
				//var onclick_args = elem.attr("onclick").split().slice(-1);
				setTimeout(elem.attr("onclick"),2);
			});
}

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

function change_language()
{
	var elem = $(this);
	add_arg("lang", elem.val());
}

function forgot_pass()
{
	var elem = $(this);
	add_arg("forgot_password", "forgot_password");
}

function change_form_view()
{
	var elem = $("input[name='client_number_q']");
	if(elem.is(':checked'))
	{
		$('.no_cl').attr('style', 'display: none;');
		$('.yes_cl').attr('style', 'display: table-row;');
		//IE6 HAS PROBLEMS SETTING DISPLAY STYLE WITH JQUERY
		$('select[name=country]')[0].style.display = "none";
		$('#loginImageGreeni_shop_01').attr("src" ,"images/loginImageGreeni-shop_01_m.gif");
		$('#loginImageGreeni_shop_04').attr("src" ,"images/loginImageGreeni-shop_04_m.gif");
		$('#loginImageGreeni_shop_04').height("499");
		$('#loginImageGreeni_shop_01').height("499");
		$('#loginImageGreeni_shop_09').height("274");
		$('#loginImageGreeni_shop_08').height("313");
		$('#login_div').css("height","283px");
	}
	else
	{
		$('.yes_cl').attr('style', 'display: none;');
		$('.no_cl').attr('style', 'display: table-row;');
		//IE6 HAS PROBLEMS SETTING DISPLAY STYLE WITH JQUERY
		$('select[name=country]')[0].style.display = "inline";
		$('#loginImageGreeni_shop_01').attr("src" ,"images/loginImageGreeni-shop_01_l.gif");
		$('#loginImageGreeni_shop_04').attr("src" ,"images/loginImageGreeni-shop_04_l.gif");
		$('#loginImageGreeni_shop_04').height("709");
		$('#loginImageGreeni_shop_01').height("709");
		$('#loginImageGreeni_shop_09').height("494");
		$('#loginImageGreeni_shop_08').height("523");
		$('#login_div').css("height","493px");
	}
}

function move_to_tag()
{
	var elem = $(this);
	var id = elem.attr("class");
	if(tag == "")
		tag = 0;
	else if(tag == "template_bottom")
		tag = $(".tag").length;
	
	if(id == "up" && tag > 1)
		tag -= 1;
	else if(id == "up")
		tag = "";
	else if(id == "down" && tag < $(".tag").length)
		tag += 1;
	else
		tag = "template_bottom";
	window.location = "#" + tag;
}

function change_length()
{
	var elem = $(this);
	add_arg("length", elem.val());
}

$(function()
{	
	//When document is ready, adjust all elements with the class info to have a pointer cursor
	$(document).ready(function ()
	{
		if($(".info").length > 0)
		{
			$(".info").css("cursor", "pointer");
		}
		if($("#select_language_index").length > 0)
		{
			$("#select_language_index").live("change", change_language);
		}
		var url = window.location.href;
		if(url.split('/').slice(-2)[0] == "catalogue_docs")
		{
			root = "../";
		}
		if($("input[name='client_number_q']").length > 0)
		{
			$("input[name='client_number_q']").live("change", change_form_view);
			change_form_view();
		}
		if($(".menu").length > 0)
		{
			$(".menu").menu();
		}
		if($(".up").length>0 && $(".down").length>0)
		{
			$(".up").click(move_to_tag);
			$(".down").click(move_to_tag);
		}

		$("select[name='length']").live("change", change_length);
	});

});


