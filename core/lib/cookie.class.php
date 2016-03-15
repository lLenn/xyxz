<?php
class Cookie
{
	const LOGIN = 'ChSh_Login';
	const PWD =  'ChSh_Pwd'; 
	
    public static function register($variable, $value, $expiration = '900')
    {
        setcookie($variable, $value, time() + $expiration);
        $_COOKIE[$variable] = $value;
    }

    public static function unregister($variable)
    {
        setcookie($variable, "", time() - 3600);
        $_COOKIE[$variable] = null;
        unset($GLOBALS[$variable]);
    }

    public static function destroy()
    {
        $cookies = $_COOKIE;
        foreach ($cookies as $key => $value)
        {
            setcookie($key, "", time() - 3600);
        }
        $_COOKIE = array();
    }

    public static function retrieve($variable)
    {
		if(self::is_set($variable))
       		return $_COOKIE[$variable];
		else
			return null;
    }

    public static function get_user_id()
    {
        return self :: retrieve('_uid');
    }
    
    public static function register_user_id($uid)
    {
    	self :: register('_uid', $uid);
    }
    
    public static function unregister_user_id()
    {
    	self :: unregister('_uid');
    }
	
	public static function is_set($variable)
	{
		return isset($_COOKIE[$variable]);
	}
}
?>