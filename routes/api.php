<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::prefix('auth')->group(function(){
    Route::get('/init', 'AuthController@init')->name('init');
    Route::post('/login', 'AuthController@login')->name('login');
    Route::post('/register', 'AuthController@register')->name('register');
    Route::post('/logout', 'AuthController@logout')->name('logout');
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

// Service Procedure Routes
Route::get('/procedure/index', 'ServiceProcedureController@index')->name('procedure.index');
Route::post('/procedure/store', 'ServiceProcedureController@store')->name('procedure.store');
Route::get('/procedure/edit/{id}', 'ServiceProcedureController@edit')->name('procedure.edit');
Route::post('/procedure/update/{id}', 'ServiceProcedureController@update')->name('procedure.update');
Route::post('/procedure/delete', 'ServiceProcedureController@delete')->name('procedure.delete');
Route::get('/procedure/template/create/{id}', 'ServiceProcedureController@content_create')->name('content.create');
Route::post('/procedure/template/update/{id}', 'ServiceProcedureController@content_update')->name('content.update');

// Actity Logs Routes
Route::get('/activity_logs', 'ActivityLogController@index')->name('activity_logs');
