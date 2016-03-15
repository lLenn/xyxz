<?php

require_once Path :: get_path() . 'pages/puzzle/difficulty/lib/difficulty.class.php';

class DifficultyDataManager extends DataManager
{
	const TABLE_NAME = 'puzzle_difficulty';
	const CLASS_NAME = 'Difficulty';
	const LANG_TABLE_NAME = 'language_translation';
	const LANG_SEC_TABLE_NAME = 'language_translation_section';
	
	public static function instance($manager)
	{
		parent::$_instance = new DifficultyDataManager($manager);
		return parent::$_instance;
	}
	
	public function retrieve_difficulty($id)
	{
		$difficulties = $this->retrieve_difficulties();
		return $difficulties[$id];
		//return parent::retrieve_by_id(self::TABLE_NAME,self::CLASS_NAME,$id);
	}
	
	public function retrieve_difficulty_by_name($name)
	{
		$condition = "name_male = '" . $name . "'";
		return parent::retrieve(self::TABLE_NAME,self::CLASS_NAME,'',self::ONE_RECORD,$condition);
	}
	
	public function retrieve_difficulties()
	{
		$difficulties = array();
		$difficulties[1] = new Difficulty(array("id" => 1, "bottom_rating" => null, "top_rating" => 499, "name_male" => 726, "name_female" => 726, "order" => 1));
		$difficulties[2] = new Difficulty(array("id" => 2, "bottom_rating" => 500, "top_rating" => 799, "name_male" => 730, "name_female" => 730, "order" => 2));
		$difficulties[3] = new Difficulty(array("id" => 3, "bottom_rating" => 800, "top_rating" => 899, "name_male" => 725, "name_female" => 725, "order" => 3));
		$difficulties[4] = new Difficulty(array("id" => 4, "bottom_rating" => 900, "top_rating" => 999, "name_male" => 727, "name_female" => 727, "order" => 4));
		$difficulties[5] = new Difficulty(array("id" => 5, "bottom_rating" => 1000, "top_rating" => 1099, "name_male" => 731, "name_female" => 731, "order" => 5));
		$difficulties[6] = new Difficulty(array("id" => 6, "bottom_rating" => 1100, "top_rating" => 1299, "name_male" => 735, "name_female" => 735, "order" => 6));
		$difficulties[7] = new Difficulty(array("id" => 7, "bottom_rating" => 1300, "top_rating" => 1399, "name_male" => 738, "name_female" => 738, "order" => 7));
		$difficulties[8] = new Difficulty(array("id" => 8, "bottom_rating" => 1400, "top_rating" => 1499, "name_male" => 732, "name_female" => 732, "order" => 8));
		$difficulties[9] = new Difficulty(array("id" => 9, "bottom_rating" => 1500, "top_rating" => 1599, "name_male" => 724, "name_female" => 724, "order" => 9));
		$difficulties[10] = new Difficulty(array("id" => 10, "bottom_rating" => 1600, "top_rating" => 1699, "name_male" => 728, "name_female" => 728, "order" => 10));
		$difficulties[11] = new Difficulty(array("id" => 11, "bottom_rating" => 1700, "top_rating" => 1799, "name_male" => 733, "name_female" => 733, "order" => 11));
		$difficulties[12] = new Difficulty(array("id" => 12, "bottom_rating" => 1800, "top_rating" => 1899, "name_male" => 736, "name_female" => 736, "order" => 12));
		$difficulties[13] = new Difficulty(array("id" => 13, "bottom_rating" => 1900, "top_rating" => 1999, "name_male" => 729, "name_female" => 729, "order" => 13));
		$difficulties[14] = new Difficulty(array("id" => 14, "bottom_rating" => 2000, "top_rating" => null, "name_male" => 1147, "name_female" => 1147, "order" => 14));
		return $difficulties;
		/*
		$order = "`order`";
		return parent::retrieve(self::TABLE_NAME,self::CLASS_NAME,$order);
		*/
	}
	
	public function insert_difficulty($difficulty)
	{
		$result = parent::insert(self::TABLE_NAME,$difficulty);
		if($result)
		{
			$languages = LanguageDataManager::instance()->retrieve_all_languages();
			$translations = $difficulty->get_translations();
			foreach($languages as $l)
			{
				$custom = new CustomProperties();
				$custom->add_property("name", $difficulty->get_name_male());
				$custom->add_property("language", $l->language);
				$custom->add_property("translation", $translations["male_" . $l->language]);
				parent::insert(self::LANG_TABLE_NAME, $custom);
				
				if($difficulty->get_name_male() != $difficulty->get_name_female())
				{
					$custom = new CustomProperties();
					$custom->add_property("name", $difficulty->get_name_female());
					$custom->add_property("language", $l->language);
					$custom->add_property("translation", $translations["female_" . $l->language]);
					parent::insert(self::LANG_TABLE_NAME, $custom);
				}
			}
			
			$custom = new CustomProperties();
			$custom->add_property("name", $difficulty->get_name_male());
			$custom->add_property("section", Language::DIFFICULTY);
			parent::insert(self::LANG_SEC_TABLE_NAME, $custom);
		
			if($difficulty->get_name_male() != $difficulty->get_name_female())
			{
				$custom = new CustomProperties();
				$custom->add_property("name", $difficulty->get_name_female());
				$custom->add_property("section", Language::DIFFICULTY);
				parent::insert(self::LANG_SEC_TABLE_NAME, $custom);
			}
		}
		return $result;
	}
	
	public function update_difficulty($difficulty)
	{
		return parent::update_by_id(self::TABLE_NAME,$difficulty);
	}
	
	public function delete_difficulty($id)
	{
		$difficulty = $this->retrieve_difficulty($id);
		$query = "DELETE d.* FROM `" . self::TABLE_NAME . "` WHERE id = " . $id;
		$success = parent::$_connection->execute_sql($query,"DELETE");
		$query = "DELETE t.*, s.* FROM `" . self::LANG_TABLE_NAME . "` as t LEFT JOIN `" . self::LANG_SEC_TABLE_NAME . "` as s ON t.name = s.name WHERE t.name = " . $difficulyt->get_name_male();
		parent::$_connection->execute_sql($query,"DELETE");
		if($difficulty->get_name_male() != $difficulty->get_name_female())
		{
			$query = "DELETE t.*, s.* FROM `" . self::LANG_TABLE_NAME . "` as t LEFT JOIN `" . self::LANG_SEC_TABLE_NAME . "` as s ON t.name = s.name WHERE t.name = " . $difficulyt->get_name_male();
			parent::$_connection->execute_sql($query,"DELETE");
		}
		return $success;
	}
	
	public function delete_other_difficulties($filter)
	{
		$size = count($filter);
		if($size)
		{
			$condition = "`id` NOT IN (";
			$i = 1;
			foreach($filter as $id)
			{
				$condition .= "'" . $id . "'";
				if($i<$size)
					$condition .= ",";
				$i++;
			}
			$condition .= ")";
			$query = "DELETE d.*, t.*, s.* FROM `" . self::TABLE_NAME . "` as d LEFT JOIN `" . self::LANG_TABLE_NAME . "` as t ON (t.name = d.name_male OR t.name = d.name_female) LEFT JOIN `" . self::LANG_SEC_TABLE_NAME . "` as s ON t.name = s.name WHERE d." . $condition;
			return parent::$_connection->execute_sql($query,"DELETE");
		}
	}
	
	public function count_difficulties()
	{
		return $this->count(self :: TABLE_NAME);	
	}
	
	public function retrieve_name_from_translations()
	{
		$query = "SELECT max(name) as max FROM " . self::LANG_TABLE_NAME;
		$result = self::$_connection->execute_sql($query, 'O');
		return $result[0]->max + 1;
	}
	
	public function retrieve_difficulty_from_post()
	{
		$data = array();
		$data['id'] = 0;
		$data['name_male'] = $this->retrieve_name_from_translations();
		if(!parent::parse_checkbox_value(Request::post("idem_female")))
			$data['name_female'] = $data['name_male']++;
		else
			$data['name_female'] = $data['name_male'];
		
		$data['order'] = $this->count_difficulties()+1;
		$validation = true;
		$languages = LanguageDataManager::instance()->retrieve_all_languages();
		$translations = array();
		foreach($languages as $l)
		{
			if(!is_null(Request::post('name_male_' . $l->language)) && Request::post('name_male_' . $l->language) != "")
			{
				$translations["male_" . $l->language] = Request::post('name_male_' . $l->language);
				if(!parent::parse_checkbox_value(Request::post("idem_female")) && !is_null(Request::post('name_female_' . $l->language)) && Request::post('name_female_' . $l->language) != "")
					$translations["female_" . $l->language] = Request::post('name_female_' . $l->language);
				else if(!parent::parse_checkbox_value(Request::post("idem_female")))
					$validation = false;
			}
			else
				$validation = false;
		}
		$data['translations'] = $translations;
		if($validation)
			return new Difficulty($data);
		else
			return false;
	}
	
	public function retrieve_difficulty_by_rating($rating)
	{
		$difficulties = self::retrieve_difficulties();
		foreach($difficulties as $difficulty)
		{
			if(is_null($difficulty->get_bottom_rating()) && $rating <= $difficulty->get_top_rating())
				return $difficulty;
			elseif(is_null($difficulty->get_top_rating()) && $rating >= $difficulty->get_bottom_rating())
				return $difficulty;
			elseif($difficulty->get_bottom_rating() <= $rating && $difficulty->get_top_rating() >= $rating)
				return $difficulty;
		}
	}
	
	public function retrieve_difficulty_cluster_by_rating($rating)
	{
		$difficulty = $this->retrieve_difficulty_by_rating($rating);
		switch($difficulty->get_id())
		{
			case 1:
			case 2:
			case 3:
			case 4: return new Difficulty(array("id" => 15, "bottom_rating" => null, "top_rating" => 999, "name_male" => 1153, "name_female" => 1153, "order" => null)); break;
			case 5:
			case 6:
			case 7:
			case 8: return new Difficulty(array("id" => 16, "bottom_rating" => 1000, "top_rating" => 1499, "name_male" => 1154, "name_female" => 1154, "order" => null)); break;
			case 9:
			case 10:
			case 11:
			case 12:
			case 13:
			case 14: return new Difficulty(array("id" => 17, "bottom_rating" => 1500, "top_rating" => null, "name_male" => 1155, "name_female" => 1155, "order" => null)); break;
		}
	}
	
	public function retrieve_difficulty_clusters()
	{
		$clusters = array();
		$clusters[] = new Difficulty(array("id" => 15, "bottom_rating" => null, "top_rating" => 999, "name_male" => 1153, "name_female" => 1153, "order" => null));
		$clusters[] = new Difficulty(array("id" => 16, "bottom_rating" => 1000, "top_rating" => 1499, "name_male" => 1154, "name_female" => 1154, "order" => null));
		$clusters[] = new Difficulty(array("id" => 17, "bottom_rating" => 1500, "top_rating" => null, "name_male" => 1155, "name_female" => 1155, "order" => null));
		return $clusters;
	}
	
	public function are_in_same_cluster($rating_a, $rating_b)
	{
		$cluster_a = $this->retrieve_difficulty_cluster_by_rating($rating_a);
		$cluster_b = $this->retrieve_difficulty_cluster_by_rating($rating_b);
		return $cluster_a->get_id() == $cluster_b->get_id();
	}
}

?>