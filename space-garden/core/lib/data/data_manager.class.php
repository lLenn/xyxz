<?php

require_once Path::get_path().'core/lib/data/connection.class.php';
require_once Path::get_path().'core/lib/data/update_object.class.php';
require_once Path::get_path().'core/lib/data/join.class.php';

class DataManager
{
	CONST MANY_RECORDS = 'many_records';
	CONST ONE_RECORD = 'one_record';

	protected static $_connection = null;
	//Singleton pattern
    protected static $_instance = null;
	private static $loaded_data = array();
	
	function __construct()
	{
		if(is_null(self::$_connection))
		{
			require Path::get_path().'core/lib/data/db.inc';
			self::$_connection = new Connection($server,$user,$password,$database);
			self::$_connection->open();
		}
	}
	
	public function retrieve_by_id($table_name,$class_name,$id)
	{
		$condition = 'id = "' . $id . '"';
		return $this->retrieve($table_name,$class_name,'',self::ONE_RECORD,$condition);
	}
	
	public function retrieve($table,$class_name,$order='',$amount_of_records=self::MANY_RECORDS,$conditions='',$limit='',$select='*')
	{
		$query = 'SELECT ' . $select . ' FROM';
		if(!is_array($table))
		{
			$query .= ' `'.$table.'`';
		}
		else
		{
			$join_query = "";
			$aliases = array();
			while(count($table) > 0)
			{
				foreach($table as $index => $t)
				{
					if($t->get_join_type() == Join::MAIN_TABLE)
					{
						$query .= ' `'.$t->get_table_name().'` AS ' . $t->get_alias();
						$aliases[Join::MAIN_TABLE] = $t->get_alias();
						unset($table[$index]);
					}
					elseif(key_exists($t->get_join_table(), $aliases))
					{
						$join_query .= " " . $t->get_join_type() . " `" . $t->get_table_name() . "` AS " . $t->get_alias() . " ON " . $aliases[$t->get_join_table()] . "." . $t->get_key() . " = " . $t->get_alias() . "." . $t->get_key();
						unset($table[$index]);
					}
				}
			}
			$query .= $join_query;
		}
		
		if($conditions!='')
		{
			$query .= ' WHERE ';
			$query .= $conditions;
		}
		if($order!='')
		{
			$query .= ' ORDER BY ';
			$query .= $order;
		}
		if($limit!='')
		{
			$query .= ' LIMIT ';
			$query .= $limit;
		}

		$objects = $this->retrieve_data($query);
		$count = count($objects);
		switch($amount_of_records)
		{
			case self::MANY_RECORDS:
				return $this->Mapping($objects,$class_name);
				break;
			case self::ONE_RECORD:
				if($count==1)
					return $this->Map($objects[0],$class_name);
				elseif($count==0)
					return null;
				else
					throw new Exception("Found too many records: ".$count);
				break;
		}
	}
	
	public function retrieve_from_sql_query($sql_query,$class_name,$amount_of_records=self::MANY_RECORDS)
	{
		$objects = $this->retrieve_data($sql_query);
		$count = count($objects);
		switch($amount_of_records)
		{
			case self::MANY_RECORDS:
				return $this->Mapping($objects,$class_name);
				break;
			case self::ONE_RECORD:
				if($count==1)
					return $this->Map($objects[0],$class_name);
				elseif($count==0)
					return null;
				else
					throw new Exception("Found too many records: ".$count);
				break;
		}
	}
	
	public function delete_by_id($table_name, $id)
	{
		$condition = 'id="'.$id.'"';
		return $this->delete($table_name,$condition);
	}
	
	public function delete($table_name, $conditions)
	{
		$query = "DELETE FROM `".$table_name."`";
		if($conditions!='')
		{
			$query .= ' WHERE ';
			$query .= $conditions;
		}
		
		return self::$_connection->executesql($query,'DELETE');
	}
	
	public function update_by_id($table_name,$object)
	{
		$condition = "id = '" . $object->get_id() . "'";
		return $this->update($table_name,$object,$condition);
	}
	
	public function update($table_name,$object,$conditions='')
	{
		$object_properties = $object->get_properties();
		$size = count($object_properties);
		
		$query = 'UPDATE `'.$table_name.'` SET ';
		$i = 1;
		foreach($object_properties as $property => $value)
		{
			$query .= '`'.$property.'` = "'.$value.'"';
			if($i<$size)
				$query .= ', ';
			$i++;
		}
		
		if($conditions!='')
		{
			$query .= ' WHERE ';
			$query .= $conditions;
		}

		return self::$_connection->executesql($query,'UPDATE');
	}
	
	public function insert($table_name,$object)
	{
		$object_properties = $object->get_properties();
		$size = count($object_properties);
		
		$query = 'INSERT INTO `' . $table_name . '` (';
		$i = 1;
		foreach($object_properties as $property => $value)
		{
			$query .= '`'.$property.'`';
			if($i<$size)
				$query .= ', ';
			$i++;
		}
		
		$query .= ') VALUES(';
		$i = 1;
		foreach($object_properties as $value)
		{
			$query .= '"'.$value.'"';
			if($i<$size)
				$query .= ', ';
			$i++;
		}
		
		$query .= ');';
		return self::$_connection->executesql($query,'INSERT');
	}
	
	public function count($table_name, $conditions = '')
	{
		$query = 'SELECT * FROM `'.$table_name.'`';
		if($conditions!='')
		{
			$query .= ' WHERE ';
			$query .= $conditions;
		}
		return self::$_connection->executesql($query,'COUNTROWS');
	}
	
    static function parse_checkbox_value($value = null)
    {
    	if(isset($value) && ($value == 1 || $value == 'on'))
        {
        	return 1;
        }
        else
        {
        	return 0;
        }
   	}

	//MAPPERS
	protected function Mapping($queryData, $class)
	{
		$resultArray = array();
		foreach($queryData as $data)
		{
			$resultArray[] = $this->Map($data, $class);
		}
		return $resultArray;
	}
	
	protected function Map($data, $class)
	{
		if(!is_null($class))
			return	$object = new $class($data);
		else
			return $data;
	}
	
	public function retrieve_data($sql_string)
	{
		if(!array_key_exists($sql_string, self::$loaded_data))
			self::$loaded_data[$sql_string] = self::$_connection->executesql($sql_string,'O');
		return self::$loaded_data[$sql_string];
	}
	
	public function get_loaded_data()
	{
		return self::$loaded_data;
	}
}

?>