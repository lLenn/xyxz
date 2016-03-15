<?php
			
class LessonPageText
{
		private $id;		
		private $text;
	   
	   function LessonPageText($data)
	   {
			if(is_array($data))
				$this->fillFromArray($data);
			else
				$this->fillFromDatabase($data);
	   }
	   
	   private function fillFromDatabase($data)
	   {
			$this->id=$data->id;
			$this->text=$data->text;
	   }
	   
	   private function fillFromArray($data)
	   {
	   		$this->id=$data['id'];
	   		$this->text=$data['text'];
	   }
	   
	   	
		public function get_properties()
		{
			return array('id' => $this->id,
						 'text' => $this->text);
		}
	   
	   public function get_id(){return $this->id;}
	   public function set_id($id){$this->id = $id;}
	   public function get_text(){return $this->text;}
	   public function set_text($text){$this->text = $text;}
	   
	}


?>