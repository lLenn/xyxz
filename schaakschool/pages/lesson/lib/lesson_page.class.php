<?php
			
class LessonPage
{
		const TEXT_TYPE = 1;
		const PUZZLE_TYPE = 2;
		const GAME_TYPE = 3;
		const VIDEO_TYPE = 4;
		const QUESTION_TYPE = 5;
		const END_GAME_TYPE = 6;
		const SELECTION_TYPE = 7;
		
		private $id;		
		private $lesson_id;
		private $title;
		private $order;
		private $type;
		private $type_object_id;
		private $next;
	   
	   function LessonPage($data=null)
	   {
	   		if(!is_null($data))
	   		{
				if(is_array($data))
					$this->fillFromArray($data);
				else
					$this->fillFromDatabase($data);
	   		}
	   }
	   
	   private function fillFromDatabase($data)
	   {
			$this->id=$data->id;
			$this->lesson_id=$data->lesson_id;
	   		$this->title=$data->title;
			$this->order=$data->order;
			$this->type=$data->type;
			$this->type_object_id=$data->type_object_id;
			$this->next=$data->next;
	   }
	   
	   private function fillFromArray($data)
	   {
	   		$this->id=$data['id'];
	   		$this->lesson_id=$data['lesson_id'];
	   	   	$this->title=$data['title'];
			$this->order=$data['order'];
			$this->type=$data['type'];
			$this->type_object_id=$data['type_object_id'];
			$this->next=$data['next'];
	   }
	   	
		public function get_properties()
		{
			return array('id' => $this->id,
						 'lesson_id' => $this->lesson_id,
						 'title' => $this->title,
						 'order' => $this->order,
						 'type' => $this->type,
						 'type_object_id' => $this->type_object_id,
						 'next' => $this->next);
		}
	   
	   	public function get_id(){return $this->id;}
	   	public function set_id($id){$this->id = $id;}
	   	public function get_lesson_id(){return $this->lesson_id;}
		public function set_lesson_id($lesson_id){$this->lesson_id = $lesson_id;}
		public function get_title(){return $this->title;}
		public function set_title($title){$this->title = $title;}
		public function get_order(){return $this->order;}
		public function set_order($order){$this->order = $order;}
		public function get_type(){return $this->type;}
		public function get_type_text(){return self::get_type_name($this->type);}
		public function set_type(){$this->type = $type;}
		public function get_type_object_id(){return $this->type_object_id;}
		public function set_type_object_id($type_object_id){$this->type_object_id = $type_object_id;}
		public function get_next(){return $this->next;}
		public function set_next(){$this->next = $next;}
	   
		static public function get_type_name($type_id)
		{
			switch($type_id)
			{
				case self::TEXT_TYPE: return "Tekst";
									  break;
				case self::PUZZLE_TYPE: return "Puzzel";
									  	break;
				case self::GAME_TYPE: return "Partij";
									  break;
				case self::VIDEO_TYPE: return "Video";
									  break;
				case self::QUESTION_TYPE: return "Meerkeuzevraag";
									  break;
				case self::END_GAME_TYPE: return "Afwikkeling";
									  break;
				case self::SELECTION_TYPE: return "Aanduidingsvraag";
									  break;
				default: return "Onbekend";
						 break;
			}
		}
	   
	}


?>