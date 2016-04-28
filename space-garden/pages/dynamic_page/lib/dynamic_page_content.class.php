<?php

	class DynamicPageContent
	{
		private $id;
		private $language;
		private $title;
		private $page_content;

		public function DynamicPageContent($data=null)
		{
			if(!is_null($data))
			{
				$this->fillFromDatabase($data);
			}
		}
		
		public function fillFromDatabase($data)
		{
			$this->id=$data->id;
			$this->language=$data->language;
			$this->title=$data->title;
			$this->page_content=$data->page_content;
		}
		
		public function get_properties()
		{
			return array('id' => $this->id,
						 'language' => $this->language,
						 'title' => $this->title,
						 'page_content' => $this->page_content);
		}
	
		public function get_id(){	return $this->id; }
		public function set_id($id){	$this->id = $id; }
		public function set_language($language){	$this->language=$language;	}
		public function get_language(){	return $this->language;}
		public function get_title(){ return $this->title; }
		public function set_title($title){ $this->title = $title; }
		public function get_page_content(){	return $this->page_content;	}
		public function set_page_content($page_content){ $this->page_content = $page_content; }
	}
	
?>