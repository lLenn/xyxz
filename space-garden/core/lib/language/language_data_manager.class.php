<?php

class LanguageDataManager extends DataManager
{
	const TABLE_NAME = 'language';
	const TRANS_TABLE_NAME = 'language_translation';
	const SECTION_TABLE_NAME = 'language_translation_section';

	public static function instance()
	{
		parent::$_instance = new LanguageDataManager();
		return parent::$_instance;
	}
	
	public function retrieve_translations_of_section($language, $section)
	{
		$join_array = array();
		$join_array[] = new Join(self::TRANS_TABLE_NAME, 'l', 'name', Join::MAIN_TABLE);
		$join_array[] = new Join(self::SECTION_TABLE_NAME, 's', 'name', 'LEFT JOIN');
		$condition = "l.language = '" . $language . "' AND s.section = '" . $section ."'";
		return parent::retrieve($join_array,null,'', self::MANY_RECORDS, $condition);
	}
	
	public function retrieve_all_languages()
	{
		return parent::retrieve(self::TABLE_NAME, null, '', self::MANY_RECORDS);
	}

}

?>