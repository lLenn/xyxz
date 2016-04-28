<?php

class Display
{
	const MESSAGE_SUCCESS = "success";
	const MESSAGE_ERROR = "error";
	const MESSAGE_INFO = "info";
	
	public static function get_header()
	{
		$html = array();
		$html[] = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		$html[] = '<title>' . Language::get_instance()->translate("title") . '</title>';
		$html[] = '<link rel="stylesheet" type="text/css" href="'.Path::get_url_path().'layout/common_layout.css" />';
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'plugins/jquery/jquery-1.3.2.min.js"></script>';
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'plugins/jquery/jquery-ui-1.7.2.custom.min.js"></script>';
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'plugins/tiny_mce/jquery.tinymce.js"></script>';
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'plugins/serialize.js"></script>';
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'core/javascript/utilities.js"></script>';
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'core/javascript/menu.js"></script>';
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'core/javascript/general.js"></script>';
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'core/javascript/table.js"></script>';
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'core/javascript/feedback.js"></script>';
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'core/javascript/tiny_mce.js"></script>';
		return implode("\n", $html);
	}
	
	public static function render_page($sections)
	{
		$file = Setting::get_instance()->get_default_setting("master_template_location");
		$lines = file($file);
		foreach($lines as $line)
		{
			foreach($sections as $index => $value)
			{
				$line = preg_replace('/{'.$index.'}/', $value, $line);
			}
			echo $line;
		}
	}
	
	public static function render_error_page($error)
	{
		$html = array();
		$html[] = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">';
		$html[] = '<html xmlns="http://www.w3.org/1999/xhtml">';
		$html[] = '<head>';
		$html[] = self::get_header();
		$html[] = '</head>';
		$html[] = '<body>';
		$html[] = self::display_message($error, self::MESSAGE_ERROR);
		$html[] = '</body>';
		$html[] = '</html>';
		echo implode("\n", $html);
	}
	
	public static function get_message()
	{
		$message = Request::get("message");
		$message_type = Request::get("message_type");
		if(!is_null($message) && !is_null($message_type))
		{
			if(is_string($message) && is_string($message_type) && ($message_type == self::MESSAGE_SUCCESS || $message_type == self::MESSAGE_ERROR || $message_type == self::MESSAGE_INFO))
			{
				return self::display_message($message, $message_type);
			}
			elseif(!is_string($message))
				throw new Exception("Message is not a valid string");
			else
				throw new Exception("Messagetype doesn't exist.");
		}
	}
	
    public static function display_message($message, $message_type)
    {
        return '<p class="' . $message_type . '">' . $message . '</p>';
    }

}

?>