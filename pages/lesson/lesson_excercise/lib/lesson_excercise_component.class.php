<?php
			
class LessonExcerciseComponent
{
		const PUZZLE_TYPE = 1;
		const QUESTION_TYPE = 2;
		const SELECTION_TYPE = 3;
		
		private $id;		
		private $lesson_excercise_id;
		private $order;
		private $type;
		private $type_object_id;
	   
	   function LessonExcerciseComponent($data)
	   {
		   	if($data != null)
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
			$this->lesson_excercise_id=$data->lesson_excercise_id;
			$this->order=$data->order;
			$this->type=$data->type;
			$this->type_object_id=$data->type_object_id;
	   }
	   
	   private function fillFromArray($data)
	   {
	   		$this->id=$data['id'];
	   		$this->lesson_excercise_id=$data['lesson_excercise_id'];
			$this->order=$data['order'];
			$this->type=$data['type'];
			$this->type_object_id=$data['type_object_id'];
	   }
	   	
		public function get_properties()
		{
			return array('id' => $this->id,
						 'lesson_excercise_id' => $this->lesson_excercise_id,
						 'order' => $this->order,
						 'type' => $this->type,
						 'type_object_id' => $this->type_object_id);
		}
	   
	   	public function get_id(){return $this->id;}
	   	public function set_id($id){$this->id = $id;}
	   	public function get_lesson_excercise_id(){return $this->lesson_excercise_id;}
		public function set_lesson_excercise_id($lesson_excercise_id){$this->lesson_excercise_id = $lesson_excercise_id;}
		public function get_order(){return $this->order;}
		public function set_order($order){$this->order = $order;}
		public function get_type(){return $this->type;}
		public function get_type_text(){return self::get_type_name($this->type);}
		public function set_type($type){$this->type = $type;}
		public function get_type_object_id(){return $this->type_object_id;}
		public function set_type_object_id($type_object_id){$this->type_object_id = $type_object_id;}
	   
		static public function get_type_name($type_id)
		{
			switch($type_id)
			{
				case self::PUZZLE_TYPE: return "Puzzel";
									  	break;
				case self::QUESTION_TYPE: return "Meerkeuzevraag";
									  break;
				case self::SELECTION_TYPE: return "Aanduidingsvraag";
									  break;
				default: return "Onbekend";
						 break;
			}
		}
		
		public function get_object_name()
		{
			switch($this->type)
			{
				case self::PUZZLE_TYPE: require_once Path :: get_path() . 'pages/puzzle/lib/puzzle_data_manager.class.php';
										$puzzle = PuzzleDataManager::instance(null)->retrieve_puzzle_properties_by_puzzle_id($this->type_object_id);
										return $puzzle->get_themes();
									  	break;
				case self::QUESTION_TYPE: require_once Path :: get_path() . 'pages/question/lib/question_data_manager.class.php';
										$question = QuestionDataManager::instance(null)->retrieve_question($this->type_object_id);
										return $question->get_question();
									  	break;
									  break;
				case self::SELECTION_TYPE: require_once Path :: get_path() . 'pages/selection/lib/selection_data_manager.class.php';
										$selection = SelectionDataManager::instance(null)->retrieve_selection($this->type_object_id);
										return $selection->get_question();
									  	break;
									  break;
				default: return "Onbekend";
						 break;
			}
		}
	   
	}


?>