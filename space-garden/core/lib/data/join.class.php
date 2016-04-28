<?php

class Join
{
	const MAIN_TABLE = "main_table";
	
	private $table_name;
	private $alias;
	private $key;
	private $join_type;
	private $join_table;
	
	function __construct($table_name, $alias, $key, $join_type, $join_table = self::MAIN_TABLE)
	{
		$this->table_name = $table_name;
		$this->alias = $alias;
		$this->key = $key;
		$this->join_type = $join_type;
		$this->join_table = $join_table;
	}
	
	public function set_table_name($table_name){ $this->table_name = $table_name; }
	public function get_table_name(){ return $this->table_name; }
	public function set_alias($alias){ $this->alias = $alias; }
	public function get_alias(){ return $this->alias; }
	public function set_key($key){ $this->key = $key; }
	public function get_key(){ return $this->key; }
	public function set_join_type($join_type){ $this->join_type = $join_type; }
	public function get_join_type(){ return $this->join_type; }
	public function set_join_table($join_table){ $this->join_table = $join_table; }
	public function get_join_table(){ return $this->join_table; }
		
}


?>