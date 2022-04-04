<?php


/*
|--------------------------------------------------------------------------
| Service Routes
|--------------------------------------------------------------------------
|
| Here is where you can register SERVICE routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your SERVICE!
|
*/
// Route::get('{vendor}/server', [
//     'as' => 'server.wsdl',
//     'uses' => 'WebService\WsdlController@index'
// ]);

Route::group([
    // 'middleware' => 'service'
], function () {
    Route::post('{vendor}', 'WebService\ServiceController@index');
});
