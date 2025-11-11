<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRole
{
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!$request->user()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = $request->user();

        // Admin có quyền truy cập tất cả
        if ($user->isAdmin()) {
            return $next($request);
        }

        // Kiểm tra role của user có trong danh sách cho phép không
        if (!in_array($user->role, $roles)) {
            return response()->json([
                'error' => 'Bạn không có quyền truy cập chức năng này'
            ], 403);
        }

        return $next($request);
    }
}