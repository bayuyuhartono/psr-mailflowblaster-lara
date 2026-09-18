<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class SuperUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'super@gmail.com'],
            [
                'name' => 'Super User',
                'password' => 'adminpass',
                'is_super_user' => true,
                'email_verified_at' => now(),
            ],
        );
    }
}
