<?php
/**
 * Created by PhpStorm.
 * User: TechnoTree BD
 * Date: 8/1/2017
 * Time: 10:36 PM
 */

namespace App\Enumaration;


class PaymentTypes
{
    public static $TypeList = array(
        "Cash" => 3,
        "Check" => 4,
        "Debit Card" => 5,
        "Credit Card" => 6,
        "Gift Card" => 7,
        "Loyalty Card" => 8,
        "Due" => 9
    );

}