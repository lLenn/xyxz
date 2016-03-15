<?php

class RightRenderer
{

	const USER_TYPE = 'user';
	const GROUP_TYPE = 'group';
	
	private $manager;
	
	function RightRenderer($manager)
	{
		$this->manager = $manager;
	}
	
	public function get_actions()
	{
		
	}
	
	public function get_icon()
	{

	}
	
	public function get_general_tab($location, $object_id)
	{
		$html = array();
		$html[] = '<form method="post" action=""><div style="overflow: hidden"><div style="float: left;"><h3 class="title">' . Language::get_instance()->translate(639) . ':</h3></div>';
		if($location->get_id()!=RightManager::USER_LOCATION_ID && $location->get_id()!=RightManager::PUZZLE_LOCATION_ID && $location->get_id()!=RightManager::LESSON_LOCATION_ID && $location->get_id()!=RightManager::LESSON_EXCERCISE_LOCATION_ID)
		{
			$html[] = '<div style="float: left; margin-top: 17px; margin-left: 20px;">' . $location->get_location() . '<br/>' . Language::get_instance()->translate(794) . ': <input type="text" name="credits_buy" style="width: 30px; height: 14px; font-size: 11px" value="' . $location->get_credits_buy() . '"/><br/>' . Language::get_instance()->translate(795) . ': <input type="text" name="credits_sell" style="width: 30px; height: 14px; font-size: 11px" value="' . $location->get_credits_sell() . '"/><br/>' . Language::get_instance()->translate(1170) . ': <input type="text" name="credits_accepted" style="width: 30px; height: 14px; font-size: 11px" value="' . $location->get_credits_accepted() . '"/>';
			$html[] = '<br/><a class="text_link" href="javascript:;" id="submit_form">' . Language::get_instance()->translate(56) . '</a><br class="clearfloat"/><br/></div><div class="clearfloat">';
		}
		elseif($location->get_id()!=RightManager::USER_LOCATION_ID)
		{
			$html[] = '<div style="float: left; margin-top: 17px; margin-left: 20px;">' . $location->get_location() . '<br/>';
			$diffuculty_manager = new DifficultyManager($this->manager->get_user());
			foreach($diffuculty_manager->get_data_manager()->retrieve_difficulty_clusters() as $cluster)
			{
				$html[] = $cluster->get_name() . ":<br/>";
				$html[] = Language::get_instance()->translate(794) . ': <input type="text" name="credits_buy_' . $cluster->get_id() . '" style="width: 30px; height: 14px; font-size: 11px" value="' . $this->manager->get_data_manager()->retrieve_location_right_meta_data($location->get_id(), "credits_buy_" . $cluster->get_id()) . '"/><br/>';
				$html[] = Language::get_instance()->translate(795) . ': <input type="text" name="credits_sell_' . $cluster->get_id() . '" style="width: 30px; height: 14px; font-size: 11px" value="' . $this->manager->get_data_manager()->retrieve_location_right_meta_data($location->get_id(), "credits_sell_" . $cluster->get_id()) . '"/></br>';
				$html[] = Language::get_instance()->translate(1170) . ': <input type="text" name="credits_accepted_' . $cluster->get_id() . '" style="width: 30px; height: 14px; font-size: 11px" value="' . $this->manager->get_data_manager()->retrieve_location_right_meta_data($location->get_id(), "credits_accepted_" . $cluster->get_id()) . '"/></br>';
			}
			$html[] = '<br/><a class="text_link" href="javascript:;" id="submit_form">' . Language::get_instance()->translate(56) . '</a><br class="clearfloat"/><br/></div><div class="clearfloat">';
		}
		else
		{
			$html[] = '<div style="float: left; margin-top: 17px; margin-left: 20px;">' . $location->get_location() . '<br/>';
			$html[] = Language::get_instance()->translate(1137) . " " . Language::get_instance()->translate(641) . ': <input type="text" name="price_club" style="width: 30px; height: 14px; font-size: 11px" value="' . $this->manager->get_data_manager()->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "price_club") . '"/>&euro;<br/>';
			$html[] = Language::get_instance()->translate(1137) . " " . Language::get_instance()->translate(642) . ': <input type="text" name="price_coach" style="width: 30px; height: 14px; font-size: 11px" value="' . $this->manager->get_data_manager()->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "price_coach") . '"/>&euro;<br/>';
			$html[] = Language::get_instance()->translate(1137) . " " . Language::get_instance()->translate(643) . ': <input type="text" name="price_pupil" style="width: 30px; height: 14px; font-size: 11px" value="' . $this->manager->get_data_manager()->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "price_pupil") . '"/>&euro;<br/>';
			$html[] = Language::get_instance()->translate(1137) . " " . Language::get_instance()->translate(1233) . ': <input type="text" name="price_individual" style="width: 30px; height: 14px; font-size: 11px" value="' . $this->manager->get_data_manager()->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "price_individual") . '"/>&euro; ' . Language::get_instance()->translate(1223) . Calendar::get_date_selector("date_price_users", floatval($this->manager->get_data_manager()->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "date_price_users")), false) . '<br/>';
			$html[] = Language::get_instance()->translate(1201) . ': <input type="text" name="max_price_1" style="width: 30px; height: 14px; font-size: 11px" value="' . $this->manager->get_data_manager()->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "max_price_1") . '"/>&euro; ' . Language::get_instance()->translate(1223) . Calendar::get_date_selector("date_max_price_1", floatval($this->manager->get_data_manager()->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "date_max_price_1")), false) . '<br/>';
			$html[] = Language::get_instance()->translate(1201) . ': <input type="text" name="max_price_2" style="width: 30px; height: 14px; font-size: 11px" value="' . $this->manager->get_data_manager()->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "max_price_2") . '"/>&euro; ' . Language::get_instance()->translate(1223) . Calendar::get_date_selector("date_max_price_2", floatval($this->manager->get_data_manager()->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "date_max_price_2")), false) . '<br/><br/>';
			$html[] = Language::get_instance()->translate(793) . " " . Language::get_instance()->translate(642) . ': <input type="text" name="credits_coach" style="width: 30px; height: 14px; font-size: 11px" value="' . $this->manager->get_data_manager()->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "credits_coach") . '"/><br/>';
			$html[] = Language::get_instance()->translate(793) . " " . Language::get_instance()->translate(643) . ': <input type="text" name="credits_pupil" style="width: 30px; height: 14px; font-size: 11px" value="' . $this->manager->get_data_manager()->retrieve_location_right_meta_data(RightManager::USER_LOCATION_ID, "credits_pupil") . '"/>';
			$html[] = '<br/><a class="text_link" href="javascript:;" id="submit_form">' . Language::get_instance()->translate(56) . '</a><br class="clearfloat"/><br/></div><div class="clearfloat">';
		}
		$html[] = '<input type="hidden" value="1" name="credits"/>';
		$html[] = '</div></div></form>';
		if(!is_null($object_id))
		{
			$location_right = $this->manager->get_data_manager()->retrieve_location_right($location->get_id());
			$function_one = $location_right->get_function_one();
			$id = $location_right->get_primary_key();
			$description = $location_right->get_description();
			$object_manager = RightManager::instance()->get_location_manager($location_right);

			$object = $object_manager->get_data_manager()->$function_one($object_id);
			$html[] = '<h3 class="title">' . Language::get_instance()->translate(365) . ':</h3>';
			$html[] = $object_manager->get_renderer()->$description($object);
		}
		$html[] = $this->manager->get_renderer()->get_location_right_objects_table($location->get_id());
		return implode("\n", $html);
	}
	
	public function get_location_right_objects_table($location_id)
	{
		$location_right = $this->manager->get_data_manager()->retrieve_location_right($location_id);
		
		$primary_key = $location_right->get_primary_key();
		$method_name = $location_right->get_function_all();
		$row_header = $location_right->get_row_header();
		$row_renderer = $location_right->get_row_renderer();
		
		$object_manager = RightManager::instance()->get_location_manager($location_right);
		
		$html = array();
		$html[] = '<h3 class="title">' . Language::get_instance()->translate(366) . ':</h3>';
		
		$objects = $object_manager->get_data_manager()->$method_name();
		if(count($objects))
		{
			$html[] = '<table>';
			$html[] = '<theader>';
			$html[] = $object_manager->get_renderer()->$row_header();
			$html[] = '<th>&nbsp;</th>';
			$html[] = '</theader>';
			$html[] = '<tbody>';
			foreach($objects as $object)
				$html[] = $this->get_object_row($object_manager, $row_renderer, $object, $primary_key, $location_id);
			$html[] = '</tbody>';
			$html[] = '</table>';
		}
		else 
			$html[] = '<p style="padding-left: 20px;">' . Language::get_instance()->translate(367) . '</p>';
			
		return implode("\n", $html);
	}
	
	public function get_object_row($object_manager, $row_renderer, $object, $primary_key, $location_id)
	{
		$html = array();
		$html[] = '</tr>';
		$html[] = $object_manager->get_renderer()->$row_renderer($object);
		$html[] = '<td>';
		$html[] = '<form action="" method="post">';
		$html[] = '<input type="hidden" name="location_id" value="'.$location_id.'" />';
		$html[] = '<input type="hidden" name="object_id" value="'.$object->$primary_key().'" />';
		$html[] = '<input type="hidden" name="todo" value="3" />';
		$html[] = '<img class="right_record" src="' . Path :: get_url_path() . '/layout/images/buttons/edit.png" title="' . Language::get_instance()->translate(368) . '" style="border: 0">';
		$html[] = '</form>';
		$html[] = '</td>';
		$html[] = '</tr>';
		return implode("\n", $html);
	}
	
	public function get_location_right_table($location_id, $type, $object_id = null)
	{
		$object = '';
		if(!is_null($object_id))
			$object = '_object';
	
		$type_name = '';
		$method_name = '';
		switch($type)
		{
			case self::USER_TYPE: $type_name = Language::get_instance()->translate(108);
							$type_plurar = Language::get_instance()->translate(369);
							$gn_type_plurar = Language::get_instance()->translate(371);
							$method_name = 'retrieve_location'.$object.'_user_rights_by_location';
							break;
			case self::GROUP_TYPE: $type_name = Language::get_instance()->translate(92);
							 $type_plurar = Language::get_instance()->translate(370);
							 $gn_type_plurar = Language::get_instance()->translate(372);
							 $method_name = 'retrieve_location'.$object.'_group_rights_by_location';
							 break;
		}
		$html = array();
		$html[] = '<h3 class="title">'.$type_plurar.':</h3>';
		
		$rights = null;
		if(!is_null($object_id))
			$rights = $this->manager->get_data_manager()->$method_name($location_id, $object_id);
		else
			$rights = $this->manager->get_data_manager()->$method_name($location_id);

		if(count($rights))
		{
			$html[] = '<table>';
			$html[] = '<thead><tr>';
			$html[] = '<th>'.$type_name.'</th>';
			if(is_null($object_id))
				$html[] = '<th>' . Language::get_instance()->translate(373) . '</th>';
			$html[] = '<th>' . Language::get_instance()->translate(374) . '</th>';
			$html[] = '<th>' . Language::get_instance()->translate(375) . '</th>';
			$html[] = '<th>' . Language::get_instance()->translate(56) . '</th>';
			$html[] = '<th>&nbsp;</th>';
			$html[] = '</tr></thead><tbody>';
			foreach($rights as $right)
				$html[] = $this->get_row($right, $type, $object_id);
			$html[] = '</tbody></table>';
		}
		else 
			$html[] = '<p style="padding-left: 20px;">'.$gn_type_plurar.'</p>';
			
		return implode("\n", $html);
	}
	
	public function get_row($right, $type, $object_id = null)
	{
		$html = array();
		$type_object_name = '';
		$type_name = '';
		$type_method = '';
		$allowed_objects = '';
		switch($type)
		{
			case self::USER_TYPE: $udm = UserDataManager::instance($this->manager);
							$user = $udm->retrieve_user($right->get_user_id());
							if(!is_null($user))
								$type_object_name = $user->get_name() . " - " . $user->get_username();
							else
								$type_object_name = Language::get_instance()->translate(108);
							$type_name = Language::get_instance()->translate(377);
							$type_method = 'get_user_id';
							break;
			case self::GROUP_TYPE: $gdm = GroupDataManager::instance($this->manager);
							 $group = $gdm->retrieve_group($right->get_group_id());
							 $type_object_name = $group->get_name();
							 $type_name = Language::get_instance()->translate(376);
							 $type_method = 'get_group_id';
							 break;
		}
		$ja = Language::get_instance()->translate(378);
		$nee = Language::get_instance()->translate(379);
		$read = ($right->get_read()?$ja:$nee);
		$write = ($right->get_write()?$ja:$nee);
		$update = ($right->get_update()?$ja:$nee);
		
		if(is_null($object_id))
			$allowed_objects = ($right->get_allowed_objects()==-1?Language::get_instance()->translate(380):$right->get_allowed_objects());
		$html[] = '<tr>';
		$html[] = '<td>'.$type_object_name.'</td>';
		if(is_null($object_id))
			$html[] = '<td>'.$allowed_objects.'</td>';
		$html[] = '<td>'.$read.'</td>';
		$html[] = '<td>'.$write.'</td>';
		$html[] = '<td>'.$update.'</td>';
        $html[] = '<td>';
		$html[] = '<form action="" method="post">';
		$html[] = '<input type="hidden" name="location_id" value="'.$right->get_location_id().'" />';
		$html[] = '<input type="hidden" name="'.$type.'_id" value="'.$right->$type_method().'" />';
		if(!is_null($object_id))
			$html[] = '<input type="hidden" name="object_id" value="'.$object_id.'" />';
		$html[] = '<input type="hidden" name="todo" value="2" />';
		$html[] = '<img class="delete_record" src="' . Path :: get_url_path() . '/layout/images/buttons/delete.png" title="'.$type_name.'" style="border: 0">';
		$html[] = '</form></td>';
		$html[] = '</tr>';
		return implode("\n", $html);
	}
	
	// DEPRECATED
	/*
	public function get_location_form($location = null)
	{
		$html = array();
		$html[] = '<h3 class="title">' . (!is_null($location)?Language::get_instance()->translate(381):Language::get_instance()->translate(382)) . '</h3>';
		$html[] = '<div class="title">' . Language::get_instance()->translate(383) . '</div>';
		$html[] = '<p>';
		$html[] = '<form action="" method="post">';
		$html[] = '<div class="record">';
		$html[] = '<input type="hidden" name="id" value="' . (!is_null($location)?$location->get_id():'0') . '" />';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(66) . ' :</div><div class="record_input"><input type="text" name="location" value="' . (!is_null($location)?$location->get_location():'') . '"/></div>';
		$html[] = '</p>';
		$html[] = '<p><a class="link_button" id="submit_form" href="javascript:;">'.(!is_null($location)?Language::get_instance()->translate(56):Language::get_instance()->translate(49)) . '"</a></p>';
		$html[] = '</div>';
		$html[] = '</form>';
		return implode("\n", $html);
	}
	*/
	
	public function get_location_right_form($location_right, $type, $object_id = null)
	{
		$type_name = '';
		switch($type)
		{
			case self::USER_TYPE: $type_name = Language::get_instance()->translate(108);
								  $type_name_tv = Language::get_instance()->translate(384);
								  $type_name_r = Language::get_instance()->translate(385);
							break;
			case self::GROUP_TYPE: $type_name = Language::get_instance()->translate(92);
								   $type_name_tv = Language::get_instance()->translate(91);
								   $type_name_r = Language::get_instance()->translate(386);
							 break;
		}
	
		$html = array();
		$html[] = '<h3 class="title">'.$type_name_tv.':</h3>';
		$html[] = '<div class="title">'.$type_name_r.'</div>';
		$html[] = '<p>';
		$html[] = '<form action="" method="post">';
		$html[] = '<input type="hidden" name="location_id" value="' . $location_right->get_id() . '" />';
		$html[] = '<div class="record"><div class="record_name_required">' . Language::get_instance()->translate(388) . ':</div><div class="record_input">' . $location_right->get_location() . '</div><div class="clearfloat"></div></div>';
		if(!is_null($object_id))
		{
			$function_one = $location_right->get_function_one();
			$id = $location_right->get_primary_key();
			$description = $location_right->get_description();
			$object_manager = RightManager::instance()->get_location_manager($location_right);
			$object = $object_manager->get_data_manager()->$function_one($object_id);
			$html[] = '<input type="hidden" name="object_id" value="' . $object_id . '" />';
			$html[] = '<div class="record"><div class="record_name_required">' . Language::get_instance()->translate(387) . ' :</div>';
			$html[] = '<div class="record_output" style="margin-left: -20px;">';
			$html[] = $object_manager->get_renderer()->$description($object);
			$html[] = '</div></div>';
		}
		$html[] = '<div class="record"><div class="record_name_required">'.$type_name.':</div><div class="record_input">';
		switch($type)
		{
			case self::USER_TYPE: $um = new UserManager($this->manager->get_user());
							$html[] = $um->get_renderer()->get_selector();
							break;
			case self::GROUP_TYPE: $gm = new GroupManager($this->manager->get_user());
							 $html[] = $gm->get_renderer()->get_selector(0, 'group_id', true);
							 break;
		}
		$html[] = '</div></div>';
		if(is_null($object_id))
				$html[] = '<div class="record"><div class="record_name_required">' . Language::get_instance()->translate(389) . ' :</div><div class="record_input"><input name="allowed_objects" type="text" value="' . Language::get_instance()->translate(380) . '"/></div><div class="clearfloat"></div></div>';
		$html[] = '<div class="record"><div class="record_name_required">' . Language::get_instance()->translate(374) . ' :</div><div class="record_input"><input name="read" type="checkbox"/></div><div class="clearfloat"></div></div>';
		$html[] = '<div class="record"><div class="record_name_required">' . Language::get_instance()->translate(375) . ' :</div><div class="record_input"><input name="write" type="checkbox"/></div><div class="clearfloat"></div></div>';
		$html[] = '<div class="record"><div class="record_name_required">' . Language::get_instance()->translate(56) . '  :</div><div class="record_input"><input name="update" type="checkbox"/></div><div class="clearfloat"></div></div>';
		$html[] = '</p>';
		$html[] = '<div class="record">';
		$html[] = '<div class="record_button"><a class="link_button" id="submit_form" href="javascript:;">' . Language::get_instance()->translate(49) . '</a></div>';
		$html[] = '</div>';
		$html[] = '</form>';
		return implode("\n", $html);
	}
	
	public function get_map_form($map = null)
	{
		$html = array();
		$html[] = '<h3 class="title">'.Language::get_instance()->translate(888).':</h3>';
		$html[] = '<p>';
		$html[] = '<form action="" method="post">';
		$html[] = '<div class="record"><div class="record_name_required">' . Language::get_instance()->translate(66) . '  :</div><div class="record_input"><input name="name" type="text"' . (!is_null($map)?' value = "' . $map->get_name() . '"':'') . '/></div><div class="clearfloat"></div></div>';
		$html[] = '</p>';
		$html[] = '<div class="record">';
		$html[] = '<div class="record_button"><a class="link_button" id="submit_form" href="javascript:;">' . (!is_null($map)?Language::get_instance()->translate(56):Language::get_instance()->translate(49)) . '</a></div>';
		$html[] = '</div>';
		$html[] = '</form>';
		return implode("\n", $html);
	}
	
	public static function render_map($user, $map, $section, $output)
	{
		$html = array();
		$html[] = '<div style="margin-left: 20px; margin-top: 10px;">';
		$check_map = RightDataManager::instance(null)->retrieve_location_user_map_by_id($map->get_id(), $user->get_id());
		if(!is_null($check_map))
		{
			$html[] = "<div style='float: right;'>";
			$html[] = Display::display_icon("down", "", "", Url::create_url(array("page" => "move_map_down", "section" => $section, "id" => $map->get_id())));
			$html[] = Display::display_icon("up", "", "", Url::create_url(array("page" => "move_map_up", "section" => $section, "id" => $map->get_id())));
			$html[] = Display::display_icon("edit", "", "", Url::create_url(array("page" => "edit_map", "section" => $section, "id" => $map->get_id())));
			$html[] = Display::display_icon("delete", "", "", Url::create_url(array("page" => "delete_map", "section" => $section, "id" => $map->get_id())));
			$html[] = "</div>";
		}
		$html[] = '<h3 class="title">'.$map->get_name().'</h3>';
		$html[] = '</div>';
		$html[] = '<div style="margin-left: 20px;">';
		$html[] = $output;
		$html[] = '</div>';
		return implode("\n", $html);
	}
	
	public function get_map_relation_form($location_id, $user_id)
	{
		$html = array();
		$html[] = '<h3 class="title">'.Language::get_instance()->translate(893).':</h3>';
		$html[] = '<p>';
		$html[] = '<form action="" method="post">';
		$html[] = '<div class="record"><div class="record_name_required">' . Language::get_instance()->translate(894) . '  :</div><div class="record_input">' . $this->get_user_map_selector($location_id, $user_id) . '</div><div class="clearfloat"></div></div>';
		$html[] = '</p>';
		$html[] = '<div class="record">';
		$html[] = '<div class="record_button"><a class="link_button" id="submit_form" href="javascript:;">' . Language::get_instance()->translate(56) . '</a></div>';
		$html[] = '</div>';
		$html[] = '</form>';
		return implode("\n", $html);
	}

	public function get_user_map_selector($location_id, $user_id)
	{
		$html = array();
		$html[] = '<select class="input_element" name="map_id" style="min-width: 125px;">';
		$html[] = '<option value="0">' . Language::get_instance()->translate(62) . '</option>';
		$arr = $this->manager->get_data_manager()->retrieve_location_user_maps($location_id, $user_id);
		foreach($arr as $value)
		{ 
			$html[] = '<option value="'.$value->get_id().'">'.$value->get_name().'</option>';
		}
		$html[] = '</select>';
		return implode("\n", $html);
	}
	
	public function get_location_table()
	{
		$html = array();
		$html[] = '<table>';
		$html[] = '<tr><th style="min-width: 100px">' . Language::get_instance()->translate(383) . '</th></tr>';
		$arr = $this->manager->get_locations();
		foreach($arr as $value)
			$html[] = '<tr><td><a href="'.Url::create_url(array('page' => 'browse_rights', 'id' =>$value->get_id())).'">' . $value->get_location(). '</td></tr>';
		$html[] = '</table>';
		return implode("\n", $html);
	}
	
	public function get_location_selector($id = 0,$name='location_id')
	{
		$html[] = array();
		$html[] = '<select class="input_element" name="'.$name.'" style="width: 125px;">';
		$html[] = '<option value="0">' . Language::get_instance()->translate(383) . ':</option>';
		$arr = $this->manager->get_locations();
		foreach($arr as $value){
			$str = '<option value="'.$value->get_id().'"';
			if($id == $value->get_id()) $str .= " selected='selected'";
			$str .= ">".$value->get_location()."</option>";
			$html[] = $str;
		}
		$html[] = '</select>';
		return implode("\n", $html);
	}

	public function get_object_review_form($object_review, $update)
	{
		$html = array();
		$html[] = '<h3 class="title">'.Language::get_instance()->translate(1267).'</h3>';
		$html[] = '<p style="vertical-align:top;font-style:italic;font-size:11px;">';
		$html[] = '('.Language::get_instance()->translate(1270).')';
		$html[] = '</p>';
		$html[] = '<form action="" method="post">';
		$html[] = '<div class="record"><div class="record_name_required">' . Language::get_instance()->translate(274) . '  :</div>';
		$html[] = '<div class="record_input" style="width: 750px;">' . $this->get_rating_selector($object_review->get_rating());
		$html[] = '<div style="padding-left: 3px; padding-top: 2px; margin-right: 5px; float: right;">' . Language::get_instance()->translate(1273) . '</div><div style="float: right;"><input type="checkbox" name="anonymous"' . ($object_review->get_anonymous()?' CHECKED':'') . '/></div>';
		$html[] = '</div>';
		$html[] = "<div class='record_name_required'>".Language::get_instance()->translate(1269)." :</div>";
		$html[] = "<div class='record_input'><textarea name='review' style='width:550px;height:300px;padding:3px; 5px;'>" . $object_review->get_review() . "</textarea></div><br class='clearfloat'/>";
		$html[] = "<input type='hidden' name='object_id' value='" . $object_review->get_object_id() . "'/>";
		$html[] = "<input type='hidden' name='location_id' value='" . $object_review->get_location_id() . "'/>";
		$html[] = '<div class="record_button"><a class="link_button" id="submit_review_form" href="javascript:;">' . ($update?Language::get_instance()->translate(56):Language::get_instance()->translate(452)) . '</a></div>';
		$html[] = '<div class="record_button"><a class="link_button" id="cancel_review_form" href="javascript:;">' . Language::get_instance()->translate(1024) . '</a></div>';
		$html[] = '</div>';
		$html[] = '</form>';
		return implode("\n", $html);
	}
	
	public function get_object_reviews($location_id, $object_id)
	{
		$reviews = $this->manager->get_data_manager()->retrieve_location_object_reviews($location_id, $object_id);
		$html = array();
		$html[] = '<h2 class="title">' . Language::get_instance()->translate(1278) . '</h2>';
		$count = count($reviews);
		if($count)
		{
			foreach($reviews as $index => $review)
			{
				$html[] = $this->get_review($review);
				if($index+1 != $count)
					$html[] = '<div class="dashed_div" style="width: 450px;"></div>';
			}
		}
		else
			$html[] = "<p>" . Language::get_instance()->translate(1280) . "</p>";
		return implode("\n", $html);
	}
	
	public function get_review($review)
	{
		$user = UserDataManager::instance($this->manager)->retrieve_user($review->get_user_id());
		$html = array();
		$html[] = '<div class="message_container" style="width: 500px;">';
		$html[] = '<div class="message_left" style="width: 30px;">';
		if($review->get_anonymous() || is_null($user->get_avatar()) || $user->get_avatar()=="")
			$avatar = "./layout/images/standard.png";
		else
			$avatar = $user->get_avatar();
		$html[] = "<img src='".$avatar."' height='30' style='margin-right: 7px;'>";
		$html[] = '</div>';
		$html[] = '<div class="message_right" style="width: 450px;">';
		$rating = $review->get_rating();
		$review_comment = $review->get_review();
		if($rating != null && is_numeric($rating) && $rating >= 0 && $rating <= 10)
			$html[] = '<div style="float: right;">' . $rating . '/10</div>';
		$html[] = sprintf(Language::get_instance()->translate(1277), ($review->get_anonymous()?Language::get_instance()->translate(1279):$user->get_name()));
		if($review_comment != null && $review_comment != "")
		{
			$html[] = '<div class="message" style="min-height: 20px; margin-top: 3px;">';
			$html[] = nl2br($review_comment);
			$html[] = '</div>';
		}
		$date = ($review->get_last_edited()==0?$review->get_added():$review->get_last_edited());
		$html[] = '<p class="sideinfo" style="margin: 1";>' . Language::get_instance()->translate(111) . ' ' . date("m/d/Y H:i:s", $date) . '</p>';		
		$html[] = '</div>';
		$html[] = '</div>';
		return implode("\n", $html);
	}
	
	public static function get_object_review_mini_form($object_id, $location_id)
	{
		$html = array();
		$html[] = '<br class="clearfloat"/>';
		$html[] = '<div style="width: 670px; margin-top: 5px;">';
		$html[] = '<div style="float: right; margin-top: 5px;" class="' . $object_id . ' ' . $location_id . '">';
		$html[] = "<a class='text_link show_review_form' href='javascript:;'>" . Language::get_instance()->translate(1271) . "</a>";
		$html[] = "<a class='text_link show_reviews' href='javascript:;'>" . Language::get_instance()->translate(1272) . "</a>";
		$html[] = '</div>';
		$html[] = '<div id="review_form_' . $object_id . '_' . $location_id . '" style="float: left;"></div>';
		$html[] = '</div>';
		$html[] = '<br class="clearfloat"/>';
		return implode("\n", $html);
	}
	
	public function get_rating_selector($rating = -1, $name='object_rating')
	{
		$html = array();
		$html[] = '<select class="input_element" name="'.$name.'" style="width: 125px;">';
		$html[] = '<option value="-1">' . Language::get_instance()->translate(1268) . '</option>';
		for($i=1;$i<=10;$i++){
			$str = '<option value="'.$i.'"';
			if($rating == $i) $str .= " selected='selected'";
			$str .= ">".$i."</option>";
			$html[] = $str;
		}
		$html[] = '</select>';
		return implode("\n", $html);
	}
	
}

?>