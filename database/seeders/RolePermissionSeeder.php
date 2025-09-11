<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionSeeder extends Seeder
{
    public function run()
    {
        // Xóa cache quyền để đảm bảo dữ liệu mới
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        // Tạo quyền (permissions)
        $permissions = [
            'manage inventory',
            'manage orders',
            'manage users',
            'view reports',
            'manage settings',
            'manage promotions',
            'support customer',
            'view products',
            'place orders',
            'update stock',
            'manage tickets',
            'approve products', // Mới: Quyền approve sản phẩm từ Supplier
            'manage reviews',
            'create tickets', // Thêm dòng này
            'manage internal requests',
            'create internal requests' // Thêm quyền mới
        ];

        foreach (array_unique($permissions) as $permission) {
            Permission::updateOrCreate(['name' => $permission]);
        }

        // Tạo vai trò và gán quyền
        $adminRole = Role::updateOrCreate(['name' => 'admin']);
        $adminRole->syncPermissions($permissions); // Admin có tất cả quyền

        $employeeRole = Role::updateOrCreate(['name' => 'employee']);
        $employeeRole->syncPermissions(['manage inventory', 'manage orders', 'manage promotions', 'support customer', 'view reports','approve products','manage reviews','support customer']);

        $customerRole = Role::updateOrCreate(['name' => 'customer']);
        $customerRole->syncPermissions(['view products', 'place orders','create tickets']);

        $supplierRole = Role::updateOrCreate(['name' => 'supplier']);
        $supplierRole->syncPermissions(['update stock', 'create internal requests']);

        // Tạo user và gán role
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );
        $admin->syncRoles(['admin']);

        $employee = User::updateOrCreate(
            ['email' => 'employee@example.com'],
            [
                'name' => 'Employee User',
                'password' => bcrypt('password'),
            ]
        );
        $employee->syncRoles(['employee']);

        $customer = User::updateOrCreate(
            ['email' => 'customer@example.com'],
            [
                'name' => 'Customer User',
                'password' => bcrypt('password'),
            ]
        );
        $customer->syncRoles(['customer']);

        $supplier = User::updateOrCreate(
            ['email' => 'supplier@example.com'],
            [
                'name' => 'Supplier User',
                'password' => bcrypt('password'),
            ]
        );
        $supplier->syncRoles(['supplier']);
    }
}
