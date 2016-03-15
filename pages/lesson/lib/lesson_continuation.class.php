<?php

class LessonContinuation
{
	private $id;
	private $message_id;
	private $message;
	private $to_user_id;
	private $from_user_id;
	private $lesson_id;
	private $lesson_excercise_ids;
	private $bought;
	private $requested;
	
	function LessonContinuation($data=null)
	{
		if(!is_null($data))
		{
			if(is_array($data))
				$this->fill_from_array($data);
			else
				$this->fill_from_database($data);
		}
	}
	
	function fill_from_array($data)
	{
		$this->id = $data['id'];
		$this->to_user_id = $data['to_user_id'];
		$this->from_user_id = $data['from_user_id'];
		$this->lesson_id = $data['lesson_id'];
		$this->lesson_excercise_ids = $data['lesson_excercise_ids'];
		$this->bought = $data['bought'];
		$this->requested = $data['requested'];
	}
	
	function fill_from_database($data)
	{
		$this->id = $data->id;
		$this->message_id = $data->message_id;
		$this->to_user_id = $data->to_user_id;
		$this->from_user_id = $data->from_user_id;
		$this->lesson_id = $data->lesson_id;
		$this->bought = $data->bought;
		$this->requested = $data->requested;
	}
	
	function get_properties()
	{
		return array('id' => $this->id,
					 'message_id' => $this->message_id,
					 'to_user_id' => $this->to_user_id,
					 'from_user_id' => $this->from_user_id,
					 'lesson_id' => $this->lesson_id,
					 'bought' => $this->bought,
					 'requested' => $this->requested);
	}

	
	function get_id()		{	return $this->id;		}
	function get_message_id()		{	return $this->message_id;		}
	function get_message()
	{
		if(is_null($this->message))
		{
			require_once Path :: get_path() . "pages/message/lib/message_data_manager.class.php";
			$this->message = MessageDataManager::instance(null)->retrieve_message($this->message_id);
		}
		return $this->message;		
	}
	function get_to_user_id()		{	return $this->to_user_id;		}
	function get_from_user_id()		{	return $this->from_user_id;		}
	function get_lesson_id()	{	return $this->lesson_id;	}
	function get_lesson_excercise_ids()
	{
		if(is_null($this->lesson_excercise_ids))
		{
			require_once Path :: get_path() . "pages/lesson/lib/lesson_data_manager.class.php";
			$this->lesson_excercise_ids = LessonDataManager::instance(null)->retrieve_lesson_selection_lesson_excercise_ids($this->id);
		}
		return $this->lesson_excercise_ids;
	}
	function get_bought() { return $this->bought; }
	function get_requested() { return $this->requested; }
	function get_lesson_text()
	{
		require_once Path :: get_path() . "pages/lesson/lib/lesson_data_manager.class.php";
		return LessonDataManager::instance(null)->retrieve_lesson($this->lesson_id)->get_title();
	}
	function get_lesson()
	{
		require_once Path :: get_path() . "pages/lesson/lib/lesson_data_manager.class.php";
		return LessonDataManager::instance(null)->retrieve_lesson($this->lesson_id);
	}
	function get_lesson_excercises_text()
	{	
		require_once Path::get_path() . "pages/lesson/lesson_excercise/lib/lesson_excercise_data_manager.class.php";
		if(!is_null($this->get_lesson_excercise_ids()) && !empty($this->lesson_excercise_ids))
		{
			$str = "";
			$size = count($this->lesson_excercise_ids);
			$i = 1;
			foreach($this->lesson_excercise_ids as $id)
			{
				$str .= LessonExcerciseDataManager::instance(null)->retrieve_lesson_excercise($id)->get_title();
				if($i < $size)
					$str .= "<br/>";
				$i++;
			}
			return $str;
		}
		else
		{
			return Language::get_instance()->translate(820);
		}
	}
	function get_title_from_message() { return $this->get_message()->get_title(); }
	function get_message_from_message() { return $this->get_message()->get_message(); }
	function get_to_user_text() { return $this->get_message()->get_to_user_text(); }
	function get_from_user_text() { return $this->get_message()->get_from_user_text(); }
	
	function set_id($id)			{	$this->id = $id;			}
	function set_to_user_id($to_user_id)				{	$this->to_user_id = $to_user_id;				}
	function set_from_user_id($from_user_id)				{	$this->from_user_id = $from_user_id;				}
	function set_message_id($message_id)			{	$this->message_id = $message_id;			}
	function set_message($message)			{	$this->message = $message;			}
	function set_lesson_id($lesson_id)	{	$this->lesson_id = $lesson_id;	}
	function set_lesson_excercise_ids($lesson_excercise_ids)				{	$this->lesson_excercise_ids = $lesson_excercise_ids;		}
	function set_bought($bought)				{	$this->bought = $bought;		}
	function set_requested($requested)				{	$this->requested = $requested;		}
}
?>