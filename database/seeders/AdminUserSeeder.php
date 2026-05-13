<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('DD_ADMIN_EMAIL', 'admin@dream-digital.info');
        $password = env('DD_ADMIN_PASSWORD');

        if (empty($password)) {
            $this->command?->warn('DD_ADMIN_PASSWORD non defini dans .env -- aucun admin seede.');
            $this->command?->warn('Definir DD_ADMIN_PASSWORD puis relancer : php artisan db:seed --class=AdminUserSeeder');
            return;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => env('DD_ADMIN_NAME', 'Admin Dream Digital'),
                'password' => Hash::make($password),
                'role' => User::ROLE_OWNER,
                'is_active' => true,
                'email_verified_at' => now(),
            ],
        );

        $this->command?->info("Admin seede : {$email}");
    }
}
