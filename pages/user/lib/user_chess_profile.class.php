<?php
	
	class UserChessProfile
	{
		private $user_id;
		private $rating;
		private $rd;

		public function UserChessProfile($data=null)
		{
			if(!is_null($data))
			{
				if(is_array($data))
					$this->fillFromArray($data);
				else
					$this->fillFromDatabase($data);
			}
		}
		
		public function fillFromDatabase($data)
		{
			$this->user_id=$data->user_id;
			$this->rating=$data->rating;
			$this->rd=$data->rd;
		}
		
		public function fillFromArray($data)
		{
			$this->user_id=$data["user_id"];
			$this->rating=$data["rating"];
			$this->rd=$data["rd"];
		}
		
		public function get_properties()
		{
			return array('user_id' => $this->user_id,
						 'rating' => $this->rating,
						 'rd' => $this->rd);
		}
				
		
		public function get_user_id(){	return $this->user_id; }
		public function set_user_id($user_id){	$this->user_id = $user_id; }
		public function get_rating(){	return $this->rating;	}
		public function set_rating($rating){ $this->rating = $rating;	}
		public function get_rd(){	return $this->rd;	}
		public function set_rd($rd){ $this->rd = $rd;	}
	}
	
?>