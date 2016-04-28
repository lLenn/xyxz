<?php

class RightBrowser
{

	private $manager;
	private $id;
	private $object_id = null;
	private $selected_tab = 0;
	
	function RightBrowser($manager)
	{
		$this->manager = $manager;
		$this->id = Request::get("id");
		if(is_null($this->id) || !is_numeric($this->id)) $this->id = 0;
	}

	public function save_changes()
	{
		$html = array();
		if(!empty($_POST))
		{
			if(!is_null(Request::post('location_id')))
			{
				$this->id = Request::post('location_id');
				if(Request::post('todo') == 1)
				{
					$success = $this->manager->get_data_manager()->delete_location_right($this->id);
					if($success)	$html[] = "<p class='good'>" . Language::get_instance()->translate(391) . "</p>";
					else			$html[] = "<p class='error'>" . Language::get_instance()->translate(392) . "</p>";
					$this->id = 0;
				}
				elseif(Request::post('todo') == 3)
				{
					$this->object_id = Request::post('object_id');
				}
				else
				{
					$object = '';
					if(!is_null(Request::post('object_id')))
					{
						$this->object_id = Request::post('object_id');
						$object = '_object';
					}
				
					$type = null;
					$type_insert_method = null;
					$type_delete_method = null;
					$type_retrieve_method = null;
					$type_id_retrieve_method = null;
				
					if(Request::post('user_id'))
					{
						$type_insert_method = 'insert_location'.$object.'_user_right';
						$type_delete_method = 'delete_location'.$object.'_user_right';
						$type_retrieve_method = 'retrieve_location'.$object.'_user_right';
						$type_id_retrieve_method = 'get_user_id';
						$type = Language::get_instance()->translate(392);
						$pos_type = Language::get_instance()->translate(394);
						$neg_type = Language::get_instance()->translate(396);
						$pos_del_type = Language::get_instance()->translate(398);
						$neg_del_type = Language::get_instance()->translate(400);
						$this->selected_tab = 2;
					}
					elseif(Request::post('group_id'))
					{
						$type_insert_method = 'insert_location'.$object.'_group_right';
						$type_delete_method = 'delete_location'.$object.'_group_right';
						$type_retrieve_method = 'retrieve_location'.$object.'_group_right';
						$type_id_retrieve_method = 'get_group_id';
						$type = Language::get_instance()->translate(393);
						$pos_type = Language::get_instance()->translate(395);
						$neg_type = Language::get_instance()->translate(397);
						$pos_del_type = Language::get_instance()->translate(399);
						$neg_del_type = Language::get_instance()->translate(401);
						$this->selected_tab = 1;
					}
					
					$right = $this->manager->get_data_manager()->retrieve_location_right_from_post();
					if(is_null(Request::post('todo')))
					{
						$retrieve = null;
						if(!is_null(Request::post('object_id')))
							$retrieve = $this->manager->get_data_manager()->$type_retrieve_method($right->get_location_id(), $right->$type_id_retrieve_method(), $this->object_id);
						else
							$retrieve = $this->manager->get_data_manager()->$type_retrieve_method($right->get_location_id(), $right->$type_id_retrieve_method());
					
						if($right && !is_null($retrieve))
							$html[] = "<p class='error'>".$type."</p>";
						elseif($right)
						{
							$success = $this->manager->get_data_manager()->$type_insert_method($right);
							if($success == 0)	$html[] = "<p class='good'>".$pos_type."</p>";
							else				$html[] = "<p class='error'>".$neg_type."</p>";
						}
						else
							$html[] = "<p class='error'>". Language::get_instance()->translate(81) ."</p>";
					}
					elseif(Request::post('todo') == 2)
					{
						$success = $this->manager->get_data_manager()->$type_delete_method($right);
						if($success)	$html[] = "<p class='good'>".$pos_del_type."</p>";
						else			$html[] = "<p class='error'>".$neg_pos_type."</p>";
					}
				}
			}
			elseif(!is_null(Request::post("credits")) && Request::post("credits") == 1)
			{
				if($this->id!=RightManager::USER_LOCATION_ID && $this->id!=RightManager::PUZZLE_LOCATION_ID && $this->id!=RightManager::LESSON_LOCATION_ID && $this->id!=RightManager::LESSON_EXCERCISE_LOCATION_ID)
				{
					$lr = new LocationRight();
					$lr->set_id($this->id);
					$lr->set_credits_buy(Request::post("credits_buy"));
					$lr->set_credits_sell(Request::post("credits_sell"));
					$lr->set_credits_sell(Request::post("credits_accepted"));
					$this->manager->get_data_manager()->update_credits($lr);
				}
				elseif($this->id != RightManager::USER_LOCATION_ID)
				{
					$difficulty_manager = new DifficultyManager($this->manager->get_user());
					foreach($difficulty_manager->get_data_manager()->retrieve_difficulty_clusters() as $cluster)
					{
						$this->manager->get_data_manager()->update_location_right_meta_data($this->id, "credits_buy_" . $cluster->get_id(), Request::post("credits_buy_" . $cluster->get_id()));
						$this->manager->get_data_manager()->update_location_right_meta_data($this->id, "credits_sell_" . $cluster->get_id(), Request::post("credits_sell_" . $cluster->get_id()));
						$this->manager->get_data_manager()->update_location_right_meta_data($this->id, "credits_accepted_" . $cluster->get_id(), Request::post("credits_accepted_" . $cluster->get_id()));
					}
				}
				else
				{
					$this->manager->get_data_manager()->update_location_right_meta_data($this->id, "price_club", Request::post("price_club"));
					$this->manager->get_data_manager()->update_location_right_meta_data($this->id, "price_coach", Request::post("price_coach"));
					$this->manager->get_data_manager()->update_location_right_meta_data($this->id, "price_pupil", Request::post("price_pupil"));
					$this->manager->get_data_manager()->update_location_right_meta_data($this->id, "price_individual", Request::post("price_individual"));
					$this->manager->get_data_manager()->update_location_right_meta_data($this->id, "date_price_users", Calendar::get_date_timestamp_from_form("date_price_users"));
					$this->manager->get_data_manager()->update_location_right_meta_data($this->id, "max_price_1", Request::post("max_price_1"));
					$this->manager->get_data_manager()->update_location_right_meta_data($this->id, "max_price_2", Request::post("max_price_2"));
					$this->manager->get_data_manager()->update_location_right_meta_data($this->id, "date_max_price_1", Calendar::get_date_timestamp_from_form("date_max_price_1"));
					$this->manager->get_data_manager()->update_location_right_meta_data($this->id, "date_max_price_2", Calendar::get_date_timestamp_from_form("date_max_price_2"));
					$this->manager->get_data_manager()->update_location_right_meta_data($this->id, "credits_coach", Request::post("credits_coach"));
					$this->manager->get_data_manager()->update_location_right_meta_data($this->id, "credits_pupil", Request::post("credits_pupil"));
				}
			}
		}
		return implode("\n", $html);
	}
	
	public function get_html()
	{
		$html = array();
		$user = $this->manager->get_user();
		if($user->is_admin())
		{
			$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'pages/right/javascript/right_manager.js"></script>';
			$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'core/javascript/editable.js"></script>';
			$html[] = '<div id="right_info">';
			$save_changes = $this->save_changes();
			$location = $this->manager->get_data_manager()->retrieve_location_right($this->id);
			if($this->id != 0)
			{
				$html[] = '<div id="tabs">';
				$html[] = '<ul width="95%">';
				$html[] = '<li><a href="#general">'. Language::get_instance()->translate(402) .'</a></li>';
				$html[] = '<li><a href="#groups">'. Language::get_instance()->translate(403) .'</a></li>';
				$html[] = '<li><a href="#users">'. Language::get_instance()->translate(404) .'</a></li>';
				$html[] = '</ul>';
				$html[] = '<div id="general">';
				if($this->selected_tab == 0)
					$html[] = $save_changes;
				$html[] = $this->manager->get_renderer()->get_general_tab($location, $this->object_id);
				$html[] = '</div>';
				$html[] = '<div id="groups">';
				if($this->selected_tab == 1)
					$html[] = $save_changes;
				$html[] = $this->manager->get_renderer()->get_location_right_form($location, RightRenderer::GROUP_TYPE, $this->object_id);
				$html[] = $this->manager->get_renderer()->get_location_right_table($location->get_id(), RightRenderer::GROUP_TYPE, $this->object_id);
				$html[] = '</div>';
				$html[] = '<div id="users">';
				if($this->selected_tab == 2)
					$html[] = $save_changes;
				$html[] = $this->manager->get_renderer()->get_location_right_form($location, RightRenderer::USER_TYPE, $this->object_id);
				$html[] = $this->manager->get_renderer()->get_location_right_table($location->get_id(), RightRenderer::USER_TYPE, $this->object_id);
				$html[] = '</div>';
				$html[] = '</div>';
				$html[] = '<script type="text/javascript">';
	       		$html[] = '  var tabnumber = ' . $this->selected_tab . ';';
	       		$html[] = '</script>';
			}
			else
				$html[] = '<h3 class="title" style="float: right;">' . Language::get_instance()->translate(405) . '</h3>';
			$html[] = '</div>';
			$html[] = '<div id="right_hide" class="hide_right">';
			$html[] = '<div id="right_table">';
			$html[] = $this->manager->get_renderer()->get_location_table($this->id);
			$html[] = '</div>';
			$html[] = '</div>';
		}
		else
			$html[] = '<p class="error">' . Language::get_instance()->translate(85) . '</p>';
		
		return implode("\n",$html);
	}

	public function get_title()
	{
		return "";
	}
	
	public function get_description()
	{
		return '<p style="vertical-align:top;font-style:italic;font-size:11px;">' . Language::get_instance()->translate(406) . '</p>';
	}
	
}

?>