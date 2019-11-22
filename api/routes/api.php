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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
//
//Route::get('/test', function (Request $request) {
//    return $request->user();
//});
Route::namespace('Api')->middleware('token')->group(function () {
    //商品
    Route::get('/product','ProductController@get');
    Route::get('/product/index','ProductController@index');
    Route::post('/product/delete','ProductController@del');
    Route::match(['get','post'],'/product/update','ProductController@update');
    Route::post('/product/create','ProductController@create');
//分类
    Route::get('/cate','CateController@get');
    Route::match(['get','post'],'/cate/update','CateController@update');
    Route::post('/cate/delete','CateController@delete');
    Route::post('/cate/create','CateController@create');
//导航
    Route::get('/nav','NavController@get');
    Route::post('/nav/create','NavController@create');
    Route::match(['get','post'],'/nav/update','NavController@update');
    Route::post('/nav/delete','NavController@delete');
//标签
    Route::get('/product_tag','ProductTagController@get');
    Route::post('/product_tag/create','ProductTagController@create');
    Route::match(['get','post'],'/product_tag/update','ProductTagController@update');
    Route::post('/product_tag/delete','ProductTagController@delete');
//库存
    Route::get('/sku','SkuController@get');
    Route::post('/sku/create','SkuController@create');
    Route::match(['get','post'],'/sku/update','SkuController@update');
    Route::post('/sku/delete','SkuController@delete');
//上传图片
    Route::post('/image/upload','ActionController@upload');
});
//登录注册
Route::post('/admin/login','Api\LoginController@login')->middleware('login');
Route::post('/admin/register','Api\LoginController@register');

