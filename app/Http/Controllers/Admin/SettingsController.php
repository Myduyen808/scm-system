<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Artisan;

class SettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:admin');
    }

    public function index()
    {
        $settings = Setting::first() ?? new Setting();
        return view('admin.settings.index', compact('settings'));

    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:20',
        ]);

        $setting = Setting::first() ?? new Setting();
        $setting->updateOrCreate(['id' => $setting->id ?? null], $validated);

        return redirect()->route('admin.settings')->with('success', 'Cài đặt đã được cập nhật thành công!');
    }

    public function backup()
    {
        try {
            Artisan::call('backup:run', ['--only-db' => true]);
            return redirect()->route('admin.settings')->with('success', 'Sao lưu database thành công! Kiểm tra thư mục storage/backup.');
        } catch (\Exception $e) {
            return redirect()->route('admin.settings')->with('error', 'Lỗi khi sao lưu: ' . $e->getMessage());
        }
    }
}
