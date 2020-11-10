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
    Route::get('/init', [
        'uses' => 'AuthController@init',
        'as' => 'auth.init'
    ])->middleware('auth:api');

    Route::post('/login', [
        'uses' => 'AuthController@login',
        'as' => 'auth.login'
    ]);

    Route::post('/register', [
        'uses' => 'AuthController@register',
        'as' => 'auth.register'
    ]);

    Route::post('/logout', [
        'uses' => 'AuthController@logout',
        'as' => 'auth.logout'
    ])->middleware('auth:api');
});

// Addresses Routes
Route::get('/provinces', 'AddressController@provinces')->name('provinces');
Route::get('/cities/{province_id}', 'AddressController@cities')->name('cities');
Route::get('/barangays/{city_id}', 'AddressController@barangays')->name('barangays');

// Patient Routes
Route::group(['prefix' => 'patient', 'middleware' => ['auth:api']], function(){
    Route::get('/index', [
        'uses' => 'PatientController@index',
        'as' => 'patient.index',
    ]);

    Route::post('/store', [
        'uses' => 'PatientController@store',
        'as' => 'patient.store',
    ]);

    Route::get('/edit/{id}', [
        'uses' => 'PatientController@edit',
        'as' => 'patient.edit',
    ]);

    Route::post('/update/{id}', [
        'uses' => 'PatientController@update',
        'as' => 'patient.update',
    ]);

    Route::post('/delete', [
        'uses' => 'PatientController@delete',
        'as' => 'patient.delete',
    ]);

});

//Patient Services
Route::group(['prefix' => 'patientservice', 'middleware' => ['auth:api']], function(){
    
    Route::get('/index', [
        'uses' => 'PatientServiceController@index',
        'as' => 'patientservice.index',
    ]);

    Route::get('/create', [
        'uses' => 'PatientServiceController@create',
        'as' => 'patientservice.create',
    ]);

    Route::post('/store', [
        'uses' => 'PatientServiceController@store',
        'as' => 'patientservice.store',
    ]);

    Route::get('/edit/{id}', [
        'uses' => 'PatientServiceController@edit',
        'as' => 'patientservice.edit',
    ]);

    Route::post('/update/{id}', [
        'uses' => 'PatientServiceController@update',
        'as' => 'patientservice.update',
    ]);

    Route::post('/update_price', [
        'uses' => 'PatientServiceController@update_price',
        'as' => 'patientservice.update_price',
    ]);

    Route::post('/cancel/{id}', [
        'uses' => 'PatientServiceController@cancel',
        'as' => 'patientservice.cancel',
    ]);

    Route::get('/services-list-per-user', [
        'uses' => 'PatientServiceController@servicesperuser',
        'as' => 'patientservice.servicesperuser',
    ]);

    Route::get('/services-list', [
        'uses' => 'PatientServiceController@serviceslist',
        'as' => 'patientservice.serviceslist',
    ]);

});

// User Routes
Route::group(['prefix' => 'user', 'middleware' => ['auth:api']], function(){
    Route::get('/index', [
        'uses' => 'UserController@index',
        'as' => 'user.index',
    ]);

    Route::post('/store', [
        'uses' => 'UserController@store',
        'as' => 'user.store',
    ]);

    Route::get('/edit/{id}', [
        'uses' => 'UserController@edit',
        'as' => 'user.edit',
    ]);

    Route::post('/update/{id}', [
        'uses' => 'UserController@update',
        'as' => 'user.update',
    ]);

    Route::post('/delete', [
        'uses' => 'UserController@delete',
        'as' => 'user.delete',
    ]);

});

// Service Routes
Route::group(['prefix' => 'service', 'middleware' => ['auth:api']], function(){
    Route::get('/index', [
        'uses' => 'ServiceController@index',
        'as' => 'service.index'
    ]);

    Route::post('/store', [
        'uses' => 'ServiceController@store',
        'as' => 'service.store'
    ]);

    Route::get('/edit/{id}', [
        'uses' => 'ServiceController@edit',
        'as' => 'service.edit'
    ]);

    Route::post('/update/{id}', [
        'uses' => 'ServiceController@update',
        'as' => 'service.update'
    ]);

    Route::post('/delete', [
        'uses' => 'ServiceController@delete',
        'as' => 'service.delete'
    ]);
    

});

// Service Procedure Routes
Route::group(['prefix' => 'procedure', 'middleware' => ['auth:api']], function(){
    Route::get('/index', [
        'uses' => 'ServiceProcedureController@index',
        'as' => 'procedure.index'
    ]);

    Route::post('/store', [
        'uses' => 'ServiceProcedureController@store',
        'as' => 'procedure.store'
    ]);
    
    Route::get('/edit/{id}', [
        'uses' => 'ServiceProcedureController@edit',
        'as' => 'procedure.edit'
    ]);

    Route::post('/update/{id}', [
        'uses' => 'ServiceProcedureController@update',
        'as' => 'procedure.update'
    ]);
    
    Route::post('/delete', [
        'uses' => 'ServiceProcedureController@delete',
        'as' => 'procedure.delete'
    ]);

    Route::get('/template/create/{id}', [
        'uses' => 'ServiceProcedureController@content_create',
        'as' => 'content.create'
    ]);

    Route::post('/template/update/{id}', [
        'uses' => 'ServiceProcedureController@content_update',
        'as' => 'content.update'
    ]);
    
});

// Actity Logs Routes
Route::get('/activity_logs', 'ActivityLogController@index')->middleware('auth:api')->name('activity_logs');
