<?php

namespace App\Http\Controllers;

use App\Models\ActivityLogModel;
use App\Models\HistorySearchModel;
use App\Models\User;
use Carbon\Carbon;
use Cartalyst\Sentinel\Laravel\Facades\Sentinel;
use Illuminate\Http\Request;
use Exception;

class HistorySearchController extends Controller
{
    public function historySearch(Request $request)
{
    try {
        $role = Sentinel::check()
            ->user_roles()
            ->first()->slug;

        $id_vp = User::join('nhanvien', 'nhanvien.nv_id', '=', 'id')
            ->join('chinhanh', 'chinhanh.cn_id', '=', 'nhanvien.nv_vanphong')
            ->where('users.id', Sentinel::getUser()->id)->first()->cn_id;

        if($role != 'admin') {
            $query = HistorySearchModel::select('history_search.*','users.first_name')
                ->join('users','users.id','=','user_id')
                ->where('vp_id', $id_vp)
                ->orderByDesc('history_search.created_at');
        } else {
            $query = HistorySearchModel::select('history_search.*','users.first_name')
                ->join('users','users.id','=','user_id')
                ->orderByDesc('history_search.created_at');
        }

        $total = $query->count();

        // 🔥 FIX LỖI Ở ĐÂY
        $date = $request->date ?? null;

        if ($date) {
            $dates = Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            $query->whereDate('history_search.created_at', $dates);
        }

        $data = $query->paginate(15);

        $count = $data->count();

        return view('admin.history_search.index', compact('data', 'count', 'date', 'total'));
    } catch (\Exception $exception) {
        \Log::error($exception);
        return $exception;
    }
}

}
