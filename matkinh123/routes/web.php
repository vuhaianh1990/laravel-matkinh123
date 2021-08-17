<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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

// Route::get('/home', 'HomeController@index')->name('home');

/************************* FrontEnd Route *************************/
Route::get('/', function () {
    return view('matkinh123::home');
});

/************************* Authentication Route *************************/
Auth::routes();

/************************* ADMIN ROUTES *************************/
Route::namespace('Admin')->prefix('admincp')->name('admin.')->middleware(['auth'])->group(function () {
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('/revenue', 'RevenueController@index')->name('revenue');
    Route::get('/cost', 'CostController@index')->name('cost');
    Route::get('/month-business-results', 'MonthResultsController@index')->name('month_business_results');
});
