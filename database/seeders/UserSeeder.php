<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $password = Hash::make('password');

        foreach (UserRole::cases() as $role) {
            $slug = strtolower($role->value);

            User::firstOrCreate(
                ['email' => "{$slug}@example.com"],
                [
                    'name' => ucfirst($slug).' User',
                    'password' => $password,
                    'role' => $role,
                    'email_verified_at' => now(),
                ]
            );
        }
    }
}
