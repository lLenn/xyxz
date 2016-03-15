<?php

require_once Path :: get_path() . 'pages/puzzle/theme/lib/theme.class.php';

class ThemeDataManager extends DataManager
{
	const TABLE_NAME = 'puzzle_theme';
	const CLASS_NAME = 'Theme';
	const LANG_TABLE_NAME = 'language_translation';
	const LANG_SEC_TABLE_NAME = 'language_translation_section';
	
	public static function instance($manager)
	{
		parent::$_instance = new ThemeDataManager($manager);
		return parent::$_instance;
	}
	
	public function retrieve_theme($id)
	{
		return parent::retrieve_by_id(self::TABLE_NAME,self::CLASS_NAME,$id);
	}
	
	public function retrieve_theme_by_name($name)
	{
		$condition = "name = '" . $name . "'";
		return parent::retrieve(self::TABLE_NAME,self::CLASS_NAME,'',self::ONE_RECORD,$condition);
	}
	
	public function retrieve_themes()
	{
		$order = "`order`";
		return parent::retrieve(self::TABLE_NAME,self::CLASS_NAME,$order);
	}
	
	public function insert_theme($theme)
	{
		$result = parent::insert(self::TABLE_NAME, $theme);
		if($result)
		{
			$languages = LanguageDataManager::instance()->retrieve_all_languages();
			$translations = $theme->get_translations();
			foreach($languages as $l)
			{
				$custom = new CustomProperties();
				$custom->add_property("name", $theme->get_name());
				$custom->add_property("language", $l->language);
				$custom->add_property("translation", $translations[$l->language]);
				parent::insert(self::LANG_TABLE_NAME, $custom);
			}
			
			$custom = new CustomProperties();
			$custom->add_property("name", $theme->get_name());
			$custom->add_property("section", Language::THEME);
			parent::insert(self::LANG_SEC_TABLE_NAME, $custom);
		}
		return $result;
	}
	
	public function update_theme($theme)
	{
		return parent::update_by_id(self::TABLE_NAME,$theme);
	}
	
	public function delete_theme($id)
	{		
		$query = "DELETE t.*, tr.*, s.* FROM `" . self::TABLE_NAME . "` as t LEFT JOIN `" . self::LANG_TABLE_NAME . "` as tr ON t.name = tr.name LEFT JOIN `" . self::LANG_SEC_TABLE_NAME . "` as s ON t.name = s.name WHERE t.id = " . $id;
		return parent::$_connection->execute_sql($query,"DELETE");
	}
	
	public function delete_other_themes($filter)
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
			$query = "DELETE t.*, tr.*, s.* FROM `" . self::TABLE_NAME . "` as t LEFT JOIN `" . self::LANG_TABLE_NAME . "` as tr ON t.name = tr.name LEFT JOIN `" . self::LANG_SEC_TABLE_NAME . "` as s ON t.name = s.name WHERE t." . $condition;
			return parent::$_connection->execute_sql($query,"DELETE");
		}
	}
	
	public function count_themes()
	{
		return $this->count(self :: TABLE_NAME);	
	}
	
	public function retrieve_name_from_translations()
	{
		$query = "SELECT max(name) as max FROM " . self::LANG_TABLE_NAME;
		$result = self::$_connection->execute_sql($query, 'O');
		return $result[0]->max + 1;
	}

	public function retrieve_theme_from_post()
	{
		$data = array();
		$data['id'] = 0;
		$data['name'] = $this->retrieve_name_from_translations();
		$data['order'] = $this->count_themes()+1;
		$validation = true;
		$languages = LanguageDataManager::instance()->retrieve_all_languages();
		$translations = array();
		foreach($languages as $l)
		{
			if(!is_null(Request::post('name_' . $l->language)) && Request::post('name_' . $l->language) != "")
				$translations[$l->language] = Request::post('name_' . $l->language);
			else
				$validation = false;
		}
		$data['translations'] = $translations;
		if($validation)
			return new Theme($data);
		else
			return false;
	}

}

?>