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
        ]);

        $this->call([
            PerguruanTinggiSeeder::class,
            ProdiSeeder::class,
            UppsSeeder::class,
            KurikulumSeeder::class,
            CplSeeder::class,
            MataKuliahSeeder::class,
            MahasiswaSeeder::class,
            MataKuliahCplSeeder::class,
            AnggotaUppsSeeder::class,
            AngkatanSeeder::class,
            KelasSeeder::class,
            TahunAkademikSeeder::class,
            KegiatanMahasiswaSeeder::class,
            AngkatanKelasSeeder::class,
            EnrollmentKelasSeeder::class,
            StatusMahasiswaSeeder::class,
            EnrollmentMataKuliahSeeder::class,
            MataKuliahAngkatanKelasSeeder::class,
            PrestasiMahasiswaSeeder::class,
            EvidencePrestasiMahasiswaSeeder::class,
            JenisPrestasiSeeder::class,
            NilaiMahasiswaSeeder::class,
        ]);
    }
}
