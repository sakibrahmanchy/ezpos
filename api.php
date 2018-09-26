<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::post('authenticate', 'Api\AuthenticateController@authenticate');
Route::post('authenticate_by_pin', 'Api\AuthenticateController@authenticateByPin');

Route::get('settings', 'Api\DataController@getSettings');

Route::post('customer/add', 'Api\AuthenticateController@addCustomer');
Route::post('customer/authenticate', 'Api\AuthenticateController@authenticateCustomer');

Route::get('menus', 'Api\DataController@getMenus');
Route::get('products/{menu}', 'Api\DataController@getProducts');
Route::get('taxes/', 'Api\DataController@getTaxes');
Route::get('discounts/', 'Api\DataController@getDiscounts');
Route::get('order_types/', 'Api\DataController@getOrderTypes');
Route::get('locations/', 'Api\DataController@getLocations');
Route::get('floor_plan/', 'Api\DataController@getFloorPlan');

Route::post('order/', 'Api\DataController@setOrder');
Route::post('order/update', 'Api\DataController@updateOrder');
Route::post('order/update/customer_info', 'Api\DataController@updateOrderCustomerInfo');
Route::post('order/update/payment_info', 'Api\DataController@updateOrderPaymentInfo');
Route::post('order/delete', 'Api\DataController@deleteOrder');
Route::get('orders/{location_id}', 'Api\DataController@getOrders');
Route::get('orders/details/{id}', 'Api\DataController@getOrderDetails');
Route::get('orders/waitress/{user_id}', 'Api\DataController@getOrdersForWaitress');
Route::get('orders/print/{id}', 'Api\DataController@printOrder');
Route::get('orders/send_notification/kitchen/{id}/{type}', 'Api\DataController@sendNotificationToKitchen');

Route::get('order/cooking/{order}', 'Api\DataController@setOrderStatusCooking');
Route::get('order/ready/{order}', 'Api\DataController@setOrderStatusReady');
Route::get('order/served/{order}', 'Api\DataController@setOrderStatusServed');
Route::get('order/paid/{order}', 'Api\DataController@setOrderStatusPaid');
Route::get('order/close/{order}', 'Api\DataController@setOrderStatusClose');

Route::get('orders/statistics/day/{date}', 'Api\DataController@getOrderStaticsForDay');
Route::post('orders/statistics/day/print', 'Api\DataController@printOrderStaticsForDay');

Route::get('order/item/ready/{order}/{product_id}', 'Api\DataController@setOrderItemStatusReady');
Route::get('order/cocktail/ready/{order}/{cocktail_id}', 'Api\DataController@setOrderCocktailStatusReady');
Route::get('order/combo/item/ready/{order}/{combo_order_id}/{product_id}', 'Api\DataController@setOrderComboItemStatusReady');

Route::post('cart/item/add', 'Api\CartController@addCartItem');
Route::post('cart/combo/add', 'Api\CartController@addCartCombo');
Route::get('cart/item/get', 'Api\CartController@getCartItems');
Route::post('cart/item/delete', 'Api\CartController@deleteCartItem');
Route::post('cart/combo/delete', 'Api\CartController@deleteCartCombo');



//Customer
Route::get('customer/tax', 'Api\CustomerController@getTax');
Route::get('braintree/token', 'Api\CustomerController@getToken');
Route::post('braintree/payment', 'Api\CustomerController@braintreePayment');
Route::post('cart/set/order', 'Api\CustomerController@setOrder');


// Test

Route::get('printer/test', 'Api\DataController@printerTest');
Route::get('pusher/test', 'Api\DataController@pusherTest');
Route::get('socket/test', 'Api\DataController@socketTest');
