<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Builder\KieuhopdongController;


/*
|--------------------------------------------------------------------------
| ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('admin')
    ->as('admin.')
    ->middleware(['has_any_role:admin|chuyen-vien-so|truong-van-phong'])
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | KIEU HOP DONGS
        |--------------------------------------------------------------------------
        */
        Route::controller(KieuhopdongController::class)
            ->prefix('kieuhopdongs')
            ->as('kieuhopdongs.')
            ->group(function () {

                Route::get('/', 'index')->name('index');
                Route::post('/', 'store')->name('store');

                Route::get('create', 'create')->name('create');

                Route::get('sync-kind', 'syncKind')->name('syncKind');
                Route::get('sync-kind-all', 'syncAllKind')->name('syncAllKind');

                Route::get('{id}', 'show')->name('show');
                Route::get('{id}/edit', 'edit')->name('edit');

                Route::put('{id}', 'update')->name('update');
                Route::patch('{id}', 'update');

                Route::get('{id}/delete', 'getDelete')->name('delete');
                Route::get('{id}/confirm-delete', 'getModalDelete')->name('confirm-delete');
            });
    });

/*
|--------------------------------------------------------------------------
| INFYOM GENERATOR BUILDER
|--------------------------------------------------------------------------
*/


