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
Route::middleware('session.has.user')->group(function () {
    Route::any('/dashboard', 'DashboardController@index')->name('dashboard');
});
// Route::get('/', function () {
//     return view('sessions._signin');
// });
Route::get('/','AuthController@signin');
Route::post('/login','AuthController@login')->name('login');
Route::get('/logout','AuthController@logout')->name('logout');
Route::view('/signup', 'sessions._signup')->name('signup');
Route::post('/postregister','AuthController@register')->name('postregister');
Route::get('/verify','AuthController@verify')->name('verify');
//----------------------------------------------------dashboard----------------------------------------------------------
Route::get('/dashboard', 'DashboardController@index')->name('dashboard');
Route::get('/export_all', 'DashboardController@export');
//----------------------------------------------------products-----------------------------------------------------------
Route::get('/products', 'ProductsController@index')->name('products');
Route::post('/searchproduct', 'ProductsController@search')->name('searchproduct');
Route::delete('/deleteproduct', 'ProductsController@delete')->name('deleteproduct');
Route::get('/product/{product_id}', 'ProductdetailController@index');
Route::get('/export_product', 'ProductsController@export');
Route::get('/export_stockcard/{product__id}', 'ProductdetailController@export');
Route::get('/editproduct/{product_id}', 'EditproductController@index');
Route::put('/editproduct', 'EditproductController@edit');
Route::get('/addproduct', 'AddproductController@index');
Route::post('/postproduct', 'AddproductController@store');
Route::get('/deleteimage/{product_file_id}', 'EditproductController@deleteimage');
//----------------------------------------------------stocks-----------------------------------------------------------
Route::get('/stocks', 'StocksController@index')->name('stocks');
Route::post('/searchstocks', 'StocksController@search');
//----------------------------------------------------category-----------------------------------------------------------
Route::get('/category', 'CategoryController@index')->name('category');
Route::post('/searchcategory', 'CategoryController@search')->name('searchcategory');
Route::post('/addcategory', 'CategoryController@store');
Route::put('/editcategory', 'CategoryController@edit');
Route::delete('/deletecategory', 'CategoryController@delete');
//----------------------------------------------------tranfer-----------------------------------------------------------
Route::get('/tranfer/{product_id}', 'TranferController@index');
Route::post('/posttranfer', 'TranferController@tranfer')->name('posttranfer');
//----------------------------------------------------adjust-----------------------------------------------------------
Route::get('/adjust/{product_id}', 'AdjustController@index');
Route::post('/postadjust', 'AdjustController@adjust')->name('postadjust');
//----------------------------------------------------purchase-----------------------------------------------------------
Route::get('/purchases', 'PurchasesController@index')->name('purchases');
Route::post('/searchpurchase', 'PurchasesController@search')->name('searchpurchase');
Route::put('/statuspurchase', 'PurchasesController@put_status')->name('statuspurchase');
Route::get('/export_purchase', 'PurchasesController@export');
Route::get('/addpurchase', 'AddpurchaseController@index');
Route::get('/addpurchase/{product_id}', 'AddpurchaseController@index');
Route::get('/editpurchase/{purchase_id}', 'EditpurchaseController@index');
Route::put('/editpurchase', 'EditpurchaseController@edit');
Route::get('/purchase/{purchase_id}', 'PurchasedetailController@index');
Route::post('/postpurchase', 'AddpurchaseController@store');
Route::put('/cancelpurchase', 'PurchasesController@cancel_status');

//----------------------------------------------------sell-----------------------------------------------------------
Route::get('/sells', 'SellsController@index')->name('sells');
Route::post('/searchsell', 'SellsController@search')->name('searchsell');
Route::put('/statussell', 'SellsController@put_status')->name('statussell');
Route::get('/export_sell', 'SellsController@export');
Route::get('/addsell', 'AddsellController@index');
Route::get('/addsell/{product_id}', 'AddsellController@index');
Route::get('/editsell/{sell_id}', 'EditsellController@index');
Route::put('/editsell', 'EditsellController@edit');
Route::get('/sell/{sell_id}', 'SelldetailController@index');
Route::post('/postsell', 'AddsellController@store');
Route::put('/cancelsell', 'SellsController@cancel_status');

//----------------------------------------------------credits-----------------------------------------------------------
Route::get('credits', 'CreditsController@index')->name('credits');
Route::post('cash', 'CreditsController@cash')->name('cash');
Route::get('test', 'CreditsController@test')->name('test');

//----------------------------------------------------others-----------------------------------------------------------
Route::view('notfound', 'others._notFound')->name('notfound');

//----------------------------------------------------admin-----------------------------------------------------------
Route::get('/admin/dashboard', 'AdmindashboardController@index');
Route::get('/export_user', 'AdmindashboardController@export');
Route::get('/admin/adminsell', 'AdminsellController@index');
Route::post('/searchadminsell', 'AdminsellController@search')->name('searchadminsell');
Route::get('/admin/shipping/{sell_id}', 'AdminshippingController@index');
Route::post('/postshipping', 'AdminshippingController@shipping')->name('postshipping');
Route::post('printlebel', 'LabelController@print');
Route::get('/admin/adminbuy', 'AdminbuyController@index');
Route::post('/searchadminbuy', 'AdminbuyController@search')->name('searchadminbuy');
Route::get('/admin/po/{purchase_id}', 'AdminbuyController@po');








//Auth::routes();

//Route::get('/home', 'HomeController@index')->name('home');
