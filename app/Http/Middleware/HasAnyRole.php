<?php

namespace App\Http\Middleware;

use Closure;
use Sentinel;

class HasAnyRole
{
    public function handle($request, Closure $next, $roles = '')
{
    $user = Sentinel::getUser();
    
    if (!$user) {
        abort(403, 'Chưa đăng nhập');
    }

    // ⚠️ Fix lỗi roles là Collection
    if ($roles instanceof \Illuminate\Support\Collection) {
        $roles = $roles->implode(',');
    }

    if (!is_string($roles)) {
        abort(403, 'Cấu hình middleware role sai');
    }

    $roleList = array_map(
        'trim',
        preg_split('/[,|]/', strtolower($roles))
    );

    $userRoles = $user->roles
        ->pluck('slug')
        ->map(fn ($r) => strtolower($r))
        ->toArray();

    if (empty(array_intersect($roleList, $userRoles))) {
        abort(403, 'Không có quyền truy cập chức năng này');
    }

    return $next($request);
}

}
