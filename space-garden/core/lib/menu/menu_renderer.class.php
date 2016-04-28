<?php

require_once Path :: get_path() . 'core/lib/menu/menu.class.php';
require_once Path :: get_path() . 'core/lib/menu/menu_item.class.php';

class MenuRenderer
{
	public static function get_menu()
	{
		$menu = new Menu();
		$menu->set_name("main");
		$menu->set_direction("horizontal");
		

		$menu_items = array();
		$dynamic_pages = DynamicPageDataManager::instance()->retrieve_dynamic_page_titles();
		foreach ($dynamic_pages as $page)
		{
			$menu_item = new MenuItem();
			$menu_item->set_name($page->title);
			$menu_item->set_url(Url::create_url(array("page" => $page->id)));
			$menu_items[] = $menu_item;
		}
		$menu->set_menu_items($menu_items);
		return self::get_menu_structure($menu, false);
	}
	
	public static function get_client_menu()
	{
		$menu = new Menu();
		$menu->set_name("client");
		$menu->set_direction("horizontal");
		

		$menu_items = array();
		if(is_null(WebApplication::get_user()))
		{
			$menu_item = new MenuItem();
			$menu_item->set_name(Language::get_instance()->translate("login"));
			$menu_item->set_url(Url::create_url(array("page" => "login")));
			$menu_items[] = $menu_item;
		}
		else
		{
			if(WebApplication::get_user()->is_admin())
			{
				$menu_item = new MenuItem();
				$menu_item->set_name(Language::get_instance()->translate("products"));
				$menu_item->set_url(Url::create_url(array("page" => "browse_products")));
				
				$menu_prod = new Menu();
				$menu_prod->set_name("product_sub");
				$menu_prod->set_direction("vertical");
				
				$menu_item_prod = new MenuItem();
				$menu_item_prod->set_name(Language::get_instance()->translate("add_product"));
				$menu_item_prod->set_url(Url::create_url(array("page" => "add_product")));
				
				$menu_prod->set_menu_items(array($menu_item_prod));
				$menu_item->set_sub_menu($menu_prod);
				$menu_items[] = $menu_item;
				
				$menu_item = new MenuItem();
				$menu_item->set_name(Language::get_instance()->translate("pages"));
				$menu_item->set_url(Url::create_url(array("page" => "browse_pages")));
				$menu_items[] = $menu_item;
			}
			else
			{
				$menu_item = new MenuItem();
				$menu_item->set_name(Language::get_instance()->translate("client_corner"));
				$menu_item->set_url(Url::create_url(array("page" => "client_corner")));
				$menu_items[] = $menu_item;
			}
			
			$menu_item = new MenuItem();
			$menu_item->set_name(Language::get_instance()->translate("logout"));
			$menu_item->set_url(Url::create_url(array("page" => "logout")));
			$menu_items[] = $menu_item;
		}
		$menu->set_menu_items($menu_items);
		return self::get_menu_structure($menu, true);
	}
	
	public static function get_menu_structure($menu, $render_sub_menu, $sub_menu = false)
	{
		$html = array();
		$html[] = '<ul class="'.($sub_menu?'sub_':'').'menu ' . (($menu->get_direction() == "horizontal")?"menu_horizontal":"menu_vertical") . '" id="menu_'.$menu->get_name().'">';
		foreach($menu->get_menu_items() as $item)
		{
			$selected = "";
			if($item->get_url() == Path::get_location_url())
			{
				$selected = " selected";
			}
			$html[] = '<li class="' .  $selected . '">';
			$url = $item->get_url();
			if(substr($item->get_url(), 0, 5) ==  "Menu:")
			{
				$url = "javascript:;";
			}
			$html[] = '<a id="menu_item_'.$item->get_name().'" href="' . $url . '"/>'.$item->get_name().'</a>';
			$html[] = '<br class="clear_float">';
			if($render_sub_menu && is_object($item->get_sub_menu()) && get_class($item->get_sub_menu()) == 'Menu')
			{
				$html [] = self :: get_menu_structure($item->get_sub_menu(), $render_sub_menu, true);
			}
			$html[] = '</li>';
		}
		$html[] = '</ul>';
		return implode("\n", $html);
	}
}
?>