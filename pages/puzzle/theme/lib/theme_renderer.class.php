<?php

class ThemeRenderer
{

	private $manager;
	
	function ThemeRenderer($manager)
	{
		$this->manager = $manager;
	}
	
	public function get_form()
	{
		$languages = LanguageDataManager::instance()->retrieve_all_languages();
		$html = array();
		$html[] = '<h3 class="title">' . Language::get_instance()->translate(325) . '</h3>';
		$html[] = '<p>';
		$html[] = '<form action="" method="post">';
		$html[] = '<div class="record">';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(66) . ' :</div><br/><br class="clear_float"/>';
		foreach($languages as $l)
		{
			$html[] = '<div class="record_name_required">' . Utilities::html_special_characters($l->full_name) . '</div><div class="record_input"><input type="text" name="name_' . $l->language . '"/></div>';
		}
		$html[] = '<div class="record_button"><a id="submit_form" class="link_button" href="javascript:;">' . Language::get_instance()->translate(49) . '</a></div>';
		$html[] = '</div></form></p>';
		return implode("\n", $html);
	}
	
	public function get_table()
	{
		$html = array();
		
		$themes = $this->manager->get_data_manager()->retrieve_themes();
		$html[] = "<h3 class=\"title\">" . Language::get_instance()->translate(326) . "</h3>";
		if(count($themes))
		{
			$count = 0;
			$languages = LanguageDataManager::instance()->retrieve_all_languages();
			$count_lang = count($languages);
			$html[] = '<form name="themes_form" action="" method="post">';
			$html[] = '<input class="input_element" type="hidden" name="save_all" value="1">';
			$html[] = '<div class="record">';
			$html[] = '<table style="margin-left: 10px;">';
			$html[] = '<thead>';
			$html[] = '<tr>';
			$html[] = '<th width="30px">' . Language::get_instance()->translate(68) . '</th>';
			$html[] = '<th width="200px">' . Language::get_instance()->translate(66) . '</th>';
			$html[] = '<th></th>';
			$html[] = '</tr>';
			$html[] = '</thead>';
			$html[] = '<tbody id="theme_sortable">';
			foreach($themes as $theme)
			{
				$html[] = $this->get_row($theme, $count, $languages, $count_lang);
				$count++;
			}
			$html[] = '</tbody>';
			$html[] = '</table>';
			$html[] = '<div class="record_button">';
			$html[] = '<a id="submit_form" class="link_button" href="javascript:;">' . Language::get_instance()->translate(69) . '</a>';
			$html[] = '</div>';
			$html[] = '<div class="record_button">';
			$html[] = '<a id="reset_theme_form" class="link_button" href="javascript:;">' . Language::get_instance()->translate(70) . '</a>';
			$html[] = '</div>';
			$html[] = '</div>';
			$html[] = '</form>';
			
		}
		else
		{
			$html[] =  '<p>' . Language::get_instance()->translate(327) . '</p>';
		}
			
		return implode("\n", $html);
	}
	
	public function get_row($theme, $count, $languages, $count_lang)
	{
		$html = array();
		$html[] = '<tr class="row '.(($count%2==1)?"odd":"even").'">';
		$html[] = '<td class="row_cell" width="30px">'.$theme->get_order().'</td>';
		$html[] = '<td class="row_cell" width="200px">';
		foreach($languages as $index => $l)
		{
			$html[] = Utilities::html_special_characters($l->full_name) . ": " . Utilities::html_special_characters(Language::get_instance()->translate($theme->get_name(), $l->language)) . "<br/>";
		}
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = '<img class="delete_record" src="' . Path :: get_url_path() . 'layout/images/buttons/delete.png" title="' . Language::get_instance()->translate(328) . '" style="border: 0">';
		$html[] = '</td>';
		$html[] = '<input type="hidden" name="id_'.$theme->get_order().'" value="'. $theme->get_id().'">';
		$html[] = '</tr>';
		return implode("\n", $html);
	}
	
	public function get_selector($ids = array(),$name='theme_id')
	{
		$html[] = '<select class="input_element" name="'.$name.'[]" style="min-width: 125px;" multiple size="7">';
		$arr = $this->manager->get_data_manager()->retrieve_themes();
		foreach($arr as $el)
			$el->set_name(Language::get_instance()->translate($el->get_name()));
		uasort($arr, array(__class__, 'self::order_by_name'));
		foreach($arr as $value){
			$str = '<option value="'.$value->get_id().'"';
			if(in_array($value->get_id(), $ids)) $str .= "selected='selected'";
			$str .= ">".Language::get_instance()->translate($value->get_name())."</option>";
			$html[] = $str;
		}
		$html[] = '</select>';
		return implode("\n", $html);
	}

	public static function order_by_name($a, $b)
	{
		return strcmp($a->get_name(), $b->get_name());
	}
}

?>