<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            UserSeeder::class,
            PerguruanTinggiSeeder::class,
            ProdiSeeder::class,
            UppsSeeder::class,
            AnggotaUppsSeeder::class,
            KurikulumSeeder::class,
            CplSeeder::class,
            MataKuliahSeeder::class,
            MataKuliahCplSeeder::class,
            AngkatanSeeder::class,
            KelasSeeder::class,
            TahunAkademikSeeder::class,
            AngkatanKelasSeeder::class,
            MahasiswaSeeder::class,
            EnrollmentKelasSeeder::class,
            StatusMahasiswaSeeder::class,
            KegiatanMahasiswaSeeder::class,
            PrestasiMahasiswaSeeder::class,
            EvidencePrestasiMahasiswaSeeder::class,
            JenisPrestasiSeeder::class,
            EnrollmentMataKuliahSeeder::class,
            MataKuliahAngkatanKelasSeeder::class,
            NilaiMahasiswaSeeder::class,
        ]);
    }
}
