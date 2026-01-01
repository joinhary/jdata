<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\SuuTraController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\EmployeesController;
use App\Http\Controllers\CustomerLogController;
use App\Http\Controllers\KetQuaHoatDongController;
use App\Http\Controllers\OfficeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TaiSanController;
use App\Http\Controllers\AssetsLogController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SuuTraLogController;
use App\Http\Controllers\HistorySearchController;
use App\Http\Controllers\BankController;
use App\Http\Controllers\Upload_bds_controller;
use App\Http\Controllers\ThongBaoChungController;
use App\Http\Controllers\SolariumController;
use App\Http\Controllers\AppController;
use App\Http\Controllers\VanBanController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\GeometryController;
use App\Http\Controllers\SolariumController_vp;
use App\Http\Controllers\SolariumController_nganchan;
use App\Http\Controllers\SystemController;
include_once 'web_builder.php';
include('suutra.php');
include('App.php');
include('nroutes.php');
include('taisan.php');
Route::pattern('slug', '[a-z0-9- _]+');
Route::group(['prefix' => 'admin/suutra-log'], function () {
    Route::get('/', [SuuTraLogController::class, 'index'])->name('suutralogIndex');
    Route::get('show/{id}', [SuuTraLogController::class, 'show'])->name('suutralogShow');
});
Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function () {
    Route::get('404', [SystemController::class, 'error404']);
   
    Route::get('500', [SystemController::class, 'error500']);
    # Lock screen
    Route::get('{id}/lockscreen', [UsersController::class,'lockscreen'])->name('lockscreen');
    Route::post('{id}/lockscreen', [UsersController::class,'postLockscreen'])->name('plockscreen');
    # All basic routes defined here
    Route::get('login', [AuthController::class,'getSignin'])->name('login');
    Route::get('signin', [AuthController::class,'getSignin'])->name('signin');
    Route::post('signin', [AuthController::class,'postSignin'])->name('postSignin');
    Route::post('signup', [AuthController::class,'postSignup'])->name('admin.signup');
    Route::post('forgot-password', [AuthController::class,'postForgotPassword'])->name('forgot-password');

    # Forgot Password Confirmation
    Route::get(
        'forgot-password/{userId}/{passwordResetCode}',
        [AuthController::class,'getForgotPasswordConfirm'
    ])->name('forgot-password-confirm');
    Route::post('forgot-password/{userId}/{passwordResetCode}', [AuthController::class,'getForgotPasswordConfirm']);

    # Logout
    Route::get('logout', [AuthController::class, 'getLogout'])->name('logout');

    # Account Activation
    Route::get('activate/{userId}/{activationCode}', [AuthController::class,'getActivate'])->name('activate');
});

/* cap nhat moi */
Route::group(['prefix' => 'new'], function () {
    Route::group(['middleware' => 'has_any_role:admin|truong-van-phong'], function () {
        Route::group(['prefix' => 'suu-tra'], function () {
            Route::get('/', [SuuTraController::class, 'index'])->name('new.suutra.index');
            Route::get('create', [SuuTraController::class, 'create'])->name('new.suutra.create');
        });
    });
});
Route::group(['middleware' => 'has_any_role:admin|truong-van-phong'], function () {
    Route::group(['prefix' => 'admin/ket-qua-hoat-dong'], function () {
        Route::get('/index', [KetQuaHoatDongController::class, 'index'])->name('searchViBang');
        //        Route::get('create', [KetQuaHoatDongController::class, 'create'])->name('new.suutra.create');
    });
});
Route::group([
    'prefix' => 'admin',
    'middleware' => 'has_any_role:admin|truong-van-phong|cong-chung-vien|chuyen-vien',
    'as' => 'admin.'
], function () {
    Route::group(['prefix' => 'templates', 'as' => 'templates.'], function () {
        Route::group(['prefix' => 'tai-san', 'as' => 'tai-san.'], function () {
            Route::get('index', [TemplateController::class,'index'])->name('index');
            Route::get('edit/{id}', [TemplateController::class,'edit'])->name('edit');
            Route::post('update/{id}', [TemplateController::class,'update'])->name('update');
            Route::post('store', [TemplateController::class,'store'])->name('store');
            Route::post('delete/{id}', [TemplateController::class,'delete'])->name('delete');
            Route::get('create', [TemplateController::class,'create'])->name('create');
        });
        Route::group(['prefix' => 'loai-hop-dong', 'as' => 'loai-hop-dong.'], function () {
            Route::get('index', [LoaiHopDongTemplateController::class,'index'])->name('index');
            Route::get('edit/{id}', [LoaiHopDongTemplateController::class,'edit'])->name('edit');
            Route::post('update/{id}', [LoaiHopDongTemplateController::class,'update'])->name('update');
            Route::post('store', [LoaiHopDongTemplateController::class,'store'])->name('store');
            Route::post('delete/{id}', [LoaiHopDongTemplateController::class,'delete'])->name('delete');
            Route::get('create', [LoaiHopDongTemplateController::class,'create'])->name('create');
            Route::get('convert-to-text', [LoaiHopDongTemplateController::class,'convertToText'])->name('convert-to-text');
        });
        Route::group(['prefix' => 'loai-khach-hang', 'as' => 'loai-khach-hang.'], function () {
            Route::get('index', [LoaiKhachHangTemplateController::class,'index'])->name('index');
            Route::get('edit/{id}', [LoaiKhachHangTemplateController::class,'edit'])->name('edit');
            Route::post('update/{id}', [LoaiKhachHangTemplateController::class,'update'])->name('update');
            Route::post('store', [LoaiKhachHangTemplateController::class,'store'])->name('store');
            Route::post('delete/{id}', [LoaiKhachHangTemplateController::class,'delete'])->name('delete');
            Route::get('create', [LoaiKhachHangTemplateController::class,'create'])->name('create');
            Route::get('convert-to-text', [LoaiKhachHangTemplateController::class,'convertToText'])->name('convert-to-text');
        });
    });
});


Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'as' => 'admin.'], function () {
    # User Management
    Route::group(['prefix' => 'users'], function () {
        Route::get('{user}/delete', [UsersController::class,'destroy'])->name('users.delete');
        Route::get('{user}/confirm-delete', [UsersController::class,'getModalDelete'])->name('users.confirm-delete');
        Route::get('{user}/restore', [UsersController::class,'getRestore'])->name('restore.user');
        //        Route::post('{user}/passwordreset', 'UsersController::class,'passwordreset')->name('passwordreset');
        Route::post('passwordreset', [UsersController::class,'passwordreset'])->name('passwordreset');
    });
    Route::resource('users', 'UsersController');

    Route::get(
        'deleted_users',
        ['before' => 'Sentinel', 'uses' => [UsersController::class,'getDeletedUsers']]
    )->name('deleted_users');
    

});

#frontend views
Route::get('/', [SystemController::class, 'home'])->name('home');

Route::post('admin/users/{id}', [UsersController::class,'update_avt'])->name('update1');

//Route::get('{name?}', 'FrontEndController::class,'showFrontEndView');
# End of frontend views


Route::group(['prefix' => 'admin'], function () {
    /* Quản lý văn phòng */
    Route::group(['prefix' => 'office', 'middleware' => 'has_any_role:admin|chuyen-vien-so'], function () {
        Route::get('index', [OfficeController::class, 'index'])->name('indexChiNhanh');
        Route::get('show/{id}', [OfficeController::class, 'show'])->name('showChiNhanh');
        Route::get('create', [OfficeController::class, 'create'])->name('createChiNhanh');
        Route::post('store', [OfficeController::class, 'store'])->name('storeChiNhanh');
        Route::get('delete/{id}', [OfficeController::class, 'destroy'])->name('destroyChiNhanh');
        Route::get('restore/{id}', [OfficeController::class, 'restore'])->name('restoreChiNhanh');
    });
    Route::group(['prefix' => 'office', 'middleware' => 'has_any_role:admin|chuyen-vien-so|truong-van-phong'], function () {
        Route::get('edit/{id}', [OfficeController::class, 'edit'])->name('editChiNhanh');
        Route::post('update/{id}', [OfficeController::class, 'update'])->name('updateChiNhanh');
    });
    Route::group(['prefix' => 'login-code', 'middleware' => 'has_any_role:admin|chuyen-vien-so|truong-van-phong'], function () {

        Route::get('get-login-code', [OfficeController::class,'getOfficeCode'])->name('getLoginCode');
        Route::post('set-login-code', [OfficeController::class,'setOfficeCode'])->name('setLoginCode');
    });
    #ngân hàng
    Route::group(['prefix' => 'bank'], function () {
        Route::get('index', [BankController::class, 'index'])->name('indexBank');
        Route::get('show/{id}',  [BankController::class, 'show'])->name('showBank');
        Route::get('create',  [BankController::class, 'create'])->name('createBank');
        Route::get('edit/{id}',  [BankController::class, 'edit'])->name('editBank');
        //update
        //store
        Route::post('store', [BankController::class, 'store'])->name('storeBank');
        Route::post('update/{id}',  [BankController::class, 'update'])->name('updateBank');
        Route::get('delete/{id}',  [BankController::class, 'destroy'])->name('destroyBank');
    });

    #Solr
    Route::group(['prefix' => 'basic-search'], function () {
        Route::get('/search', [SolariumController::class,'search'])->name('searchSolr');
        Route::get('/print', [SolariumController::class,'printSolr'])->name('printSolr');
        Route::get('/ping', [SolariumController::class,'ping']);
        Route::get('/delete-solr/{id}', [SolariumController::class,'deleteSolr'])->name('deleteSolr');
    });
    #Solr_nganchan
    Route::group(['prefix' => 'prevent-search'], function () {
        Route::get('/search', [SolariumController_nganchan::class,'search'])->name('searchSolr_nganchan');
        Route::get('/print', [SolariumController_nganchan::class,'printSolr'])->name('printSolr_nganchan');
        Route::get('/ping', [SolariumController_nganchan::class,'ping']);
    });
    #Solr_vp
    Route::group(['prefix' => 'office-search'], function () {
        Route::get('/search', [SolariumController_vp::class,'search'])->name('searchSolr_vp');
        Route::get('/print', [SolariumController_vp::class,'printSolr'])->name('printSolr_vp');
        Route::get('/ping', [SolariumController_vp::class,'ping']);
    });
    //BDS
    Route::group(['prefix' => 'bds'], function () {
        Route::get('index', [Upload_bds_controller::class, 'index'])->name('indexBds');
        Route::post('export', [Upload_bds_controller::class, 'export'])->name('exportBds');
        Route::post('store', [Upload_bds_controller::class, 'store'])->name('storeBds');
        Route::get('delete/{id}',  [Upload_bds_controller::class, 'destroy'])->name('destroyBDS');
        Route::post('update/{id}',  [Upload_bds_controller::class, 'update'])->name('updateBDS');
        Route::get('edit/{id}',  [Upload_bds_controller::class, 'edit'])->name('editBDS');
        Route::post('accepted',  [Upload_bds_controller::class,'accepted'])->name('acceptedBDS');
        Route::post('export_sum',  [Upload_bds_controller::class,'export_Sum'])->name('exportSum');
    });

    //Quản lý nhân viên
    Route::group(
        ['prefix' => 'employee', 'middleware' => 'has_any_role:admin|truong-van-phong|quan-tri-vien'],
        function () {
            Route::get('index', [EmployeesController::class, 'index'])->name('indexNhanVien');
            Route::get('show/{id}', [EmployeesController::class, 'show'])->name('showNhanVien');
            Route::get('create', [EmployeesController::class, 'create'])->name('createNhanVien');
            Route::post('store', [EmployeesController::class, 'store'])->name('storeNhanVien');
            Route::get('edit/{id}', [EmployeesController::class, 'edit'])->name('editNhanVien');
            Route::post('update/{id}', [EmployeesController::class, 'update'])->name('updateNhanVien');
            Route::post('reset/{id}', [EmployeesController::class, 'reset'])->name('resetNhanVien');
            Route::get('delete/{id}', [EmployeesController::class, 'destroy'])->name('destroyNhanVien');
        }
    );
    //Quản lý khách hàng
    Route::middleware('user')->group(function () {
    Route::group(['prefix' => 'customer'], function () {

        Route::get('index', [CustomerController::class, 'index'])->name('indexKhachHang');
        Route::get('show/{id}', [CustomerController::class, 'show'])->name('showKhachHang');
        Route::get('create', [CustomerController::class, 'create'])->name('createKhachHang');
        Route::get('history/{id}', [CustomerController::class, 'get_history'])->name('getHistory');

        Route::group(['prefix' => 'logs'], function () {
            Route::get('/', [CustomerLogController::class, 'index'])->name('indexKhachHangLog');
            Route::get('list/{id}', [CustomerLogController::class, 'list'])->name('listKhachHangLog');
        });

        Route::get('close', function () {
            return view("admin.khachhang.close");
        });

        Route::get('get_tm_select', [CustomerController::class, 'get_tieumuc_select'])->name('getTMKHSelect');
        Route::get('get_options', [CustomerController::class, 'get_tieumuc_options'])->name('getTMKHOptions');
        Route::get('get_edit_form', [CustomerController::class, 'get_tieumuc_edit'])->name('getTMKHEdit');

        Route::post('store', [CustomerController::class, 'store'])->name('storeKhachHang');
        Route::get('edit/{id}', [CustomerController::class, 'edit'])->name('editKhachHang');
        Route::post('update/{id}', [CustomerController::class, 'update'])->name('updateKhachHang');
        Route::get('delete/{id}', [CustomerController::class, 'destroy'])->name('destroyKhachHang');

        Route::get('valid_cmnd', [CustomerController::class, 'valid_cmnd'])->name('validCMND');
        Route::get('get_kieu_kh', [CustomerController::class, 'get_kieu'])->name('getKieu');

        Route::get('change_type_kh/{idKH}/{k_newID}', [CustomerController::class, 'change_type_kh'])
            ->name('changeTypeKhachHang');

        Route::get('get_honphoi_tm/{k_id}', [CustomerController::class, 'get_honphoi_tm'])
            ->name('getHonPhoiTM');

        Route::get('get_khachhang_select', [CustomerController::class, 'find_khachhang_select2'])
            ->name('getKHSelect');

        Route::get('get_khachhang_select_all', [CustomerController::class, 'find_khachhang_select2All'])
            ->name('getKHSelectAll');
    });
});

    // quản lý tài sản
    Route::group(['prefix' => 'assets','middleware' => 'sentinel.auth'], function () {
       
        Route::get('/', [TaiSanController::class, 'index'])->name('indexTaiSan');
        Route::get('create', [TaiSanController::class, 'create'])->name('createTaiSan');
        Route::group(['prefix' => 'logs'], function () {
            Route::get('/', [AssetsLogController::class, 'index'])->name('indexTaiSanLog');
            Route::get('list/{id}', [AssetsLogController::class, 'list'])->name('listTaiSanLog');
        });

        Route::get('destroy/{id}', [TaiSanController::class, 'destroys'])->name('destroysTaiSan');

        Route::get('showcreate/{id}', [TaiSanController::class, 'showCreate'])->name('showCreate');
        Route::post('showstore/{id}', [TaiSanController::class, 'showStore'])->name('showStoreTaiSan');

        Route::get('showedit/{id}', [TaiSanController::class, 'showEdit'])->name('showEditTaiSan');
        Route::post('showeupdate/{id}', [TaiSanController::class, 'showUpdate'])->name('updateTaiSan');

        Route::get('getKieu', [TaiSanController::class, 'getKieu'])->name('getKieuTaiSan');
        Route::get('get_tm_select', [TaiSanController::class, 'get_tieumuc_select'])->name('getTMSelect');
        Route::get('get_options', [TaiSanController::class, 'get_tieumuc_options'])->name('getTMOptions');
        Route::get('read-number', [TaiSanController::class, 'read_area'])->name('readArea');

        Route::get('showshow/{id}', [TaiSanController::class, 'showShow'])->name('showShowTaiSan');

        Route::get('change/{id}', [TaiSanController::class, 'changeCreate'])->name('changeCreate');

        Route::get('change/{id}/{id2}', [TaiSanController::class, 'changeStore'])->name('changeStore');
        Route::get('history/{id}', [TaiSanController::class, 'get_history'])->name('getHistoryTS');
    });

    Route::get('index', [ThongBaoChungController::class,'adminIndex'])->name('admin');
    Route::get('thong-bao', [ThongBaoChungController::class,'index'])->name('adminIndex');
    Route::get('/thong-bao-create', [ThongBaoChungController::class,'create'])->name('createTBC');
    Route::post('/thong-bao-store', [ThongBaoChungController::class,'store'])->name('storeTBC');
    Route::get('/thong-bao-edit/{id}', [ThongBaoChungController::class,'edit'])->name('editTBC');
    Route::post('/thong-bao-update/{id}', [ThongBaoChungController::class,'update'])->name('updateTBC');
    Route::get('/thong-bao-delete', [ThongBaoChungController::class,'delete'])->name('deleteTBC');
    Route::get('/thong-bao-show/{id}', [ThongBaoChungController::class,'show'])->name('showTBC');
    /* báo cáo thống kê */
    Route::group([
        'prefix' => 'report',
        // 'middleware' => ['has_any_role:admin,cong-chung-vien,chuyen-vien-so,truong-van-phong,chuyen-vien,ke-toan,phong-khac']

    ], function () {
        Route::get('export-example', [SuuTraController::class,'exportExample'])->name('exportExample');
        Route::get('index', [ReportController::class, 'index'])->name('indexReport');
        Route::get('export', [ReportController::class, 'export'])->name('exportReport');
        Route::get('export-draw-data', [ReportController::class, 'exportView'])->name('exportReportView');
        Route::get('export-data-bds', [ReportController::class, 'exportVanBan'])->name('exportReportBds');
    });
});


//Quản lý kiểu
Route::group(['prefix' => 'admin/kieu', 'middleware' => 'has_any_role:admin'], function () {
    Route::get('index', [KieuController::class,'index'])->name('indexKieu');
    Route::get('getKieu', [KieuController::class,'getKieu'])->name('getDSKieu');
    Route::get('show/{id}', [KieuController::class,'show'])->name('showKieu');
    Route::get('create', [KieuController::class,'create'])->name('createKieu');
    Route::post('store', [KieuController::class,'store'])->name('storeKieu');
    Route::get('edit/{id}', [KieuController::class,'edit'])->name('editKieu');
    Route::post('update/{id}', [KieuController::class,'update'])->name('updateKieu');
    Route::get('delete/{id}', [KieuController::class,'destroy'])->name('destroyKieu');
    Route::get('check', [KieuController::class,'validate_form'])->name('checkKieu');
});

//Quản lý tiểu mục
Route::group(['prefix' => 'admin/tieumuc', 'middleware' => 'has_any_role:admin'], function () {
    Route::get('index', [TieuMucController::class,'index'])->name('indexTieuMuc');
    Route::get('show/{id}', [TieuMucController::class,'show'])->name('showTieuMuc');
    Route::get('create', [TieuMucController::class,'create'])->name('createTieuMuc');
    Route::post('store', [TieuMucController::class,'store'])->name('storeTieuMuc');
    Route::get('edit/{id}', [TieuMucController::class,'edit'])->name('editTieuMuc');
    Route::get('traloi/{id}', [TieuMucController::class,'tieumuc_menu'])->name('traloiTieuMuc');
    Route::post('update/{id}', [TieuMucController::class,'update'])->name('updateTieuMuc');
    Route::get('delete/{id}', [TieuMucController::class,'destroy'])->name('destroyTieuMuc');
    Route::post('menu/{id}', [TieuMucController::class,'addToMenu'])->name('contentMenu');
    Route::any('answers', [TieuMucController::class,'get_answers'])->name('getAnswers');
});

//Quản lý điều khoản
Route::group(['prefix' => 'admin/dieukhoan', 'middleware' => 'has_any_role:admin|quan-tri-vien'], function () {
    Route::get('index', [DieuKhoanController::class,'index'])->name('indexDieuKhoan');
    Route::get('show/{id}', [DieuKhoanController::class,'show'])->name('showDieuKhoan');
    Route::get('create', [DieuKhoanController::class,'create'])->name('createDieuKhoan');
    Route::post('store', [DieuKhoanController::class,'store'])->name('storeDieuKhoan');
    Route::get('edit/{id}', [DieuKhoanController::class,'edit'])->name('editDieuKhoan');
    Route::post('update/{id}',[DieuKhoanController::class,'update'])->name('updateDieuKhoan');
    Route::get('delete/{id}', [DieuKhoanController::class,'destroy'])->name('destroyDieuKhoan');
    Route::get('check', [DieuKhoanController::class,'check_dieukhoan'])->name('checkDieuKhoan');
    Route::get('getDoanList/{id}', [DieuKhoanController::class,'get_doan']);
});

//Quản lý điều khoản
Route::group(['prefix' => 'admin/dieukhoanv2', 'middleware' => 'has_any_role:admin|quan-tri-vien'], function () {
    Route::get('create', [DieuKhoanControllerV2::class,'create'])->name('createDieuKhoanV2');
    Route::post('store', [DieuKhoanControllerV2::class,'store'])->name('storeDieuKhoanV2');
    Route::get('edit/{id}', [DieuKhoanControllerV2::class,'edit'])->name('editDieuKhoanV2');
    Route::post('update/{id}', [DieuKhoanControllerV2::class,'update'])->name('updateDieuKhoanV2');
    Route::get('delete/{id}', [DieuKhoanControllerV2::class,'destroy'])->name('destroyDieuKhoanV2');
});
Route::group(
    ['prefix' => 'admin/dieukhoanv2', 'middleware' => 'has_any_role:admin|truong-van-phong|quan-tri-vien'],
    function () {
        Route::get('index', [DieuKhoanControllerV2::class,'index'])->name('indexDieuKhoanV2');
        Route::get('show/{id}',[DieuKhoanControllerV2::class,'show'])->name('showDieuKhoanV2');
        Route::get('edit/{id}', [DieuKhoanControllerV2::class,'edit'])->name('editDieuKhoanV2');
        Route::post('update/{id}', [DieuKhoanControllerV2::class,'update'])->name('updateDieuKhoanV2');
        Route::get('check', [DieuKhoanControllerV2::class,'check_dieukhoan'])->name('checkDieuKhoanV2');
        Route::get('getDoanList/{id}', [DieuKhoanControllerV2::class,'get_doan']);
    }
);


//Lý lịch khách hàng
Route::group(['prefix' => 'admin/lylich'], function () {
    Route::group(['prefix' => 'khachhang'], function () {
        Route::get('index/{idKH}', [LyLichKhachHangController::class,'index'])->name('indexLyLich');
        Route::get('create/{idKH}', [LyLichKhachHangController::class,'create'])->name('createLyLich');
        Route::get('edit/{id}', [LyLichKhachHangController::class,'edit'])->name('editLyLich');
        Route::post('store/{idKH}', [LyLichKhachHangController::class,'store'])->name('storeLyLich');
        Route::any('update/{id}', [LyLichKhachHangController::class,'update'])->name('updateLyLich');
        Route::post('delete', [LyLichKhachHangController::class,'destroy'])->name('deleteLyLich');
        Route::get('validate_sohs', [LyLichKhachHangController::class,'validate_sohs'])->name('validSoHoSo');
        Route::get('validate_sovaoso', [LyLichKhachHangController::class,'validate_sovaoso'])->name('validSoVaoSo');
        Route::post('add_image_handle', [LyLichKhachHangController::class,'add_image_handle'])->name('imageAddHandle');
        Route::post('remove_image_handle', [LyLichKhachHangController::class,'remove_image_handle'])->name('imageRemoveHandle');
        Route::get('get_image', [LyLichKhachHangController::class,'get_image'])->name('getImage');
    });
});

Route::group(['prefix' => 'admin/uchi'], function () {
    Route::get('index', [UchiController::class,'index'])->name('indexUchi');
    Route::get('create/{id}', [UchiController::class,'create'])->name('createUchi');
    Route::post('push-uchi', [UchiController::class,'store'])->name('pushUchi');
    Route::get('hide/{id}', [UchiController::class,'destroy'])->name('hideUchi');
    Route::get('restore/{id}', [UchiController::class,'restore'])->name('restoreUchi');
    Route::get('delete/{id}', [UchiController::class,'delete'])->name('deleteUchi');
    Route::get('get-contract-template', [UchiController::class,'getContractTemplate'])->name('getContractTemplates');
    Route::get('get-number-temp-obj', [UchiController::class,'getTempInfo'])->name('getNumberTempObj');
});

Route::group(['prefix' => 'admin/convert'], function () {
    Route::get('convert-index', [ConvertController::class,'index'])->name('indexConvert');
    Route::get('convert-number-process', [ConvertController::class,'read_number'])->name('readNumber');
    Route::get('convert-date-process', [ConvertController::class,'read_date'])->name('readDate');
});
Route::get('get_geometry', [GeometryController::class,'get_geometry'])->name('getGeometry');
Route::get('get_district', [GeometryController::class,'districts_list'])->name('getDistrict');
Route::get('get_ward', [GeometryController::class,'wards_list'])->name('getWard');

#ctv
Route::group(['prefix' => 'admin/ctv-khach-hang'], function () {
    Route::get('index', [CTVKhachHangController::class,'index'])->name('indexCTVKhachHang');
    Route::get('show/{id}', [CTVKhachHangController::class,'show'])->name('showCTVKhachHang');
    Route::get('create', [CTVKhachHangController::class,'create'])->name('createCTVKhachHang');
    Route::get('close', function () {
        return view("admin.khachhang.close");
    });
    Route::get('get_tm_select', [CTVKhachHangController::class,'get_tieumuc_select'])->name('getCTVTMKHSelect');
    Route::get('get_options', [CTVKhachHangController::class,'get_tieumuc_options'])->name('getCTVTMKHOptions');
    Route::get('get_edit_form', [CTVKhachHangController::class,'get_tieumuc_edit'])->name('getCTVTMKHEdit');
    Route::post('store',[CTVKhachHangController::class,'store'])->name('storeCTVKhachHang');
    Route::get('edit/{id}', [CTVKhachHangController::class,'edit'])->name('editCTVKhachHang');
    Route::post('update/{id}', [CTVKhachHangController::class,'update'])->name('updateCTVKhachHang');
    Route::get('delete/{id}', [CTVKhachHangController::class,'destroy'])->name('destroyCTVKhachHang');
    Route::get('valid_cmnd', [CTVKhachHangController::class,'valid_cmnd'])->name('validCTVCMND');
    Route::get('get_kieu_kh', [CTVKhachHangController::class,'get_kieu'])->name('getCTVKieu');
    Route::get(
        'change_type_kh/{idKH}/{k_newID}',
        [CTVKhachHangController::class,'change_type_kh']
    )->name('changeTypeCTVKhachHang');
    Route::get('get_honphoi_tm/{k_id}', [CTVKhachHangController::class,'get_honphoi_tm'])->name('getCTVHonPhoiTM');
    Route::get('get_khachhang_select', [CTVKhachHangController::class,'find_khachhang_select2'])->name('getCTVKHSelect');
});


//Thong bao

Route::get('generator_builder', [\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController::class,'builder']);
Route::get('field_template', [\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController::class,'fieldTemplate']);
Route::post('generator_builder/generate', [\InfyOm\GeneratorBuilder\Controllers\GeneratorBuilderController::class,'generate']);


Route::group(
    ['prefix' => 'admin/manager/users', 'as' => 'admin.manager.users.', 'middleware' => 'has_any_role:admin'],
    function () {
        Route::get('/', [UsersController::class,'getIndex'])->name('index');
        Route::get('data', [UsersController::class,'data'])->name('data');
        Route::post('register', [UsersController::class,'register'])->name('register');
        Route::post('change_password', [UsersController::class,'change_password'])->name('change_password');
        Route::get('info_user', [UsersController::class,'info_user'])->name('info_user');
        Route::get('diary/{id}', [UsersController::class,'diary'])->name('diary');
        Route::get('ajax_active', [UsersController::class,'ajax_active'])->name('ajax_active');
        Route::get('ajax_block', [UsersController::class,'ajax_block'])->name('ajax_block');
        Route::post('destroy', [UsersController::class,'destroy'])->name('destroy');
    }
);


////Quản lý kiểu văn bản
Route::group([
    'prefix' => 'admin/van-ban',
    'middleware' => ['has_any_role:cong-chung-vien|admin|chuyen-vien-so|truong-van-phong']
], function () {
    Route::get('index', [VanBanController::class,'index'])->name('indexVB');
    Route::get('show/{id}', [VanBanController::class,'show'])->name('showVB');
    Route::get('create', [VanBanController::class,'create'])->name('createVB');
    Route::get('create/{id}', [VanBanController::class,'creates2'])->name('createVBs2');
    Route::post('store', [VanBanController::class,'store'])->name('storeVB');
    Route::post('store/{id}', [VanBanController::class,'stores2'])->name('storeVBs2');
    Route::get('edit/{id}',[VanBanController::class,'edit'])->name('editVB');
    Route::post('update/{id}', [VanBanController::class,'update'])->name('updateVB');
    Route::get('delete/{id}', [VanBanController::class,'destroy'])->name('destroyVB');
    Route::get('vai-tro-trong-vb', [VanBanController::class,'getVaiTroofVB'])->name('vaitroofvb');
    Route::get('van-ban-thuoc-kieu', [VanBanController::class,'getVBofKieuHD'])->name('vbofkieu');
    Route::get('sync-template', [VanBanController::class,'syncTemplate'])->name('syncTemplate');
    Route::get('sync-template-all', [VanBanController::class,'syncTemplateAll'])->name('syncTemplateAll');
});
Route::group(['prefix' => 'admin/ctv-tai-san'], function () {
    Route::get('index', [CTVTaiSanController::class,'index'])->name('indexCTVTaiSan');
    Route::get('create', [CTVTaiSanController::class,'create'])->name('createCTVTaiSan');

    Route::get('destroy/{id}', [CTVTaiSanController::class,'destroys'])->name('destroysCTVTaiSan');

    Route::get('showcreate/{id}', [CTVTaiSanController::class,'showCreate']);
    Route::post('showcreate/{id}', [CTVTaiSanController::class,'showStore'])->name('showStoreCTVTaiSan');

    Route::get('showedit/{id}', [CTVTaiSanController::class,'showEdit'])->name('showEditCTVTaiSan');
    Route::post('showeupdate/{id}', [CTVTaiSanController::class,'showUpdate']);

    Route::get('getKieu', [CTVTaiSanController::class,'getKieu'])->name('getKieuCTVTaiSan');
    Route::get('get_tm_select', [CTVTaiSanController::class,'get_tieumuc_select'])->name('getCTVTMSelect');
    Route::get('get_options', [CTVTaiSanController::class,'get_tieumuc_options'])->name('getCTVTMOptions');

    Route::get('showshow/{id}', [CTVTaiSanController::class,'showShow'])->name('showShowCTVTaiSan');

    Route::get('change/{id}', [CTVTaiSanController::class,'changeCreate'])->name('changeCTVCreate');

    Route::get('change/{id}/{id2}', [CTVTaiSanController::class,'changeStore'])->name('changeCTVStore');
});
Route::group(['prefix' => 'admin/yeucau'], function () {
    Route::get('index', [YeuCauConTroller::class,'index'])->name('indexYC');
    Route::get('show/{id}', [YeuCauConTroller::class,'show'])->name('showPhieuTaiSan');
    Route::any('save/{id}', [YeuCauConTroller::class,'save_taisan'])->name('savePhieuTaiSan');
    Route::any('sign/{id}', [YeuCauConTroller::class,'signed'])->name('signPhieuTaiSan');
    Route::any('cancel/{id}', [YeuCauConTroller::class,'cancel'])->name('cancelPhieuTaiSan');
    Route::get('hidden/{id}', [YeuCauConTroller::class,'hidden'])->name('hiddenYeucau');
    Route::any('chuyen-ccv', [YeuCauConTroller::class,'change_ccv'])->name('chuyenCCV');
    Route::any('yeu-cau-bo-sung', [YeuCauConTroller::class,'yeu_cau_bo_sung'])->name('yeucauBosung');
    Route::any('gui-hop-dong', [YeuCauConTroller::class,'send_summary_hd'])->name('sendHD');
    Route::any('confirm-received', [YeuCauConTroller::class,'confirm_received'])->name('confirmReceived');
});
Route::group(['prefix' => 'admin/history-login'], function () {
    Route::get('/', [AuthController::class, 'historyLogin'])->name('historyLogin');
});
Route::group(['middleware' => ['has_any_role:cong-chung-vien|admin|chuyen-vien-so|truong-van-phong|chuyen-vien']], function () {
    Route::get('download-img/{img}/{name}', [SuuTraController::class, 'downloadImg'])->name('downloadImg');
});


Route::group([
    'prefix' => 'admin/history-search',
    'middleware' => ['has_any_role:admin,cong-chung-vien,chuyen-vien-so,truong-van-phong']
], function () {
    Route::get('/', [HistorySearchController::class, 'historySearch'])->name('historySearch');
});

Route::get('/merge-content', [SolariumController::class,'merge_content'])->name('merge_content');
Route::get('/check-solr', [SolariumController::class,'check_solr'])->name('check_solr');
Route::get('/merge-content1', [Upload_bds_controller::class,'merge_content'])->name('merge_content1');
Route::get('/remove_2cham', [Upload_bds_controller::class,'remove_2cham'])->name('remove_2cham');
// Route::get('/dump_lost', 'Upload_bds_controller::class,'dump_lost')->name('dump_lost');
// Route::get('/get_dump_lost', 'Upload_bds_controller::class,'get_dump_lost')->name('get_dump_lost');
//luu lich su solr
Route::post('/save-History-Solr', [SolariumController::class,'saveHistoryPdf'])->name('saveHistoryPdf');
Route::get('/delete-test/{id}', [SolariumController::class,'delete']);
Route::get('/check-progress', [SystemController::class, 'checkProgress']);
Route::get('/get-sync-logs', function () {
    return response()->json([
        'logs' => session('sync_logs', [])
    ]);
});
Route::get('/get-live-logs', function () {
    $logs = session('live_logs', []);
    session()->forget('live_logs'); // clear logs after read
    return response()->json(['logs' => $logs]);
});