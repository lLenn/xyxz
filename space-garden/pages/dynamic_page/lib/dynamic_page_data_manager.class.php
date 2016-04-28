<?php

require_once Path :: get_path() . 'pages/dynamic_page/lib/dynamic_page.class.php';
require_once Path :: get_path() . 'pages/dynamic_page/lib/dynamic_page_content.class.php';

class DynamicPageDataManager extends DataManager
{
	const TABLE_NAME = 'dynamic_page';
	const CONT_TABLE_NAME = 'dynamic_page_content';
	const CLASS_NAME = 'DynamicPage';
	const CONT_CLASS_NAME = 'DynamicPageContent';
	
	public static function instance()
	{
		parent::$_instance = new DynamicPageDataManager();
		return parent::$_instance;
	}

	public function retrieve_dynamic_page($id)
	{
		return parent::retrieve_by_id(self::TABLE_NAME,self::CLASS_NAME,$id);
	}
	
	public function update_dynamic_page_order($dynamic_page)
	{
		$condition = "id = '" . $dynamic_page->get_id() . "'";
		return parent::update(self::TABLE_NAME,$dynamic_page, $condition);
	}
	
	public function retrieve_dynamic_page_titles()
	{
		$join = array();
		$join[] = new Join(self::TABLE_NAME, 'p', 'id', Join :: MAIN_TABLE);
		$join[] = new Join(self::CONT_TABLE_NAME, 'c', 'id', 'LEFT JOIN');
		$select = "DISTINCT p.id, c.title";
		$order = "p.order";
		$condition = "c.language = '" . Language::get_instance()->get_language() . "'";
		return parent::retrieve($join,null,$order,self::MANY_RECORDS,$condition,'',$select);
	}
	
	public function retrieve_dynamic_pages()
	{
		$order = "`order`";
		return parent::retrieve(self::TABLE_NAME,self::CLASS_NAME,$order);
	}
	
	public function retrieve_dynamic_page_contents($id)
	{
		$condition = "id = '" . $id . "'";
		return parent::retrieve(self::CONT_TABLE_NAME,self::CONT_CLASS_NAME,'',self::MANY_RECORDS,$condition);
	}
	
	/*
	 * Function retrieve_dynamic_page_contents_from_post()
	 * Retrieves the page contents from the post and validates the input
	 */
	function retrieve_dynamic_page_contents_from_post()
	{
		$output = "";
		$dynamic_page = $this->retrieve_dynamic_page(Request::get('id'));
		
		// VALIDATE THE MESSAGE INPUT
		$contents = array();
		$languages = LanguageDataManager::instance()->retrieve_all_languages();
		$count = 0;
		foreach ($languages as $lang)
		{
			$validation = true;
			$validation_partly = 0;
			$content = new DynamicPageContent();
			$content->set_id(Request::get('id'));
			$content->set_language($lang->language);
			if(!is_null(Request::post("title_" . $lang->language)) && Request::post("title_" . $lang->language) != '')
			{
				$content->set_title(addslashes(Request::post("title_" . $lang->language)));
				$validation_partly++;
			}
			else
			{
				$validation = false;
			}
			if(!is_null(Request::post("page_content_" . $lang->language)) && Request::post("page_content_" . $lang->language) != '')
			{
				$content->set_page_content(addslashes(Request::post("page_content_" . $lang->language)));
				$validation_partly++;
			}
			else
			{
				$validation = false;
			}
			if($validation)
			{
				$count++;
			}
			elseif($validation_partly)
			{
				$output .= Language::get_instance()->translate("fill_in_required") . "<br/>";
			}
			if($validation || $validation_partly)
			{
				$contents[] = $content;
			}
		}
		if(!$count)
        {
			$output .= Language::get_instance()->translate("translate_at_least") . "<br/>";
        }
		
		$dynamic_page->set_contents($contents);
		
		return array("dynamic_page" => $dynamic_page, "error" => $output);
	}
	
	function update_dynamic_page_contents($dynamic_page)
	{
		$output = array();
		$old_contents = $this->retrieve_dynamic_page_contents($dynamic_page->get_id());
		$old_lang = array();
		$result = true;
		foreach ($old_contents as $old_cont)
		{
			$old_lang[$old_cont->get_language()] = $old_cont->get_language();
		}
		
		foreach($dynamic_page->get_contents() as $content)
		{
			if(in_array($content->get_language(),$old_lang))
			{
				$conditions = "id = '" . $content->get_id() . "' AND language = '" . $content->get_language() . "'";
				$result .= parent::update(self::CONT_TABLE_NAME, $content, $conditions);
				unset($old_lang[$content->get_language()]);
			}
			else
			{
				$result .= parent::insert(self::CONT_TABLE_NAME, $content);
			}
		}
		
		foreach($old_lang as $lang)
		{
			$condition = "id = '" . $dynamic_page->get_id() . "' AND language = '" . $lang . "'";
			$result .= parent::delete(self::CONT_TABLE_NAME, $condition);
		}
		
		if(!$result)
		{
			$output[] = Language :: get_instance()->translate("update_failed_dp");
		}
				
		return array("result"=>$result, "error"=>implode("\n", $output));
	}
}

?>