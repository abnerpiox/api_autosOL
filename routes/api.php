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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'auth:api'], function (){
    //Logout
    Route::post('logout', 'Auth\LoginController@logout');

    //Users
//    Route::get('users', 'UserController@index');
//    Route::get('users/{user}', 'UserController@show');
//    Route::put('users/{user}', 'UserController@update');
//    Route::delete('users/{user}', 'UserController@delete');

    //Rutas de cars
    Route::get('cars', 'CarController@index');
    Route::post('cars', 'CarController@store');
    Route::put('cars/{car}', 'CarController@update');
    Route::get('cars/{car}', 'CarController@show');
    Route::delete('cars/{car}', 'CarController@delete');

    //Ruta de Sales
    Route::get('sales', 'SaleController@index');
    Route::post('sales', 'SaleController@store');
    //Route::put('sales/{sale}', 'SaleController@update');
    Route::get('sales/{sale}', 'SaleController@show');
    Route::delete('sales/{sale}', 'SaleController@delete');

    //Estadisticas
    Route::get('statistics', 'StatisticsController@statistics');
});



Route::post('register', 'Auth\RegisterController@register');
Route::post('login', 'Auth\LoginController@login');



