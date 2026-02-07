<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdminEmail = env('ADMIN_SEED_EMAIL');
        $superAdminPassword = env('ADMIN_SEED_PASSWORD');
        $superAdminName = env('ADMIN_SEED_NAME', 'Super Admin');

        if ($superAdminEmail && $superAdminPassword) {
            $superAdmin = User::updateOrCreate(
                ['email' => $superAdminEmail],
                [
                    'name' => $superAdminName,
                    'password' => $superAdminPassword,
                    'email_verified_at' => now(),
                    'trust_score' => 100,
                ]
            );

            $superAdmin->assignRole('super-admin');
        }

        // Crear Admin de prueba (solo local)
        if (app()->environment('local', 'development')) {
            $demoEmail = env('ADMIN_DEMO_EMAIL');
            $demoPassword = env('ADMIN_DEMO_PASSWORD');
            $demoName = env('ADMIN_DEMO_NAME', 'Admin Demo');

            if ($demoEmail && $demoPassword) {
                $admin = User::firstOrCreate(
                    ['email' => $demoEmail],
                    [
                        'name' => $demoName,
                        'password' => $demoPassword,
                        'email_verified_at' => now(),
                        'trust_score' => 80,
                    ]
                );

                $admin->assignRole('admin');
            }
        }
    }
}
