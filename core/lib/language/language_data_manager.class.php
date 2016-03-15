<?php

class LanguageDataManager extends DataManager
{
	const TABLE_NAME = 'language';
	const TRANS_TABLE_NAME = 'language_translation';
	const SECTION_TABLE_NAME = 'language_translation_section';

	public static function instance()
	{
		parent::$_instance = new LanguageDataManager(null);
		return parent::$_instance;
	}
	
	public function retrieve_translations_of_section($language, $section)
	{
		$join_array = array();
		$join_array[] = new Join(self::TRANS_TABLE_NAME, 'l', 'name', Join::MAIN_TABLE);
		$join_array[] = new Join(self::SECTION_TABLE_NAME, 's', 'name', 'LEFT JOIN', Join::MAIN_TABLE, 'name');
		$condition = "l.language = '" . $language . "' AND s.section = '" . $section ."'";
		return parent::retrieve($join_array,null,'', self::MANY_RECORDS, $condition);
	}
	
	public function retrieve_specific_translation($name, $language)
	{
		$condition = "language = '" . $language . "' AND name = '" . $name . "'";
		return parent::retrieve(self::TRANS_TABLE_NAME, null, '', self::ONE_RECORD, $condition);
	}
	

	public function retrieve_translations($name, $exclude = array())
	{
		$condition = "name = '" . $name . "'";
		if(!empty($exclude))
		{
			$extra_cond = "language NOT IN (";
			foreach($exclude as $lang)
			{
				$extra_cond .= "'" . $lang . "', ";
			}
			$extra_cond = substr($extra_cond, 0, -2) . ")";
			$condition .= " AND " . $extra_cond;
		}
		return parent::retrieve(self::TRANS_TABLE_NAME, null, '', self::MANY_RECORDS, $condition);
	}
	
	public function retrieve_all_languages()
	{
		return parent::retrieve(self::TABLE_NAME, null, '', self::MANY_RECORDS);
	}

	public function retrieve_search_translations($language, $limit = 20, $start = 0, $keywords = array())
	{
		$condition = $this->get_condition_search_translations($language, $keywords, true);
		if(empty($keywords))
			return parent::retrieve(self::TRANS_TABLE_NAME,null,'', self::MANY_RECORDS, $condition, $start . ',' . ($start + $limit));
		else
		{
			$join = array();
			$join[] = new Join(self::TRANS_TABLE_NAME, "t", "name", Join::MAIN_TABLE);
			$join[] = new Join(self::TRANS_TABLE_NAME, "t2", "name", "LEFT JOIN", Join::MAIN_TABLE, "name");
			$condition .= " AND t2.language = 'NL'";
			return parent::retrieve($join,null,'', self::MANY_RECORDS, $condition, $start . ',' . ($start + $limit), 't.language as language, t.name as name, t2.translation as translation, t.translation as search');
		}
	}
	
	public function count_search_translations($language, $keywords = array())
	{
		$condition = $this->get_condition_search_translations($language, $keywords);
		$result = parent::retrieve(self::TRANS_TABLE_NAME,null,'', self::ONE_RECORD, $condition, '', 'count(*) as count');
		return $result->count;
	}
	
	private function get_condition_search_translations($language, $keywords = array(), $join = false)
	{
		if(empty($keywords))
			return "language = 'NL' AND name NOT IN(SELECT name FROM `" . self::TRANS_TABLE_NAME. "` WHERE language = '" . $language . "')";
		else
		{
			$condition = ($join?"t.":"") . "language = '" . $language . "' AND (";
			foreach ($keywords as $word)
			{
				$condition .= ($join?"t.":"") . "translation LIKE '%" . $word . "%' OR ";
			}
			return substr($condition, 0, -4) . ")";
		}
	}
	
	public function insert_translation($name, $language, $translation)
	{
		$cp = new CustomProperties();
		$cp->add_property("name", $name);
		$cp->add_property("language", $language);
		$cp->add_property("translation", $translation);
		parent::insert(self::TRANS_TABLE_NAME, $cp);
		return Error::get_instance()->get_result();
	}
	
	public function update_translation($name, $language, $translation)
	{
		$cp = new CustomProperties();
		$cp->add_property("translation", $translation);
		parent::update(self::TRANS_TABLE_NAME, $cp, "name = '" . $name . "' AND language = '" . $language . "'");
		return Error::get_instance()->get_result();
	}

	public function delete_translation($name, $language)
	{
		parent::delete(self::TRANS_TABLE_NAME, "name = '" . $name . "' AND language = '" . $language . "'");
		return Error::get_instance()->get_result();
	}

}

?>