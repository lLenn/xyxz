<?php
require_once Path :: get_path() . "pages/right/lib/location_right.class.php";
require_once Path :: get_path() . "pages/right/lib/right_data_manager.class.php";
require_once Path :: get_path() . "pages/right/lib/right_renderer.class.php";
require_once Path :: get_path() . "pages/group/lib/group_manager.class.php";
require_once Path :: get_path() . "pages/puzzle/difficulty/lib/difficulty_manager.class.php";

class RightManager
{
	const NO_RIGHT = 0;
	const READ_RIGHT = 1;
	const WRITE_RIGHT = 2;
	const UPDATE_RIGHT = 3;
	
	const MENU_LOCATION_ID = 16;
	const PUZZLE_LOCATION_ID = 17;
	const USER_LOCATION_ID = 18;
	const GROUP_LOCATION_ID = 19;
	const SET_LOCATION_ID = 21;
	const GAME_LOCATION_ID = 22;
	const LESSON_LOCATION_ID = 23;
	const LESSON_EXCERCISE_LOCATION_ID = 47;
	const ENDGAME_LOCATION_ID = 40;
	const VIDEO_LOCATION_ID = 42;
	const QUESTION_LOCATION_ID = 43;
	const QUESTIONSET_LOCATION_ID = 44;
	const HELP_LOCATION_ID = 45;
	const SELECTION_LOCATION_ID = 48;
	const SELECTIONSET_LOCATION_ID = 49;
	const MESSAGE_LOCATION_ID = 50;
	const NEWS_LOCATION_ID = 52;
	
	const RIGHT_BROWSER = "Right_Browser";
	const RIGHT_MAP_CREATOR = "Right_Map_Creator";
	const RIGHT_MAP_CHANGER = "Right_Map_Changer";
	const RIGHT_MAP_EDITOR = "Right_Map_Editor";
	const RIGHT_MAP_DELETOR = "Right_Map_Deletor";
	const RIGHT_MAP_UP = "Right_Map_Up";
	const RIGHT_MAP_DOWN = "Right_Map_Down";
	
	private $user;
	private $renderer;
	public static $_time = 0;
	
    protected static $_instance = null;
    protected static $_debug = false;
    private static $_locations = array();

	public static function instance()
	{
		if(is_null(self::$_instance))
			self::$_instance = new RightManager(UserDataManager::instance(null)->retrieve_user(Session::get_user_id()));
		return self::$_instance;
	}
	
	public static function set_debug($debug)
	{
		self::$_debug = $debug;
	}
	
	function RightManager($user)
	{
		Language::get_instance()->add_section_to_translations(Language::RIGHT);
		$this->user = $user;
		$this->renderer = new RightRenderer($this);
	}
	
	public function get_data_manager()
	{
		return RightDataManager::instance($this);
	}
	
	public function get_renderer()
	{
		return $this->renderer;
	}
	
	public function get_user()
	{
		return $this->user;
	}
	
	public function factory($action)
	{
		switch($action)
		{
			case self::RIGHT_BROWSER: 
				require_once Path :: get_path() . "pages/right/right_browser.page.php";
				return $this->action_object = new RightBrowser($this);
				break;
			case self::RIGHT_MAP_CHANGER: 
			case self::RIGHT_MAP_CREATOR:
			case self::RIGHT_MAP_DELETOR: 
			case self::RIGHT_MAP_EDITOR:  
			case self::RIGHT_MAP_UP: 
			case self::RIGHT_MAP_DOWN:  
				require_once Path :: get_path() . "pages/right/map_manager.page.php";
				return $this->action_object = new RightMapManager($this, $action);
				break;	
		}
	}
	
	public function get_locations()
	{
		if(empty(self::$_locations))
		{
			$root_files = new RecursiveDirectoryIterator('.');
	        $root_files = new RecursiveIteratorIterator($root_files, 1);
	
			foreach($root_files as $file)
			{
				if($file->isFile() && $file->getFileName() == 'location.xml')
				{
					self::$_locations[] = $this->get_location($file);
				}
			}
			
			$saved_locations = $this->get_data_manager()->retrieve_location_rights();
			
			foreach(self::$_locations as $location)
			{
				$validation = false;
				foreach($saved_locations as $index => $saved_location)
				{
					if($location->get_location() == $saved_location->get_location())
					{
						$location->set_id($saved_location->get_id());
						$this->get_data_manager()->update_location_right($location);
						unset($saved_locations[$index]);
						$validation = true;
						break;
					}
				}
				if(!$validation)
				{
					$id = $this->get_data_manager()->insert_location_right($location);
					$location->set_id($id);
				}
			}
			
			foreach($saved_locations as $saved_location)
			{
				$this->get_data_manager()->delete_location_right($saved_location->get_id());
			}
		}
		
		return self::$_locations;
	}
	
	public function get_location($file)
	{
		$doc = new DOMDocument();
        $location = new LocationRight();

        $doc->load($file);
        $object = $doc->getElementsByTagname('location')->item(0);
		$name = strtolower($object->getAttribute('name'));
        $location->set_location(Utilities::convert_underscore_to_camelcase($name));
		
		$dataprovider = $object->getElementsByTagname('dataprovider')->item(0);
		$location->set_function_all($dataprovider->getAttribute('function_all'));
		$location->set_function_one($dataprovider->getAttribute('function_one'));
		$location->set_primary_key($dataprovider->getAttribute('primary_key'));
		$location->set_row_header($dataprovider->getAttribute('row_header'));
		$location->set_row_renderer($dataprovider->getAttribute('row_renderer'));
		$location->set_description($dataprovider->getAttribute('description'));
		$parent = $this->get_data_manager()->retrieve_location_right_by_location($dataprovider->getAttribute('parent'));
		$location->set_parent_id(is_null($parent)?null:$parent->get_id());

		return $location;
	}
	
	public function check_right_location($check_right, $location, $user, $exclude_admin_right = false, $exclude_allowed_objects = false)
	{
		$time = microtime(true);
		if(self::$_debug)
		{
			dump("///----- CHECK RIGHT LOCATION -----///");
		}
			
		if(is_null($user))
		{
			if(self::$_debug)
			{
				dump("User is null.");
			}
			self::$_time += microtime(true) - $time;
			return false;
		}
		elseif($user->is_admin() && !$exclude_admin_right)
		{
			if(self::$_debug)
			{
				dump("User is admin.");
			}
			self::$_time += microtime(true) - $time;
			return true;
		}
		
		if(!is_numeric($location))
		{
			if(self::$_debug)
			{
				dump("Location variable is string: " . $location);
			}
			$location_id = $this->get_data_manager()->retrieve_location_right_by_location($location)->get_id();
			
		}
		else
		{
			$location_id = $location;
		}
		
		if(self::$_debug)
		{
			dump("Location id: " . $location_id);
			dump("User:");
			dump($user);
		}
		
		$right = self::NO_RIGHT;
		
		if(self::$_debug)
		{
			dump("Starting retrieving right");
		}
		
		/*
		$location_user_right = $this->get_data_manager()->retrieve_location_user_right($location_id, $user->get_id());
		
		if(self::$_debug)
		{
			dump("Location user right:");
			dump($location_user_right);
		}
		
		if(!is_null($location_user_right))
		{
			if($location_user_right->get_update())
				$right = self::UPDATE_RIGHT;
			elseif($location_user_right->get_write())
				$right = self::WRITE_RIGHT;
			elseif($location_user_right->get_read())
				$right = self::READ_RIGHT;
		}
		
		if(self::$_debug)
		{
			dump("Right:");
			dump($right);
		}
		
		$location_object_user_rights = $this->get_data_manager()->retrieve_location_object_user_rights_by_user_id($location_id, $user->get_id());
		
		if(self::$_debug)
		{
			dump("Location object user rights:");
			dump($location_object_user_rights);
		}
		
		if($right != self::UPDATE_RIGHT)
		{
			foreach($location_object_user_rights as $location_object_user_right)
			{
				if($location_object_user_right->get_update())
					$right = self::UPDATE_RIGHT;
				elseif($location_object_user_right->get_write())
					$right = self::WRITE_RIGHT;
				elseif($location_object_user_right->get_read())
					$right = self::READ_RIGHT;
			}
		}
		
		if(self::$_debug)
		{
			dump("Right:");
			dump($right);
		}
		*/
		
		$group_user_relations = $user->get_group_id();
		if(self::$_debug)
		{
			dump("Groups:");
			dump($group_user_relations);
		}
		$location_group_right = $this->get_data_manager()->retrieve_location_group_right($location_id, $group_user_relations);

		if(self::$_debug)
		{
			dump("Location group rights:");
		}
		if($right != self::UPDATE_RIGHT)
		{				
			if(!is_null($location_group_right))
			{
				if($location_group_right->get_update())
					$right = self::UPDATE_RIGHT;
				elseif($location_group_right->get_write())
					$right = self::WRITE_RIGHT;
				elseif($location_group_right->get_read())
					$right = self::READ_RIGHT;
			}
				
			if(self::$_debug)
			{
				dump("Right:");
				dump($right);
			}		
		}
		
		
		/*
		if($right != self::UPDATE_RIGHT)
		{
			$location_object_group_rights = array();
			foreach($group_user_relations as $group_user_relation)
				$location_object_group_rights[] = $this->get_data_manager()->retrieve_location_object_group_rights_by_group_id($location_id, $group_user_relation->get_group_id());
			foreach($location_object_group_rights as $location_object_group_right)
			{
				foreach($location_object_group_right as $group_right)
				{
					if($group_right->get_update())
						$right =  self::UPDATE_RIGHT;
					elseif($group_right->get_write())
						$right =  self::WRITE_RIGHT;
					elseif($group_right->get_read())
						$right = self::READ_RIGHT;
				}
			}
		}
		*/
		
		if($check_right == self :: WRITE_RIGHT && ($right == self :: WRITE_RIGHT || $right == self :: UPDATE_RIGHT) && $exclude_allowed_objects == false)
		{
			if(self::$_debug)
			{
				dump("Allowed to write and update.");
			}
			
			$allowed_objects = -1;
			/*
			if(!is_null($location_user_right))
			{
				$allowed_objects = $location_user_right->get_allowed_objects();
			}
			
			if(self::$_debug && !is_null($location_user_right))
			{
				dump("Allowed objects user right: " . $location_user_right->get_allowed_objects());
				dump($location_user_right);
			}

			if($allowed_objects != 0)
			{
				foreach($location_group_rights as $location_group_right)
				{
			*/
					if(self::$_debug)
					{
						dump("Allowed objects right: " . self::get_allowed_objects($location, $user, $exclude_admin_right));
						dump($location_group_right);
					}
					if(!is_null($location_group_right)/* && $location_group_right->get_allowed_objects() != 0*/)
					{
						$allowed_objects = self::get_allowed_objects($location, $user, $exclude_admin_right);
					}
					/*
					elseif(!is_null($location_group_right) && $location_group_right->get_allowed_objects() == 0)
					{
						$allowed_objects = 0;
						break;
					}
					*/
			/*
			 	}
			}
			*/
			$made_objects = $this->get_data_manager()->retrieve_made_objects($user->get_id(), $location_id);
			if($allowed_objects != -1 && count($made_objects) >= $allowed_objects && $right > self::READ_RIGHT)
			{
				if(self::$_debug)
				{
					dump("All allowed objects are used up right is set to read.");
				}
				$right = self :: READ_RIGHT;
			}
		}
		
		if(self::$_debug)
		{
			$return = $right >= $check_right;
			dump("///----- RETURN: " . $return . " -----///");
		}
		self::$_time += microtime(true) - $time;
		return $right >= $check_right;
	}
	
	public function get_right_location_object($location, $user, $object_id, $exclude_admin_right = false)
	{
		$time = microtime(true);
		if(self::$_debug)
		{
			dump("///----- GET RIGHT LOCATION OBJECT -----///");
		}
		
		if(is_null($user))
		{
			if(self::$_debug)
			{
				dump("User is null.");
			}
			self::$_time += microtime(true) - $time;
			return self::NO_RIGHT;
		}
		elseif(($user->is_admin() ||  $this->get_user()->is_admin()) && !$exclude_admin_right)
		{
			if(self::$_debug)
			{
				dump("User is admin.");
			}
			self::$_time += microtime(true) - $time;
			return self::UPDATE_RIGHT;
		}
		
		if(!is_numeric($location))
		{
			if(self::$_debug)
			{
				dump("Location variable is string: " . $location);
			}
			$location_id = $this->get_data_manager()->retrieve_location_right_by_location($location)->get_id();
			
		}
		else
		{
			$location_id = $location;
		}
		
		if(self::$_debug)
		{
			dump("Location id: " . $location_id);
			dump("Object id: " . $object_id);
			dump("User:");
			dump($user);
		}
		
		$right = self::NO_RIGHT;
		if(self::$_debug)
		{
			dump("Starting retrieving right.");
		}
		
		$location_object_user_right = $this->get_data_manager()->retrieve_location_object_user_right($location_id, $user->get_id(), $object_id);
		if(self::$_debug)
		{
			dump("Location object user right: ");
			dump($location_object_user_right);
		}
		if(!is_null($location_object_user_right))
		{
			if($location_object_user_right->get_update())
			{
				self::$_time += microtime(true) - $time;
				return self::UPDATE_RIGHT;
			}
			elseif($location_object_user_right->get_write())
			{
				$right = self::WRITE_RIGHT;
			}
			elseif($location_object_user_right->get_read())
			{
				$right = self::READ_RIGHT;
			}
		}
		
		/*
		if(self::$_debug)
		{
			dump("Right:");
			dump($right);
		}
		
		$group_user_relations = GroupdataManager::instance(new GroupManager($user))->retrieve_group_user_relations_by_user_id($user->get_id());
		if(self::$_debug)
		{
			dump("Groups:");
			dump($group_user_relations);
		}
		$location_object_group_rights = array();
		foreach($group_user_relations as $group_user_relation)
		{
			$location_object_group_rights[] = $this->get_data_manager()->retrieve_location_object_group_right($location_id, $group_user_relation->get_group_id(), $object_id);
		}
		if(self::$_debug)
		{
			dump("Object group rights:");
		}
		foreach($location_object_group_rights as $object_group_right)
		{
			if(self::$_debug)
			{
				dump("Object group right:");
				dump($object_group_right);
			}
			
			if(!is_null($object_group_right))
			{
				if($object_group_right->get_update())
				{
					self::$_time += microtime(true) - $time;
					return self::UPDATE_RIGHT;
				}
				elseif($object_group_right->get_write())
				{
					$right = self::WRITE_RIGHT;
				}
				elseif($object_group_right->get_read())
				{
					$right = self::READ_RIGHT;
				}
			}
				
			if(self::$_debug)
			{
				dump("Right:");
				dump($right);
			}
		}
		*/
	
		if(self::$_debug)
		{
			dump("///----- RETURN: " . $right . " -----///");
		}
		
		self::$_time += microtime(true) - $time;
		return $right;
	}
	
	public function add_location_object_user_right($location, $user_id, $object_id, $right)
	{
		if(self::$_debug)
		{
			dump("///----- ADD LOCATION OBJECT USER RIGHT -----///");
		}
		
		$data = array();
		
		if(!is_numeric($location))
		{
			$data["location_id"] = $this->get_data_manager()->retrieve_location_right_by_location($location)->get_id();
		}
		else
		{
			$data["location_id"] = $location;
		}
		
		$data["user_id"] = $user_id;
		$data["object_id"] = $object_id;
		if($right == self::UPDATE_RIGHT)
		{
			$data["update"] = 1;
			$data["read"] = 1;
			$data["write"] = 1;
		}
		elseif($right == self::WRITE_RIGHT)
		{
			$data["update"] = 0;
			$data["read"] = 1;
			$data["write"] = 1;
		}
		elseif($right == self::READ_RIGHT)
		{
			$data["update"] = 0;
			$data["read"] = 1;
			$data["write"] = 0;
		}
	
		if(self::$_debug)
		{
			dump("Location object user right to add:");
			dump(new LocationObjectUserRight($data));
		}
			
		$this->get_data_manager()->insert_location_object_user_right(new LocationObjectUserRight($data));
		if(self::$_debug)
		{
			$this->get_data_manager()->insert_location_object_user_right(new LocationObjectUserRight($data));
			$mysql_result = mysql_errno() == 0;
			dump("///----- RESULT: " . $mysql_result . " -----///");
		}
	}
	
	public function delete_location_object_user_right($location, $user_id, $object_id)
	{
		$time = microtime(true);
		if(self::$_debug)
		{
			dump("///----- DELETE LOCATION OBJECT USER RIGHT -----///");
		}
		$right = new LocationObjectUserRight();
		
		if(!is_numeric($location))
		{
			$right->set_location_id($this->get_data_manager()->retrieve_location_right_by_location($location)->get_id());
		}
		else
		{
			$right->set_location_id($location);
		}
		$right->set_object_id($object_id);
		$right->set_user_id($user_id);
		
		if(self::$_debug)
		{
			dump("Location object user right to delete:");
			dump($right);
		}
		
		self::$_time += microtime(true) - $time;
		return $this->get_data_manager()->delete_location_object_user_right($right);
		
		if(self::$_debug)
		{
			$mysql_result = mysql_errno() == 0;
			dump("///----- RESULT: " . $mysql_result . " -----///");
		}
	}
	
	public function delete_location_user_right($location, $user_id)
	{
		$time = microtime(true);
		if(self::$_debug)
		{
			dump("///----- DELETE LOCATION OBJECT USER RIGHT -----///");
		}
		
		$right = new LocationUserRight();
		
		if(!is_numeric($location))
			$right->set_location_id($this->get_data_manager()->retrieve_location_right_by_location($location)->get_id());
		else
			$right->set_location_id($location);
		$right->set_user_id($user_id);
		
		if(self::$_debug)
		{
			dump("Location user righst to delete:");
			dump($right);
		}
		
		self::$_time += microtime(true) - $time;
		$this->get_data_manager()->delete_location_user_right($right);
		if(self::$_debug)
		{
			$mysql_result = mysql_errno() == 0;
			dump("///----- RESULT: " . $mysql_result . " -----///");
		}
	}
	
	public function delete_location_object_user_rights($location, $object_id)
	{
		$time = microtime(true);
		if(self::$_debug)
		{
			dump("///----- DELETE LOCATION OBJECT USER RIGHT -----///");
		}
		
		$right = new LocationObjectUserRight();
		
		if(!is_numeric($location))
			$right->set_location_id($this->get_data_manager()->retrieve_location_right_by_location($location)->get_id());
		else
			$right->set_location_id($location);
		$right->set_object_id($object_id);
		
		if(self::$_debug)
		{
			dump("Location object user righst to delete:");
			dump($right);
		}
		
		self::$_time += microtime(true) - $time;
		$this->get_data_manager()->delete_location_object_user_rights($right);
		if(self::$_debug)
		{
			$mysql_result = mysql_errno() == 0;
			dump("///----- RESULT: " . $mysql_result . " -----///");
		}
	}
	
	public function retrieve_location_object_meta_data($location_id, $object_id, $key)
	{
		return $this->get_data_manager()->retrieve_location_object_meta_data($location_id, $object_id, $key);
	}
	
	public function add_location_object_meta_data($location_id, $object_id, $key, $value)
	{
		$meta_data = new LocationObjectMetaData();
		$meta_data->set_location_id($location_id);
		$meta_data->set_object_id($object_id);
		$meta_data->set_value($value);
		$meta_data->set_key($key);
		$this->get_data_manager()->delete_location_object_meta_data($location_id, $object_id, $key);
		$this->get_data_manager()->insert_location_object_meta_data($meta_data);
	}
	
	public function filter_array($array, $location, $user, $right, $exclude_admin_right = false)
	{
		if(self::$_debug)
		{
			dump("///----- FILTER  ARRAY -----///");
		}
		
		if(!is_numeric($location))
		{
			$location = $this->get_data_manager()->retrieve_location_right_by_location($location);
		}
		else
		{
			$location = $this->get_data_manager()->retrieve_location_right($location);
		}
		
		if(self::$_debug)
		{
			dump("Location:");
			dump($location);
		}
		
		$id = $location->get_primary_key();
		
		if(self::$_debug)
		{
			dump("Id:");
			dump($id);
		}
		$new_array = array();
		foreach ($array as $object)
		{
			if($this->check_right_location($right, $location->get_id(), $user, $exclude_admin_right))
			{
				if(self::$_debug)
				{
					dump("check_right_location passed.<br>Object added.");
				}
				$new_array[] = $object;
			}
			elseif($right <= $this->get_right_location_object($location->get_id(), $user, $object->$id(), $exclude_admin_right))
			{
				if(self::$_debug)
				{
					dump("right <= right location object passed.<br>Object added.");
				}
				$new_array[] = $object;
			}				
		}
		
		if(self::$_debug)
		{
			dump("///----- RETURN: " . $new_array . " -----///");
		}
		
		return $new_array;
	}
	
	public function get_location_manager($location_right)
	{
		if($location_right->get_parent_id())
			$location_parent = $this->get_data_manager()->retrieve_location_right($location_right->get_parent_id());
		if($location_right->get_parent_id())
		{
			require_once("pages/".Utilities::convert_camelcase_to_underscore($location_parent->get_location())."/".Utilities::convert_camelcase_to_underscore($location_right->get_location())."/lib/".Utilities::convert_camelcase_to_underscore($location_right->get_location())."_manager.class.php");
			require_once("pages/".Utilities::convert_camelcase_to_underscore($location_parent->get_location())."/lib/".Utilities::convert_camelcase_to_underscore($location_parent->get_location())."_manager.class.php");
		}
		else
			require_once("pages/".Utilities::convert_camelcase_to_underscore($location_right->get_location())."/lib/".Utilities::convert_camelcase_to_underscore($location_right->get_location())."_manager.class.php");
			
		$class = $location_right->get_location()."Manager";
		if($location_right->get_parent_id())
		{
			$parent_class = $location_parent->get_location()."Manager";
			$parent_object_manager = new $parent_class($this->get_user());
			$object_manager = new $class($this->get_user(), $parent_object_manager);
		}
		else
			$object_manager = new $class($this->get_user());
		return $object_manager;
	}
	
	public function get_allowed_objects($location, $user, $exclude_admin_right = false)
	{
		$time = microtime(true);
		if(self::$_debug)
		{
			dump("///----- GET ALLOWED OBJECTS -----///");
		}
			
		if(is_null($user))
		{
			if(self::$_debug)
			{
				dump("User is null.");
			}
			return false;
		}
		elseif($user->is_admin() && !$exclude_admin_right)
		{
			if(self::$_debug)
			{
				dump("User is admin.");
			}
			return -1;
		}
		
		if(!is_numeric($location))
		{
			if(self::$_debug)
			{
				dump("Location variable is string: " . $location);
			}
			$location_id = $this->get_data_manager()->retrieve_location_right_by_location($location)->get_id();
			
		}
		else
		{
			$location_id = $location;
		}
		
		if(self::$_debug)
		{
			dump("Location id: " . $location_id);
			dump("User:");
			dump($user);
		}
		
		if(self::$_debug)
		{
			dump("Starting retrieving right");
		}
		
		$location_user_right = $this->get_data_manager()->retrieve_location_user_right($location_id, $user->get_id());
		
		if(self::$_debug)
		{
			dump("Location user right:");
			dump($location_user_right);
		}
	
		$allowed_objects = -1;
		if(self::$_debug && !is_null($location_user_right))
		{
			dump("Allowed objects user right: " . $location_user_right->get_allowed_objects());
			dump($location_user_right);
		}
		
		if(!is_null($location_user_right))
		{
			return $location_user_right->get_allowed_objects();
		}
		
		$group_user_relation = $user->get_group_id();
		if(self::$_debug)
		{
			dump("Groups:");
			dump($group_user_relation);
		}
		$location_group_right = null;
		if(!is_null($user->get_group()))
		{
			$location_group_right = $this->get_data_manager()->retrieve_location_group_right($location_id, $user->get_group_id());
		}
		if(self::$_debug)
		{
			dump("Location group rights:");
		}
		
		if(self::$_debug)
		{
			dump("Location group right:");
			dump($location_group_right);
		}
		/*
		if($allowed_objects != 0)
		{
			foreach($location_group_rights as $location_group_right)
			{
			*/
				if(self::$_debug)
				{
					dump("Allowed objects group right: " . $location_group_right->get_allowed_objects());
					dump($location_group_right);
				}
				if(!is_null($location_group_right)/* && $location_group_right->get_allowed_objects() != 0*/)
				{
					$allowed_objects = /*$allowed_objects>=$location_group_right->get_allowed_objects()?$allowed_objects:*/$location_group_right->get_allowed_objects();
				}
				/*
				elseif(!is_null($location_group_right) && $location_group_right->get_allowed_objects() == 0)
				{
					$allowed_objects = 0;
					break;
				}
			}
		}
		*/
		self::$_time += microtime(true) - $time;
		return $allowed_objects;
	}

	public function set_allowed_objects_user($location_id, $user_id, $allowed_objects)
	{
		$location_user_right = new LocationUserRight();
		$location_user_right->set_location_id($location_id);
		$location_user_right->set_user_id($user_id);
		$location_user_right->set_allowed_objects($allowed_objects);
		$location_user_right->set_read(1);
		$location_user_right->set_write(1);
		$location_user_right->set_update(0);
		return $this->get_data_manager()->insert_location_user_right($location_user_right);
	}
	
}

?>