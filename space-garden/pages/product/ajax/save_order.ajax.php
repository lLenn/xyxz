<?php

require_once '../../../core/lib/path.class.php';
require_once Path :: get_path() . 'core/lib/global.inc.php';
require_once Path :: get_path() . 'pages/product/lib/product_manager.class.php';

if (Session :: get_user_id())
{
	$user = ClientDataManager::instance(null)->retrieve_client_user_account(Session :: get_user_id());
	if($user->is_admin())
	{
		$records = unserialize(Request::post("records"));
		$success = true;
		foreach($records as $record)
		{
			$product = new Product();
			$product->set_id(trim($record[1]));
			$product->set_order($record[0]);
			$success &= ProductDataManager::instance()->update_product_order($product);
		}
		echo $success;
		exit;
	}
}

echo 0;

?>