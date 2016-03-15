<?php

class LanguageRenderer
{
	public static function get_selector($selected_index='FR', $name='language')
	{
		$html[] = '<select class="input_element" name="'.$name.'" style="width: 150px;">';
		$arr = LanguageDataManager::instance()->retrieve_all_languages();
		foreach($arr as $value)
		{
			$str = '<option value="'.$value->language.'"';
			if($selected_index == $value->language)
			{
				$str .= "selected='selected'";
			}
			$str .= ">".Utilities::html_special_characters($value->full_name)."</option>";
			$html[] = $str;
		}
		$html[] = '</select>';
		return implode("\n", $html);
	}
	
	public static function get_full_name($index)
	{
		$arr = LanguageDataManager::instance()->retrieve_all_languages();
		foreach($arr as $value)
		{
			if($index == $value->language)
			{
				return Utilities::html_special_characters($value->full_name);
			}
		}
		return self::get_full_name(Language::get_instance()->get_language());
	}
}

?>