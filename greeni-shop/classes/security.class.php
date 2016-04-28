<?php

class Security
{
	private static $search  = array('<script', '</script>', 'onunload', 'onclick', 'onload');
	private static $replace = array('&lt;script', '&lt;\script&gt;', '', '', '');

	public static function remove_XSS($variable)
	{
		// TODO: Should this be UTF-8 by default ?
		// return htmlentities($variable, ENT_QUOTES, 'UTF-8');

		if (is_array($variable))
		{
			return self :: remove_XSS_recursive($variable);
		}

		return str_ireplace(self::$search, self::$replace, $variable);
	}

	public static function remove_XSS_recursive($array)
	{
		foreach ($array as $key => $value)
		{
			$key2 = self :: remove_XSS($key);
			$value2 = (is_array($value)) ? self :: remove_XSS_recursive($value) : self :: remove_XSS($value);

			unset($array[$key]);
			$array[$key2] = $value2;
		}
		return $array;
	}
}
?>
