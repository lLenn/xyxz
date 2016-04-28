(function($)
{
	jQuery.fn.limited_text_field = function (length) {
		
		function getCaret(el) 
		{
			  if (el.selectionStart) {
			    return el.selectionStart;
			  } else if (document.selection) {
			    el.focus();

			    var r = document.selection.createRange();
			    if (r == null) {
			      return 0;
			    }

			    var re = el.createTextRange(),
			        rc = re.duplicate();
			    re.moveToBookmark(r.getBookmark());
			    rc.setEndPoint('EndToStart', re);

			    return rc.text.length;
			  }
			  return 0;
		}
		
		function setSelectionRange(input, selectionStart, selectionEnd)
		{
			  if (input.setSelectionRange)
			  {
			    input.focus();
			    input.setSelectionRange(selectionStart, selectionEnd);
			  }
			  else if (input.createTextRange)
			  {
			    var range = input.createTextRange();
			    range.collapse(true);
			    range.moveEnd('character', selectionEnd);
			    range.moveStart('character', selectionStart);
			    range.select();
			  }
		}
		
		function limit_textarea(event)
		{ 
			var elem = $(this);
			if (elem.val().length > length)
			{
				var pos = getCaret(this);
				elem.val(elem.val().substring(0, length));
				setSelectionRange(this, pos, pos);
			}
		}

		$(this).live('keydown', limit_textarea);
		$(this).live('keyup', limit_textarea);
	};
})(jQuery);