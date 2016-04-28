<?php
	
class DynamicPage
{
	private $id;
	private $order;
	
	private $contents = array();

	function DynamicPage($data = null)
	{
		if(!is_null($data))
		{
			$this->fillFromDatabase($data);
		}
	}
	
	private function fillFromDatabase($data)
	{
		$this->id=$data->id;
		$this->order=$data->order;
	}

	public function get_properties()
	{
		return array('id' => $this->id,
					 'order' => $this->order);
	}

	public function get_id(){return $this->id;}
	public function set_id($id){$this->id = $id;}
	public function get_order(){return $this->order;}
	public function set_order($order){$this->order = $order;}
	public function get_contents()
	{
		if(empty($this->contents))
		{
			$this->contents = DynamicPageDataManager::instance()->retrieve_dynamic_page_contents($this->id);
		}
		return $this->contents;
	}
	public function set_contents($contents){$this->contents = $contents;}
	public function get_content_by_language($language)
	{
		foreach($this->get_contents() as $page_content)
		{
			if($page_content->get_language() == $language)
			{
				return $page_content;
			}
		}
		return null;
	}
}


?>