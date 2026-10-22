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

        // Akun Pengguna Sesuai Spesifikasi Integrasi SSO K1 ➔ K2
        $users = [
            // 1. Super Admin
            [
                'name' => 'Administrator Sistem',
                'email' => 'administrator@polines.ac.id',
                'role' => UserRole::ADMINISTRATOR,
                'direct_permissions' => null,
            ],
            [
                'name' => 'Super Administrator SSO',
                'email' => 'administrator@example.com',
                'role' => UserRole::ADMINISTRATOR,
                'direct_permissions' => null,
            ],

            // 2. Operator Master Data (Role: ADMINISTRATOR dengan pembatasan direct permissions K2)
            [
                'name' => 'Operator Akademik',
                'email' => 'operator@polines.ac.id',
                'role' => UserRole::ADMINISTRATOR,
                'direct_permissions' => [
                    'dosen.*',
                    'tendik.*',
                    'mitra.*',
                    'atribut.*',
                    'kegiatan.*',
                    'luaran.*',
                    'sertifikasi.*',
                    'kerjasama.*',
                    'evidence.*',
                    'dashboard.read',
                ],
            ],
            [
                'name' => 'Operator Master Data SSO',
                'email' => 'operator@example.com',
                'role' => UserRole::ADMINISTRATOR,
                'direct_permissions' => [
                    'dosen.*',
                    'tendik.*',
                    'mitra.*',
                    'atribut.*',
                    'kegiatan.*',
                    'luaran.*',
                    'sertifikasi.*',
                    'kerjasama.*',
                    'evidence.*',
                    'dashboard.read',
                ],
            ],

            // 3. Ketua Program Studi
            [
                'name' => 'Kuwat Santoso, S.T., M.Kom.',
                'email' => 'kaprodi@polines.ac.id',
                'role' => UserRole::KAPRODI,
                'direct_permissions' => null,
            ],
            [
                'name' => 'Kaprodi SSO',
                'email' => 'kaprodi@example.com',
                'role' => UserRole::KAPRODI,
                'direct_permissions' => null,
            ],

            // 4. Dosen DTPS
            [
                'name' => 'Dr. Amin Suharjono, S.T., M.T.',
                'email' => 'dosen@polines.ac.id',
                'role' => UserRole::DOSEN,
                'direct_permissions' => null,
            ],
            [
                'name' => 'Dr. Eni Dwi Wardihani, S.T., M.T.',
                'email' => 'dosen2@polines.ac.id',
                'role' => UserRole::DOSEN,
                'direct_permissions' => null,
            ],
            [
                'name' => 'Slamet Handoko, S.T., M.T.',
                'email' => 'dosen3@polines.ac.id',
                'role' => UserRole::DOSEN,
                'direct_permissions' => null,
            ],
            [
                'name' => 'Dr. Dosen Contoh, M.Kom',
                'email' => 'dosen@example.com',
                'role' => UserRole::DOSEN,
                'direct_permissions' => null,
            ],

            // 5. Asesor / Reviewer
            [
                'name' => 'Dr. Ir. Wahyudi, M.T. (Asesor Reviewer)',
                'email' => 'reviewer@polines.ac.id',
                'role' => UserRole::REVIEWER,
                'direct_permissions' => null,
            ],
            [
                'name' => 'Asesor Reviewer SSO',
                'email' => 'reviewer@example.com',
                'role' => UserRole::REVIEWER,
                'direct_permissions' => null,
            ],

            // 6. Pimpinan Eksekutif (Role di K1: REVIEWER, diakui ASESOR di K2)
            [
                'name' => 'Direktur Politeknik Negeri Semarang',
                'email' => 'pimpinan@polines.ac.id',
                'role' => UserRole::REVIEWER,
                'direct_permissions' => [
                    'dosen.read',
                    'kriteria.read',
                    'kegiatan.read',
                    'luaran.read',
                    'kerjasama.read',
                    'evidence.read',
                    'assessment.read',
                    'assessment.run',
                    'dashboard.read',
                    'led.read',
                    'lkps.read',
                ],
            ],
            [
                'name' => 'Pimpinan Eksekutif SSO',
                'email' => 'pimpinan@example.com',
                'role' => UserRole::REVIEWER,
                'direct_permissions' => [
                    'dosen.read',
                    'kriteria.read',
                    'kegiatan.read',
                    'luaran.read',
                    'kerjasama.read',
                    'evidence.read',
                    'assessment.read',
                    'assessment.run',
                    'dashboard.read',
                    'led.read',
                    'lkps.read',
                ],
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

            if ($user->role !== $userData['role']) {
                $user->update(['role' => $userData['role']]);
            }

            $user->syncRoles([$userData['role']->value]);

            if (! empty($userData['direct_permissions'])) {
                $user->syncPermissions($userData['direct_permissions']);
            } else {
                $user->syncPermissions([]);
            }
        }
    }
}
