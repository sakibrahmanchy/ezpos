<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/



Auth::routes();

Route::get('/',function(){
    return redirect()->route('dashboard');
});

Route::get('/error-401',function(){
    return view('errors.401');
})->name('error-401');

Route::get('/login/pin', ['as' => 'pin_log_in', 'uses' => 'UserController@pinLogin']);
Route::post('/login/pin', ['as' => 'pin_log_in_post', 'uses' => 'UserController@pinLoginPost']);

Route::group(['middleware' => ['admin']], function () {

    Route::get('/home','HomeController@getDashBoard')->name('dashboard')->middleware('auth');

    route::get('/user/profile/edit/{user_id}','UserController@UpdateUserProfileGet')->name('user_profile_edit')->middleware('auth');
    route::post('/user/profile/save','UserController@UploadUserProfilePost')->name('user_profile_save')->middleware('auth');

    route::get('/settings/edit','SettingsController@GetSettings')->name('change_settings')->middleware('auth');
    route::post('/settings/save','SettingsController@SaveSettings')->name('save_settings')->middleware('auth');


    route::get('sales',function(){
        return view('sales');
    })->name('sales')->middleware('auth');

    Route::get('/file_upload',function(){
        return view('fileUpload');
    })->name('file-upload')->middleware('auth');


    route::get('/customer/new','CustomerController@GetCustomerForm' )->name('new_customer')->middleware('auth');
    route::post('/customer/new','CustomerController@AddCustomer')->name('new_customer')->middleware('auth');
    route::get('customer/list','CustomerController@GetCustomerList')->name('customer_list')->middleware('auth');
    Route::get('customer/profile/{customer_id}','CustomerController@getCustomerProfile')->name('customer_profile')->middleware('auth');
    Route::get('customer/invoice/{invoice_id}','CustomerController@getCustomerDueInvoice')->name('customer_invoice')->middleware('auth');
    Route::post('customer/invoice_generate','CustomerController@generateCustomerDueInvoice')->name('customer_invoice_generate')->middleware('auth');
    Route::get('customer/balance/add','CustomerController@customerAddBalanceGet')->name('customer_balance_add')->middleware('auth');
    Route::post('customer/balance/add','CustomerController@customerAddBalancePost')->name('customer_balance_add')->middleware('auth');
    Route::get('customer/assign/price_level/{customer_id}','CustomerController@customerAssignPriceLevelGet')->name('customer_assign_price_level_get')->middleware('auth');
    Route::post('customer/assign/price_level','CustomerController@customerAssignPriceLevelPost')->name('customer_assign_price_level')->middleware('auth');
    Route::post('customer/remove/price_level','CustomerController@removePriceLevelPost')->name('customer_remove_price_level')->middleware('auth');
    route::get('customer/edit/{customer_id}','CustomerController@EditCustomerGet')->name('customer_edit')->middleware('auth');
    route::post('customer/edit/{customer_id}','CustomerController@EditCustomerPost')->name('customer_edit')->middleware('auth');
    route::get('customer/delete/{customer_id}','CustomerController@DeleteCustomerGet')->name('customer_delete')->middleware('auth');
    Route::post('customers/delete','CustomerController@DeleteCustomers')->name('customers_delete')->middleware('auth');

    Route::get('customer/invoice/{invoice_id}/email','InvoiceController@emailInvoice')->name('customer_invoice_email')->middleware('auth');
    Route::get('customer/invoice/{invoice_id}/pdf','InvoiceController@DownloadInvoiceReceipt')->name('customer_invoice_pdf')->middleware('auth');


    route::get('/item/new','ItemController@GetItemForm' )->name('new_item')->middleware('auth');
    route::get('/item/clone/{item_id}','ItemController@cloneItem' )->name('clone_item')->middleware('auth');
    route::post('/item/new','ItemController@AddItem')->name('new_item')->middleware('auth');
    route::get('item/list','ItemController@GetItemListAjax')->name('item_list')->middleware('auth');
    route::get('item/list/ajax','ItemController@GetItemListAjax')->name('item_list_ajax')->middleware('auth');
    route::get('item/edit/{item_id}','ItemController@EditItemGet')->name('item_edit')->middleware('auth');
    route::post('item/edit/{item_id}','ItemController@EditItemPost')->name('item_edit')->middleware('auth');
    route::post('item/price','ItemController@getItemPrice')->name('item_price')->middleware('auth');
    route::get('item/names','ItemController@GetItems')->name('item_names')->middleware('auth');
    route::get('item/image/delete/{item_id}/{image_id}','ItemController@DeleteItemImage')->name('item_image_delete')->middleware('auth');
    route::get('item/names/autocomplete','ItemController@GetItemsAutocomplete' )->name('item_list_autocomplete')->middleware('auth');
    route::get('item/delete/{item_id}','ItemController@DeleteItemGet')->name('item_delete')->middleware('auth');
    Route::get('item/excel/import','ItemController@importExcelGet')->name('item_import_excel')->middleware('auth');
    Route::post('item/excel/import', 'ItemController@importExcel')->name('item_import_excel')->middleware('auth');
    route::get('item/all','ItemController@getAllItemsData')->name('all_items_data')->middleware('auth');
    Route::post('items/delete','ItemController@DeleteItems')->name('items_delete')->middleware('auth');

    route::get('/itemkit/new','ItemKitController@GetItemKitForm' )->name('new_item_kit')->middleware('auth');
    route::post('/itemkit/new','ItemKitController@AddItemKit')->name('new_item_kit')->middleware('auth');
    route::get('itemkit/list','ItemKitController@GetItemKitList')->name('item_kit_list')->middleware('auth');
    route::get('itemkit/edit/{item_kit_id}','ItemKitController@EditItemKitGet')->name('item_kit_edit')->middleware('auth');
    route::post('itemkit/edit/{item_kit_id}','ItemKitController@EditItemKitPost')->name('item_kit_edit')->middleware('auth');
    route::get('itemkit/delete/{item_kit_id}','ItemKitController@DeleteItemKitGet')->name('item_kit_delete')->middleware('auth');
    route::post('itemkits/delete','ItemKitController@DeleteItemKits')->name('item_kits_delete')->middleware('auth');

    route::get('/price_rule/new','PriceRuleController@GetPriceRuleForm' )->name('new_price_rule')->middleware('auth');
    route::post('/price_rule/new','PriceRuleController@AddPriceRule')->name('new_price_rule')->middleware('auth');
    route::get('price_rule/list','PriceRuleController@GetPriceRuleList')->name('price_rule_list')->middleware('auth');
    route::get('price_rule/edit/{price_rule_id}','PriceRuleController@EditPriceRuleGet')->name('price_rule_edit')->middleware('auth');
    route::post('price_rule/edit/{price_rule_id}','PriceRuleController@EditPriceRulePost')->name('price_rule_edit')->middleware('auth');
    route::get('price_rule/delete/{price_rule_id}','PriceRuleController@DeletePriceRuleGet')->name('price_rule_delete')->middleware('auth');
    route::post('price_rules/delete','PriceRuleController@DeletePriceRules')->name('price_rules_delete')->middleware('auth');

    route::get('/price_level/new','PriceLevelController@GetPriceLevelForm' )->name('new_price_level')->middleware('auth');
    route::post('/price_level/new','PriceLevelController@AddPriceLevel')->name('new_price_level')->middleware('auth');
    route::get('price_level/list','PriceLevelController@GetPriceLevelList')->name('price_level_list')->middleware('auth');
    route::get('price_level/edit/{price_level_id}','PriceLevelController@EditPriceLevelGet')->name('price_level_edit')->middleware('auth');
    route::post('price_level/edit/{price_level_id}','PriceLevelController@EditPriceLevelPost')->name('price_level_edit')->middleware('auth');
    route::get('price_level/delete/{price_level_id}','PriceLevelController@DeletePriceLevelGet')->name('price_level_delete')->middleware('auth');
    route::post('price_levels/delete','PriceLevelController@DeletePriceLevels')->name('price_levels_delete')->middleware('auth');

    route::get('/supplier/new','SupplierController@GetSupplierForm' )->name('new_supplier')->middleware('auth');
    route::post('/supplier/new','SupplierController@AddSupplier')->name('new_supplier')->middleware('auth');
    route::get('supplier/list','SupplierController@GetSupplierList')->name('supplier_list')->middleware('auth');
    route::get('supplier/edit/{supplier_id}','SupplierController@EditSupplierGet')->name('supplier_edit')->middleware('auth');
    route::post('supplier/edit/{supplier_id}','SupplierController@EditSupplierPost')->name('supplier_edit')->middleware('auth');
    route::get('/supplier/export','SupplierController@SuppliersDataToExcel' )->name('supplier_to_excel')->middleware('auth');
    route::get('supplier/delete/{supplier_id}','SupplierController@DeleteSupplierPost')->name('supplier_delete')->middleware('auth');
    route::post('suppliers/delete','SupplierController@DeleteSuppliers')->name('suppliers_delete')->middleware('auth');
    Route::get('supplier/excel/import','SupplierController@importExcelGet')->name('supplier_import_excel')->middleware('auth');
    Route::post('supplier/excel/import', 'SupplierController@importExcel')->name('supplier_import_excel')->middleware('auth');

    route::get('/sale/new','SaleController@GetSaleForm' )->name('new_sale')->middleware('auth');
    route::post('/sale/new','SaleController@AddSale')->name('new_sale')->middleware('auth');
    route::post('/sale/suspend','SaleController@SuspendSale')->name('suspend_sale')->middleware('auth');
    route::get('sale/suspended','SaleController@GetSuspendedSale')->name('suspended_sale_list')->middleware('auth');
    route::get('/sale/receipt/{sale_id}','SaleController@GetSaleReceipt')->name('sale_receipt')->middleware('auth');
    route::get('sale/edit/{sale_id}','SaleController@EditSaleVueGet')->name('sale_edit')->middleware('auth');
    route::get('sale/pre_edit/{sale_id}','SaleController@PreEditSaleGet')->name('sale_pre_edit')->middleware('auth');
    route::post('sale/pre_edit/{sale_id}','SaleController@PreEditSalePost')->name('sale_pre_edit_post')->middleware('auth');
    route::get('sale/edit/vue/{sale_id}','SaleController@EditSaleVueGet')->name('sale_edit_vue')->middleware('auth');
    route::post('sale/edit/{sale_id}','SaleController@EditSalePost')->name('sale_edit')->middleware('auth');
    route::get('sale/show_last_sale','SaleController@showLastSaleReceipt')->name('sale_last_receipt')->middleware('auth');
    route::get('sale/suspended/list','SaleController@GetSuspendedSales')->name('suspended_sale_list')->middleware('auth');
    route::get('sale/download_receipt/{sale_id}','SaleController@DownloadSaleReceipt')->name('download_sale_receipt')->middleware('auth');
    route::get('sale/mail_receipt/{sale_id}','SaleController@MailSaleReceipt')->name('mail_sale_receipt')->middleware('auth');
    route::get('sale/search','SaleController@SearchSaleGet')->name('search_sale')->middleware('auth');
    route::post('sale/search','SaleController@SearchSalePost')->name('search_sale')->middleware('auth');
    route::get('sale/print/{sale_id}','SaleController@printSaleReciept')->name('print_sale')->middleware('auth');
    route::get("sale/test_print/{counter_id}","SaleController@testPrint")->name("test_print")->middleware("auth");
    route::get("sale/open_cash_drawer","SaleController@popOpenCashDrawer")->name("pop_open_cash_drawer");
    route::get("sale/delete/{sale_id}","SaleController@DeleteSale")->name("sale_delete");

    route::get('/report/dashboard',function(){
        return view('reports.report_dashboard');
    })->name('report_dashboard')->middleware('auth');

    route::get('/report/category/graphical','Reports\CategoryReportController@ReportCategoryGraphical')->name('report_category_graphical')->middleware('auth');
    route::post('/report/category/graphical/ajax','Reports\CategoryReportController@ReportCategoryAjax')->name('report_category_ajax')->middleware('auth');
    route::get('/report/category/summary','Reports\CategoryReportController@ReportCategorySummary')->name('report_category_summary')->middleware('auth');

    route::get('/report/customer/graphical','Reports\CustomerReportController@ReportCustomerGraphical')->name('report_customer_graphical')->middleware('auth');
    route::post('/report/customer/ajax','Reports\CustomerReportController@ReportCustomerAjax')->name('report_customer_ajax')->middleware('auth');
    route::get('/report/customer/summary','Reports\CustomerReportController@ReportCustomerSummary')->name('report_customer_summary')->middleware('auth');
    route::get('/report/customer/detail','Reports\CustomerReportController@ReportCustomerDetail')->name('report_customer_detail')->middleware('auth');
    route::get('/report/customer/ajax','Reports\CustomerReportController@ReportCustomerAjax')->name('report_customer_ajax')->middleware('auth');


    route::get('/report/employee/graphical','Reports\EmployeeReportController@ReportEmployeeGraphical')->name('report_employee_graphical')->middleware('auth');
    route::post('/report/employee/ajax','Reports\EmployeeReportController@ReportEmployeeAjax')->name('report_employee_ajax')->middleware('auth');
    route::get('/report/employee/summary','Reports\EmployeeReportController@ReportEmployeeSummary')->name('report_employee_summary')->middleware('auth');
    route::get('/report/employee/detail','Reports\EmployeeReportController@ReportEmployeeDetail')->name('report_employee_detail')->middleware('auth');


    route::get('/report/discount/graphical','Reports\DiscountReportController@ReportDiscountGraphical')->name('report_discount_graphical')->middleware('auth');
    route::post('/report/discount/ajax','Reports\DiscountReportController@ReportDiscountAjax')->name('report_discount_ajax')->middleware('auth');
    route::get('/report/discount/summary','Reports\DiscountReportController@ReportDiscountSummary')->name('report_discount_summary')->middleware('auth');

    route::get('/report/itemkit/graphical','Reports\ItemKitReportController@ReportItemKitGraphical')->name('report_itemkit_graphical')->middleware('auth');
    route::post('/report/itemkit/ajax','Reports\ItemKitReportController@ReportItemKitAjax')->name('report_itemkit_ajax')->middleware('auth');
    route::get('/report/itemkit/summary','Reports\ItemKitReportController@ReportItemKitSummary')->name('report_itemkit_summary')->middleware('auth');

    route::get('/report/item/graphical','Reports\ItemReportController@ReportItemGraphical')->name('report_item_graphical')->middleware('auth');
    route::post('/report/item/ajax','Reports\ItemReportController@ReportItemAjax')->name('report_item_ajax')->middleware('auth');
    route::get('/report/item/summary','Reports\ItemReportController@ReportItemSummary')->name('report_item_summary')->middleware('auth');
    route::get('/report/item/import_log','Reports\ItemReportController@ReportItemImportLog')->name('report_item_import_log')->middleware('auth');


    route::get('/report/manufacturer/graphical','Reports\ManufacturerReportController@ReportManufacturerGraphical')->name('report_manufacturer_graphical')->middleware('auth');
    route::post('/report/manufacturer/ajax','Reports\ManufacturerReportController@ReportManufacturerAjax')->name('report_manufacturer_ajax')->middleware('auth');
    route::get('/report/manufacturer/summary','Reports\ManufacturerReportController@ReportManufacturerSummary')->name('report_manufacturer_summary')->middleware('auth');

    //route::get('/report/item/graphical','SaleController@ReportManufacturerGraphical')->name('report_item_graphical')->middleware('auth');
    route::get('/report/payment/graphical','Reports\PaymentReportController@ReportPaymentGraphical')->name('report_payment_graphical')->middleware('auth');
    route::post('/report/payment/ajax','Reports\PaymentReportController@ReportPaymentAjax')->name('report_payment_ajax')->middleware('auth');
    route::get('/report/payment/summary','Reports\PaymentReportController@ReportPaymentSummary')->name('report_payment_summary')->middleware('auth');
    route::get('/report/payment/detail','Reports\PaymentReportController@ReportPaymentDetail')->name('report_payment_detail')->middleware('auth');

    route::get('/report/sale/graphical','Reports\SaleReportController@ReportSaleGraphical')->name('report_sale_graphical')->middleware('auth');
    route::post('/report/sale/ajax','Reports\SaleReportController@ReportSaleAjax')->name('report_sale_ajax')->middleware('auth');
    route::get('/report/sale/summary','Reports\SaleReportController@ReportSaleSummary')->name('report_sale_summary')->middleware('auth');
    route::get('/report/sale/detail','Reports\SaleReportController@ReportSaleDetail')->name('report_sale_detail')->middleware('auth');
    route::post('/report/sale/detail/ajax','Reports\SaleReportController@ReportSaleDetailAjax')->name('report_sale_detail_ajax')->middleware('auth');
    route::get('/report/sale/graphical/hourly','Reports\SaleReportController@ReportSaleGraphicalHourly')->name('report_sale_graphical_hourly')->middleware('auth');
    route::post('/report/sale/hourly/ajax','Reports\SaleReportController@ReportSaleHourlyAjax')->name('report_sale_hourly_ajax')->middleware('auth');
    route::get('/report/sale/summary/hourly','Reports\SaleReportController@ReportSaleSummaryHourly')->name('report_sale_summary_hourly')->middleware('auth');
    route::get('/report/sale/deleted','Reports\SaleReportController@ReportSaleDeleted')->name('report_sale_deleted')->middleware('auth');
    route::post('/report/sale/deleted/ajax','Reports\SaleReportController@ReportSaleDeletedAjax')->name('report_sale_deleted_ajax')->middleware('auth');

    route::get('/report/supplier/graphical','Reports\SupplierReportController@ReportSupplierGraphical')->name('report_supplier_graphical')->middleware('auth');
    route::post('/report/supplier/ajax','Reports\SupplierReportController@ReportSupplierAjax')->name('report_supplier_ajax')->middleware('auth');
    route::get('/report/supplier/summary','Reports\SupplierReportController@ReportSupplierSummary')->name('report_supplier_summary')->middleware('auth');
    route::get('/report/supplier/detail','Reports\SupplierReportController@ReportSupplierDetail')->name('report_supplier_detail')->middleware('auth');


    route::post('/report/inventory/ajax','Reports\InventoryReportController@ReportInventoryAjax')->name('report_inventory_ajax')->middleware('auth');
    route::get('/report/inventory/summary','Reports\InventoryReportController@ReportInventorySummary')->name('report_inventory_summary')->middleware('auth');
    route::get('/report/inventory/detail','Reports\InventoryReportController@ReportInventoryDetail')->name('report_inventory_detail')->middleware('auth');
    route::get('/report/inventory/low','Reports\InventoryReportController@ReportInventoryLow')->name('report_inventory_low')->middleware('auth');


    route::get('/report/suspended/detail','Reports\SaleReportController@ReportSuspendedDetail')->name('report_suspended_detail')->middleware('auth');
    route::post('/report/suspended/detail/ajax','Reports\SaleReportController@ReportSuspendedDetailAjax')->name('report_suspended_detail_ajax')->middleware('auth');

    route::get('/report/profit_loss/summary','Reports\ProfitLossReportController@ReportProfitLossSummary')->name('report_profit_loss_summary')->middleware('auth');
    route::post('/report/profit_loss/ajax','Reports\ProfitLossReportController@ReportProfitLossAjax')->name('report_profit_loss_ajax')->middleware('auth');
    route::get('/report/profit_loss/detail','Reports\ProfitLossReportController@ReportProfitLossDetail')->name('report_profit_loss_detail')->middleware('auth');

    route::get('/report/close_out/summary','Reports\CloseOutReportController@ReportCloseOutSummary')->name('report_close_out_summary')->middleware('auth');
    route::post('/report/close_out/ajax','Reports\CloseOutReportController@ReportCloseOutAjax')->name('report_close_out_ajax')->middleware('auth');

    route::get('/report/cash_register_logs/details','Reports\CashRegisterController@detailedReports')->name('cash_register_log_report_details');
    route::post('/report/cash_register_logs/details/ajax','Reports\CashRegisterController@detailedReportsAjax')->name('cash_register_log_report_details_ajax');


    route::get('/report/transaction/details','Reports\TransactionReportController@detailedReports')->name('report_transaction_details');
    route::post('/report/transaction/details/ajax','Reports\TransactionReportController@detailedReportsAjax')->name('report_transaction_details_ajax');

    route::get('/employee/new','EmployeeController@GetEmployeeForm' )->name('new_employee')->middleware('auth');
    route::get('/employee/clone/{item_id}','EmployeeController@cloneEmployee' )->name('clone_employee')->middleware('auth');
    route::post('/employee/new','EmployeeController@AddEmployee')->name('new_employee')->middleware('auth');
    route::get('employee/list','EmployeeController@GetEmployeeList')->name('employee_list')->middleware('auth');
    route::get('employee/edit/{employee_id}','EmployeeController@EditEmployeeGet')->name('employee_edit')->middleware('auth');
    route::post('employee/edit/{employee_id}','EmployeeController@EditEmployeePost')->name('employee_edit')->middleware('auth');
    route::get('employee/delete/{employee_id}','EmployeeController@DeleteEmployeeGet')->name('employee_delete')->middleware('auth');
    route::post('employees/delete','EmployeeController@DeleteEmployees')->name('employees_delete')->middleware('auth');

    route::get('category/list','CategoryController@GetCategoryList')->name('category_list')->middleware('auth');
    route::post('category/new','CategoryController@AddCategory')->name('new_category')->middleware('auth');
    route::post('category/edit','CategoryController@EditCategory')->name('edit_category')->middleware('auth');
    route::post('category/delete','CategoryController@DeleteCategory')->name('delete_category')->middleware('auth');
    route::get('category/find','CategoryController@FindCategoriesByLevel')->name('categories_by_level')->middleware('auth');
    route::get('category/parent/find','CategoryController@FindCategoryParent')->name('category_parent')->middleware('auth');
    route::get('category/products/find','CategoryController@FetchProductsInCategory')->name('products_by_categories')->middleware('auth');
    route::get('category/get_data','CategoryController@getCategoryData')->name('get_category_data')->middleware('auth');
    /*route::get('tag/names','TagController@GetTags')->name('tag_names')->middleware('auth');
    route::get('tag/list','TagController@GetTagList')->name('tag_list')->middleware('auth');
    route::post('tag/new','TagController@AddTag')->name('new_tag')->middleware('auth');
    route::post('tag/edit','TagController@EditTag')->name('edit_tag')->middleware('auth');
    route::post('tag/delete','TagController@DeleteTag')->name('delete_tag')->middleware('auth');*/

    route::get('manufacturer/list','ManufacturerController@GetManufacturerList')->name('manufacturer_list')->middleware('auth');
    route::post('manufacturer/new','ManufacturerController@AddManufacturer')->name('new_manufacturer')->middleware('auth');
    route::post('manufacturer/edit','ManufacturerController@EditManufacturer')->name('edit_manufacturer')->middleware('auth');
    route::post('manufacturer/delete','ManufacturerController@DeleteManufacturer')->name('delete_manufacturer')->middleware('auth');

    route::get('/gift_card/new','GiftCardController@GetGiftCardForm' )->name('new_gift_card')->middleware('auth');
    route::post('/gift_card/new','GiftCardController@AddGiftCard')->name('new_gift_card')->middleware('auth');
    route::get('gift_card/list','GiftCardController@GetGiftCardList')->name('gift_card_list')->middleware('auth');
    route::get('gift_card/edit/{gift_card_id}','GiftCardController@EditGiftCardGet')->name('gift_card_edit')->middleware('auth');
    route::post('gift_card/edit/{gift_card_id}','GiftCardController@EditGiftCardPost')->name('gift_card_edit')->middleware('auth');
    route::get('gift_card/delete/{gift_card_id}','GiftCardController@DeleteGiftCardGet')->name('gift_card_delete')->middleware('auth');
    route::post('gift_card/use','GiftCardController@UseGiftCard')->name('gift_card_use')->middleware('auth');
    route::post('gift_cards/delete','GiftCardController@DeleteGiftCards')->name('gift_cards_delete')->middleware('auth');

    
    route::get('/counter/new','CounterController@GetCounterForm' )->name('new_counter')->middleware('auth');
    route::post('/counter/new','CounterController@AddCounter')->name('new_counter')->middleware('auth');
    route::get('counter/list','CounterController@GetCounterList')->name('counter_list')->middleware('auth');
    route::get('counter/list/ajax','CounterController@GetCounterListAjax')->name('counter_list_ajax')->middleware('auth');
    route::get('counter/edit/{counter_id}','CounterController@EditCounterGet')->name('counter_edit')->middleware('auth');
    route::post('counter/edit/{counter_id}','CounterController@EditCounterPost')->name('counter_edit')->middleware('auth');
    route::get('counter/delete/{counter_id}','CounterController@DeleteCounterGet')->name('counter_delete')->middleware('auth');
    route::post('counter/set/default','CounterController@SetDefaultCounter')->name('counter_set_default')->middleware('auth');
    route::post('counters/delete','CounterController@DeleteCounters')->name('counters_delete')->middleware('auth');
    route::get('counter/set/{counter_id}','CounterController@SetCounter')->name('counter_set')->middleware('auth');
	route::get('counter/ajax/set/{counter_id}','CounterController@SetCounterAjax')->name('counter_set_ajax')->middleware('auth');

    route::get("/cash_register/open","CashRegisterController@openNewCashRegisterGet")->name('open_cash_register')->middleware('auth');
    route::post('/cash_register/open','CashRegisterController@openNewCashRegister')->name('open_cash_register')->middleware('auth');
    route::get('/cash_register/add','CashRegisterController@addCashToRegister')->name('add_cash_to_register')->middleware('auth');
    route::post('/cash_register/add','CashRegisterController@addCashToRegisterPost')->name('add_cash_to_register')->middleware('auth');
    route::get('/cash_register/subtract','CashRegisterController@subtractCashFromRegister')->name('subtract_cash_from_register')->middleware('auth');
    route::post('/cash_register/subtract','CashRegisterController@subtractCashFromRegisterPost')->name('subtract_cash_from_register')->middleware('auth');
    route::get('/cash_register/close','CashRegisterController@closeCurrentCashRegister')->name('close_cash_register')->middleware('auth');
    route::post('/cash_register/close','CashRegisterController@closeCashRegisterPost')->name('close_cash_register')->middleware('auth');
    route::get('/cash_register/log_details/{register_id}','CashRegisterController@cashRegisterLogDetails')->name('cash_register_log_details')->middleware('auth');
    route::get('/cash_register_logs/print/summary/{register_id}','CashRegisterController@printRegisterLogSummary')->name('print_register_log_summary');
    route::get('/cash_register_logs/print/details/{register_id}','CashRegisterController@printRegisterLogDetails')->name('print_register_log_details');

    route::post('file/insert','FileController@InsertFile')->name('insert_file')->middleware('auth');
    route::post('file/item/insert','FileController@InsertItemFile')->name('insert_item_file')->middleware('auth');
    route::post('file/item/insertEdit','FileController@InsertItemFileEdit')->name('insert_item_file_edit')->middleware('auth');
    route::get('file/delete/{file_id}','FileController@DeleteFile')->name('delete_file')->middleware('auth');

    route::post('loyalty_card/use','CustomerController@UseLoyaltyCard')->name('loyalty_card_use')->middleware('auth');


});




/*Route::get('/home', 'HomeController@index')->name('home');*/

