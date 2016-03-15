<?php

class PuzzleRenderer
{

	private $manager;
	
	function PuzzleRenderer($manager)
	{
		$this->manager = $manager;
	}
	
	public function get_actions()
	{
		$html = array();
		$html[] = '<ul class="menu menu_actions menu_vertical" id="menu_actions">';
		if(Alias::instance()->get_alias(Request::get("page")) == PuzzleManager :: PUZZLE_BROWSER)
		{
			$id = Request::get("id");
			if(!is_numeric($id))
				$id = null;
			if(RightManager::instance()->check_right_location(RightManager::WRITE_RIGHT, "Puzzle", $this->manager->get_user()))
			{
				if(Request::get("shop")==0 && GroupManager::group_is_not_test($this->manager->get_user()->get_group_id()) && !GroupManager::is_free_group($this->manager->get_user()->get_group_id()))
				{
					$html[] = '<li>';
					$html[] = '<a href="' . Url :: create_url(array('page' => 'shop', 'section' => 1)) . '" title="' . Language::get_instance()->translate(791) . '">' . Language::get_instance()->translate(791) . '</a>';
					$html[] = '</li>';
				}
				
				$html[] = '<li>';
				$html[] = '<a href="' . Url :: create_url(array('page' => 'add_puzzle')) . '" title="' . Language::get_instance()->translate(49) . '">' . Language::get_instance()->translate(262) . '</a>';
				$html[] = '</li>';

				if(!is_null($id) && RightManager::instance()->get_right_location_object("Puzzle", $this->manager->get_user(), $id) == RightManager::UPDATE_RIGHT)
				{
					$html[] = '<li>';
					$html[] = '<a href="' . Url :: create_url(array('page' => 'add_puzzle', 'id' => $id)) . '" title="' . Language::get_instance()->translate(50) . '">' . Language::get_instance()->translate(263) . '</a>';
					$html[] = '</li>';
				}
			}
		}
		/*
		if(Alias::instance()->get_alias(Request::get("page")) == PuzzleManager :: PUZZLE_SET_BROWSER)
		{
			$id = Request::get("id");
			$object_right = RightManager::NO_RIGHT;
			if(!is_numeric($id))
				$id = null;
			else
				$object_right = RightManager::instance()->get_right_location_object(RightManager::SET_LOCATION_ID, $this->manager->get_user(), $id);
				
			if(RightManager::instance()->check_right_location(RightManager::WRITE_RIGHT, "Puzzle", $this->manager->get_user()))
			{
				if(!is_null($id))
				{
					$html[] = '<li>';
					$html[] = '<a href="' . Url :: create_url(array('page' => 'view_puzzle_set', 'id' => $id)) . '" title="' . Language::get_instance()->translate(270) . '">' . Language::get_instance()->translate(264) . '</a>';
					$html[] = '</li>';
				}
			
				if(Request::get("shop")==0 && GroupManager::group_is_not_test($this->manager->get_user()->get_group_id()))
				{
					$html[] = '<li>';
					$html[] = '<a href="' . Url :: create_url(array('page' => 'shop', 'section' => 2)) . '" title="' . Language::get_instance()->translate(827) . '">' . Language::get_instance()->translate(827) . '</a>';
					$html[] = '</li>';
				}				
				$html[] = '<li>';
				$html[] = '<a href="' . Url :: create_url(array('page' => 'add_puzzle_set')) . '" title="' . Language::get_instance()->translate(49) . '">' . Language::get_instance()->translate(265) . '</a>';
				$html[] = '</li>';

				if(!is_null($id) && $object_right == RightManager::UPDATE_RIGHT)
				{
					$html[] = '<li>';
					$html[] = '<a href="' . Url :: create_url(array('page' => 'edit_puzzle_set', 'id' => $id)) . '" title="' . Language::get_instance()->translate(50) . '">' . Language::get_instance()->translate(266) . '</a>';
					$html[] = '</li>';
						
					$html[] = '<li>';
					$html[] = "<a href='javascript: confirmation(\"" . Language::get_instance()->translate(268) . "\", \"" . Url :: create_url(array('page' => 'remove_puzzle_set', 'id' => $id)) . "\")' title='" . Language::get_instance()->translate(51) . "'>" . Language::get_instance()->translate(267) . "</a>";
					$html[] = '</li>';
					$html[] = '<br />';
					
					$html[] = '<li>';
					$html[] = "<a id=\"add_set_puzzles\" href=\"javascript:;\" title=\"" . Language::get_instance()->translate(133) . "\">" . Language::get_instance()->translate(269) . "</a>";
					$html[] = '</li>';
				}
			}
		}
		*/
		$html[] = '</ul>';
		return implode("\n", $html);
	}
	
	public function get_icon()
	{
		return '<img src="' . Path :: get_url_path() . 'layout/images/icons/puzzle_icon.png" style="border: 0"/>';
	}
	
	public function get_puzzle_form($puzzle)
	{
		$html = array();
		
		$submit = Language::get_instance()->translate(49);
		if(!is_null($puzzle)) $submit = Language::get_instance()->translate(56);
		
		$t_submit = Language::get_instance()->translate(271);
		if(!is_null($puzzle)) $t_submit = Language::get_instance()->translate(272);
		
		$html[] = '<form action="" method="post" id="puzzle_creator_form">';
		$html[] = '<div class="record">';
		$html[] = '<input type="hidden" name="id" value="' . (is_null($puzzle)?0:$puzzle->get_id()) . '"/>';
		$html[] = '<p><h3 class="title">'. $t_submit . '</h3></p>';
		$theme_ids = array();
		if(!is_null($puzzle))
			$theme_ids = $puzzle->get_theme_ids();
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(273) . ' :</div><div class="record_input">'.$this->manager->get_theme_manager()->get_renderer()->get_selector($theme_ids).'</div><br class="clearfloat"/>';


		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(274) . ' :</div>';
		if(is_null($puzzle) || $this->manager->get_user()->is_admin())
			$html[] = '<div class="record_input"><input type="text" name="rating" size="3" value="'.(!is_null($puzzle)?$puzzle->get_rating():'').'"/></div>';
		else
		{
			$html[] = '<div class="record_output">'.$puzzle->get_rating().'</div><br class="clearfloat"/>';
			$html[] = '<input type="hidden" name="rating" value="'.$puzzle->get_rating().'"/>';
		}
		
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(275) . ' :</div><div class="record_input"><textarea style="width:250px;height:50px;" name="comment">'.(!is_null($puzzle)?$puzzle->get_comment():'').'</textarea></div>';								
		
		$html[] = '<div class="record_button"><a id="submit_form" class="link_button" href="javascript:;">'.$submit.'</a></div></form>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
	
	public function get_puzzle_validate_form($right = RightManager :: READ_RIGHT, $form_search = false)
	{
		$html = array();
		
		$html[] = '<h3 class="title">' . Language::get_instance()->translate(276) . '</h3>';
		$html[] = '<form action="" method="post" id="puzzle_valid_form">';
		$html[] = '<p><input type="checkbox" name="select"/> ' . Language::get_instance()->translate(277) . '</p>';
		$puzzles = $this->manager->get_data_manager()->retrieve_invalid_puzzle_properties();
		$html[] = '<div class="record">';
		foreach($puzzles as $puzzle)
		{
			$html[] = '<div style="float: left; margin-bottom: 10px">';
			$html[] = '<div style="float: left">';
			$html[] = '<input type="checkbox" name="puzzle_id[]" value="'.$puzzle->get_puzzle_id().'"/>';
			$url =  Path::get_url_path() . 'pages/puzzle/images/' . $puzzle->get_puzzle_id() . '.gif';
		    if(!file_exists(Path::get_path() . 'pages/puzzle/images/' . $puzzle->get_puzzle_id() . '.gif'))
				$url = Path::get_url_path() . 'pages/puzzle/ajax/retrieve_puzzle_image.ajax.php?puzzle_id=' . $puzzle->get_puzzle_id();
			$html[] = '<img style="vertical-align: top;" src="' . $url . '"/>';
			$html[] = '</div>';
			$html[] = '<div style="float: left">';
			$html[] = $this->get_description_puzzle($puzzle);
			$html[] = '</div>';
			$html[] = '</div>';
		}
		$html[] = '<br class="clearfloat" />';
		$html[] = '<div class="record_button"><a id="submit_form" class="link_button" href="javascript:;">' . Language::get_instance()->translate(276) . '</a></div></div></form>';

		return implode("\n", $html);
	}
	
	public function get_dubble_puzzles_form()
	{
		$html = array();
		$puzzles = $this->manager->get_data_manager()->retrieve_dubble_puzzles();
		
		$html[] = '<p><h3 class="title">' . Language::get_instance()->translate(278) . ':</h3></p>';	
		if(count($puzzles)>0)
		{	
			$html[] = '<p style="height:25px;vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(279) . '</p>';
			$html[] = '<form action="" method="post" id="dubble_puzzles_form">';
			$html[] = '<div class="record">';
			
			$comments = -1;
			$variations = -1;
			$variation_comments = -1;
			$prev_puzzle_fen = -1;
			$superiour_puzzle_id = -1;
			$same_fen_puzzles = array();
			$count = 0;
			
			foreach($puzzles as $puzzle)
			{
				if($puzzle->get_fen() != $prev_puzzle_fen)
				{
					if($prev_puzzle_fen != -1)
					{
						$html[] = $this->render_end_same_fen_puzzle_table($same_fen_puzzles, $superiour_puzzle_id, $count);
						$superiour_puzzle_id = -1;
						$same_fen_puzzles = array();
					}
						
					$html[] = $this->render_start_same_fen_puzzle_table($puzzle);
					$prev_puzzle_fen = $puzzle->get_fen();
					$comments = -1;
					$variations = -1;
					$variation_comments = -1;
					$count++;
				}
				
				$moves_count = strlen($this->manager->get_data_manager()->retrieve_puzzle_moves($puzzle->get_id()))/5;
				$comments_count = $this->manager->get_data_manager()->count_puzzle_comments_by_puzzle_id($puzzle->get_id());
				$variations_count = $this->manager->get_data_manager()->count_puzzle_variations_by_puzzle_id($puzzle->get_id());
				$variation_comments_count = $this->manager->get_data_manager()->count_puzzle_variation_comments_by_puzzle_id($puzzle->get_id());
				
				$superior_puzzle = false;
				if ($variations < $variations_count)
					$superior_puzzle = true;
				if($superior_puzzle == false && $variations == $variations_count && $comments < $comments_count)
					$superior_puzzle = true;
				if($superior_puzzle == false && $variations == $variations_count && $comments == $comments_count && $variation_comments < $variation_comments_count)
					$superior_puzzle = true;
				
				if($superior_puzzle)
				{
					$comments = $comments_count;
					$variations = $variations_count;
					$variation_comments = $variation_comments_count;
					$superiour_puzzle_id = $puzzle->get_id();
				}	
				
				$same_fen_puzzles[] = array($puzzle->get_id(), $moves_count, $comments_count, $variations_count, $variation_comments_count);
			}
			$html[] = $this->render_end_same_fen_puzzle_table($same_fen_puzzles, $superiour_puzzle_id, $count);
			$html[] = '<br class="clearfloat" />';
			$html[] = '<input type="hidden" value="' . $count .'" name="count" />';
			$html[] = '<div class="record_button"><a id="submit_form" class="link_button" href="javascript:;">' . Language::get_instance()->translate(280) . '/a></div></div></form>';
		}
		else
			$html[] = '<p>' . Language::get_instance()->translate(281) . '</p>';
			
		return implode("\n", $html);
	}
	
	private function render_start_same_fen_puzzle_table($puzzle)
	{
		$html = array();
		$html[] = '<div>';
		$html[] = '<div style="float: left; margin-right: 10px;">';
		
		$src = Path::get_url_path() . 'pages/puzzle/ajax/retrieve_puzzle_image.ajax.php?puzzle_id=' . $puzzle->get_id();
		if(file_exists(Path::get_path() . "pages/puzzle/images/" . $puzzle->get_id() . ".gif"))
			$src = Path::get_url_path() . 'pages/puzzle/images/' . $puzzle->get_id() . '.gif';
		
		$html[] = '<img style="vertical-align: top;" src="'.$src.'"/>';
		$html[] = '</div>';
		$html[] = '<div style="float: left;">';			
		$html[] = '<table>';
		$html[] = '<tr>';
		$html[] = '<th>#</th>';
		$html[] = '<th># ' . Language::get_instance()->translate(282) . '</th>';
		$html[] = '<th># ' . Language::get_instance()->translate(283) . '</th>';
		$html[] = '<th># ' . Language::get_instance()->translate(284) . '</th>';
		$html[] = '<th># ' . Language::get_instance()->translate(285) . '</th>';
		$html[] = '<th>' . Language::get_instance()->translate(286) . '</th>';
		$html[] = '<th>' . Language::get_instance()->translate(287) . '</th>';
		$html[] = '</tr>';
		return implode("\n", $html);
	}
	
	private function render_end_same_fen_puzzle_table($same_fen_puzzles, $superiour_puzzle_id, $count)
	{
		$html = array();
		foreach ($same_fen_puzzles as $data)
		{
			$html[] = '<tr>';
			$html[] = '<td>';
			$html[] = $data[0];
			$html[] = '</td>';
			$html[] = '<td>';
			$html[] = $data[1];
			$html[] = '</td>';
			$html[] = '<td>';
			$html[] = $data[2];
			$html[] = '</td>';
			$html[] = '<td>';
			$html[] = $data[3];
			$html[] = '</td>';
			$html[] = '<td>';
			$html[] = $data[4];
			$html[] = '</td>';
			$html[] = '<td>';
			$html[] = '<input type="radio" name="puzzle_superior_'.$count.'" value="'.$data[0].'" '.($data[0]==$superiour_puzzle_id?'checked':'').'/>';
			$html[] = '</td>';
			$html[] = '<td>';
			$html[] = '<input type="checkbox" name="puzzles_'.$count.'[]" value="'.$data[0].'" '.($data[0]!=$superiour_puzzle_id?'checked':'').'/>';
			$html[] = '</td>';
		}
		$html[] = '</table>';
		$html[] = '</div>';
		$html[] = '</div>';
		$html[] = '<br class="clearfloat"/>';
		$html[] = '<br/>';
		
		return implode("\n", $html);
	}
	
	public function get_puzzle_table($right = RightManager :: READ_RIGHT, $form_search = false, $editable = true, $start = 0, $length = 0)
	{
		$html = array();
		$puzzles_without_properties = $this->manager->get_data_manager()->retrieve_all_puzzles_without_properties($this->manager->get_user());
		if(count($puzzles_without_properties))
		{
			$html[] = '<p class="error">' . Language::get_instance()->translate(288) . '</p>';
			$html[] = '<table id="puzzles_without_table">';
			$html[] = '<tr>';
			$html[] = '<th>#</th>';
			$html[] = '<th>Fen</th>';
			$html[] = '<th>' . Language::get_instance()->translate(289) . '</th>';
			$html[] = '<th>' . Language::get_instance()->translate(290) . '</th>';
			if($editable)
			{
				$html[] = '<th></th>';
				$html[] = '<th></th>';
			}
			$html[] = '</tr>';
			
			$count = 0;
			foreach($puzzles_without_properties as $puzzle)
			{
				
				$create = "";
		    	if(!file_exists(Path::get_path() . "pages/puzzle/images/" . $puzzle->get_id() . ".gif"))
					$create = " create ";
				
				$html[] = '<tr class="puzzle_link without row'.$create.' '.(($count%2==1)?"odd":"even").'">';
				$html[] = '<td>';
				$html[] = $puzzle->get_id();
				$html[] = '</td>';
				$html[] = '<td>';
				$html[] = $puzzle->get_fen();
				$html[] = '</td>';
				$html[] = '<td>';
				$html[] = $puzzle->get_first_move();
				$html[] = '</td>';
				$html[] = '<td>';
				$html[] = Utilities::truncate_string($puzzle->get_moves(), 60);
				$html[] = '</td>';
				if($editable)
				{
		        	$html[] = '<td class="tool_btn edit">';
					$html[] = '<img style="position: relative; z-order: 1000; border: 0;" src="' . Path :: get_url_path() . 'layout/images/buttons/edit.png" title="' . Language::get_instance()->translate(291) . '" border="0">';
					$html[] = '</td>';
		    	    $html[] = '<td class="tool_btn delete">';
					$html[] = '<img style="position: relative; z-order: 1000; border: 0;" src="' . Path :: get_url_path() . 'layout/images/buttons/delete.png" title="' . Language::get_instance()->translate(292) . '" border="0">';
					$html[] = '</td>';
				}
				$html[] = '</tr>';
				$count++;
			}
			$html[] = '</table>';
			$html[] = '<br>';
		}
		
		if($length != 0)
		{
			$count = 0;
			if($form_search)
				$count = $this->manager->get_data_manager()->retrieve_puzzle_properties_with_search_form("", $right, '', false, false, true);
			else
				$count = $this->manager->get_data_manager()->retrieve_all_puzzle_properties($right, '', true);
			if($count!=0)
			{
				$no_pages = ceil($count/$length);
				$page = $start==0?1:ceil($no_pages * ($start/$count));
			}
			else
			{
				$start = 0;
				$length = 0;
			}
		}
		elseif($length == 0 && $start != 0)
		{
			$start = 0;
		}
		
		$limit = $start;
		if($length != 0)
			$limit .= ", " . ($length + 1);
		if($form_search)
			$puzzles = $this->manager->get_data_manager()->retrieve_puzzle_properties_with_search_form("", $right, $limit);
		else
			$puzzles = $this->manager->get_data_manager()->retrieve_all_puzzle_properties($right, $limit);

		if(count($puzzles))
		{
			if(($start == 0 && $length != 0 && count($puzzles) == ($length + 1)) || $start != 0)
			{
				$html[] = '<div style="float: right;">';
				$html[] = '<input type="hidden" id="page_search" name="pg_search"  value="' . ($form_search?1:0) . '">';
				for($i = 1; $i <= $no_pages; $i++)
				{
					if($i != $page)
						$html[] = '<a href="javascript:;" class="text_link page_link_btn">' . $i . '</a>';
					else
						$html[] = '<span style="padding: 1px 8px;">' . $i . '</span>';
					$html[] = '<input type="hidden" id="page_start_' . $i . '" name="pg_start_' . $i . '"  value="' . (($i-1) * $length) . '">';
				}
				$html[] = '</div>';
				$html[] = '<br class="clear_float"/><br/>';
			}
			$html[] = '<table id="puzzles_table">';
			$html[] = '<tr>';
			$html[] = '<th>#</th>';
			if($this->manager->get_user()->is_admin())
				$html[] = '<th>' . Language::get_instance()->translate(293) . '</th>';
			$html[] = '<th>' . Language::get_instance()->translate(273) . '</th>';
			$html[] = '<th>' . Language::get_instance()->translate(255) . '</th>';
			$html[] = '<th>' . Language::get_instance()->translate(274) . '</th>';
			$html[] = '<th>' . Language::get_instance()->translate(294) . '</th>';
			$html[] = '<th>' . Language::get_instance()->translate(930) . '</th>';
			$html[] = '<th>' . Language::get_instance()->translate(295) . '</th>';
			$html[] = '<th>' . Language::get_instance()->translate(296) . '</th>';
			if($editable)
			{
				$html[] = '<th></th>';
				$html[] = '<th></th>';
			}
			$html[] = '</tr>';
			
			$count = 0;
			foreach($puzzles as $puzzle)
			{
				if($count<$length || $length == 0)
				{
					$html[] = $this->get_puzzle_row($puzzle, $count, $editable);
					$count++;
				}
			}
			$html[] = '</table>';
		}
		else
		{
			if($form_search)
				$html[] = '<p>' . Language::get_instance()->translate(297) . '</p>';
			else
				$html[] = '<p>' . Language::get_instance()->translate(298) . '</p>';
		}
		return implode("\n", $html);
	}
	
	public function get_puzzle_row($puzzle,$count,$editable)
	{
		$html = array();
		
		$create = "";
    	if(!file_exists(Path::get_path() . "pages/puzzle/images/" . $puzzle->get_puzzle_id() . ".gif"))
			$create = " create ";
			
		$html[] = '<tr class="puzzle_link row'.$create.' '.(($count%2==1)?"odd":"even").'">';
		$html[] = '<td>';
		$html[] = $puzzle->get_puzzle_id();
		$html[] = '</td>';
		if($this->manager->get_user()->is_admin())
		{
			$html[] = '<td>';
			$html[] = $puzzle->get_valid()?Language::get_instance()->translate(167):Language::get_instance()->translate(168);
			$html[] = '</td>';
		}
		$html[] = '<td>';
		$html[] = Utilities::truncate_string($puzzle->get_themes(), 30);
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $puzzle->get_difficulty();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $puzzle->get_rating();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $puzzle->get_number_of_moves();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = Utilities::truncate_string($puzzle->get_comment(), 150);
		$html[] = '</td>';
		$html[] = '<td align="center">';
		$meta_data = RightDataManager::instance(null)->retrieve_location_object_creation(17, $puzzle->get_puzzle_id());
		if(!is_null($meta_data))
		{
			$user = UserDataManager::instance(null)->retrieve_user(intval($meta_data->creator_id));
			if(!is_null($user))
				$html[] = $user->get_name();
			else
				$html[] = "-";
		}
		else
		{
			$html[] = "-";
		}
		$html[] = '</td>';
		$html[] = '<td align="center">';
		if(!is_null($meta_data) && $meta_data->creation_time != 0)
		{
			$html[] = date("d/m/Y", intval($meta_data->creation_time));
		}
		else
		{
			$html[] = "-";
		}
		$html[] = '</td>';
		if($editable)
		{
			if($puzzle->get_update()==1)
			{
        		$html[] = '<td class="tool_btn edit">';
        		$html[] = Display::display_icon("edit", Language::get_instance()->translate(291));
				$html[] = '</td>';
			}
			else
			{
        		$html[] = '<td>&nbsp;</td>';
			}
        	$html[] = '<td class="tool_btn delete">';
        	$html[] = Display::display_icon("delete", Language::get_instance()->translate(292));
			$html[] = '</td>';
		}
		
		$html[] = '</tr>';
		return implode("\n", $html);
	}
	
	public function get_puzzle_row_header()
	{
		$html = array();
		$html[] = '<th>#</th>';
		$html[] = '<th>'.Language::get_instance()->translate(293).'</th>';
		$html[] = '<th>' . Language::get_instance()->translate(273) . '</th>';
		$html[] = '<th>' . Language::get_instance()->translate(255) . '</th>';
		$html[] = '<th>' . Language::get_instance()->translate(274) . '</th>';
		$html[] = '<th>' . Language::get_instance()->translate(294) . '</th>';
		$html[] = '<th>' . Language::get_instance()->translate(930) . '</th>';
		return implode("\n", $html);
	}
	
	public function get_puzzle_row_render($puzzle)
	{
		$html = array();
		$html[] = '<td>';
		$html[] = $puzzle->get_puzzle_id();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $puzzle->get_valid()?"Waar":"Vals";
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $puzzle->get_themes();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $puzzle->get_difficulty();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $puzzle->get_rating();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $puzzle->get_number_of_moves();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $puzzle->get_comment();
		$html[] = '</td>';
		return implode("\n", $html);
	}
	
	public function get_description_puzzle($puzzle, $hide_id = false)
	{
		$html = array();
		if(!$hide_id)
		{
			$html[] = '<div class="record_name"># :</div><div class="record_output">'.$puzzle->get_puzzle_id().'</div><br class="clearfloat"/>';
			$html[] = "<div class='record_name'>" . Language::get_instance()->translate(293) . " :</div><div class='record_output'>".($puzzle->get_valid()?"Waar":"Vals")."</div><br class='clearfloat'/>";
		}
		$html[] = "<div class='record_name'>" . Language::get_instance()->translate(273) . " :</div><div class='record_output'>".$puzzle->get_themes()."</div><br class='clearfloat'/>";
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(255) . ' :</div><div class="record_output">'.$puzzle->get_difficulty().'</div><br class="clearfloat"/>';
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(274) . ' :</div><div class="record_output">'.$puzzle->get_rating().'</div><br class="clearfloat"/>';
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(294) . '</div><div class="record_output">'.$puzzle->get_number_of_moves().'</div><br class="clearfloat"/>';
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(930) . ' :</div><div class="record_output">'.$puzzle->get_comment().'</div><br class="clearfloat"/>';
		return implode("\n", $html);
	}

	public function get_puzzle_search($all_puzzles = false, $own_form = true)
	{
		$html = array();
		if($own_form)
		{
			$html[] = '<form action="" method="post" id="puzzle_search_form">';
			$html[] = '<div class="record">';
			$html[] = '<p><h3 class="title">' . Language::get_instance()->translate(63) . ':</h3></p>';
		}
		$html[] = '<input type="hidden" name="all" value="' . ($all_puzzles?"1":"0") . '"/>';
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(273) . ' :</div><div class="record_input">'.$this->manager->get_theme_manager()->get_renderer()->get_selector().'</div>';
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(255) . ' :</div><div class="record_input">'.$this->manager->get_difficulty_manager()->get_renderer()->get_selector().'</div>';						
		//$html[] = '<div class="record_name">Rating :</div><div class="record_input"><input type="text" name="min_rating" size="3" value="Min"/> - <input type="text" name="max_rating" size="3" value="Max"/></div>';
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(294) . ' :</div><div class="record_input"><input type="text" name="min_nom" size="3" value="' . Language::get_instance()->translate(299) . '"/> - <input type="text" name="max_nom" size="3" value="' . Language::get_instance()->translate(300) . '"/></div>';
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(296) . ' :</div><div class="record_input">' . Calendar::get_date_selector("from_date", time(), true, 2010, intval(date("Y"))) . '</div>';
		$html[] = '<div class="record_name"></div><div class="record_output" style="margin-bottom: 5px;">' . Language::get_instance()->translate(1051) . '</div><br class="clearfloat"/>';
		$html[] = '<div class="record_name"></div><div class="record_input">'. Calendar::get_date_selector("to_date", time(), true, 2010, intval(date("Y"))) . '</div>';
		$html[] = '<div class="record_button"><a id="submit_search_form" class="link_button" href="javascript:;">' . Language::get_instance()->translate(63) . '</a></div><br class="clearfloat"/>';								
		if($own_form)
		{
			$html[] = '</div></form>';
		}
		return implode("\n", $html);		
	}
	
	public function get_puzzle_image_html($puzzle)
	{
		$url =  Path::get_url_path() . 'pages/puzzle/images/' . $puzzle->get_puzzle_id() . '.gif';
		if(!file_exists(Path::get_path() . 'pages/puzzle/images/' . $puzzle->get_puzzle_id() . '.gif'))
		$url = Path::get_url_path() . 'pages/puzzle/ajax/retrieve_puzzle_image.ajax.php?puzzle_id=' . $puzzle->get_puzzle_id();
		return '<img src="' . $url . '" style="vertical-align: top"/>';
	}
	
	public function get_shop_detail($puzzle)
	{
		return $this->get_description_puzzle($puzzle, true);
	}
	
	public function get_puzzle_detailed_form($form_search = false, $name = 'object_id', $radio = true, $all_puzzles = false, $form = false, $start = 0, $limit = 0)
	{
		$html = array();
		$puzzles = null;
		$limit_qry = "";
		if($limit != 0)
			$limit_qry = $start . ", " . ($limit + 1);
		else if($start != 0)
			$limit_qry = $start;
		if($form_search)
			$puzzles = $this->manager->get_data_manager()->retrieve_puzzle_properties_with_search_form("", $all_puzzles?RightManager::NO_RIGHT:RightManager::READ_RIGHT, $limit_qry);
		else
			$puzzles =  $this->manager->get_data_manager()->retrieve_all_puzzle_properties(RightManager::READ_RIGHT);
		
		$puzzle_count = count($puzzles);
		if($puzzle_count)
		{
			if($form && $start == 0)
			{
				$html[] = '<form action="" method="post" id="puzzle_shop">';
			}
			if($start == 0)
			{
				$html[] = '<div class="record">';
				$html[] = '<div id="more_puzzles_record">';
			}
			$count = 0;
			foreach($puzzles as $puzzle)
			{
				$html[] = '<div style="margin-top: 10px;">';
				$html[] = '<div style="float: left">';
				$html[] = '<input type="' . ($radio?'radio':'checkbox') . '" name="' . $name  . ($radio?'':'[]') . '" value="'.$puzzle->get_puzzle_id().'"/>';
				$html[] = $this->get_puzzle_image_html($puzzle);
				$html[] = '</div>';
				$html[] = '<div style="float: left">';
				$html[] = $this->get_description_puzzle($puzzle);
				$html[] = '</div>';
				$html[] = '<br class="clearfloat" />';
				$html[] = '</div>';
				$count++;
				if($count == $limit && $limit != 0)
					break;
			}
			if($count == $limit && $limit != 0 && $count + 1 == $puzzle_count)
				$html[] = '<div id="more_puzzles_block" class="record_button" style="width: 100%; text-align: center;"><a id="more_puzzles" class="text_link" href="javascript:;">' . Language::get_instance()->translate(792) . '</a></div><br class="clearfloat"/>';
			if($form && $start == 0)
			{
				$html[] = '</div>';
				$html[] = '<div class="record_button"><a id="submit_form" class="link_button" href="javascript:;">' . Language::get_instance()->translate(49) . '</a></div><br class="clearfloat"/>';
				$html[] = '<input type="hidden" name="puzzle_shop" value="1">';
			}
			if($start == 0)						
				$html[] = '</div>';
			if($form && $start == 0)
				$html[] = '</form>';
			if($start == 0)
				$html[] = '<br class="clearfloat" />';
		}
		else
			$html[] = '<p>' . Language::get_instance()->translate(297) . '</p>';
		
		return implode("\n", $html);
	}
	
	public function get_puzzle_image($fen, $firstmove, $id, $setup_only = false, $create_only = false, $other_url = null)
	{
		$arr = array();
		$curl_handler = curl_init();
		curl_setopt($curl_handler, CURLOPT_RETURNTRANSFER, 1); 
		for($i = 7; $i>=0; $i--)
		{
			$index = strpos($fen, "/");
			$rowString = null;
			if($index !== false && $i>0)
			{
				$rowString = substr($fen, 0, $index);
				$fen = substr($fen, $index+1);
			}
			else if($index === false && $i==0)
			{
				$index = strpos($fen, " ");
				if($index !== false)
				{
					$rowString = substr($fen, 0, $index);
					$fen = substr($fen, $index+1);
				}
			}
			if(is_null($rowString))
				return null;

			$j = 8;
			while($j>0)
			{
				$piece = substr($rowString, 0, 1);
				$skip = intval($piece);
				if(!is_numeric($skip) || $skip == 0)
				{
					$arr[$j-1][$i] = $piece;
					$j--;
				}
				else
				{
					$j-=$skip;
				}
				if(strlen($rowString) > 1 && $j>0)
					$rowString = substr($rowString, 1);
				else if(strlen($rowString) > 1 && $j <= 0)
					return null;
				else if($j < 0 || $j > 0)
					return null;
			}	
		}
		
		//Check whose turn it is.
		$flip = false;
		$index = strpos($fen, " ");
		if($index !== false)
		{
			$turn = substr($fen, 0, $index);
			$fen = substr($fen, $index);
			if($turn == "w" || $turn == "b")
			{
				if($turn == "b")
				{
					$flip = true;
				}
			}
			else return null;
		} 
		else return null;
		

		$x1 = intval(substr($firstmove, 0, 1));
		$y1 = intval(substr($firstmove, 1, 1));
		$x2 = intval(substr($firstmove, 2, 1));
		$y2 = intval(substr($firstmove, 3, 1));
			
		$board = $this->get_image("Board", $curl_handler);
		//$index = imagecolorclosest ( $im,  255, 255, 255);
		//imagecolorset($im,$index,92,92,92);
			
		foreach($arr as $j => $arr2)
		{
			foreach($arr2 as $i => $piece)
			{
				$piece_img = $this->get_image($piece, $curl_handler);
				if($i == $x1 && $j == $y1 && $setup_only == false)
					imagecopy($board, $piece_img, $flip?$y2*45:(7-$y2)*45, $flip?$x2*45:(7-$x2)*45, 0, 0, 45, 45);
				else if(!($i == $x2 && $j == $y2 && $setup_only == false))
					imagecopy($board, $piece_img, $flip?$j*45:(7-$j)*45, $flip?$i*45:(7-$i)*45, 0, 0, 45, 45);
				imagedestroy($piece_img);
			}
		}
			
		if($setup_only == false || $firstmove != "")
		{
			$xFrom = $flip?$y1*45:(7-$y1)*45;
			$yFrom = $flip?$x1*45:(7-$x1)*45;
			$xTo = $flip?$y2*45:(7-$y2)*45;
			$yTo = $flip?$x2*45:(7-$x2)*45;
				
			if($xFrom != $xTo && $yFrom != $yTo)
				$degree = (($yFrom - $yTo)/abs($yTo - $yFrom)) * (atan2(abs($yFrom - $yTo), abs($xFrom - $xTo)) * (180 / pi()));
			else
				$degree = 0;

			$width = sqrt(pow($xFrom - $xTo, 2) + pow($yFrom - $yTo, 2));

			if(abs($degree) > 45 || ($degree == 45 && ($xFrom > $xTo && $yFrom > $yTo)) || ($degree == -45 && !(($xFrom < $xTo && $yFrom > $yTo) || ($xFrom > $xTo && $yFrom < $yTo))) || ($degree == 0 && $xFrom == $xTo))
				$arrow = imagecreate(45, $width);
			else
				$arrow = imagecreate($width, 45);
				
			$background = imagecolorallocate($arrow, 255, 255, 255);
			$line_colour = imagecolorallocate($arrow, 60, 115, 4);
			 
			imagesetthickness($arrow, 5);
			imagecolortransparent($arrow, $background);
				
			if(abs($degree) > 45 || ($degree == 45 && ($xFrom > $xTo && $yFrom > $yTo)) || ($degree == -45 && !(($xFrom < $xTo && $yFrom > $yTo) || ($xFrom > $xTo && $yFrom < $yTo))) || ($degree == 0 && $xFrom == $xTo))
			{
				imageline($arrow, 20, 0, 20, $width, $line_colour);
				if($yFrom >= $yTo)
				{
					imageline($arrow, 22.5, 1, 0, 15, $line_colour);
					imageline($arrow, 22.5, 2, 45, 15, $line_colour);
					$degree = 90-$degree;
					if($xFrom >= $xTo)
					{
						if($degree != 90)
							$arrow = imagerotate($arrow, $degree, $background);
						imagecopy($board, $arrow, $xTo + 22.5 - (imagesx($arrow)-($xFrom-$xTo))/2, $yTo + 22.5 - (imagesy($arrow)-($yFrom-$yTo))/2, 0, 0, imagesx($arrow), imagesy($arrow));
					}
					else
					{
						$arrow = imagerotate($arrow, -$degree, $background);
						imagecopy($board, $arrow, $xFrom + 22.5 - (imagesx($arrow)-($xTo-$xFrom))/2, $yTo + 22.5 - (imagesy($arrow)-($yFrom-$yTo))/2, 0, 0, imagesx($arrow), imagesy($arrow));
					}
				}
				else
				{
					imageline($arrow, 22.5, $width-2, 0, $width-15, $line_colour);
					imageline($arrow, 22.5, $width-3, 45, $width-15, $line_colour);
					$degree = -90-$degree;
					if($xFrom >= $xTo)
					{
						if($degree != -90)
							$arrow = imagerotate($arrow, $degree, $background);
						imagecopy($board, $arrow, $xTo + 22.5 - (imagesx($arrow)-($xFrom-$xTo))/2, $yFrom + 22.5 - (imagesy($arrow)-($yTo-$yFrom))/2, 0, 0, imagesx($arrow), imagesy($arrow));
					}
					else
					{
						$arrow = imagerotate($arrow, -$degree, $background);
						imagecopy($board, $arrow, $xFrom + 22.5 - (imagesx($arrow)-($xTo-$xFrom))/2, $yFrom + 22.5 - (imagesy($arrow)-($yTo-$yFrom))/2, 0, 0, imagesx($arrow), imagesy($arrow));
					}
				}
			}
			else
			{
				imageline($arrow, 0, 20, $width, 20, $line_colour);
				if($xFrom >= $xTo)
				{
					imageline($arrow, 2, 22.5, 15, 0, $line_colour);
					imageline($arrow, 2, 22.5, 15, 45, $line_colour);
					if($degree != 0)
						$arrow = imagerotate($arrow, -$degree, $background);
					if($xFrom >= $xTo)
						imagecopy($board, $arrow, $xTo + 22.5 - (imagesx($arrow)-($xFrom-$xTo))/2, $yTo + 22.5 - (imagesy($arrow)-($yFrom-$yTo))/2, 0, 0, imagesx($arrow), imagesy($arrow));
					else
						imagecopy($board, $arrow, $xFrom + 22.5 - (imagesx($arrow)-($xTo-$xFrom))/2, $yTo + 22.5 - (imagesy($arrow)-($yFrom-$yTo))/2, 0, 0, imagesx($arrow), imagesy($arrow));
				}
				else
				{
					imageline($arrow, $width-2, 22.5, $width-15, 0, $line_colour);
					imageline($arrow, $width-2, 22.5, $width-15, 45, $line_colour);
					if($degree != 0)
						$arrow = imagerotate($arrow, $degree, $background);
					if($xFrom >= $xTo)
						imagecopy($board, $arrow, $xFrom + 22.5 - (imagesx($arrow)-($xFrom-$xTo))/2, $yFrom + 22.5 - (imagesy($arrow)-($yTo-$yFrom))/2, 0, 0, imagesx($arrow), imagesy($arrow));
					else
						imagecopy($board, $arrow, $xFrom + 22.5 - (imagesx($arrow)-($xTo-$xFrom))/2, $yFrom + 22.5 - (imagesy($arrow)-($yTo-$yFrom))/2, 0, 0, imagesx($arrow), imagesy($arrow));
				}
			}
		}
		
		$new_board = imagecreate(150, 150);
		imagecopyresampled($new_board, $board, 0, 0, 0, 0, 150, 150, 360, 360);
		header('Content-type: image/gif');
		if(!is_null($other_url))
		{
			$filename = $other_url . $id . ".gif";
		}
		else
		{
			$filename = Path::get_path() . "pages/puzzle/images/" . $id . ".gif";
		}
		imagegif($new_board, $filename);
		if(!$create_only)
			imagegif($new_board);
		if($setup_only == false || $firstmove != "")
			imagedestroy($arrow);
		imagedestroy($board);
		imagedestroy($new_board);
		curl_close($curl_handler);
	}
	
	public function get_image($type, $curl_handler)
	{
		switch($type)
		{
			case "Board": $imgname = Path::get_url_path() . "flash/assets/images/chessboard/BorderlessBoard.png";
						  curl_setopt($curl_handler, CURLOPT_URL, $imgname);
						  $im = ImageCreateFromString(curl_exec($curl_handler));
						  break;
			case "r": $imgname = Path::get_url_path() . "flash/assets/images/chessboard/BR.gif";
				      curl_setopt($curl_handler, CURLOPT_URL, $imgname);
					  $im = ImageCreateFromString(curl_exec($curl_handler));
					  break;
			case "n": $imgname = Path::get_url_path() . "flash/assets/images/chessboard/BN.gif";
					  curl_setopt($curl_handler, CURLOPT_URL, $imgname);
					  $im = ImageCreateFromString(curl_exec($curl_handler));
					  break;
			case "b": $imgname = Path::get_url_path() . "flash/assets/images/chessboard/BB.gif";
					  curl_setopt($curl_handler, CURLOPT_URL, $imgname);
					  $im = ImageCreateFromString(curl_exec($curl_handler));
					  break;
			case "k": $imgname = Path::get_url_path() . "flash/assets/images/chessboard/BK.gif";
					  curl_setopt($curl_handler, CURLOPT_URL, $imgname);
					  $im = ImageCreateFromString(curl_exec($curl_handler));
					  break;
			case "q": $imgname = Path::get_url_path() . "flash/assets/images/chessboard/BQ.gif";
					  curl_setopt($curl_handler, CURLOPT_URL, $imgname);
					  $im = ImageCreateFromString(curl_exec($curl_handler));
					  break;
			case "p": $imgname = Path::get_url_path() . "flash/assets/images/chessboard/B_.gif";
					  curl_setopt($curl_handler, CURLOPT_URL, $imgname);
					  $im = ImageCreateFromString(curl_exec($curl_handler));
					  break;
			case "R": $imgname = Path::get_url_path() . "flash/assets/images/chessboard/WR.gif";
					  curl_setopt($curl_handler, CURLOPT_URL, $imgname);
					  $im = ImageCreateFromString(curl_exec($curl_handler));
					  break;
			case "N": $imgname = Path::get_url_path() . "flash/assets/images/chessboard/WN.gif";
					  curl_setopt($curl_handler, CURLOPT_URL, $imgname);
					  $im = ImageCreateFromString(curl_exec($curl_handler));
					  break;
			case "B": $imgname = Path::get_url_path() . "flash/assets/images/chessboard/WB.gif";
					  curl_setopt($curl_handler, CURLOPT_URL, $imgname);
					  $im = ImageCreateFromString(curl_exec($curl_handler));
					  break;
			case "K": $imgname = Path::get_url_path() . "flash/assets/images/chessboard/WK.gif";
					  curl_setopt($curl_handler, CURLOPT_URL, $imgname);
					  $im = ImageCreateFromString(curl_exec($curl_handler));
					  break;
			case "Q": $imgname = Path::get_url_path() . "flash/assets/images/chessboard/WQ.gif";
					  curl_setopt($curl_handler, CURLOPT_URL, $imgname);
					  $im = ImageCreateFromString(curl_exec($curl_handler));
					  break;
			case "P": $imgname = Path::get_url_path() . "flash/assets/images/chessboard/W_.gif";
					  curl_setopt($curl_handler, CURLOPT_URL, $imgname);
					  $im = ImageCreateFromString(curl_exec($curl_handler));
					  break;
			default: $im = null;
		}
		return $im;
	}
}

?>