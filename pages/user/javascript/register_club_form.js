jQuery(function() 
{
	function add_coach(event)
	{
		count = ++jQuery(".coach_div").length;
		var	div_main = jQuery(document.createElement('div')),
			div_required_1 = jQuery(document.createElement('div')),
			div_required_2 = jQuery(document.createElement('div')),
			div_input = jQuery(document.createElement('div')),
			input = jQuery(document.createElement('input')),
			br_break = jQuery(document.createElement('br'));
		
		input.attr("type", "text");
		input.attr("name", "coach" + count + "_pupils");
		input.attr("size", 2);
		div_input.attr("class", "record_input");
		div_input.append(input);
		
		div_required_1.attr("class", "record_name_required");
		div_required_1.append(coach_text + " " + count + ":");
		br_break.attr("class", "clearfloat");
		div_required_2.append(pupil_text + " :");
		div_required_2.attr("class", "record_name_required");
		div_required_2.css("width", "200px");

		div_main.attr("id", "coach_div_" + count);
		div_main.attr("class", "coach_div");
		div_main.append(div_required_1);
		div_main.append(br_break);
		div_main.append(div_required_2);
		div_main.append(div_input);
		jQuery("#coaches_div").append(div_main);
	}
	
	function remove_coach(event)
	{
		count = jQuery(".coach_div").length;
		if(count>1)
			jQuery("#coach_div_" + count).remove();
	}
	
	function accept_payment(event)
	{
		jQuery("#confirmed").val(1);
		jQuery("#request_form").submit();
	}
	
	function cancel_payment(event)
	{
		jQuery("#request_form").submit();
	}
	
	function search_organisations(event)
	{
		var string = jQuery("input[name=city_code]").val();
		if(string.length == 4 && Math.floor(string) == string && jQuery.isNumeric(string))
		{
			var organisation_type = jQuery("input[name=registration_type]").val() - 2;
			jQuery.post("pages/user/ajax/retrieve_organisations.ajax.php",
					{city_code: string, organisation_type: organisation_type},
				function(data)
	        	{
					jQuery("#result_clubs").empty();
					jQuery("#result_clubs").append(data);
		        }
			);
		}
	}
	
	function prevent_submit(event)
	{
		if(event.which == 13) 
		{
			event.preventDefault();
			return false;
		}
	}
	
	function show_coaches_or_not(event)
	{
		var dont_show = jQuery(this).val()==0?false:true;
		if(dont_show)
		{
			jQuery("#coaches_div").css("display", "none");
		}
		else
		{
			jQuery("#coaches_div").css("display", "block");
		}
	}
	
	jQuery(document).ready(function ()
	{
		if(jQuery("#add_coach").length)
		{
			jQuery("#add_coach").click(add_coach);
			jQuery("#add_coach").css("cursor", "pointer");
			jQuery("#remove_coach").click(remove_coach);
			jQuery("#remove_coach").css("cursor", "pointer");
		}
		if(jQuery("#accept_payment").length)
		{
			jQuery("#accept_payment").click(accept_payment);
			jQuery("#cancel_payment").click(cancel_payment);			
		}
		if(jQuery("input[name=city_code]").length)
		{
			jQuery("input[name=city_code]").keyup(search_organisations);
			jQuery("input[name=city_code]").keydown(prevent_submit);
		}
		if(jQuery("input[name=price_arrangement]").length)
			jQuery("input[name=price_arrangement]").click(show_coaches_or_not)
	});

});