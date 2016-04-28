<?php

require_once Path :: get_path() . 'pages/right/lib/right.class.php';
require_once Path :: get_path() . 'pages/right/lib/location_right.class.php';
require_once Path :: get_path() . 'pages/right/lib/location_group.class.php';
require_once Path :: get_path() . 'pages/right/lib/location_user.class.php';
require_once Path :: get_path() . 'pages/right/lib/location_user_map.class.php';
require_once Path :: get_path() . 'pages/right/lib/location_user_map_relation.class.php';
require_once Path :: get_path() . 'pages/right/lib/location_user_right.class.php';
require_once Path :: get_path() . 'pages/right/lib/location_group_right.class.php';
require_once Path :: get_path() . 'pages/right/lib/location_object_user_right.class.php';
require_once Path :: get_path() . 'pages/right/lib/location_object_group_right.class.php';
require_once Path :: get_path() . 'pages/right/lib/location_object_meta_data.class.php';
require_once Path :: get_path() . 'pages/right/lib/location_object_review.class.php';

class RightDataManager extends DataManager
{
	const USER_TABLE_NAME = 'user';
	
	const LOC_TABLE_NAME = 'rights_location_right';
	const LOC_CLASS_NAME = 'LocationRight';
	const LOC_META_TABLE_NAME = 'rights_location_right_meta_data';
	
	const LOC_USER_TABLE_NAME = 'rights_location_user_right';
	const LOC_USER_CLASS_NAME = 'LocationUserRight';

	const LOC_GROUP_TABLE_NAME = 'rights_location_group_right';
	const LOC_GROUP_CLASS_NAME = 'LocationGroupRight';

	const LOC_OBJ_GROUP_TABLE_NAME = 'rights_location_object_group_right';
	const LOC_OBJ_GROUP_CLASS_NAME = 'LocationObjectGroupRight';

	const LOC_OBJ_USER_TABLE_NAME = 'rights_location_object_user_right';
	const LOC_OBJ_USER_CLASS_NAME = 'LocationObjectUserRight';

	const LOC_OBJ_META_TABLE_NAME = 'rights_location_object_meta_data';
	const LOC_OBJ_META_CLASS_NAME = 'LocationObjectMetaData';

	const LOC_OBJ_REV_TABLE_NAME = 'rights_location_object_review';
	const LOC_OBJ_REV_CLASS_NAME = 'LocationObjectReview';
		
	const LOC_USER_MAP_TABLE_NAME = 'rights_location_user_map';
	const LOC_USER_MAP_CLASS_NAME = 'LocationUserMap';
	
	const LOC_USER_MAP_REL_TABLE_NAME = 'rights_location_user_map_relation';
	const LOC_USER_MAP_REL_CLASS_NAME = 'LocationUserMapRelation';
	
	const LOC_OBJ_CRT_TABLE_NAME = 'rights_location_object_creation';
	
	public static function instance($manager)
	{
		parent::$_instance = new RightDataManager($manager);
		return parent::$_instance;
	}
	
	/** LOCATION RIGHT **/
	
	public function retrieve_location_right($id)
	{
		return parent::retrieve_by_id(self::LOC_TABLE_NAME,self::LOC_CLASS_NAME,$id);
	}
	
	public function retrieve_location_right_by_location($location)
	{
		$condition = "location = '" . $location . "'";
		return parent::retrieve(self::LOC_TABLE_NAME,self::LOC_CLASS_NAME,'',self::ONE_RECORD,$condition);
	}
	
	public function retrieve_location_rights()
	{
		$order = "location";
		return parent::retrieve(self::LOC_TABLE_NAME,self::LOC_CLASS_NAME,$order);
	}
	
	public function insert_location_right($right)
	{
		return parent::insert(self::LOC_TABLE_NAME,$right);
	}
	
	public function update_location_right($right)
	{
		return parent::update_by_id(self::LOC_TABLE_NAME,$right);
	}
	
	public function update_credits($right)
	{
		$custom = new CustomProperties();
		$custom->add_property("credits_buy", $right->get_credits_buy());
		$custom->add_property("credits_sell", $right->get_credits_sell());
		$custom->add_property("credits_accepted", $right->get_credits_accepted());
		$cond = "id = " . $right->get_id();
		return parent::update(self::LOC_TABLE_NAME, $custom, $cond);
	}
	
	public function delete_location_right($id)
	{
		return parent::delete_by_id(self::LOC_TABLE_NAME,$id);
	}
	
	public function retrieve_location_right_meta_data($location_id, $key)
	{
		$meta_data = parent::retrieve(self::LOC_META_TABLE_NAME, null, '', self::ONE_RECORD, 'location_id = ' . $location_id . ' AND `key` = "' . $key . '"');
		if(!is_null($meta_data))
			return $meta_data->value;
		else
			return null;
	}

	public function update_location_right_meta_data($location_id, $key, $value)
	{
		$custom = new CustomProperties();
		$custom->add_property("location_id", $location_id);
		$custom->add_property("key", $key);
		$custom->add_property("value", $value);
		if(!is_null($this->retrieve_location_right_meta_data($location_id, $key)))
			return parent::update(self::LOC_META_TABLE_NAME, $custom, "location_id = " . $location_id . " AND `key` = '" . $key . "'");
		else
			return parent::insert(self::LOC_META_TABLE_NAME, $custom);
	}
	
	/** LOCATION USER RIGHT **/
	
	public function retrieve_location_user_right($location_id, $user_id)
	{
		$condition = "location_id = '" . $location_id . "' AND user_id = '" . $user_id . "'";
		return parent::retrieve(self::LOC_USER_TABLE_NAME,self::LOC_USER_CLASS_NAME,'',self::ONE_RECORD,$condition);
	}
	
	public function retrieve_location_user_rights_by_location($location_id)
	{
		$condition = "location_id = '" . $location_id . "'";
		return parent::retrieve(self::LOC_USER_TABLE_NAME,self::LOC_USER_CLASS_NAME,'',self::MANY_RECORDS,$condition);
	}
	
	public function insert_location_user_right($right)
	{
		return parent::insert(self::LOC_USER_TABLE_NAME,$right);
	}
	
	public function update_location_user_right($right)
	{
		return parent::update_by_id(self::LOC_USER_TABLE_NAME,$right);
	}
	
	public function delete_location_user_right($location_user_right)
	{
		$condition = "location_id = '" . $location_user_right->get_location_id() . "' AND user_id = '" . $location_user_right->get_user_id() . "'";
		return parent::delete(self::LOC_USER_TABLE_NAME,$condition);
	}
	
	/** LOCATION GROUP RIGHT **/
	
	public function retrieve_location_group_right($location_id, $group_id)
	{
		$condition = "location_id = '" . $location_id . "' AND group_id = '" . $group_id . "'";
		return parent::retrieve(self::LOC_GROUP_TABLE_NAME,self::LOC_GROUP_CLASS_NAME,'',self::ONE_RECORD,$condition);
	}
	
	public function retrieve_location_group_rights_by_location($location_id)
	{
		$condition = "location_id = '" . $location_id . "'";
		return parent::retrieve(self::LOC_GROUP_TABLE_NAME,self::LOC_GROUP_CLASS_NAME,'',self::MANY_RECORDS,$condition);
	}
	
	public function insert_location_group_right($right)
	{
		return parent::insert(self::LOC_GROUP_TABLE_NAME,$right);
	}
	
	public function update_location_group_right($right)
	{
		return parent::update_by_id(self::LOC_GROUP_TABLE_NAME,$right);
	}
	
	public function delete_location_group_right($location_group_right)
	{
		$condition = "location_id = '" . $location_group_right->get_location_id() . "' AND group_id = '" . $location_group_right->get_group_id() . "'";
		return parent::delete(self::LOC_GROUP_TABLE_NAME,$condition);
	}
	
	/** LOCATION OBJECT GROUP RIGHT **/
	
	public function retrieve_location_object_group_right($location_id, $group_id, $object_id)
	{
		$condition = "location_id = '" . $location_id . "' AND group_id = '" . $group_id . "' AND object_id = '" . $object_id . "'";
		return parent::retrieve(self::LOC_OBJ_GROUP_TABLE_NAME,self::LOC_OBJ_GROUP_CLASS_NAME,'',self::ONE_RECORD,$condition);
	}
	
	public function retrieve_location_object_group_rights_by_location($location_id, $object_id)
	{
		$condition = "location_id = '" . $location_id . "' AND object_id = '" . $object_id . "'";
		return parent::retrieve(self::LOC_OBJ_GROUP_TABLE_NAME,self::LOC_OBJ_GROUP_CLASS_NAME,'',self::MANY_RECORDS,$condition);
	}
	
	public function retrieve_location_object_group_rights_by_group_id($location_id, $group_id)
	{
		$condition = "location_id = '" . $location_id . "' AND group_id = '" . $group_id . "'";
		return parent::retrieve(self::LOC_OBJ_GROUP_TABLE_NAME,self::LOC_OBJ_GROUP_CLASS_NAME,'',self::MANY_RECORDS,$condition);
	}
	
	public function insert_location_object_group_right($right)
	{
		return parent::insert(self::LOC_OBJ_GROUP_TABLE_NAME,$right);
	}
	
	public function update_location_object_group_right($right)
	{
		return parent::update_by_id(self::LOC_OBJ_GROUP_TABLE_NAME,$right);
	}
	
	public function delete_location_object_group_right($right)
	{
		$condition = "location_id = '" . $right->get_location_id() . "' AND group_id = '" . $right->get_group_id() . "' AND object_id = '" . $right->get_object_id() . "'";
		return parent::delete(self::LOC_OBJ_GROUP_TABLE_NAME,$condition);
	}
	
	/** LOCATION OBJECT USER RIGHT **/
	
	public function retrieve_location_object_user_right($location_id, $user_id, $object_id)
	{
		$condition = "location_id = '" . $location_id . "' AND user_id = '" . $user_id . "' AND object_id = '" . $object_id . "'";
		return parent::retrieve(self::LOC_OBJ_USER_TABLE_NAME,self::LOC_OBJ_USER_CLASS_NAME,'',self::ONE_RECORD,$condition);
	}
	
	public function retrieve_location_object_user_rights_by_location($location_id, $object_id)
	{
		$condition = "location_id = '" . $location_id . "' AND object_id = '" . $object_id . "'";
		return parent::retrieve(self::LOC_OBJ_USER_TABLE_NAME,self::LOC_OBJ_USER_CLASS_NAME,'',self::MANY_RECORDS,$condition);
	}
	
	public function retrieve_location_object_user_rights_by_user_id($location_id, $user_id)
	{
		$condition = "location_id = '" . $location_id . "' AND user_id = '" . $user_id . "'";
		return parent::retrieve(self::LOC_OBJ_USER_TABLE_NAME,self::LOC_OBJ_USER_CLASS_NAME,'',self::MANY_RECORDS,$condition);
	}
		
	public function insert_location_object_user_right($right)
	{
		if($right->get_update())
		{
			$custom = new CustomProperties();
			$custom->add_property("location_id", $right->get_location_id());
			$custom->add_property("object_id", $right->get_object_id());
			$custom->add_property("creator_id", $right->get_user_id());
			$custom->add_property("creation_time", time());
			parent::insert(self::LOC_OBJ_CRT_TABLE_NAME,$custom);
		}
		return parent::insert(self::LOC_OBJ_USER_TABLE_NAME,$right);
	}
	
	public function update_location_object_user_right($right)
	{
		return parent::update_by_id(self::LOC_OBJ_USER_TABLE_NAME,$right);
	}
	
	public function delete_location_object_user_right($right)
	{
		$condition = "location_id = '" . $right->get_location_id() . "' AND user_id = '" . $right->get_user_id() . "' AND object_id = '" . $right->get_object_id() . "'";
		return parent::delete(self::LOC_OBJ_USER_TABLE_NAME,$condition);
	}
	
	public function delete_location_object_user_rights($right)
	{
		$condition = "location_id = '" . $right->get_location_id() . "' AND object_id = '" . $right->get_object_id() . "'";
		return parent::delete(self::LOC_OBJ_USER_TABLE_NAME,$condition);
	}
	
	public function retrieve_location_right_from_post()
	{
		$data = array();
		$data['location_id'] = Request::post('location_id');
		if(Request::post('user_id'))
			$data['user_id'] = Request::post('user_id');
		if(Request::post('group_id'))
			$data['group_id'] = Request::post('group_id');
		if(Request::post('object_id'))
			$data['object_id'] = Request::post('object_id');
		else
		{
			$data['allowed_objects'] = Request::post('allowed_objects');
			if($data['allowed_objects'] == Language::get_instance()->translate(380))
				$data['allowed_objects'] = -1;
			elseif(is_null($data['allowed_objects']) || $data['allowed_objects'] == "" || !is_numeric($data['allowed_objects']) || $data['allowed_objects'] < 0)
				$data['allowed_objects'] = 0;
		}		
		$data['read'] = $this->parse_checkbox_value(Request::post('read'));
		$data['write'] = $this->parse_checkbox_value(Request::post('write'));
		$data['update'] = $this->parse_checkbox_value(Request::post('update'));

		if(Request::post('user_id') && is_null(Request::post('object_id')))
			return new LocationUserRight($data);
		elseif(Request::post('user_id') && Request::post('object_id'))
			return new LocationObjectUserRight($data);
		elseif(Request::post('group_id') && is_null(Request::post('object_id')))
			return new LocationGroupRight($data);
		elseif(Request::post('group_id') && Request::post('object_id'))
			return new LocationObjectGroupRight($data);
		else
			return false;
	}
	
	public function retrieve_made_objects($user_id, $location)
	{
		$join = array();
		$join[] = new Join(self::LOC_OBJ_USER_TABLE_NAME, 'p', 'object_id', Join :: MAIN_TABLE);
		$join[] = new Join(self::USER_TABLE_NAME, 'u', 'id', 'LEFT JOIN', Join :: MAIN_TABLE, "object_id");
		$condition = "p.location_id = " . $location . " AND p.user_id = " . $user_id . " AND u.parent_id = " . $user_id;
		return parent::retrieve($join,null,'',self::MANY_RECORDS,$condition);
	}
	
	public function retrieve_location_object_creator($location_id, $object_id)
	{
		$cond = "location_id = " . $location_id . " AND object_id = " . $object_id;
		$result = parent::retrieve(self::LOC_OBJ_CRT_TABLE_NAME,null,'',self::ONE_RECORD,$cond);
		if(!is_null($result))
			return $result->creator_id;
		else
			return null;
	}
	
	public function retrieve_location_object_creation($location_id, $object_id)
	{
		$cond = "location_id = " . $location_id . " AND object_id = " . $object_id;
		return parent::retrieve(self::LOC_OBJ_CRT_TABLE_NAME,null,'',self::ONE_RECORD,$cond);
	}
	
	/** LOCATION OBJECT META DATA **/
	
	public function retrieve_location_object_meta_data($location_id, $object_id, $key)
	{
		$condition = "location_id = '" . $location_id . "' AND object_id = '" . $object_id . "' AND meta_key = '" . $key . "'";
		return self::retrieve(self::LOC_OBJ_META_TABLE_NAME, self::LOC_OBJ_META_CLASS_NAME, '', self::ONE_RECORD, $condition);
	}
	
	public function delete_location_object_meta_data($location_id, $object_id, $key)
	{
		$condition = "location_id = '" . $location_id . "' AND object_id = '" . $object_id . "' AND meta_key = '" . $key . "'";
		return self::delete(self::LOC_OBJ_META_TABLE_NAME, $condition);
	}
	
	public function insert_location_object_meta_data($location_object_meta_data)
	{	
		self::insert(self::LOC_OBJ_META_TABLE_NAME, $location_object_meta_data);
	}
	
	/** LOCATION OBJECT REVIEW **/
	
	public function retrieve_location_object_review($location_id, $object_id, $user_id)
	{
		$condition = "location_id = '" . $location_id . "' AND object_id = '" . $object_id . "' AND user_id = '" . $user_id . "'";
		return self::retrieve(self::LOC_OBJ_REV_TABLE_NAME, self::LOC_OBJ_REV_CLASS_NAME, 'added DESC', self::ONE_RECORD, $condition);
	}
	
	public function retrieve_location_object_reviews($location_id, $object_id)
	{
		$condition = "location_id = '" . $location_id . "' AND object_id = '" . $object_id . "'";
		return self::retrieve(self::LOC_OBJ_REV_TABLE_NAME, self::LOC_OBJ_REV_CLASS_NAME, '', self::MANY_RECORDS, $condition);
	}
	
	
	public function delete_location_object_review($location_id, $object_id, $user_id)
	{
		$condition = "location_id = '" . $location_id . "' AND object_id = '" . $object_id . "' AND user_id = '" . $user_id . "'";
		return self::delete(self::LOC_OBJ_REV_TABLE_NAME, $condition);
	}
	
	public function insert_location_object_review($location_object_review)
	{	
		self::insert(self::LOC_OBJ_REV_TABLE_NAME, $location_object_review);
	}

	public function update_location_object_review($location_object_review)
	{	
		$conditions = "location_id = " . $location_object_review->get_location_id() . " AND user_id = " . $location_object_review->get_user_id() . " AND object_id = " . $location_object_review->get_object_id();
		self::update(self::LOC_OBJ_REV_TABLE_NAME, $location_object_review, $conditions);
	}
	
	public function retrieve_location_object_review_from_post()
	{
		$data = array();
		$data['location_id'] = Request::post('location_id');
		$data['user_id'] = $this->manager->get_user()->get_id();
		$data['object_id'] = Request::post('object_id');

		$data['rating'] = Request::post('object_rating');
		$data['review'] = addslashes(htmlspecialchars(Request::post('review')));
		$data['anonymous'] = DataManager::parse_checkbox_value(Request::post('anonymous'));
		
		if(($data['rating']==null || $data['rating']==-1) && 
		   ($data["review"]==null || $data["review"]==""))
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->append_message(Language::get_instance()->translate(1266));
		}
		
		return new LocationObjectReview($data);
	}
	/*
	public function update_location_object_meta_data($location_object_meta_data)
	{	
		self::update(self::LOC_OBJ_META_DATA_TABLE, $location_object_meta_data);
	}
	*/
	
	/* LOCATION USER MAP */
	
	public function retrieve_location_user_map_by_id($id, $user_id)
	{	
		$condition = "id = " . $id . " AND user_id = " .$user_id;
		return self::retrieve(self::LOC_USER_MAP_TABLE_NAME, self::LOC_USER_MAP_CLASS_NAME, '', self::ONE_RECORD, $condition);
	}
	
	public function insert_location_user_map($location_user_map)
	{	
		return self::insert(self::LOC_USER_MAP_TABLE_NAME, $location_user_map);
	}

	public function update_location_user_map($location_user_map)
	{	
		return self::update_by_id(self::LOC_USER_MAP_TABLE_NAME, $location_user_map);
	}

	public function change_location_user_map_order($id, $user_id, $up)
	{	
		$org = $this->retrieve_location_user_map_by_id($id, $user_id);

		$condition = "location_id = " . $org->get_location_id() . " AND user_id = " .$user_id;
		$order = "";
		if($up)
		{
			$condition .= " AND `order` < " . $org->get_order();
			$order = '`order` DESC';
		} 
		else
		{
			$condition .= " AND `order` > " . $org->get_order();
			$order = '`order` ASC';
		} 
		$to_chg = self::retrieve(self::LOC_USER_MAP_TABLE_NAME, self::LOC_USER_MAP_CLASS_NAME, $order, self::ONE_RECORD, $condition, '1');

		if(!is_null($to_chg))
		{
			$org_order = $org->get_order();
			$org->set_order($to_chg->get_order());
			$to_chg->set_order($org_order);
			
			$success = self::update_by_id(self::LOC_USER_MAP_TABLE_NAME, $org);
			$success &= self::update_by_id(self::LOC_USER_MAP_TABLE_NAME, $to_chg);
		}
	}
	
	public function delete_location_user_map($id)
	{	
		self::delete(self::LOC_USER_MAP_TABLE_NAME, "id = " . $id);
		self::delete(self::LOC_USER_MAP_REL_TABLE_NAME, "map_id = " . $id);
	}
	
	public function retrieve_location_user_maps($location_id, $user_id)
	{	
		$condition = "location_id = " . $location_id . " AND user_id = " .$user_id;
		return self::retrieve(self::LOC_USER_MAP_TABLE_NAME, self::LOC_USER_MAP_CLASS_NAME, "`order`", self::MANY_RECORDS, $condition);
	}
	
	public function retrieve_location_user_map($user_id, $object_id, $location_id)
	{	
		$join = array();
		$join[] = new Join(self::LOC_USER_MAP_TABLE_NAME, "l", "id", Join::MAIN_TABLE);
		$join[] = new Join(self::LOC_USER_MAP_REL_TABLE_NAME, "m", "map_id", "LEFT JOIN", Join::MAIN_TABLE, "id");
		$condition = "l.user_id = " . $user_id . " AND m.object_id = " .$object_id . " AND l.location_id = " . $location_id;
		return self::retrieve($join, self::LOC_USER_MAP_CLASS_NAME, '', self::ONE_RECORD, $condition);
	}
	
	public function check_location_user_map_name($location_id, $user_id, $name)
	{	
		return 0==self::count(self::LOC_USER_MAP_TABLE_NAME, "location_id = " . $location_id . " AND user_id = " . $user_id . " AND name = '" . $name . "'");
	}
	
	public function retrieve_location_user_map_from_post($location_id, $user_id)
	{	
		$data = array();
		$data["name"] = addslashes(htmlspecialchars(Request::post("name")));
		$data["order"] = 0;
		if($data["name"] == "")
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->set_message(87);
		}
		elseif($this->check_location_user_map_name($location_id, $user_id, $data["name"]))
		{
			$data["id"] = 0;
			$data["location_id"] = $location_id;
			$data["user_id"] = $user_id;
			return new LocationUserMap($data);
		}
		else
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->set_message(889);
		}
	}
	
	/* LOCATION USER MAP RELATION */
	public function delete_location_user_map_relation($id, $object_id)
	{
		return self::delete(self::LOC_USER_MAP_REL_TABLE_NAME, "map_id = " . $id . " AND object_id = " . $object_id);
	}

	public function delete_zero_location_user_map_relation($object_id, $location_id, $user_id)
	{
		return self::delete(self::LOC_USER_MAP_REL_TABLE_NAME, "map_id = 0 AND object_id = " . $object_id . " AND location_id = " . $location_id . " AND user_id = " . $user_id);
	}

	public function retrieve_location_user_map_relations($map_id)
	{	
		return self::retrieve(self::LOC_USER_MAP_REL_TABLE_NAME, self::LOC_USER_MAP_REL_CLASS_NAME, '', self::MANY_RECORDS, "map_id = " . $map_id);
	}
	
	public function insert_location_user_map_relation($location_user_map_relation)
	{	
		return self::insert(self::LOC_USER_MAP_REL_TABLE_NAME, $location_user_map_relation);
	}
	
	public function retrieve_location_user_map_relation_from_post($location_id, $user_id, $object_id)
	{	
		$data = array();
		$data["map_id"] = Request::post("map_id");
		$data["object_id"] = $object_id;
		if($data["map_id"] != 0)
		{
			$maps = $this->retrieve_location_user_maps($location_id, $user_id);
			$validation = false;
			foreach($maps as $map)
			{
				if($map->get_id() == $data["map_id"])
				{
					$validation = true;
					break;
				}
			}
			if(!$validation)
			{
				Error::get_instance()->set_result(false);
				Error::get_instance()->set_message(895);
			}
		}
		return new LocationUserMapRelation($data);
	}
	
	public static function get_right_conditions($right)
	{
		switch($right)
		{
			case RightManager::READ_RIGHT: return "`read` = 1"; break;
			case RightManager::WRITE_RIGHT: return "`write` = 1"; break;
			case RightManager::UPDATE_RIGHT: return "`update` = 1"; break;
		}
		return "read = 1";
	}
}

?>