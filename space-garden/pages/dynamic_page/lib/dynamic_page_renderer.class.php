<?php

class DynamicPageRenderer
{
	private $manager;
	
	function DynamicPageRenderer($manager)
	{
		$this->manager = $manager;
	}
	
	public function get_table()
	{
		$pages = $this->manager->get_data_manager()->retrieve_dynamic_pages();
		$query = "SELECT * FROM `dynamic_page` ORDER BY `order`";
		$table = new Table($query, "DynamicPage");
		$table->set_table_id("pages_table");

		$table->set_ids("id");
        	$table->set_table_body_id("dp_sortable");
		$table->set_row_link("edit_page", "id");
		$table->set_no_data_message(Language::get_instance()->translate("no_pages"));

		$columns = array();
		$column = new Column(Language::get_instance()->translate("order"), "order");
		$column->set_style_attributes(array("width"=>"50px", "text-align"=>"right"));
		$columns[] = $column;
		$columns[] = new Column(Language::get_instance()->translate("id"), "id");
		$table->set_columns($columns);
		
		return $table->render_table();
	}
	   
	public function get_form($dynamic_page = null)
	{
		$html = array();
		$html[] = '<script type="text/javascript" src="'.Path::get_url_path().'pages/dynamic_page/javascript/dynamic_page_form.js"></script>';
		$html[] = '<form method="post" action="" class="page" name="editor" id="editor">';
		$html[] = '<div class="record">';
		$html[] = '<div class="record_name_required">' . Language::get_instance()->translate("form_language") . ' : </div>';
		$html[] = '<div class="record_input">' . LanguageRenderer::get_selector() . '</div>';
		$html[] = '<br class="clear_float">';
		$html[] = '<div id="language_div">';
		$languages = LanguageDataManager::instance()->retrieve_all_languages();
		foreach($languages as $language)
		{
			$content = $dynamic_page->get_content_by_language($language->language);
			$html[] = '<div id="language_'. $language->language . '"' . ($language->language!='FR'?' style="display: none;"':'') . '>';
			$html[] = '<div class="record_name_required">' . Language::get_instance()->translate("form_title") . ' : </div>';
			$html[] = '<div class="record_input"><input type="text" size="100" name="title_'. $language->language . '" value="' . (!is_null($content)?$content->get_title():"") . '"></div>';
			$html[] = '<br class="clear_float">';
			$html[] = '<div class="record_name_required">' . Language::get_instance()->translate("form_body") . ' : </div>';
			if($dynamic_page->get_id() == "products")
			{
				$html[] = '<p class="feedback" style="float: right; margin-right: 25px;">' . Language::get_instance()->translate("products_legend") . ' : {product_slider, 500}<br>';
				$html[] = Language::get_instance()->translate("products_legend_width") . '</p>';
			}
			$html[] = '<br class="clear_float">';
			$html[] = '<div class="record_text_area">';
			$html[] = '<textarea class="mce_editor" name="page_content_'. $language->language . '" style="width: 100%; height: 700px;">';
			if(!is_null($content))	
			{
				$html[] = $content->get_page_content();
			}
			$html[] = '</textarea>';
			$html[] = '</div>';
			$html[] = '<br class="clear_float">';
			$html[] = '</div>';
		}
		$html[] = '</div>';
		$html[] = '<br>';
		$html[] = '<br>';
		$html[] = '<div class="record_button_aligned"><a id="submit_form" class="link_button" href="javascript:;">' . Language :: get_instance()->translate("form_submit"). '</a></div>';
		$html[] = '<br class="clear_float">';
		$html[] = '</div>';
		$html[] = '</form>';
		return implode("\n", $html);
	}
}

?>