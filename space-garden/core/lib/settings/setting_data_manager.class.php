<?php

class SettingDataManager extends DataManager
{
	const TABLE_NAME = 'settings';

	public static function instance()
	{
		parent::$_instance = new SettingDataManager();
		return parent::$_instance;
	}
	
	public function retrieve_setting($name)
	{
		$condition = "name = '" . $name . "'";
		return parent::retrieve(self::TABLE_NAME,null,'', self::ONE_RECORD, $condition);
	}

}

?>