<?php

class Display
{
	const MESSAGE_SUCCESS = "good";
	const MESSAGE_ERROR = "error";
	const MESSAGE_INFO = "info";
	
	public static function get_header($pupil = false)
	{	
		$html = array();
		$html[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
        $html[] = '<html xmlns="http://www.w3.org/1999/xhtml">';
        $html[] = '<head>';
		$html[] = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />';
		$html[] = '<title>Schaakschool</title>';
		$html[] = '<link rel="shortcut icon" href="'.Path::get_url_path().'layout/images/logo.ico">';
		$html[] = '<link rel="stylesheet" type="text/css" href="'.Path::get_url_path().'layout/' . (/*$pupil*/false?'pupil_layout/':'') . 'merged_layout.css" />';
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'plugins/merged/merged_plugins_v1_1.js"></script>';
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'plugins/tiny_mce/tiny_mce.js"></script>';
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'core/javascript/merged_core_javascript.js"></script>';
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'pages/right/javascript/review.js"></script>';
		if(true /*$pupil*/)
		{
			$html[] = '<script type="text/javascript">';
			$html[] = ' table_color = \'#D0DF99\';';
			$html[] = ' table_odd_color = \'#E3EDC4\';';
			$html[] = ' root_url = "' . Path::get_url_path() . '";';
			$html[] = '</script>';
		}
		$html[] = '</head>';
		$html[] = '<body>';
		return implode("\n", $html);
	}
	
	public static function get_footer()
	{
		$html = array();
		$html[] = '<br class="clearfloat"/>';
		$html[] = '</body>';
        $html[] = '</html>';
		return implode("\n", $html);
	}
		
	public static function render_error_page($error)
	{
		$html = array();
		$html[] = self::get_header();
		$html[] = self::display_message($error, self::MESSAGE_ERROR);
		$html[] = self::get_footer();
		echo implode("\n", $html);
	}
	
	public static function get_message($width = null)
	{
		$message = Request::get("message");
		$message_type = Request::get("message_type");
		if(!is_null($message) && !is_null($message_type))
		{
			if(is_string($message) && is_string($message_type) && ($message_type == self::MESSAGE_SUCCESS || $message_type == self::MESSAGE_ERROR || $message_type == self::MESSAGE_INFO))
			{
				return self::display_message($message, $message_type, $width);
			}
			elseif(!is_string($message))
				throw new Exception("Message is not a valid string");
			else
				throw new Exception("Messagetype doesn't exist.");
		}
	}
	
    public static function display_message($message, $message_type, $width = null)
    {
        return '<p class="' . $message_type . '" ' . (!is_null($width)?'style="width:' . $width . 'px"':''). '>' . (is_numeric($message)?Language::get_instance()->translate($message):$message) . '</p>';
    }
    

    public static function display_icon($type, $tooltip = "", $class = "", $url = "")
    {
    	$image = "";
    	switch($type)
    	{
    		case "up":
    		case "down":
    		case "edit":
    		case "delete":
    		case "change_map":
    			$image = "icons/mappenbeheer.png";
    			break;
    		default: $image = $type;
    	}
    	
    	$x = 0;
    	switch($type)
    	{
    		case "up": $x = 44; break;
    		case "down": $x = 66; break;
    		case "edit": $x = 22; break;
    		case "delete": $x = 88; break;
    		case "change_map": $x = 0; break;
    		default: $x = 0;
    	}
    	
        return '<div style="display: inline;"><div style="float: left; width: 20px; height: 20px; overflow: hidden;"' . ($tooltip!=""?' title="' . $tooltip . '"':''). '>' . ($url!=""?'<a href="' . $url . '">':'') . '<img ' . ($class!=""?'class="' . $class . '" ':'') . 'style="border: none; margin-left: -' . $x . 'px" src="' . Path::get_url_path() . 'layout/images/' . $image . '"/>' . ($url?'</a>':'') . '</div></div>';
    }
}

?>