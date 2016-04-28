<?php

require_once Path :: get_path() . 'core/lib/html/html.class.php';
require_once Path :: get_path() . 'core/lib/html/column.class.php';

/*
 * TODO: Add sort, caption;
 * 		 Header utility underscore to capital
 * 		 Order !done
 * 		 Implement for url created with .htaccess example http://site/page-{action}-{id}.html like msveeklo.be
 * 		 Auto-save
 * 		 Reset script load !done
 */

class Table extends Html
{
	private static $invalid_get_functions = array("get_properties");
	private static $used_ids = array();
	private static $editable_script_added = false;
	private static $table_script_added = false;
	private static $sortable_script_added = false;
	private $table_id = null;
    	private $table_body_id = null;
	
	private $data_provider = null;
	private $data_class = null;
	private $columns = null;
	private $ids = null;
	private $no_data_message = '';
	
	private $row_link_url = null;
	private $row_link_actions = null;
	
	private $delete_url = null;
	private $delete_actions = null;
	private $delete_message = '';
	private $delete_title = 'Verwijderen';
	
	private $caption = null;
	private $has_limit = false;
	private $start = null;
	private $default_limit = null;
	private $limit_choices = array();
	private $limit_prev_message = "";
	private $limit_next_message = "";
	
	private $editable = false;
	private $editable_id = "";
	private $editable_delete = true;
	private $editable_save_message = "Opslaan";
	private $editable_reset_message = "Reset";
	private $classes_to_load = array();
	
	private $sortable = false;
	
	private $row_count = 0;

	public function set_table_id($table_id){ $this->table_id = $table_id; }
	public function get_table_id(){ return $this->table_id; }
    public function set_table_body_id($table_body_id){ $this->table_body_id = $table_body_id; }
    public function get_table_body_id(){ return $this->table_body_id; }
	public function set_data_provider($data_provider, $data_class = null)
	{
		if((is_array($data_provider) && (empty($data_provider) || is_object($data_provider[0]))) ||
		   (is_string($data_provider) && strtolower(substr($data_provider, 0, 6)) == "select" && !is_null($data_class) && is_string($data_class) && class_exists($data_class)))
		{
			$this->data_provider = $data_provider;
			$this->data_class = $data_class;
		}
		else
		{
			throw new Exception("Please give a valid data provider, either an array with objects or a sql select query with a valid class.");
		}
	}
	public function get_data_provider(){ return $this->data_provider; }
	public function set_data_class($data_class){ $this->data_class = $data_class; }
	public function get_data_class(){ return $this->data_class; }
	public function set_columns($columns){ $this->columns = $columns; }
	public function get_columns(){ return $this->columns; }
	public function set_ids($ids)
	{ 
		if(!is_array($ids))
		{
			$ids = array($ids);
		}
		$this->ids = $ids; 
	}
	public function get_ids(){ return $this->ids; }
	public function get_ids_url($row_object)
	{
		$url = "";
		foreach($this->ids as $id)
		{
			$function = "get_" . $id;
			$url .= "&" . $row_object->$function();
		}
		return substr($url, 1);
	}
	public function set_no_data_message($no_data_message){ $this->no_data_message = $no_data_message; }
	public function get_no_data_message(){ return $this->no_data_message; }
	
	public function set_row_link($row_link_url, $row_link_actions)
	{ 
		$this->row_link_url = $row_link_url;
		$this->set_row_link_actions($row_link_actions);
	}
	
	public function set_row_link_url($row_link_url){ $this->row_link_url = $row_link_url; }
	public function get_row_link_url(){ return $this->row_link_url; }
	public function set_row_link_actions($row_link_actions)
	{ 
		if(!is_array($row_link_actions))
		{
			$row_link_actions = array($row_link_actions);
		}
		$this->row_link_actions = $row_link_actions; 
	}
	public function get_row_link_actions(){ return $this->row_link_actions; }
	public function get_row_link_actions_url()
	{
		$url = "";
		foreach($this->row_link_actions as $action)
		{
			$url .= "&" . $action;
		}
		return substr($url, 1);
	}
	
	public function set_delete_button($delete_url, $delete_actions, $delete_title = '', $delete_message = '')
	{ 
		$this->delete_url = $delete_url;
		$this->set_delete_actions($delete_actions);
		$this->delete_title = $delete_title;
		$this->delete_message = $delete_message;
	}
	public function set_delete_url($delete_url){ $this->delete_url = $delete_url; }
	public function get_delete_url(){ return $this->delete_url; }
	public function set_delete_actions($delete_actions)
	{ 
		if(!is_array($delete_actions))
		{
			$delete_actions = array($delete_actions);
		}
		$this->delete_actions = $delete_actions;
	}
	public function get_delete_actions(){ return $this->delete_actions; }
	public function get_delete_actions_url()
	{
		$url = "";
		foreach($this->delete_actions as $action)
		{
			$url .= "&" . $action;
		}
		return substr($url, 1);
	}
	public function set_delete_title($delete_title){ $this->delete_title = $delete_title; }
	public function get_delete_title(){ return $this->delete_title; }
	public function set_delete_message($delete_message){ $this->delete_message = $delete_message; }
	public function get_delete_message(){ return $this->delete_message; }
	
	public function set_caption($caption){ $this->caption = $caption; }
	public function get_caption(){ return $this->caption; }
	public function has_limit($has_limit = null)
	{ 
		if(is_null($has_limit))
		{
			return $this->has_limit;
		}
		else
		{
			$this->has_limit = $has_limit;
		} 
	}
	public function set_start($start){ $this->start = $start; }
	public function get_start()
	{
		if($this->has_limit() && !is_null(Request::get('start')) && is_numeric(Request::get('start')))
		{
			$this->start = Request::get('start');
		}
		
		if(($this->has_limit() && is_null($this->start)) || $this->start <= 0)
		{
			return 0;
		}
		return $this->start;
	}
	public function set_default_limit($default_limit)
	{ 
		$this->default_limit = $default_limit;
	}
	public function get_default_limit($default_limit)
	{ 
		return $this->default_limit;
	}
	public function get_limit()
	{
		if($this->has_limit() && !is_null(Request::get('limit')) && is_numeric(Request::get('limit')))
		{
			Session::register('limit', Request::get('limit'));
		}
		elseif($this->has_limit() && is_null(Session::retrieve('limit')) && !is_null($this->default_limit) && is_numeric($this->default_limit))
		{
			Session::register('limit', $this->default_limit);
		}
		elseif($this->has_limit() && is_null(Session::retrieve('limit')))
		{
			Session::register('limit', 20);
		}
		
		return Session::retrieve('limit');
	}
	public function set_limit_choices($limit_choices){ $this->limit_choices = $limit_choices; }
	public function get_limit_choices()
	{ 
		if(empty($this->limit_choices))
		{
			return array(2,20,50,100,150);
		}
		else
		{
			return $this->limit_choices;
		}
	}
	public function add_limit_choice($limit_choice){ $this->limit_choice[$limit_choice] = $limit_choice; }
	public function remove_limit_choice($limit_choice)
	{ 
		if(isset($this->limit_choices[$limit_choice]))
		{
			unset($this->limit_choices[$limit_choice]);
		} 
	}
	public function set_limit_prev_message($limit_prev_message){ $this->limit_prev_message = $limit_prev_message; }
	public function get_limit_prev_message(){ return $this->limit_prev_message; }
	public function set_limit_next_message($limit_next_message){ $this->limit_next_message = $limit_next_message; }
	public function get_limit_next_message(){ return $this->limit_next_message; }
	public function set_editable($editable){ $this->editable = $editable; }
	public function is_editable()
	{
		if($this->editable && $this->editable_id != "")
		{
			return true; 
		}
		elseif($this->editable && $this->editable_id == "")
		{
			throw new Exception("Please also specify an editable id when setting the table as editable");
		}
		else
		{
			return false;
		}
	}
	public function set_editable_id($editable_id){ $this->editable_id = $editable_id; }
	public function get_editable_id(){ return $this->editable_id; }
	public function set_editable_delete($editable_delete){ $this->editable_delete = $editable_delete; }
	public function get_editable_delete(){ return $this->editable_delete; }
	public function set_editable_save_message($editable_save_message){ $this->editable_save_message = $editable_save_message; }
	public function get_editable_save_message(){ return $this->editable_save_message; }
	public function set_editable_reset_message($editable_reset_message){ $this->editable_reset_message = $editable_reset_message; }
	public function get_editable_reset_message(){ return $this->editable_reset_message; }
	public function set_classes_to_load($classes_to_load){ $this->classes_to_load = $classes_to_load; }
	public function get_classes_to_load(){ return $this->classes_to_load; }
	public function add_class_to_load($class){ $this->classes_to_load[] = $class; }
	public function set_sortable($sortable){ $this->sortable = $sortable; }
	public function is_sortable()
	{ 
		if($this->editable && $this->sortable && $this->editable_id != "")
		{
			return true; 
		}
		elseif(!$this->editable && $this->sortable && $this->editable_id != "")
		{
			throw new Exception("Please also set the table as editable in order to make it sortable");
		}
		elseif($this->editable && $this->sortable && $this->editable_id == "")
		{
			throw new Exception("Please also specify an editable id when setting the table as sortable");
		}
		else
		{
			return false;
		}
	}

	function Table($data_provider, $data_class = null)
	{
		do
		{
			$this->table_id = $this->generate_table_id();
		}
		while(in_array($this->table_id, self::$used_ids));
		self::$used_ids[] = $this->table_id;
		$this->set_data_provider($data_provider, $data_class);
	}
	
	public function render_table($reset = false)
	{
		$html = array();
		if(!$reset)
		{
			$html[] = '<div id="table_' . $this->table_id . '">';
		}
		if(!$reset && $this->has_link() && !self::$table_script_added)
		{
			$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'core/javascript/table.js"></script>';
			self::$table_script_added = true;
		}
		if($this->editable)
		{
			if(!$reset && !self::$editable_script_added)
			{
				$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'core/javascript/editable.js"></script>';
				self::$editable_script_added = true;
			}
			$html[] = '<form name="save_all_form_' . $this->table_id . '" action="" method="post">';
			$html[] = '<input class="input_element" type="hidden" name="save_all_' . $this->table_id . '" value="1">';
			$html[] = '<div class="record">';
		}
		if(!$reset && $this->sortable && !self::$sortable_script_added)
		{
			$html[] = '<script type="text/javascript" src="' . Path :: get_url_path() . 'core/javascript/sortable_table.js"></script>';
			self::$sortable_script_added = true;
		}
		if(!is_null($this->caption) && is_string($this->caption))
		{
			$html[] = '<div style="float: left;">' . $this->caption . ' :</div>';
		}
		if(!is_null($this->get_data()) && count($this->get_data()))
		{
			if($this->has_limit())
			{
				$html[] = '<div style="float: right;">';
				$html[] = $this->render_limit_choices();
				$html[] = '</div>';
			}
			
			if((!is_null($this->caption) && is_string($this->caption)) || $this->has_limit())
			{
				$html[] = '<br class="clear_float"/>';
			}

			$row_link_class = '';
			if($this->has_link())
			{
				$row_link_class = ' ' . $this->row_link_url . ' ' . $this->get_row_link_actions_url();
			}
			
			$this->set_attribute("class", $this->get_attribute("class") . $row_link_class);
			$html[] = '<table'.$this->render_attributes().$this->render_style_attributes().'>';
			$html[] = '<thead>';
			$html[] = '<tr>';
			foreach($this->get_columns_to_use() as $column)
			{
				$html[] = '<th>';
				$html[] = $column->get_header(); 
				$html[] = '</th>';
			}
			if($this->has_delete_button() || ($this->editable && $this->editable_delete))
			{
				$html[] = '<th>&nbsp;</th>';
			}
			$html[] = '</tr>';
			$html[] = '</thead>';
			
			$html[] = '<tbody' . (!is_null($this->table_body_id)?' id="' . $this->table_body_id . '"':'') . ($this->sortable?' class="sortable_table" id="sortable_' . $this->table_id . '"':'') . '>';
			$this->row_count = 0;
			$next = false;
			foreach($this->get_data() as $data)
			{
				if(!$this->has_limit() || $this->row_count < $this->get_limit())
				{
					$html[] = $this->render_row($data);
				}
		        else if($this->row_count == $this->get_limit())
		        {
	        		$next = true;
		        }
				$this->row_count++;
			}
			$html[] = '</tbody>';
			$html[] = '</table>';
		}
		else
		{
			$html[] = '<p>' . $this->no_data_message . '</p>';
		}
		
		if($this->has_limit())
		{
			$html[] = "<br/>";
			if($this->get_start() > 0)
			{
				$html[] = "<div style='float: left; margin-left: 2px;'><a href='javascript:;' class='change_start ".($this->get_start() - $this->get_limit())."'> <<< " . $this->get_limit_prev_message() . "</a></div>";
			}
			if($next)
			{
				$html[] = "<div style='float: right; margin-right: 2px;'><a href='javascript:;' class='change_start ".($this->get_start() + $this->get_limit())."'>" . $this->get_limit_next_message() . " >>> </a></div>";
			}
			$html[] = "<br class='clear_float'/>";
		}
		if($this->editable)
		{
			$html[] = '<div class="record_button">';
			$html[] = '<a id="submit_form" class="link_button" href="javascript:;">' . $this->editable_save_message . '</a>';
			$html[] = '</div>';
			$html[] = '<div class="record_button">';
			$html[] = '<a id="reset_form" class="link_button ' . $this->table_id . '" href="javascript:;">' . $this->editable_reset_message . '</a>';
			$html[] = '</div>';
			$html[] = '</div>';
			Session :: register("table_model_" . $this->table_id, serialize($this));
			Session :: register("table_model_classes_to_load_" . $this->table_id, serialize($this->classes_to_load));
			$html[] = '</form>';
		}
		if(!$reset)
		{
			$html[] = '</div>';
		}
		return implode("\n", $html);
	}
	
	private function render_row($row_object)
	{
		$link_id = '';
		if($this->has_link() || $this->is_editable())
		{
			$link_id = $this->get_ids_url($row_object);
		}
		
		$html = array();           
		$html[] = '<tr class="row' . ($this->has_link()?' row_link ':' ') . ($this->row_count%2==0?'even ':'odd ') . $link_id . '">';
		
		foreach($this->get_columns_to_use() as $column)
		{
			$function = "get_" . $column->get_name();
			$class = "";
			if($column->is_editable())
			{
				$class = ' class="edit_cell"';
			}
			if($column->is_order())
			{
				$class = ' class="order_cell"';
			}
			$html[] = '<td' . $class . $column->render_attributes() . $column->render_style_attributes() . '>';
			if($column->is_editable())
			{
				$html[] = '<div class="text">';
			}
			$html[] = $row_object->$function();
			if($column->is_editable())
			{
				$html[] = '</div>';
				$html[] = '<div class="input" style="display: none">';
				$html[] = $this->render_editable_input($column, $row_object);
				$html[] = '</div>';
			}
			$html[] = '</td>';
		}
		
		if($this->has_delete_button() && !$this->is_editable())
		{
			$html[] = '<td class="tool_btn delete ' .  $this->get_delete_actions_url() . ' ' . $this->delete_url . ' ' . $this->delete_message . '">';
			$html[] = '<img title="' . $this->delete_title . '" src="layout/images/buttons/delete.gif">';
			$html[] = '</td>';
		}
		if($this->is_editable())
		{
			if($this->editable_delete)
			{
				$html[] = '<td class="tool_btn">';
				$html[] = '<img class="delete_record" src="' . Path :: get_url_path() . 'layout/images/buttons/delete.gif" title="' . $this->delete_title . '" style="border: 0">';
				$html[] = '</td>';
			}
	
			$name = 'name="' . $this->editable_id . '_' . ($this->row_count+1) . '"';
			$html[] = '<input class="editable_input" type="hidden" ' . $name . '" value="'. $link_id . '">';
		}
		$html[] = '</tr>';
		return implode("\n", $html);
	}
	
	private function render_limit_choices()
	{
		$html = array();
		$html[] = '<select name="limit" style="margin-right: 2px">';
		foreach($this->get_limit_choices() as $limit)
		{
			$html[] = '<option value="'.$limit.'"'.($this->get_limit()==$limit?'selected':'').'>'.$limit.'</option>';
		}
		$html[] = '</select>';
		return implode("\n", $html);
	}
	
	private function render_editable_input($column, $object)
	{
		$html = array();
		$function = "get_" . $column->get_editable_name();
		$name = 'name="' . $column->get_editable_name() . '_' . ($this->row_count+1) . '"';
		switch($column->get_editable_type())
		{
			case "text": $value = ' value="' . $object->$function() . '"';
				break;
			case "checkbox": $value = $object->$function()?'checked="checked"':'';
				break;
			case "default": throw Exception("Wrong editable_type defined");
				break;
		}
		$html[] = '<input class="editable_input input_element" type="' . $column->get_editable_type() . '"' . $value . " " . $name . "/>";
		return implode("\n", $html);
	}
	
	private function get_data()
	{
		if(!is_array($this->data_provider))
		{
			$data_manager = new DataManager();
			$query_limit = "";	
			if(!is_null($this->get_start()))
			{
				$query_limit = " LIMIT " . $this->get_start() . ", " . ($this->get_limit() + 1);
			}
			preg_match("/[lL][iI][mM][iI][tT][ ]+[0-9]+[ ]*[,][ ]*[0-9]+[ ]*/", $this->data_provider, $matches);
			if(count($matches)==1)
			{
				$this->data_provider = preg_replace("/" . $matches[0] . "/", $query_limit, $this->data_provider);
			}
			else
			{
				$this->data_provider .= $query_limit;
			}
			$this->data_provider = $data_manager->retrieve_from_sql_query($this->data_provider, $this->data_class, DataManager::MANY_RECORDS);
		}
		
		return $this->data_provider;
	}
	
	private function get_columns_to_use()
	{
		if(is_null($this->columns))
		{
			$columns = array();
			foreach (get_class_methods($this->get_class_name()) as $fun)
			{
				if(strtolower(substr($fun,0,4)) == "get_" && !in_array(strtolower($fun), self::$invalid_get_functions))
				{
					$columns[] = new Column(substr($fun,4), substr($fun,4));
				}
			}
			return $columns;
		}
		else
		{
			return $this->columns;
		}
	}
	
	private function get_class_name()
	{
		return get_class($this->data_provider[0]);
	}
	
	private function has_link()
	{
		$ids = $this->get_ids();
		$actions = $this->get_row_link_actions();
		if(!is_null($this->get_row_link_url()) && !empty($ids) && !empty($actions))
		{
			return true;
		}
		elseif(!is_null($this->get_row_link_url()) && empty($ids) && empty($actions))
		{
			throw new Exception("Please also specify an id and row link action when setting a row link");
		}
		elseif(!is_null($this->get_row_link_url()) && count($ids) != count($actions))
		{
			throw new Exception("Please also specify an equal amount of row link ids as actions");
		}
		else
		{
			return false;
		}
	}
	
	private function has_delete_button()
	{
		$ids = $this->get_ids();
		$actions = $this->get_delete_actions();
		if(!is_null($this->get_delete_url()) && !empty($ids) && !empty($actions))
		{
			return true;
		}
		elseif(!is_null($this->get_delete_url()) && empty($ids) && empty($actions))
		{
			throw new Exception("Please also specify an id and delete action when setting a delete button");
		}
		elseif(!is_null($this->get_delete_url()) && count($ids) != count($actions))
		{
			throw new Exception("Please also specify an equal amount of ids as actions when setting a delete button");
		}
		else
		{
			return false;
		}
	}
	
	private function generate_table_id()
	{
		$charset = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$table_id = "";
		for($i=0;$i<8;$i++)
		{
			$table_id .= $charset{rand(0,61)};
		}
		return $table_id;
	}
}

?>