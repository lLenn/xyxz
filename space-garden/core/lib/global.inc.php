<?php 

require_once Path::get_path().'core/lib/data/data_manager.class.php';
require_once Path::get_path().'pages/client/lib/client_manager.class.php';
//require_once Path::get_path().'pages/right/lib/right_manager.class.php';
//require_once Path::get_path().'pages/statistics/lib/statistics_manager.class.php';
require_once Path::get_path().'core/lib/web_application.class.php';
require_once Path::get_path().'core/lib/session.class.php';
require_once Path::get_path().'core/lib/cookie.class.php';
require_once Path::get_path().'core/lib/hashing.class.php';
require_once Path::get_path().'core/lib/request.class.php';
require_once Path::get_path().'core/lib/display.class.php';
require_once Path::get_path().'core/lib/alias.class.php';
require_once Path::get_path().'core/lib/url.class.php';
require_once Path::get_path().'core/lib/utilities.class.php';
require_once Path::get_path().'core/lib/country.class.php';
require_once Path::get_path().'core/lib/settings/setting.class.php';
require_once Path::get_path().'core/lib/language/language.class.php';
require_once Path::get_path().'core/lib/menu/menu_renderer.class.php';
require_once Path::get_path().'core/lib/html/table.class.php';
require_once Path::get_path().'pages/dynamic_page/lib/dynamic_page_manager.class.php';

// Start session
Session :: start();

// Handle login and logout
/*
if (!isset($_SESSION['CREATED'])) 
{
    $_SESSION['CREATED'] = time();
} 
else if (time() - $_SESSION['CREATED'] > 1800) 
{
    // session started more than 30 minutes ago
    session_destroy();
    $_SESSION = array();
}
*/

// Login
if ((REQUEST::post('login') || ( Cookie::retrieve(Cookie::LOGIN) && Cookie::retrieve(Cookie::PWD))) && is_null(Session::get_user_id()))
{
	$manager = new ClientManager();
	$user = null;
	if(REQUEST::post('login'))
    	$user = $manager->login(Request::post('login'), Request::post('password'));
	else
		$user = $manager->login(Cookie :: retrieve(Cookie::LOGIN), Cookie :: retrieve(Cookie::PWD));
		
    if (is_object($user) && get_class($user) == 'ClientUserAccount')
    {
        Session :: register_user_id($user->get_id());
        //StatisticsManager::instance()->register_login();
		if(REQUEST::post('login') && Request::post("save")=="1")
		{
			Cookie :: register(Cookie::LOGIN,Request::post('login'),86400*7);
			Cookie :: register(Cookie::PWD, Request::post('password'),86400*7);
		}
		elseif(REQUEST::post('login'))
		{
			Cookie :: unregister(Cookie::LOGIN);
			Cookie :: unregister(Cookie::PWD);
		}
		
		if(REQUEST::post('login'))
		{
			if($user->is_admin())
			{
				header("location: " . Url::create_url(array("page" => "browse_pages")));
				exit;
			}
			else
			{
				header("location: " . Url::create_url(array("page" => "client_corner")));
				exit;
			}
		}
    }
    else
    {
        Session :: unregister_user_id();
    }
}

// Log out
if (REQUEST::get('action') == 'logout' || REQUEST::get('page')=='logout')
{
    $user_id = Session :: get_user_id();
    if (isset($user_id))
    {
        Session :: destroy();
		Cookie :: unregister(Cookie::LOGIN);
		Cookie :: unregister(Cookie::PWD);
    }
	header("Location: " . Url :: create_url());
	exit;
}

// Dump functionality with decent output
function dump($variable)
{
    echo '<pre style="background-color: white; color: black; padding: 5px; margin: 0px;">';
    print_r($variable);
    echo '</pre>';
}
?>