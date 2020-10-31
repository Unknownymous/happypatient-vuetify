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
Auth::routes();
Route::get('/', function () {
    return view('layouts.app');
});


// Addresses Routes
Route::get('/provinces', 'AddressController@provinces')->name('provinces');
Route::get('/cities/{province_id}', 'AddressController@cities')->name('cities');
Route::get('/barangays/{city_id}', 'AddressController@barangays')->name('barangays');

// Patient Routes
Route::get('/patient/index', 'PatientController@index')->name('patient.index');
Route::post('/patient/store', 'PatientController@store')->name('patient.store');
Route::get('/patient/edit/{id}', 'PatientController@edit')->name('patient.edit');
Route::post('/patient/update/{id}', 'PatientController@update')->name('patient.update');
Route::post('/patient/delete', 'PatientController@delete')->name('patient.delete');

// User Routes
Route::get('/user/index', 'UserController@index')->name('patient.index');
Route::post('/user/store', 'UserController@store')->name('patient.store');
Route::get('/user/edit/{id}', 'UserController@edit')->name('patient.edit');
Route::post('/user/update/{id}', 'UserController@update')->name('patient.update');
Route::post('/user/delete', 'UserController@delete')->name('patient.delete');

// Service Routes
Route::get('/service/index', 'ServiceController@index')->name('service.index');
Route::post('/service/store', 'ServiceController@store')->name('service.store');
Route::get('/service/edit/{id}', 'ServiceController@edit')->name('service.edit');
Route::post('/service/update/{id}', 'ServiceController@update')->name('service.update');
Route::post('/service/delete', 'ServiceController@delete')->name('service.delete');

// Actity Logs Routes
Route::get('/activity_logs', 'ActivityLogController@index')->name('activity_logs');



