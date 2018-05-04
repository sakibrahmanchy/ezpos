<?php

Breadcrumbs::register('dashboard', function($breadcrumbs)
{
    $breadcrumbs->push('Dashboard', route('dashboard'),['icon' => 'dashboard']);
});


Breadcrumbs::register('customer_list', function($breadcrumbs)
{
    $breadcrumbs->push('Customers', route('customer_list'),['icon' => 'users']);
});

Breadcrumbs::register('new_customer', function($breadcrumbs)
{
    $breadcrumbs->parent('customer_list');
    $breadcrumbs->push('New Customer', route('new_customer'));
});

Breadcrumbs::register('customer_edit', function($breadcrumbs, $customer_id)
{
    $breadcrumbs->parent('customer_list');
    $breadcrumbs->push('Edit Customer', route('customer_edit',['customer_id'=>$customer_id]));
});


Breadcrumbs::register('item_list', function($breadcrumbs)
{
    $breadcrumbs->push('Items', route('item_list'),['icon' => 'hdd-o']);
});

Breadcrumbs::register('new_item', function($breadcrumbs)
{
    $breadcrumbs->parent('item_list');
    $breadcrumbs->push('New Item', route('new_item'));
});

Breadcrumbs::register('item_edit', function($breadcrumbs, $item_id)
{
    $breadcrumbs->parent('item_list');
    $breadcrumbs->push('Edit Item', route('item_edit',['item_id'=>$item_id]));
});

Breadcrumbs::register('item_import', function($breadcrumbs)
{
    $breadcrumbs->parent('item_list');
    $breadcrumbs->push('Import Items', route('item_import_excel'));
});



Breadcrumbs::register('item_categories', function($breadcrumbs)
{
    $breadcrumbs->parent('item_list');
    $breadcrumbs->push('Item Categories', route('category_list'));
});

Breadcrumbs::register('item_manufacturers', function($breadcrumbs)
{
    $breadcrumbs->parent('item_list');
    $breadcrumbs->push('Item Manufacturers', route('manufacturer_list'));
});

Breadcrumbs::register('item_kit_list', function($breadcrumbs)
{
    $breadcrumbs->push('Item kits', route('item_kit_list'),['icon' => 'align-justify']);
});


Breadcrumbs::register('new_item_kit', function($breadcrumbs)
{
    $breadcrumbs->parent('item_kit_list');
    $breadcrumbs->push('New Item Kit', route('new_item_kit'));
});

Breadcrumbs::register('item_kit_edit', function($breadcrumbs, $item_kit_id)
{
    $breadcrumbs->parent('item_kit_list');
    $breadcrumbs->push('Edit Item Kit', route('item_kit_edit',['item_kit_id'=>$item_kit_id]));
});

Breadcrumbs::register('price_rule_list', function($breadcrumbs)
{
    $breadcrumbs->push('Price Rules', route('price_rule_list'),['icon' => 'tag']);
});


Breadcrumbs::register('new_price_rule', function($breadcrumbs)
{
    $breadcrumbs->parent('price_rule_list');
    $breadcrumbs->push('New Price Rule', route('new_price_rule'));
});

Breadcrumbs::register('price_rule_edit', function($breadcrumbs, $price_rule_id)
{
    $breadcrumbs->parent('price_rule_list');
    $breadcrumbs->push('Edit Price Rules', route('price_rule_edit',['price_rule_id'=>$price_rule_id]));
});


Breadcrumbs::register('price_level_list', function($breadcrumbs)
{
    $breadcrumbs->push('Price Levels', route('price_level_list'),['icon' => 'tag']);
});


Breadcrumbs::register('new_price_level', function($breadcrumbs)
{
    $breadcrumbs->parent('price_level_list');
    $breadcrumbs->push('New Price Level', route('new_price_level'));
});

Breadcrumbs::register('price_level_edit', function($breadcrumbs, $price_level_id)
{
    $breadcrumbs->parent('price_level_list');
    $breadcrumbs->push('Edit Price Level', route('price_level_edit',['price_level_id'=>$price_level_id]));
});


Breadcrumbs::register('supplier_list', function($breadcrumbs)
{
    $breadcrumbs->push('Suppliers', route('supplier_list'),['icon' => 'download']);
});


Breadcrumbs::register('new_supplier', function($breadcrumbs)
{
    $breadcrumbs->parent('supplier_list');
    $breadcrumbs->push('New Supplier', route('new_supplier'));
});

Breadcrumbs::register('supplier_edit', function($breadcrumbs, $supplier_id)
{
    $breadcrumbs->parent('supplier_list');
    $breadcrumbs->push('Edit Supplier', route('supplier_edit',['supplier_edit'=>$supplier_id]));
});

Breadcrumbs::register('supplier_import', function($breadcrumbs)
{
    $breadcrumbs->parent('supplier_list');
    $breadcrumbs->push('Import Suppliers', route('supplier_import_excel'));
});



Breadcrumbs::register('employee_list', function($breadcrumbs)
{
    $breadcrumbs->push('Employee', route('employee_list'),['icon' => 'id-card']);
});


Breadcrumbs::register('new_employee', function($breadcrumbs)
{
    $breadcrumbs->parent('employee_list');
    $breadcrumbs->push('New Employee', route('new_employee'));
});

Breadcrumbs::register('employee_edit', function($breadcrumbs, $employee_id)
{
    $breadcrumbs->parent('employee_list');
    $breadcrumbs->push('Edit Employee', route('employee_edit',['employee_edit'=>$employee_id]));
});


Breadcrumbs::register('gift_card_list', function($breadcrumbs)
{
    $breadcrumbs->push('Gift Card', route('gift_card_list'),['icon' => 'gift']);
});


Breadcrumbs::register('new_gift_card', function($breadcrumbs)
{
    $breadcrumbs->parent('gift_card_list');
    $breadcrumbs->push('New Gift Card', route('new_gift_card'));
});

Breadcrumbs::register('gift_card_edit', function($breadcrumbs, $gift_card_id)
{
    $breadcrumbs->parent('gift_card_list');
    $breadcrumbs->push('Edit Gift Card', route('gift_card_edit',['gift_card_edit'=>$gift_card_id]));
});

Breadcrumbs::register('report_dashboard', function($breadcrumbs)
{
    $breadcrumbs->push('Reports', route('report_dashboard'),['icon' => 'bar-chart']);
});

Breadcrumbs::register('report_category', function($breadcrumbs)
{
    $breadcrumbs->parent('report_dashboard');
    $breadcrumbs->push('Category', route('category_list'));
});

Breadcrumbs::register('report_category_graphical', function($breadcrumbs)
{
    $breadcrumbs->parent('report_category');
    $breadcrumbs->push('Category Graphical', route('report_category_graphical'));
});

Breadcrumbs::register('report_category_summary', function($breadcrumbs)
{
    $breadcrumbs->parent('report_category');
    $breadcrumbs->push('Category Summary', route('report_category_summary'));
});

Breadcrumbs::register('report_closeout', function($breadcrumbs)
{
    $breadcrumbs->parent('report_dashboard');
    $breadcrumbs->push('Close Out', route('report_close_out_summary'));
});

Breadcrumbs::register('report_customer', function($breadcrumbs)
{
    $breadcrumbs->parent('report_dashboard');
    $breadcrumbs->push('Customer', route('customer_list'));
});

Breadcrumbs::register('report_customer_detail', function($breadcrumbs)
{
    $breadcrumbs->parent('report_customer');
    $breadcrumbs->push('Detail', route('report_customer_detail'));
});


Breadcrumbs::register('report_customer_graphical', function($breadcrumbs)
{
    $breadcrumbs->parent('report_customer');
    $breadcrumbs->push('Graphical', route('report_customer_graphical'));
});

Breadcrumbs::register('report_customer_summary', function($breadcrumbs)
{
    $breadcrumbs->parent('report_customer');
    $breadcrumbs->push('Summary', route('report_customer_graphical'));
});


Breadcrumbs::register('report_discount', function($breadcrumbs)
{
    $breadcrumbs->parent('report_dashboard');
    $breadcrumbs->push('Discount', null);
});

Breadcrumbs::register('report_discount_graphical', function($breadcrumbs)
{
    $breadcrumbs->parent('report_discount');
    $breadcrumbs->push('Graphical', route('report_discount_graphical'));
});

Breadcrumbs::register('report_discount_summary', function($breadcrumbs)
{
    $breadcrumbs->parent('report_discount');
    $breadcrumbs->push('Summary', route('report_discount_summary'));
});


Breadcrumbs::register('report_employee', function($breadcrumbs)
{
    $breadcrumbs->parent('report_dashboard');
    $breadcrumbs->push('Employee', null);
});

Breadcrumbs::register('report_employee_graphical', function($breadcrumbs)
{
    $breadcrumbs->parent('report_employee');
    $breadcrumbs->push('Graphical', route('report_employee_graphical'));
});

Breadcrumbs::register('report_employee_summary', function($breadcrumbs)
{
    $breadcrumbs->parent('report_employee');
    $breadcrumbs->push('Summary', route('report_employee_summary'));
});

Breadcrumbs::register('report_employee_detail', function($breadcrumbs)
{
    $breadcrumbs->parent('report_employee');
    $breadcrumbs->push('Detail', route('report_employee_detail'));
});


Breadcrumbs::register('report_inventory', function($breadcrumbs)
{
    $breadcrumbs->parent('report_dashboard');
    $breadcrumbs->push('Inventory', null);
});

Breadcrumbs::register('report_inventory_low', function($breadcrumbs)
{
    $breadcrumbs->parent('report_inventory');
    $breadcrumbs->push('Low Inventory', route('report_inventory_low'));
});

Breadcrumbs::register('report_inventory_summary', function($breadcrumbs)
{
    $breadcrumbs->parent('report_inventory');
    $breadcrumbs->push('Summary', route('report_inventory_summary'));
});

Breadcrumbs::register('report_inventory_detail', function($breadcrumbs)
{
    $breadcrumbs->parent('report_inventory');
    $breadcrumbs->push('Detail', route('report_inventory_detail'));
});

Breadcrumbs::register('report_item_kit', function($breadcrumbs)
{
    $breadcrumbs->parent('report_dashboard');
    $breadcrumbs->push('Item Kit', null);
});

Breadcrumbs::register('report_item_kit_graphical', function($breadcrumbs)
{
    $breadcrumbs->parent('report_item_kit');
    $breadcrumbs->push('Graphical', route('report_itemkit_graphical'));
});

Breadcrumbs::register('report_item_kit_summary', function($breadcrumbs)
{
    $breadcrumbs->parent('report_item_kit');
    $breadcrumbs->push('Summary', route('report_itemkit_summary'));
});

Breadcrumbs::register('report_item', function($breadcrumbs)
{
    $breadcrumbs->parent('report_dashboard');
    $breadcrumbs->push('Item', null);
});

Breadcrumbs::register('report_item_graphical', function($breadcrumbs)
{
    $breadcrumbs->parent('report_item');
    $breadcrumbs->push('Graphical', route('report_item_graphical'));
});

Breadcrumbs::register('report_item_summary', function($breadcrumbs)
{
    $breadcrumbs->parent('report_item');
    $breadcrumbs->push('Summary', route('report_item_summary'));
});

Breadcrumbs::register('report_item_import_log', function($breadcrumbs)
{
    $breadcrumbs->parent('report_item');
    $breadcrumbs->push('Item Import Log', route('report_item_import_log'));
});

Breadcrumbs::register('report_manufacturer', function($breadcrumbs)
{
    $breadcrumbs->parent('report_dashboard');
    $breadcrumbs->push('Manufacturer', null);
});

Breadcrumbs::register('report_manufacturer_graphical', function($breadcrumbs)
{
    $breadcrumbs->parent('report_manufacturer');
    $breadcrumbs->push('Graphical', route('report_manufacturer_graphical'));
});

Breadcrumbs::register('report_manufacturer_summary', function($breadcrumbs)
{
    $breadcrumbs->parent('report_manufacturer');
    $breadcrumbs->push('Summary', route('report_manufacturer_summary'));
});

Breadcrumbs::register('report_payment', function($breadcrumbs)
{
    $breadcrumbs->parent('report_dashboard');
    $breadcrumbs->push('Payment', null);
});

Breadcrumbs::register('report_payment_graphical', function($breadcrumbs)
{
    $breadcrumbs->parent('report_payment');
    $breadcrumbs->push('Graphical', route('report_payment_graphical'));
});

Breadcrumbs::register('report_payment_summary', function($breadcrumbs)
{
    $breadcrumbs->parent('report_payment');
    $breadcrumbs->push('Summary', route('report_payment_summary'));
});

Breadcrumbs::register('report_payment_detail', function($breadcrumbs)
{
    $breadcrumbs->parent('report_payment');
    $breadcrumbs->push('Detail', route('report_payment_detail'));
});

Breadcrumbs::register('report_profit_and_loss', function($breadcrumbs)
{
    $breadcrumbs->parent('report_dashboard');
    $breadcrumbs->push('Profit and Loss', null);
});

Breadcrumbs::register('report_profit_and_loss_detail', function($breadcrumbs)
{
    $breadcrumbs->parent('report_profit_and_loss');
    $breadcrumbs->push('Detail', route('report_profit_loss_detail'));
});

Breadcrumbs::register('report_profit_and_loss_summary', function($breadcrumbs)
{
    $breadcrumbs->parent('report_profit_and_loss');
    $breadcrumbs->push('Summary', route('report_profit_loss_summary'));
});

Breadcrumbs::register('report_sale', function($breadcrumbs)
{
    $breadcrumbs->parent('report_dashboard');
    $breadcrumbs->push('Sale', null);
});

Breadcrumbs::register('report_sale_graphical', function($breadcrumbs)
{
    $breadcrumbs->parent('report_sale');
    $breadcrumbs->push('Graphical', route('report_sale_graphical'));
});

Breadcrumbs::register('report_sale_summary', function($breadcrumbs)
{
    $breadcrumbs->parent('report_sale');
    $breadcrumbs->push('Summary', route('report_sale_summary'));
});

Breadcrumbs::register('report_sale_detail', function($breadcrumbs)
{
    $breadcrumbs->parent('report_sale');
    $breadcrumbs->push('Detail', route('report_sale_detail'));
});

Breadcrumbs::register('report_sale_graphical_hourly', function($breadcrumbs)
{
    $breadcrumbs->parent('report_sale');
    $breadcrumbs->push('Graphical Hourly', route('report_sale_graphical_hourly'));
});

Breadcrumbs::register('report_sale_summary_hourly', function($breadcrumbs)
{
    $breadcrumbs->parent('report_sale');
    $breadcrumbs->push('Summary Hourly', route('report_sale_summary_hourly'));
});

Breadcrumbs::register('report_supplier', function($breadcrumbs)
{
    $breadcrumbs->parent('report_dashboard');
    $breadcrumbs->push('Supplier', null);
});

Breadcrumbs::register('report_supplier_graphical', function($breadcrumbs)
{
    $breadcrumbs->parent('report_supplier');
    $breadcrumbs->push('Graphical', route('report_supplier_graphical'));
});

Breadcrumbs::register('report_supplier_summary', function($breadcrumbs)
{
    $breadcrumbs->parent('report_supplier');
    $breadcrumbs->push('Summary', route('report_supplier_summary'));
});

Breadcrumbs::register('report_supplier_detail', function($breadcrumbs)
{
    $breadcrumbs->parent('report_supplier');
    $breadcrumbs->push('Detail', route('report_supplier_detail'));
});

Breadcrumbs::register('report_suspended_sale', function($breadcrumbs)
{
    $breadcrumbs->parent('report_dashboard');
    $breadcrumbs->push('Suspended Sale', null);
});

Breadcrumbs::register('report_suspended_sale_detail', function($breadcrumbs)
{
    $breadcrumbs->parent('report_suspended_sale');
    $breadcrumbs->push('Detail', route('report_suspended_detail'));
});

Breadcrumbs::register('sale', function($breadcrumbs)
{
     $breadcrumbs->push('Sale', route('new_sale'),['icon' => 'cart-plus']);
});

Breadcrumbs::register('new_sale', function($breadcrumbs)
{
    $breadcrumbs->parent('sale');
    $breadcrumbs->push('New Sale', route('new_sale'));
});

Breadcrumbs::register('edit_sale', function($breadcrumbs,$sale_id)
{
    $breadcrumbs->parent('sale');
    $breadcrumbs->push('Edit Sale', route('sale_edit',["sale_id"=>$sale_id]));
});

Breadcrumbs::register('sale_receipt', function($breadcrumbs,$sale_id)
{
    $breadcrumbs->parent('sale');
    $breadcrumbs->push('Sale Receipt', route('sale_receipt',["sale_id"=>$sale_id]));
});

Breadcrumbs::register('sale_search', function($breadcrumbs)
{
    $breadcrumbs->parent('sale');
    $breadcrumbs->push('Sale Search', route('search_sale'));
});

Breadcrumbs::register('user_profile', function($breadcrumbs,$user_id)
{
    $breadcrumbs->push('User Profile', route('user_profile_edit',["user_id"=>$user_id]),['icon' => 'user']);
});

Breadcrumbs::register('settings', function($breadcrumbs)
{
    $breadcrumbs->push('Settings', route('change_settings'),['icon' => 'cog fa-spin']);
});

Breadcrumbs::register('counter_list', function($breadcrumbs)
{
    $breadcrumbs->push('Counter', route('counter_list'),['icon' => 'map-marker']);
});


Breadcrumbs::register('new_counter', function($breadcrumbs)
{
    $breadcrumbs->parent('counter_list');
    $breadcrumbs->push('New Counter', route('new_counter'));
});

Breadcrumbs::register('counter_edit', function($breadcrumbs, $counter_id)
{
    $breadcrumbs->parent('counter_list');
    $breadcrumbs->push('Edit Counter ', route('counter_edit',['counter_edit'=>$counter_id]));
});


