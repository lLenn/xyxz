<?php
class Session
{

    static function start()
    {
        //$session_key = Configuration :: get_instance()->get_parameter('general', 'security_key');
        //if (is_null($session_key))
          //  $session_key = 'dk_sid';
        
        //session_name($session_key);
        session_start();
    }

    static function register($variable, $value)
    {
        $_SESSION[$variable] = $value;
    }

    static function unregister($variable)
    {
        $_SESSION[$variable] = null;
        unset($GLOBALS[$variable]);
    }

    static function clear()
    {
        session_regenerate_id();
        session_unset();
        $_SESSION = array();
    }

    static function destroy()
    {
        session_unset();
        $_SESSION = array();
        session_destroy();
    }

    static function retrieve($variable)
    {
		if(isset($_SESSION[$variable]))
        	return $_SESSION[$variable];
		else return null;
    }

    static function get_user_id()
    {
        return self :: retrieve('_uid');
    }
    
   	static function register_user_id($id)
    {
        self :: register('_uid', $id);
    }
    
   	static function unregister_user_id()
    {
        self :: unregister('_uid');
    }
}
?>