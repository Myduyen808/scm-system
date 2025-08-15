<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, $permission)
    {
        $user = Auth::user();

        if (! $user || ! $user->hasPermissionTo($permission)) {
            abort(403, 'Bạn không có quyền truy cập.');
        }

        return $next($request);
    }
}
