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
Route::any('/wechat','WechatController@valid');
Route::any('/wechat','WechatController@checkSignature');
Route::any('/wechat','WechatController@responseMsg');
Route::any('/wechat','WechatController@receiveText');
Route::any('/wechat','WechatController@receiveEvent');
Route::any('/wechat','WechatController@callTuling');