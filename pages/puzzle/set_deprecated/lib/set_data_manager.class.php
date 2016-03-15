<?php

require_once Path :: get_path() . 'pages/puzzle/set/lib/set.class.php';

class SetDataManager extends DataManager
{
	const STAT_TABLE_NAME = 'statistics_puzzle';
	const REL_TABLE_NAME = 'puzzle_set_relation';
	const REL_THEME_TABLE_NAME = 'puzzle_set_theme_relation';
	const TABLE_NAME = 'puzzle_set';
	const LES_EXC_TABLE_NAME = 'lesson_excercise';
	const CLASS_NAME = 'Set';
	
	public static function instance($manager)
	{
		parent::$_instance = new SetDataManager($manager);
		return parent::$_instance;
	}
	
	public function retrieve_set($id)
	{
		return parent::retrieve_by_id(self::TABLE_NAME,self::CLASS_NAME,$id);
	}

	public function retrieve_sets_by_conditions($conditions)
	{
		return parent::retrieve(self::TABLE_NAME,self::CLASS_NAME,'',self::MANY_RECORDS,$conditions);
	}
	
	public function retrieve_sets($right = RightManager::READ_RIGHT)
	{
		if(!$this->manager->get_user()->is_admin())
		{
			$join = array();
			$join[] = new Join(self::TABLE_NAME, "s", "id", Join::MAIN_TABLE);
			$join[] = new Join(RightDataManager::LOC_OBJ_USER_TABLE_NAME, "urt", "object_id", "LEFT JOIN", Join::MAIN_TABLE, "id");
			$conditions = "location_id = " . RightManager::SET_LOCATION_ID . " AND urt.user_id = " . $this->manager->get_user()->get_id() . " AND urt." . RightDataManager::get_right_conditions($right);
			return parent::retrieve($join,self::CLASS_NAME, '', self::MANY_RECORDS, $conditions);
		}
		else
		{
			return parent::retrieve(self::TABLE_NAME,self::CLASS_NAME);
		}
	}

	public function count_sets($right = RightManager::READ_RIGHT)
	{
		if(!$this->manager->get_user()->is_admin())
		{
			$conditions = "location_id = " . RightManager::SET_LOCATION_ID . " AND urt.user_id = " . $this->manager->get_user()->get_id() . " AND urt." . RightDataManager::get_right_conditions($right);
			return parent::count(RightDataManager::LOC_OBJ_USER_TABLE_NAME, $conditions);
		}
		else
		{
			return parent::count(self::TABLE_NAME);
		}
	}
	
	public function retrieve_set_puzzles($set_id)
	{
		$puzzles = array();
		$condition = "set_id = '" . $set_id . "'";
		$relations = parent::retrieve(self::REL_TABLE_NAME,null,'',self::MANY_RECORDS,$condition);
		foreach($relations as $relation)
		{
			if(!is_null($relation))
			{
				$puzzles[] = $this->manager->get_parent_manager()->get_data_manager()->retrieve_puzzle_properties_by_puzzle_id($relation->puzzle_id);
			}
		}
		return $puzzles;
	}
	
	public function retrieve_set_attempt($set_id, $user_id)
	{
		$puzzles = array();
		$sqlString = "SELECT max(set_attempt) as max FROM `" . self::STAT_TABLE_NAME . "` WHERE user_id = '" . $user_id . "' AND set_id = '".$set_id."';";
		$resultData = $this->retrieve_data($sqlString);
		return $resultData[0]->max+1;
	}
	
	public function retrieve_sets_with_search_form($right = RightManager::READ_RIGHT, $limit = "", $shop = false, $filter = false)
	{
		$conditions = "";
		$theme_ids = Request :: post('theme_id');
		if(!is_null($theme_ids) && !empty($theme_ids))
		{
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
		if(Request :: post('difficulty_id') != 0)
		{
			if($conditions != "")
				$conditions .= " AND ";
			$conditions .= "difficulty_id = '" . Request :: post('difficulty_id') ."'";
		}
		$sqlString = "SELECT DISTINCT(s.id) as si, s.* FROM `".self::TABLE_NAME."` as s LEFT JOIN `".self::REL_THEME_TABLE_NAME."` as t ON s.id = t.set_id";
	
		if(!$this->manager->get_user()->is_admin() && $right != RightManager::NO_RIGHT)
		{
			$sqlString .= " LEFT JOIN `" . RightDataManager::LOC_OBJ_USER_TABLE_NAME . "` as urt ON urt.object_id = s.id";
			if($conditions != "")
			{
				$conditions .= " AND ";
			}
			$conditions .= "location_id = " . RightManager::SET_LOCATION_ID . " AND urt.user_id = " . $this->manager->get_user()->get_id() . " AND urt." . RightDataManager::get_right_conditions($right);
		}
		if($right == RightManager::NO_RIGHT)
		{
			if($shop)
				$sqlString .= " LEFT JOIN `shop_meta_data` as sm ON sm.object_id = s.id AND sm.location_id = " . RightManager::SET_LOCATION_ID;
			if($conditions != "")
			{
				$conditions .= " AND ";
			}
			$conditions .= (!$filter?"s.id NOT IN (SELECT object_id FROM `" . RightDataManager::LOC_OBJ_USER_TABLE_NAME . "` WHERE location_id = " . RightManager::SET_LOCATION_ID . " AND user_id = " . $this->manager->get_user()->get_id() . ") ".($shop?"AND":""):"") . ($shop?" sm.valid" . ($filter?" IS NULL":" = 1"):"");
		}
		if($shop)
		{
			if($conditions != "")
			{
				$conditions .= " AND ";
			}
			$conditions .= "s.id NOT IN (SELECT object_id FROM `shop` WHERE location_id = " . RightManager::SET_LOCATION_ID . " AND user_id = " . $this->manager->get_user()->get_id() . ")";
		}
		if($conditions != "")
			$sqlString .= " WHERE " . $conditions;
	
		if($limit!="")
		{
			$sqlString .= " LIMIT " . $limit;
		}
		$objects = $this->retrieve_data($sqlString);
		$results = $this->Mapping($objects,self::CLASS_NAME);
		return $results;
	}
	
	public function retrieve_set_themes($set_id)
	{
		$condition = "set_id = '" . $set_id . "'";
		$result_ids = parent::retrieve(self::REL_THEME_TABLE_NAME, null, '', self::MANY_RECORDS, $condition);
		$theme_ids = array();
		foreach($result_ids as $id)
			$theme_ids[] = $id->theme_id;
		return $theme_ids;
	}
		
	public function insert_set($set)
	{
		$id = parent::insert(self::TABLE_NAME,$set);		
		if($id)
		{
			$query = 'INSERT INTO `'.self::REL_THEME_TABLE_NAME.'` (set_id, theme_id) VALUES';
			$i = 1;
			$size = count($set->get_theme_ids());
			foreach($set->get_theme_ids() as $theme_id)
			{
				$query .= "('".$id."', '".$theme_id."')";
				if($i<$size)
					$query .= ", ";
				$i++;
			}
			
			$query .= ';';
			self::$_connection->execute_sql($query,'INSERT');
		}
		return $id;
	}
	
	public function insert_set_puzzles($set_id, $puzzle_ids)
	{		
		$query = 'INSERT INTO `'.self::REL_TABLE_NAME.'` (set_id, puzzle_id) VALUES';

		$i = 1;
		$size = count($puzzle_ids);
		foreach($puzzle_ids as $puzzle_id)
		{
			$query .= "('".$set_id."', '".$puzzle_id."')";
			if($i<$size)
				$query .= ", ";
			$i++;
		}
		
		$query .= ';';
		return self::$_connection->execute_sql($query,'INSERT');
	}

	public function update_set($set)
	{
		$conditions = 'id="'.$set->get_id().'"';
		$success = parent::update(self::TABLE_NAME,$set,$conditions);
		if($success)
		{
			$conditions = "set_id = '". $set->get_id()."'"; 
			parent::delete(self::REL_THEME_TABLE_NAME, $conditions);
			
			$query = 'INSERT INTO `'.self::REL_THEME_TABLE_NAME.'` (set_id, theme_id) VALUES';
			$i = 1;
			$size = count($set->get_theme_ids());
			foreach($set->get_theme_ids() as $theme_id)
			{
				$query .= "('".$set->get_id()."', '".$theme_id."')";
				if($i<$size)
					$query .= ", ";
				$i++;
			}
			
			$query .= ';';
			self::$_connection->execute_sql($query,'INSERT');
		}
		return $success;
	}
    
	public function delete_set($id)
	{
		$condition = "set_id = '" . $id . "'";
		$success = parent::delete_by_id(self::TABLE_NAME,$id) && parent::delete(self::REL_TABLE_NAME, $condition) && parent::delete(self::REL_THEME_TABLE_NAME, $condition);
		$update = new CustomProperties();
		$update->add_property("set_id", 0);
		parent::update(self::LES_EXC_TABLE_NAME, $update, "set_id = " . $id);
		$success &= parent::delete(self::LES_EXC_TABLE_NAME, "selection_set_id = 0 AND set_id = 0 AND question_set_id = 0");
		//$success &= RightManager::instance()->delete_location_object_user_right("QuestionSet", $this->manager->get_user()->get_id(), $id);
		return $success;
		/*
		$condition = "set_id = '" . $id . "'";
		return parent::delete_by_id(self::TABLE_NAME,$id) && parent::delete(self::REL_TABLE_NAME, $condition) && parent::delete(self::REL_THEME_TABLE_NAME, $condition);
		*/
	}
	
	public function delete_set_puzzle($set_id, $puzzle_id)
	{
		$condition = "set_id = '" . $set_id . "' AND puzzle_id = '" . $puzzle_id . "'";
		return parent::delete(self::REL_TABLE_NAME,$condition);
	}
	
	public function retrieve_set_from_post()
	{
		$data = array();
		$data["id"] = Request::post('id');
		$data["name"] = addslashes(Request::post('name'));
		$data["theme_ids"] = Request::post('theme_id');
		$data["difficulty_id"] = Request::post('difficulty_id');
		$data["description"] = addslashes(Request::post('description'));
		
		if(is_null($data['name']) || $data['name'] == "")
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->append_message(Language::get_instance()->translate(87));
		}
		if(is_null($data["theme_ids"]) || !is_array($data["theme_ids"]) || empty($data["theme_ids"]))
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->append_message(Language::get_instance()->translate(305));
		}
		if(is_null($data['difficulty_id']) || $data['difficulty_id'] == 0 || !is_numeric($data['difficulty_id']))
		{
			Error::get_instance()->set_result(false);
			Error::get_instance()->append_message(Language::get_instance()->translate(306));
		}
		return new Set($data);
	}
}
