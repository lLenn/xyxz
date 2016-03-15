<?php

require_once Path :: get_path() . 'pages/puzzle/lib/puzzle.class.php';
require_once Path :: get_path() . 'pages/puzzle/lib/puzzle_properties.class.php';

class PuzzleDataManager extends DataManager
{
	const PUZZLE_TABLE_NAME = 'puzzle';
	const PUZZLE_CLASS_NAME = 'Puzzle';
	const PUZZLE_COM_TABLE_NAME = 'puzzle_comment';
	const PUZZLE_VAR_TABLE_NAME = 'puzzle_variation';
	const PUZZLE_VAR_COM_TABLE_NAME = 'puzzle_variation_comment';
	//const PUZZLE_SET_TABLE_NAME = 'puzzle_set_relation';
	const LES_PAGE_TABLE_NAME = 'lesson_page';
	const REL_TABLE_NAME = 'puzzle_theme_relation';
	const THEME_TABLE_NAME = 'puzzle_theme';
	const META_TABLE_NAME = 'rights_location_object_creation';
	const RIGHT_TABLE_NAME = 'rights_location_object_user_right';
	const TABLE_NAME = 'puzzle_properties';
	const CLASS_NAME = 'PuzzleProperties';
	const EXC_TABLE_NAME = 'lesson_excercise_components';
	const BASIC_RATING = 1500;
	const PUZZLE_LOCATION_ID = "17";
	
	public static function instance($manager)
	{
		parent::$_instance = new PuzzleDataManager($manager);
		return parent::$_instance;
	}
	
	public function retrieve_puzzle($id)
	{
		return parent::retrieve_by_id('puzzle',null,$id);
	}
	
	public function retrieve_puzzle_properties($id)
	{
		return parent::retrieve_by_id(self::TABLE_NAME,self::CLASS_NAME,$id);
	}
	
	public function retrieve_all_data_from_puzzle($id)
	{
		$puzzle = Array();
		$puzzle["object"] = parent::retrieve_by_id(self::PUZZLE_TABLE_NAME,null,$id);
		$puzzle["puzzle_properties"] = parent::retrieve(self::TABLE_NAME,null,'', self::ONE_RECORD, "puzzle_id = " . $id);
		$join = Array();
		$join[] = new Join(self::REL_TABLE_NAME, "r", "puzzle_id", Join::MAIN_TABLE);
		$join[] = new Join(self::THEME_TABLE_NAME, "t", "id", "LEFT JOIN", Join::MAIN_TABLE, "theme_id");
		$puzzle["object_themes"] = parent::retrieve($join,null,'',self::MANY_RECORDS, "puzzle_id = " . $id);
		$puzzle["object_comments"] = parent::retrieve(self::PUZZLE_COM_TABLE_NAME,null,'',self::MANY_RECORDS, "puzzle_id = " . $id);
		$puzzle["object_variations"] = parent::retrieve(self::PUZZLE_VAR_TABLE_NAME,null,'',self::MANY_RECORDS, "puzzle_id = " . $id);
		$puzzle["object_variation_comments"] = parent::retrieve(self::PUZZLE_VAR_COM_TABLE_NAME,null,'',self::MANY_RECORDS, "puzzle_id = " . $id);
		return $puzzle;
	}
	
	public function retrieve_random_puzzle()
	{
		$rating_user = $this->manager->get_user()->get_chess_profile()->get_rating();
	
		$resultData = StatisticsDataManager::instance(null)->retrieve_statistics_puzzle_by_user_id($this->manager->get_user()->get_id(), time(), (24*60*60*60), "DESC", StatisticsDataManager::GOOD_SCORE);
		$size = count($resultData);
		$condition = "";
		if($size)
		{
			$condition = "`puzzle_id` NOT IN (";
			$i = 1;
			foreach($resultData as $data)
			{
				$condition .= $data->get_puzzle_id();
				if($i<$size)
					$condition .= ",";
				$i++;
			}
			$condition .= ")";
		}
		$diff_rating = 100;
		do
		{
			$resultData2 = $this->retrieve(self::TABLE_NAME, null, "RAND()", self::ONE_RECORD, "valid = 1 AND rating <= " . ($rating_user + $diff_rating) . " AND rating >= " . ($rating_user - $diff_rating) . " " . ($condition!=""?"AND ":"") . $condition,"1");
			$diff_rating += 100;
		}
		while(!($count = count($resultData2)) && $diff_rating <= 500);
		if(!$count)
		{
			$resultData2 = $this->retrieve(self::TABLE_NAME, null, "RAND()", self::ONE_RECORD, "valid = 1 AND rating <= " . ($rating_user + $diff_rating) . " AND rating >= " . ($rating_user - $diff_rating),"1");
		}
		return $this->retrieve_all_data_from_puzzle($resultData2->puzzle_id);
	}
	
	public function retrieve_all_puzzle_properties($right = RightManager::READ_RIGHT, $limit = "", $count = false)
	{
		if(!$this->manager->get_user()->is_admin())
		{
			$join = array();
			$join[] = new Join(self::TABLE_NAME, "p", "puzzle_id", Join::MAIN_TABLE);
			$join[] = new Join(RightDataManager::LOC_OBJ_USER_TABLE_NAME, "urt", "object_id", "LEFT JOIN", Join::MAIN_TABLE, "puzzle_id");
			$conditions = "location_id = " . RightManager::PUZZLE_LOCATION_ID . " AND urt.user_id = " . $this->manager->get_user()->get_id() . " AND urt." . RightDataManager::get_right_conditions($right);
			return parent::retrieve($join, self::CLASS_NAME, '', self::MANY_RECORDS, $conditions, $limit, "*", $count);
		}
		else
		{
			return parent::retrieve(self::TABLE_NAME, self::CLASS_NAME, "puzzle_id", self::MANY_RECORDS, "", $limit, "*", $count);
		}
	}
	
	public function retrieve_puzzles_properties_by_conditions($conditions)
	{
		return parent::retrieve(self::TABLE_NAME,self::CLASS_NAME, '', self::MANY_RECORDS, $conditions);
	}
	
	public function count_puzzles($right = RightManager::READ_RIGHT)
	{
		if(!$this->manager->get_user()->is_admin())
		{
			$conditions = "location_id = " . RightManager::PUZZLE_LOCATION_ID . " AND user_id = " . $this->manager->get_user()->get_id() . " AND " . RightDataManager::get_right_conditions($right);
			return parent::count(RightDataManager::LOC_OBJ_USER_TABLE_NAME, $conditions);
		}
		else
		{
			return parent::count(self::TABLE_NAME);
		}
	}
	
	public function retrieve_all_valid_puzzle_properties()
	{
		$conditions = "valid = '1'";
		if(!$this->manager->get_user()->is_admin())
		{
			$join = array();
			$join[] = new Join(self::TABLE_NAME, "p", "puzzle_id", Join::MAIN_TABLE);
			$join[] = new Join(RightDataManager::LOC_OBJ_USER_TABLE_NAME, "urt", "object_id", "LEFT JOIN", Join::MAIN_TABLE, "puzzle_id");
			$conditions .= " AND location_id = " . RightManager::PUZZLE_LOCATION_ID . " AND urt.user_id = " . $this->manager->get_user()->get_id() . " AND urt." . RightDataManager::get_right_conditions($right);
			return parent::retrieve($join,self::CLASS_NAME, '', self::MANY_RECORDS, $conditions);
		}
		else
		{
			return parent::retrieve(self::TABLE_NAME, self::CLASS_NAME,'',self::MANY_RECORDS,$conditions);
		}
	}
	
	public function retrieve_all_puzzles_without_properties($user)
	{
		$query = "SELECT pz.* FROM `" . self::PUZZLE_TABLE_NAME . "` as pz LEFT JOIN `" . self::TABLE_NAME . "` as pp ON pz.id = pp.puzzle_id " . ($user->is_admin()?"":"LEFT JOIN `" . self::RIGHT_TABLE_NAME . "` as r ON r.object_id = pz.id ") . "WHERE pp.puzzle_id IS NULL" . ($user->is_admin()?"":" AND r.location_id = " . self::PUZZLE_LOCATION_ID . " AND r.user_id = " . $user->get_id() . " AND r.update = 1");
		$objects = parent::retrieve_data($query);
		return $this->Mapping($objects,self::PUZZLE_CLASS_NAME);
	}
	
	public function retrieve_invalid_puzzle_properties()
	{
		$condition = "valid = '0'";
		return parent::retrieve(self::TABLE_NAME,self::CLASS_NAME,'',self::MANY_RECORDS,$condition);
	}
	
	public function retrieve_puzzle_properties_by_puzzle_id($id)
	{
		$condition = "puzzle_id = '" . $id . "'";
		return parent::retrieve(self::TABLE_NAME,self::CLASS_NAME,'',self :: ONE_RECORD, $condition);
	}
	
	public function retrieve_dubble_puzzles()
	{
		$query = "SELECT DISTINCT p1.* FROM `" . self::PUZZLE_TABLE_NAME . "` as p1 LEFT JOIN `" . self::PUZZLE_TABLE_NAME . "` as p2 ON p1.fen = p2.fen WHERE (p1.moves LIKE CONCAT(p2.moves, '%') OR p2.moves LIKE CONCAT(p1.moves, '%')) AND p1.id <> p2.id ORDER BY fen";
		return parent::Mapping(parent::retrieve_data($query), self::PUZZLE_CLASS_NAME);
	}
	
	public function count_puzzle_comments_by_puzzle_id($puzzle_id)
	{
		return parent::count(self::PUZZLE_COM_TABLE_NAME, "puzzle_id = '" . $puzzle_id . "'");
	}
	
	public function count_puzzle_variations_by_puzzle_id($puzzle_id)
	{
		return parent::count(self::PUZZLE_VAR_TABLE_NAME, "puzzle_id = '" . $puzzle_id . "'");
	}
	
	public function count_puzzle_variation_comments_by_puzzle_id($puzzle_id)
	{
		return parent::count(self::PUZZLE_VAR_COM_TABLE_NAME, "puzzle_id = '" . $puzzle_id . "'");
	}
	
	public function replace_puzzle_id($old_puzzle_id, $new_puzzle_id)
	{
		require_once Path::get_path() . 'pages/lesson/lesson_excercise/lib/lesson_excercise_component.class.php';
		$objects = parent::retrieve(self::EXC_TABLE_NAME, null, '', self::MANY_RECORDS, "type_object_id = " . $new_puzzle_id . " AND type_id = " . LessonExcerciseComponent::PUZZLE_TYPE);
		// In case the old and new puzzle id are in the same set.
		$set_conditions = "";
		if(!is_null($objects) && !empty($objects))
		{
			$size = count($objects);
			$i = 1;
			
			$set_conditions .= "excercise_id NOT IN (";
			foreach($objects as $set_rel)
			{
				$set_conditions .= "'".$set_rel->excercise_id."'";
				if($i < $size)
					$set_conditions .= ", ";
				$i++;
			}
			$set_conditions .= ")";
		}
		$update_object = new CustomProperties();
		$update_object->add_property("type_object_id", $new_puzzle_id);
		$condition = ($set_conditions!=""?$set_conditions . " AND ":"") . "type_object_id = " . $old_puzzle_id . " AND type_id = " . LessonExcerciseComponent::PUZZLE_TYPE;
		$success = parent::update(self::EXC_TABLE_NAME, $update_object, $condition);

		$objects = parent::retrieve(self::LES_PAGE_TABLE_NAME, null, '', self::MANY_RECORDS, "type_object_id = " . $new_puzzle_id . " AND type = 2");
		$page_conditions = "";
		if(!is_null($objects) && !empty($objects))
		{
			$size = count($objects);
			$i = 1;
			
			$page_conditions .= "page_id NOT IN (";
			foreach($objects as $page_rel)
			{
				$page_conditions .= "'".$page_rel->lesson_id."'";
				if($i < $size)
					$page_conditions .= ", ";
				$i++;
			}
			$page_conditions .= ")";
		}
		$update_object->remove_property("puzzle_id");
		$update_object->add_property("type_object_id", $new_puzzle_id);
		$condition = ($page_conditions!=""?$page_conditions . " AND ":"") . "type = 2 AND type_object_id = " . $old_puzzle_id;
		$success &= parent::update(self::LES_PAGE_TABLE_NAME, $update_object, $condition);
		$success &= parent::delete(self::LES_PAGE_TABLE_NAME, "type = 2 AND type_object_id = " . $old_puzzle_id);
		$success &= parent::delete(self::EXC_TABLE_NAME, "type_object_id = " . $old_puzzle_id . " AND type_id = " . LessonExcerciseComponent::PUZZLE_TYPE);
		return $success;
	}
	
	public function retrieve_puzzle_themes($puzzle_id)
	{
		$condition = "puzzle_id = '" . $puzzle_id . "'";
		$result_ids = parent::retrieve(self::REL_TABLE_NAME, null, '', self::MANY_RECORDS, $condition);
		$theme_ids = array();
		foreach($result_ids as $id)
			$theme_ids[] = $id->theme_id;
		return $theme_ids;
	}

	public function insert_puzzle_properties($puzzle)
	{
		$id = parent::insert(self::TABLE_NAME,$puzzle);
		if($id)
		{
			$query = 'INSERT INTO `' . self::REL_TABLE_NAME . '` ( puzzle_id, theme_id ) VALUES';
			$i = 1;
			$size = count($puzzle->get_theme_ids());
			foreach($puzzle->get_theme_ids() as $theme_id)
			{
				$query .= "('".$puzzle->get_puzzle_id()."', '".$theme_id."')";
				if($i<$size)
					$query .= ", ";
				$i++;
			}
			
			$query .= ';';
			self::$_connection->execute_sql($query,'INSERT');
		}
		return $id;
	}

	public function update_puzzle_properties($puzzle)
	{
		$conditions = 'id="'.$puzzle->get_id().'"';
		$success = parent::update(self::TABLE_NAME,$puzzle,$conditions);
		if($success)
		{
			$conditions = "puzzle_id = '". $puzzle->get_puzzle_id()."'"; 
			parent::delete(self::REL_TABLE_NAME, $conditions);
			
			$query = 'INSERT INTO `'.self::REL_TABLE_NAME.'` (puzzle_id, theme_id) VALUES';
			$i = 1;
			$size = count($puzzle->get_theme_ids());
			foreach($puzzle->get_theme_ids() as $theme_id)
			{
				$query .= "('".$puzzle->get_puzzle_id()."', '".$theme_id."')";
				if($i<$size)
					$query .= ", ";
				$i++;
			}
			
			$query .= ';';
			self::$_connection->execute_sql($query,'INSERT');
		}
		return $success;
	}
	
	public function update_invalid_puzzle_properties_to_valid($puzzle_id)
	{
		$query = 'UPDATE `' . self::TABLE_NAME . '` SET valid = 1 WHERE puzzle_id = "'.$puzzle_id.'"';
		return self::$_connection->execute_sql($query,'UPDATE');
	}
    
	public function delete_puzzle($id)
	{
		RightManager::instance()->delete_location_object_user_rights(self::PUZZLE_LOCATION_ID, $id);
		return parent::delete(self::PUZZLE_TABLE_NAME, "id = '" . $id . "'") &&
			   parent::delete(self::PUZZLE_COM_TABLE_NAME, "puzzle_id = '" . $id . "'") &&
			   parent::delete(self::PUZZLE_VAR_TABLE_NAME, "puzzle_id = '" . $id . "'") &&
			   parent::delete(self::PUZZLE_VAR_COM_TABLE_NAME, "puzzle_id = '" . $id . "'") &&
			   parent::delete(self::EXC_TABLE_NAME, "puzzle_id = '" . $id . "'") &&
			   parent::delete(self::LES_PAGE_TABLE_NAME, "type = '2' AND type_object_id = '" . $id . "'") &&
			   $this->delete_puzzle_properties_by_puzzle_id($id);
	}
	
	public function delete_puzzle_properties_by_puzzle_id($id)
	{
		return parent::delete(self::TABLE_NAME,"puzzle_id = '" . $id . "'") && parent::delete(self::REL_TABLE_NAME, "puzzle_id = '" . $id . "'");
	}
	
	public function retrieve_puzzle_moves($id)
	{
	    $query = "SELECT moves FROM `puzzle` WHERE id = '" . $id . "'";
		$object = $this->retrieve_data($query);
		if(is_object($object[0]))
			return $object[0]->moves;
		else
			return "";
	}
	
	public function retrieve_invalid_puzzle_properties_with_search_form($right = RightManager::UPDATE_RIGHT)
	{
		$conditions = "valid = '0'";
		return $this->retrieve_puzzle_properties_with_search_form($conditions, $right);
	}
	
	public function retrieve_valid_puzzle_properties_with_search_form($right = RightManager::READ_RIGHT)
	{
		$conditions = "valid = '1'";
		return $this->retrieve_puzzle_properties_with_search_form($conditions, $right);
	}
	
	public function retrieve_puzzle_properties_with_search_form($conditions = "", $right = RightManager::READ_RIGHT, $limit = "", $shop = false, $filter = false, $count = false, $undo = false)
	{
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
		if(Request :: post('difficulty_id') != 0 && is_numeric(Request :: post('difficulty_id')))
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
		if(Request :: post('min_nom') != "" && is_numeric(Request :: post('min_nom')))
		{
			if($conditions != "")
			{
				$conditions .= " AND ";
			}
			$conditions .= "number_of_moves >= '" . Request :: post('min_nom') ."'";
		}
		if(Request :: post('max_nom') != "" && is_numeric(Request :: post('max_nom')))
		{
			if($conditions != "")
			{
				$conditions .= " AND ";
			}
			$conditions .= "number_of_moves <= '" . Request :: post('max_nom') ."'";
		}
		$sqlSelect = "SELECT DISTINCT(p.puzzle_id) as dp, p.*";
		$sqlString = " FROM `".self::TABLE_NAME."` as p " .
						"LEFT JOIN `".self::REL_TABLE_NAME."` as t ON p.puzzle_id = t.puzzle_id";
		$from_date = Calendar::get_date_timestamp_from_form("from_date");
		$to_date = Calendar::get_date_timestamp_from_form("to_date");
		if(!is_null($from_date) && !is_null($to_date))
		{
			$sqlString .= " LEFT JOIN `".self::META_TABLE_NAME."` as m ON m.object_id = p.puzzle_id";
			if($conditions != "")
			{
				$conditions .= " AND ";
			}
			$conditions .= "location_id = 17 AND creation_time >= " . $from_date ." AND creation_time <= " . ( $to_date + 24 * 60 * 60 );
		}
		if(!$this->manager->get_user()->is_admin() && $right != RightManager::NO_RIGHT)
		{
			$sqlString .= " LEFT JOIN `" . RightDataManager::LOC_OBJ_USER_TABLE_NAME . "` as urt ON urt.object_id = p.puzzle_id";
			if($conditions != "")
			{
				$conditions .= " AND ";
			}
			$conditions .= "location_id = " . RightManager::PUZZLE_LOCATION_ID . " AND urt.user_id = " . $this->manager->get_user()->get_id() . " AND urt." . RightDataManager::get_right_conditions($right);
		}
		if($right == RightManager::NO_RIGHT)
		{
			if($shop)
			{
				$sqlString .= " LEFT JOIN `shop_meta_data` as sm ON sm.object_id = p.puzzle_id AND sm.location_id = " . RightManager::PUZZLE_LOCATION_ID;
				if(!is_null(Request :: post('user')) != "" && Request::post('user') != "")
				{
					$words = preg_split("/ /", Request :: post('user'));
					$count = count($words);
					$i = 1;
					if($count)
					{
						$sqlString .= " LEFT JOIN `" . RightDataManager::LOC_OBJ_CRT_TABLE_NAME . "` as ct on p.puzzle_id = ct.object_id AND ct.location_id = " . RightManager::PUZZLE_LOCATION_ID;
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
			$conditions .= (!$filter?"p.puzzle_id NOT IN (SELECT object_id FROM `" . RightDataManager::LOC_OBJ_USER_TABLE_NAME . "` WHERE location_id = " . RightManager::PUZZLE_LOCATION_ID . " AND user_id = " . $this->manager->get_user()->get_id() . ") ".($shop?"AND":""):"") . ($shop?" sm.valid" . ($filter?(!$undo?" IS NULL":(is_null(Request::post("shop_valid")) || !is_numeric(Request::post("shop_valid"))?" IN (0, 1)":" = " . Request::post("shop_valid"))):" = 1"):"");
		}
		if($shop && !$undo)
		{
			if($conditions != "")
			{
				$conditions .= " AND ";
			}
			$conditions .= "p.puzzle_id NOT IN (SELECT object_id FROM `shop` WHERE location_id = " . RightManager::PUZZLE_LOCATION_ID . " AND user_id = " . $this->manager->get_user()->get_id() . ")";
		}
		$sqlString = $sqlSelect . $sqlString;
		if($conditions != "")
		{
			$sqlString .= " WHERE " . $conditions;
		}
		if($limit!="")
		{
			$sqlString .= " LIMIT " . $limit;
		}
		if(!$count)
		{
			$objects = $this->retrieve_data($sqlString);
			$results = $this->Mapping($objects,self::CLASS_NAME);
		}
		else
		{
			$results = self::$_connection->execute_sql($sqlString,'COUNTROWS');
		}
		return $results;
	}
	
	public function retrieve_puzzle_properties_from_post()
	{
		$data = array();
		$data["id"] = Request::post('id');
		$data["puzzle_id"] = Request::get('id');
		$data["theme_ids"] = Request::post('theme_id');
		$data["rating"] = intval(Request::post('rating'));
		$data["number_of_moves"] = strlen($this->retrieve_puzzle_moves($data["puzzle_id"]))/5;
		$data["comment"] = Request::post('comment');
		if(!is_null($data["rating"]) && is_numeric($data["rating"]) && $data["rating"] >= 1000 && $data["rating"] <= 2500 &&
		   !is_null($data["theme_ids"]) && is_array($data["theme_ids"]) && !empty($data["theme_ids"]))
			return new PuzzleProperties($data);
		else
			return false;
	}
	
	public function register_new_ratings($puzzle_id, $score, $time_left, $total_moves, $set_id = null, $attempt = null)
	{

		$rating_user = $this->manager->get_user()->get_chess_profile()->get_rating();
		$rd_user = $this->manager->get_user()->get_chess_profile()->get_rd();
		$rating_puzzle = $this->retrieve_puzzle_properties_by_puzzle_id($puzzle_id)->get_rating();
	
		$logins_user = StatisticsDataManager::instance(null)->retrieve_statistics_from_login_by_user_id($this->manager->get_user()->get_id());
		$number_exc = count($logins_user);
		$last_login = floor(time()/(60*60*24));
		foreach ($logins_user as $login)
		{
			if(floor($login->get_login_time()/(60*60*24)) != $last_login)
			{
				$last_login = $login->get_login_time();
				break;
			}
		}
		if($number_exc > 0)
			$first_login = $logins_user[$number_exc-1]->get_login_time();
		else
			$first_login = $last_login - (60*60*24);
		
		$this->manager->calculate_new_rating_and_rd($rating_user, $rd_user, $first_login, $last_login, $number_exc, $rating_puzzle, $score);
	
		if($this->manager->get_user()->get_group_id() != 11)
		{
			$validation = false;
			if($score && !is_null($set_id) && is_numeric($set_id) && $set_id>0)
			{
				$stats = StatisticsDataManager::instance(null)->retrieve_statistics_puzzle_by_condition("user_id = " . $this->manager->get_user()->get_id() . " AND puzzle_id = " . $puzzle_id . " AND score = 1 AND set_id = " . $set_id . " AND score_modified = 1", "`time` DESC", 4);
				$year_ago = time() - (365*24*3600);
				if(count($stats) == 4)
				{
					foreach($stats as $stat)
					{
						if($stat->get_time() < $year_ago)
							$validation = true;
					}
				}
				else
					$validation = true;
			}
			else
			{
				$validation = true;
			}
				
			if($validation)
			{
				$sqlString = "INSERT INTO `statistics_puzzle` (user_id, puzzle_id, `time`, time_left, user_rating, user_rd, puzzle_rating, score, total_moves, set_id, set_attempt) VALUES ('" . $this->manager->get_user()->get_id() . "', '" . $puzzle_id . "', '" . time() . "', '" . $time_left . "' , '" . $rating_user . "', '" . $rd_user . "', '" . $rating_puzzle . "', '" . $score . "', '" . $total_moves . "', '" . $set_id . "', '" . $attempt . "')";
				self::$_connection->execute_sql($sqlString,'INSERT');
				$sqlString = "UPDATE `user_chess_profile` SET rating = '" . $rating_user . "', rd = '" . $rd_user . "' WHERE user_id = '" . $this->manager->get_user()->get_id() . "'";
				self::$_connection->execute_sql($sqlString,'UPDATE');
				$sqlString = "UPDATE `puzzle_properties` SET rating = '" . $rating_puzzle . "' WHERE puzzle_id = '" . $puzzle_id . "'";
				self::$_connection->execute_sql($sqlString,'UPDATE');
			}
			else
			{
				$sqlString = "INSERT INTO `statistics_puzzle` (user_id, puzzle_id, `time`, time_left, user_rating, user_rd, puzzle_rating, score, total_moves, set_id, set_attempt, score_modified) VALUES ('" . $this->manager->get_user()->get_id() . "', '" . $puzzle_id . "', '" . time() . "', '" . $time_left . "' , '" . $rating_user . "', '" . $rd_user . "', '" . $rating_puzzle . "', '" . $score . "', '" . $total_moves . "', '" . $set_id . "', '" . $attempt . "', 0)";
				self::$_connection->execute_sql($sqlString,'INSERT');
			}
		}
		else
		{
			$statistics_puzzle = null;
			if(isset($_SESSION["statistics_puzzle"]))
				$statistics_puzzle = unserialize($_SESSION["statistics_puzzle"]);
			if(is_null($statistics_puzzle) || !is_array($statistics_puzzle))
			{
				$statistics_puzzle = array();
			}
			$statistics_puzzle[] = array("user_id" => $this->manager->get_user()->get_id(), "puzzle_id" => $puzzle_id, "score" => $score, "set_id" => $set_id, "set_attempt" => $attempt);
			$_SESSION["statistics_puzzle"] = serialize($statistics_puzzle);
		}
	}
}
