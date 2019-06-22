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
    Route::prefix('branch')->group(function() {
        Route::get('/', 'BranchController@index')->name('branch');
        Route::post('datatable', 'BranchController@dataTable')->name('branch.dataTable');
        Route::get('view', 'BranchController@get')->name('branch.get');
        Route::post('create', 'BranchController@create')->name('branch.create');
        Route::patch('edit', 'BranchController@edit')->name('branch.update');
    });

    if(Module::has('Menu')) {
        Route::prefix('menu')->group(function() {
            Route::prefix('branch')->group(function() {
                Route::get('/', 'BranchMenuController@index')->name('menu.branch');
                Route::post('datatable', 'BranchMenuController@dataTable')->name('menu.branch.dataTable');
                Route::get('view', 'BranchMenuController@get')->name('menu.branch.get');
                Route::post('create', 'BranchMenuController@create')->name('menu.branch.create');
                Route::patch('edit', 'BranchMenuController@edit')->name('menu.branch.update');
                Route::delete('delete', 'BranchMenuController@delete')->name('menu.branch.delete');
                Route::patch('availability', 'BranchMenuController@setAvailability')->name('menu.branch.availability');
            });
        });
    }
});
