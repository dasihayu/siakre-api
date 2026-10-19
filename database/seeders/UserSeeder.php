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

        $users = [
            [
                'name' => 'Administrator Sistem',
                'email' => 'administrator@polines.ac.id',
                'role' => UserRole::ADMINISTRATOR,
            ],
            [
                'name' => 'Operator Akademik',
                'email' => 'operator@polines.ac.id',
                'role' => UserRole::OPERATOR,
            ],
            [
                'name' => 'Kuwat Santoso, S.T., M.Kom.',
                'email' => 'kaprodi@polines.ac.id',
                'role' => UserRole::KAPRODI,
            ],
            [
                'name' => 'Dr. Amin Suharjono, S.T., M.T.',
                'email' => 'dosen@polines.ac.id',
                'role' => UserRole::DOSEN,
            ],
            [
                'name' => 'Dr. Eni Dwi Wardihani, S.T., M.T.',
                'email' => 'dosen2@polines.ac.id',
                'role' => UserRole::DOSEN,
            ],
            [
                'name' => 'Slamet Handoko, S.T., M.T.',
                'email' => 'dosen3@polines.ac.id',
                'role' => UserRole::DOSEN,
            ],
            [
                'name' => 'Dr. Ir. Wahyudi, M.T. (Asesor Reviewer)',
                'email' => 'reviewer@polines.ac.id',
                'role' => UserRole::REVIEWER,
            ],
            [
                'name' => 'Direktur Politeknik Negeri Semarang',
                'email' => 'pimpinan@polines.ac.id',
                'role' => UserRole::PIMPINAN,
            ],
        ];

        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => $password,
                    'role' => $userData['role'],
                    'email_verified_at' => now(),
                ]
            );

            $user->syncRoles([$userData['role']->value]);
        }
    }
}
