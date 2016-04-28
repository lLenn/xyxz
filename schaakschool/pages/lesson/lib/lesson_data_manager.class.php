<?php

require_once Path :: get_path() . 'pages/lesson/lib/lesson.class.php';
require_once Path :: get_path() . 'pages/lesson/lib/lesson_page.class.php';
require_once Path :: get_path() . 'pages/lesson/lib/lesson_page_text.class.php';
require_once Path :: get_path() . 'pages/lesson/lib/lesson_continuation.class.php';
require_once Path :: get_path() . 'pages/message/lib/message_manager.class.php';

class LessonDataManager extends DataManager
{
	const TABLE_NAME = 'lesson';
	const CLASS_NAME = 'Lesson';
	const EXC_TABLE_NAME = 'lesson_excercise';
	const EXC_CLASS_NAME = 'LessonExcercise';
	const TABLE_META_NAME = 'lesson_meta_data';
	const TABLE_META_EXC_NAME = 'lesson_meta_data_criteria_excercise_ids';
	const REL_TABLE_NAME = 'lesson_relation';
	const PAGE_TABLE_NAME = 'lesson_page';
	const PAGE_CLASS_NAME = 'LessonPage';
	const PAGE_TEXT_TABLE_NAME = 'lesson_page_text';
	const PAGE_TEXT_CLASS_NAME = 'LessonPageText';
	const MET_EXC_TABLE_NAME = 'lesson_excercise_meta_data';
	const REL_EXC_TABLE_NAME = 'lesson_excercise_relation';
	const RIGHT_TABLE_NAME = 'rights_location_object_user_right';
	const EXC_TABLE_USER_META_NAME = 'lesson_excercise_user_data';
	const EXC_COMP_TABLE_NAME = 'lesson_excercise_component';
	const LES_THEME_TABLE_NAME = 'lesson_theme';
	const LESSON_COURSE_USER_ID = 451;
	//const SET_REL_TABLE_NAME = 'puzzle_set_relation';
	//const QST_SET_REL_TABLE_NAME = 'question_set_relation';
	
	public static function instance($manager)
	{
		parent::$_instance = new LessonDataManager($manager);
		return parent::$_instance;
	}

	public function insert_lesson($lesson)
	{
		$id = parent::insert(self::TABLE_NAME, $lesson);
		if($id)
		{
			RightManager::instance()->add_location_object_user_right("lesson", $this->manager->get_user()->get_id(), $id, RightManager::UPDATE_RIGHT);
			
			$lesson->set_id($id);
			$this->insert_lesson_relations($lesson);
			
			$properties = new CustomProperties();
			$properties->add_property("lesson_id", $id);
			foreach($lesson->get_theme_ids() as $theme_id)
			{
				$properties->add_property("theme_id", $theme_id);
				self::insert(self::LES_THEME_TABLE_NAME, $properties);
			}
			
			$properties = new CustomProperties();
			$properties->add_property("lesson_id", $id);
			$properties->add_property("user_id", $this->manager->get_user()->get_id());
			$properties->add_property("visible", $lesson->get_visible());
			$properties->add_property("new", $lesson->get_new());
			$properties->add_property("order", $lesson->get_order());
			if($lesson->get_visible())
				$properties->add_property("first_visible", time());
			else
				$properties->add_property("first_visible", 0);
			self::insert(self::TABLE_META_NAME, $properties);
			
			$lesson->set_id($id);
			$lesson->set_user_id($this->manager->get_user()->get_id());
			$this->update_lesson_criteria($lesson);
			//if($lesson->get_visible())
			//	Mail::send_available_email($lesson);
		}
		if(Error::get_instance()->get_result())
			return $id;
		else
			return 0;
	}

	public function update_lesson($lesson, $update_relations = true)
	{
		$success = self::update_by_id(self::TABLE_NAME, $lesson);
		$user_ids = $lesson->get_user_ids();
		if($success && $update_relations && !empty($user_ids))
		{
			$this->update_lesson_users($lesson);
		}
		elseif($success && $update_relations)
		{
			$conditions = "lesson_id = '". $lesson->get_id()."'"; 
			parent::delete(self::REL_TABLE_NAME, $conditions);
		}
		if($success)
		{
			parent::delete(self::LES_THEME_TABLE_NAME, "lesson_id = " . $lesson->get_id());
			
			$properties = new CustomProperties();
			$properties->add_property("lesson_id", $lesson->get_id());
			foreach($lesson->get_theme_ids() as $theme_id)
			{
				$properties->add_property("theme_id", $theme_id);
				self::insert(self::LES_THEME_TABLE_NAME, $properties);
			}
			
			$first_visible = self::retrieve_lesson_first_visible($lesson->get_id(), $lesson->get_user_id());
			if($first_visible == null)
				$first_visible = 0;
			if($first_visible == 0 && $lesson->get_visible())
				$lesson->set_first_visible(time());
			else
				$lesson->set_first_visible(0);
			$success &= $this->update_lesson_visible_and_order($lesson);
			$success &= $this->update_lesson_criteria($lesson);
			/*
			if($success && $lesson->get_visible() && $first_visible == 0)
			{
				Mail::send_available_email($lesson);
			}
			*/
		}
		return $success;
	}
	
	public function add_visible_and_order_new_lessons($maps)
	{
		$sql_query = "SELECT * FROM `" . self::TABLE_META_NAME . "` AS m RIGHT JOIN `" . self::RIGHT_TABLE_NAME . "` AS r ON m.lesson_id = r.object_id AND m.user_id = r.user_id WHERE r.user_id = '" . $this->manager->get_user()->get_id() . "' AND r.location_id = '" . RightManager::LESSON_LOCATION_ID . "' AND m.order IS NULL";
		$result = self::retrieve_data($sql_query);
		foreach($result as $r)
		{
			$properties = new CustomProperties();
			$properties->add_property("lesson_id", $r->object_id);
			$properties->add_property("user_id", $this->manager->get_user()->get_id());
			$properties->add_property("visible", 0);
			$properties->add_property("first_visible", 0);
			$properties->add_property("new", 0);
			$properties->add_property("order", $this->get_new_order());
			self::insert(self::TABLE_META_NAME, $properties);
			
			$map_rel = new CustomProperties();
			$map_rel->add_property("map_id", 0);
			$map_rel->add_property("object_id", $r->object_id);
			$map_rel->add_property("user_id", $this->manager->get_user()->get_id());
			$map_rel->add_property("location_id", RightManager::LESSON_LOCATION_ID);
			RightDataManager::instance(null)->insert_location_user_map_relation($map_rel);
		}
	}
	
	public function rearrange_lesson_order($maps)
	{
		$join = array();
		$join[] = new Join(self::TABLE_META_NAME, "m", "lesson_id", Join::MAIN_TABLE);
		$join[] = new Join(RightDataManager::LOC_OBJ_USER_TABLE_NAME, "urt", "object_id", "LEFT JOIN", Join::MAIN_TABLE, "lesson_id");
		$join[] = new Join(RightDataManager::LOC_USER_MAP_REL_TABLE_NAME, "um", "object_id", "LEFT JOIN", Join::MAIN_TABLE, "lesson_id");
		$order = "`order`";
		$condition = "m.user_id = " . $this->manager->get_user()->get_id();
		$condition .= " AND urt.location_id = " . RightManager::LESSON_LOCATION_ID . " AND urt.user_id = " . $this->manager->get_user()->get_id();
		$conditions = $condition . " AND um.map_id = 0 AND um.user_id = " . $this->manager->get_user()->get_id() . " AND um.location_id = " . RightManager::LESSON_LOCATION_ID;	
		$results = parent::retrieve($join,null,$order,self::MANY_RECORDS,$conditions);
		$n_order = 1;
		foreach($results as $result)
		{
			$properties = new CustomProperties();
			$properties->add_property("order", $n_order);
			self::update(self::TABLE_META_NAME, $properties, "lesson_id = " . $result->lesson_id . " AND user_id = " . $this->manager->get_user()->get_id());
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
				self::update(self::TABLE_META_NAME, $properties, "lesson_id = " . $result->lesson_id . " AND user_id = " . $this->manager->get_user()->get_id());
				$n_order++;
			}
		}
	}
	
	public function update_lesson_visible_and_order($lesson)
	{
		$properties = new CustomProperties();
		$properties->add_property("visible", $lesson->get_visible());
		$properties->add_property("order", $lesson->get_order());
		$properties->add_property("new", $lesson->get_new());
		if(!is_null($lesson->get_first_visible()) && $lesson->get_first_visible() != "")
			$properties->add_property("first_visible", $lesson->get_first_visible());
		self::update(self::TABLE_META_NAME, $properties, "lesson_id = '" . $lesson->get_id() . "' AND user_id = '" . $this->manager->get_user()->get_id() . "'");
		return Error::get_instance()->get_result();
	}
	
	public function update_lesson_criteria($lesson)
	{
		$condition = "lesson_id = '" . $lesson->get_id() . "' AND user_id = '" . $lesson->get_user_id() . "'";
		
		$properties = new CustomProperties();
		$properties->add_property("criteria_lesson_id", $lesson->get_criteria_lesson_id());
		$properties->add_property("criteria_lesson_percentage", $lesson->get_criteria_lesson_percentage());
		$properties->add_property("criteria_lesson_excercise_percentage", $lesson->get_criteria_lesson_excercise_percentage());
		self::update(self::TABLE_META_NAME, $properties, $condition);
		
		self::delete(self::TABLE_META_EXC_NAME, $condition);
		foreach($lesson->get_criteria_lesson_excercise_ids() as $id)
		{
			$properties = new CustomProperties();
			$properties->add_property("lesson_id", $lesson->get_id());
			$properties->add_property("user_id", $lesson->get_user_id());
			$properties->add_property("criteria_lesson_excercise_id", $id);
			self::insert(self::TABLE_META_EXC_NAME, $properties);
		}
		return Error::get_instance()->get_result();
	}
	
	public function update_lesson_users($lesson)
	{
		$user_id = $this->manager->get_user()->get_id();
		$conditions = "lesson_id = '". $lesson->get_id()."' AND user_id = '" . $user_id . "'"; 
		parent::delete(self::REL_TABLE_NAME, $conditions);
		
		$this->insert_lesson_relations($lesson);
	}
	
	public function insert_lesson_relations($lesson)
	{
		$user_ids = $lesson->get_user_ids();
		$size = count($user_ids);
		if($size != 0)
		{
			$user_id = $lesson->get_user_id();
			$query = 'INSERT INTO `'.self::REL_TABLE_NAME.'` (lesson_id, user_id, pupil_id) VALUES ';
			$i = 1;
			foreach($user_ids as $pupil_id)
			{
				$query .= "('".$lesson->get_id()."', '".$user_id."', '".$pupil_id."')";
				if($i<$size)
					$query .= ", ";
				$i++;
			}
				
			$query .= ';';
			self::$_connection->execute_sql($query,'INSERT');
		}
	}
	
	public function delete_other_lessons($filter, $map_id)
	{
		$size = count($filter);
		$condition = "";
		if($size)
		{
			$i = 1;
			foreach($filter as $id)
			{
				$condition .= "'" . $id . "'";
				if($i<$size)
					$condition .= ",";
				$i++;
			}
			$condition .= ")";
		}
		
		$join = array();
		$join[] = new Join(self::TABLE_META_NAME, "m", "lesson_id", Join::MAIN_TABLE);
		$j_condition = "m.user_id = " . $this->manager->get_user()->get_id();
		if(!is_null($map_id))
		{
			$join[] = new Join(RightDataManager::LOC_USER_MAP_REL_TABLE_NAME, "um", "object_id", "LEFT JOIN", Join::MAIN_TABLE, "lesson_id");
			if($map_id == "others")
				$j_condition .= " AND um.map_id = 0 AND um.user_id = " . $this->manager->get_user()->get_id() . " AND um.location_id = " . RightManager::LESSON_LOCATION_ID;
			else
				$j_condition .= " AND um.map_id = " . $map_id;
		}
		
		$results = parent::retrieve($join,null,'',self::MANY_RECORDS, $j_condition . ($condition!=""?" AND `lesson_id` NOT IN (" . $condition:""));
		$size = count($results);
		$condition = "";
		if($size)
		{
			$i = 1;
			foreach($results as $result)
			{
				$condition .= "'" . $result->lesson_id . "'";
				if($i<$size)
					$condition .= ",";
				$i++;
			}
			$condition .= ")";
		}
		
		$r_condition =  "user_id = '" . $this->manager->get_user()->get_id() . "' AND location_id = '" . RightManager::LESSON_LOCATION_ID . "'" . ($condition!=""?" AND `object_id` IN (" . $condition:"");
		$u_condition = "user_id = '" . $this->manager->get_user()->get_id() . "'" . ($condition!=""?" AND `lesson_id` IN (" . $condition:"");
		$success = parent::delete(self::RIGHT_TABLE_NAME, $r_condition);
		if(is_numeric($map_id))
			$success &= parent::delete(RightDataManager::LOC_USER_MAP_REL_TABLE_NAME, "map_id = '" . $map_id . "'" . ($condition!=""?" AND `object_id` IN (" . $condition:""));
		$condition =  "user_id = '" . $this->manager->get_user()->get_id() . "'" . ($condition!=""?" AND `lesson_id` IN (" . $condition:"");
		$success &= parent::delete(self::REL_TABLE_NAME, $condition);
		$success &= parent::delete(self::TABLE_META_NAME, $condition);
		$custom = new CustomProperties();
		$custom->add_property("lesson_id", 0);
		parent::update(self::EXC_TABLE_USER_META_NAME, $custom, $u_condition);
		return $success;
	}
	
	public function retrieve_lesson($id)
	{
		return parent::retrieve(self::TABLE_NAME,self::CLASS_NAME,'',self::ONE_RECORD,"id = '" . $id . "'");
	}
	
	public function retrieve_lesson_by_user_id($id, $user_id)
	{
		$join = array();
		$join[] = new Join(self::TABLE_NAME, "l", "id", Join::MAIN_TABLE);
		$join[] = new Join(self::TABLE_META_NAME, "m", "lesson_id", "LEFT JOIN", Join::MAIN_TABLE, "id");
		$condition = "l.id = " . $id . " AND m.user_id = " . $user_id;
		return parent::retrieve($join,self::CLASS_NAME,'',self::ONE_RECORD,$condition);
	}
	
	public function retrieve_lesson_by_title($title)
	{
		$cond = "title = '" . $title . "'";
		$join = array();
		$join[] = new Join(self::TABLE_NAME, 'l', 'id', Join::MAIN_TABLE);
		$join[] = new Join(self::TABLE_META_NAME, 'm', 'lesson_id', "LEFT JOIN", Join::MAIN_TABLE, 'id');
		return parent::retrieve($join,self::CLASS_NAME,'',self::ONE_RECORD,$cond);
	}

	public function retrieve_all_lessons()
	{
		return parent::retrieve(self::TABLE_NAME,self::CLASS_NAME,'',self::MANY_RECORDS);
	}
	
	public function retrieve_lessons($condition = "", $user_id = null, $right = RightManager::READ_RIGHT, $map = null, $count_criteria_excercise_ids = false, $having = "")
	{
		$join = array();
		$join[] = new Join(self::TABLE_NAME, "l", "id", Join::MAIN_TABLE);
		$join[] = new Join(self::TABLE_META_NAME, "m", "lesson_id", "LEFT JOIN", Join::MAIN_TABLE, "id");
		$order = "`order`";
		$condition .= ($condition==""?"":" AND ") . "m.user_id = " . (is_null($user_id)?$this->manager->get_user()->get_id():$user_id);
		$select = 'l.*, m.*';
		$group_by = '';
		if(!$this->manager->get_user()->is_admin())
		{
			$join[] = new Join(RightDataManager::LOC_OBJ_USER_TABLE_NAME, "urt", "object_id", "LEFT JOIN", Join::MAIN_TABLE, "id");
			$condition .= " AND urt.location_id = " . RightManager::LESSON_LOCATION_ID . " AND urt.user_id = " . (is_null($user_id)?$this->manager->get_user()->get_id():$user_id) . " AND urt." . RightDataManager::get_right_conditions($right);
		}
		if(!is_null($map))
		{
			$join[] = new Join(RightDataManager::LOC_USER_MAP_REL_TABLE_NAME, "um", "object_id", "LEFT JOIN", Join::MAIN_TABLE, "id");
			if($map == "others")
				$condition .= " AND um.map_id = 0 AND um.user_id = " . (is_null($user_id)?$this->manager->get_user()->get_id():$user_id) . " AND um.location_id = " . RightManager::LESSON_LOCATION_ID;
			else
				$condition .= " AND um.map_id = " . $map->get_id();
		}
		if($count_criteria_excercise_ids)
		{
			$extra_condition = " AND cmd.user_id = " . (is_null($user_id)?$this->manager->get_user()->get_id():$user_id);
			$join[] = new Join(self::TABLE_META_EXC_NAME, "cmd", "lesson_id", "Left JOIN", Join::MAIN_TABLE, "id", $extra_condition);
			$select .= ", cmd.user_id as cmd_uid, COUNT(cmd.criteria_lesson_excercise_id) as cmd_cnt";
			$group_by .= 'l.id';
		}
		return parent::retrieve($join,self::CLASS_NAME,$order,self::MANY_RECORDS, $condition, '', $select, false, $group_by, $having);
	}
	
	public function retrieve_lessons_by_conditions($conditions)
	{
		return parent::retrieve(self::TABLE_NAME,self::CLASS_NAME, '', self::MANY_RECORDS, $conditions);
	}
	
	public function retrieve_lesson_relations_by_lesson_id($lesson_id = "", $user_id = "")
	{
		$condition = "lesson_id = '" . $lesson_id . "' AND user_id = '" . $user_id . "'";
		$rels = parent::retrieve(self::REL_TABLE_NAME,null,'',self::MANY_RECORDS,$condition);
		$arr = array();
		foreach ($rels as $rel)
			$arr[] = $rel->pupil_id;
		return $arr;
	}

	public function retrieve_visible_and_new_lessons_by_user_id($user_id)
	{
		$condition = "m.visible = '1' AND m.new = '1'";
		return $this->retrieve_lessons($condition, $user_id);
	}
	
	public function retrieve_lessons_by_visibility($visible = true, $user_id = null, $right, $map)
	{
		$condition = "m.visible = '" . ($visible?"1":"0") . "'";
		return $this->retrieve_lessons($condition, $user_id, $right, $map);
	}
	
	public function retrieve_lessons_by_visibility_and_criteria($visible = true, $user_id = null, $right, $map)
	{
		$condition = "m.visible = '" . ($visible?"1":"0") . "'";
		$criteria = $visible?"(m.criteria_lesson_id != 0 OR cmd_cnt != 0)":"(m.criteria_lesson_id != 0 AND cmd_cnt != 0)";
		return $this->retrieve_lessons("", $user_id, $right, $map, true, "(" . $condition . " OR " . $criteria . ")");
	}
	
	public function filter_lessons(&$lesson_without_map, &$lessons_with_map, $user)
	{
		$lessons_with_map[] = $lesson_without_map;
		$all_lessons = array();
		foreach($lessons_with_map as $lessons)
		{
			foreach($lessons as $i => $lesson)
				$all_lessons[] = $lesson;
		}
		
		foreach($lessons_with_map as $m_index => $lessons)
		{
			foreach($lessons as $index => $lesson)
			{
				$users = $lesson->get_user_ids();
				if(!empty($users) && !in_array($user->get_id(), $users))
					unset($lessons[$index]);
				elseif($lesson->get_visible())
				{
					$this->check_child($lesson, $all_lessons, $user);
				}
			}
			$lessons_with_map[$m_index] = $lessons;
		}
		
		foreach($lessons_with_map as $m_index => $lessons)
		{
			foreach($lessons as $index => $lesson)
			{
				if(!$lesson->get_visible() && !$lesson->get_criteria_lesson_percentage() && $lesson->get_criteria_lesson_excercise_percentage())
				{
					$this->check_criteria($lesson, $user);
					if(!$lesson->get_teaser())
						unset($lessons[$index]);
				}
				elseif(!$lesson->get_visible() && !$lesson->get_teaser())
					unset($lessons[$index]);
			}
			$lessons_with_map[$m_index] = $lessons;
		}

		$lesson_without_map = $lessons_with_map[count($lessons_with_map)-1];
		unset($lessons_with_map[count($lessons_with_map)-1]);
	}

	public function check_child(&$lesson, &$all_lessons, $user)
	{
		foreach($all_lessons as $l_lesson)
		{
			$users = $l_lesson->get_user_ids();
			if((empty($users) || in_array($user->get_id(), $users)) && $l_lesson->get_criteria_lesson_id() == $lesson->get_id())
			{
				$this->check_criteria($l_lesson, $user, $lesson->get_visible());
				if($l_lesson->get_visible())
				{
					$this->check_child($l_lesson, $all_lessons, $user);
				}
			}
		}
	}
	public function count_lesson_excercise_components($excercise_id)
	{
		return self::count(self::EXC_COMP_TABLE_NAME, "lesson_excercise_id = " . $excercise_id);
	}
	
	public function check_criteria(&$lesson, $user, $parent_visible = false, $check_user = true, $check_visible = true)
	{
		//dump($lesson);
		$teaser = false;
		$visible = false;
		if($lesson->get_criteria_lesson_percentage())
		{
			if($parent_visible)
			{
				$pages = $this->count_lesson_pages($lesson->get_criteria_lesson_id());
				//CHECK EXECUTION TIME!!!!
				$viewed = StatisticsDataManager::instance($this->manager)->count_statistics_actions_lesson_views($this->manager->get_user()->get_id(), $lesson->get_criteria_lesson_id(), $pages);
				$percentage = $pages!=0?($viewed/$pages)*100:0;
				$lesson->set_percentage_finished($percentage);
				$lesson->set_percentage_added(1);
				//dump("percentage lesson: " . $percentage);
				//dump($viewed . " / " . $pages);
				if($percentage >= $lesson->get_criteria_lesson_percentage())
					$visible = true;
			}
			$teaser = $parent_visible;
		}
		else
		{
			$teaser = true;
			$visible = true;
		}
		//dump("lesson teaser : " . $teaser);
		//dump("lesson zichtbaar : " . $visible);
		
		$e_teaser = true;
		$e_visible = true;
		//dump($lesson);
		if($lesson->get_criteria_lesson_excercise_percentage())
		{
			$lesson_exc = $lesson->get_criteria_lesson_excercise_ids();
			$excercises = $this->manager->get_lesson_excercise_manager()->get_data_manager()->retrieve_lesson_excercises_by_user_id($lesson->get_user_id());
			foreach($excercises as $exc)
			{
				$in_arr = in_array($exc->get_id(), $lesson_exc);
				if($in_arr)
				{
					$users = $exc->get_user_ids();
					if(empty($users) || in_array($user->get_id(), $users) || $check_user == false)
					{
						if($exc->get_visible() || $exc->get_criteria_lesson_excercise_percentage() || $exc->get_criteria_lesson_percentage() || $check_visible == false)
						{
							$number = $this->count_lesson_excercise_components($exc->get_id());
							$correct = StatisticsDataManager::instance($this->manager)->count_statistics_set_succes($this->manager->get_user()->get_id(), $exc->get_id());
							$correct += StatisticsDataManager::instance($this->manager)->count_statistics_question_set_succes($this->manager->get_user()->get_id(), $exc->get_id());
							$correct += StatisticsDataManager::instance($this->manager)->count_statistics_selection_set_succes($this->manager->get_user()->get_id(), $exc->get_id());
							$percentage = $number!=0?($correct/$number)*100:0;
							$lesson->set_percentage_finished($lesson->get_percentage_finished()+$percentage);
							$lesson->set_percentage_added($lesson->get_percentage_added()+1);
							//dump("percentage set: " . $percentage);
							if($percentage<$lesson->get_criteria_lesson_excercise_percentage())
							{
								$e_visible = false;
								//dump("exc zichtbaar: ");
								break;
							}
						}
						else
						{
							//dump("exc teaser: ");
							$e_teaser = false;
						}
					}
				}
			}
		}
		$lesson->set_teaser($teaser && $e_teaser);
		$lesson->set_visible($visible && $e_visible);
		//dump("teaser: " . $lesson->get_teaser());
		//dump("zichtbaar: " . $lesson->get_visible());
	}
	
	public function retrieve_visible_and_order_by_lesson_and_user_id($lesson_id, $user_id)
	{
		$condition = "lesson_id = '" . $lesson_id . "' AND user_id = '" . $user_id . "'";
		return parent::retrieve(self::TABLE_META_NAME, null, '', self::ONE_RECORD, $condition);
	}
	
	public function count_lessons($user_id = null, $map_id = null)
	{	   	
		if(is_null($user_id))
			$user_id = $this->manager->get_user()->get_id();
		$query = "SELECT count(`order`) as count FROM `".self::TABLE_META_NAME."` as m";
		$conditions = " WHERE m.user_id = '" . $user_id . "'";
		if(is_numeric($map_id))
		{
			$query .= " LEFT JOIN `" . RightDataManager::LOC_USER_MAP_REL_TABLE_NAME . "` as r ON r.object_id = m.lesson_id";
			$conditions .= " AND map_id = " . $map_id;
		}
		elseif($map_id == "others")
		{
			$query .= " LEFT JOIN `" . RightDataManager::LOC_USER_MAP_REL_TABLE_NAME . "` as r ON r.object_id = m.lesson_id";
			$conditions .= " AND r.map_id = 0 AND r.user_id = " . $user_id . " AND r.location_id = " . RightManager::LESSON_LOCATION_ID;
		}
		$resultData = $this->retrieve_data($query . $conditions);
		return $resultData[0]->count;
	}
	
	public function retrieve_lesson_themes_by_lesson_id($lesson_id)
	{
		$theme_ids = array();
		$themes = parent::retrieve(self::LES_THEME_TABLE_NAME, null, '', self::MANY_RECORDS, "lesson_id = " . $lesson_id);
		foreach($themes as $theme)
			$theme_ids[] = $theme->theme_id;
		return $theme_ids;
	}
	
	public function retrieve_excercise_from_lesson_criteria_lesson($lesson_id, $user_id)
	{
		require_once Path :: get_path() . 'pages/lesson/lesson_excercise/lib/lesson_excercise.class.php';
		$condition = "criteria_lesson_id = " . $lesson_id . " AND user_id = " . $user_id;
		$lesson_meta = parent::retrieve(self::TABLE_META_NAME, null, '', self::MANY_RECORDS, $condition);
		$count = count($lesson_meta);
		if($count)
		{
			$condition = "lesson_id IN (";
			foreach($lesson_meta as $index => $meta)
			{
				$condition .= $meta->lesson_id;
				if($count > $index + 1)
					$condition .= ", ";
				else
					$condition .= ")";
			}
			$join = array();
			$join[] = new Join(self::EXC_TABLE_NAME, 'e', 'id', Join::MAIN_TABLE);
			$join[] = new Join(self::TABLE_META_EXC_NAME, 'm', 'criteria_lesson_excercise_id', "LEFT JOIN", Join::MAIN_TABLE, 'id');
			return parent::retrieve($join,self::EXC_CLASS_NAME,'',self::MANY_RECORDS,$condition);
		}
		else return array();
	}
	
	public function retrieve_lesson_from_post($order_nr = "", $check_criteria_only = false)
	{
		$arr = array();
		$arr['id'] = Request::post("id");
		$arr['title'] = addslashes(htmlspecialchars(Request::post("title")));
		$arr['description'] = addslashes(htmlspecialchars(Request::post("description")));
		$arr['rating'] = Request::post("rating");
		if(is_null(Request::post("order")))
			$arr['order'] = self :: get_new_order();
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
				$incorrect_number = is_null($criteria_lesson_percentage) || !is_numeric($criteria_lesson_percentage) || $criteria_lesson_percentage < 0 || $criteria_lesson_percentage > 100;
				if(!$no_selection && $incorrect_number)
				{
					Error::get_instance()->set_result(false);
					Error::get_instance()->append_message(Language::get_instance()->translate(940));
				}
				elseif($no_selection)
					$arr['criteria_lesson_percentage'] = 0;
				
				$incorrect_number = is_null($criteria_lesson_excercise_percentage) || !is_numeric($criteria_lesson_excercise_percentage) || $criteria_lesson_excercise_percentage < 0 || $criteria_lesson_excercise_percentage > 100;
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
		
		$arr['user_id'] = $this->manager->get_user()->get_id();
		
		if(!$check_criteria_only)
		{
			if(is_null($arr['title']) || $arr['title'] == "")
			{
				Error::get_instance()->set_result(false);
				Error::get_instance()->append_message(Language::get_instance()->translate(119));
			}
			/*
			elseif($order_nr == "" && $arr['id'] == 0 && !is_null($this->manager->get_data_manager()->retrieve_lesson_by_title($arr['title'])))
			{
				Error::get_instance()->set_result(false);
				Error::get_instance()->append_message(Language::get_instance()->translate(120));
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
			if(is_null($arr['theme_ids']) || empty($arr['theme_ids']))
			{
				Error::get_instance()->set_result(false);
				Error::get_instance()->append_message(Language::get_instance()->translate(305));
			}
		}
		
		return new Lesson($arr);
	}
	
	public function get_new_order()
	{
	   	$query = "SELECT max(`order`) as max FROM `".self::TABLE_META_NAME."` as m LEFT JOIN `" . RightDataManager::LOC_USER_MAP_REL_TABLE_NAME . "` as r ON m.lesson_id = r.object_id AND m.user_id = r.user_id WHERE m.user_id = '" . $this->manager->get_user()->get_id() . "' AND r.location_id = " . RightManager::LESSON_LOCATION_ID;
		$resultData = $this->retrieve_data($query);
		return ++$resultData[0]->max;
	}

	public function retrieve_lesson_first_visible($lesson_id, $user_id)
	{
		$conditions = "lesson_id = '" . $lesson_id . "' AND user_id = '" . $user_id . "'";
		$obj = self::retrieve(self :: TABLE_META_NAME, null, '', self :: ONE_RECORD, $conditions);
		if(!is_null($obj))
		{
			return $obj->first_visible;
		}
		else
		{
			return 0;
		}
	}
	
	public function retrieve_lessons_with_search_form($right, $limit = "", $shop = false, $filter = false, $continuation = false, $undo = false)
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
					$conditions .= 'description LIKE  \'%' . addslashes($word) . '%\' OR title LIKE \'%' . addslashes($word) . '%\'';
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
		$sqlString = "SELECT DISTINCT(v.id) as di, v.* FROM `".self::TABLE_NAME."` as v" .
						" LEFT JOIN `".self::LES_THEME_TABLE_NAME."` as t ON v.id = t.lesson_id";
		if(!$this->manager->get_user()->is_admin() && $right != RightManager::NO_RIGHT)
		{
			$sqlString .= " LEFT JOIN `" . RightDataManager::LOC_OBJ_USER_TABLE_NAME . "` as urt ON urt.object_id = v.id";
			if($conditions != "")
			{
				$conditions .= " AND ";
			}
			$conditions .= "location_id = " . RightManager::LESSON_LOCATION_ID . " AND urt.user_id = " . $this->manager->get_user()->get_id() . " AND urt." . RightDataManager::get_right_conditions($right);
		}
		if($right == RightManager::NO_RIGHT && ($shop || $filter))
		{
			if($shop)
			{
				$sqlString .= " LEFT JOIN `shop_meta_data` as sm ON sm.object_id = v.id AND sm.location_id = " . RightManager::LESSON_LOCATION_ID;
				if(!is_null(Request :: post('user')) != "" && Request::post('user') != "")
				{				
					$words = preg_split("/ /", Request :: post('user'));
					$count = count($words);
					$i = 1;
					if($count)
					{
						$sqlString .= " LEFT JOIN `" . RightDataManager::LOC_OBJ_CRT_TABLE_NAME . "` as ct on v.id = ct.object_id AND ct.location_id = " . RightManager::LESSON_LOCATION_ID;
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
			$conditions .= (!$filter?"v.id NOT IN (SELECT object_id FROM `" . RightDataManager::LOC_OBJ_USER_TABLE_NAME . "` WHERE location_id = " . RightManager::LESSON_LOCATION_ID . " AND user_id = " . $this->manager->get_user()->get_id() . ") " . ($shop?"AND":""):"") . ($shop?" sm.valid" . ($filter?(!$undo?" IS NULL":(is_null(Request::post("shop_valid")) || !is_numeric(Request::post("shop_valid"))?" IN (0, 1)":" = " . Request::post("shop_valid"))):" = 1"):"");
		}
		if($shop)
		{
			if($conditions != "")
			{
				$conditions .= " AND ";
			}
			$conditions .= "v.id NOT IN (SELECT object_id FROM `shop` WHERE location_id = " . RightManager::LESSON_LOCATION_ID . " AND user_id = " . $this->manager->get_user()->get_id() . ")";
		}
		if($continuation)
		{
			if($conditions != "")
			{
				$conditions .= " AND ";
			}
			$conditions .= "v.id NOT IN (SELECT object_id FROM `lesson_continuation_available_objects` WHERE type_id = " . LessonContinuationAvailability::TYPE_LESSON . ") AND 1 = (SELECT count(*) FROM `" . self::LES_THEME_TABLE_NAME . "` WHERE lesson_id = id)";
		}
		if($conditions != "")
			$sqlString .= " WHERE " . $conditions;
	
		if($limit!="")
			$sqlString .= " LIMIT " . $limit;
		$objects = $this->retrieve_data($sqlString);
		$results = $this->Mapping($objects,self::CLASS_NAME);
		return $results;
	}
	
	public function retrieve_lesson_meta_data_criteria_excercise_ids($lesson_id, $user_id)
	{
		$condition = "lesson_id = " . $lesson_id ." AND user_id = " . $user_id;
		$results = parent::retrieve(self::TABLE_META_EXC_NAME, null, '', self::MANY_RECORDS, $condition);
		
		$ids = array();
		foreach($results as $r)
			$ids[] = $r->criteria_lesson_excercise_id;
		return $ids;
	}
	
	/** LESSON_PAGE */

	public function retrieve_lesson_pages_by_lesson_id($lesson_id)
	{
		$condition = "lesson_id = '" . $lesson_id ."'";
		$order = "`order`";
		return parent::retrieve(self::PAGE_TABLE_NAME, self::PAGE_CLASS_NAME, $order, self::MANY_RECORDS, $condition);
	}
	
	public function retrieve_lesson_page_by_lesson_id_and_order($lesson_id, $order)
	{
		$condition = "lesson_id = '" . $lesson_id ."' AND `order` = '" . $order . "'";
		return parent::retrieve(self::PAGE_TABLE_NAME, self::PAGE_CLASS_NAME, '', self::ONE_RECORD, $condition);
	}
	
	public function retrieve_lesson_page($lesson_page_id)
	{
		return parent::retrieve_by_id(self::PAGE_TABLE_NAME, self::PAGE_CLASS_NAME, $lesson_page_id);
	}
	
	public function insert_lesson_page($lesson_page)
	{
		return parent::insert(self::PAGE_TABLE_NAME, $lesson_page);
	}
	
	public function update_lesson_page($lesson_page)
	{
		return self::update_by_id(self::PAGE_TABLE_NAME, $lesson_page);
	}
	
	public function update_lesson_page_order($lesson_page_id, $order)
	{
		$custom_properties = new CustomProperties();
		$custom_properties->add_property("order", $order);
		$conditions = "id = '" . $lesson_page_id . "'";
		return self::update(self::PAGE_TABLE_NAME, $custom_properties, $conditions);
	}
	
	public function delete_lesson_page($id)
	{
		return self::delete_by_id(self::PAGE_TABLE_NAME, $id);
	}
	
	public function count_lesson_pages($lesson_id)
	{	   	
		$condition = "lesson_id = '" . $lesson_id . "'";
		return self::count(self::PAGE_TABLE_NAME, $condition);
	}
	
	public function retrieve_lesson_page_from_post()
	{
		$arr = array();
		$arr['id'] = Request::get("id");
		$arr['lesson_id'] = Request::get("lesson_id");
		$arr['title'] = addslashes(htmlspecialchars(Request::post("title")));
		$arr['type'] = Request::post("type_id");
		if(is_null(Request::post('order')))
			$arr['order'] = self :: get_new_page_order($arr['lesson_id']);
		else
			$arr['order'] = Request::post('order');
		$arr['type_object_id'] = Request::post('object_id');
		$arr['next'] = self::parse_checkbox_value(Request::post('next'));
		
		if(is_null($arr['title']) || $arr['title'] == "")
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->append_message(Language::get_instance()->translate(119));
		}
		if(is_null($arr['type']) || $arr['type'] == 0)
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->append_message(Language::get_instance()->translate(122));
		}
		elseif(is_null($arr['type_object_id']) || $arr['type_object_id'] == 0)
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->append_message(Language::get_instance()->translate(123));
		}
		
		return new LessonPage($arr);
	}
	
	public function get_new_page_order($lesson_id)
	{
	   	$query = "SELECT max(`order`) as max FROM `".self::PAGE_TABLE_NAME."` WHERE lesson_id = '" . $lesson_id . "'";
		$resultData = $this->retrieve_data($query);
		return ++$resultData[0]->max;
	}
	
	public function delete_other_lesson_pages($lesson_id, $filter)
	{
		$size = count($filter);
		$condition = "lesson_id = '" . $lesson_id . "'";
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
			
		$condition_textless = ($condition != ""?$condition . " AND ":""). "type <> '1'";
		$condition_text = ($condition != ""?$condition . " AND ":""). "type = '1'";
			
		$result = self::delete(self::PAGE_TABLE_NAME, $condition_textless);
		$result_data = self::retrieve(self::PAGE_TABLE_NAME, self::PAGE_CLASS_NAME, '', self::MANY_RECORDS, $condition_text);
		foreach ($result_data as $data)
		{
			$conditions = "id = '" . $data->get_type_object_id() . "'";
			$result &= self::delete(self::PAGE_TEXT_TABLE_NAME, $conditions);
		}
		$result &= self::delete(self::PAGE_TABLE_NAME, $condition_text);

		return $result;
	}
	
	public function is_lesson_course($lesson_id)
	{
		$user = UserDataManager::instance(null)->retrieve_user(self::LESSON_COURSE_USER_ID);
		return RightManager::instance()->get_right_location_object("lesson", $user, $lesson_id) >= RightManager::READ_RIGHT;
	}

	/** LESSON_PAGE_TEXT */
		
	public function retrieve_lesson_page_text($lesson_page_text_id)
	{
		return parent::retrieve_by_id(self::PAGE_TEXT_TABLE_NAME, self::PAGE_TEXT_CLASS_NAME, $lesson_page_text_id);
	}
	
	public function insert_lesson_page_text($lesson_page_text)
	{
		return parent::insert(self::PAGE_TEXT_TABLE_NAME, $lesson_page_text);
	}
	
	public function update_lesson_page_text($lesson_page_text)
	{
		return self::update_by_id(self::PAGE_TEXT_TABLE_NAME, $lesson_page_text);
	}
	
	public function delete_lesson_page_text($id)
	{
		return self::delete_by_id(self::PAGE_TEXT_TABLE_NAME, $id);
	}
	
	public function retrieve_lesson_page_text_from_post()
	{
		$arr = array();
		$arr['id'] = Request::post("object_id");
		$arr['text'] = addslashes(Request::post("text"));
		if(is_null($arr['text']) || $arr['text'] == "")
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->append_message(Language::get_instance()->translate(124));
		}
		if(is_null($arr['id']))
		{
			Error::get_instance()->set_result(false);
		}
		
		return new LessonPageText($arr);
	}
	
	/** LESSON_CONTINUATION **/
	const CONT_TABLE_NAME = 'lesson_continuation';
	const CONT_CHK_TABLE_NAME = 'lesson_continuation_checked';
	const CONT_CLASS_NAME = 'LessonContinuation';
	const CONT_REL_TABLE_NAME = 'lesson_continuation_excercise_relation';
	const CONT_AV_TABLE_NAME = 'lesson_continuation_available_objects';
	const CONT_TRANSF_TABLE_NAME = 'lesson_continuation_transfered';
	const MES_TABLE_NAME = 'message';
	const MES_REL_TABLE_NAME = 'message_to_user';
	public function insert_lesson_continuation($lesson_continuation)
	{
		if($this->manager->get_user()->get_group_id() != GroupManager::GROUP_GUEST_ID)
		{ 
			$id = MessageDataManager::instance(null)->insert_message($lesson_continuation->get_message(), true);
			if($id)
			{
				$lesson_continuation->set_message_id($id);
				$id = self::insert(self::CONT_TABLE_NAME, $lesson_continuation);
				
				foreach($lesson_continuation->get_lesson_excercise_ids() as $lesson_excercise_id)
				{
					$custom_properties = new CustomProperties();
					$custom_properties->add_property("lesson_continuation_id", $id);
					$custom_properties->add_property("lesson_excercise_id", $lesson_excercise_id);
					self::insert(self::CONT_REL_TABLE_NAME, $custom_properties);
				}
			}
			return $id;
		}
		else
		{
			$continuations = unserialize(Session::retrieve("suggested_lesson_continuations"));
			if(is_null($continuations) || !is_array($continuations))
			{
				$continuations = array();
			}
			$id = 1;
			foreach($continuations as $index => $continuation)
			{
				if($continuation->get_id() > $id)
					$id = $continuation->get_id();
			}
			$id++;
			$lesson_continuation->set_id($id);
			$continuations[] = $lesson_continuation;
			Session::register("suggested_lesson_continuations", serialize($continuations));
		}
	}

	public function retrieve_lesson_continuation($id)
	{
		return self::retrieve_by_id(self::CONT_TABLE_NAME, self::CONT_CLASS_NAME, $id);
	}
	
	public function update_lesson_continuation($lesson_continuation)
	{
		return self::update_by_id(self::CONT_TABLE_NAME, $lesson_continuation);
	}
	
	public function retrieve_lesson_continuations_by_user_id($user_id, $from = true)
	{
		if($this->manager->get_user()->get_group_id() != GroupManager::GROUP_GUEST_ID)
		{
			$conditions = ($from?"from_user_id":"to_user_id") . "=" . $user_id;
			return self::retrieve(self::CONT_TABLE_NAME, self::CONT_CLASS_NAME, '', self::MANY_RECORDS, $conditions);
		}
		else
		{
			$suggested = unserialize(Session::retrieve("suggested_lesson_continuations"));
			if(!is_null($suggested) && is_array($suggested))
				return $suggested;
			else
				return array();
		}
	}

	public function count_lesson_continuations_by_user_id($user, $from = true)
	{
		if($user->get_group_id() != GroupManager::GROUP_GUEST_ID)
		{
			$conditions = ($from?"from_user_id":"to_user_id") . "=" . $user->get_id();
			return self::count(self::CONT_TABLE_NAME, $conditions);
		}
		else
		{
			$suggested = unserialize(Session::retrieve("suggested_lesson_continuations"));
			if(!is_null($suggested) && is_array($suggested))
				return count($suggested);
			else
				return 0;
		}
	}
	
	public function retrieve_lesson_continuation_from_post()
	{
		$message = array();
		$message["id"] = 0;
		$message["from_user_id"] = $this->manager->get_user()->get_id();
		$message["to_user_ids"] = array(Request::get("user_id"));
		$message["time"] = time();
		$message["read"] = 0;
		$message["title"] = addslashes(htmlspecialchars(Request::post("title")));
		$message["message"] = addslashes(htmlspecialchars(Request::post("message")));
		
		$arr = array();
		$arr['id'] = Request::post("id");
			
		$lesson_id = Request::post("lesson_id");
		$lesson_excercise_ids = Request::post("lesson_excercise_ids");
			
		$arr['lesson_id'] = $lesson_id;
		$arr['lesson_excercise_ids'] = is_null($lesson_excercise_ids)?array():$lesson_excercise_ids;
		$arr['from_user_id'] = $this->manager->get_user()->get_id();
		$arr['to_user_id'] = Request::get("user_id");
		$arr['bought'] = 1;
		$arr['requested'] = 0;
		
		if(is_null($message['title']) || $message['title'] == "")
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->append_message(Language::get_instance()->translate(119));
		}
		if(is_null($message['message']) || $message['message'] == "")
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->append_message(Language::get_instance()->translate(121));
		}
		if(is_null($arr['lesson_id']) || !is_numeric($arr["lesson_id"]))
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->append_message(Language::get_instance()->translate(1073));
		}
		$lesson_continuation = new LessonContinuation($arr);
		$lesson_continuation->set_message(new Message($message));
		return $lesson_continuation;
	}
	
	public function retrieve_lesson_selection_lesson_excercise_ids($id)
	{
		$excercise_ids = array();
		$results = parent::retrieve(self::CONT_REL_TABLE_NAME, null, '', self::MANY_RECORDS, "lesson_continuation_id = " . $id);
		if(count($results))
		{
			foreach($results as $r)
				$excercise_ids[] = $r->lesson_excercise_id;
		}
		return $excercise_ids;
	}
	
	public function retrieve_suggested_lesson_continuations($user_id)
	{
		if($this->manager->get_user()->get_group_id() != GroupManager::GROUP_GUEST_ID)
		{
			$join = array();
			$join[] = new Join(self::CONT_TABLE_NAME, "l", "id", Join::MAIN_TABLE);
			$join[] = new Join(self::CONT_TRANSF_TABLE_NAME, "f", "lesson_continuation_id", "LEFT JOIN", Join::MAIN_TABLE, "id");
			return self::retrieve($join, self::CONT_CLASS_NAME, '', self::MANY_RECORDS, '(to_user_id = ' . $user_id . ' AND from_user_id = 0) OR (f.user_id = ' . $user_id . ')');
		}
		else
		{
			$suggested = unserialize(Session::retrieve("suggested_lesson_continuations"));
			if(!is_null($suggested) && is_array($suggested))
				return $suggested;
			else
				return array();
		}
	}
	
	public function insert_transfered_lesson_continuation($lesson_continuation_id, $user_id)
	{
		$custom_properties = new CustomProperties();
		$custom_properties->add_property("lesson_continuation_id", $lesson_continuation_id);
		$custom_properties->add_property("user_id", $user_id);
		return self::insert(self::CONT_TRANSF_TABLE_NAME, $custom_properties);
	}
	
	public function retrieve_lesson_continuation_available_objects($type_id)
	{
		$class_name = self::CLASS_NAME;
		$table_name = self::TABLE_NAME;
		if($type_id == LessonContinuationAvailability::TYPE_LESSON_EXCERCISE)
		{
			require_once Path :: get_path() . 'pages/lesson/lesson_excercise/lib/lesson_excercise.class.php';
			$table_name = self::EXC_TABLE_NAME;
			$class_name = self::EXC_CLASS_NAME;
		}
		$join = array();
		$join[] = new Join(self::CONT_AV_TABLE_NAME, "ca", "object_id", Join::MAIN_TABLE);
		$join[] = new Join($table_name, "m", "id", "LEFT JOIN", Join::MAIN_TABLE, "object_id");
		$conditions = "type_id = " . $type_id;
		return parent::retrieve($join, $class_name, '', self::MANY_RECORDS, $conditions, '', 'm.*');
	}
	
	public function is_available_lesson($lesson_id)
	{
		$conditions = "type_id = " . LessonContinuationAvailability::TYPE_LESSON . " AND object_id = " . $lesson_id;
		return parent::count(self::CONT_AV_TABLE_NAME, $conditions);
	}

	public function insert_lesson_continuation_available_object($type_id, $object_id)
	{
		$custom = new CustomProperties();
		$custom->add_property("type_id", $type_id);
		$custom->add_property("object_id", $object_id);
		return parent::insert(self::CONT_AV_TABLE_NAME, $custom);
	}

	public function delete_other_lesson_continuation_available_objects($filter, $type_id)
	{
		$size = count($filter);
		$condition = "";
		if($size)
		{
			$i = 1;
			foreach($filter as $id)
			{
				$condition .= "'" . $id . "'";
				if($i<$size)
					$condition .= ",";
				$i++;
			}
			$condition .= ")";
		}
		
		$condition = "type_id = '" . $type_id . "'" . ($condition!=""?" AND `object_id` NOT IN (" . $condition:"");
		return parent::delete(self::CONT_AV_TABLE_NAME, $condition);
	}
	
	public function delete_other_lesson_continuations($filter)
	{
		$size = count($filter);
		$condition = "";
		if($size)
		{
			$i = 1;
			foreach($filter as $id)
			{
				$condition .= "'" . $id . "'";
				if($i<$size)
					$condition .= ",";
				$i++;
			}
			$condition .= ")";
		}
		
		$condition = "from_user_id = " . $this->manager->get_user()->get_id() . ($condition==""?"":" AND `id` NOT IN (" . $condition);
		$results = parent::retrieve(self::CONT_TABLE_NAME, self::CONT_CLASS_NAME, "", self::MANY_RECORDS, $condition);
		parent::delete(self::CONT_TABLE_NAME, $condition);
		if(!is_null($results) && !empty($results) && Error::get_instance()->get_result())
		{
			$size = count($results);
			$condition = "";
			$l_condition = "";
		
			$i = 1;
			foreach($results as $result)
			{
				RightManager::instance()->delete_location_object_user_rights(RightManager::MESSAGE_LOCATION_ID, $result->get_message_id());
				$condition .= "'" . $result->get_message_id() . "'";
				$l_condition .= "'" . $result->get_id() . "'";
				if($i<$size)
				{
					$condition .= ",";
					$l_condition .= ",";
				}
				$i++;
			}
			$condition .= ")";
			$l_condition .= ")";
			$m_condition = ($condition==""?"":"`id` IN (" . $condition);
			$lr_condition = ($l_condition==""?"":"`lesson_continuation_id` IN (" . $l_condition);
			$r_condition = ($condition==""?"":"`message_id` IN (" . $condition);
			parent::delete(self::MES_TABLE_NAME, $m_condition);
			parent::delete(self::CONT_REL_TABLE_NAME, $lr_condition);
			parent::delete(self::MES_REL_TABLE_NAME, $r_condition);
		}
		return Error::get_instance()->get_result();
	}

	public function delete_lesson_continuation($id)
	{
		if($this->manager->get_user()->get_group_id() != GroupManager::GROUP_GUEST_ID)
		{
			$condition = "id = " . $id;
			$result = parent::retrieve(self::CONT_TABLE_NAME, self::CONT_CLASS_NAME, "", self::ONE_RECORD, $condition);
			parent::delete(self::CONT_TABLE_NAME, $condition);
			if(!is_null($result) && Error::get_instance()->get_result())
			{
				$m_condition = "`id` = " . $result->get_message_id();
				$lr_condition = "`lesson_continuation_id` = " . $result->get_id();
				$r_condition = "`message_id`  = " . $result->get_message_id();
				parent::delete(self::MES_TABLE_NAME, $m_condition);
				parent::delete(self::CONT_REL_TABLE_NAME, $lr_condition);
				parent::delete(self::MES_REL_TABLE_NAME, $r_condition);
				RightManager::instance()->delete_location_object_user_rights(RightManager::MESSAGE_LOCATION_ID, $result->get_message_id());
			}
			return Error::get_instance()->get_result();
		}
		else
		{
			$continuations = unserialize(Session::retrieve("suggested_lesson_continuations"));
			if(is_null($continuations) || !is_array($continuations))
			{
				$continuations = array();
			}
			foreach($continuations as $index => $continuation)
			{
				if($continuation->get_id() == $id)
					unset($continuations[$index]);
			}
			Session::retrieve("suggested_lesson_continuations", serialize($continuations));
		}
	}
	
	public function retrieve_lesson_continuation_checked($user_id)
	{
		return self::retrieve(self::CONT_CHK_TABLE_NAME, null, '', self::ONE_RECORD, "user_id = " . $user_id);
	}
	
	public function insert_lesson_continuation_checked($user_id, $time)
	{
		$custom = new CustomProperties();
		$custom->add_property("user_id", $user_id);
		$custom->add_property("time", $time);
		return self::insert(self::CONT_CHK_TABLE_NAME, $custom);
	}

	public function update_lesson_continuation_checked($user_id, $time)
	{
		$custom = new CustomProperties();
		$custom->add_property("time", $time);
		return self::update(self::CONT_CHK_TABLE_NAME, $custom, "user_id = " . $user_id);
	}
	
	public function update_lesson_continuation_bought($id, $bought)
	{
		$custom = new CustomProperties();
		$custom->add_property("bought", $bought);
		return self::update(self::CONT_TABLE_NAME, $custom, "id = " . $id);
	}
	
	public function update_lesson_continuation_requested($id, $requested)
	{
		$custom = new CustomProperties();
		$custom->add_property("requested", $requested);
		return self::update(self::CONT_TABLE_NAME, $custom, "id = " . $id);
	}
}

?>