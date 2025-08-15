<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Tạo vai trò
        $adminRole = Role::create(['name' => 'admin']);
        $employeeRole = Role::create(['name' => 'employee']);
        $customerRole = Role::create(['name' => 'customer']);

        // Tạo quyền
        $permissions = [
            'manage inventory', // Quản lý kho
            'manage orders',    // Quản lý đơn hàng
            'manage promotions', // Quản lý khuyến mãi
            'support customer',  // Hỗ trợ khách hàng
            'view reports',     // Xem báo cáo
            'view products',    // Xem sản phẩm
            'place order',      // Đặt hàng
            'make payment',     // Thanh toán
            'track order',      // Theo dõi đơn hàng
            'review product',   // Đánh giá sản phẩm
            'submit complaint', // Gửi khiếu nại
            'manage supplier products', // Quản lý sản phẩm nhà cung cấp
            'track supplier orders',   // Theo dõi đơn hàng nhà cung cấp
            'respond to requests',     // Phản hồi yêu cầu nhập hàng
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Gán quyền cho vai trò
        $adminRole->givePermissionTo([
            'manage inventory', 'manage orders', 'manage promotions',
            'support customer', 'view reports'
        ]);

        $employeeRole->givePermissionTo([
            'manage inventory', 'manage orders', 'manage promotions',
            'support customer', 'view reports'
        ]);

        $customerRole->givePermissionTo([
            'view products', 'place order', 'make payment',
            'track order', 'review product', 'submit complaint'
        ]);

        $supplierRole = Role::create(['name' => 'supplier']);
        $supplierRole->givePermissionTo([
            'manage supplier products', 'track supplier orders', 'respond to requests'
        ]);

        //  Tạo role customer nếu chưa tồn tại
        if (!Role::where('name', 'customer')->exists()) {
            $customerRole = Role::create(['name' => 'customer']);
            $customerRole->givePermissionTo([
                'view products',
                'place order',
                'make payment',
                'track order',
                'review product',
                'submit complaint'
            ]);
    }
    
    }

}
