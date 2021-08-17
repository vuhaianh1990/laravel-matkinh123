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

/************************* Authentication Route *************************/
Route::get('/login', 'Auth\LoginController@login');
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


/************************* ADMIN ROUTES *************************/
Route::namespace('Admin')->group(function () {
    Route::post('statistics', 'StatisticsController@getStatistics');

    // Revenues
    Route::post('revenues', 'RevenueController@getList');
    Route::put('revenues', 'RevenueController@addRevenue');
    Route::delete('revenues', 'RevenueController@delRow');
    Route::put('revenues/{id}', 'RevenueController@editRevenue');

    // Costs
    Route::post('costs', 'CostController@getList');
    Route::put('costs', 'CostController@addCost');
    Route::delete('costs', 'CostController@delRow');
    Route::put('costs/{id}', 'CostController@editCost');
});

