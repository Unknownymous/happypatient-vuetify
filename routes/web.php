<?php

use Illuminate\Support\Facades\Route;

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
    return view('layouts.app');
});

Route::get('/provinces', 'AddressController@provinces')->name('provinces');
Route::get('/cities/{province_id}', 'AddressController@cities')->name('cities');
Route::get('/barangays/{city_id}', 'AddressController@barangays')->name('barangays');

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
