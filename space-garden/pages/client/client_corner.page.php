<?php

class ClientCorner
{

	private $manager;
	
	function ClientCorner($manager)
	{
		$this->manager = $manager;
	}
	
	public function get_html()
	{	
		$html = array();
		$html[] = "<p class='title'>" . Language::get_instance()->translate("client_corner") . "</p>";
		$html[] = "<p style='margin-left: 70px; margin-top: 40px; spacing: 2px;'>";
		$html[] = "<a href='http://www.greeni-shop.eu/'><img src='" . Path :: get_url_path() . "layout/images/logo_greeni_shop.jpg'><br><br>";
		$html[] = Language::get_instance()->translate("greeni_shop_description") . "</a><br><br><br>";
		$html[] = "<a href='http://www.greeni.eu/'><img src='" . Path :: get_url_path() . "layout/images/logo_greeni.jpg'><br><br>";
		$html[] = Language::get_instance()->translate("greeni_description") . "</a></p>";
		return implode("\n", $html);
	}
}
?>