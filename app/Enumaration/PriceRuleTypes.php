<?php
/**
 * Created by PhpStorm.
 * User: TechnoTree BD
 * Date: 8/23/2017
 * Time: 12:50 PM
 */

namespace App\Enumaration;


class PriceRuleTypes
{
    public static $PRICE_RULE = array(
        "simple_discount"=>1,
        "advanced_discount"=>2,
        "buy_x_get_y_free"=>3,
        "buy_x_get_discount"=>4,
        "spend_x_get_discount"=>5
    );


}