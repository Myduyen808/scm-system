<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;

// Trang chủ public
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [HomeController::class, 'index'])->name('about');

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

Route::middleware('auth')->group(function () {


});

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
        // Import Excel vào inventory
        Route::post('/inventory/import', [AdminController::class, 'importInventory'])->name('inventory.import');

        // Cập nhật số lượng nhanh
        Route::get('/inventory/forecast', [AdminController::class, 'forecastInventory'])->name('inventory.forecast');
        Route::get('/orders/export', [AdminController::class, 'exportOrders'])->name('orders.export');
        Route::get('/orders/stats', [AdminController::class, 'orderStats'])->name('orders.stats');
        Route::post('/tickets/{id}/assign', [AdminController::class, 'assignTicket'])->name('tickets.assign');
        Route::get('/tickets', [AdminController::class, 'tickets'])->name('tickets');


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
        Route::post('/backup', [AdminController::class, 'backupDatabase'])->name('settings.backup');

        // ==== Giám sát hoạt động (Activity Log) ====
        Route::get('/logs', [AdminController::class, 'activityLogs'])->name('logs');



        // Quản lý khuyến mãi
        Route::get('/promotions', [AdminController::class, 'promotions'])->name('promotions');
        Route::get('/promotions/create', [AdminController::class, 'create'])->name('promotions.create');
        Route::post('/promotions', [AdminController::class, 'store'])->name('promotions.store');
        Route::get('/promotions/{promotion}/edit', [AdminController::class, 'edit'])->name('promotions.edit');
        Route::put('/promotions/{promotion}', [AdminController::class, 'update'])->name('promotions.update');
        Route::delete('/promotions/{promotion}', [AdminController::class, 'destroy'])->name('promotions.destroy');
        Route::post('/promotions/{promotion}/apply-product', [AdminController::class, 'applyProduct'])->name('promotions.applyProduct');

    // Quản lý sản phẩm (bao gồm phê duyệt)
        Route::post('/products', [ProductController::class, 'store'])->name('products.store');
        Route::patch('/products/{id}/approve', [ProductController::class, 'approve'])->name('products.approve');
        Route::get('/pending-products', [ProductController::class, 'pendingProducts'])->name('pending.products');
        });


// Employee routes
Route::middleware(['auth', 'role:employee'])->prefix('employee')->group(function () {
    Route::get('/dashboard', [EmployeeController::class, 'dashboard'])->name('employee.dashboard');
    Route::get('/inventory', [EmployeeController::class, 'inventory'])->name('employee.inventory');
    Route::patch('/inventory/{product}', [EmployeeController::class, 'updateInventory'])->name('employee.inventory.update');
    Route::get('/inventory/create', [EmployeeController::class, 'createInventory'])->name('employee.inventory.create');
    Route::post('/inventory', [EmployeeController::class, 'storeInventory'])->name('employee.inventory.store');
    Route::get('/inventory/{product}', [EmployeeController::class, 'showProduct'])->name('employee.inventory.show');
    Route::delete('/inventory/{product}', [EmployeeController::class, 'destroyInventory'])->name('employee.inventory.destroy');
    Route::get('/orders', [EmployeeController::class, 'orders'])->name('employee.orders');
    Route::patch('/orders/{order}/status', [EmployeeController::class, 'updateOrderStatus'])->name('employee.orders.update-status');
    Route::get('/orders/{order}', [EmployeeController::class, 'showOrder'])->name('employee.orders.show');
    Route::post('/orders/{order}/cancel', [EmployeeController::class, 'cancelOrder'])->name('employee.orders.cancel');
    Route::get('/promotions', [EmployeeController::class, 'promotions'])->name('employee.promotions');
    Route::get('/promotions/create', [EmployeeController::class, 'createPromotion'])->name('employee.promotions.create');
    Route::post('/promotions', [EmployeeController::class, 'storePromotion'])->name('employee.promotions.store');
    Route::get('/promotions/{promotion}/edit', [EmployeeController::class, 'editPromotion'])->name('employee.promotions.edit');
    Route::put('/promotions/{promotion}', [EmployeeController::class, 'updatePromotion'])->name('employee.promotions.update');
    Route::delete('/promotions/{promotion}', [EmployeeController::class, 'destroyPromotion'])->name('employee.promotions.destroy');
    Route::get('/requests', [EmployeeController::class, 'requests'])->name('employee.requests');
    Route::post('/requests/{request}/process', [EmployeeController::class, 'processRequest'])->name('employee.requests.process');
    Route::get('/support', [EmployeeController::class, 'support'])->name('employee.support');
    Route::get('/support/{ticket}/reply', [EmployeeController::class, 'replySupportTicket'])->name('employee.support.reply');
    Route::post('/support/{ticket}/reply', [EmployeeController::class, 'storeSupportReply'])->name('employee.support.store-reply');
    Route::get('/reports', [EmployeeController::class, 'reports'])->name('employee.reports');


    Route::get('/pending-products', [ProductController::class, 'pendingProducts'])->name('pending.products');
    Route::patch('/products/{id}/approve', [ProductController::class, 'approve'])->name('products.approve');
    Route::get('/approved-products', [ProductController::class, 'approvedProducts'])->name('approved.products');

});

//customer routes
Route::middleware(['auth', 'role:customer'])->group(function () {
    Route::get('/customer/home', [CustomerController::class, 'home'])->name('customer.home');
    Route::get('/customer/products', [CustomerController::class, 'products'])->name('customer.products');
    Route::get('/customer/products/{id}', [CustomerController::class, 'showProduct'])->name('customer.products.show');
    Route::post('/customer/cart/add/{id}', [CustomerController::class, 'addToCart'])->name('cart.add');
    Route::get('/customer/cart', [CustomerController::class, 'cart'])->name('customer.cart');
    Route::post('/customer/cart/update/{id}', [CustomerController::class, 'updateCart'])->name('cart.update');
    Route::delete('/customer/cart/remove/{id}', [CustomerController::class, 'removeFromCart'])->name('cart.remove');
    Route::get('/customer/checkout', [CustomerController::class, 'checkout'])->name('customer.checkout');
    Route::post('/customer/checkout', [CustomerController::class, 'placeOrder'])->name('customer.checkout.store');
    Route::get('/customer/orders', [CustomerController::class, 'orders'])->name('customer.orders');
    Route::get('/customer/orders/{id}', [CustomerController::class, 'showOrder'])->name('customer.orders.show');
    Route::get('/customer/orders/confirm/{id}', [CustomerController::class, 'confirmOrder'])->name('customer.order.confirm');
    Route::get('/customer/orders/track', [CustomerController::class, 'trackOrder'])->name('customer.orders.track');
    Route::get('/customer/addresses', [CustomerController::class, 'addresses'])->name('customer.addresses');
    Route::post('/customer/addresses', [CustomerController::class, 'storeAddress'])->name('customer.addresses.index');
    Route::get('/customer/addresses/create', [CustomerController::class, 'createAddress'])->name('customer.addresses.create');
    Route::put('/customer/addresses/{id}', [CustomerController::class, 'updateAddress'])->name('customer.addresses.update');
    Route::delete('/customer/addresses/{id}', [CustomerController::class, 'deleteAddress'])->name('customer.addresses.delete');
    Route::get('/customer/support', [CustomerController::class, 'support'])->name('customer.support');
    Route::post('/customer/support', [CustomerController::class, 'createTicket'])->name('customer.support.create');
    Route::get('/customer/support/{id}', [CustomerController::class, 'showTicket'])->name('customer.support.show');
    Route::post('/customer/support/{id}/reply', [CustomerController::class, 'replyTicket'])->name('customer.support.reply');
    Route::get('/customer/reviews/create/{product_id}', [CustomerController::class, 'createReview'])->name('customer.reviews.create');
    Route::post('/customer/reviews', [CustomerController::class, 'storeReview'])->name('customer.reviews.store');

    Route::get('/customer/promotions', [CustomerController::class, 'promotions'])->name('customer.promotions');
});

// Routes cho Supplier
Route::middleware(['auth', 'role:supplier'])->group(function () {
    Route::get('/supplier/dashboard', [SupplierController::class, 'dashboard'])->name('supplier.dashboard');
    Route::get('/supplier/products', [SupplierController::class, 'products'])->name('supplier.products');
    Route::get('/supplier/products/create', [SupplierController::class, 'createProduct'])->name('supplier.products.create');
    Route::post('/supplier/products', [SupplierController::class, 'storeProduct'])->name('supplier.products.store');
    Route::get('/supplier/products/{id}/edit', [SupplierController::class, 'editProduct'])->name('supplier.products.edit');
    Route::put('/supplier/products/{id}', [SupplierController::class, 'updateProduct'])->name('supplier.products.update');
    Route::delete('/supplier/products/{id}', [SupplierController::class, 'destroyProduct'])->name('supplier.products.destroy');
    Route::put('/supplier/products/{id}/stock', [SupplierController::class, 'updateStock'])->name('supplier.products.updateStock');

    Route::get('/supplier/orders', [SupplierController::class, 'orders'])->name('supplier.orders');
    Route::get('/supplier/orders/{id}', [SupplierController::class, 'showOrder'])->name('supplier.orders.show');

    Route::get('/supplier/requests', [SupplierController::class, 'requests'])->name('supplier.requests');
    Route::post('/supplier/requests/{id}/process', [SupplierController::class, 'processRequest'])->name('supplier.requests.process');
});
