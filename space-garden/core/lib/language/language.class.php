<?php
/*
 * class Language: 
 * 		Singleton pattern
 * 		Manages the translations of the site.
 */

require_once Path :: get_path() . 'core/lib/language/language_data_manager.class.php';
require_once Path :: get_path() . 'core/lib/language/language_renderer.class.php';

class Language
{
    const GENERAL = 0;
    const PRODUCT = 1;
    const DYNAMIC_PAGE = 2;
    const CLIENT = 3;
    
    private static $instance;
    private $translations;
    private $language;

    private function Language()
    {
        if (!is_null(Request::get("language")))
        {
        	$this->language = Request::get("language");
        	Session::register("language", Request::get("language"));
        }
        elseif(!is_null(Session::retrieve("language")))
        {
        	$this->language = Session::retrieve("language");
        }
        else
        {
            $this->language = Setting::get_instance()->get_default_setting("language");
        }
        $this->translations = array();
    }

    static function get_instance()
    {
        if (! isset(self :: $instance))
        {
            self :: $instance = new self();
        }
        return self :: $instance;
    }

    function get_language() { return $this->language; }
    function set_language($language) { $this->language = $language; }

    /*
     * function translate($name)
     * 		Gets the translation of the name from the translations array.
     * 		Throws error if name is not set.
     * Return: the translation of the name.
     */
    function translate($name)
    {
    	if(isset($this->translations[$name]))
    		return $this->translations[$name];
    	elseif(Setting::get_instance()->get_default_setting("fault_level") == "strict")
    		throw new Exception("The translation for " . $name . " in " . $this->language . " doesn't exist.");
    	else
    		return $name;
    }

    /*
     * function add_section_to_translations($section)
     * 		Adds the translations of the given section to the translations array.
     */
    function add_section_to_translations($section)
    {
        $translation_objects = LanguageDataManager::instance()->retrieve_translations_of_section($this->language, $section);
        foreach($translation_objects as $translation)
        {
        	if(!isset($this->translations[$translation->name]))
        		$this->translations[$translation->name] = $translation->translation;
        }
    }
}
?>