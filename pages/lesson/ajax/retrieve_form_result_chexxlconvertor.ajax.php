<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/lesson/lib/lesson_manager.class.php';
require_once Path :: get_path() . 'pages/game/lib/game_manager.class.php';

if (Session :: get_user_id())
{
    $usermgr = new UserManager(Session :: get_user_id());
    $user = $usermgr->get_user();

    if(!empty($_POST))
    {
    	$title = Request::post("title");
    	$text = Request::post("text");
    	$game_ids = Request::post("game_ids");   	
    	$errors = array();
    	
    	if($title == "" || $text == "")
    	{
    		$errors[] = "Gelieve alle verplichte velden in te vullen!";
    	}
    	else
    	{
    		$gm = new GameManager($user);
			$lm = new LessonManager($user);
    		
    		$texts = array();
    		$index = strpos($text, "[");
    		while($index !== false)
    		{
    			$last_index = strpos($text, "]");
    			$first_text = substr($text, 0, $index);
    			
    			if(trim($first_text)!="")
    			{
	    			$p_text = strrpos($first_text, "<p>");
	    			$pb_text = strrpos($first_text, "</p>");
	    			if($p_text !== false && (($pb_text === false && $p_text !== false) || ($p_text !== false && $pb_text !== false && $p_text > $pb_text)))
	    				$first_text = substr($first_text, 0, $p_text - 2);
	    			
	    			if(trim(strip_tags(str_replace("&nbsp;", " ", $first_text))) != "")
	    				$texts[] = $first_text;
    			}
    			$texts[] = substr($text, $index, $last_index - $index + 1);
    			
    			$second_text = substr($text, $last_index + 1);
    			$p_sec_text = strpos($second_text, "<p>");
    			$pb_sec_text = strpos($second_text, "</p>");
    			if($pb_sec_text !== false && trim(strip_tags(str_replace("&nbsp;", " ", substr($second_text, 0, $pb_sec_text)))) == "" && (($pb_sec_text !== false && $p_sec_text === false) || ($p_sec_text !== false && $pb_sec_text !== false && $p_sec_text > $pb_sec_text)))
    			{
    				$text = substr($second_text, $pb_sec_text + 4);
    			}
    			else
    				$text = $second_text;
    				
    			$index = strpos($text, "[");
    		}
    		if(trim(strip_tags(str_replace("&nbsp;", " ", $text))) != "")
    			$texts[] = $text;

    		$pages = array();
    		$length = count($texts);
    		$index_order_page = null;
    		$order_urls = array();
    		$index_page = 0;
    		foreach($texts as $index => $text)
    		{
    			if($text != "")
    			{
    				if($text{0} != "[")
    				{
    					$data = array();
    					$data["id"] = 0;
    					$data["text"] = addslashes($text);
    					$page_text = new LessonPageText($data);
    					if($index+1!=$length && $texts[$index+1]{0}=="[" && strpos($texts[$index+1], "index"))
    					{
    						$index_order_page = $index_page;
    					}
    					
    					$page_data = array();
    					$page_data["id"] = 0;
    					$page_data["lesson_id"] = 0;
    					$page_data["title"] = $title;
    					$page_data["order"] = $index_page + 1;
    					$page_data["type"] = LessonPage::TEXT_TYPE;
    					$page_data["type_object_id"] = 0;
    					$page_data["next"] = 0;
    					$page = new LessonPage($page_data);
    					
    					$pages[] = array($page, $page_text);
    					$index_page++;
    				}
    				elseif(strpos($text, "chexxl_"))
    				{
    					preg_match("/nr[ ]*=[ ]*\"[ ]*[0-9]+[ ]*\"/", $text, $game_nr);
    					if(count($game_nr))
    					{
    						preg_match('/[0-9]+/', $game_nr[0], $game_nr);
    						$game_nr = intval($game_nr[0]);
    						
    						if(isset($game_ids[$game_nr-1]))
    						{
    							$meta_data = $gm->get_data_manager()->retrieve_game_meta_data($game_ids[$game_nr-1], array("White", "Black"));
    							$white = "";
    							$black = "";
    							foreach($meta_data as $data)
    							{
    								if($data->get_key() == "White")
    									$white = $data->get_value();
    								elseif($data->get_key() == "Black")
    									$black = $data->get_value();
    							}
    							if($white == "")
    								$title_game = $black;
    							elseif($black == "")
    								$title_game = $white;
    							else
    								$title_game = $white . " - " . $black;
    							
    							$gp = new GameProperties();
    							$gp->set_game_id($game_ids[$game_nr-1]);
    							$gp->set_title($title_game);
    							$gm->get_data_manager()->insert_game_properties($gp);
    							
    							$order_urls[] = array($title_game, $index_page + 1);
    								
	    						$page_data = array();
		    					$page_data["id"] = 0;
		    					$page_data["lesson_id"] = 0;
		    					$page_data["title"] = addslashes($title_game);
		    					$page_data["order"] = $index_page + 1;
		    					$page_data["type"] = LessonPage::GAME_TYPE;
		    					$page_data["type_object_id"] = $game_ids[$game_nr-1];
		    					$page_data["next"] = 0;
		    					$page = new LessonPage($page_data);
		    					
		    					$pages[] = array($page);
		    					$index_page++;
    						}
    						else
    						{
			    				$errors[] = "Chexxl_game-tag partij bestaat niet: " . $text;
			    			}	
    					}
		    			else
		    			{
		    				$errors[] = "Chexxl_game-tag verkeerd geformateerd: " . $text;
		    			}
	    			}
    			}
    		}
    		
    		if(empty($errors))
    		{
	    		$lesson = new Lesson();
	    		$lesson->set_id(0);
	    		$lesson->set_title($title);
	    		$lesson->set_description(Utilities::truncate_string(preg_replace("/<[^>]*>/", "", $pages[0][1]->get_text())));
	    		$lesson->set_rating(1500);
	    		$lesson->set_theme_ids(array());
	    		$lesson->set_order($lm->get_data_manager()->get_new_order());
	    		$lesson->set_user_id($user->get_id());
	    		$lesson->set_user_ids(array());
	    		$lesson->set_visible(0);
	    		$lesson->set_new(1);
	    		$lesson->set_criteria_lesson_excercise_ids(array());
	    		$lesson->set_criteria_lesson_excercise_percentage(0);
	    		$lesson->set_criteria_lesson_id(0);
	    		$lesson->set_criteria_lesson_percentage(0);
	    		
	    		$l_id = $lm->get_data_manager()->insert_lesson($lesson);
	    		
	    		$map_rel = new CustomProperties();
				$map_rel->add_property("map_id", 0);
				$map_rel->add_property("object_id", $l_id);
				$map_rel->add_property("user_id", $user->get_id());
				$map_rel->add_property("location_id", RightManager::LESSON_LOCATION_ID);
				RightDataManager::instance(null)->insert_location_user_map_relation($map_rel);
	    		
	    		if(!is_null($index_order_page) && is_numeric($index_order_page) && count($order_urls))
	    		{
		    		$order_txt = "<p><ul style='line-height: 1.3em;'>";
		    		foreach($order_urls as $url)
		    		{
		    			$order_txt .= "<li><a href='" . Url::create_url(array("page" => "view_lesson", "id" => $l_id, "page_nr" => $url[1])) . "' class='inline'>" . $url[0] . "</a></li>";
		    		}
		    		$order_txt .= "</ul></p>";
		    		$pages[$index_order_page][1]->set_text($pages[$index_order_page][1]->get_text() . $order_txt);
	    		}
	    		
	    		foreach($pages as $page)
	    		{
	    			if($page[0]->get_type() == LessonPage::TEXT_TYPE)
	    			{
	    				$lpt_id = $lm->get_data_manager()->insert_lesson_page_text($page[1]);
	    				$page[0]->set_type_object_id($lpt_id);
	    			}
	    			
	    			$page[0]->set_lesson_id($l_id);
	    			$lm->get_data_manager()->insert_lesson_page($page[0]);
	    		}
    		}
    	}
    	
    	if(!empty($errors))
    	{
    		echo Display::display_message(implode("<br>", $errors), "error");
    	}
    	else
    		echo "1";
    }    
}

?>