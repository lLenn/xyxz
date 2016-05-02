<?php

require_once 'classes/article/article_manager.class.php';
require_once 'order_data_manager.class.php';
require_once 'order_renderer.class.php';
require_once 'order.class.php';
require_once 'special_order.class.php';
require_once 'order_promotion.class.php';
require_once 'invoice.class.php';

class OrderManager
{
	public static function create_invoice($user, $order_id)
	{
		$invoice = new Invoice();
		$invoice->set_order_id($order_id);
		$invoice->set_currency(OrderDataManager::retrieve_currency_by_pricelist($user->get_country()));
		if($user->get_country() == "BE")
		{
			$invoice->set_vat(21);
		}
		
		// NORMAL ORDERS
		$orders = OrderDataManager::retrieve_orders($user->get_id(), $order_id);
		foreach($orders as $order)
		{
			$order->set_stock(ArticleManager::get_availability($order->get_article_code()));
			$order->set_replacements(ArticleManager::get_rep_or_related($order->get_article_code()));
			$order->set_related(ArticleManager::get_rep_or_related($order->get_article_code()), false);
			$invoice->add_to_total($order->get_price() * $order->get_quantity());
			// HAS PROMOTION?
			$promotions = PromotionManager::calculate_promotions_for_order($order);
			if($promotions !== false)
			{
				$add_promotion_discount = false;
				$gets_discount = false;
				foreach($promotions as $promotion)
				{
					$invoice->add_to_total(PromotionRenderer::get_promotion_order_price_output($promotion) * $promotion->get_quantity());
					if($promotion->get_add_to_discount_total() && !in_array($user->get_country(), OrderDiscountDataManager::get_order_discount_pricelist_exceptions()))
					{
						if($promotion->get_gets_discount())
						{
							$gets_discount = true;
							$invoice->add_to_discount_total(PromotionRenderer::get_promotion_order_price_output($promotion) * $promotion->get_quantity());
						}
						else
						{
							$add_promotion_discount = true;
							$invoice->add_to_promotion_discount_extra(PromotionRenderer::get_promotion_order_price_output($promotion) * $promotion->get_quantity());
						}
					}
				}
				if($gets_discount)
					$invoice->add_to_discount_total($order->get_price() * $order->get_quantity());
				elseif($add_promotion_discount)
					$invoice->add_to_promotion_discount_extra($order->get_price() * $order->get_quantity());
				$order->set_promotions($promotions);
				$invoice->add_promotion($order, $promotions);
			}
			else
			{
				// CAN HAVE DISCOUNT?
				if(!in_array($user->get_country(), OrderDiscountDataManager::get_order_discount_pricelist_exceptions()) &&
				   !OrderDiscountDataManager::is_order_discount_article_exception($order->get_article_code()))
				{
					$invoice->add_to_discount_total($order->get_price() * $order->get_quantity());
					$invoice->add_order_with_discount($order);
				}
				else
				{
					$invoice->add_order($order);
				}
			}
		}

		// SPECIAL ORDERS
		$special_orders = OrderDataManager::retrieve_special_orders($user->get_id(), $order_id);
		foreach($special_orders as $index => $special)
		{
			if($special->get_price() != "-")
			{                                                                                                                   
				$invoice->add_to_total($special->get_total_price());
			}
			else
			{
				$invoice->add_special_order_without_price($special);
				unset($special_orders[$index]);
			}
		}
		$invoice->set_special_orders($special_orders);
		
		// CALCULATE DISCOUNT
		$discount = OrderDiscountManager::calculate_discount($invoice->get_discount_total() + $invoice->get_promotion_discount_extra());
		if(!is_null($discount[0]))
		{
			$invoice->set_discount($discount[0]);
			foreach($invoice->get_orders_with_discount() as $order)
			{
				$order->set_discount($discount[0]->get_discount_percentage());
			}
				
			foreach($invoice->get_promotions() as $order)
			{
				foreach($order->get_promotions() as $promotion)
				{
					if($promotion->get_add_to_discount_total() && !in_array($user->get_country(), OrderDiscountDataManager::get_order_discount_pricelist_exceptions()))
					{
						if($promotion->get_gets_discount())
						{
							$order->set_discount($discount[0]->get_discount_percentage());
							break;
						}
					}
				}
			}
		}
		
		if(!is_null($discount[1]))
		{
			$invoice->set_next_discounts($discount[1]);
		}
        
		//TRANSPORT
		$discount = OrderDiscountManager::calculate_discount($invoice->get_total());
		$invoice->set_transport(OrderDiscountManager::calculate_transport($invoice->get_total(), $discount[0]));
		
		//DETAILS
		$data = OrderDataManager::retrieve_order_details($user->get_id());
		if(!empty($data))
		{
			$invoice->set_remark($data[0]->remark);
			$invoice->set_delivery_method($data[0]->delivery_method);
			$invoice->set_special_delivery_method($data[0]->special_delivery_method);
			if(($invoice->get_special_delivery_method() == "" || is_null($invoice->get_special_delivery_method()) || $invoice->get_special_delivery_method()==0) && !empty($special_orders))
			{
				$invoice->set_special_delivery_method(1);
			}
			elseif(empty($special_orders))
			{
				$invoice->set_special_delivery_method(0);
			}
			$invoice->set_reference($data[0]->reference_number);
		}
		elseif(!empty($special_orders))
		{
			$invoice->set_special_delivery_method(1);
		}
		return $invoice;
	}
}

?>