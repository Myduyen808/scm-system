<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleOrPermissionMiddleware
{
    public function handle(Request $request, Closure $next, $roleOrPermission)
    {
        $user = Auth::user();

        if (! $user || (! $user->hasRole($roleOrPermission) && ! $user->hasPermissionTo($roleOrPermission))) {
            abort(403, 'Bạn không có quyền truy cập.');
        }

        return $next($request);
    }
}
