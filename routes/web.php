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
    route::get('customer/edit/{customer_id}','CustomerController@EditCustomerGet')->name('customer_edit')->middleware('auth');
    route::post('customer/edit/{customer_id}','CustomerController@EditCustomerPost')->name('customer_edit')->middleware('auth');
    route::get('customer/delete/{customer_id}','CustomerController@DeleteCustomerGet')->name('customer_delete')->middleware('auth');
    Route::post('customers/delete','CustomerController@DeleteCustomers')->name('customers_delete')->middleware('auth');

    route::get('/item/new','ItemController@GetItemForm' )->name('new_item')->middleware('auth');
    route::post('/item/new','ItemController@AddItem')->name('new_item')->middleware('auth');
    route::get('item/list','ItemController@GetItemList')->name('item_list')->middleware('auth');
    route::get('item/edit/{item_id}','ItemController@EditItemGet')->name('item_edit')->middleware('auth');
    route::post('item/edit/{item_id}','ItemController@EditItemPost')->name('item_edit')->middleware('auth');
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

    route::get('/supplier/new','SupplierController@GetSupplierForm' )->name('new_supplier')->middleware('auth');
    route::post('/supplier/new','SupplierController@AddSupplier')->name('new_supplier')->middleware('auth');
    route::get('supplier/list','SupplierController@GetSupplierList')->name('supplier_list')->middleware('auth');
    route::get('supplier/edit/{supplier_id}','SupplierController@EditSupplierGet')->name('supplier_edit')->middleware('auth');
    route::post('supplier/edit/{supplier_id}','SupplierController@EditSupplierPost')->name('supplier_edit')->middleware('auth');
    route::get('/supplier/export','SupplierController@SuppliersDataToExcel' )->name('supplier_to_excel')->middleware('auth');
    route::get('supplier/delete/{supplier_id}','SupplierController@DeleteSupplierPost')->name('supplier_delete')->middleware('auth');
    route::post('suppliers/delete','SupplierController@DeleteSuppliers')->name('suppliers_delete')->middleware('auth');

    route::get('/sale/new','SaleController@GetSaleForm' )->name('new_sale')->middleware('auth');
    route::post('/sale/new','SaleController@AddSale')->name('new_sale')->middleware('auth');
    route::post('/sale/suspend','SaleController@SuspendSale')->name('suspend_sale')->middleware('auth');
    route::get('sale/suspended','SaleController@GetSuspendedSale')->name('suspended_sale_list')->middleware('auth');
    route::get('/sale/receipt/{sale_id}','SaleController@GetSaleReceipt')->name('sale_receipt')->middleware('auth');
    route::get('sale/edit/{sale_id}','SaleController@EditSaleGet')->name('sale_edit')->middleware('auth');
    route::post('sale/edit/{sale_id}','SaleController@EditSalePost')->name('sale_edit')->middleware('auth');
    route::get('sale/show_last_sale','SaleController@showLastSaleReceipt')->name('sale_last_receipt')->middleware('auth');
    route::get('sale/suspended/list','SaleController@GetSuspendedSales')->name('suspended_sale_list')->middleware('auth');
    route::get('sale/download_receipt/{sale_id}','SaleController@DownloadSaleReceipt')->name('download_sale_receipt')->middleware('auth');
    route::get('sale/mail_receipt/{sale_id}','SaleController@MailSaleReceipt')->name('mail_sale_receipt')->middleware('auth');
    route::get('sale/search','SaleController@SearchSaleGet')->name('search_sale')->middleware('auth');
    route::post('sale/search','SaleController@SearchSalePost')->name('search_sale')->middleware('auth');
    route::get('sale/print/{sale_id}','SaleController@printSaleReciept')->name('print_sale')->middleware('auth');

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

    route::get('/employee/new','EmployeeController@GetEmployeeForm' )->name('new_employee')->middleware('auth');
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

    route::post('file/insert','FileController@InsertFile')->name('insert_file')->middleware('auth');
    route::post('file/item/insert','FileController@InsertItemFile')->name('insert_item_file')->middleware('auth');
    route::post('file/item/insertEdit','FileController@InsertItemFileEdit')->name('insert_item_file_edit')->middleware('auth');
    route::get('file/delete/{file_id}','FileController@DeleteFile')->name('delete_file')->middleware('auth');


});




/*Route::get('/home', 'HomeController@index')->name('home');*/
