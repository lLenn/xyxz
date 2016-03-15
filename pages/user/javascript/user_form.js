var before = "";

jQuery(function() 
{ 
	function before(event)
	{
		if(event.keyCode == 13)
		{
			before = jQuery("input[name=login]").val();
		}
	}
	
	function after(event)
	{
		if(event.keyCode == 13)
		{
			var after = jQuery(this).val();
			if(before != after)
			{
				event.preventDefault();
				return false;
			}
			else
			{
				jQuery(this).parent().parent().parent().submit();
				return false;
			}
		}
	}
	
	function submit_form(event)
	{
		if(event.keyCode == 13)
			jQuery(this).parent().parent().parent().submit();
		return false;
	}
	
	
	function submit_request_form()
	{
		jQuery.post("pages/user/ajax/request_account.ajax.php", 
			jQuery("#request_form").serialize(), 
			function(data) 
        	{
				jQuery("#reg_ajax").empty();
				jQuery("#reg_ajax").css("margin-top", "0px");
				jQuery("#reg_ajax").append(data);
				jQuery(".error").css("width", "400px");
				Custom.init();
            }
		);
	}
	
	
	function make_buttons_disappear()
	{
		jQuery("#submit_form_alter").fadeOut('slow', function()
				{
					jQuery("#submit_form_alter").css('visibility', 'hidden');
					jQuery("#submit_form_alter").css('display', 'block');
				});
		if(jQuery("#submit_form_info").length > 0)
		{
			jQuery("#submit_form_info").fadeOut('slow', function()
					{
						jQuery("#submit_form_info").css('visibility', 'hidden');
						jQuery("#submit_form_info").css('display', 'block');
					});
			
			jQuery("#submit_form_reg").fadeOut('slow', function()
					{
						jQuery("#submit_form_reg").css('visibility', 'hidden');
						jQuery("#submit_form_reg").css('display', 'block');
					});
			
			jQuery("#submit_form_guest").fadeOut('slow', function()
					{
						jQuery("#submit_form_guest").css('visibility', 'hidden');
						jQuery("#submit_form_guest").css('display', 'block');
					});

			jQuery("#what_form_info").fadeIn('slow', function()
					{
						jQuery("#what_form_info").css('visibility', 'hidden');
					});

			jQuery("#how_form_info").fadeIn('slow', function()
					{
						jQuery("#how_form_info").css('visibility', 'hidden');
					});

			jQuery("#why_form_info").fadeIn('slow', function()
					{
						jQuery("#why_form_info").css('visibility', 'hidden');
					});

			jQuery("#adv_form_info").fadeIn('slow', function()
					{
						jQuery("#adv_form_info").css('visibility', 'hidden');
					});
		}
	}
	
	function make_form_appear(event)
	{
		make_buttons_disappear();
		jQuery("#login_form_div").fadeIn('slow', function()
				{
					jQuery("#login_form_div").css('visibility', '');
				});
	}

	function make_info_appear(event)
	{
		if(jQuery("#what_form_info").css("top") != '158px')
		{
			jQuery("#what_form_info").animate({top: '158px'}, 500);
			jQuery("#how_form_info").animate({top: '160px'}, 500);
			jQuery("#why_form_info").animate({top: '162px'}, 500);
			jQuery("#adv_form_info").animate({top: '164px'}, 500);
		}
		else
		{
			jQuery("#what_form_info").animate({top: '252px'}, 500);
			jQuery("#how_form_info").animate({top: '232px'}, 500);
			jQuery("#why_form_info").animate({top: '212px'}, 500);
			jQuery("#adv_form_info").animate({top: '192px'}, 500);
		}
	}
	
	function make_what_info_appear(event)
	{
		jQuery("#what_info_div").css('display', 'block');
		jQuery("#how_info_div").css('display', 'none');
		jQuery("#why_info_div").css('display', 'none');
		jQuery("#adv_info_div").css('display', 'none');
		show_info();
	}

	function make_how_info_appear(event)
	{
		jQuery("#what_info_div").css('display', 'none');
		jQuery("#how_info_div").css('display', 'block');
		jQuery("#why_info_div").css('display', 'none');
		jQuery("#adv_info_div").css('display', 'none');
		show_info();
	}

	function make_why_info_appear(event)
	{
		jQuery("#what_info_div").css('display', 'none');
		jQuery("#how_info_div").css('display', 'none');
		jQuery("#why_info_div").css('display', 'block');
		jQuery("#adv_info_div").css('display', 'none');
		show_info();
	}

	function make_adv_info_appear(event)
	{
		jQuery("#what_info_div").css('display', 'none');
		jQuery("#how_info_div").css('display', 'none');
		jQuery("#why_info_div").css('display', 'none');
		jQuery("#adv_info_div").css('display', 'block');
		show_info();
	}
	
	function show_info()
	{
		make_buttons_disappear();
		jQuery("#login_form_div").css('display', 'none');
		jQuery("#registration_div").css('display', 'none');
		jQuery("#information_div").css('display', 'block');
		jQuery("#information_div").fadeIn('slow', function()
				{
					jQuery("#information_div").css('visibility', '');
				});
	}

	
	function make_reg_appear(event)
	{
		make_buttons_disappear();
		jQuery("#login_form_div").css('display', 'none');
		jQuery("#information_div").css('display', 'none');
		jQuery("#registration_div").css('display', 'block');
		jQuery("#registration_div").fadeIn('slow', function()
				{
					jQuery("#registration_div").css('visibility', '');
				});
	}
	

	function login_guest(event)
	{
		_gaq.push(['_trackEvent', 'Guest',  'LoggedIn']);
		window.location.assign("index.php?guest=1");
	}
	
	function make_buttons_appear()
	{
		jQuery("#submit_form_alter").fadeIn('slow', function()
				{
					jQuery("#submit_form_alter").css('visibility', '');
				});
		
		if(jQuery("#submit_form_info").length > 0)
		{
			jQuery("#submit_form_info").fadeIn('slow', function()
					{
						jQuery("#submit_form_info").css('visibility', '');
					});
			
			jQuery("#submit_form_reg").fadeIn('slow', function()
					{
						jQuery("#submit_form_reg").css('visibility', '');
					});
			
			jQuery("#submit_form_guest").fadeIn('slow', function()
					{
						jQuery("#submit_form_guest").css('visibility', '');
					});
			
			jQuery("#what_form_info").fadeIn('slow', function()
					{
						jQuery("#what_form_info").css('visibility', '');
					});

			jQuery("#how_form_info").fadeIn('slow', function()
					{
						jQuery("#how_form_info").css('visibility', '');
					});

			jQuery("#why_form_info").fadeIn('slow', function()
					{
						jQuery("#why_form_info").css('visibility', '');
					});

			jQuery("#adv_form_info").fadeIn('slow', function()
					{
						jQuery("#adv_form_info").css('visibility', '');
					});
		}
	}
	
	function make_form_disappear(event)
	{
		jQuery("#information_div").fadeOut('slow', function()
				{
					jQuery("#information_div").css('visibility', 'hidden');
					jQuery("#registration_div").fadeOut('slow', function()
							{
								jQuery("#registration_div").css('visibility', 'hidden');
								jQuery("#login_form_div").fadeOut('slow', function()
										{
											jQuery("#login_form_div").css('visibility', 'hidden');
											jQuery("#login_form_div").css('display', 'block');
										});
							});
				});
		make_buttons_appear();
	}
	
	function submit_language_form()
	{
		document.language_form.submit();
	}

	function switch_test_account()
	{
		var checked = jQuery("#test_account").prop("checked");
		if(checked)
		{
			jQuery("#test_account_block").css("display", "none");
			jQuery("#test_account_group_block").css("display", "block");
		}
		else
		{
			jQuery("#test_account_block").css("display", "block");
			jQuery("#test_account_group_block").css("display", "none");
		}
	}
	
	jQuery(document).ready(function ()
	{
		if(jQuery("select[name='extra_parent_ids[]']").length > 0)
			jQuery("select[name='extra_parent_ids[]']").multiselect2side({
					selectedPosition: 'right',
					moveOptions: false,
					labelsx: '',
					labeldx: ''
					});
		if(jQuery("input[name=login]").length > 0)
		{
			jQuery("input[name=login]").keydown(before);
			jQuery("input[name=login]").keyup(after);
			jQuery("input[name=password]").keyup(submit_form);
			jQuery("#submit_form_alter").on("click", make_form_appear);
			jQuery(".close_form").on("click", make_form_disappear);
			jQuery(".close_form").css("cursor", "pointer");
		}
		if(jQuery("#submit_form_info").length > 0)
		{
			jQuery("#submit_form_info").on("click", make_info_appear);
			//jQuery("#submit_form_reg").on("click", make_reg_appear);
			jQuery("#submit_form_guest").on("click", login_guest);
			jQuery("#what_form_info").on("click", make_what_info_appear);
			jQuery("#how_form_info").on("click", make_how_info_appear);
			jQuery("#why_form_info").on("click", make_why_info_appear);
			jQuery("#adv_form_info").on("click", make_adv_info_appear);
		}
		if(jQuery("select[name=language]").length > 0)
		{
			jQuery("select[name=language]").on("change", submit_language_form);
		}
		
		if(jQuery("#submit_request_form").length > 0)
		{
			jQuery("#submit_request_form").on("click", submit_request_form);
		}
		
		if(jQuery("#test_account").length > 0)
		{
			jQuery("#test_account").on("click", switch_test_account);
		}
	});

});