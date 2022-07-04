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

Route::get('/', function () {
    return redirect('login');
});

Route::get('forgot/your/password', 'UserController@showForgotPassword');
Route::post('forgot/your/password/email', 'UserController@checkEmail');
Route::post('/forgot/your/password/verification/code', 'UserController@verificationCode');
Route::post('/forgot/your/password/change', 'UserController@changePassword');

Auth::routes();

Route::get('printer-detect', 'AdditionalController@printerDetect');

Route::post('printNota', 'AdditionalController@printNota');

Route::group(['middleware' => 'auth'], function () {
    /* Dashboard sebagai halaman pertama setelah login */
    Route::get('dashboard', 'HomeController@index');
    Route::post('sales/{id}/update-status', 'HomeController@updateStatus');
    Route::post('sales/{id}/delete-status', 'HomeController@deleteStatus');

    Route::get('credit/{id}', 'HomeController@creditIndex');
    Route::post('credit/create', 'CreditController@store');
    Route::post('datatable/credits', 'CreditController@datatable');


    /* My Account (Update Profile & Password) */
    Route::get('user/profile', 'AuthController@getMyAccount');
    Route::post('user/update-profile', 'AuthController@postUpdateProfile');
    Route::post('user/update-password', 'AuthController@postUpdatePassword');
    Route::get('user/{user_id}/reset-password', 'UserController@showResetPassword');

    /* Datatable */
    Route::post('datatable/users', 'UserController@datatable');
    Route::post('datatable/roles', 'RoleController@datatable');
    Route::post('datatable/products', 'ProductController@datatable');
    Route::post('datatable/products/raw-material', 'ProductController@datatableRawProduct');
    Route::post('datatable/customers', 'CustomerController@datatable');
    Route::post('datatable/sales/tabledit', 'AdditionalController@salesTabledit');
    Route::post('datatable/product/tabledit', 'AdditionalController@productTabledit');
    Route::post('datatable/sales', 'SalesController@datatable');
    Route::post('datatable/recipe', 'RecipeController@datatable');
    Route::post('datatable/stock-low', 'StockController@datatableLowStock');
    Route::post('datatable/stock/history-admin', 'StockController@datatableHistoryAdmin');
    Route::post('datatable/stock/history-user', 'StockController@datatableHistoryUser');
    Route::post('datatable/stock/activity', 'StockController@datatableActivity');
    Route::post('datatable/stocks', 'StockController@datatable');
    Route::post('datatable/stocks/raw-material', 'StockController@datatableRawProduct');
    Route::post('datatable/recipe/raw-material', 'RecipeController@datatableRawProduct');
    Route::post('datatable/dashboard', 'HomeController@datatableSales');

    /* Master Data */
    Route::get('data-master/product', 'ProductController@index');
    Route::get('data-master/product/create', 'ProductController@create');
    Route::post('data-master/product/create', 'ProductController@store');
    Route::get('data-master/product/{id}/edit', 'ProductController@edit');
    Route::put('data-master/product/{id}/edit', 'ProductController@update');
    Route::delete('data-master/product/{id}/delete', 'ProductController@destroy');

    Route::get('data-master/recipe', 'RecipeController@index');
    Route::get('data-master/recipe/create', 'RecipeController@create');
    Route::post('data-master/recipe/create', 'RecipeController@store');
    Route::get('data-master/recipe/{id}/edit', 'RecipeController@edit');
    Route::put('data-master/recipe/{id}/edit', 'RecipeController@update');
    Route::delete('data-master/recipe/{id}/delete', 'RecipeController@destroy');

    Route::get('data-master/stock', 'StockController@index');
    Route::get('data-master/stock/manage', 'StockController@manage');
    Route::get('data-master/stock/history', 'StockController@history');
    Route::post('data-master/stock/manage', 'StockController@manageUpdate');
    Route::get('data-master/stock/activity', 'StockController@activity');
    Route::post('data-master/stock/{id}/rollback', 'StockController@rollback');

    Route::get('data-master/stock-low', 'StockController@lowStockIndex');
    Route::post('data-master/stock-low/adjust', 'StockController@lowStockAdjust');

    Route::get('data-master/customer', 'CustomerController@index');
    Route::get('data-master/customer/create', 'CustomerController@create');
    Route::post('data-master/customer/create', 'CustomerController@store');
    Route::get('data-master/customer/{id}/edit', 'CustomerController@edit');
    Route::put('data-master/customer/{id}/edit', 'CustomerController@update');
    Route::delete('data-master/customer/{id}/delete', 'CustomerController@destroy');

    Route::post('data-master/address/create', 'AddressController@store');
    Route::put('data-master/address/{id}/edit', 'AddressController@update');
    Route::delete('data-master/address/{id}/delete', 'AddressController@destroy');

    Route::post('data-master/telephone/create', 'TelephoneController@store');
    Route::put('data-master/telephone/{id}/edit', 'TelephoneController@update');
    Route::delete('data-master/telephone/{id}/delete', 'TelephoneController@destroy');

    /* Sales */
    Route::get('sales', 'SalesController@index');
    Route::get('sales/create', 'SalesController@create');
    Route::post('sales/create', 'SalesController@store');
    Route::get('sales/{id}/detail', 'SalesController@getSales');
    Route::delete('sales/{id}/delete', 'SalesController@destroy');

    /* Surat Jalan */
    Route::get('surat-jalan', 'SuratJalanController@index');
    Route::post('surat-jalan/create', 'SuratJalanController@store');

    /* Additional */
    Route::post('additional/sales/customer', 'AdditionalController@getCustomerInfo');
    Route::post('additional/recipe/product', 'AdditionalController@getProductIngredientsInfo');
    Route::post('additional/upload', 'AdditionalController@upload');


    /* Configuration */
    Route::get('configuration/user', 'UserController@index');
    Route::get('configuration/user/create', 'UserController@create');
    Route::post('configuration/user/create', 'UserController@store');
    Route::get('configuration/user/{id}/edit', 'UserController@edit');
    Route::put('configuration/user/{id}/edit', 'UserController@update');
    Route::delete('configuration/user/{id}/delete', 'UserController@destroy');

    Route::get('configuration/role', 'RoleController@index');
    Route::get('configuration/role/create', 'RoleController@create');
    Route::post('configuration/role/create', 'RoleController@store');
    Route::get('configuration/role/{id}/edit', 'RoleController@edit');
    Route::put('configuration/role/{id}/edit', 'RoleController@update');
    Route::delete('configuration/role/{id}/delete', 'RoleController@destroy');

    /* JSTree */
    Route::get('jstree/menu', 'RoleController@generateMenu');

    Route::resource('importcsv', 'ImportCSVController');
    Route::post('import-store-csv', 'ImportCSVController@readCSVtoArr');

    /*
     * API Controller
     * untuk melakukan uji coba request API
     * contoh dapat dilihat pada API Controller
    */
    Route::get('get-token', 'APIController@getToken');
    Route::get('get-all-user', 'APIController@getAllUser');
});
