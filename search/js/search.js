$(function()
{
	var search_default = "Zoek stad...",
		no_search_result = "Geen resultaten gevonden",
		search_limit = 11,
		search_offset = 0,
		search_offices = null,
		search_prev = "",
		suggestions_abort = null,
		suggestions_timeout = false,
		suggestions_prev = "",
		suggestions_focus = false,
		suggestions = new Array(),
		scroll_pointer = -1,
		map_default = "https://www.google.com/maps/embed/v1/search?key=AIzaSyAifoqz92uDrQFEeiJY46I4vRuA0vtfw3A&q=";
	
	var search_container = $("#search_container"),
		search_form = $("#search_form"),
		search_input = $("#search_input"),
		search_hasSupportDesk = $("#search_has_support_desk"),
		search_isOpenDuringWeekends = $("#search_is_open_during_weekends"),
		search_content = $("#search_content"),
		suggestions_container = $("#suggestions_container"),
		map_iframe = $("#map_iframe");
	
	function search(value, new_search, append)
	{
		if(value instanceof $.Event)
			value = search_prev;
		
		if(typeof new_search === 'undefined')
			new_search = true;
		
		if(new_search)
			search_offset = -search_limit;
		
		if(typeof append === 'undefined')
			append = false;

		$.ajax({method: search_form.attr("method"),
				url: search_form.attr("action"),
				data: {search: value,
					   search_has_support_desk: search_hasSupportDesk.prop('checked')?1:0,
					   search_is_open_during_weekends: search_isOpenDuringWeekends.prop('checked')?1:0,
					   search_limit: search_limit,
					   search_offset: search_offset + search_limit},
				dataType: "json",
				}).done(function(data)
					{
						console.log(data);
						if(append)
							search_content.append(data.html);
						else
							search_content.html(data.html);
						
						search_limit = parseInt(data.limit);
						search_offset = parseInt(data.offset);
						
						if(append)
							search_offices = search_offices.concat(data.offices);
						else
							search_offices = data.offices;
						
						if(data.show_more)
							$("#search_show_more").css("display", "block");
						else
							$("#search_show_more").css("display", "none");

						for(var i=0, len=search_offices.length; i<len; i++)
						{
							var office = search_offices[i],
								office_element = search_content.children(":nth-child(" + (i + 1) + ")");
							
							addOfficeEvents(office, office_element);
						}
						
						search_prev = value;
					});
	}
	
	function addOfficeEvents(office, office_element)
	{
		office_element.off("mouseover");
		office_element.off("mouseout");
		office_element.off("click");
		
		office_element.on("mouseover", function(){ office_element.toggleClass("hover"); });
		office_element.on("mouseout", function(){ office_element.toggleClass("hover"); });
		office_element.on("click", function()
				{
					console.log(encodeURI(map_default + office.street + " " + office.city));
					map_iframe.attr("src", encodeURI(map_default + office.street + " " + office.city));
				});
	}
	
	function searchSuggestions(event, value)
	{
		if(event instanceof $.Event)
		{
			if(event.which == 40)
				scrollSuggestions("Down");
			else if(event.which == 38)
				scrollSuggestions("Up");
			
			if(event.which == 13)
			{
				event.preventDefault();
				event.stopPropagation();
				
				if(scroll_pointer != -1)
				{
					search(suggestions[scroll_pointer][0].name);
					suggestions_focus = false;
					focusout();
					return false;
				}
			}
			
			if(event.which == 40 || event.which == 38)
				return false;
		}
		
		if(typeof value === 'undefined')
			value = false;
		
		if(suggestions_abort !== null)
			suggestions_abort.abort();
		
		if(suggestions_timeout !== false)
			clearTimeout(suggestions_timeout);
		
		suggestions_timeout = 
			setTimeout(function()
			{
				value = value!==false?value:search_input.val();
				if(value != "" && value != suggestions_prev)
				{
					suggestions_abort = 
						$.ajax({method: search_suggestions_ajax.method,
							url: search_suggestions_ajax.url,
							data: {search: value},
							dataType: "json",
							}).done(function(data)
								{
									renderSuggestions(data);
									suggestions_prev = value;
								});
					suggestions_available = false;
				}
				else if(value == "")
				{
					suggestions_container.empty();
				}
			}, 150);
	}
	
	function scrollSuggestions(scroll)
	{
		if(suggestions.length && suggestions_container.css("display") == "block")
		{
			var prev_scroll_pointer = scroll_pointer;
			if(scroll == "Up")
			{
				scroll_pointer--;
				if(scroll_pointer < 0)
					scroll_pointer = suggestions.length - 1;
			}
			else if(scroll == "Down")
			{
				scroll_pointer++;
				if(scroll_pointer >= suggestions.length)
					scroll_pointer = 0;
			}
			
			if(prev_scroll_pointer != -1)
				mouseoutSuggestion(suggestions[prev_scroll_pointer][1]);
			mouseoverSuggestion(suggestions[scroll_pointer][1]);
			search_input.val(suggestions[scroll_pointer][0].label)
		}
	}
	
	function focus()
	{
		if(search_input.val() == search_default)
			search_input.val("");
		if(suggestions.length)
			suggestions_container.css("display", "block");
		search_input.removeClass("soft");
	}
	
	function focusout()
	{
		if(!suggestions_focus)
			suggestions_container.css("display", "none");
		if(search_input.val() == "")
		{
			search_input.val(search_default);
			search_input.addClass("soft");
		}
		if(scroll_pointer != -1)
		{
			suggestions[scroll_pointer][1].removeClass("hover"); 
			scroll_pointer = -1;
		}
	}

	function renderSuggestions(data)
	{
		suggestions_container.css("display", "block");
		suggestions_container.empty();
		if(data.length)
		{
			suggestions = new Array();
			scroll_pointer = -1;
			for(var i=0, len=data.length; i<len; i++)
			{
				var city = data[i],
					child = createSuggestionChild(city);
				suggestions.push([city, child]);
				suggestions_container.append(child);
			}
		}
		else
		{
			var city = {name: null, label: no_search_result},
				child = createSuggestionChild(city);
			suggestions_container.append(child);
		}
	}
	
	function createSuggestionChild(city)
	{
		var child = $(document.createElement("div")),
			label = $(document.createElement("div"));
		
		child.addClass("city_suggestion");
		child.on("mouseover", function()
				{
					mouseoverSuggestion(child);
				});
		child.on("mouseout", function()
				{
					mouseoutSuggestion(child); 
				});
		child.on("click", function(event)
				{
					search(city.name);
					search_input.val(city.label);
					suggestions_container.css("display", "none");
				});
		child.append(label);
		
		label.addClass("city_suggestion_label");
		label.html(city.label);
		label.css("font-size", search_input.css("font-size"));
		label.css("font-family", search_input.css("font-family"));
		label.css("font-weight", search_input.css("font-weight"));
		
		return child;
	}
	
	function mouseoverSuggestion(element)
	{
		if(scroll_pointer != -1)
			suggestions[scroll_pointer][1].removeClass("hover"); 
		suggestions_focus = true;
		element.addClass("hover");
		for(var i=0, len=suggestions.length;i<len;i++)
		{
			if(element.is(suggestions[i][1]))
			{
				scroll_pointer = i;
				break;
			}
		}
	}
	
	function mouseoutSuggestion(element)
	{
		suggestions_focus = false;
		element.removeClass("hover");
	}
	
	function keydown(event)
	{
		if(event.which == 13 || event.which == 40 || event.which == 38)
		{
			event.preventDefault();
			event.stopPropagation();
		}
	}

	search_input.on("keydown", keydown);
	search_input.on("keyup", searchSuggestions);
	search_input.on("focus", focus);
	search_input.on("focusout", focusout);
	if(search_input.val() == "" || search_input.val() == search_default)
	{
		search_input.val(search_default);
		search_input.addClass("soft");
	}
	
	suggestions_container.css("width", search_input.outerWidth() + "px");
	
	$("#search_show_more_results").on("click", function(){ search(search_prev, false, true); });
	
	search_isOpenDuringWeekends.on("change", search);
	search_hasSupportDesk.on("change", search);
	
	map_iframe.attr("src", map_default + "@51.09623,4.2279751");
	
	search("");
});