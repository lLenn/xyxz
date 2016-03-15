<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/game/lib/game_manager.class.php';

if (Session :: get_user_id())
{
    $usermgr = new UserManager(Session :: get_user_id());
    $user = $usermgr->get_user();

    if(!empty($_POST))
    {
    	$game_ids = Request::post("game_ids");
    	
    	if(!empty($game_ids))
    	{
    		$gm = new GameManager($user);
    		foreach($game_ids as $id)
    		{
    			if(is_numeric($id) && RightManager::instance()->get_right_location_object("Game", $user, $id) == RightManager::UPDATE_RIGHT)
    			{
    				$gm->get_data_manager()->delete_game($id);
    				RightManager::instance()->delete_location_object_user_rights("Game", $id);
    			}
    		}
    	}
    }
    echo "1";
}

?>