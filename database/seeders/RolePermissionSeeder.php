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
        // Tạo quyền (permissions)
        $permissions = [
            // Admin permissions
            'manage inventory', 'manage orders', 'manage users', 'view reports', 'manage settings',
            // Employee permissions
            'manage inventory', 'manage orders', 'manage promotions', 'support customer', 'view reports',
            // Customer permissions
            'view products', 'place orders',
            // Supplier permissions
            'update stock',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Tạo vai trò và gán quyền
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo($permissions); // Admin có tất cả quyền

        $employeeRole = Role::create(['name' => 'employee']);
        $employeeRole->givePermissionTo(['manage inventory', 'manage orders', 'manage promotions', 'support customer', 'view reports']);

        $customerRole = Role::create(['name' => 'customer']);
        $customerRole->givePermissionTo(['view products', 'place orders']);

        $supplierRole = Role::create(['name' => 'supplier']);
        $supplierRole->givePermissionTo(['update stock']);

        // Tạo user và gán role
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
        ])->assignRole('admin');

        User::create([
            'name' => 'Employee User',
            'email' => 'employee@example.com',
            'password' => bcrypt('password'),
        ])->assignRole('employee');

        User::create([
            'name' => 'Customer User',
            'email' => 'customer@example.com',
            'password' => bcrypt('password'),
        ])->assignRole('customer');

        User::create([
            'name' => 'Supplier User',
            'email' => 'supplier@example.com',
            'password' => bcrypt('password'),
        ])->assignRole('supplier');
    }
}
