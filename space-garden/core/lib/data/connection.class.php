<?php
class Connection
{
	private $_hostname;
	private $_username;
	private $_password;

	private $_database;

	private $_connectionId;
	private $_isOpen;
	private $_resultQuery;

	private $_errors = array(
					'host'=>'Host not defined!',
					'db'=>'Database not defined!',
					'user'=>'User not defined!',
					'pwd'=>'Password not defined!',
					'conn'=>'Bad Connection!',
					'query'=>'Bad Query, reason: ',
					'kind'=>'Type resultquery not passed!'
					);

	function __construct($Host, $User, $Pwd, $DB)
	{
		$this->_isOpen = false;
		
		if( $Host ) {
			$this->_hostname = $Host;
		}
		else {
			throw new Exception($this->_errors['host']);
		}

		if( $DB ) {
			$this->_database = $DB;
		}
		else {
			throw new Exception($this->_errors['db']);
		}

		if( $User ) {
			$this->_username = $User;
		}
		else {
			throw new Exception($this->_errors['user']);
		}


		if( $Pwd ) {
			$this->_password = $Pwd;
		}
		else {
			throw new Exception($this->_errors['pwd']);
		}
	}
	public function open()
	{
		// connection is created, if successful ...
		$this->_connectionId = mysql_connect($this->_hostname, $this->_username, $this->_password);
		if (!is_resource($this->_connectionId)){
			throw new Exception($this->_errors['conn']);
		}
		else
		{
			//... database is selected
			if (!mysql_select_db($this->_database,$this->_connectionId)){
				throw new Exception($this->_database.': '.$this->_errors['conn']);
			}
			else{
				$this->_isOpen=true;
			}
        }
	}
	public function executesql($aSql,$kind){
		//query is executed and result is stored in resultQuery
		$resultArray = array();
		$this->_resultQuery = mysql_query($aSql);
 		if (is_resource($this->_resultQuery)){
		/*	
			Record by record will be read,
			depending on the kind of method the result will be:
			fetch_object: an object with the names of columns as properties
			fetch_array: an array with or a numeric index or an associatif array,
							with the column name as key-value.
		*/
				switch ($kind){
					case 'O':
						while($row=mysql_fetch_object($this->_resultQuery)){
							$resultArray[]=$row;
							};
						break;
					case 'A':
						while($row=mysql_fetch_assoc($this->_resultQuery)){
							$resultArray[]=$row;
							};
						break;
					case 'N':
						while($row=mysql_fetch_array($this->_resultQuery,MYSQL_NUM)){
							$resultArray[]=$row;
							};
						break;
					case 'B':
						while($row=mysql_fetch_array($this->_resultQuery,MYSQL_BOTH)){
							$resultArray[]=$row;
							};
						break;
					case 'COUNTROWS':
						return mysql_num_rows($this->_resultQuery);
						break;
					default:  throw new Exception($this->_errors['kind'].mysql_error());
				}
				if (isset($resultArray)){
					return $resultArray;
				}
				else{
					return false;
				}
		}
		else{
			switch ($kind)
			{
				case 'INSERT': return mysql_insert_id();
				case 'UPDATE': return $this->_resultQuery;
				case 'DELETE': return $this->_resultQuery;
				default: throw new Exception($this->_errors['kind'].mysql_error());
			}
			if (mysql_error()){
				throw new Exception($this->_errors['query'].mysql_error());
			}
		}
	}


}
?>
