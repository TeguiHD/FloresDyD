<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // =========================================
        // PERMISSIONS
        // =========================================
        
        // Dashboard
        Permission::firstOrCreate(['name' => 'view dashboard']);
        
        // Products
        Permission::firstOrCreate(['name' => 'view products']);
        Permission::firstOrCreate(['name' => 'create products']);
        Permission::firstOrCreate(['name' => 'edit products']);
        Permission::firstOrCreate(['name' => 'delete products']);
        
        // Categories
        Permission::firstOrCreate(['name' => 'view categories']);
        Permission::firstOrCreate(['name' => 'create categories']);
        Permission::firstOrCreate(['name' => 'edit categories']);
        Permission::firstOrCreate(['name' => 'delete categories']);
        
        // Orders
        Permission::firstOrCreate(['name' => 'view orders']);
        Permission::firstOrCreate(['name' => 'process orders']);
        Permission::firstOrCreate(['name' => 'cancel orders']);
        Permission::firstOrCreate(['name' => 'delete orders']);
        Permission::firstOrCreate(['name' => 'export orders']);
        
        // Customers
        Permission::firstOrCreate(['name' => 'view customers']);
        Permission::firstOrCreate(['name' => 'edit customers']);
        Permission::firstOrCreate(['name' => 'delete customers']);
        
        // Reviews
        Permission::firstOrCreate(['name' => 'view reviews']);
        Permission::firstOrCreate(['name' => 'approve reviews']);
        Permission::firstOrCreate(['name' => 'delete reviews']);
        
        // Coupons
        Permission::firstOrCreate(['name' => 'view coupons']);
        Permission::firstOrCreate(['name' => 'create coupons']);
        Permission::firstOrCreate(['name' => 'edit coupons']);
        Permission::firstOrCreate(['name' => 'delete coupons']);
        
        // Marketing
        Permission::firstOrCreate(['name' => 'manage popups']);
        Permission::firstOrCreate(['name' => 'manage banners']);
        Permission::firstOrCreate(['name' => 'manage testimonials']);
        
        // Settings
        Permission::firstOrCreate(['name' => 'view settings']);
        Permission::firstOrCreate(['name' => 'edit settings']);
        
        // Users
        Permission::firstOrCreate(['name' => 'view users']);
        Permission::firstOrCreate(['name' => 'create users']);
        Permission::firstOrCreate(['name' => 'edit users']);
        Permission::firstOrCreate(['name' => 'delete users']);
        
        // Roles
        Permission::firstOrCreate(['name' => 'view roles']);
        Permission::firstOrCreate(['name' => 'manage roles']);
        
        // Audit logs
        Permission::firstOrCreate(['name' => 'view audit logs']);
        
        // Reports
        Permission::firstOrCreate(['name' => 'view reports']);
        Permission::firstOrCreate(['name' => 'export reports']);

        // =========================================
        // ROLES
        // =========================================

        // Super Admin - Todo acceso
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        // Super admin tiene todos los permisos implícitamente en Gate::before

        // Admin - Casi todo excepto configuración crítica
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions([
            'view dashboard',
            'view products', 'create products', 'edit products', 'delete products',
            'view categories', 'create categories', 'edit categories', 'delete categories',
            'view orders', 'process orders', 'cancel orders', 'export orders',
            'view customers', 'edit customers',
            'view reviews', 'approve reviews', 'delete reviews',
            'view coupons', 'create coupons', 'edit coupons', 'delete coupons',
            'manage popups', 'manage banners', 'manage testimonials',
            'view settings',
            'view users',
            'view reports', 'export reports',
        ]);

        // Editor - Gestión de contenido
        $editor = Role::firstOrCreate(['name' => 'editor']);
        $editor->syncPermissions([
            'view dashboard',
            'view products', 'create products', 'edit products',
            'view categories', 'create categories', 'edit categories',
            'view reviews', 'approve reviews',
            'manage popups', 'manage banners', 'manage testimonials',
        ]);

        // Sales - Solo pedidos
        $sales = Role::firstOrCreate(['name' => 'sales']);
        $sales->syncPermissions([
            'view dashboard',
            'view products',
            'view orders', 'process orders',
            'view customers',
            'view reports',
        ]);

        // Viewer - Solo lectura
        $viewer = Role::firstOrCreate(['name' => 'viewer']);
        $viewer->syncPermissions([
            'view dashboard',
            'view products',
            'view orders',
            'view customers',
            'view reports',
        ]);
    }
}
