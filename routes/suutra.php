<?php

use App\Http\Controllers\SuuTraController;
use App\Http\Controllers\SolariumController_vp;
use App\Http\Controllers\SuuTraLogController;


Route::get('get-data-from-stp2', [SuuTraController::class, 'returnDataToNotaryOffice']);
Route::get('get-data-for-backup', [SuuTraController::class, 'returnDataToBackup']);
Route::get('update-mortage', [SuuTraController::class, 'updateMortage']);
Route::get('update-kieu', [SuuTraController::class, 'updateKieuVanBan']);


Route::group([
    'prefix' => 'admin',
  
], function () {
    Route::group(['prefix' => 'suutra'], function () {
        Route::get('index', [SuuTraController::class, 'index'])->name('indexSuutra2');
        Route::get('index-other', [SuuTraController::class, 'indexOther'])->name('indexOtherSuutra');
        Route::get('index-advanced', [SuuTraController::class, 'indexAdvanced'])->name('indexAdvancedSuutra');
        Route::get('duong-su-index', [SuuTraController::class, 'duongSuIndex'])->name('duongSuIndexSuuTra');
        Route::get('update-sync-code', [SuuTraController::class, 'updateSyncCode'])->name('updateSyncCode');
        Route::get('update-en-column', [SuuTraController::class, 'updateEnColumn'])->name('updateEnColumn');
        Route::get('delete/{id}', [SuuTraController::class, 'deleteSuutra'])->name('deleteSuutra');
        Route::get('update-doc-number', 'SuuTraController@updateNumber')->name('update_doc_number');

        Route::get('update-sync', [SuuTraController::class, 'updateReverse'])->name('updateReverse');
        Route::post('import', [SuuTraController::class, 'import'])->name('importSuutra');
        Route::post('store', [SuuTraController::class, 'store'])->name('storeSuutra');
        Route::get('edit/{id}', [SuuTraController::class, 'edit'])->name('editSuutra');
        // Route::get('do-cancel-edit/{id}', [SuuTraController::class, 'doCanCelEdit'])->name('doCanCelEdit');
        Route::get('do-cancel-edit/{id}', [SuuTraController::class, 'doCancelEdit'])->name('doCanCelEdit');
        Route::get('create-appendix/{id}', [SuuTraController::class, 'createAppendix'])->name('createAppendix');
        Route::get('giai-chap-suu-tra/{id}', [SuuTraController::class, 'giaiChapSuutra'])->name('giaiChapSuutra');

        Route::get('edit-stp/{id}', [SuuTraController::class, 'editSTP'])->name('editSuutraSTP');

        Route::get('accept', [SuuTraController::class, 'accept'])->name('acceptSuutra');
        Route::any('update/{id}', [SuuTraController::class, 'update'])->name('updateSuutra');
        Route::post('update-giai-chap/{id}', [SuuTraController::class, 'updateGiaiChap'])->name('updateGiaiChap');

        Route::get('create', [SuuTraController::class, 'createSuutra'])->name('createSuutra');
        Route::get('prevent/create', [SuuTraController::class, 'createSuutraSTP'])->name('createSuutraSTP');
        Route::get('create2', [SuuTraController::class, 'create'])->name('createSuutra2');
        Route::post('store2', [SuuTraController::class, 'store2'])->name('storeCongVanNganChan');
        Route::get('listKieu', [SuuTraController::class, 'listVanban'])->name('listVanban');
        Route::get('themduongsua', [SuuTraController::class, 'themduongsua'])->name('themduongsua');
        Route::get('duyet-suu-tra/{id}', [SuuTraController::class, 'duyetSuutra'])->name('duyetSuutra');
        Route::get('thong-tin-duong-su', [SuuTraController::class, 'thongTinDuongSu'])->name('thongTinDuongSu');
        Route::get('add-duong-su', [SuuTraController::class, 'addDuongSu'])->name('addDuongSu');
        Route::get('thong-tin-tai-san', [SuuTraController::class, 'thongTinTaiSan'])->name('thongTinTaiSan');
        Route::get('list-kieu-van-ban', [SuuTraController::class, 'listKieuVanBan'])->name('listKieuVanBan');
        Route::get('list-khach-hang', [SuuTraController::class, 'listKhachHang'])->name('listKhachHang');
        Route::get('print', [SuuTraController::class, 'print'])->name('PrintSuuTra');
        Route::get('index/new', [SuuTraLogController::class, 'indexNew'])->name('indexSuutraNew');
        Route::get('index/newOther', [SuuTraLogController::class, 'indexNewOther'])->name('indexSuutraNewOther');
        Route::get('sync-history', [SuuTraLogController::class, 'sync_history'])->name('syncHistory');
        Route::get('print/new', [SuuTraLogController::class, 'printNew'])->name('PrintSuuTraNew');
        Route::get('editSuutraSolr', [SuuTraController::class, 'editSuutraSolr'])->name('editSuutraSolr');
        // Route::post('updateSuutraSolr/{id}', [SuuTraController::class, 'updateSuutraSolr'])->name('updateSuutraSolr');
        // Accept both GET and POST
        Route::match(['get', 'post'], 'updateSuutraSolr/{id}', [SuuTraController::class, 'updateSuutraSolr'])->name('updateSuutraSolr');

        Route::post('check-trung-ho-so', [SolariumController_vp::class, 'XuLyTrungHoSo'])->name('checkTrungHoSo');
    
        Route::post('updateSuutraSolrByDate', [SuuTraController::class, 'updateSuutraSolrByDate'])->name('updateSuutraSolrByDate');
        Route::get('viewsolr', [SuuTraController::class, 'viewsolr'])->name('viewsolr');
        Route::get('/solr/import', [SuuTraController::class, 'importSolr'])->name('importsolr');
    });
});
