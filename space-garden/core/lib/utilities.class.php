<?php

class Utilities
{
    private static $camel_us_map = array();
	private static $search = array("á", "é", "í", "ó", "ú", "ñ", "ç", "Á", "É", "Í", "Ó", "Ú", "Ñ", "Ç", "à", "è", "ì", "ò", "ù", "À", "È", "Ì", "Ò", "Ù",
 "ä", "ë", "ï", "ö", "ü", "Ä", "Ë", "Ï", "Ö", "Ü", "â", "ê", "î", "ô", "û", "Â", "Ê", "Î", "Ô", "Û");
 	private static $replace = array("&aacute;", "&eacute;", "&iacute;", "&oacute;", "&uacute;", "&ntilde;", "&ccedil;", "&Aacute;", 
"&Eacute;", "&Iacute;", "&Oacute;", "&Uacute;", "&Ntilde;", "&Ccedil;", "&agrave;", "&egrave;", "&igrave;", "&ograve;",
 "&ugrave;", "&Agrave;", "&Egrave;", "&Igrave;", "&Ograve;", "&Ugrave;", "&auml;", "&euml;", "&iuml;", "&ouml;", 
"&uuml;", "&Auml;", "&Euml;", "&Iuml;", "&Ouml;", "&Uuml;", "&acirc;", "&ecirc;", "&icirc;", "&ocirc;", "&ucirc;", "&Acirc;", 
"&Ecirc;", "&Icirc;", "&Ocirc;", "&Ucirc;");
 
	
    
    /**
     * function truncate_string()
     * 		Strips tags and truncates a given string to be the given length if the string is longer.
     * 		Adds a character at the end (either specified or default ...)
     * 		Boolean $strip to indicate if the string has to be stripped
     * 		@param string $string
     * 		@param int $length
     * 		@param boolean $strip
     * 		@param char $char
     * 		@return string
     */
    static function truncate_string($string, $length = 200, $strip = true, $char = '&hellip;')
    {
        if ($strip)
        {
            $string = strip_tags($string);
        }

        $decoded_string = html_entity_decode($string);
        if (strlen($decoded_string) >= $length)
        {
            mb_internal_encoding("UTF-8");
            $string = mb_substr($string, 0, $length - 3) . $char;
        }

        return $string;
    }
    

    /**
     * function camelcase_to_underscores()
     * 		Converts the given CamelCase string to under_score notation.
     * 		@param: $string: the string to be converted.
     *      @return: string: the string in under_score notation.
     */
    static function camelcase_to_underscores($string)
    {
        if (! isset(self :: $camel_us_map[$string]))
        {
            self :: $camel_us_map[$string] = preg_replace(array('/^([A-Z])/e', '/([A-Z])/e'), array('strtolower("\1")', '"_".strtolower("\1")'), $string);
        }
        return self :: $camel_us_map[$string];
    }
    
    /**
     * function html_special_characters()
     * 		Converts special characters to corresponding html entity
     * 		@param: $string: the string to be converted
     * 		@return: string: the string with html entities
     */
    static function html_special_characters($string)
    {
    	return str_replace(self :: $search, self :: $replace, $string);
    }
}
?>