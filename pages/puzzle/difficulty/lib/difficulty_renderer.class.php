<?php

class DifficultyRenderer
{

	private $manager;
	
	function DifficultyRenderer($manager)
	{
		$this->manager = $manager;
	}
	
	public function get_form()
	{
		$languages = LanguageDataManager::instance()->retrieve_all_languages();
		$html = array();
		$html[] = '<h3 class="title">' . Language::get_instance()->translate(251) . '</h3>';
		$html[] = '<p>';
		$html[] = '<form action="" method="post">';
		$html[] = '<div class="record">';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(66) . ' ' . Language::get_instance()->translate(785)  . ':</div><br/><br class="clear_float"/>';
		foreach($languages as $l)
		{
			$html[] = '<div class="record_name_required">' . Utilities::html_special_characters($l->full_name) . '</div><div class="record_input"><input type="text" name="name_male_' . $l->language . '"/></div>';
		}
		$html[] = '<br/><br />';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(66) . ' ' . Language::get_instance()->translate(786)  . ':</div><div class="record_input"><input type="checkbox" id="idem_female" name="idem_female"/> ' . Language::get_instance()->translate(788)  . '</div>';
		$html[] = '<div id="female_block">';
		foreach($languages as $l)
		{
			$html[] = '<div class="record_name_required">' . Utilities::html_special_characters($l->full_name) . '</div><div class="record_input"><input type="text" name="name_female_' . $l->language . '"/></div>';
		}
		$html[] = '</div>';
		$html[] = '<div class="record_button"><a id="submit_form" class="link_button" href="javascript:;">' . Language::get_instance()->translate(49) . '</a></div>';
		$html[] = '</div></p>';
		$html[] = '</form>';
		return implode("\n", $html);
	}
	
	public function get_table()
	{
		$html = array();
		
		$difficulties = $this->manager->get_data_manager()->retrieve_difficulties();
		$html[] = "<h3 class=\"title\">" . Language::get_instance()->translate(252) . "</h3>";
		if(count($difficulties))
		{
			$count = 0;
			$languages = LanguageDataManager::instance()->retrieve_all_languages();
			$count_lang = count($languages);
			$html[] = '<form name="difficulties_form" action="" method="post">';
			$html[] = '<input class="input_element" type="hidden" name="save_all" value="1">';
			$html[] = '<div class="record">';
			$html[] = '<table style="margin-left: 10px;">';
			$html[] = '<thead>';
			$html[] = '<tr>';
			$html[] = '<th width="30px">' . Language::get_instance()->translate(68) . '</th>';
			$html[] = '<th width="200px">' . Language::get_instance()->translate(49) . '</th>';
			$html[] = '<th width="75px">&nbsp;</th>';
			$html[] = '<th width="75px">&nbsp;</th>';
			$html[] = '</tr>';
			$html[] = '</thead>';
			$html[] = '<tbody id="difficulty_sortable">';
			foreach($difficulties as $difficulty)
			{
				$html[] = $this->get_row($difficulty, $count, $languages, $count_lang);
				$count++;
			}
			$html[] = '</tbody>';
			$html[] = '</table>';
			$html[] = '</div>';
			$html[] = '</form>';
			
		}
		else
			$html[] =  '<p>' . Language::get_instance()->translate(253) . '</p>';
			
		return implode("\n", $html);
	}
	
	public function get_row($difficulty, $count, $languages, $count_lang)
	{
		$html = array();
		$html[] = '<tr class="row '.(($count%2==1)?"odd":"even").'">';
		$html[] = '<td class="row_cell" width="30px">'.$difficulty->get_order().'</td>';
		$html[] = '<td class="row_cell" width="200px">';
		foreach($languages as $index => $l)
		{
			$html[] = Utilities::html_special_characters($l->full_name) . ": " . Utilities::html_special_characters(Language::get_instance()->translate($difficulty->get_name_male(), $l->language) . ($difficulty->get_name_male()!=$difficulty->get_name_female()?"/" . Language::get_instance()->translate($difficulty->get_name_female(), $l->language):"")) . "<br/>";
		}
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $difficulty->get_bottom_rating_text();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $difficulty->get_top_rating_text();
		$html[] = '</td>';
		$html[] = '</tr>';
		return implode("\n", $html);
	}
	
	public function get_selector($id = 0,$name='difficulty_id')
	{
		$html[] = '<select class="input_element" name="'.$name.'" style="min-width: 125px;">';
		$html[] = '<option value="0">' . Language::get_instance()->translate(255) . ':</option>';
		$arr = $this->manager->get_data_manager()->retrieve_difficulties();
		foreach($arr as $value){
			$str = '<option value="'.$value->get_id().'"';
			if($id == $value->get_id()) $str .= "selected='selected'";
			$str .= ">".$value->get_name().": ".$value->get_bottom_rating_text().($value->get_bottom_rating_text()=="<"||$value->get_top_rating_text()=="<"?" ":" - ") . $value->get_top_rating_text() . "</option>";
			$html[] = $str;
		}
		$html[] = '</select>';
		return implode("\n", $html);
	}
	
}

?>