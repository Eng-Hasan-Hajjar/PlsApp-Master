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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/VisLogIn',['uses'=>'ApiController@VisitorLogIn']);

Route::post('/VisRegister',['uses'=>"ApiController@VisitorRegister"]);

Route::get('CategoryAll/{limit}/{SortKey}/{SortType}',['uses'=>'ApiController@CategoryAll']);

Route::get('CategoryOne/{CatId}',['uses'=>'ApiController@CategoryOne']);

Route::get('ServiceAll/{limit}/{SortKey}/{SortType}',['uses'=>'ApiController@ServiceAll']);

Route::get('ServiceOne/{ServiceId}',['uses'=>'ApiController@ServiceOne']);

Route::get('ServiceByCat/{CatId}/{limit}/{SortType}/{SortKey}',['uses'=>"ApiController@ServiceByCat"]);


Route::group(['middleware' =>  [ 'auth:api','jwt.auth']], function () {
    
    Route::get('VisInfo',['uses'=>'ApiController@VisitorInfo']);

    Route::post('VisUpdate',['uses'=>'ApiController@VisitorUpdate']);

    Route::get('VisLogOut',['uses'=>'ApiController@VisitorLogOut']);

    Route::post('SaveRate',['uses'=>'ApiController@SaveRate']);

    Route::post('SaveOrder',['uses'=>'ApiController@SaveOrder']);

    Route::get('OrderAll/{limit}/{SortType}/{SortKey}',['uses'=>'ApiController@OrderAll']);

    Route::get('OrderOne/{OrderId}',['uses'=>'ApiController@OrderOne']);
    

});




