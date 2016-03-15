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
	const ARTICLE = 1;
	const GAME = 2;
	const GROUP = 3;
	const HELP = 4;
	const LESSON = 5;
	const PUZZLE = 6;
	const QUESTION = 7;
	const RIGHT = 8;
	const STATISTICS = 9;
	const USER = 10;
	const VIDEO = 11;
	const FLASH = 12;
	const THEME = 13;
	const DIFFICULTY = 14;
	const SELECTION = 15;
	const MESSAGE = 16;
	const NEWS = 17;
	
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
        elseif (!is_null(Request::post("language")))
        {
        	$this->language = Request::post("language");
        	Session::register("language", Request::post("language"));
        }
        elseif(!is_null(Session::retrieve("language")))
        {
        	$this->language = Session::retrieve("language");
        }
        else
        {
            $this->language = "NL"; //strtoupper(substr(Request::server('HTTP_ACCEPT_LANGUAGE'), 0, 2));
        }

        if(is_null($this->language) || !in_array($this->language, Setting::get_instance()->get_default_setting("supported_languages")))
        {
        	$this->language = "NL"; //Setting::get_instance()->get_default_setting("language");
        }
        $this->translations = array();
        $this->add_section_to_translations(self::GENERAL);
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
    function get_section() { return $instance->section; }
    function set_section($section) { $this->section = $section; }

    /*
     * function translate($name)
     * 		Gets the translation of the name from the translations array.
     * 		Throws error if name is not set.
     * Return: the translation of the name.
     */
    function translate($name, $language = false)
    {
    	global $error_html;
    	//dump($name);
    	if($language === false)
    	{
	    	if(isset($this->translations[$name]))
	    		return Utilities::html_special_characters($this->translations[$name]);
	    	elseif(Setting::get_instance()->get_default_setting("fault_level") == "strict")
	    		throw new Exception("The translation for " . $name . " in " . $this->language . " doesn't exist.");
	    	else
	    	{
	    		//dump($this->translations);
	    		$error_html[] = "The translation for " . $name . " in " . $this->language . " doesn't exist.<br />\n";
	    		return $name;
	    	}
    	}
    	else
    	{
    		$trans = LanguageDataManager::instance()->retrieve_specific_translation($name, $language);
    		if(!is_null($trans))
    		{
    			return $trans->translation;
    		}
    		else
    		{
    			return $name;
    		}
    	}
    }

    /*
     * function add_section_to_translations($section)
     * 		Adds the translations of the given section to the translations array.
     */
    function add_section_to_translations($section, $overwrite = false)
    {
    	//dump("Section:" . $section);
        $translation_objects = LanguageDataManager::instance()->retrieve_translations_of_section($this->language, $section);
        foreach($translation_objects as $translation)
        {
        	if(!isset($this->translations[$translation->name]) || $overwrite)
        		$this->translations[$translation->name] = $translation->translation;
        }
    }
}
?>