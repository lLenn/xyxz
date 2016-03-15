<?php

class SetRenderer
{

	private $manager;
	
	function SetRenderer($manager)
	{
		$this->manager = $manager;
	}
	
	public function get_set_info($set)
	{
		$html = array();

		$html[] = '<div class="record">';
		$html[] = '<p><h3 class="title">' . Language::get_instance()->translate(307) . '</h3></p>';
		$html[] = $this->get_description_set($set);
		$html[] = '</div>';
		return implode("\n", $html);
	}
	
	public function get_set_puzzles_info($set_id, $right, $title = true)
	{
		$html = array();
		if($title)
			$html[] = '<p><h3 class="title">' . Language::get_instance()->translate(308) . '</h3></p>';
		$puzzles = $this->manager->get_data_manager()->retrieve_set_puzzles($set_id);
		if(count($puzzles))
		{
			$html[] = '<table id="puzzles_table">';
			if($title)
			{
				$html[] = '<tr>';
				$html[] = $this->manager->get_parent_manager()->get_renderer()->get_puzzle_row_header();
				if($right == RightManager::UPDATE_RIGHT)
					$html[] = '<th></th>';
				$html[] = '</tr>';
			}
			$odd = true;
			foreach($puzzles as $puzzle)
			{
				$html[] = '<tr class="puzzle_image_row' . ($odd?' odd':' even') .'">';
				$html[] = $this->manager->get_parent_manager()->get_renderer()->get_puzzle_row_render($puzzle);
				if($right == RightManager::UPDATE_RIGHT)
					$html[] = '<td><img src="'.Path::get_url_path().'layout/images/buttons/delete.png" class="remove_puzzle_set_relation" style="border: 0px"/></td>';
				$html[] = '</tr>';
				$odd = !$odd;
			}
			
			$html[] = '</table>';
		}
		else
			$html[] = '<p>' . Language::get_instance()->translate(309) . '</p>';
		return implode("\n", $html);
	}
	
	public function get_set_form($set=null)
	{
		$html = array();
		
		$submit = Language::get_instance()->translate(310);
		if(!is_null($set)) $submit = Language::get_instance()->translate(311);
		
		$html[] = '<form action="" method="post" id="set_creator_form">';
		$html[] = '<div class="record">';
		$html[] = '<input type="hidden" name="id" value="' . (is_null($set)?0:$set->get_id()) . '"/>';
		$html[] = '<p><h3 class="title">' . $submit . '</h3></p>';
		
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(66) . ' :</div><div class="record_input"><input type="text" name="name" value="'.(!is_null($set)?$set->get_name():'').'"/></div>';										
		$theme_ids = array();
		if(!is_null($set))
			$theme_ids = $set->get_theme_ids();
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(152) . ' :</div><div class="record_input">'.$this->manager->get_parent_manager()->get_theme_manager()->get_renderer()->get_selector($theme_ids).'</div><br class="clearfloat"/>';
		
		$difficulty_id = 0;
		if(!is_null($set))
			$difficulty_id = $set->get_difficulty_id();			
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate(255) . ' :</div><div class="record_input">'.$this->manager->get_parent_manager()->get_difficulty_manager()->get_renderer()->get_selector($difficulty_id).'</div>';						
		
		$submit = Language::get_instance()->translate(49);
		if(!is_null($set)) $submit = Language::get_instance()->translate(56);
		
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(55) . ' :</div><div class="record_input"><textarea style="width:250px;height:50px;" name="description">'.(!is_null($set)?$set->get_description():'').'</textarea></div>';								
		$html[] = '<div class="record_button"><a id="submit_form" class="link_button" href="javascript:;" style="float: left;">'.$submit.'</a></div>';
		$html[] = '</div></form>';
		return implode("\n", $html);
	}
	
	public function get_puzzle_set_relation_form($set_id, $right = RightManager::READ_RIGHT, $form_search = false)
	{
		$html = array();
		
		$html[] = '<p><h3 class="title">' . Language::get_instance()->translate(312) . '</h3></p>';
		$theme_ids = Request::post("theme_id");
		if(is_null($theme_ids) || !is_array($theme_ids))
			$theme_ids = array();
		$set = $this->manager->get_data_manager()->retrieve_set($set_id);
		$set_theme_ids = $set->get_theme_ids();
		foreach ($set_theme_ids as $id)
			$theme_ids[] = $id;
		Request::set_post("theme_id", $theme_ids);
		$puzzles = $this->manager->get_parent_manager()->get_data_manager()->retrieve_valid_puzzle_properties_with_search_form($right);
		$invalid_puzzles = $this->manager->get_parent_manager()->get_data_manager()->retrieve_invalid_puzzle_properties_with_search_form(RightManager::UPDATE_RIGHT);
		$puzzles = array_merge($puzzles, $invalid_puzzles);
		$puzzle_rel = $this->manager->get_data_manager()->retrieve_set_puzzles($set_id);
		foreach($puzzles as $index => $puzzle)
		{
			foreach ($puzzle_rel as $rel)
			{
				if($rel->get_puzzle_id() == $puzzle->get_puzzle_id())
					unset($puzzles[$index]);
			}
		}
		if(count($puzzles))
		{
			$html[] = '<form action="" method="post" id="set_relation_form">';
			$html[] = '<input type="hidden" name="set_id" value="'.$set_id.'"/>';
			$html[] = '<div class="record">';
			foreach($puzzles as $puzzle)
			{
				$html[] = '<div style="float: left">';
				$html[] = '<div style="float: left">';
				$html[] = '<input type="checkbox" name="puzzle_id[]" value="'.$puzzle->get_puzzle_id().'"/>';
				$url =  Path::get_url_path() . 'pages/puzzle/images/' . $puzzle->get_puzzle_id() . '.gif';
			    if(!file_exists(Path::get_path() . 'pages/puzzle/images/' . $puzzle->get_puzzle_id() . '.gif'))
					$url = Path::get_url_path() . 'pages/puzzle/ajax/retrieve_puzzle_image.ajax.php?puzzle_id=' . $puzzle->get_puzzle_id();
				$html[] = '<img style="vertical-align: top;" src="' . $url . '"/>';
				$html[] = '</div>';
				$html[] = '<div style="float: left">';
				$html[] = $this->manager->get_parent_manager()->get_renderer()->get_description_puzzle($puzzle);
				$html[] = '</div>';
				$html[] = '</div>';
			}
			$html[] = '<br class="clearfloat" />';
			$html[] = '<div class="record_button"><a id="submit_rel_form" class="link_button" href="javascript:;">' . Language::get_instance()->translate(49) . '</a></div></div></form>';
		}
		else
		{
			$html[] = Language::get_instance()->translate(880);
		}
		return implode("\n", $html);
	}

	public function get_shop_detail($set)
	{
		return $this->get_description_set($set);
	}
	
	public function get_set_table($right = RightManager :: READ_RIGHT, $form_search = false, $editable = true)
	{
		$html = array();
		$sets = null;
		if($form_search)
			$sets = $this->manager->get_data_manager()->retrieve_sets_with_search_form($right);
		else
			$sets = $this->manager->get_data_manager()->retrieve_sets($right);
		
		$table = new Table($sets);
		$table->set_table_id("sets_table");
		$table->set_attributes(array("id" => "sets_table"));
		$table->set_ids(array("id"));
		if($editable)
		{
			$table->set_row_link("browse_puzzle_sets", array("id"));
		}
		else
			$table->set_row_link("view_puzzle_set", array("id"));
		if($form_search)
			$table->set_no_data_message(Language::get_instance()->translate(313));
		else
			$table->set_no_data_message(Language::get_instance()->translate(314));
		
		$columns = array();
		$columns[] = new Column(Language::get_instance()->translate(66), "name");
		$columns[] = new Column(Language::get_instance()->translate(152), "themes");
		$columns[] = new Column(Language::get_instance()->translate(255), "difficulty");
		$columns[] = new Column(Language::get_instance()->translate(55), "description");
		$table->set_columns($columns);
		
		$html[] = $table->render_table();
		return implode("\n", $html);
	}
	
	public function get_set_row_header()
	{
		$html = array();
		$html[] = '<th>#</th>';
		$html[] = '<th>Naam</th>';
		$html[] = '<th>Thema</th>';
		$html[] = '<th>Moeilijkheid</th>';
		$html[] = '<th>Beschrijving</th>';
		return implode("\n", $html);
	}
	
	public function get_set_row_render($set)
	{
		$html = array();
		$html[] = '<td>';
		$html[] = $set->get_id();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $set->get_name();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $set->get_themes();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $this->manager->get_parent_manager()->get_difficulty_manager()->get_data_manager()->retrieve_difficulty($set->get_difficulty_id())->get_name();
		$html[] = '</td>';
		$html[] = '<td>';
		$html[] = $set->get_description();
		$html[] = '</td>';
		return implode("\n", $html);
	}
	
	public function get_description_set($set)
	{
		$html = array();
		$html[] = '<div class="record_name"># :</div><div class="record_output">'.$set->get_id().'</div><br class="clearfloat"/>';
		$html[] = '<div class="record_name">Naam :</div><div class="record_output">'.$set->get_name().'</div><br class="clearfloat"/>';
		$html[] = "<div class='record_name'>Thema's :</div><div class='record_output'>".$set->get_themes()."</div><br class='clearfloat'/>";
		$html[] = '<div class="record_name">Moeilijkheid :</div><div class="record_output">'.$this->manager->get_parent_manager()->get_difficulty_manager()->get_data_manager()->retrieve_difficulty($set->get_difficulty_id())->get_name().'</div><br class="clearfloat"/>';
		$html[] = '<div class="record_name">Beschrijving :</div><div class="record_output">'.nl2br($set->get_description()).'</div><br class="clearfloat"/>';
		return implode("\n", $html);
	}

	public function get_set_search($own_form = true, $search_button = true)
	{
		$html = array();
		if($own_form)
		{
			$html[] = '<form action="" method="post" id="set_search_form">';
			$html[] = '<div class="record">';
			$html[] = '<p><h3 class="title">' . Language::get_instance()->translate(315) . ' :</h3></p>';
		}
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(152) . ' :</div><div class="record_input">'.$this->manager->get_parent_manager()->get_theme_manager()->get_renderer()->get_selector().'</div>';
		$html[] = '<div class="record_name">' . Language::get_instance()->translate(255) . ' :</div><div class="record_input">'.$this->manager->get_parent_manager()->get_difficulty_manager()->get_renderer()->get_selector().'</div>';						
		if($search_button)
			$html[] = '<div class="record_button"><a id="submit_search_form" class="link_button" href="javascript:;">' . Language::get_instance()->translate(63) . '</a></div><br class="clearfloat"/>';
		if($own_form)
		{
			$html[] = '</div>';
			$html[] = '</form>';
		}
		return implode("\n", $html);		
	}
	
	public function get_selector($id = 0,$name='set_id')
	{
		$html[] = '<select class="input_element" name="'.$name.'" style="width: 125px;">';
		$html[] = '<option value="0">' . Language::get_instance()->translate(307) . ' :</option>';
		$arr = $this->manager->get_data_manager()->retrieve_sets(RightManager::READ_RIGHT);
		foreach($arr as $value)
		{
			$str = '<option value="'.$value->get_id().'"';
			if($id == $value->get_id()) $str .= " selected='selected'";
			$str .= ">".$value->get_name()."</option>";
			$html[] = $str;
		}
		$html[] = '</select>';
		return implode("\n", $html);
	}
}

?>