# Portfolio

These are a few examples of code that I have written during my years as a programmer.
This folder is used to provide future employers a glance of what kind of asset I can be to their company.
I've made a selection of best and/or latest code I have written.

## Greeni-shop

This code was written during my employment at Garden Machinery during 2010 - 2011.
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

1. The quantity of the promotion is bigger than the quantity specified in the order. If this is the case, the criteria of the promotion is checked to see if it is applicable on line 70. The function `Criteria::get_condition_compare_value` is used for this. If the promotion is applicable there needs to be a check first whether there is another promotion where the quantity is also bigger than that of the order, but is lower than that of the current promotion being processed. (Line 72 to 90) If this is the case the promotion is skipped, otherwise the promotion is added, the order is restructured to fit the promotion with function `self::create_new_orders_from_promotion($order, $promotion` on line 95 and the loop is closed.
2. The quantity of the promotion is smaller than or equal to the quantity specified in the order. This will only be checked if the conditions in the first scenario are not met. This is why the promotions are ordered by descending quantity. If a promotion in this scenario is eligible the quantity of the promotion will be deducted from the quantity of the order via the function `self::create_new_orders_from_promotion`. (line 60) The promotion is applied mutliple times until the quantity of the order is no longer bigger than the quantity of the promotion. (line 58) This scenario will loop through the remaining promotions until the quantity of the order is lower or equal to 0 and no more articles are able to be considered for a promotion (line 48), when the conditions of the first scenario are met and the process is reset, or when the criteria of the promotion does not start with "=". (line 62)

Some examples:

Criteria | ["=50", "=25", "=10", "=7"] | ["=50", "=25", "=10", "=7"] | ["=50", "=25", "=10", "=7"] | ["=50", "=25", "=10", "=7"] | ["=50", ">=25", "=10", "=7"] | ["<=15"] | ["<=15", "<=10"] | ["<50", "=25", "=7"]
-------- | --------------------------- | --------------------------- | --------------------------- | --------------------------- | ---------------------------- | --------- | ------------------ | --------------------
# order | 100 | 75 | 35 | 12 | 75 | 10 | 10 | 74
1st cycle | "=50" x 2 (l58) | "=50" | "=50" skipped | "=50" skipped | "=50" | "<=15" & break (l95) | "<=15" skipped | "<50" skipped
# order | 0 | 25 | 35 | 12 | 25 | 0 | 100 | 74
2nd cycle | break (l48) | "=25" | "=25" | "=25" skipped | ">=25" & break (l62) | break (l95) | "<=10" & break (l95) | "=25" x 2
# order | 0 | 0 | 15 | 12 | 0 | 0 | 0 | 14
3rd cycle | break (l48) | break (l48) | "=10" | "=10" | break (l62) | break (l95) | break (l95) | "=7" x 2
# order | 0 | 0 | 5 | 2 | 0 | 0 | 0 | 0
4th cycle | break (l48) | break (l48) | "=7" skipped | "=7" skipped | break (l62) | break (l95) | break (l95) | break (l48)
# order | 0 | 0 | 5 | 2 | 0 | 0 | 0 | 0
