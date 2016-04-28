<?php

require_once Path :: get_path() . 'core/lib/settings/setting_data_manager.class.php';

class Setting
{
    private static $instance;
    private $default_settings;

    private function Setting()
    {
    	require_once Path::get_path() . "core/lib/settings/default_settings.inc.php";
    	$this->default_settings = $default_settings;
    }

    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }
    
    public function get_default_setting($value)
    {
    	if(is_array($this->default_settings) && isset($this->default_settings[$value]))
    		return $this->default_settings[$value];
    	else
    		throw new Exception("Default setting doesn't exist!<br>" . debug_backtrace());
    }
    
    public function get_setting($name)
    {
    	$setting = SettingDataManager::instance()->retrieve_setting($name);
    	if(!is_null($setting) && isset($setting->name) && isset($setting->value))
    		return $setting->value;
    	else
    		throw new Exception("Setting doesn't exist!<br>" . debug_backtrace());
    }
}
?>