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
    Route::prefix('category')->group(function() {
        Route::get('/', 'CategoryController@index')->name('category');
        Route::post('datatable', 'CategoryController@dataTable')->name('category.dataTable');
        Route::get('view', 'CategoryController@get')->name('category.get');
        Route::post('create', 'CategoryController@create')->name('category.create');
        Route::patch('edit', 'CategoryController@edit')->name('category.update');
        Route::delete('delete', 'CategoryController@delete')->name('category.delete');
    });
});
