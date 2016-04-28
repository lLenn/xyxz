<?php
class Path
{
	const LOCALHOST = "http://localhost/space-garden/";
    private static $sys_path;
    private static $url_path;

    private function init_paths()
    {
    	if (! self :: $sys_path)
    	{
        	self :: $sys_path = self :: $url_path = realpath(dirname(__FILE__) . '/../../') . '/';
        	if(self :: $sys_path == "C:\wamp\www\space-garden/")
        		self :: $url_path = "http://localhost/space-garden/";
        	elseif(self :: $sys_path == "/var/www/vhosts/space-garden.fr/httpdocs/")
        		self :: $url_path = "http://www.space-garden.fr/";
    	}
    }
    
    public static function get_path()
    {
    	self :: init_paths();
        return self :: $sys_path;
    }
    
    public static function get_url_path()
    {
    	self :: init_paths();
    	return self :: $url_path;
    }
    
	function get_location_url()
	{
		$location_url = 'http';
	 	if (!is_null(Request::server("HTTPS")) && Request::server("HTTPS") == "on") 
	 	{
	 		$location_url .= "s";
	 	}
	 	$location_url .= "://";
	 	if (!is_null(Request::server("SERVER_PORT")) && Request::server("SERVER_PORT") != "80") 
	 	{
	  		$location_url .= Request::server("SERVER_NAME").":".Request::server("SERVER_PORT").Request::server("REQUEST_URI");
	 	} 
	 	else 
	 	{
		  	$location_url .= Request::server("SERVER_NAME").Request::server("REQUEST_URI");
		}
	 	return $location_url;
	}
}
?>