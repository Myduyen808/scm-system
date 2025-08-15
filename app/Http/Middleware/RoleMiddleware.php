<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role)
    {
        $user = Auth::user();

        if (! $user || ! $user->hasRole($role)) {
            abort(403, 'Bạn không có quyền truy cập.');
        }

        return $next($request);
    }
}
