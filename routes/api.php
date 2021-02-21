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

Route::post('/device/register', 'Auth\RegisterDeviceController@store')->name('api.device.register');
Route::middleware('auth.device')->post('/device/register-receivers', 'Auth\RegisterDeviceController@registerReceivers')->name('api.device.registerReceivers');
Route::middleware('auth.device')->put('/device/update-location', 'Auth\RegisterDeviceController@updateLocation')->name('api.device.updateLocation');

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
