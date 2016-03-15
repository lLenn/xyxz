<?php

class LessonExcercise
{
		private $id;
		private $title;
		private $description;
		private $theme_ids;
		private $rating;
		private $user_ids;
		private $user_id;
		private $order;
		private $visible;
		private $teaser;
		private $percentage_finished = 0;
		private $percentage_added = 0;
		private $first_visible;
		private $new;
		private $criteria_lesson_id;
		private $criteria_lesson_excercise_ids = null;
		private $criteria_lesson_percentage;
		private $criteria_lesson_excercise_percentage;
	   
	   function LessonExcercise($data)
	   {
			if(is_array($data))
				$this->fillFromArray($data);
			else
				$this->fillFromDatabase($data);
	   }
	   
	   private function fillFromDatabase($data)
	   {
			$this->id=$data->id;
	   		$this->title=$data->title;
	   		$this->description=$data->description;
	   		$this->rating=$data->rating;
	   		if(isset($data->order))
	   		{
				$this->user_id=$data->user_id;
				$this->order=$data->order;
				$this->visible=$data->visible;
				$this->first_visible=$data->first_visible;
				$this->new=$data->new;
				$this->criteria_lesson_id=$data->criteria_lesson_id;
				$this->criteria_lesson_percentage=$data->criteria_lesson_percentage;
				$this->criteria_lesson_excercise_percentage=$data->criteria_lesson_excercise_percentage;
	   		}
	   }
	   
	   private function fillFromArray($data)
	   {
	   		$this->id=$data['id'];
	   	   	$this->title=$data['title'];
	   	   	$this->description=$data['description'];
	   	   	$this->theme_ids = $data['theme_ids'];
	   	   	$this->rating=$data['rating'];
	   	   	$this->user_id = $data['user_id'];
	   	   	$this->user_ids = $data['user_ids'];
			$this->order=$data['order'];
			$this->visible=$data['visible'];
			$this->new=$data['new'];
			if(isset($data['criteria_lesson_id']))
			{
				$this->criteria_lesson_id=$data['criteria_lesson_id'];
				$this->criteria_lesson_excercise_ids=$data['criteria_lesson_excercise_ids'];
				$this->criteria_lesson_percentage=$data['criteria_lesson_percentage'];
				$this->criteria_lesson_excercise_percentage=$data['criteria_lesson_excercise_percentage'];
			}
	   }
	   
	   	
		public function get_properties()
		{
			return array('id' => $this->id,
						 'title' => $this->title,
						 'description' => $this->description,
						 'rating' => $this->rating);
		}
	   
	   public function get_id(){return $this->id;}
	   public function set_id($id){$this->id = $id;}
	   public function set_theme_ids($theme_ids) { $this->theme_ids = $theme_ids; }
		public function get_theme_ids() 
		{
			if(is_null($this->theme_ids))
			{
				require_once Path :: get_path() . "pages/lesson/lesson_excercise/lib/lesson_excercise_data_manager.class.php";
				$this->theme_ids = LessonExcerciseDataManager::instance(null)->retrieve_lesson_excercise_themes_by_lesson_excercise_id($this->id);
			}
			return $this->theme_ids; 
		}	
		public function get_themes()
		{
			if(!is_null($this->get_theme_ids()) && !empty($this->theme_ids))
			{
				require_once Path::get_path() . "pages/puzzle/theme/lib/theme_manager.class.php";
				$theme_manager = new ThemeManager(null);
				$themes = array();
				foreach($this->theme_ids as $id)
					$themes[] = $theme_manager->get_data_manager()->retrieve_theme($id);
				foreach($themes as $theme)
					$theme->set_name(Language::get_instance()->translate($theme->get_name()));
				uasort($themes, array('ThemeRenderer', 'ThemeRenderer::order_by_name'));
				
				$str = "";
				$size = count($themes);
				$i = 1;
				foreach($themes as $theme)
				{
					$str .= $theme->get_name();
					if($i < $size)
						$str .= "<br/>";
					$i++;
				}
				return $str;
			}
			else
			{
				return Language::get_instance()->translate(62);
			} 
		}
		public function get_user_ids() 
		{
			if(is_null($this->user_ids))
			{
				require_once Path :: get_path() . "pages/lesson/lib/lesson_data_manager.class.php";
				$this->user_ids = LessonExcerciseDataManager::instance(null)->retrieve_lesson_excercise_relations_by_lesson_excercise_id($this->id, $this->user_id);
			}
			return $this->user_ids; 
		}	
		public function get_users() 
		{
			require_once Path :: get_path() . "pages/lesson/lib/lesson_data_manager.class.php";
			if(!is_null($this->get_user_ids()) && !empty($this->user_ids))
			{
				$str = "";
				$size = count($this->user_ids);
				$i = 1;
				foreach($this->user_ids as $id)
				{
					$str .= UserDataManager::instance(null)->retrieve_user($id)->get_name();
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
		public function set_user_ids($user_ids) { $this->user_ids = $user_ids; }
	   public function get_user_id(){return $this->user_id;}
	   public function set_user_id($user_id){$this->user_id = $user_id;}
	   public function get_title(){return $this->title;}
	   public function set_title($title){$this->title = $title;}
	   public function get_description(){return $this->description;}
	   public function set_description($description){$this->description = $description;}
	   public function get_rating(){return $this->rating;}
	   public function set_rating($rating){$this->rating = $rating;}
		public function get_difficulty()
		{ 
			require_once Path :: get_path() . "pages/puzzle/difficulty/lib/difficulty_data_manager.class.php";
			$difficulty = DifficultyDataManager::instance(null)->retrieve_difficulty_by_rating($this->rating);
			if(!is_null($difficulty) && $this->rating != 0)
			{
				return $difficulty->get_name()."</br>".$this->rating;
			}
			else
			{
				return Language::get_instance()->translate(62);
			} 
		}
	   public function get_order(){	return $this->order; }
	   public function set_order($order){$this->order = $order;}
	   public function get_visible(){return $this->visible;}
	   public function set_visible($visible){$this->visible = $visible;}
	   public function get_visible_text(){return $this->visible?Language::get_instance()->translate(167):Language::get_instance()->translate(168);}
	   public function get_teaser(){return $this->teaser;}
	   public function set_teaser($teaser){$this->teaser = $teaser;}
	   public function get_percentage_finished(){return $this->percentage_finished;}
	   public function set_percentage_finished($percentage_finished){$this->percentage_finished = $percentage_finished;}
	   public function get_percentage_added(){return $this->percentage_added;}
	   public function set_percentage_added($percentage_added){$this->percentage_added = $percentage_added;}
	   public function get_new(){return $this->new;}
	   public function set_new($new){$this->new = $new;}
	   public function get_new_text(){return $this->new?Language::get_instance()->translate(167):Language::get_instance()->translate(168);}
	   public function get_first_visible(){return $this->first_visible;}
	   public function set_first_visible($first_visible){$this->first_visible = $first_visible;}
	   public function get_criteria_lesson_id(){return $this->criteria_lesson_id;}
	   public function set_criteria_lesson_id($criteria_lesson_id){$this->criteria_lesson_id = $criteria_lesson_id;}
	   public function get_criteria_lesson_excercise_ids()
	   {
	   		if(is_null($this->criteria_lesson_excercise_ids))
	   		{
	   			$this->criteria_lesson_excercise_ids = LessonExcerciseDataManager::instance(null)->retrieve_lesson_excercise_meta_data_criteria_excercise_ids($this->id, $this->user_id);
	   		}
	   		return $this->criteria_lesson_excercise_ids;
	   }
	   public function set_criteria_lesson_excercise_ids($criteria_lesson_excercise_ids){$this->criteria_lesson_excercise_ids = $criteria_lesson_excercise_ids;}
	   public function get_criteria_lesson_percentage(){return $this->criteria_lesson_percentage;}
	   public function set_criteria_lesson_percentage($criteria_lesson_percentage){$this->criteria_lesson_percentage = $criteria_lesson_percentage;}
	   public function get_criteria_lesson_excercise_percentage(){return $this->criteria_lesson_excercise_percentage;}
	   public function set_criteria_lesson_excercise_percentage($criteria_lesson_excercise_percentage){$this->criteria_lesson_excercise_percentage = $criteria_lesson_excercise_percentage;}
	   public function get_criteria_visible()
	   {
	   		return $this->criteria_lesson_percentage || $this->criteria_lesson_excercise_percentage;
	   }
	   public function get_criteria_visible_text(){return $this->get_criteria_visible()?Language::get_instance()->translate(167):Language::get_instance()->translate(168);}
	   public function get_criteria_visible_text_details()
	   {
	   		$str = "";
	   		if($this->criteria_lesson_percentage || $this->criteria_lesson_excercise_percentage)
	   		{
	   			if($this->criteria_lesson_percentage)
	   			{
					$criteria_lesson_id = $this->get_criteria_lesson_id();
					
					$lesson = LessonDataManager::instance(null)->retrieve_lesson($this->criteria_lesson_id);
					$str .= Language::get_instance()->translate(146) . ": " . $lesson->get_title() . "\n";
					$str .= Language::get_instance()->translate(938) . ": " . $this->criteria_lesson_percentage . "%\n";
	   			}
	   			
	   			if($this->criteria_lesson_excercise_percentage)
	   			{
					$output = "";
					$count = 0;
					$total = count($this->get_criteria_lesson_excercise_ids());
	   				foreach($this->get_criteria_lesson_excercise_ids() as $id)
	   				{
	   				
						$exc = LessonExcerciseDataManager::instance(null)->retrieve_lesson_excercise($id);
						$output .= "    " . $exc->get_title(). ($count+1!=$total?"\n":"");
						$count++;
					}
	   				$str .= ($str!=""?"\n":"") . Language::get_instance()->translate(18) . ":\n" . $output . "\n";
					$str .= Language::get_instance()->translate(939) . ": " . $this->criteria_lesson_excercise_percentage . "%\n";
	   			}
			
			}
			else
				$str = Language::get_instance()->translate(62);
			return $str;
	   }
	}

/*
class LessonExcercise
{	
		private $id;
		private $lesson_id;
		private $set_id;
		private $question_set_id;
		private $selection_set_id;
		private $user_id;
		private $user_ids;
		private $visible;
		private $order;
		private $first_visible;
		private $new;
	   
	   function LessonExcercise($data)
	   {
			if(is_array($data))
			{
				$this->fillFromArray($data);
			}
			else
			{
				$this->fillFromDatabase($data);
			}
	   }
	   
	   private function fillFromDatabase($data)
	   {
			$this->id = $data->id;
	   		$this->set_id = $data->set_id;
	   		$this->question_set_id = $data->question_set_id;
	   		$this->selection_set_id = $data->selection_set_id;
	   		if(isset($data->order))
	   		{
				$this->lesson_id = $data->lesson_id;
	   			$this->user_id = $data->user_id;
	   			$this->visible = $data->visible;
	   			$this->order = $data->order;
	   			$this->first_visible = $data->first_visible;
	   			$this->new = $data->new;
	   		}
	   }
	   
	   private function fillFromArray($data)
	   {
	   		$this->id = $data['id'];
	   		$this->lesson_id = $data['lesson_id'];
	   	   	$this->set_id = $data['set_id'];
	   	   	$this->question_set_id = $data['question_set_id'];
	   	   	$this->selection_set_id = $data['selection_set_id'];
	   	   	$this->user_id = $data['user_id'];
	   	   	$this->user_ids = $data['user_ids'];
	   	   	$this->visible = $data['visible'];
	   	   	$this->new = $data['new'];
	   	   	$this->order = $data['order'];
	   	   	$this->checked = 1;
	   }
	   	
		public function get_properties()
		{
			return array('id' => $this->id,
						 'set_id' => $this->set_id,
						 'question_set_id' => $this->question_set_id,
						 'selection_set_id' => $this->selection_set_id);
		}

	   	public function get_id(){return $this->id;}
		public function set_id($id){$this->id = $id;}
	   	public function get_lesson_id(){return $this->lesson_id;}
		public function set_lesson_id($lesson_id){$this->lesson_id = $lesson_id;}
		public function get_set_id(){return $this->set_id;}
		public function set_set_id($set_id){$this->set_id = $set_id;}
		public function get_question_set_id(){return $this->question_set_id;}
		public function set_question_set_id($question_set_id){$this->question_set_id = $question_set_id;}
		public function get_selection_set_id(){return $this->selection_set_id;}
		public function set_selection_set_id($selection_set_id){$this->selection_set_id = $selection_set_id;}
		public function get_user_id(){return $this->user_id;}
		public function set_user_id($user_id){$this->user_id = $user_id;}
		public function get_user_ids() 
		{
			if(is_null($this->user_ids))
			{
				require_once Path :: get_path() . "pages/lesson/lib/lesson_data_manager.class.php";
				$this->user_ids = LessonExcerciseDataManager::instance(null)->retrieve_lesson_excercise_relations_by_lesson_excercise_id($this->id, $this->user_id);
			}
			return $this->user_ids; 
		}	
		public function get_users($seperation = "<br/>") 
		{
			require_once Path :: get_path() . "pages/lesson/lib/lesson_data_manager.class.php";
			if(!is_null($this->get_user_ids()) && !empty($this->user_ids))
			{
				$str = "";
				$size = count($this->user_ids);
				$i = 1;
				foreach($this->user_ids as $id)
				{
					$str .= UserDataManager::instance(null)->retrieve_user($id)->get_name();
					if($i < $size)
						$str .= $seperation;
					$i++;
				}
				return $str;
			}
			else
			{
				return "Allemaal";
			} 
		}
		public function set_user_ids($user_ids) { $this->user_ids = $user_ids; }
		public function get_visible(){return $this->visible;}
		public function set_visible($visible){$this->visible = $visible;}
		public function get_new(){return $this->new;}
		public function set_new($new){$this->new = $new;}
		public function get_order(){return $this->order;}
		public function set_order($order){$this->order = $order;}
		public function get_first_visible(){return $this->first_visible;}
		public function set_first_visible($first_visible){$this->first_visible = $first_visible;}
	}
*/
?>