<?php

require_once Path::get_path() . 'pages/puzzle/theme/lib/theme_manager.class.php';

class LessonRenderer
{
	private $manager;
	
	function LessonRenderer($manager)
	{
		$this->manager = $manager;
	}
	
	public function get_table($right = RightManager :: READ_RIGHT, $form_search = false, $editable = true, $title = true, $map = null)
	{
		$html = array();
		if($form_search)
		{
			$lessons = $this->manager->get_data_manager()->retrieve_lessons_with_search_form($right);
		}
		else
		{
			$lessons = $this->manager->get_data_manager()->retrieve_lessons("", null, $right, $map);
		}
		
		$table = new Table($lessons);
		$table->set_attributes(array("id" => "lessons_table"));
		$table->set_ids("id");
		if($editable)
		{
			$table->set_row_link("browse_lessons", "id");
		}
		else
		{
			$table->set_row_link("view_lesson", "id");
		}
		if($form_search)
		{
			$table->set_no_data_message('<p>' . Language::get_instance()->translate(140) . '</p>');
		}
		else
		{
			$table->set_no_data_message('<p>' . Language::get_instance()->translate(141) . '</p>');
		}
		if($editable)
		{
			$table->set_editable(true);
			$table->set_editable_id("id");
			$table->set_sortable(true);
			$table->set_delete_title(Language::get_instance()->translate(142));
			$table->add_class_to_load(Path::get_path() . 'pages/lesson/lib/lesson.class.php');
			$table->add_language_to_load(Language::LESSON);
			$table->add_hidden_input("save_all_lesson", 1);
			if(is_object($map))
				$table->add_hidden_input("map_id", $map->get_id());
			elseif($map == 'others')
				$table->add_hidden_input("map_id", "others");
			$action = new Action("add_lesson", "id", Language::get_instance()->translate(131));
			$table->add_action($action);
			$action = new Action("change_map&section=8", "id", Language::get_instance()->translate(892), '', 'change_map');
			$table->add_action($action);
		}
		
		$columns = array();
		$column = new Column("#", "order");
		$column->set_style_attributes(array("width"=>"50px", "text-align"=>"right"));
		if($editable)
			$column->set_order(true);
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(54), "title");
		$column->set_style_attributes(array("width"=>"200px"));
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(55), "description");
		$column->set_style_attributes(array("width"=>"250px", "word-wrap" => "break-word"));
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(143), "users");
		$column->set_style_attributes(array("width"=>"150px"));
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(152), "themes");
		$column->set_style_attributes(array("width"=>"75px"));
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(274), "difficulty");
		$column->set_style_attributes(array("width"=>"75px"));
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(144), "visible_text");
		$column->set_style_attributes(array("width"=>"50px"));
		if($editable)
		{
			$column->set_editable(true);
			$column->set_editable_type("checkbox");
			$column->set_editable_name("visible");
			$column->set_editable_id("order");
		}
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(949), "criteria_visible_text");
		$column->set_style_attributes(array("width"=>"50px"));
		$column->set_title("criteria_visible_text_details");
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(920), "new_text");
		$column->set_style_attributes(array("width"=>"50px"));
		if($editable)
		{
			$column->set_editable(true);
			$column->set_editable_type("checkbox");
			$column->set_editable_name("new");
			$column->set_editable_id("order");
		}
		$columns[] = $column;
		$table->set_columns($columns);
		$html[] = $table->render_table();
		return implode("\n", $html);
	}
	
	public function get_relation_table($lesson_id, $editable = true, $title = true)
	{
		$html = array();
		if($title)
			$html[] = "<h3 class=\"title\">" . ($editable?Language::get_instance()->translate(157):Language::get_instance()->translate(1150)) . "</h3>";
		$lesson_pages = $this->manager->get_data_manager()->retrieve_lesson_pages_by_lesson_id($lesson_id);
		
		$table = new Table($lesson_pages);
		$table->set_table_id("lesson_relation");
		$table->set_attributes(array("id" => "lessons_relation_table"));
		$table->set_add_header($title);
		if($editable)
		{
			$table->set_ids(array("id", "lesson_id"));
			$table->set_row_link("edit_lesson_page", array("id", "lesson_id"));
			$table->set_editable(true);
			$table->set_editable_id("id");
			$table->set_sortable(true);
			$table->set_delete_title(Language::get_instance()->translate(159));
			$table->add_class_to_load(Path::get_path() . 'pages/lesson/lib/lesson_page.class.php');
		}
		$table->set_no_data_message("<p>" . Language::get_instance()->translate(158) . "</p>");
		
		$columns = array();
		$column = new Column("#", "order");
		$column->set_style_attributes(array("width"=>"50px", "text-align"=>"right"));
		$column->set_order(true);
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(54), "title");
		$column->set_style_attributes(array("width"=>"200px"));
		$columns[] = $column;
		$column = new Column(Language::get_instance()->translate(161), "type_text");
		$column->set_style_attributes(array("width"=>"75px"));
		$columns[] = $column;
		if($title)
		{
			$column = new Column(Language::get_instance()->translate(160), "next", "boolean");
			$columns[] = $column;
		}
		$table->set_columns($columns);
		$html[] = $table->render_table();
		return implode($html, "\n");
	}

	public static function order_by_title($a, $b)
	{
		return strcmp($a->get_title(), $b->get_title());
	}
}

?>