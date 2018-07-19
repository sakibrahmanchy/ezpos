<?php
/**
 * Created by PhpStorm.
 * User: TechnoTree BD
 * Date: 8/1/2017
 * Time: 10:36 PM
 */

namespace App\Enumaration;


class CashRegisterTransactionType
{
    public static $ADD_BALANCE = 1;
    public static $SUBTRACT_BALANCE = 2;
    public static $CASH_SALES = 3;
    public static $CHECK_SALES = 4;
    public static $DEBIT_CARD_SALES = 5;
    public static $CREDIT_CARD_SALES = 6;
    public static $GIFT_CARD_SALES = 7;
    public static $LOYALTY_CARD_SALES = 8;
    public static $DUE = 9;
}