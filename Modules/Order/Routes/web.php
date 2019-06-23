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

Route::middleware(['auth', 'verified'])->group(function () {
    Route::prefix('order')->group(function() {
        Route::get('/', 'OrderController@index')->name('order');
        Route::post('datatable', 'OrderController@dataTable')->name('order.dataTable');
        Route::get('view', 'OrderController@get')->name('order.get');
        Route::post('create', 'OrderController@create')->name('order.create');
        Route::patch('edit', 'OrderController@edit')->name('order.update');
    });

    Route::prefix('history')->group(function() {
        Route::prefix('order')->group(function() {
            Route::get('/', 'ReportController@orderHistory')->name('order.history');
            Route::get('view', 'ReportController@getOrderHistory')->name('order.history.get');
            Route::post('datatable', 'ReportController@dataTableOrderHistory')->name('order.history.dataTable');
        });
    });
});
