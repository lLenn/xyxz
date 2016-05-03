## Greeni-shop

This code was written during my employment at Garden Machinery during 2010 - 2011.

### Promotion module

The first code I'm providing here is the logical model behind a promotion module.

**The first goal of this code is to compare a newly added promotion with other promotions and give an approriate error message.**

#### greeni-shop/classes/promotion/promotion\_data\_manager.class.php

The code starts on line 9 with the `retrieve_promotion_from_post()` function which is called to validate the request sent. After the input syntax is validated (from line 16 to 112) there are 2 functions that seek conflicts with the new given promotion criteria.

`PromotionManager::is_criteria_valid_for_article($promotion, $update_id)` on line 123 searches whether the given criteria overlaps with other existing criteria of a certain article.

`PromotionManager::are_promotion_types_valid_for_article($promotion, $update_id)` on line 134 calculates if the promotion is more profitable than other existing promotions, i.e. if the customer can choose one promotion over the other because the reduction is bigger if he buys less quantities on seperate occasions. This function returns the promotion that was extracted from the request, whether the promotion is conform to the syntax and is therefor legitimate, and, if not, which message is to be displayed together with possible other articles that conflict with the given criteria and what type of conflict was handled.

#### greeni-shop/classes/promotion/promotion\_manager.class.php

`is_criteria_valid_for_article($promotion, $update_id = "")` on line 235 first composes a condition that is then compared existing promotions in the database. The condition adresses whether dates for the promotion of an article and a certain criteria overlap. If this is the case the conficting promotions are returned.

`are_promotion_types_valid_for_article($promotion, $update_id = "")` on line 271 first retrieves all the promotions of the specified article where the date overlap. (line 273 - 294) After this each criteria of these promotions are compared with the criteria of the given promotion via `self::compare_promotions($promo, $promotion)` on line 303 and 311.

`compare_promotions($promo_a, $promo_b)` on line 328 loops through all hardcoded promotion types, checks whether `$promo_a` and/or `$promo_b` has these present and calculates the advantage each promotion retrieves. Lines 334 to 359 check if the promotion type is present both variables. Lines 361 to 398 calculate the advantage if the promotion type is present in the promotions. Each promotion can have more than one promotion type attached to it. This function returns 0 of the advantage of both promotions is equal, 1 if the advantage of `$promo_a` > `$promo_b` otherwise it returns -1.

**The second goal of this code is to determine which promotion is best applied if the customer buys the article the promotions are linked to.**

#### greeni-shop/classes/order/order\_manager.class.php

The code start on line 13 with `create_invoice($user, $order_id)` when a request is made to show the invoice of the customer. Line 23 to 75 seperates the orders that have a promotion and those that have not and adds them to different arrays in the invoice. On line 32 `PromotionManager::calculate_promotions_for_order($order)` is called to check if there are promotions attached to the article.

#### greeni-shop/classes/promotion/promotion\_manager.class.php

`calculate_promotions_for_order($order)` on line 22 starts by retrieving all promotions that might come into consideration for the specified article and date. (line 24 to 37) The considered promotions are then sorted according to quantity, highest first, on line 41 and 42 using the function `promotion_compare_by_criteria`; this the promotions can be easily looped through and stop when needed. This loop starts at line 46 and stops at 100. For each promotion there are 2 possible scenarios:

1. The quantity of the promotion is bigger than the quantity specified in the order. If this is the case, the criteria of the promotion is checked to see if it is applicable on line 70. The function `Criteria::get_condition_compare_value` is used for this. If the promotion is applicable there needs to be a check first whether there is another promotion where the quantity is also bigger than that of the order, but is lower than that of the current promotion being processed. (Line 72 to 90) If this is the case the promotion is skipped, otherwise the promotion is added, the order is restructured to fit the promotion with function `self::create_new_orders_from_promotion($order, $promotion)` on line 95 and the loop is closed.
2. The quantity of the promotion is smaller than or equal to the quantity specified in the order. This will only be checked if the conditions in the first scenario are not met. This is why the promotions are ordered by descending quantity. If a promotion in this scenario is eligible the quantity of the promotion will be deducted from the quantity of the order via the function `self::create_new_orders_from_promotion`. (line 60) The promotion is applied mutliple times until the quantity of the order is no longer bigger than the quantity of the promotion. (line 58) This scenario will loop through the remaining promotions until the quantity of the order is lower or equal to 0 and no more articles are able to be considered for a promotion (line 48), when the conditions of the first scenario are met and the process is reset, or when the criteria of the promotion does not start with "=". (line 62)

Some examples:

Criteria | ["=50", "=25", "=10", "=7"] | ["=50", "=25", "=10", "=7"] | ["=50", "=25", "=10", "=7"] | ["=50", "=25", "=10", "=7"] | ["=50", ">=25", "=10", "=7"] | ["<=15"] | ["<=15", "<=10"] | ["<50", "=25", "=7"]
-------- | --------------------------- | --------------------------- | --------------------------- | --------------------------- | ---------------------------- | -------- | ---------------- | --------------------
# order | 100 | 75 | 35 | 12 | 75 | 10 | 10 | 74
1st cycle | "=50" x 2 (l58) | "=50" | "=50" skipped | "=50" skipped | "=50" | "<=15" & break (l95) | "<=15" skipped | "<50" skipped
# order | 0 | 25 | 35 | 12 | 25 | 0 | 100 | 74
2nd cycle | break (l48) | "=25" | "=25" | "=25" skipped | ">=25" & break (l62) | break (l95) | "<=10" & break (l95) | "=25" x 2 (l58)
# order | 0 | 0 | 15 | 12 | 0 | 0 | 0 | 14
3rd cycle | break (l48) | break (l48) | "=10" | "=10" | break (l62) | break (l95) | break (l95) | "=7" x 2 (l58)
# order | 0 | 0 | 5 | 2 | 0 | 0 | 0 | 0
4th cycle | break (l48) | break (l48) | "=7" skipped | "=7" skipped | break (l62) | break (l95) | break (l95) | break (l48)
# order | 0 | 0 | 5 | 2 | 0 | 0 | 0 | 0

All possible new orders from this loop are added to a temporary array `$temp_new_orders` (line 44) and are added to a new array `$new_orders` grouped by price and article code. (line 111 to 125) This funtion return a new array of orders derived from the promotions that apply to them.
-+
`promotion_compare_by_criteria($a_prom, $b_prom)` on line 415 compares two promotions based on the criteria with the function `Criteria :: compare_by_criteria($a, $b)`

`create_new_orders_from_promotion(&$order, $promotion)` on line 137 creates an array of new orders seperated by the promotions that are applicable to the original order and return this array. The parameter `$order` is passed by reference. Depending on the condition of the criteria the quantity is deducted by that of the promotion or set to 0. (line 149 and 151) After which new orders are created based on the promotion types of the promotion. (line 154 to 175) 

#### greeni-shop/classes/criteria.class.php
 
`get_condition_compare_value($condition)` on line 241 returns a number based on the condition of the criteria that can be used in conditions.

`compare_by_criteria($a, $b)` on line 31 first check if both criteria are legitimate (line 33), otherwise it triggers an error (line 81). Thereafter, it checks to see if the amounts of both criteria are legitimate (line 37), otherwise it triggers an error (line 81). If these are all legitimate the criteria can be compared by first checking if the amounts are equal. If not, 1 or -1 is return based on the difference of the amount (line 68 to 76). If so, the criteria are checked for their conditions and return 0, 1 or -1 accordingly (line 56 to 64), or triggers an error (line 81) if the conditions arent legitimate (line 54).

### cURL Module

This module was written to embed some sites from suppliers into the greeni-shop website. This way customers can directly order from their site while the order is added to the greeni-shop invoice. This module accesses the suppliers site with cURL after which it modifies it according to a template specifically made for the supplier to add form elements that redirects back to the greeni-shop site.

#### get_url.php

Each supplier has its own page that embeds an iframe with a specific id that requests this page, e.g. car_parts.php, and sends allong 2 parameters: root & page. The root parameter is used to specify which template is to be used, if there need to be a login to access the suppliers site and others. The page parameter specifies which page from the supplier is to be loaded first.

After the usual check are done to ensure a good database connection, whether the page can be viewed, etc. (line 2 to 21), a new Curl object is initiated with the root parameter. (line 23) If the page parameter is given, this is also set in the Curl object. (line 24 to 27) Some suppliers only provide access to their webshop after you have logged in. This is done with the `login` function. (line 32 and 37) The id of the iframe that the pages of the suppliers use is set (line 39) so javascript and others can use it where needed and a request is loaded in the right frame. After everything is set, the curl object loads the page and displays it. (line 42)

#### classes/supplier/embedded/curl.class.php

`Curl($root)` on line 50 initiates a new Curl object.  