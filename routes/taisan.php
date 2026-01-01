<?php

Route::group(['prefix' => 'admin/ctv-tai-san'], function () {
    Route::get('index',[CTVTaiSanController::class,'index'])->name('indexCTVTaiSan');
    Route::get('create',[CTVTaiSanController::class,'create'])->name('createCTVTaiSan');

    Route::get('destroy/{id}',[CTVTaiSanController::class,'destroys'])->name('destroysCTVTaiSan');

    Route::get('showcreate/{id}',[CTVTaiSanController::class,'showCreate']);
    Route::post('showcreate/{id}',[CTVTaiSanController::class,'showStore'])->name('showStoreCTVTaiSan');

    Route::get('showedit/{id}',[CTVTaiSanController::class,'showEdit'])->name('showEditCTVTaiSan');
    Route::post('showedit/{id}',[CTVTaiSanController::class,'showUpdate']);

    Route::get('getKieu',[CTVTaiSanController::class,'getKieu'])->name('getKieuCTVTaiSan');
    Route::get('get_tm_select',[CTVTaiSanController::class,'get_tieumuc_select'])->name('getCTVTMSelect');
    Route::get('get_options',[CTVTaiSanController::class,'get_tieumuc_options'])->name('getCTVTMOptions');

    Route::get('showshow/{id}',[CTVTaiSanController::class,'showShow'])->name('showShowCTVTaiSan');

    Route::get('change/{id}',[CTVTaiSanController::class,'changeCreate'])->name('changeCTVCreate');

    Route::get('change/{id}/{id2}',[CTVTaiSanController::class,'changeStore'])->name('changeCTVStore');

});
Route::group(['prefix' => 'admin/yeucau'], function () {
    Route::get('index',[YeuCauConTroller::class,'index'])->name('indexYC');
    Route::get('show/{id}',[YeuCauConTroller::class,'show'])->name('showPhieuTaiSan');
    Route::any('save/{id}',[YeuCauConTroller::class,'save_taisan'])->name('savePhieuTaiSan');
    Route::any('sign/{id}',[YeuCauConTroller::class,'signed'])->name('signPhieuTaiSan');
    Route::any('cancel/{id}',[YeuCauConTroller::class,'cancel'])->name('cancelPhieuTaiSan');
    Route::get('hidden/{id}',[YeuCauConTroller::class,'hidden'])->name('hiddenYeucau');
    Route::any('chuyen-ccv',[YeuCauConTroller::class,'change_ccv'])->name('chuyenCCV');
    Route::any('yeu-cau-bo-sung',[YeuCauConTroller::class,'yeu_cau_bo_sung'])->name('yeucauBosung');
    Route::any('gui-hop-dong',[YeuCauConTroller::class,'send_summary_hd'])->name('sendHD');
    Route::any('confirm-received', [YeuCauConTroller::class,'confirm_received'])->name('confirmReceived');



});
