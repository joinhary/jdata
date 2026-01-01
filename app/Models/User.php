<?php

namespace App\Models;

use Cartalyst\Sentinel\Users\EloquentUser as SentinelUser;


class User extends SentinelUser
{
    protected $table = 'users';

    protected $fillable = [
        'id','email','password','first_name','last_name','phone','gender','dob','bio','pic',
        'country','user_state','city','address','postal','provider','k_id','deleted_at',
        'id_device','id_vp','id_ccv','is_active','expried_token'
    ];

    protected $guarded = ['id'];
    protected $hidden = ['password', 'remember_token'];

    public function role_user()
    {
        return $this->belongsTo(RoleUsersModel::class, 'user_id', 'id');
    }

    public function hasRole($slug)
{
    return $this->roles->pluck('slug')->contains($slug);
}

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function user_roles()
    {
        return $this->belongsToMany(RoleModel::class, 'role_users', 'user_id', 'role_id');
    }

    public function isCustomer()
    {
        return optional($this->user_roles()->first())->slug === 'khach-hang';
    }

    // ==== NHIỀU HÀM isXXX KHÁC ==== //
    // Tôi không chỉnh vì logic bạn dùng đúng.

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function getAuthIdentifierName()
    {
        return 'id';
    }
  

    public function activityLog()
    {
        return $this->hasMany(ActivityLogModel::class, 'subject_id', 'id');
    }
   

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function nhanvien()
    {
        return $this->hasOne('App\Models\NhanVienModel', 'nv_id');
    }
    public function so_hop_dong()
    {
        return $this->hasMany('App\HopDongModel', 'ccv_id')->count();
    }

  

    public function isAdmin()
{
    return $this->hasRole('admin');
}

public function isCVS()
{
    return $this->hasRole('chuyen-vien-so');
}

public function isTruongVP()
{
    return $this->hasRole('truong-van-phong');
}

public function isCCV()
{
    return $this->hasRole('cong-chung-vien');
}

public function isCTV()
{
    return $this->hasRole('cong-tac-vien');
}

public function isLuuTru()
{
    return $this->hasRole('luu-tru-vien');
}

public function isMod()
{
    return $this->hasRole('quan-tri-vien');
}

public function isPC()
{
    return $this->hasRole('phong-khac');
}


    public function thongTinStr($data, $id_vanphong)
    {
        $templateStr = LoaiKhachHangTemplate::whereLoaiKhachHangId($this->k_id)->where('id_vanphong', '=', 2020)->first()->template_transformed;
        foreach ($data as $val) {

            $templateStr = str_replace('<' . $val->tm_keywords . '>', $val->kh_giatri ?? '...', $templateStr);
        }
        preg_match_all('/\[(.*?)\]/s', $templateStr, $matches);
        foreach ($matches[0] as $key => $match) {
            $parameters = explode(':', substr($match, 1, strlen($match) - 2));
            $value = $parameters[0];
            $conditions = explode('|', $parameters[1]);
            foreach ($conditions as $condition) {
                $tempArr = explode(',', $condition);
                if ($value == $tempArr[0]) {
                    $value = $tempArr[1];
                    break;
                }
            }
            $templateStr = str_replace($match, $value, $templateStr);
        }
        return $templateStr;
    }

    public function info($id_vanphong)
    {
        $thongTinArr = KhachHangModel::select('tm_nhan', 'tm_loai', 'tm_keywords', 'kh_giatri')
            ->where('kh_id', $this->id)
            ->join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
            ->orderBy('tieumuc.tm_id', 'asc')
            ->get();
        $hon_phoi_id = KhachHangModel::join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
            ->where('tm_keywords', 'hon-phoi')
            ->where('kh_id', $this->id)
            ->first();

        if ($hon_phoi_id) {
            $honphoi = KhachHangModel::select('tm_nhan', 'tm_loai', 'tm_keywords', 'kh_giatri', 'khachhang.tm_id')
                ->where('kh_id', $hon_phoi_id->kh_giatri)
                ->join('tieumuc', 'tieumuc.tm_id', '=', 'khachhang.tm_id')
                ->orderBy('khachhang.created_at', 'asc')
                ->get();
            foreach ($honphoi as $hp) {
                if ($hp->tm_loai == 'select' && $hp->tm_keywords != 'hon-phoi') {
                    $hp->kh_giatri = KieuTieuMucModel::find($hp->kh_giatri)['ktm_traloi'];
                }

                if ($hp->tm_loai == 'select' && $hp->tm_keywords == 'hon-phoi') {
                    $hp->kh_giatri = User::find($hp->kh_giatri)['first_name'];
                }

                if ($hp->tm_loai == 'file') {
                    if ($hp->kh_giatri) {
                        $list = [];
                        //                        foreach (json_decode($hp->kh_giatri) as $item) {
                        //                            $list[] = AppController::convert_nextcloud($item, '/khach-hang/giay-to/');
                        //                        }
                        $hp->kh_giatri = json_encode($list);
                    }
                }
            }
        } else {
            $honphoi = [];
        }

        foreach ($thongTinArr as $kh) {
            if ($kh->tm_loai == 'select' && $kh->tm_keywords != 'hon-phoi') {
                $kh->kh_giatri = KieuTieuMucModel::find($kh->kh_giatri)['ktm_traloi'];
            }
            if ($kh->tm_loai == 'select' && $kh->tm_keywords == 'hon-phoi') {
                $kh->kh_giatri = User::find($kh->kh_giatri)['first_name'];
            }
            if ($kh->tm_loai == 'file') {
                if ($kh->kh_giatri) {
                    $list = [];
                    $kh->kh_giatri = json_encode($list);
                }
            }
        }

        $lichsuhonnhan = LichSuHonNhanModel::select('ds2_id', 'first_name', 'ktm_traloi as tinhtrang')
            ->leftjoin('users', 'users.id', '=', 'ds2_id')
            ->leftjoin('kieu_tieumuc', 'ktm_id', '=', 'lshn_tinhtrang')
            ->where('ds1_id', $this->id)
            ->get();

        $thongTinStr = $this->thongTinStr($thongTinArr, $id_vanphong);
        // image arrays of this customer
        return ['thong_tin_arr' => $thongTinArr, 'thong_tin_str' => $thongTinStr, 'lich_su_hon_nhan' => $lichsuhonnhan];
    }
    

   
    
}
