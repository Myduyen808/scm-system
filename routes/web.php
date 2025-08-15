<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;

// Trang chủ public
Route::get('/', [HomeController::class, 'index'])->name('home');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Home route after login (redirect based on role)
Route::get('/home', function () {
    $user = auth()->user();
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
})->middleware('auth')->name('redirect.role');

// ==================== ADMIN ROUTES - ĐẦY ĐỦ ====================
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // ==== QUẢN LÝ KHO (INVENTORY) ====
    Route::get('/inventory', [AdminController::class, 'inventory'])->name('inventory');
    Route::get('/inventory/create', [AdminController::class, 'createProduct'])->name('inventory.create');
    Route::post('/inventory/store', [AdminController::class, 'storeProduct'])->name('inventory.store');
    Route::get('/inventory/{product}/edit', [AdminController::class, 'editProduct'])->name('inventory.edit');
    Route::put('/inventory/{product}', [AdminController::class, 'updateProduct'])->name('inventory.update');
    Route::delete('/inventory/{product}', [AdminController::class, 'destroyProduct'])->name('inventory.destroy');

    // Cập nhật số lượng nhanh
    Route::patch('/inventory/{product}/stock', [AdminController::class, 'updateStock'])->name('inventory.update-stock');

    // ==== QUẢN LÝ ĐỔN HÀNG (ORDERS) ====
    Route::get('/orders', [AdminController::class, 'orders'])->name('orders');
    Route::get('/orders/{order}', [AdminController::class, 'showOrder'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminController::class, 'updateOrderStatus'])->name('orders.update-status');
    Route::patch('/orders/{order}/payment-status', [AdminController::class, 'updatePaymentStatus'])->name('orders.update-payment');
    Route::delete('/orders/{order}', [AdminController::class, 'cancelOrder'])->name('orders.cancel');

    // ==== QUẢN LÝ NGƯỜI DÙNG (USERS) ====
    Route::get('/users', [AdminController::class, 'users'])->name('users');
    Route::get('/users/create', [AdminController::class, 'createUser'])->name('users.create');
    Route::post('/users', [AdminController::class, 'storeUser'])->name('users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');

    // Thay đổi vai trò user
    Route::patch('/users/{user}/role', [AdminController::class, 'updateUserRole'])->name('users.update-role');

    // ==== BÁO CÁO (REPORTS) ====
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    Route::get('/reports/revenue', [AdminController::class, 'revenueReport'])->name('reports.revenue');
    Route::get('/reports/products', [AdminController::class, 'productReport'])->name('reports.products');
    Route::get('/reports/customers', [AdminController::class, 'customerReport'])->name('reports.customers');

    // Export reports
    Route::get('/reports/export/revenue', [AdminController::class, 'exportRevenue'])->name('reports.export.revenue');
    Route::get('/reports/export/products', [AdminController::class, 'exportProducts'])->name('reports.export.products');

    // ==== CÀI ĐẶT HỆ THỐNG (SETTINGS) ====
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::post('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
});


// Employee routes
Route::middleware(['auth', 'role:employee'])->prefix('employee')->group(function () {
    Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('employee.dashboard');
    Route::get('/inventory', [EmployeeController::class, 'inventory'])->name('employee.inventory');
    Route::get('/orders', [EmployeeController::class, 'orders'])->name('employee.orders');
    Route::get('/support', [EmployeeController::class, 'support'])->name('employee.support');
});

// Customer routes
Route::middleware(['auth', 'role:customer'])->prefix('customer')->group(function () {
    Route::get('/home', [CustomerController::class, 'home'])->name('customer.home');
    Route::get('/products', [CustomerController::class, 'products'])->name('customer.products');
    Route::get('/orders', [CustomerController::class, 'orders'])->name('customer.orders');
    Route::get('/cart', [CustomerController::class, 'cart'])->name('customer.cart');
});

// Supplier routes
Route::middleware(['auth', 'role:supplier'])->prefix('supplier')->group(function () {
    Route::get('/dashboard', [SupplierController::class, 'dashboard'])->name('supplier.dashboard');
    Route::get('/products', [SupplierController::class, 'products'])->name('supplier.products');
    Route::get('/orders', [SupplierController::class, 'orders'])->name('supplier.orders');
    Route::get('/requests', [SupplierController::class, 'requests'])->name('supplier.requests');
});
