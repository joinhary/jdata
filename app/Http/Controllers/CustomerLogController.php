<?php

namespace App\Http\Controllers;

use App\Models\KhachHangLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CustomerLogController extends Controller
{
    public function index(Request $request)
    {
        $date = explode('-', $request->date);
        if ($date) {
            $created_from = $date[0] ?? null;
            $created_to = $date[1] ?? null;
        }
        $creator_id = $request->creator_id ?? null;
        $kh_id = $request->kh_id ?? '';
        $search = $request->name;
        $creators = User::leftjoin('role_users', 'role_users.user_id', '=', 'users.id')
            ->leftjoin('roles', 'roles.id', '=', 'role_users.role_id')
            ->where('roles.slug', '!=', 'khach-hang')
            ->pluck('users.first_name', 'users.id');
        $where = [];
        if ($creator_id) {
            $where[] = ['khach_hang_logs.creator_id', '=', $creator_id];
        }
        $customers = User::rightjoin('role_users', 'role_users.user_id', '=', 'users.id')
            ->rightjoin('roles', 'roles.id', '=', 'role_users.role_id')
            ->rightjoin('khach_hang_logs', 'khach_hang_logs.kh_id', '=', 'users.id')
            ->where('slug', 'khach-hang')
            ->select([
                'users.id',
                'users.first_name as name',
                'khach_hang_logs.kh_id',
                'khach_hang_logs.log_content',
                'khach_hang_logs.creator_id',
                'khach_hang_logs.created_at',
            ])
            ->orderby('khach_hang_logs.created_at', 'desc')
            ->where('users.first_name', 'like', '%' . $request->name . '%')
            ->where($where);
        $tong = $customers->get()->count();
        $customers = $customers->paginate(20);
        $logs = $customers->map(function ($log) use ($customers, $creator_id) {
            $log_content = json_decode($log->log_content);
            $basic = $log_content->basic;
            $tieu_muc = $log_content->tieu_muc;
            $hon_nhan = $log_content->hon_nhan;
            $creator_name = (User::find($log->creator_id)) ? User::find($log->creator_id)->first_name : "không tìm thấy";
            $tieu_muc = array_column($tieu_muc, 'kh_giatri', 'tm_keywords');
            $customer_name = $log->name;
            $creator_id = $log->creator_id;
            return [
                'kh_id' => $log->kh_id,
                'creator_id' => $creator_id,
                'creator_name' => $creator_name,
                'customer_name' => $customer_name,
                'created_at' => Carbon::parse($log->created_at)->format('d/m/Y H:i:s')
            ];
        });
        if ($kh_id != '') {
            $logs = $logs->where('kh_id', $kh_id);
        }
        if ($creator_id != '') {
            $logs = $logs->where('creator_id', $creator_id);
        }
        if ($created_from != '') {
            $logs = $logs->where('created_at', '>=', Carbon::parse($created_from)->format('d/m/Y 00:00:00'));
        }

        if ($created_to != '') {
            $logs = $logs->where('created_at', '<=', Carbon::parse($created_to)->format('d/m/Y 23:59:59'));
        }
        $count = $logs->count();
        return view('admin.khach_hang_log.index', compact(
            'search', 'creators', 'tong', 'logs', 'customers', 'count',
            'kh_id', 'creator_id', 'created_from', 'created_to'));
    }

    public function list($kh_id)
    {
        $logs = KhachHangLog::join('users', 'users.id', '=', 'khach_hang_logs.creator_id')
            ->select([
                'users.first_name',
                'khach_hang_logs.kh_id as id',
                'khach_hang_logs.log_content',
                'khach_hang_logs.creator_id',
                'khach_hang_logs.created_at',
            ])->orderBy('khach_hang_logs.created_at', 'desc')
            ->where('kh_id', $kh_id)->get();
        $logs = $logs->map(function ($log) {
            $log_content = json_decode($log->log_content);
            $basic = $log_content->basic;
            $tieu_muc = $log_content->tieu_muc;
            $hon_nhan = $log_content->hon_nhan;
            $creator_name = $log->first_name;
            $tieu_muc = array_column($tieu_muc, 'kh_giatri', 'tm_keywords');
            $customer_name = (isset($tieu_muc['ho-duong-su'])) ? $tieu_muc['ho-duong-su'] . ' ' . $tieu_muc['ten-duong-su'] : $tieu_muc['ho-ten-nguoi-dai-dien'];
            return [
                'note' => $log_content->note ?? "Không có ghi chú",
                'id' => $log->id,
                'creator_name' => $creator_name,
                'customer_name' => $customer_name,
                'created_at' => Carbon::parse($log->created_at)->format('d/m/Y H:i:s')
            ];
        });
        return view('admin.khach_hang_log.list', compact('logs'));
    }
}
