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

Route::middleware(['namespace' => 'Api'])->get('/user', function (Request $request) {
    return $request->user();
});



Route::post('test',function() {
    echo "Auth success";
})->middleware('pos-auth');


Route::group(['namespace' => 'Api'], function () {
    Route::get('/status',function() {
        return response(["success"=>true, "message" => "Api Running"]);
    });
    Route::post('/login', 'UserController@login');
    Route::post('/pin_login', 'UserController@loginByPin');
    Route::get('/counters', 'CounterController@GetCounterList')->middleware('auth:api');;

    Route::post('/cash_register/open', 'CashRegisterController@openCashRegister')->middleware('auth:api');
    Route::get('cash_register/active', "CashRegisterController@getActiveCashRegister")->middleware('auth:api');
    Route::post('cash_register/close', "CashRegisterController@closeActiveRegister")->middleware('auth:api');
    Route::get('cash_register/log/{cash_register_id}', "CashRegisterController@cashRegisterLogDetails")->middleware('auth:api');
    Route::get('/cash_register/log/print/summary/{register_id}','CashRegisterController@printRegisterLogSummary')->middleware('auth:api');
    Route::get('/cash_register/log/print/details/{register_id}','CashRegisterController@printRegisterLogDetails')->middleware('auth:api');

    Route::post('/order/process', 'OrderController@processOrder')->middleware('auth:api');
    Route::get('/orders/{id}', 'OrderController@getSaleReceipt')->middleware('auth:api');
    Route::get('/orders/{id}/receipt', 'OrderController@printOrder')->middleware('auth:api');

    Route::get('/items/autocomplete', 'ItemController@GetItemsAutocomplete')->middleware('auth:api');
    Route::get('/item/price', 'ItemController@getItemPrice')->middleware('auth:api');


    Route::get('customers','CustomerController@getCustomerList')->middleware('auth:api');
    Route::get('customers/{customer_id}','CustomerController@getCustomer')->middleware('auth:api');
});
