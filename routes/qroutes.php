<?php
/**
 * Created by PhpStorm.
 * User: DELL-PC
 * Date: 2018-11-28
 * Time: 09:57
 */
/*Route::group(['prefix' => 'admin', 'middleware' => 'has_any_role:admin|truong-van-phong', 'as' => 'admin.'], function () {
    Route::group(['prefix' => 'condition'],function(){
//       Route::get('/', 'ConditionController@index');
    });

});


Route::group(['prefix' => 'doan','middleware' => 'has_any_role:admin|truong-van-phong'],function(){
    Route::get('index', 'DoanController@index')->name('indexDoan');
    Route::get('create', 'DoanController@create')->name('createDoan');
    Route::post('create', 'DoanController@store')->name('storeDoan');
    Route::get('create/{id}', 'DoanController@creates2')->name('createDoans2');
    Route::post('createDoan/{id}', 'DoanController@stores2')->name('storeDoan2');
    Route::get('listsDoan', 'DoanController@lists')->name('listDoan');
    Route::get('edit/{id}', 'DoanController@edit')->name('editDoan');
    Route::post('updateDoan/{id}', 'DoanController@update')->name('updateDoan');
    Route::get('add/{id}','DoanController@add')->name('add');
    Route::any('delete/{id}','DoanController@destroy')->name('deleteDoan');

});
*/
////Quản lý kiểu văn bản
Route::group(['prefix' => 'admin/van-ban', 'middleware' => ['has_any_role:cong-chung-vien|admin|chuyen-vien-so|truong-van-phong']], function () {
    Route::get('index', 'VanBanController@index')->name('indexVB');
    Route::get('show/{id}', 'VanBanController@show')->name('showVB');
    Route::get('create', 'VanBanController@create')->name('createVB');
    Route::get('create/{id}', 'VanBanController@creates2')->name('createVBs2');
    Route::post('store', 'VanBanController@store')->name('storeVB');
    Route::post('store/{id}', 'VanBanController@stores2')->name('storeVBs2');
    Route::get('edit/{id}', 'VanBanController@edit')->name('editVB');
    Route::post('update/{id}', 'VanBanController@update')->name('updateVB');
    Route::get('delete/{id}', 'VanBanController@destroy')->name('destroyVB');
    Route::get('vai-tro-trong-vb', 'VanBanController@getVaiTroofVB')->name('vaitroofvb');
    Route::get('van-ban-thuoc-kieu', 'VanBanController@getVBofKieuHD')->name('vbofkieu');


});
