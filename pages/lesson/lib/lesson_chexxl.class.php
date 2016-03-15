<?php
			
class LessonChexxl
{
		private $title;
		private $text;
		private $pgn;
	   
	   function LessonChexxl($data)
	   {
	   		$this->fillFromArray($data);
	   }
	   	   
	   private function fillFromArray($data)
	   {
	   	   	$this->title=$data['title'];
	   	   	$this->text=$data['text'];
	   		$this->pgn=$data['pgn'];
	   }
	   
	   public function get_title(){return $this->title;}
	   public function set_title($title){$this->title = $title;}
	   public function get_text(){return $this->text;}
	   public function set_text($text){$this->text = $text;}
	   public function get_pgn(){return $this->pgn;}
	   public function set_pgn($pgn){$this->pgn = $pgn;}
	   
	}


?>