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

        $pimpinanPermissions = [
            // K1 Read-Only
            'perguruan_tinggi.read',
            'prodi.read',
            'kelas.read',
            'upps.read',
            'anggota_upps.read',
            'mata_kuliah.read',
            'tahun_akademik.read',
            'angkatan.read',
            'kurikulum.read',
            'cpl.read',
            'mata_kuliah_cpl.read',
            'mahasiswa.read',
            'status_mahasiswa.read',
            'kegiatan_mahasiswa.read',
            'nilai_mahasiswa.read',
            'prestasi_mahasiswa.read',
            'evidence_prestasi_mahasiswa.read',
            'jenis_prestasi.read',
            'angkatan_kelas.read',
            'enrollment_kelas.read',
            'enrollment_mata_kuliah.read',
            'mata_kuliah_angkatan_kelas.read',
            // K2 Pimpinan (Tanpa evidence.validate)
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
        ];

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
                'direct_permissions' => $pimpinanPermissions,
            ],
            [
                'name' => 'Pimpinan Eksekutif SSO',
                'email' => 'pimpinan@example.com',
                'role' => UserRole::REVIEWER,
                'direct_permissions' => $pimpinanPermissions,
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
