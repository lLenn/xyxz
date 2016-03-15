<?php

require_once Path :: get_path() . "pages/puzzle/lib/puzzle_data_manager.class.php";
require_once Path :: get_path() . "pages/puzzle/lib/puzzle_renderer.class.php";

class PuzzleManager
{
	const PUZZLE_CREATOR = "Puzzle_Creator";
	const PUZZLE_VIEWER = "Puzzle_Viewer";
	const PUZZLE_BROWSER = "Puzzle_Browser";
	const PUZZLE_VALIDATOR = "Puzzle_Validator";
	const PUZZLE_DUBBLE_REMOVER = "Puzzle_Dubble_Remover";
	const PUZZLE_RANDOM = "Puzzle_Random";
	
	const PUZZLE_DIFFICULTY_BROWSER = "Puzzle_Difficulty_Browser";
	
	const PUZZLE_THEME_BROWSER = "Puzzle_Theme_Browser";
/*
	const PUZZLE_SET_BROWSER = "Puzzle_Set_Browser";
	const PUZZLE_SET_VIEWER = "Puzzle_Set_Viewer";
	const PUZZLE_SET_CREATOR = "Puzzle_Set_Creator";
	const PUZZLE_SET_EDITOR = "Puzzle_Set_Editor";
	const PUZZLE_SET_DELETOR = "Puzzle_Set_Deletor";
*/
	const Q = 0.0057565;
	
	private $user;
	private $renderer;
	private $difficulty_manager = null;
	private $theme_manager = null;
	//private $set_manager = null;
	
	function PuzzleManager($user)
	{
		Language::get_instance()->add_section_to_translations(Language::PUZZLE);
		Language::get_instance()->add_section_to_translations(Language::THEME);
		Language::get_instance()->add_section_to_translations(Language::DIFFICULTY);
		$this->user = $user;
		$this->renderer = new PuzzleRenderer($this);
	}
	
	public function get_data_manager()
	{
		return PuzzleDataManager::instance($this);
	}
	
	public function get_renderer()
	{
		return $this->renderer;
	}
	
	public function get_user()
	{
		return $this->user;
	}
	
	public function get_difficulty_manager()
	{
		if(is_null($this->difficulty_manager))
		{
			require_once Path :: get_path() . "pages/puzzle/difficulty/lib/difficulty_manager.class.php";
			$this->difficulty_manager = new DifficultyManager($this->user);
		}
		return $this->difficulty_manager;
	}
	
	public function get_theme_manager()
	{
		if(is_null($this->theme_manager))
		{
			require_once Path :: get_path() . "pages/puzzle/theme/lib/theme_manager.class.php";
			$this->theme_manager = new ThemeManager($this->user);
		}
		return $this->theme_manager;
	}
	/*
	public function get_set_manager()
	{
		if(is_null($this->set_manager))
		{
			require_once Path :: get_path() . "pages/puzzle/set/lib/set_manager.class.php";
			$this->set_manager = new SetManager($this->user, $this);
		}
		return $this->set_manager;
	}
	*/
	public function factory($action)
	{
		/*
		$user_chess_profile = UserDataManager::instance($this)->retrieve_user_chess_profile($user->get_id());
		if(!is_null($user_chess_profile) && $user_chess_profile->get_rating() == 1)
		{
			require_once Path :: get_path() . "pages/puzzle/set_master_viewer.page.php";
			return $this->action_object = new PuzzleViewer($this);
			break;
		}
		*/
		switch($action)
		{
			case self::PUZZLE_VIEWER: 
				require_once Path :: get_path() . "pages/puzzle/puzzle_viewer.page.php";
				return $this->action_object = new PuzzleViewer($this);
				break;
			case self::PUZZLE_CREATOR: 
				require_once Path :: get_path() . "pages/puzzle/puzzle_creator.page.php";
				return $this->action_object = new PuzzleCreator($this);
				break;
			case self::PUZZLE_BROWSER: 
				require_once Path :: get_path() . "pages/puzzle/puzzle_browser.page.php";
				return $this->action_object = new PuzzleBrowser($this);
				break;
			case self::PUZZLE_RANDOM: 
				require_once Path :: get_path() . "pages/puzzle/puzzle_viewer.page.php";
				Request::set_get("random", 1);
				return $this->action_object = new PuzzleViewer($this);
				break;
			case self::PUZZLE_VALIDATOR: 
				require_once Path :: get_path() . "pages/puzzle/puzzle_validator.page.php";
				return $this->action_object = new PuzzleValidator($this);
				break;
			case self::PUZZLE_DUBBLE_REMOVER: 
				require_once Path :: get_path() . "pages/puzzle/puzzle_dubble_remover.page.php";
				return $this->action_object = new PuzzleDubbleRemover($this);
				break;
			case self::PUZZLE_DIFFICULTY_BROWSER: 
				require_once Path :: get_path() . "pages/puzzle/difficulty/difficulty_browser.page.php";
				return $this->action_object = new DifficultyBrowser($this->get_difficulty_manager());
				break;
			case self::PUZZLE_THEME_BROWSER: 
				require_once Path :: get_path() . "pages/puzzle/theme/theme_browser.page.php";
				return $this->action_object = new ThemeBrowser($this->get_theme_manager());
				break;
			/*
			case self::PUZZLE_SET_BROWSER: 
				require_once Path :: get_path() . "pages/puzzle/set/set_browser.page.php";
				return $this->action_object = new SetBrowser($this->get_set_manager());
				break;
			case self::PUZZLE_SET_VIEWER: 
				require_once Path :: get_path() . "pages/puzzle/set/set_viewer.page.php";
				return $this->action_object = new SetViewer($this->get_set_manager());
				break;
			case self::PUZZLE_SET_CREATOR:
				require_once Path :: get_path() . "pages/puzzle/set/set_creator.page.php";
				return $this->action_object = new SetCreator($this->get_set_manager());
				break;
			case self::PUZZLE_SET_EDITOR: 
				require_once Path :: get_path() . "pages/puzzle/set/set_editor.page.php";
				return $this->action_object = new SetEditor($this->get_set_manager());
				break;
			case self::PUZZLE_SET_DELETOR: 
				require_once Path :: get_path() . "pages/puzzle/set/set_deletor.page.php";
				return $this->action_object = new SetDeletor($this->get_set_manager());
				break;
			*/
		}
	}
	
	public function calculate_new_rating_and_rd(&$rating_user, &$rd_user, $first_login, $last_login, $number_exc, &$rating_puzzle, $score, $now = null)
	{
		if(is_null($now))
			$now = time();
	
		$number_of_days_logged_in = ceil(($now - $first_login)/(60*60*24));
		$number_of_days_since_last_login = floor(($now - $last_login)/(60*60*24));
		$c = max( (100-($number_exc*35)/$number_of_days_logged_in) , 30);
		$t = $number_of_days_since_last_login*12/360;
	
		$rd_puzzle = 150;
		$new_rd = min( sqrt ( pow ($rd_user,2) + pow($c,2)*$t) , 350);
		$gRD_puzzle = 1/sqrt ( ( 1 + (3*pow(self::Q,2)*pow($rd_puzzle,2) /pow( pi(),2) )));
		$EsrRD= 1/(1 + pow(10,(-($gRD_puzzle*($rating_user - $rating_puzzle)/400)) ) );
		$d2 = pow((pow(self::Q,2)*pow($gRD_puzzle,2)*$EsrRD*(1-$EsrRD) ), (-1));
		$new_rating_user = $rating_user + (self::Q/(1/(pow($new_rd,2)) + 1/($d2))*($gRD_puzzle*($score-$EsrRD)));
		$new_rd_user = sqrt(  pow((1/pow($new_rd,2) + 1/($d2)),(-1)));
	
		$gRD_user = 1/sqrt (( 1 + (3*pow(self::Q,2)*pow($rd_user,2) /pow( pi(),2) )));
		$EsrRD_puzzle= 1/(1 + pow(10,(-($gRD_user*($rating_puzzle - $rating_user)/400)) ) );
		$d2_puzzle = pow((pow(self::Q,2)*pow($gRD_user,2)*$EsrRD_puzzle*(1-$EsrRD_puzzle) ), (-1));
		$new_rating_puzzle = $rating_puzzle + (self::Q/(1/pow($rd_puzzle,2) + 1/($d2_puzzle))*($gRD_user*((1-$score)-$EsrRD_puzzle)));
		$new_rd_puzzle = 150;

		$rating_user = ceil($new_rating_user);
		$rd_user = ceil($new_rd_user);
		$rating_puzzle = ceil($new_rating_puzzle);
	}
}

?>