<?php

namespace App\Http\Controllers;

use App\Models\NhanVienModel;
use App\Models\TaiSanLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Sentinel;

class AssetsLogController extends Controller
{
    public function index(Request $request)
    {
        $date = explode('-', $request->date);
        if ($date) {
            $created_from = $date[0] ?? null;
            $created_to = $date[1] ?? null;
        }
        $creator_id = $request->creator_id ?? '';
        $ts_id = $request->ts_id ?? '';
        $search = $request->tai_san;
        $creators = User::leftjoin('role_users', 'role_users.user_id', '=', 'users.id')
            ->leftjoin('roles', 'roles.id', '=', 'role_users.role_id')
            ->where('roles.slug', '!=', 'khach-hang')
            ->pluck('users.first_name', 'users.id');
        $where = [];
        if ($creator_id) {
            $where[] = ['khach_hang_logs.creator_id', '=', $creator_id];
        }
        $tai_san = TaiSanLog::join('users', 'users.id', '=', 'tai_san_logs.creator_id')
            ->join('taisan', 'taisan.ts_id', '=', 'tai_san_logs.ts_id')
            ->where('taisan.ts_nhan', 'like', '%' . $request->tai_san . '%')
            ->select([
                'users.first_name',
                'tai_san_logs.ts_id',
                'tai_san_logs.log_content',
                'tai_san_logs.creator_id'
            ])->orderBy('tai_san_logs.created_at', 'desc');
        $tong = $tai_san->get()->count();
        $tai_san = $tai_san->paginate(15);
        $logs = $tai_san->map(function ($log) {
            $log_content = json_decode($log->log_content);
            $data = $log_content->data;
            $content = array_column($data, 'ts_giatri', 'tm_id');
            $name = (isset($content['30'])) ? $content['30'] : "No Name";
            $creator_name = $log->first_name;
            return [
                'note' => $log_content->note ?? "Không có ghi chú",
                'ts_id' => $log->ts_id,
                'creator_name' => $creator_name,
                'creator_id' => $log->creator_id,
                'customer_name' => $name,
                'created_at' => Carbon::parse($log->created_at)->format('d/m/Y H:i:s')
            ];
        });
        if ($ts_id != '') {
            $logs = $logs->where('ts_id', $ts_id);
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
        return view('admin.tai_san_logs.index', compact('search', 'creators', 'logs', 'count', 'tai_san', 'tong'));
    }

    public function list($ts_id)
    {
        $logs = TaiSanLog::join('users', 'users.id', '=', 'tai_san_logs.creator_id')
            ->where('ts_id', $ts_id)
            ->select([
                'users.first_name',
                'tai_san_logs.ts_id as id',
                'tai_san_logs.log_content',
                'tai_san_logs.creator_id',
                'tai_san_logs.created_at'
            ])->orderBy('tai_san_logs.created_at', 'desc')
            ->get();
        $logs = $logs->map(function ($log) {
            $log_content = json_decode($log->log_content);
            $data = $log_content->data;
            $content = array_column($data, 'ts_giatri', 'tm_id');
            $name = $content['30'];
            $creator_name = $log->first_name;
            return [
                'note' => $log_content->note ?? "Không có ghi chú",
                'id' => $log->id,
                'creator_name' => $creator_name,
                'customer_name' => $name,
                'created_at' => Carbon::parse($log->created_at)->format('d/m/Y H:i:s')
            ];
        });
        return view('admin.tai_san_logs.list', compact('logs'));
    }
}
