<?php

use App\Http\Controllers\AppController;
use App\Http\Controllers\SuuTraController;


header('Access-Control-Allow-Origin: *');
Route::get('unicode-combination-to-standard',[SuuTraController::class,'updateReverse']);

Route::get('get-uchi', [SuuTraController::class,'getUchi'])->name('getUchi');
Route::get('get-uchi-ngan-chan', [SuuTraController::class,'getUchi_nganchan'])->name('getUchi_nganchan');

Route::get('get-dotary/{id}', [SuuTraController::class,'kiemtraid']);
Route::get('get-dotary-ngan-chan/{id}', [SuuTraController::class,'kiemtraid_nganchan']);

Route::get('get-data-suutra-stp', [SuuTraController::class,'getSuutra']);

Route::post('get-id-ngan-chan/{ma_dong_bo}', [SuuTraController::class,'getIdNganChan']);

Route::get('getDataSuuTra', [SuuTraController::class,'getDataSuuTra']);
Route::get('readConfig', [SuuTraController::class,'readConfig']);
Route::post('updateSYNC/{id}', [SuuTraController::class,'updateSYNC']);

Route::get('log-uchi', [SuuTraController::class,'logUchi']);

Route::get('get-date-suutra0', [SuuTraController::class,'getNgayLoad0']);
Route::get('get-date-suutra0-ngan-chan', [SuuTraController::class,'getNgayLoad0_nganchan']);
Route::get('get-date-suutra1', [SuuTraController::class,'getNgayLoad1']);
Route::get('get-date-suutra1-ngan-chan', [SuuTraController::class,'getNgayLoad1_nganchan']);
Route::get('get-date-suutra2', [SuuTraController::class,'getNgayLoad2']);
Route::get('get-date-suutra2-ngan-chan', [SuuTraController::class,'getNgayLoad2_nganchan']);
Route::get('get-date-suutra3', [SuuTraController::class,'getNgayLoad3']);
Route::get('get-date-suutra3-ngan-chan', [SuuTraController::class,'getNgayLoad3_nganchan']);

Route::post('post-data-suutra-stp', [SuuTraController::class,'postDataSTP']);
Route::post('post-data-suutra-stp-future/{id}', [SuuTraController::class,'postDataSTPFuture']);
Route::post('update-data-suutra-stp/{id}', [SuuTraController::class,'updateDataSTP']);
Route::post('update-data-suutra-property/{id}', [uuTraController::class,'updateDataProperty']);

Route::post('update-data-suutra-stp-ngan-chan/{id}', [SuuTraController::class,'updateDataSTP_nganchan']);

Route::post('update-date-suutra-log0', [SuuTraController::class,'updateNgayLoad0']);
Route::post('update-date-suutra-log0-ngan-chan', [SuuTraController::class,'updateNgayLoad0_nganchan']);
Route::post('update-date-suutra-log1', [SuuTraController::class,'updateNgayLoad1']);
Route::post('update-date-suutra-log1-ngan-chan', [SuuTraController::class,'updateNgayLoad1_nganchan']);


//Trả về URL
Route::get('url/image', [AppController::class,'get_url']);

/*********DANH SÁCH TỈNH THÀNH-QUẬN HUYỆN-PHƯỜNG XÃ-ẤP*******/
//Lay DS tinh thành
Route::get('province/list', [er::class,'districts_list']);

//Lay DS phuong xa
Route::get('village/list', [AppController::class,'villages_list']);

//Lay DS ap,khu vuc
Route::get('ward/list', [AppController::class,'wards_list']);

//Lay DS role
Route::get('role/list', [AppController::class,'roles_list']);


/**********NHÂN VIÊN*******/
//danh sach tai khoan nhan vien
Route::get('account/nv', [AppController::class,'nvaccounts_list']);

//tao moi tai khoan nhan vien
Route::post('add/nv', [AppController::class,'createNv']);

//cap nhat tai khoan nhan vien
Route::post('update/nv', [AppController::class,'updateNv']);

//doi mat khau tai khoan
Route::post('changepass/account', [AppController::class,'changepassword']);

//xoa nhan vien
Route::get('destroy/nv', [AppController::class,'nvDestroy']);

//tim kiem nhan vien
Route::get('nv/search', [AppController::class,'search_nhanvien']);

/**********KHÁCH HÀNG*******/
//khách hàng account list
Route::get('account/kh', [AppController::class,'list_duong_su']);

//ds khách hàng có tình trạng hôn nhân khác "kết hôn"
Route::get('list/kh', [AppController::class,'list_ds']);

//tim kiem khách hàng
Route::get('kh/search', [AppController::class,'search_khachHang']);

//tim kiem khách hàng chưa kết hôn
Route::get('ds/search', [AppController::class,'search_ds']);

//xoa khách hàng
Route::get('destroy/kh', [AppController::class,'destroy_duong_su']);

//add khach hang
Route::post('add/kh', [AppController::class,'store_kh']);

//lấy thông tin chi tiết đương sự
Route::get('kh/detail', [AppController::class,'chi_tiet_duong_su']);

//form sửa đương sự
Route::get('editform/kh', [AppController::class,'edit_form_customer']);

//edit đương sự
Route::post('edit/kh', [AppController::class,'update_kh']);

/****************************CHI NHÁNH*************/
//ds chi nhanh
Route::get('chinhanh/list', [AppController::class,'dsChinhanh']);

/****************************KIỂU*************/
//ds kiểu cơ sở
Route::get('kieucs/duongsu', [AppController::class,'kieu_cs_duongsu_list']);

//ds kiểu cơ sở
Route::get('kieucs/taisan', [AppController::class,'kieu_cs_taisan_list']);

//check kieu co so tai san
Route::get('kieucs/check', [AppController::class,'check_kieu_cs_taisan']);

//ds kiểu tiểu mục duong su
Route::get('kieutm/list', [AppController::class,'kieu_list']);

//ds kiểu tiểu mục vo chong duong su
Route::get('kieutmvc/list', [AppController::class,'kieutm_vo_chong_list']);

/*************BANG GIA DICH VU*******/
Route::get('dichvu/list', [AppController::class,'bangGiaDichVu']);

/*************Tin tức thông báo*******/
//lay danh sach news
Route::get('news', [AppController::class,'tin_tuc_thong_bao']);

//lay post detail
Route::get('post/detail', [AppController::class,'get_news_detail']);

/***********TÀI SẢN***********/
//Danh sách tài sản
Route::get('taisan/list', [AppController::class,'danhsach_taisan']);

//Tìm kiếm tài sản theo nhãn
Route::get('taisan/search', [AppController::class,'search_taisan']);

//Chi tiết tài sản theo ts_id
Route::get('taisan/detail', [AppController::class,'chitiet_taisan']);

//Thêm mới tài sản
Route::post('add/taisan', [AppController::class,'store_taisan']);

//Điều chỉnh thông tin tài sản
Route::post('edit/taisan', [AppController::class,'edit_taisan']);

//Xóa tài sản
Route::get('destroy/taisan', [AppController::class,'destroy_taisan']);

/**********PHIẾU TÀI SẢN******/
//Danh sách phiếu tài sản của user đang login
Route::get('phieutaisan/list', [AppController::class,'danhsach_phieutaisan']);

//tìm kiếm phiếu tài sản theo nhãn
Route::get('phieutaisan/search', [AppController::class,'search_phieutaisan']);

//chi tiết phiếu tài sản
Route::get('phieutaisan/detail', [AppController::class,'chitiet_phieutaisan']);

//edit phiếu tài sản
Route::post('edit/phieutaisan', [AppController::class,'edit_phieutaisan']);

//Xóa phiếu tài sản
Route::get('phieutaisan/destroy', [AppController::class,'destroy_phieutaisan']);

//Xóa phiếu tài sản sau khi quá ngày hẹn
Route::get('phieutaisan/autodestroy', [AppController::class,'auto_destroy_phieutaisan']);

//Login
Route::get('login/app', [AppController::class,'login']);

//Quên mật khẩu - gởi mã xác nhận
Route::get('send/code', [AppController::class,'send_email']);

//Quên mật khẩu - xử lý mật khẩu mới
Route::post('reset/password', [AppController::class,'forget_password']);

//update mật khẩu cho user đang login
Route::post('user/changepw', [AppController::class,'update_password']);

/******SƯU TRA*******/
//danh sach suu tra gd
Route::get('suutra/list', [AppController::class,'danh_sach_suu_tra_giao_dich']);

//danh sach suu tra ngan chan
Route::get('nganchan/list', [AppController::class,'danh_sach_suu_tra_ngan_chan']);

//Chi tiet hop dong suu tra
Route::get('suutra/detail', [AppController::class,'chi_tiet_hop_dong']);

//Tim kiem suu tra giao dich
Route::get('search/giaodich', [AppController::class,'search_giao_dich']);

//Tim kiem suu tra ngan chan
Route::get('search/nganchan', [AppController::class,'search_ngan_chan']);

/********HỢP ĐỒNG********/
Route::get('hopdong/list', [AppController::class,'hopdong_list']);

//tìm kiếm hợp đồng theo số công chứng
Route::get('search/hopdong', [AppController::class,'search_hopdong']);

//Chi tiết hợp đồng
Route::get('hopdong/detail', [AppController::class,'chitiet_hopdong']);

//Chi tiết hợp đồng theo kh_id và hd_id
Route::get('hd/detail', [AppController::class,'chitiet_hd']);

//DS hồ sơ của tôi
Route::get('myhs/list', [AppController::class,'my_files_list']);

//Tìm kiếm hồ sơ của tôi
Route::get('search/myfile', [AppController::class,'search_my_files']);

//Upload images cho hợp đồng
Route::post('upload/anh_bs', [AppController::class,'upload_images_hs']);

//logo quảng cáo
Route::get('logo/list', [AppController::class,'get_logo']);
Route::get('search/suu-tra', [AppController::class,'searchSuuTra']);
