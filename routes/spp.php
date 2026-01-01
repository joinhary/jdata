<?php

use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\SPP\SPPController;


Route::post('auth-token', [SPPController::class, 'getToken'])->name('getToken');
Route::group([
    'prefix' => 'spp',
    'as' => 'spp.',
    'middleware' => ['auth_api']
], function () {

    Route::get('suutra', [SPPController::class, 'suutra'])->name('suutra');
    Route::get('vpcc-list', [SPPController::class, 'vpccList'])->name('vpccList');

});
