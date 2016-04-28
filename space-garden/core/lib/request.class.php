<?php
class Request
{

    static function get($variable)
    {
        if (isset($_GET[$variable]))
        {
            $value = $_GET[$variable];
            // TODO: Add the necessary security filters if and where necessary
            //$value = Security :: remove_XSS($value);
            return $value;
        }
        else
        {
            return null;
        }
    }

    static function set_get($variable, $value)
    {
        $_GET[$variable] = $value;
    }
    
    static function set_post($variable, $value)
    {
        $_POST[$variable] = $value;
    }

    static function post($variable)
    {
        if (isset($_POST[$variable]))
        {
            $value = $_POST[$variable];
            // TODO: Add the necessary security filters if and where necessary
            return $value;
        }
        else
        {
            return null;
        }
    }

    static function server($variable)
    {
        if (isset($_SERVER[$variable]))
        {
            $value = $_SERVER[$variable];
            return $value;
        }
        else
        {
            return null;
        }
    }

    static function file($variable)
    {
	    if (isset($_FILES[$variable]))
        {
            $value = $_FILES[$variable];
            // TODO: Add the necessary security filters if and where necessary
            return $value;
        }
        else
        {
            return null;
        }
    }

    static function environment($variable)
    {
        $value = $_ENV[$variable];
        // TODO: Add the necessary security filters if and where necessary
        return $value;
    }
}
?>