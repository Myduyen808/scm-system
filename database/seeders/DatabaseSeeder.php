<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Chạy seeder vai trò và quyền trước
        $this->call(RolePermissionSeeder::class);

        // Tạo user admin
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('admin');

        // Tạo user nhân viên
        $employee = User::create([
            'name' => 'Employee User',
            'email' => 'employee@example.com',
            'password' => Hash::make('password'),
        ]);
        $employee->assignRole('employee');

        // Tạo user khách hàng
        $customer = User::create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => Hash::make('password'),
        ]);
        $customer->assignRole('customer');

        // Tạo user nhà cung cấp
        $supplier = User::create([
            'name' => 'Supplier User',
            'email' => 'supplier@example.com',
            'password' => Hash::make('password'),
        ]);
        $supplier->assignRole('supplier');
            // Thêm seeder mới
        $this->call(ProductSeeder::class);

    }
}
