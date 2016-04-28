<?php

class Error
{	
	private $result;
	private $message;
	private $debug_message;
	private static $_instance = null;
	
	function Error($result = true, $message = "", $debug_message = "")
	{
		$this->result = $result;
		$this->message = $message;
		$this->debug_message = $debug_message;
	}
	
	public static function get_instance()
	{
		if(is_null(self::$_instance))
		{
			self::$_instance = new Error();
		}
		return self::$_instance;
	}
	
	public static function new_instance()
	{
		self::$_instance = new Error();
	}
	
	public function get_result() { return $this->result; }
	public function set_result($result) { $this->result = $result; }
	public function add_result($result) { $this->result &= $result; }
	public function get_message() { return $this->message; }
	public function set_message($message) { $this->message = $message; }
	public function append_message($message) 
	{
		if($this->message != "")
		{
			$this->message .= "<br>";
		}
		
		$this->message .= $message; 
	}
	public function get_debug_message() { return $this->debug_message; }
	public function set_debug_message($debug_message) { $this->debug_message = $debug_message; }
	public function append_debug_message($mysql, $debug_message = "") 
	{
		if($this->debug_message != "")
		{
			$this->debug_message .= "<br>";
		}
		
		if($debug_message != "")
		{
			$this->debug_message .= $debug_message; 
		}
		
		if($mysql)
		{
			//$this->debug_message .= "<br>" . mysqli_errno().": ".mysqli_error(); 
		}
	}
	public function print_error($return = false)
	{
		$html = array();
		$html[] = "<p class='error'>".Error::get_instance()->get_message()."</p>";
	    if(isset($_GET["debug"]))
	    {
	    	$html[] = "<p class='error'>".Error::get_instance()->get_debug_message()."</p>";
	    }
	    
	    if($return)
	    {
	    	return implode("\n", $html);
	    }
	    else
	    {
	    	echo implode("\n", $html);
	    }
	}
}

?>