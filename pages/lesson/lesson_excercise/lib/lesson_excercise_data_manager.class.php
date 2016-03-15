<?php

require_once Path :: get_path() . 'pages/lesson/lesson_excercise/lib/lesson_excercise.class.php';
require_once Path :: get_path() . 'pages/lesson/lesson_excercise/lib/lesson_excercise_component.class.php';

class LessonExcerciseDataManager extends DataManager
{
	const MET_EXC_TABLE_NAME = 'lesson_excercise_meta_data';
	const REL_EXC_TABLE_NAME = 'lesson_excercise_relation';
	const EXC_TABLE_NAME = 'lesson_excercise';
	const EXC_CLASS_NAME = 'LessonExcercise';
	const EXC_TABLE_USER_META_NAME = 'lesson_excercise_user_data';
	const TABLE_META_EXC_NAME = 'lesson_excercise_meta_data_criteria_excercise_ids';
	const RIGHT_TABLE_NAME = 'rights_location_object_user_right';
	const COMPONENT_TABLE_NAME = 'lesson_excercise_component';
	const COMPONENT_CLASS_NAME = 'LessonExcerciseComponent';
	const PUZZLE_STATS_TABLE_NAME = 'statistics_puzzle';
	const QUESTION_STATS_TABLE_NAME = 'statistics_question';
	const SELECTION_STATS_TABLE_NAME = 'statistics_selection';
	const LES_EXC_THEME_TABLE_NAME = 'lesson_excercise_theme';
	
	public static function instance($manager)
	{
		parent::$_instance = new LessonExcerciseDataManager($manager);
		return parent::$_instance;
	}
	
	/** LESSON_EXERCISE **/
	
	public function retrieve_all_data_excercise($id)
	{
		$excercise = array();
		$excercise["excercise"] = parent::retrieve_by_id(self::EXC_TABLE_NAME,null,$id);
		$excercise["components"] = parent::retrieve(self::COMPONENT_TABLE_NAME, null, '`order`',self::MANY_RECORDS, 'lesson_excercise_id = ' . $id);
		return $excercise;
	}
	
	public function retrieve_lesson_excercises()
	{
		return parent::retrieve(self::EXC_TABLE_NAME, self::EXC_CLASS_NAME);
	}
	
	public function retrieve_lesson_excercises_by_user_id($user_id, $map = null)
	{
		$join = array();
		$join[] = new Join(self::EXC_TABLE_NAME, 'e', 'id', Join::MAIN_TABLE);
		$join[] = new Join(self::EXC_TABLE_USER_META_NAME, 'u', 'lesson_excercise_id', 'LEFT JOIN', Join::MAIN_TABLE, 'id');
		$condition = "u.`user_id` = '" . $user_id . "'";
		$order = "`order`";
		if(!is_null($map))
		{
			$join[] = new Join(RightDataManager::LOC_USER_MAP_REL_TABLE_NAME, "um", "object_id", "LEFT JOIN", Join::MAIN_TABLE, "id");
			if($map == "others")
				$condition .= " AND um.map_id = 0 AND um.user_id = " . $user_id . " AND um.location_id = " . RightManager::LESSON_EXCERCISE_LOCATION_ID;
			else
				$condition .= " AND um.map_id = " . $map->get_id();
		}
		return parent::retrieve($join, self::EXC_CLASS_NAME, $order, self::MANY_RECORDS, $condition, '', 'e.*, u.*');
	}
	
	public function retrieve_lesson_excercises_by_user_id_and_visibility($user_id, $visible = true, $map = null)
	{
		$join = array();
		$join[] = new Join(self::EXC_TABLE_NAME, 'e', 'id', Join::MAIN_TABLE);
		$join[] = new Join(self::EXC_TABLE_USER_META_NAME, 'u', 'lesson_excercise_id', 'LEFT JOIN', Join::MAIN_TABLE, 'id');
		$condition = "u.user_id = '" . $user_id ."' AND u.`visible` = '" . ($visible?"1":"0") . "'";
		$order = "`order`";
		if(!is_null($map))
		{
			$join[] = new Join(RightDataManager::LOC_USER_MAP_REL_TABLE_NAME, "um", "object_id", "LEFT JOIN", Join::MAIN_TABLE, "id");
			if($map == "others")
				$condition .= " AND um.map_id = 0 AND um.user_id = " . $user_id . " AND um.location_id = " . RightManager::LESSON_EXCERCISE_LOCATION_ID;
			else
				$condition .= " AND um.map_id = " . $map->get_id();
		}
		return parent::retrieve($join, self::EXC_CLASS_NAME, $order, self::MANY_RECORDS, $condition, '', 'e.*, u.*');
	}
	
	public function retrieve_lesson_excercises_by_user_id_visibility_and_criteria($user_id, $visible = true, $map = null)
	{
		$join = array();
		$join[] = new Join(self::EXC_TABLE_NAME, 'e', 'id', Join::MAIN_TABLE);
		$join[] = new Join(self::EXC_TABLE_USER_META_NAME, 'u', 'lesson_excercise_id', 'LEFT JOIN', Join::MAIN_TABLE, 'id');
		$condition = "u.user_id = '" . $user_id ."' AND u.`visible` = '" . ($visible?"1":"0") . "'";
		$criteria = $visible?"(u.criteria_lesson_percentage != 0 OR u.criteria_lesson_excercise_percentage != 0)":"(u.criteria_lesson_percentage != 0 AND u.criteria_lesson_excercise_percentage != 0)";
		$condition = "( " . $condition .  " OR " . $criteria . " )";
		$order = "`order`";
		if(!is_null($map))
		{
			$join[] = new Join(RightDataManager::LOC_USER_MAP_REL_TABLE_NAME, "um", "object_id", "LEFT JOIN", Join::MAIN_TABLE, "id");
			if($map == "others")
				$condition .= " AND um.map_id = 0 AND um.user_id = " . $user_id . " AND um.location_id = " . RightManager::LESSON_EXCERCISE_LOCATION_ID;
			else
				$condition .= " AND um.map_id = " . $map->get_id();
		}
		return parent::retrieve($join, self::EXC_CLASS_NAME, $order, self::MANY_RECORDS, $condition, '', 'e.*, u.*');
	}
	
	public function filter_lesson_excercises(&$lesson_excercises_without_map, &$lesson_excercises_with_map, $user, $parent_user)
	{
		$all_lessons = $this->manager->get_parent_manager()->get_data_manager()->retrieve_lessons_by_visibility_and_criteria(true, $parent_user->get_id(), RightManager::READ_RIGHT, null);
		foreach($all_lessons as $index => $lesson)
		{
			$users = $lesson->get_user_ids();
			if(!empty($users) && !in_array($user->get_id(), $users))
				unset($all_lessons[$index]);
			elseif($lesson->get_visible())
			{
				$this->manager->get_parent_manager()->get_data_manager()->check_child($lesson, $all_lessons, $user);
			}
		}

		foreach($all_lessons as $index => $lesson)
		{
			if(!$lesson->get_visible() && !$lesson->get_criteria_lesson_percentage() && $lesson->get_criteria_lesson_excercise_percentage())
			{
				$this->manager->get_parent_manager()->get_data_manager()->check_criteria($lesson, $user);
				if(!$lesson->get_teaser())
					unset($all_lessons[$index]);
			}
			elseif(!$lesson->get_visible() && !$lesson->get_teaser())
				unset($all_lessons[$index]);
		}
		
		$lesson_excercises_with_map[] = $lesson_excercises_without_map;
		foreach($lesson_excercises_with_map as $m_index => $lesson_excercises)
		{
			foreach($lesson_excercises as $index => $lesson_excercise)
			{
				$users = $lesson_excercise->get_user_ids();
				if(!empty($users) && !in_array($user->get_id(), $users))
					unset($lesson_excercises[$index]);
				elseif(!$lesson_excercise->get_visible())
				{
					$lesson_visible = false;
					if($lesson_excercise->get_criteria_lesson_percentage())
					{
						foreach ($all_lessons as $lesson)
						{
							if($lesson->get_id() == $lesson_excercise->get_criteria_lesson_id())
							{
								$lesson_visible = $lesson->get_visible();
							}
						}
					}
					$this->manager->get_parent_manager()->get_data_manager()->check_criteria($lesson_excercise, $user, $lesson_visible);
					if(!$lesson_excercise->get_teaser() && !$lesson_excercise->get_visible())
						unset($lesson_excercises[$index]);
				}
			}
			$lesson_excercises_with_map[$m_index] = $lesson_excercises;
		}

		$lesson_excercises_without_map = $lesson_excercises_with_map[count($lesson_excercises_with_map)-1];
		unset($lesson_excercises_with_map[count($lesson_excercises_with_map)-1]);
	}
	
	public function retrieve_visible_and_new_lesson_excercises_by_user_id($user_id)
	{
		$join = array();
		$join[] = new Join(self::EXC_TABLE_NAME, 'e', 'id', Join::MAIN_TABLE);
		$join[] = new Join(self::EXC_TABLE_USER_META_NAME, 'u', 'lesson_excercise_id', 'LEFT JOIN', Join::MAIN_TABLE, 'id');
		$condition = "u.user_id = '" . $user_id ."' AND u.`visible` = '1' AND u.`new` = '1'";
		$order = "`order`";
		return parent::retrieve($join, self::EXC_CLASS_NAME, $order, self::MANY_RECORDS, $condition, '', 'e.*, u.*');
	}
	
	public function retrieve_lesson_excercises_by_conditions($conditions)
	{
		return parent::retrieve(self::EXC_TABLE_NAME, self::EXC_CLASS_NAME, '', self::MANY_RECORDS, $conditions);
	}
	
	public function add_new_lesson_excercises($user_id)
	{
		$sql_query = "SELECT * FROM `" . self::EXC_TABLE_USER_META_NAME . "` AS m RIGHT JOIN `" . self::RIGHT_TABLE_NAME . "` AS r ON m.lesson_excercise_id = r.object_id AND m.user_id = r.user_id WHERE r.user_id = '" . $user_id . "' AND r.location_id = '" . RightManager::LESSON_EXCERCISE_LOCATION_ID . "' AND m.order IS NULL";
		$result = self::retrieve_data($sql_query);
		foreach($result as $r)
		{
			$properties = new CustomProperties();
			$properties->add_property("lesson_excercise_id", $r->object_id);
			$properties->add_property("user_id", $user_id);
			$properties->add_property("visible", 0);
			$properties->add_property("first_visible", 0);
			$properties->add_property("new", 0);
			$properties->add_property("order", $this->get_new_excercise_order($user_id));
			self::insert(self::EXC_TABLE_USER_META_NAME, $properties);
			
			$map_rel = new CustomProperties();
			$map_rel->add_property("map_id", 0);
			$map_rel->add_property("object_id", $r->object_id);
			$map_rel->add_property("user_id", $this->manager->get_user()->get_id());
			$map_rel->add_property("location_id", RightManager::LESSON_EXCERCISE_LOCATION_ID);
			RightDataManager::instance(null)->insert_location_user_map_relation($map_rel);
		}
	}
	
	public function retrieve_lesson_excercise($id)
	{
		return parent::retrieve_by_id(self::EXC_TABLE_NAME, self::EXC_CLASS_NAME, $id);
	}
	
	public function retrieve_lesson_excercise_by_id($id, $user_id)
	{
		$join = array();
		$join[] = new Join(self::EXC_TABLE_NAME, 'e', 'id', Join::MAIN_TABLE);
		$join[] = new Join(self::EXC_TABLE_USER_META_NAME, 'u', 'lesson_excercise_id', 'LEFT JOIN', Join::MAIN_TABLE, 'id');
		$condition = "e.id = '" . $id . "' AND u.user_id = " . $user_id;
		return parent::retrieve($join, self::EXC_CLASS_NAME, '', self::ONE_RECORD, $condition);
	}
	
	public function retrieve_lesson_excercise_relations_by_lesson_excercise_id($lesson_excercise_id = "", $user_id = "")
	{
		$condition = "lesson_excercise_id = '" . $lesson_excercise_id . "' AND user_id = " . $user_id;
		$rels = parent::retrieve(self::REL_EXC_TABLE_NAME,null,'',self::MANY_RECORDS,$condition);
		$arr = array();
		foreach ($rels as $rel)
			$arr[] = $rel->pupil_id;
		return $arr;
	}
	
	public function insert_lesson_excercise($lesson_excercise)
	{
		$id = parent::insert(self::EXC_TABLE_NAME, $lesson_excercise);
		if($id)
		{
			$lesson_excercise->set_id($id);
			$this->insert_lesson_excercise_relations($lesson_excercise);
		
			$properties = new CustomProperties();
			$properties->add_property("lesson_excercise_id", $id);
			foreach($lesson_excercise->get_theme_ids() as $theme_id)
			{
				$properties->add_property("theme_id", $theme_id);
				self::insert(self::LES_EXC_THEME_TABLE_NAME, $properties);
			}
			
			$properties = new CustomProperties();
			$properties->add_property("lesson_excercise_id", $id);
			$properties->add_property("user_id", $this->manager->get_user()->get_id());
			//$properties->add_property("lesson_id", $lesson_excercise->get_lesson_id());
			$properties->add_property("visible", $lesson_excercise->get_visible());
			$properties->add_property("new", $lesson_excercise->get_new());
			$properties->add_property("order", $lesson_excercise->get_order());
			if($lesson_excercise->get_visible())
				$properties->add_property("first_visible", time());
			else
				$properties->add_property("first_visible", 0);
			self::insert(self::EXC_TABLE_USER_META_NAME, $properties);
			
			RightManager::instance()->add_location_object_user_right(RightManager::LESSON_EXCERCISE_LOCATION_ID, $this->manager->get_user()->get_id(), $id, RightManager::UPDATE_RIGHT);
			$this->update_lesson_excercise_criteria($lesson_excercise);
			
			//if($lesson_excercise->get_visible())
			//	Mail::send_available_email($lesson_excercise);
		}
		if(Error::get_instance()->get_result())
			return $id;
		else
			return 0;
	}
	
	public function update_lesson_excercise($lesson_excercise, $update_relations = true)
	{
		$success = self::update_by_id(self::EXC_TABLE_NAME, $lesson_excercise);
		if($success && $update_relations)
		{
			$this->update_lesson_excercise_relations($lesson_excercise);
		}
		if($success)
		{
			parent::delete(self::LES_EXC_THEME_TABLE_NAME, "lesson_excercise_id = " . $lesson_excercise->get_id());
			
			$properties = new CustomProperties();
			$properties->add_property("lesson_excercise_id", $lesson_excercise->get_id());
			foreach($lesson_excercise->get_theme_ids() as $theme_id)
			{
				$properties->add_property("theme_id", $theme_id);
				self::insert(self::LES_EXC_THEME_TABLE_NAME, $properties);
			}
			
			$first_visible = self::retrieve_lesson_excercise_first_visible($lesson_excercise->get_id(), $lesson_excercise->get_user_id());
			if($first_visible==null)
				$first_visible = 0;
			if($first_visible == 0 && $lesson_excercise->get_visible())
				$lesson_excercise->set_first_visible(time());
			else
				$lesson_excercise->set_first_visible($first_visible);
			$success &= $this->update_lesson_excercise_visible_and_order($lesson_excercise);
			$success &= $this->update_lesson_excercise_criteria($lesson_excercise);
			/*
			if($success && $lesson_excercise->get_visible() && $first_visible == 0)
			{
				Mail::send_available_email($lesson_excercise);
			}
			*/
		}
		return $success;
	}
	
	public function update_lesson_excercise_relations($lesson_excercise)
	{
		$conditions = "lesson_excercise_id = '". $lesson_excercise->get_id()."'"; 
		parent :: delete(self :: REL_EXC_TABLE_NAME, $conditions);
		
		$this->insert_lesson_excercise_relations($lesson_excercise);
	}
	
	public function insert_lesson_excercise_relations($lesson_excercise)
	{
		$size = count($lesson_excercise->get_user_ids());
		if($size)
		{
			$query = 'INSERT INTO `' . self::REL_EXC_TABLE_NAME . '` ( lesson_excercise_id, user_id, pupil_id ) VALUES';
			$i = 1;
			$id = $lesson_excercise->get_id();
			$parent_id = $this->manager->get_user()->get_id();
			foreach($lesson_excercise->get_user_ids() as $user_id)
			{
				$query .= "('".$id."', '".$parent_id."', '".$user_id."')";
				if($i<$size)
				{
					$query .= ", ";
				}
				$i++;
			}
			
			$query .= ';';
			self::$_connection->execute_sql($query,'INSERT');
		}
	}
	
	public function update_lesson_excercise_visible_and_order($lesson_excercise)
	{
		$properties = new CustomProperties();
		$properties->add_property("visible", $lesson_excercise->get_visible());
		$properties->add_property("new", $lesson_excercise->get_new());
		$properties->add_property("order", $lesson_excercise->get_order());
		//$properties->add_property("lesson_id", $lesson_excercise->get_lesson_id());
		$properties->add_property("first_visible", $lesson_excercise->get_first_visible());
		self::update(self::EXC_TABLE_USER_META_NAME, $properties, "lesson_excercise_id = '" . $lesson_excercise->get_id() . "' AND user_id = '" . $this->manager->get_user()->get_id() . "'");
		return Error::get_instance()->get_result();
	}

	public function update_lesson_excercise_criteria($lesson_excercise)
	{
		$condition = "lesson_excercise_id = '" . $lesson_excercise->get_id() . "' AND user_id = '" . $lesson_excercise->get_user_id() . "'";
		
		$properties = new CustomProperties();
		$properties->add_property("criteria_lesson_id", $lesson_excercise->get_criteria_lesson_id());
		$properties->add_property("criteria_lesson_percentage", $lesson_excercise->get_criteria_lesson_percentage());
		$properties->add_property("criteria_lesson_excercise_percentage", $lesson_excercise->get_criteria_lesson_excercise_percentage());
		self::update(self::EXC_TABLE_USER_META_NAME, $properties, $condition);
		
		self::delete(self::TABLE_META_EXC_NAME, $condition);
		foreach($lesson_excercise->get_criteria_lesson_excercise_ids() as $id)
		{
			$properties = new CustomProperties();
			$properties->add_property("lesson_excercise_id", $lesson_excercise->get_id());
			$properties->add_property("user_id", $lesson_excercise->get_user_id());
			$properties->add_property("criteria_lesson_excercise_id", $id);
			self::insert(self::TABLE_META_EXC_NAME, $properties);
		}
		return Error::get_instance()->get_result();
	}
	
	public function delete_lesson_excercise($id)
	{
		return self :: delete(self :: EXC_TABLE_USER_META_NAME, "lesson_excercise_id = '" . $id . "'") && RightManager::instance()->delete_location_object_user_right(RightManager::LESSON_EXCERCISE_LOCATION_ID, $this->manager->get_user(), $id);
	}
	
	public function rearrange_lesson_excercise_order($maps)
	{
		$join = array();
		$join[] = new Join(self::EXC_TABLE_USER_META_NAME, "m", "lesson_excercise_id", Join::MAIN_TABLE);
		$join[] = new Join(RightDataManager::LOC_OBJ_USER_TABLE_NAME, "urt", "object_id", "LEFT JOIN", Join::MAIN_TABLE, "lesson_excercise_id");
		$join[] = new Join(RightDataManager::LOC_USER_MAP_REL_TABLE_NAME, "um", "object_id", "LEFT JOIN", Join::MAIN_TABLE, "lesson_excercise_id");
		$order = "`order`";
		$condition = "m.user_id = " . $this->manager->get_user()->get_id();
		$condition .= " AND urt.location_id = " . RightManager::LESSON_EXCERCISE_LOCATION_ID . " AND urt.user_id = " . $this->manager->get_user()->get_id();
		$conditions = $condition . " AND um.map_id = 0 AND um.user_id = " . $this->manager->get_user()->get_id() . " AND um.location_id = " . RightManager::LESSON_EXCERCISE_LOCATION_ID;
		$results = parent::retrieve($join,null,$order,self::MANY_RECORDS,$conditions);
		$n_order = 1;
		foreach($results as $result)
		{
			$properties = new CustomProperties();
			$properties->add_property("order", $n_order);
			self::update(self::EXC_TABLE_USER_META_NAME, $properties, "lesson_excercise_id = " . $result->lesson_excercise_id . " AND user_id = " . $this->manager->get_user()->get_id());
			$n_order++;
		}
		foreach($maps as $map)
		{
			$conditions = $condition . " AND um.map_id = " . $map->get_id();
			$results = parent::retrieve($join,null,$order,self::MANY_RECORDS,$conditions);
			$n_order = 1;
			foreach($results as $result)
			{
				$properties = new CustomProperties();
				$properties->add_property("order", $n_order);
				self::update(self::EXC_TABLE_USER_META_NAME, $properties, "lesson_excercise_id = " . $result->lesson_excercise_id . " AND user_id = " . $this->manager->get_user()->get_id());
				$n_order++;
			}
		}
	}
	
	public function count_lesson_excercises_by_user_id($user_id, $map_id = null)
	{	   	
		$query = "SELECT count(`order`) as count FROM `".self::EXC_TABLE_USER_META_NAME."` as m";
		$conditions = " WHERE m.user_id = '" . $user_id . "'";
		if(is_numeric($map_id))
		{
			$query .= " LEFT JOIN `" . RightDataManager::LOC_USER_MAP_REL_TABLE_NAME . "` as r ON r.object_id = m.lesson_excercise_id";
			$conditions .= " AND map_id = " . $map_id;
		}
		elseif($map_id == "others")
		{
			$query .= " LEFT JOIN `" . RightDataManager::LOC_USER_MAP_REL_TABLE_NAME . "` as r ON r.object_id = m.lesson_excercise_id";
			$conditions .= " AND r.map_id = 0 AND r.user_id = " . $user_id . " AND r.location_id = " . RightManager::LESSON_EXCERCISE_LOCATION_ID;
		}
		$resultData = $this->retrieve_data($query . $conditions);
		return $resultData[0]->count;
	}
	
	public function delete_other_lesson_excercises($user_id, $filter, $map_id = null)
	{
		$size = count($filter);
		$condition = "";
		$result = false;
		if($size)
		{
			$i = 1;
			foreach($filter as $id)
			{
				$condition .= "'" . $id . "'";
				if($i<$size)
				{
					$condition .= ", ";
				}
				$i++;
			}
			$condition .= ")";
		}

		$join = array();
		$join[] = new Join(self::EXC_TABLE_USER_META_NAME, "m", "lesson_excercise_id", Join::MAIN_TABLE);
		$j_condition = "m.user_id = " . $this->manager->get_user()->get_id();
		if(!is_null($map_id))
		{
			$join[] = new Join(RightDataManager::LOC_USER_MAP_REL_TABLE_NAME, "um", "object_id", "LEFT JOIN", Join::MAIN_TABLE, "lesson_excercise_id");
			if($map_id == "others")
				$j_condition .= " AND um.map_id = 0 AND um.user_id = " . $this->manager->get_user()->get_id() . " AND um.location_id = " . RightManager::LESSON_EXCERCISE_LOCATION_ID;
			else
				$j_condition .= " AND um.map_id = " . $map_id;
		}
		$results = parent::retrieve($join,null,'',self::MANY_RECORDS,$j_condition . ($condition!=""?" AND `lesson_excercise_id` NOT IN (" . $condition:""));
		
		$size = count($results);
		$condition = "";
		if($size)
		{
			$i = 1;
			foreach($results as $result)
			{
				$condition .= "'" . $result->lesson_excercise_id . "'";
				if($i<$size)
					$condition .= ",";
				$i++;
			}
			$condition .= ")";
		}
		
		$r_condition =  "user_id = '" . $this->manager->get_user()->get_id() . "' AND location_id = '" . RightManager::LESSON_EXCERCISE_LOCATION_ID . "'" . ($condition!=""?" AND `object_id` IN (" . $condition:"");
		$success = parent::delete(self::RIGHT_TABLE_NAME, $r_condition);
		if(is_numeric($map_id))
			$success &= parent::delete(RightDataManager::LOC_USER_MAP_REL_TABLE_NAME, "map_id = '" . $map_id . "'" . ($condition!=""?" AND `object_id` IN (" . $condition:""));
		$condition =  "user_id = '" . $this->manager->get_user()->get_id() . "'" . ($condition!=""?" AND `lesson_excercise_id` IN (" . $condition:"");
		$success &= parent::delete(self::REL_EXC_TABLE_NAME, $condition);
		$success &= parent::delete(self::EXC_TABLE_USER_META_NAME, $condition);
		//dump($query);
		return $success;
	}

	public function retrieve_lesson_excercise_themes_by_lesson_excercise_id($lesson_excercise_id)
	{
		$theme_ids = array();
		$themes = parent::retrieve(self::LES_EXC_THEME_TABLE_NAME, null, '', self::MANY_RECORDS, "lesson_excercise_id = " . $lesson_excercise_id);
		foreach($themes as $theme)
			$theme_ids[] = $theme->theme_id;
		return $theme_ids;
	}
	
	public function retrieve_lesson_excercise_from_post($check_criteria_only = false)
	{
		$arr = array();
		$arr['id'] = Request::post("id");
		$arr['title'] = addslashes(htmlspecialchars(Request::post("title")));
		$arr['description'] = addslashes(htmlspecialchars(Request::post("description")));
		$arr['rating'] = Request::post("rating");
		$arr['user_id'] = $this->manager->get_user()->get_id();
		if(is_null(Request::post("order")))
			$arr['order'] = self :: get_new_excercise_order($arr['user_id']);
		else
			$arr['order'] = Request::post("order");
		$arr['visible'] = self :: parse_checkbox_value(Request::post("visible"));
		if($arr['visible']==0 && self :: parse_checkbox_value(Request::post("criteria_visible")))
		{
			$criteria_lesson_id = Request::post("criteria_lesson_id");
			$criteria_lesson_percentage = Request::post("criteria_lesson_percentage");
			
			$criteria_lesson_excercise_ids = Request::post("criteria_lesson_excercise_ids");
			$criteria_lesson_excercise_percentage = Request::post("criteria_lesson_excercise_percentage");
			
			$arr['criteria_lesson_id'] = $criteria_lesson_id;
			$arr['criteria_lesson_percentage'] = $criteria_lesson_percentage;
					
			$arr['criteria_lesson_excercise_ids'] = is_null($criteria_lesson_excercise_ids)?array():$criteria_lesson_excercise_ids;
			$arr['criteria_lesson_excercise_percentage'] = $criteria_lesson_excercise_percentage;
			
			$no_selection = $criteria_lesson_id == 0;
			$no_selection_exc = empty($criteria_lesson_excercise_ids);
			if(!$no_selection || !$no_selection_exc)
			{
				$incorrect_number = is_null($criteria_lesson_percentage) || !is_numeric($criteria_lesson_percentage) || $criteria_lesson_percentage < 1 || $criteria_lesson_percentage > 100;
				if(!$no_selection && $incorrect_number)
				{
					Error::get_instance()->set_result(false);
					Error::get_instance()->append_message(Language::get_instance()->translate(940));
				}
				elseif($no_selection)
					$arr['criteria_lesson_percentage'] = 0;
				
				$incorrect_number = is_null($criteria_lesson_excercise_percentage) || !is_numeric($criteria_lesson_excercise_percentage) || $criteria_lesson_excercise_percentage < 1 || $criteria_lesson_excercise_percentage > 100;
				if(!$no_selection_exc && $incorrect_number && Error::get_instance()->get_result())
				{
					Error::get_instance()->set_result(false);
					Error::get_instance()->append_message(Language::get_instance()->translate(940));
				}
				elseif($no_selection_exc)
					$arr['criteria_lesson_excercise_percentage'] = 0;
			}
			else
			{
				Error::get_instance()->set_result(false);
				Error::get_instance()->append_message(Language::get_instance()->translate(941));
			}
		}
		else
		{
			$arr['criteria_lesson_id'] = 0;
			$arr['criteria_lesson_percentage'] = 0;
					
			$arr['criteria_lesson_excercise_ids'] = array();
			$arr['criteria_lesson_excercise_percentage'] = 0;
		}
		$arr['new'] = self :: parse_checkbox_value(Request::post("new"));
		if(self::parse_checkbox_value(Request::post("add_pupils")))
			$arr['user_ids'] = Request::post("user_ids");
		else
			$arr['user_ids'] = array();
		
		$arr['theme_ids'] = Request::post("theme_id");
		if(is_null($arr['theme_ids']))
			$arr['theme_ids'] = array();
		
		if(!$check_criteria_only)
		{
			if(is_null($arr['title']) || $arr['title'] == "")
			{
				Error::get_instance()->set_result(false);
				Error::get_instance()->append_message(Language::get_instance()->translate(119));
			}
			/*
			elseif($arr['id'] == 0 && !is_null($this->manager->get_data_manager()->retrieve_lesson_excercise_by_title($arr['title'])))
			{
				Error::get_instance()->set_result(false);
				Error::get_instance()->append_message(Language::get_instance()->translate(976));
			}
			*/
			if(is_null($arr['description']) || $arr['description'] == "")
			{
				Error::get_instance()->set_result(false);
				Error::get_instance()->append_message(Language::get_instance()->translate(121));
			}
			if(is_null($arr['rating']) || !is_numeric($arr['rating']) || $arr['rating'] <= 0)
			{
				Error::get_instance()->set_result(false);
				Error::get_instance()->append_message(Language::get_instance()->translate(550));
			}
			if(empty($arr['theme_ids']))
			{
				Error::get_instance()->set_result(false);
				Error::get_instance()->append_message(Language::get_instance()->translate(305));
			}
		}
		
		//dump(new LessonExcercise($arr));
		//exit;
		return new LessonExcercise($arr);
		/*

		$arr = array();
		$arr['id'] = Request :: post("id");
		$arr['user_id'] = $this->manager->get_user()->get_id();
		if(self :: parse_checkbox_value(Request :: post("add_lesson")))
		{
			$arr['lesson_id'] = Request :: post("lesson_id");
		}
		else
		{
			$arr['lesson_id'] = null;
		}
		
		if(self :: parse_checkbox_value(Request :: post("add_pupils")))
		{
			$arr['user_ids'] = Request :: post("user_id");
		}
		else
		{
			$arr['user_ids'] = array();
		}
		
		$arr['set_id'] = Request :: post("set_id");
		$arr['question_set_id'] = Request :: post("question_set_id");
		$arr['selection_set_id'] = Request :: post("selection_set_id");
		if(is_null(Request :: post('order')))
		{
			$arr['order'] = self :: get_new_excercise_order($arr['user_id']);
		}
		else
		{
			$arr['order'] = Request :: post('order');
		}
		$arr['visible'] = self :: parse_checkbox_value(Request :: post('visible'));
		$arr['new'] = self :: parse_checkbox_value(Request :: post('new'));
		if((is_null($arr['set_id']) || !is_numeric($arr['set_id']) || $arr['set_id'] == 0) &&
		   (is_null($arr['question_set_id']) || !is_numeric($arr['question_set_id']) || $arr['question_set_id'] == 0) &&
		   (is_null($arr['selection_set_id']) || !is_numeric($arr['selection_set_id']) || $arr['selection_set_id'] == 0))
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->set_message(Language::get_instance()->translate(125));
		}
		return new LessonExcercise($arr);
		*/
	}
	
	public function get_new_excercise_order($user_id)
	{
	   	$query = "SELECT max(`order`) as max FROM `".self::EXC_TABLE_USER_META_NAME."` as m LEFT JOIN `" . RightDataManager::LOC_USER_MAP_REL_TABLE_NAME . "` as r ON m.lesson_excercise_id = r.object_id AND m.user_id = r.user_id WHERE m.user_id = '" . $this->manager->get_user()->get_id() . "' AND r.location_id = " . RightManager::LESSON_EXCERCISE_LOCATION_ID;
		$resultData = $this->retrieve_data($query);
		return ++$resultData[0]->max;
	}
	
	public function retrieve_lesson_excercise_first_visible($lesson_id, $user_id)
	{
		$conditions = "lesson_excercise_id = '" . $lesson_id . "' AND user_id = '" . $user_id . "'";
		$obj = self::retrieve(self :: EXC_TABLE_USER_META_NAME, null, '', self :: ONE_RECORD, $conditions);
		if(!is_null($obj))
		{
			return $obj->first_visible;
		}
		else
		{
			return 0;
		}
	}

	public function retrieve_lesson_excercise_by_title($title)
	{
		$cond = "title = '" . $title . "'";
		$join = array();
		$join[] = new Join(self::EXC_TABLE_NAME, 'e', 'id', Join::MAIN_TABLE);
		$join[] = new Join(self::EXC_TABLE_USER_META_NAME, 'u', 'lesson_excercise_id', 'LEFT JOIN', Join::MAIN_TABLE, 'id');
		return parent::retrieve($join, self::EXC_CLASS_NAME, '', self::ONE_RECORD, $cond);
	}
	
	public function retrieve_new_lesson_excercises_by_user_id($user_id, $duration = null)
	{
		if(is_null($duration))
		{
			$duration = 3*7*24*60*60;
		}
		$query = "SELECT * FROM `" . self :: EXC_TABLE_NAME . "` AS exc
					LEFT JOIN `" . self::EXC_TABLE_USER_META_NAME . "` AS u ON exc.id = u.lesson_excercise_id 
					WHERE u.visible = 1 AND u.user_id = '" . $user_id . "' AND u.first_visible <= " . time() . " AND u.first_visible >= " . (time() - $duration);
		$objects = $this->retrieve_data($query);
		return $this->Mapping($objects, self :: EXC_CLASS_NAME);
	}

	public function retrieve_lesson_excercises_with_search_form($right, $limit = "", $shop = false, $filter = false, $continuation = false, $undo = false)
	{
		$conditions = "";
		if(Request :: post('keywords') != "")
		{
			if($conditions != "")
				$conditions .= " AND ";
			$words = preg_split("/ /", Request :: post('keywords'));
			$end = count($words);
			$i = 1;
			if($end)
			{
				$conditions .= "(";
				foreach ($words as $word)
				{
					$conditions .= 'description LIKE  \'%' . $word . '%\' OR title LIKE \'%' . $word . '%\'';
					if($i != $end)
						$conditions .= ' OR ';
					$i++;
				}
				$conditions .= ")";
			}
		}
		$theme_ids = Request :: post('theme_id');
		if(!is_null($theme_ids) && !empty($theme_ids))
		{
			if($conditions != "")
			{
				$conditions .= " AND ";
			}
			foreach($theme_ids as $index => $id)
			{
				if(!is_numeric($id))
				{
					unset($theme_ids[$index]);
				}
			}
			$size = count($theme_ids);
			$i = 1;
			
			$conditions .= "theme_id IN (";
			foreach($theme_ids as $id)
			{
				$conditions .= "'".$id."'";
				if($i < $size)
					$conditions .= ", ";
				$i++;
			}
			$conditions .= ")";
		}
		if(Request :: post('difficulty_id') != 0 && is_numeric(Request::post('difficulty_id')))
		{
			require_once Path :: get_path() . "pages/puzzle/difficulty/lib/difficulty_data_manager.class.php";
			$difficulty = DifficultyDataManager::instance(null)->retrieve_difficulty(Request :: post('difficulty_id'));
			$min_rating = $difficulty->get_bottom_rating();
			$max_rating = $difficulty->get_top_rating();
			if($conditions != "")
			{
				$conditions .= " AND ";
			}
			$conditions .= (!is_null($min_rating)?"rating >= '" . $min_rating ."'":"") . (!is_null($min_rating)&&!is_null($max_rating)?" AND ":"") . (!is_null($max_rating)?"rating <= '" .$max_rating ."'":"");
		}
		$sqlString = "SELECT DISTINCT(v.id) as di, v.* FROM `".self::EXC_TABLE_NAME."` as v" .
						" LEFT JOIN `".self::LES_EXC_THEME_TABLE_NAME."` as t ON v.id = t.lesson_excercise_id";
		if(!$this->manager->get_user()->is_admin() && $right != RightManager::NO_RIGHT)
		{
			$sqlString .= " LEFT JOIN `" . RightDataManager::LOC_OBJ_USER_TABLE_NAME . "` as urt ON urt.object_id = v.id";
			if($conditions != "")
			{
				$conditions .= " AND ";
			}
			$conditions .= "location_id = " . RightManager::LESSON_EXCERCISE_LOCATION_ID . " AND urt.user_id = " . $this->manager->get_user()->get_id() . " AND urt." . RightDataManager::get_right_conditions($right);
		}
		if($right == RightManager::NO_RIGHT && ($shop || $filter))
		{
			if($shop)
			{
				$sqlString .= " LEFT JOIN `shop_meta_data` as sm ON sm.object_id = id AND sm.location_id = " . RightManager::LESSON_EXCERCISE_LOCATION_ID;
				if(!is_null(Request :: post('user')) != "" && Request::post('user') != "")
				{
					$words = preg_split("/ /", Request :: post('user'));
					$count = count($words);
					$i = 1;
					if($count)
					{
						$sqlString .= " LEFT JOIN `" . RightDataManager::LOC_OBJ_CRT_TABLE_NAME . "` as ct on v.id = ct.object_id AND ct.location_id = " . RightManager::LESSON_EXCERCISE_LOCATION_ID;
						$sqlString .= " LEFT JOIN `" . UserDataManager::TABLE_NAME . "` as ut on ct.creator_id = ut.id";
						if($conditions != "")
							$conditions .= " AND ";
						$conditions .= "(";
						foreach ($words as $word)
						{
							$conditions .= "username LIKE '%" . addslashes(Request :: post('user')) ."%' OR firstname LIKE '%" . addslashes(Request :: post('user')) ."%' OR lastname LIKE '%" . Request :: post('user') ."%'";
							if($i != $count)
								$conditions .= " OR ";
							$i++;
						}
						$conditions .= ")";
					}
				}
			}
			if($conditions != "")
			{
				$conditions .= " AND ";
			}
			$conditions .= (!$filter?"v.id NOT IN (SELECT object_id FROM `" . RightDataManager::LOC_OBJ_USER_TABLE_NAME . "` WHERE location_id = " . RightManager::LESSON_EXCERCISE_LOCATION_ID . " AND user_id = " . $this->manager->get_user()->get_id() . ") " . ($shop?"AND":""):"") . ($shop?" sm.valid" . ($filter?(!$undo?" IS NULL":(is_null(Request::post("shop_valid")) || !is_numeric(Request::post("shop_valid"))?" IN (0, 1)":" = " . Request::post("shop_valid"))):" = 1"):"");
		}
		if($shop)
		{
			if($conditions != "")
			{
				$conditions .= " AND ";
			}
			$conditions .= "v.id NOT IN (SELECT object_id FROM `shop` WHERE location_id = " . RightManager::LESSON_EXCERCISE_LOCATION_ID . " AND user_id = " . $this->manager->get_user()->get_id() . ")";
		}
		if($continuation)
		{
			if($conditions != "")
			{
				$conditions .= " AND ";
			}
			$conditions .= "v.id NOT IN (SELECT object_id FROM `lesson_continuation_available_objects` WHERE type_id = " . LessonContinuationAvailability::TYPE_LESSON_EXCERCISE . ") AND 1 = (SELECT count(*) FROM `" . self::LES_EXC_THEME_TABLE_NAME . "` WHERE lesson_excercise_id = id)";
		}
		if($conditions != "")
			$sqlString .= " WHERE " . $conditions;
	
		if($limit!="")
			$sqlString .= " LIMIT " . $limit;

		$objects = $this->retrieve_data($sqlString);
		$results = $this->Mapping($objects,self::EXC_CLASS_NAME);
		return $results;
	}
	/*
	public function retrieve_lesson_excercises_with_search_form($right, $limit = "", $shop = false, $filter = false)
	{
		require_once Path::get_path() . "/pages/puzzle/set/lib/set_data_manager.class.php";
		require_once Path::get_path() . "/pages/question/question_set/lib/question_set_data_manager.class.php";
		$theme_ids = Request :: post('theme_id');
		$all_sets = (is_null($theme_ids) || empty($theme_ids)) && Request :: post('difficulty_id') == 0;
		$all_questions = Request :: post('question_set_keywords') == "";
		$sets = !$all_questions && $all_sets?array():SetDataManager::instance($this->manager)->retrieve_sets_with_search_form($right);
		$questions = $all_questions && !$all_sets?array():QuestionSetDataManager::instance($this->manager)->retrieve_question_sets_with_search_form($right);
	
		$size = count($sets);
		$conditions = "";
		if($size)
		{
			$conditions = "set_id IN (";
			$i = 1;
			foreach($sets as $set)
			{
				$conditions .= "'" . $set->get_id() . "'";
				if($i<$size)
				{
					$conditions .= ", ";
				}
				$i++;
			}
			$conditions .= ")";
		}
		
		$size = count($questions);
		if($size)
		{
			if($conditions != "")
				$conditions .= " OR ";
			$conditions .= "question_set_id IN (";
			$i = 1;
			foreach($questions as $question)
			{
				$conditions .= "'" . $question->get_id() . "'";
				if($i<$size)
				{
					$conditions .= ", ";
				}
				$i++;
			}
			$conditions .= ")";
		}
		
		if($conditions == "")
			return array();
		else
			$conditions = "(" . $conditions .  ")";
			
		$sqlString = "SELECT DISTINCT(id) as di, v.* FROM `".self::EXC_TABLE_NAME."` as v";
		if(!$this->manager->get_user()->is_admin() && $right != RightManager::NO_RIGHT)
		{
			$sqlString .= " LEFT JOIN `" . RightDataManager::LOC_OBJ_USER_TABLE_NAME . "` as urt ON urt.object_id = v.id";
			if($conditions != "")
			{
				$conditions .= " AND ";
			}
			$conditions .= "location_id = " . RightManager::LESSON_EXCERCISE_LOCATION_ID . " AND urt.user_id = " . $this->manager->get_user()->get_id() . " AND urt." . RightDataManager::get_right_conditions($right);
		}
		if($right == RightManager::NO_RIGHT)
		{
			if($shop)
				$sqlString .= " LEFT JOIN `shop_meta_data` as sm ON sm.object_id = id AND sm.location_id = " . RightManager::LESSON_EXCERCISE_LOCATION_ID;
			if($conditions != "")
			{
				$conditions .= " AND ";
			}
			$conditions .= (!$filter?"id NOT IN (SELECT object_id FROM `" . RightDataManager::LOC_OBJ_USER_TABLE_NAME . "` WHERE location_id = " . RightManager::LESSON_EXCERCISE_LOCATION_ID . " AND user_id = " . $this->manager->get_user()->get_id() . ") " . ($shop?"AND":""):"") . ($shop?" sm.valid" . ($filter?" IS NULL":" = 1"):"");
		}
		if($shop)
		{
			if($conditions != "")
			{
				$conditions .= " AND ";
			}
			$conditions .= "id NOT IN (SELECT object_id FROM `shop` WHERE location_id = " . RightManager::LESSON_EXCERCISE_LOCATION_ID . " AND user_id = " . $this->manager->get_user()->get_id() . ")";
		}
		if($conditions != "")
			$sqlString .= " WHERE " . $conditions;
	
		if($limit!="")
			$sqlString .= " LIMIT " . $limit;

		$objects = $this->retrieve_data($sqlString);
		$results = $this->Mapping($objects,self::EXC_CLASS_NAME);
		return $results;
	}
	*/
	public function retrieve_lesson_excercise_meta_data_criteria_excercise_ids($lesson_excercise_id, $user_id)
	{
		$condition = "lesson_excercise_id = " . $lesson_excercise_id ." AND user_id = " . $user_id;
		$results = parent::retrieve(self::TABLE_META_EXC_NAME, null, '', self::MANY_RECORDS, $condition);
		
		$ids = array();
		foreach($results as $r)
			$ids[] = $r->criteria_lesson_excercise_id;
		return $ids;
	}
	
	public function retrieve_excercise_attempt($excercise_id, $user_id)
	{
		$attempt = 1;
		if($this->manager->get_user()->get_group_id() != GroupManager::GROUP_GUEST_ID)
		{
			$condition = "set_id = " . $excercise_id . " AND user_id = " . $user_id;
			$result = parent::retrieve(self::PUZZLE_STATS_TABLE_NAME, null, '', self::ONE_RECORD, $condition, "1", "max(set_attempt) as set_attempt");
			
			if($result != null)
				$attempt = $result->set_attempt;
			
			$condition = "set_id = " . $excercise_id . " AND user_id = " . $user_id;
			$result = parent::retrieve(self::QUESTION_STATS_TABLE_NAME, null, '', self::ONE_RECORD, $condition, "1", "max(set_attempt) as set_attempt");
			
			if($result != null && $result->set_attempt > $attempt)
				$attempt = $result->set_attempt;
				 
			$condition = "set_id = " . $excercise_id . " AND user_id = " . $user_id;
			$result = parent::retrieve(self::SELECTION_STATS_TABLE_NAME, null, '', self::ONE_RECORD, $condition, "1", "max(set_attempt) as set_attempt");
			
			if($result != null && $result->set_attempt > $attempt)
				$attempt = $result->set_attempt;
		}
		else
		{
			$statistics = array();
			$statistics_puzzle = unserialize(Session::retrieve("statistics_puzzle"));
			if(!is_null($statistics_puzzle) && is_array($statistics_puzzle))
			{
				foreach ($statistics_puzzle as $statistic_puzzle)
				{
					if($statistic_puzzle["set_id"] == $excercise_id)
					{
						if($statistic_puzzle["set_attempt"]>$attempt)
							$attempt = $statistic_puzzle["set_attempt"];
					}
				}
			}
			
			$statistics = array();
			$statistics_question = unserialize(Session::retrieve("statistics_question"));
			if(!is_null($statistics_question) && is_array($statistics_question))
			{
				foreach ($statistics_question as $statistic_question)
				{
					if($statistic_question["set_id"] == $excercise_id)
					{
						if($statistic_question["set_attempt"]>$attempt)
							$attempt = $statistic_question["set_attempt"];
					}
				}
			}
			
			$statistics = array();
			$statistics_selection = unserialize(Session::retrieve("statistics_selection"));
			if(!is_null($statistics_selection) && is_array($statistics_selection))
			{
				foreach ($statistics_selection as $statistic_selection)
				{
					if($statistic_selection["set_id"] == $excercise_id)
					{
						if($statistic_selection["set_attempt"]>$attempt)
							$attempt = $statistic_selection["set_attempt"];
					}
				}
			}
		}
		
		return ++$attempt;
	}

	public function is_lesson_course_excercise($lesson_excercise_id)
	{
		$user = UserDataManager::instance(null)->retrieve_user(LessonDataManager::LESSON_COURSE_USER_ID);
		return RightManager::instance()->get_right_location_object("LessonExcercise", $user, $lesson_excercise_id) >= RightManager::READ_RIGHT;
	}
	
	/** LESSON_EXCERCISE_COMPONENT */

	public function retrieve_lesson_excercise_components_by_lesson_excercise_id($lesson_excercise_id)
	{
		$condition = "lesson_excercise_id = '" . $lesson_excercise_id ."'";
		$order = "`order`";
		return parent::retrieve(self::COMPONENT_TABLE_NAME, self::COMPONENT_CLASS_NAME, $order, self::MANY_RECORDS, $condition);
	}
	
	public function retrieve_lesson_excercise_component_by_lesson_excercise_id_and_order($lesson_excercise_id, $order)
	{
		$condition = "lesson_excercise_id = '" . $lesson_excercise_id ."' AND `order` = '" . $order . "'";
		return parent::retrieve(self::COMPONENT_TABLE_NAME, self::COMPONENT_CLASS_NAME, '', self::ONE_RECORD, $condition);
	}
	
	public function retrieve_lesson_excercise_component($lesson_excercise_component_id)
	{
		return parent::retrieve_by_id(self::COMPONENT_TABLE_NAME, self::COMPONENT_CLASS_NAME, $lesson_excercise_component_id);
	}
	
	public function insert_lesson_excercise_component($lesson_excercise_component)
	{
		return parent::insert(self::COMPONENT_TABLE_NAME, $lesson_excercise_component);
	}
	
	public function update_lesson_excercise_component($lesson_excercise_component)
	{
		return self::update_by_id(self::COMPONENT_TABLE_NAME, $lesson_excercise_component);
	}
	
	public function update_lesson_excercise_component_order($lesson_excercise_component_id, $order)
	{
		$custom_properties = new CustomProperties();
		$custom_properties->add_property("order", $order);
		$conditions = "id = '" . $lesson_excercise_component_id . "'";
		return self::update(self::COMPONENT_TABLE_NAME, $custom_properties, $conditions);
	}
	
	public function delete_lesson_excercise_component($id)
	{
		return self::delete_by_id(self::COMPONENT_TABLE_NAME, $id);
	}
	
	public function count_lesson_excercise_components($lesson_excercise_id)
	{	   	
		$condition = "lesson_excercise_id = '" . $lesson_excercise_id . "'";
		return self::count(self::COMPONENT_TABLE_NAME, $condition);
	}
	
	public function retrieve_lesson_excercise_components_from_post()
	{
		$arr = array();
		$arr['id'] = null; //Request::get("id");
		$arr['lesson_excercise_id'] = Request::get("lesson_excercise_id");
		$arr['type'] = Request::post("type_id");
		
		if(is_null(Request::post('order')))
			$arr['order'] = self :: get_new_component_order($arr['lesson_excercise_id']);
		else
			$arr['order'] = Request::post('order');
			
		$object_ids = Request::post('object_id');
		
		if(is_null($arr['type']) || $arr['type'] == 0)
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->append_message(Language::get_instance()->translate(122));
			return null;
		}
		elseif(is_null($object_ids) || !is_array($object_ids))
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->append_message(Language::get_instance()->translate(123));
			return null;
		}
		else
		{
			$components = array();
			foreach($object_ids as $object_id)
			{
				$arr['type_object_id'] = is_array($object_id)?$object_id[0]:$object_id;
				$components[] = new LessonExcerciseComponent($arr);
				$arr['order'] = $arr['order']+1;
			}
			return $components;
		}
		
	}
	
	public function get_new_component_order($lesson_excercise_id)
	{
	   	$query = "SELECT max(`order`) as max FROM `".self::COMPONENT_TABLE_NAME."` WHERE lesson_excercise_id = '" . $lesson_excercise_id . "'";
		$resultData = $this->retrieve_data($query);
		return ++$resultData[0]->max;
	}

	public function delete_other_lesson_excercise_components($lesson_excercise_id, $filter)
	{
		$size = count($filter);
		$condition = "lesson_excercise_id = '" . $lesson_excercise_id . "'";
		if($size)
		{
			$condition .= " AND `id` NOT IN (";
			$i = 1;
			foreach($filter as $id)
			{
				$condition .= "'" . $id . "'";
				if($i<$size)
					$condition .= ", ";
				$i++;
			}
			$condition .= ")";
		}	
		
		$result = self::delete(self::COMPONENT_TABLE_NAME, $condition);
		return $result;
	}
	
	public function retrieve_excercise_components_previous_mistakes($excercise_id)
	{
		$returnArr = array();
		$puzzle_random = rand(1, 5);
		$user_id = $this->manager->get_user()->get_id();
	
		$sqlString = "SELECT DISTINCT(puzzle_id) FROM `statistics_puzzle` WHERE user_id = '".$user_id."' AND score = '0' AND set_id != '" . $excercise_id . "' AND time >= '" . (time()-(24*60*60*60)) . "' ORDER BY RAND()";
		$resultData = self::$_connection->execute_sql($sqlString,'O');
		$size = count($resultData);
		$condition = "";
		if($size)
		{
			$condition = "`puzzle_id` IN (";
			$i = 1;
			foreach($resultData as $data)
			{
				$condition .= "'" . $data->puzzle_id . "'";
				if($i<$size)
					$condition .= ",";
				$i++;
			}
			$condition .= ")";
			$sqlString = "SELECT DISTINCT(puzzle_id) FROM `statistics_puzzle` WHERE user_id = '".$user_id."' AND score = '1' AND time >= '" . (time()-(24*60*60*60)) . "' AND " . $condition;
			$resultData2 = self::$_connection->execute_sql($sqlString,'N');
				
			$result_arr = array();
			foreach($resultData2 as $data)
			{
				$result_arr[] = $data[0];
			}
				
			$count = 0;
			foreach($resultData as $index => $data)
			{
				if(!in_array($data->puzzle_id, $result_arr) && !in_array($data->puzzle_id, $returnArr) && $count < $puzzle_random)
				{
					$component =  new LessonExcerciseComponent(null);
					$component->set_id($count);
					$component->set_type(1);
					$component->set_type_object_id($data->puzzle_id);
					$returnArr[] = $component;
					$count++;
				}
			}
		}
	
		$count = count($returnArr);
		if($count<4)
			$selection_random = rand(1, (5-$count));
		elseif($count==4)
		$selection_random = 1;
		else
			$selection_random = 0;
			
		if($selection_random != 0)
		{
			$sqlString = "SELECT DISTINCT(selection_id) FROM `statistics_selection` WHERE user_id = '".$user_id."' AND score = '0' AND set_id != '" . $excercise_id . "' AND time >= '" . (time()-(24*60*60*60)) . "' ORDER BY RAND()";
			$resultData = self::$_connection->execute_sql($sqlString,'O');
			$size = count($resultData);
			$condition = "";
			if($size)
			{
				$condition = "`selection_id` IN (";
				$i = 1;
				foreach($resultData as $data)
				{
					$condition .= "'" . $data->selection_id . "'";
					if($i<$size)
						$condition .= ",";
					$i++;
				}
				$condition .= ")";
				$sqlString = "SELECT DISTINCT(selection_id) FROM `statistics_selection` WHERE user_id = '".$user_id."' AND score = '1' AND time >= '" . (time()-(24*60*60*60)) . "' AND " . $condition;
				$resultData2 = self::$_connection->execute_sql($sqlString,'N');
				$count = 0;
					
				$result_arr = array();
				foreach($resultData2 as $data)
				{
					$result_arr[] = $data[0];
				}
	
				foreach($resultData as $index => $data)
				{
					if(!in_array($data->selection_id, $result_arr) && !in_array($data->selection_id, $returnArr) && $count < $selection_random)
					{
						$component =  new LessonExcerciseComponent(null);
						$component->set_id($count);
						$component->set_type(3);
						$component->set_type_object_id($data->selection_id);
						$returnArr[] = $component;
						$count++;
					}
				}
			}
		}
	
		$count = count($returnArr);
		if($count<4)
			$question_random = rand(1, (5-$count));
		elseif($count==4)
		$question_random = 1;
		else
			$question_random = 0;
			
		if($question_random != 0)
		{
			$sqlString = "SELECT DISTINCT(question_id) FROM `statistics_question` WHERE user_id = '".$user_id."' AND score = '0' AND set_id != '" . $excercise_id . "' AND time >= '" . (time()-(24*60*60*60)) . "' ORDER BY RAND()";
			$resultData = self::$_connection->execute_sql($sqlString,'O');
			$size = count($resultData);
			$condition = "";
			if($size)
			{
				$condition = "`question_id` IN (";
				$i = 1;
				foreach($resultData as $data)
				{
					$condition .= "'" . $data->question_id . "'";
					if($i<$size)
						$condition .= ",";
					$i++;
				}
				$condition .= ")";
				$sqlString = "SELECT DISTINCT(question_id) FROM `statistics_question` WHERE user_id = '".$user_id."' AND score = '1' AND time >= '" . (time()-(24*60*60*60)) . "' AND " . $condition;
				$resultData2 = self::$_connection->execute_sql($sqlString,'N');
	
				$result_arr = array();
				foreach($resultData2 as $data)
				{
					$result_arr[] = $data[0];
				}
	
				$count = 0;
				foreach($resultData as $index => $data)
				{
					if(!in_array($data->question_id, $result_arr) && !in_array($data->question_id, $returnArr) && $count < $question_random)
					{
						$component =  new LessonExcerciseComponent(null);
						$component->set_id($count);
						$component->set_type(2);
						$component->set_type_object_id($data->question_id);
						$returnArr[] = $component;
						$count++;
					}
				}
			}
		}
		return $returnArr;
	}
}

?>