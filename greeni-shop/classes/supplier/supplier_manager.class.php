<?php
            
require_once "supplier_renderer.class.php";
require_once "supplier_data_manager.class.php";
require_once "delivery_date_manager.class.php";

class SupplierManager
{
	private static $suppliers = null;
	
	public static function get_supplier_name($supplier_id)
	{
		$suppliers = self :: get_all_suppliers();
		if(isset($suppliers[$supplier_id]))
		{
			return $suppliers[$supplier_id]->name;
		}
		else
		{
			throw Exception("Supplier_id not recognized.");
		}
	}
	
	public static function get_supplier_id_by_name($supplier_name)
	{
		$suppliers = self :: get_all_suppliers();
		foreach($suppliers as $s)
		{
			if(!strcmp(strtolower($s->name), strtolower($supplier_name)))
			{
				return $s->_id;
			}
		}
		throw Exception("Supplier_name not recognized.");
	}
	
	public static function get_all_suppliers()
	{
		if(is_null(self :: $suppliers))
		{
			self :: $suppliers = SupplierDataManager::retrieve_suppliers();
		}
		return self :: $suppliers;
	}
}    

?>