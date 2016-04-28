<?php

class Url
{
	public static function create_url($actions = null)
	{
		if(!is_array($actions) && $actions != null)
			$actions = array($actions);

		$url = Path :: get_url_path() . "index.php";
		if($actions != null)
		{
			if(count($actions) > 0)
				$url .= "?";
			foreach ($actions as $index => $action)
				$url .= $index . "=" . $action . "&";
			$url = substr($url, 0, strlen($url) - 1);
		}
		return $url;
	}
}

?>