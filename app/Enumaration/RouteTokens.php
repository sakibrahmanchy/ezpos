<?php
/**
 * Created by PhpStorm.
 * User: TechnoTree BD
 * Date: 8/23/2017
 * Time: 12:50 PM
 */

namespace App\Enumaration;


class RouteTokens
{
    public static $ROUTE_TOKENS = array(

        "new_customer"=>"customer_add_update",
        "customer_list"=>"customer_add_update",
        "customer_edit"=>"customer_add_update",
        "customer_delete"=>"customer_delete",
        "customers_delete"=>"customer_delete",

        "new_item"=>"item_add_update",
        "item_list"=>"item_search",
        "item_edit"=>"item_add_update",
        "item_names"=>"item_search",
        "item_image_delete"=>"item_add_update",
        "item_delete"=>"item_delete",
        "items_delete"=>"item_delete",

        "item_import_excel"=>"item_add_update",
        "insert_item_file"=>"item_add_update",

        "new_item_kit"=>"itemkit_add_update",
        "item_kit_list"=>"itemkit_add_update",
        "item_kit_edit"=>"itemkit_add_update",
        "item_kit_delete"=>"itemkit_delete",
        "item_kits_delete"=>"itemkit_delete",

        "new_price_rule"=>"price_rules_add_update",
        "price_rule_list"=>"price_rules_add_update",
        "price_rule_edit"=>"price_rules_add_update",
        "price_rule_delete"=>"price_rules_delete",
        "price_rules_delete"=>"price_rules_delete",

        "new_supplier"=>"suppliers_add_update",
        "supplier_list" => "suppliers_add_update",
        "supplier_edit"=>"suppliers_add_update",
        "supplier_delete"=>"suppliers_delete",
        "suppliers_delete"=>"suppliers_delete",
        "supplier_to_excel"=>"suppliers_add_update",

        "new_sale"=>"sale_add_update",
        "counter_list_ajax"=>"sale_add_update",
        "counter_set"=>"sale_add_update",
        "suspended_sale_list"=>"sale_add_update",
        "sale_add_update" => "sale_edit",
        "sale_last_receipt"=>"sale_receipt",
        "search_sale" =>"sale_search",
        "sale_receipt"=>"sale_receipt",
        "mail_sale_receipt"=>"sale_receipt",
        "print_sale"=>"sale_receipt",
        "sale_list"=>"sale_add_update",
        "item_list_autocomplete"=>"sale_add_update",
        "sale_edit"=>"sale_add_update",
        'download_sale_receipt'=>'sale_receipt',
        "pop_open_cash_drawer"=>"sale_add_update",
        "open_cash_register"=>"sale_add_update",
        "add_cash_to_register"=>"sale_add_update",
        "subtract_cash_from_register"=>"sale_add_update",
        "close_cash_register"=>"sale_add_update",

        "report_category_graphical"=>"report_categories",
        "report_category_ajax"=>"report_categories",
        "report_category_summary"=>"report_categories",

        "report_close_out_summary"=>"report_closeout",
        "report_close_out_ajax"=>"report_closeout",

        "report_customer_graphical"=>"report_customers",
        "report_customer_ajax"=>"report_customers",
        "report_customer_summary"=>"report_customers",
        "report_customer_detail"=>"report_customers",

        "report_employee_graphical"=>"report_employees",
        "report_employee_ajax"=>"report_employees",
        "report_employee_summary"=>"report_employees",
        "report_employee_detail"=>"report_employees",

        "report_discount_graphical"=>"report_discounts",
        "report_discount_ajax"=>"report_discounts",
        "report_discount_summary"=>"report_discounts",

        "report_itemkit_graphical"=>"report_item_kits",
        "report_itemkit_ajax"=>"report_item_kits",
        "report_itemkit_summary"=>"report_item_kits",

        "report_item_graphical"=>"report_items",
        "report_item_ajax"=>"report_items",
        "report_item_summary"=>"report_items",

        "report_manufacturer_graphical"=>"report_manufacturers",
        "report_manufacturer_ajax"=>"report_manufacturers",
        "report_manufacturer_summary"=>"report_manufacturers",

        "report_payment_graphical"=>"report_payments",
        "report_payment_ajax"=>"report_payments",
        "report_payment_summary"=>"report_payments",
        "report_payment_detail"=>"report_payments",

        "report_sale_graphical"=>"report_sales",
        "report_sale_ajax"=>"report_sales",
        "report_sale_summary"=>"report_sales",
        "report_sale_detail"=>"report_sales",
        "report_sale_detail_ajax"=>"report_sales",
        "report_sale_graphical_hourly"=>"report_sales",
        'report_sale_hourly_ajax'=>'report_sales',
        "report_sale_summary_hourly"=>"report_sales",

        "report_supplier_graphical"=>"report_suppliers",
        "report_supplier_ajax"=>"report_suppliers",
        "report_supplier_summary"=>"report_suppliers",
        "report_supplier_detail"=>"report_suppliers",

        "report_inventory_graphical"=>"report_inventory",
        "report_inventory_ajax"=>"report_inventory",
        "report_inventory_summary"=>"report_inventory",
        "report_inventory_detail"=>"report_inventory",
        "report_inventory_low"=>"report_inventory",
        "report_inventory_low_ajax"=>"report_inventory",

        "report_profit_loss_summary"=>"report_profit_loss",
        "report_profit_loss_ajax"=>"report_profit_loss",
        "report_profit_loss_detail"=>"report_profit_loss",

        "report_suspended_detail"=>"report_suspended_sales",
        "report_suspended_detail_ajax"=>"report_suspended_sales",

        "new_employee"=>"employees_add_update",
        "employee_list" => "employees_add_update",
        "employee_edit"=>"employees_add_update",
        "employee_delete"=>"employees_delete",
        "employees_delete"=>"employees_delete",

        "category_list"=>"item_manage_categories",
        "new_category"=>"item_manage_categories",
        "edit_category"=>"item_manage_categories",
        "delete_category"=>"item_manage_categories",

        "tag_names"=>"item_manage_tags",
        "tag_list"=>"item_manage_tags",
        "new_tag"=>"item_manage_tags",
        "edit_tag"=>"item_manage_tags",
        "delete_tag"=>"item_manage_tags",

        "manufacturer_list"=>"item_manage_manufacturers",
        "new_manufacturer"=>"item_manage_manufacturers",
        "edit_manufacturer"=>"item_manage_manufacturers",
        "delete_manufacturer"=>"item_manage_manufacturers",

        "new_gift_card"=>"gift_cards_add_update",
        "gift_card_list"=>"gift_cards_add_update",
        "gift_card_edit"=>"gift_cards_add_update",
        "gift_card_delete"=>"gift_cards_delete",
        "gift_cards_delete"=>"gift_cards_delete",
        "gift_card_use"=>"gift_card_use",
        
        "new_counter"=>"counters_add_update",
        "counter_list"=>"counters_add_update",
        "counter_edit"=>"counters_add_update",
        "counter_delete"=>"counters_delete",
        "counters_delete"=>"counters_delete",
        "counter_set_default"=>"counters_add_update",
        "test_print"=>"counters_add_update",


    );


}
