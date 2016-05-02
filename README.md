# Portfolio

These are a few examples of code that I have written during my years as a programmer.  <return>
This folder is used to provide future employers a glance of what kind of asset I can be to their company.  <return>
I've made a selection of best and/or latest code I have written.

## Greeni-shop

This code was written during my employment at Garden Machinery during 2010 - 2011.  <return>
The first code I'm providing here is the logical model behind a promotion module.

**The first goal of this code is compare a newly added promotion with other promotions and give an approriate error message.**

##### greeni-shop/classes/promotion/promotion\_data\_manager.class.php

The code starts on line 9 with the `retrieve_promotion_from_post()` function which is called to validate the request sent.  <return>
After the input syntax is validated (from line 16 to 112) there are 2 functions that seek conflicts with the new given promotion criteria.  <return>

`PromotionManager::is_criteria_valid_for_article($promotion, $update_id)` on line 123 searches whether the given criteria overlaps with other existing criteria of a certain article.  <return>

`PromotionManager::are_promotion_types_valid_for_article($promotion, $update_id)` on line 134 calculates if the promotion is more profitable than other existing promotions, i.e. if the customer can choose one promotion over the other because the reduction is bigger if he buys less quantities on seperate occasions. This function returns the promotion that was extracted from the request, whether the promotion is conform to the syntax and is therefor legitimate, and if not which message is to be displayed together with possible other articles that conflict with the given criteria and what type of conflict was handled.

##### greeni-shop/classes/promotion/promotion\_manager.class.php

`is_criteria_valid_for_article($promotion, $update_id = "")` on line 235 first composes a condition that is then compared existing promotions in the database. The condition adresses whether dates for the promotion of an article and a certain criteria overlap. If this is the case the conficting promotions are returned.

`are_promotion_types_valid_for_article($promotion, $update_id = "")` on line 271 first retrieves all the promotions of the specified article where the date overlap. (line 273 - 294) After this each criteria of these promotions are compared with the criteria of the given promotion via `self::compare_promotions($promo, $promotion)` on line 303 and 311.

`compare_promotions($promo_a, $promo_b)` on line 328 loops through all hardcoded promotion types, checks whether `$promo_a` and/or `$promo_b` has these present and calculates the advantage each promotion retrieves. Lines 334 to 359 check if the promotion type is present both variables. Lines 361 - 398 calculate the advantage if the promotion type is present in the promotions. Each promotion can have more than one promotion type attached to it. This function returns 0 of the advantage of both promotions is equal, 1 if the advantage of `$promo_a` > `$promo_b` otherwise it returns -1.

**The second goal of this code is to determine which promotion is best applied if the customer buys the article they are attached to.**

