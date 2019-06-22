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
    Route::prefix('menu')->group(function() {
        Route::get('/', 'MenuController@index')->name('menu');
        Route::post('datatable', 'MenuController@dataTable')->name('menu.dataTable');
        Route::get('view', 'MenuController@get')->name('menu.get');
        Route::post('create', 'MenuController@create')->name('menu.create');
        Route::patch('edit', 'MenuController@edit')->name('menu.update');
        Route::delete('delete', 'MenuController@delete')->name('menu.delete');
    });
});
