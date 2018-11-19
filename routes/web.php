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
    return view('welcome');
});


Route::get('/ups-address-validation', 'UpsController@address_validation');
Route::get('/ups-address-simple-validation', 'UpsController@simpleAddressValidation');
Route::get('/ups-tracking-by-number', 'UpsController@shipmentStatusByTrackNumber');
Route::get('/ups-rate', 'UpsController@upsRate');
Route::get('/ups-create-shipment', 'UpsController@createShipmentLabel');


