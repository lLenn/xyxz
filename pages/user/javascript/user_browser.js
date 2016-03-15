jQuery(function() 
{
	var added = false;
	var loaded = false;
	var lesson_excercise_added = false;
	var lesson_excercise_loaded = false;
	var puzzle_added = false;
	var puzzle_loaded = false;
	var puzzle_set_added = false;
	var puzzle_set_loaded = false;
	var end_game_added = false;
	var end_game_loaded = false;
	var game_added = false;
	var game_loaded = false;
	var question_added = false;
	var question_loaded = false;
	var question_set_added = false;
	var question_set_loaded = false;
	var video_added = false;
	var video_loaded = false;
	
	function submit_lesson_ajax(event)
	{
		if(!added)
		{
			jQuery("#add_lesson_ajax").attr("src", "layout/images/loading.gif");
			jQuery("#add_lesson_ajax").css("cursor", "default");
			added = true;
			user_id = jQuery("input[name=user_id]").val();
			jQuery.post("pages/user/ajax/lesson_table.ajax.php", 
					{user_id : user_id}, 
					function(data) 
					{
						jQuery("#add_lesson_ajax").attr("src", "layout/images/buttons/add.png");
						jQuery("#add_lesson_ajax").css("cursor", "pointer");
						jQuery("#lesson_title_ajax").css("display", "none");
						jQuery("#lesson_block_ajax").append(data);
						jQuery("#remove_lesson_ajax").on("click", remove_lesson_ajax);
						jQuery("#remove_lesson_ajax").css("cursor", "pointer");
						loaded = true;
					}
				);
		}
		else if(loaded)
		{
			jQuery("#lesson_block_ajax").css("display", "block");
			jQuery("#lesson_title_ajax").css("display", "none");
		}
	}
	
	function remove_lesson_ajax(event)
	{
		jQuery("#lesson_block_ajax").css("display", "none");
		jQuery("#lesson_title_ajax").css("display", "block");
	}
	
	function submit_lesson_excercise_ajax(event)
	{
		if(!lesson_excercise_added)
		{
			jQuery("#add_lesson_excercise_ajax").attr("src", "layout/images/loading.gif");
			jQuery("#add_lesson_excercise_ajax").css("cursor", "default");
			lesson_excercise_added = true;
			user_id = jQuery("input[name=user_id]").val();
			jQuery.post("pages/user/ajax/lesson_excercise_table.ajax.php", 
					{user_id : user_id}, 
					function(data) 
					{
						jQuery("#add_lesson_excercise_ajax").attr("src", "layout/images/buttons/add.png");
						jQuery("#add_lesson_excercise_ajax").css("cursor", "pointer");
						jQuery("#lesson_excercise_title_ajax").css("display", "none");
						jQuery("#lesson_excercise_block_ajax").append(data);
						jQuery("#remove_lesson_excercise_ajax").on("click", remove_lesson_excercise_ajax);
						jQuery("#remove_lesson_excercise_ajax").css("cursor", "pointer");
						lesson_excercise_loaded = true;
					}
				);
		}
		else if(lesson_excercise_loaded)
		{
			jQuery("#lesson_excercise_block_ajax").css("display", "block");
			jQuery("#lesson_excercise_title_ajax").css("display", "none");
		}
	}
	
	function remove_lesson_excercise_ajax(event)
	{
		jQuery("#lesson_excercise_block_ajax").css("display", "none");
		jQuery("#lesson_excercise_title_ajax").css("display", "block");
	}
	
	function submit_puzzle_ajax(event)
	{
		if(!puzzle_added)
		{
			jQuery("#add_puzzle_ajax").attr("src", "layout/images/loading.gif");
			jQuery("#add_puzzle_ajax").css("cursor", "default");
			puzzle_added = true;
			user_id = jQuery("input[name=user_id]").val();
			jQuery.post("pages/user/ajax/puzzle_table.ajax.php", 
					{user_id : user_id}, 
					function(data) 
					{
						jQuery("#add_puzzle_ajax").attr("src", "layout/images/buttons/add.png");
						jQuery("#add_puzzle_ajax").css("cursor", "pointer");
						jQuery("#puzzle_title_ajax").css("display", "none");
						jQuery("#puzzle_block_ajax").append(data);
						jQuery("#remove_puzzle_ajax").on("click", remove_puzzle_ajax);
						jQuery("#remove_puzzle_ajax").css("cursor", "pointer");
						jQuery(".puzzle_link").get_puzzle_thumbs();
						jQuery("#explorer").click(function(){
							jQuery(".puzzle_link").get_puzzle_thumbs();
						});
						puzzle_loaded = true;
					}
				);
		}
		else if(puzzle_loaded)
		{
			jQuery("#puzzle_block_ajax").css("display", "block");
			jQuery("#puzzle_title_ajax").css("display", "none");
		}
	}
	
	function remove_puzzle_ajax(event)
	{
		jQuery("#puzzle_block_ajax").css("display", "none");
		jQuery("#puzzle_title_ajax").css("display", "block");
	}
	
	function submit_puzzle_set_ajax(event)
	{
		if(!puzzle_set_added)
		{
			jQuery("#add_puzzle_set_ajax").attr("src", "layout/images/loading.gif");
			jQuery("#add_puzzle_set_ajax").css("cursor", "default");
			puzzle_set_added = true;
			user_id = jQuery("input[name=user_id]").val();
			jQuery.post("pages/user/ajax/puzzle_set_table.ajax.php", 
					{user_id : user_id}, 
					function(data) 
					{
						jQuery("#add_puzzle_set_ajax").attr("src", "layout/images/buttons/add.png");
						jQuery("#add_puzzle_set_ajax").css("cursor", "pointer");
						jQuery("#puzzle_set_title_ajax").css("display", "none");
						jQuery("#puzzle_set_block_ajax").append(data);
						jQuery("#remove_puzzle_set_ajax").on("click", remove_puzzle_set_ajax);
						jQuery("#remove_puzzle_set_ajax").css("cursor", "pointer");
						puzzle_set_loaded = true;
					}
				);
		}
		else if(puzzle_set_loaded)
		{
			jQuery("#puzzle_set_block_ajax").css("display", "block");
			jQuery("#puzzle_set_title_ajax").css("display", "none");
		}
	}
	
	function remove_puzzle_set_ajax(event)
	{
		jQuery("#puzzle_set_block_ajax").css("display", "none");
		jQuery("#puzzle_set_title_ajax").css("display", "block");
	}
	
	function submit_end_game_ajax(event)
	{
		if(!end_game_added)
		{
			jQuery("#add_end_game_ajax").attr("src", "layout/images/loading.gif");
			jQuery("#add_end_game_ajax").css("cursor", "default");
			end_game_added = true;
			user_id = jQuery("input[name=user_id]").val();
			jQuery.post("pages/user/ajax/end_game_table.ajax.php", 
					{user_id : user_id}, 
					function(data) 
					{
						jQuery("#add_end_game_ajax").attr("src", "layout/images/buttons/add.png");
						jQuery("#add_end_game_ajax").css("cursor", "pointer");
						jQuery("#end_game_title_ajax").css("display", "none");
						jQuery("#end_game_block_ajax").append(data);
						jQuery("#remove_end_game_ajax").on("click", remove_end_game_ajax);
						jQuery("#remove_end_game_ajax").css("cursor", "pointer");
						end_game_loaded = true;
					}
				);
		}
		else if(end_game_loaded)
		{
			jQuery("#end_game_block_ajax").css("display", "block");
			jQuery("#end_game_title_ajax").css("display", "none");
		}
	}
	
	function remove_end_game_ajax(event)
	{
		jQuery("#end_game_block_ajax").css("display", "none");
		jQuery("#end_game_title_ajax").css("display", "block");
	}
	
	function submit_game_ajax(event)
	{
		if(!game_added)
		{
			jQuery("#add_game_ajax").attr("src", "layout/images/loading.gif");
			jQuery("#add_game_ajax").css("cursor", "default");
			game_added = true;
			user_id = jQuery("input[name=user_id]").val();
			jQuery.post("pages/user/ajax/game_table.ajax.php", 
					{user_id : user_id}, 
					function(data) 
					{
						jQuery("#add_game_ajax").attr("src", "layout/images/buttons/add.png");
						jQuery("#add_game_ajax").css("cursor", "pointer");
						jQuery("#game_title_ajax").css("display", "none");
						jQuery("#game_block_ajax").append(data);
						jQuery("#remove_game_ajax").on("click", remove_game_ajax);
						jQuery("#remove_game_ajax").css("cursor", "pointer");
						game_loaded = true;
					}
				);
		}
		else if(game_loaded)
		{
			jQuery("#game_block_ajax").css("display", "block");
			jQuery("#game_title_ajax").css("display", "none");
		}
	}
	
	function remove_game_ajax(event)
	{
		jQuery("#game_block_ajax").css("display", "none");
		jQuery("#game_title_ajax").css("display", "block");
	}
	
	function submit_question_ajax(event)
	{
		if(!question_added)
		{
			jQuery("#add_question_ajax").attr("src", "layout/images/loading.gif");
			jQuery("#add_question_ajax").css("cursor", "default");
			question_added = true;
			user_id = jQuery("input[name=user_id]").val();
			jQuery.post("pages/user/ajax/question_table.ajax.php", 
					{user_id : user_id}, 
					function(data) 
					{
						jQuery("#add_question_ajax").attr("src", "layout/images/buttons/add.png");
						jQuery("#add_question_ajax").css("cursor", "pointer");
						jQuery("#question_title_ajax").css("display", "none");
						jQuery("#question_block_ajax").append(data);
						jQuery("#remove_question_ajax").on("click", remove_question_ajax);
						jQuery("#remove_question_ajax").css("cursor", "pointer");
						question_loaded = true;
					}
				);
		}
		else if(question_loaded)
		{
			jQuery("#question_block_ajax").css("display", "block");
			jQuery("#question_title_ajax").css("display", "none");
		}
	}
	
	function remove_question_ajax(event)
	{
		jQuery("#question_block_ajax").css("display", "none");
		jQuery("#question_title_ajax").css("display", "block");
	}
	
	function submit_question_set_ajax(event)
	{
		if(!question_set_added)
		{
			jQuery("#add_question_set_ajax").attr("src", "layout/images/loading.gif");
			jQuery("#add_question_set_ajax").css("cursor", "default");
			question_set_added = true;
			user_id = jQuery("input[name=user_id]").val();
			jQuery.post("pages/user/ajax/question_set_table.ajax.php", 
					{user_id : user_id}, 
					function(data) 
					{
						jQuery("#add_question_set_ajax").attr("src", "layout/images/buttons/add.png");
						jQuery("#add_question_set_ajax").css("cursor", "pointer");
						jQuery("#question_set_title_ajax").css("display", "none");
						jQuery("#question_set_block_ajax").append(data);
						jQuery("#remove_question_set_ajax").on("click", remove_question_set_ajax);
						jQuery("#remove_question_set_ajax").css("cursor", "pointer");
						question_set_loaded = true;
					}
				);
		}
		else if(question_set_loaded)
		{
			jQuery("#question_set_block_ajax").css("display", "block");
			jQuery("#question_set_title_ajax").css("display", "none");
		}
	}
	
	function remove_question_set_ajax(event)
	{
		jQuery("#question_set_block_ajax").css("display", "none");
		jQuery("#question_set_title_ajax").css("display", "block");
	}
	
	function submit_video_ajax(event)
	{
		if(!video_added)
		{
			jQuery("#add_video_ajax").attr("src", "layout/images/loading.gif");
			jQuery("#add_video_ajax").css("cursor", "default");
			video_added = true;
			user_id = jQuery("input[name=user_id]").val();
			jQuery.post("pages/user/ajax/video_table.ajax.php", 
					{user_id : user_id}, 
					function(data) 
					{
						jQuery("#add_video_ajax").attr("src", "layout/images/buttons/add.png");
						jQuery("#add_video_ajax").css("cursor", "pointer");
						jQuery("#video_title_ajax").css("display", "none");
						jQuery("#video_block_ajax").append(data);
						jQuery("#remove_video_ajax").on("click", remove_video_ajax);
						jQuery("#remove_video_ajax").css("cursor", "pointer");
						video_loaded = true;
					}
				);
		}
		else if(video_loaded)
		{
			jQuery("#video_block_ajax").css("display", "block");
			jQuery("#video_title_ajax").css("display", "none");
		}
	}
	
	function remove_video_ajax(event)
	{
		jQuery("#video_block_ajax").css("display", "none");
		jQuery("#video_title_ajax").css("display", "block");
	}
	
	jQuery(document).ready(function ()
	{
		if(jQuery("#add_lesson_ajax").length > 0)
		{
			jQuery("#add_lesson_ajax").on("click", submit_lesson_ajax);
			jQuery("#add_lesson_ajax").css("cursor", "pointer");
		}
		
		if(jQuery("#add_lesson_excercise_ajax").length > 0)
		{
			jQuery("#add_lesson_excercise_ajax").on("click", submit_lesson_excercise_ajax);
			jQuery("#add_lesson_excercise_ajax").css("cursor", "pointer");
		}

		if(jQuery("#add_puzzle_ajax").length > 0)
		{
			jQuery("#add_puzzle_ajax").on("click", submit_puzzle_ajax);
			jQuery("#add_puzzle_ajax").css("cursor", "pointer");
		}

		if(jQuery("#add_puzzle_set_ajax").length > 0)
		{
			jQuery("#add_puzzle_set_ajax").on("click", submit_puzzle_set_ajax);
			jQuery("#add_puzzle_set_ajax").css("cursor", "pointer");
		}
		
		if(jQuery("#add_game_ajax").length > 0)
		{
			jQuery("#add_game_ajax").on("click", submit_game_ajax);
			jQuery("#add_game_ajax").css("cursor", "pointer");
		}

		if(jQuery("#add_end_game_ajax").length > 0)
		{
			jQuery("#add_end_game_ajax").on("click", submit_end_game_ajax);
			jQuery("#add_end_game_ajax").css("cursor", "pointer");
		}

		if(jQuery("#add_question_ajax").length > 0)
		{
			jQuery("#add_question_ajax").on("click", submit_question_ajax);
			jQuery("#add_question_ajax").css("cursor", "pointer");
		}

		if(jQuery("#add_question_set_ajax").length > 0)
		{
			jQuery("#add_question_set_ajax").on("click", submit_question_set_ajax);
			jQuery("#add_question_set_ajax").css("cursor", "pointer");
		}

		if(jQuery("#add_video_ajax").length > 0)
		{
			jQuery("#add_video_ajax").on("click", submit_video_ajax);
			jQuery("#add_video_ajax").css("cursor", "pointer");
		}
	});

});