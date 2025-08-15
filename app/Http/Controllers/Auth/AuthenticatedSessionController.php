<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Providers\RouteServiceProvider; // Thêm dòng này

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = Auth::user(); // hoặc auth()->user();
        
        if (!$user) {
            return redirect('/login'); // Phòng khi chưa đăng nhập
        }

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }
        if ($user->hasRole('employee')) {
            return redirect()->route('employee.dashboard');
        }
        if ($user->hasRole('customer')) {
            return redirect()->route('customer.home');
        }
        if ($user->hasRole('supplier')) {
            return redirect()->route('supplier.dashboard');
        }

        return redirect()->intended('/dashboard');

    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
