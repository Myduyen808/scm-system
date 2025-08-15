<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');
        $remember = $request->filled('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Chuyển hướng theo vai trò
            return $this->redirectBasedOnRole();
        }

        throw ValidationException::withMessages([
            'email' => ['Thông tin đăng nhập không chính xác.'],
        ]);
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Gán vai trò customer mặc định cho user mới
        $user->assignRole('customer');

        // Tự động đăng nhập sau khi đăng ký
        Auth::login($user);

        return redirect()->route('customer.home')
                         ->with('success', 'Đăng ký thành công! Chào mừng bạn đến với SCM System.');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')
                         ->with('success', 'Đã đăng xuất thành công.');
    }

    private function redirectBasedOnRole()
    {
        $user = Auth::user();

        if ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        } elseif ($user->hasRole('employee')) {
            return redirect()->route('employee.dashboard');
        } elseif ($user->hasRole('customer')) {
            return redirect()->route('customer.home');
        } elseif ($user->hasRole('supplier')) {
            return redirect()->route('supplier.dashboard');
        }

        return redirect()->route('home');
    }
}
