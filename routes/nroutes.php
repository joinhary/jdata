<?php


use App\Http\Requests\UserRequest;

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsersController;
Route::group(['prefix' => 'admin/bang-gia-dich-vu', 'as' => 'admin.bgdv.'], function () {
    Route::get('/', [BangGiaDichVuController::class,'index'])->name('index');
    Route::get('file-mau-bgdv', [BangGiaDichVuController::class,'export_file_mau_bgdv'])->name('export_file_mau');
    Route::get('download-file-mau-bgdv', [BangGiaDichVuController::class,'download_file_mau_bgdv'])->name('download_file_mau');
    Route::post('import_bgdv', [BangGiaDichVuController::class,'import_bgdv'])->name('import_bgdv');
    Route::get('ajax_edit_bgdv', [BangGiaDichVuController::class,'ajax_edit_bgdv'])->name('ajax_edit_bgdv');
    Route::post('edit_bgdv', [BangGiaDichVuController::class,'edit_bgdv'])->name('edit_bgdv');
    Route::post('create_bgdv', [BangGiaDichVuController::class,'create_bgdv'])->name('create_bgdv');
    Route::get('delete_bgdv/{id}', [BangGiaDichVuController::class,'delete_bgdv'])->name('delete_bgdv');
    Route::post('uploads_image', [BangGiaDichVuController::class,'image'])->name('uploads-bang-gia');


});

Route::group(['prefix' => 'admin/roles', 'as' => 'admin.roles.', 'middleware' => 'has_any_role:admin'], function () {
    Route::get('/', [PermissionsController::class,'roles_index'])->name('index');
    Route::get('roles-create', [PermissionsController::class,'roles_create'])->name('create');
    Route::post('roles-create', [PermissionsController::class,'roles_store']);
    Route::get('roles-edit/{id}', [PermissionsController::class,'roles_edit'])->name('edit');
    Route::post('roles-edit/{id}', [PermissionsController::class,'roles_update']);
    Route::get('roles-delete/{id}', [PermissionsController::class,'roles_destroy'])->name('delete');
});

Route::group(['prefix' => 'admin/permissions', 'as' => 'admin.permissions.', 'middleware' => 'has_any_role:admin'], function () {
    Route::get('/', [PermissionsController::class,'permissions_index'])->name('index');
    Route::get('permissions-create', [PermissionsController::class,'permissions_create'])->name('create');
    Route::post('permissions-create', [PermissionsController::class,'permissions_store']);
    Route::get('permissions-delete/{id}', [PermissionsController::class,'permissions_destroy'])->name('delete');
});


Route::group(['prefix' => 'admin/manager/users', 'as' => 'admin.manager.users.', 'middleware' => 'has_any_role:admin'], function () {
    Route::get('/', [UsersController::class,'getIndex'])->name('index');
//        ->middleware('has_any_permission:truongvanphong.readonly|truongvanphong.edit|admin.admin');
    Route::get('data', [UsersController::class,'data'])->name('data');
    Route::post('register', [UsersController::class,'register'])->name('register');
    Route::post('change_password', [UsersController::class,'change_password'])->name('change_password');
    Route::get('info_user', [UsersController::class,'info_user'])->name('info_user');
    Route::get('diary/{id}', [UsersController::class,'diary'])->name('diary');
    Route::get('ajax_active', [UsersController::class,'ajax_active'])->name('ajax_active');
    Route::get('ajax_block', [UsersController::class,'ajax_block'])->name('ajax_block');
    Route::post('destroy', [UsersController::class,'destroy'])->name('destroy');
});

Route::group(['prefix' => 'admin/taisan/lichsu', 'as' => 'admin.taisan.lichsu.'], function () {
    Route::get('/{id_ts}', [TaiSanLichSuController::class,'index'])->name('index');
    Route::get('/{id_ts}/create', [TaiSanLichSuController::class,'create'])->name('create');
    Route::post('/{id_ts}/create', [TaiSanLichSuController::class,'store']);
    Route::get('/{id_ts}/edit/{id_ls}', [TaiSanLichSuController::class,'edit'])->name('edit');
    Route::patch('/{id_ts}/edit/{id_ls}', [TaiSanLichSuController::class,'update']);
    Route::delete('/{id_ts}/delete/{id_ls}', [TaiSanLichSuController::class,'destroy'])->name('delete');

    Route::post('/formdata-image',[TaiSanLichSuController::class,'formdata_image'])->name('formdata_image');
});
    Route::get('get-image', [TaiSanLichSuController::class,'get_image'])->name('get-image-ts');




