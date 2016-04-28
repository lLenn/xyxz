<?php

class Action
{
	private $url = null;
	private $actions = null;
	private $message = '';
	private $title = '';
	private $title_function= '';
	private $image= '';
	private $image_function= '';
	private $condition_function= '';
	
	public function Action($url, $actions, $title = '', $message = '', $image = 'edit')
	{ 
		$this->url = $url;
		$this->set_actions($actions);
		$this->title = $title;
		$this->message = $message;
		$this->image = $image;
	}
	public function set_url($url){ $this->url = $url; }
	public function get_url(){ return $this->url; }
	public function set_actions($actions)
	{ 
		if(!is_array($actions))
		{
			$actions = array($actions);
		}
		$this->actions = $actions;
	}
	public function get_actions(){ return $this->actions; }
	public function get_actions_url()
	{
		$url = "";
		foreach($this->actions as $action)
		{
			$url .= "&" . $action;
		}
		return substr($url, 1);
	}
	public function set_title($title){ $this->title = $title; }
	public function get_title(){ return $this->title; }
	public function set_title_function($title_function){ $this->title_function = $title_function; }
	public function get_title_function(){ return $this->title_function; }
	public function set_message($message){ $this->message = $message; }
	public function get_message(){ return $this->message; }
	public function set_image($image){ $this->image = $image; }
	public function get_image(){ return $this->image; }
	public function set_image_function($image_function){ $this->image_function = $image_function; }
	public function get_image_function(){ return $this->image_function; }
	public function set_condition_function($condition_function){ $this->condition_function = $condition_function; }
	public function get_condition_function(){ return $this->condition_function; }
}