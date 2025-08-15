<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RedirectBasedOnRole
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            // Chuyển hướng theo vai trò
            if ($user->hasRole('admin')) {
                return redirect()->route('admin.dashboard');
            } elseif ($user->hasRole('employee')) {
                return redirect()->route('employee.dashboard');
            } elseif ($user->hasRole('customer')) {
                return redirect()->route('customer.home');
            } elseif ($user->hasRole('supplier')) {
                return redirect()->route('supplier.dashboard');
            }
        }

        return $next($request);
    }
}
