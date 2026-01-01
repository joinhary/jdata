<?php

//Quản lý văn phòng
Route::group(['prefix' => 'admin/chinhanh', 'middleware' => 'has_any_role:admin|chuyen-vien-so'], function () {
    Route::get('index', 'ChiNhanhController@index')->name('indexChiNhanh');
    Route::get('show/{id}', 'ChiNhanhController@show')->name('showChiNhanh');
    Route::get('create', 'ChiNhanhController@create')->name('createChiNhanh');
    Route::post('store', 'ChiNhanhController@store')->name('storeChiNhanh');
    Route::get('edit/{id}', 'ChiNhanhController@edit')->name('editChiNhanh');
    Route::post('update/{id}', 'ChiNhanhController@update')->name('updateChiNhanh');
    Route::get('delete/{id}', 'ChiNhanhController@destroy')->name('destroyChiNhanh');

});

//Quản lý nhân viên
Route::group(['prefix' => 'admin/nhanvien', 'middleware' => 'has_any_role:admin|truong-van-phong|quan-tri-vien'], function () {
    Route::get('index', 'NhanVienController@index')->name('indexNhanVien');
    Route::get('show/{id}', 'NhanVienController@show')->name('showNhanVien');
    Route::get('create', 'NhanVienController@create')->name('createNhanVien');
    Route::post('store', 'NhanVienController@store')->name('storeNhanVien');
    Route::get('edit/{id}', 'NhanVienController@edit')->name('editNhanVien');
    Route::post('update/{id}', 'NhanVienController@update')->name('updateNhanVien');
    Route::post('reset/{id}', 'NhanVienController@reset')->name('resetNhanVien');
    Route::get('delete/{id}', 'NhanVienController@destroy')->name('destroyNhanVien');
});

//Quản lý kiểu
Route::group(['prefix' => 'admin/kieu', 'middleware' => 'has_any_role:admin'], function () {
    Route::get('index', 'KieuController@index')->name('indexKieu');
    Route::get('getKieu', 'KieuController@getKieu')->name('getDSKieu');
    Route::get('show/{id}', 'KieuController@show')->name('showKieu');
    Route::get('create', 'KieuController@create')->name('createKieu');
    Route::post('store', 'KieuController@store')->name('storeKieu');
    Route::get('edit/{id}', 'KieuController@edit')->name('editKieu');
    Route::post('update/{id}', 'KieuController@update')->name('updateKieu');
    Route::get('delete/{id}', 'KieuController@destroy')->name('destroyKieu');
    Route::get('check', 'KieuController@validate_form')->name('checkKieu');
});

//Quản lý tiểu mục
Route::group(['prefix' => 'admin/tieumuc', 'middleware' => 'has_any_role:admin'], function () {
    Route::get('index', 'TieuMucController@index')->name('indexTieuMuc');
    Route::get('show/{id}', 'TieuMucController@show')->name('showTieuMuc');
    Route::get('create', 'TieuMucController@create')->name('createTieuMuc');
    Route::post('store', 'TieuMucController@store')->name('storeTieuMuc');
    Route::get('edit/{id}', 'TieuMucController@edit')->name('editTieuMuc');
    Route::get('traloi/{id}', 'TieuMucController@tieumuc_menu')->name('traloiTieuMuc');
    Route::post('update/{id}', 'TieuMucController@update')->name('updateTieuMuc');
    Route::get('delete/{id}', 'TieuMucController@destroy')->name('destroyTieuMuc');
    Route::post('menu/{id}', 'TieuMucController@addToMenu')->name('contentMenu');
    Route::any('answers', 'TieuMucController@get_answers')->name('getAnswers');


});

//Quản lý điều khoản
Route::group(['prefix' => 'admin/dieukhoan', 'middleware' => 'has_any_role:admin|quan-tri-vien'], function () {
    Route::get('index', 'DieuKhoanController@index')->name('indexDieuKhoan');
    Route::get('show/{id}', 'DieuKhoanController@show')->name('showDieuKhoan');
    Route::get('create', 'DieuKhoanController@create')->name('createDieuKhoan');
    Route::post('store', 'DieuKhoanController@store')->name('storeDieuKhoan');
    Route::get('edit/{id}', 'DieuKhoanController@edit')->name('editDieuKhoan');
    Route::post('update/{id}', 'DieuKhoanController@update')->name('updateDieuKhoan');
    Route::get('delete/{id}', 'DieuKhoanController@destroy')->name('destroyDieuKhoan');
    Route::get('check', 'DieuKhoanController@check_dieukhoan')->name('checkDieuKhoan');
    Route::get('getDoanList/{id}', 'DieuKhoanController@get_doan');
});

//Quản lý điều khoản
Route::group(['prefix' => 'admin/dieukhoanv2', 'middleware' => 'has_any_role:admin|quan-tri-vien'], function () {
    Route::get('create', 'DieuKhoanControllerV2@create')->name('createDieuKhoanV2');
    Route::post('store', 'DieuKhoanControllerV2@store')->name('storeDieuKhoanV2');
    Route::get('edit/{id}', 'DieuKhoanControllerV2@edit')->name('editDieuKhoanV2');
    Route::post('update/{id}', 'DieuKhoanControllerV2@update')->name('updateDieuKhoanV2');
    Route::get('delete/{id}', 'DieuKhoanControllerV2@destroy')->name('destroyDieuKhoanV2');

});
Route::group(['prefix' => 'admin/dieukhoanv2', 'middleware' => 'has_any_role:admin|truong-van-phong|quan-tri-vien'], function () {
    Route::get('index', 'DieuKhoanControllerV2@index')->name('indexDieuKhoanV2');
    Route::get('show/{id}', 'DieuKhoanControllerV2@show')->name('showDieuKhoanV2');
    Route::get('edit/{id}', 'DieuKhoanControllerV2@edit')->name('editDieuKhoanV2');
    Route::post('update/{id}', 'DieuKhoanControllerV2@update')->name('updateDieuKhoanV2');
    Route::get('check', 'DieuKhoanControllerV2@check_dieukhoan')->name('checkDieuKhoanV2');
    Route::get('getDoanList/{id}', 'DieuKhoanControllerV2@get_doan');
});
//Quản lý khách hàng
Route::group(['prefix' => 'admin/khachhang'], function () {
    Route::get('index', 'KhachHangController@index')->name('indexKhachHang');
    Route::get('show/{id}', 'KhachHangController@show')->name('showKhachHang');
    Route::get('create', 'KhachHangController@create')->name('createKhachHang');
    Route::get('history/{id}','KhachHangController@get_history')->name('getHistory');
    Route::get('close',function (){
       return view("admin.khachhang.close");
    });
    Route::get('get_tm_select', 'KhachHangController@get_tieumuc_select')->name('getTMKHSelect');
    Route::get('get_options', 'KhachHangController@get_tieumuc_options')->name('getTMKHOptions');
    Route::get('get_edit_form', 'KhachHangController@get_tieumuc_edit')->name('getTMKHEdit');
    Route::post('store', 'KhachHangController@store')->name('storeKhachHang');
    Route::get('edit/{id}', 'KhachHangController@edit')->name('editKhachHang');
    Route::post('update/{id}', 'KhachHangController@update')->name('updateKhachHang');
    Route::get('delete/{id}', 'KhachHangController@destroy')->name('destroyKhachHang');
    Route::get('valid_cmnd', 'KhachHangController@valid_cmnd')->name('validCMND');
    Route::get('get_kieu_kh', 'KhachHangController@get_kieu')->name('getKieu');
    Route::get('change_type_kh/{idKH}/{k_newID}', 'KhachHangController@change_type_kh')->name('changeTypeKhachHang');
    Route::get('get_honphoi_tm/{k_id}', 'KhachHangController@get_honphoi_tm')->name('getHonPhoiTM');
    Route::get('get_khachhang_select', 'KhachHangController@find_khachhang_select2')->name('getKHSelect');
    Route::get('get_khachhang_select_all', 'KhachHangController@find_khachhang_select2All')->name('getKHSelectAll');

});

//Lý lịch khách hàng
Route::group(['prefix' => 'admin/lylich'], function () {
    Route::group(['prefix' => 'khachhang'], function () {
        Route::get('index/{idKH}', 'LyLichKhachHangController@index')->name('indexLyLich');
        Route::get('create/{idKH}', 'LyLichKhachHangController@create')->name('createLyLich');
        Route::get('edit/{id}', 'LyLichKhachHangController@edit')->name('editLyLich');
        Route::post('store/{idKH}', 'LyLichKhachHangController@store')->name('storeLyLich');
        Route::any('update/{id}', 'LyLichKhachHangController@update')->name('updateLyLich');
        Route::post('delete', 'LyLichKhachHangController@destroy')->name('deleteLyLich');
        Route::get('validate_sohs', 'LyLichKhachHangController@validate_sohs')->name('validSoHoSo');
        Route::get('validate_sovaoso', 'LyLichKhachHangController@validate_sovaoso')->name('validSoVaoSo');
        Route::post('add_image_handle', 'LyLichKhachHangController@add_image_handle')->name('imageAddHandle');
        Route::post('remove_image_handle', 'LyLichKhachHangController@remove_image_handle')->name('imageRemoveHandle');
        Route::get('get_image', 'LyLichKhachHangController@get_image')->name('getImage');
    });
});

Route::group(['prefix' => 'admin/uchi'], function () {
    Route::get('index', 'UchiController@index')->name('indexUchi');
    Route::get('create/{id}', 'UchiController@create')->name('createUchi');
    Route::post('push-uchi', 'UchiController@store')->name('pushUchi');
    Route::get('hide/{id}', 'UchiController@destroy')->name('hideUchi');
    Route::get('restore/{id}', 'UchiController@restore')->name('restoreUchi');
    Route::get('delete/{id}', 'UchiController@delete')->name('deleteUchi');
    Route::get('get-contract-template', 'UchiController@getContractTemplate')->name('getContractTemplates');
    Route::get('get-number-temp-obj', 'UchiController@getTempInfo')->name('getNumberTempObj');
});

Route::group(['prefix' => 'admin/convert'], function () {
    Route::get('convert-index', 'ConvertController@index')->name('indexConvert');
    Route::get('convert-number-process', 'ConvertController@read_number')->name('readNumber');
    Route::get('convert-date-process', 'ConvertController@read_date')->name('readDate');
});
Route::get('get_geometry', 'GeometryController@get_geometry')->name('getGeometry');
Route::get('get_district', 'GeometryController@districts_list')->name('getDistrict');
Route::get('get_ward', 'GeometryController@wards_list')->name('getWard');

#ctv
Route::group(['prefix' => 'admin/ctv-khach-hang'], function () {
    Route::get('index', 'CTVKhachHangController@index')->name('indexCTVKhachHang');
    Route::get('show/{id}', 'CTVKhachHangController@show')->name('showCTVKhachHang');
    Route::get('create', 'CTVKhachHangController@create')->name('createCTVKhachHang');
    Route::get('close',function (){
        return view("admin.khachhang.close");
    });
    Route::get('get_tm_select', 'CTVKhachHangController@get_tieumuc_select')->name('getCTVTMKHSelect');
    Route::get('get_options', 'CTVKhachHangController@get_tieumuc_options')->name('getCTVTMKHOptions');
    Route::get('get_edit_form', 'CTVKhachHangController@get_tieumuc_edit')->name('getCTVTMKHEdit');
    Route::post('store', 'CTVKhachHangController@store')->name('storeCTVKhachHang');
    Route::get('edit/{id}', 'CTVKhachHangController@edit')->name('editCTVKhachHang');
    Route::post('update/{id}', 'CTVKhachHangController@update')->name('updateCTVKhachHang');
    Route::get('delete/{id}', 'CTVKhachHangController@destroy')->name('destroyCTVKhachHang');
    Route::get('valid_cmnd', 'CTVKhachHangController@valid_cmnd')->name('validCTVCMND');
    Route::get('get_kieu_kh', 'CTVKhachHangController@get_kieu')->name('getCTVKieu');
    Route::get('change_type_kh/{idKH}/{k_newID}', 'CTVKhachHangController@change_type_kh')->name('changeTypeCTVKhachHang');
    Route::get('get_honphoi_tm/{k_id}', 'CTVKhachHangController@get_honphoi_tm')->name('getCTVHonPhoiTM');
    Route::get('get_khachhang_select', 'CTVKhachHangController@find_khachhang_select2')->name('getCTVKHSelect');
});
