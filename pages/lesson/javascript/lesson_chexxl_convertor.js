var prev_game_ids = null;

function getMyApp(appName) 
{
	if (navigator.appName.indexOf ("Microsoft") !=-1) {
		return window[appName];
	} else {
		return document[appName];
	}
}

function chexxl_converted(game_ids, errors)
{
	prev_game_ids = game_ids;
	tinyMCE.triggerSave();
	var arr = jQuery("#chexxl_convertor_form").serialize();
	for(index = 0; index < game_ids.length; index++)
		arr += "&game_ids[]=" + game_ids[index];
	jQuery.post("pages/lesson/ajax/retrieve_form_result_chexxlconvertor.ajax.php",
			arr,
			function(data)
			{
				if(data == "1")
					window.location = root_url + "index.php?page=browse_lessons&message=Conversie+geslaagd&message_type=good";
				else
				{
					jQuery("#chexxl_convertor_feedback").empty();
					jQuery("#chexxl_convertor_feedback").append(data);
				}
			});
}



jQuery(function() 
{
	function submit_chexxl_conversion_form(event)
	{
		jQuery("#chexxl_convertor_feedback").empty();
		jQuery("#chexxl_convertor_feedback").append(loadImg);
		if(prev_game_ids != null)
		{
			var arr = "game_ids[]=" + prev_game_ids[0];
			for(index = 1; index < prev_game_ids.length; index++)
				arr += "&game_ids[]=" + prev_game_ids[index];
			jQuery.post("pages/lesson/ajax/delete_prev_games_chexxlconvertor.ajax.php",
					arr,
					function(data){
						if(data=="1")
							load_pgn();
						else
						{
							jQuery("#chexxl_convertor_feedback").empty();
							jQuery("#chexxl_convertor_feedback").append(data);
						}
					});
		}
		else
			load_pgn();
	}
	
	function load_pgn()
	{
		try
		{
			getMyApp("FileUploader").convertChexxl();
		}
		catch(err)
		{
			jQuery("#chexxl_convertor_feedback").empty();
			alert(err.message);
		}
	}
	
	jQuery(document).ready(function ()
	{
		jQuery("#submit_chexxl_conversion_form").on('click', submit_chexxl_conversion_form);
	});

});
