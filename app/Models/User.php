<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Enums\UserRole;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;

#[Fillable(['name', 'email', 'password', 'role', 'google_id', 'avatar'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, Notifiable;

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     */
    public function getJWTIdentifier(): mixed
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array<string, mixed>
     */
    public function getJWTCustomClaims(): array
    {
        return [
            'email' => $this->email,
            'nama' => $this->name,
            'role' => $this->role?->value,
            'permissions' => $this->permissions_array,
        ];
    }

    /**
     * Get all assigned permissions array.
     *
     * @return array<string>
     */
    public function getPermissionsArrayAttribute(): array
    {
        $directPermissions = $this->getDirectPermissions()->pluck('name')->toArray();
        if (! empty($directPermissions)) {
            return $directPermissions;
        }

        if ($this->hasRole(UserRole::ADMINISTRATOR->value) || $this->role === UserRole::ADMINISTRATOR) {
            return ['*'];
        }

        $permissions = $this->getAllPermissions()->pluck('name')->toArray();

        if (! empty($permissions)) {
            return $permissions;
        }

        return match ($this->role) {
            UserRole::KAPRODI => [
                'kurikulum.create',
                'kurikulum.read',
                'kurikulum.update',
                'kurikulum.delete',
                'cpl.create',
                'cpl.read',
                'cpl.update',
                'cpl.delete',
                'mata_kuliah.create',
                'mata_kuliah.read',
                'mata_kuliah.update',
                'mata_kuliah.delete',
                'mata_kuliah_cpl.create',
                'mata_kuliah_cpl.read',
                'mata_kuliah_cpl.update',
                'mata_kuliah_cpl.delete',
                'mata_kuliah_angkatan_kelas.create',
                'mata_kuliah_angkatan_kelas.read',
                'mata_kuliah_angkatan_kelas.update',
                'mata_kuliah_angkatan_kelas.delete',
                'enrollment_mata_kuliah.create',
                'enrollment_mata_kuliah.read',
                'enrollment_mata_kuliah.update',
                'enrollment_mata_kuliah.delete',
                'nilai_mahasiswa.create',
                'nilai_mahasiswa.read',
                'nilai_mahasiswa.update',
                'nilai_mahasiswa.delete',
                'dosen.read',
                'kriteria.read',
                'penelitian.read',
                'pengabdian.read',
                'kegiatan.read',
                'luaran.read',
                'kerjasama.read',
                'sertifikasi.read',
                'evidence.read',
                'evidence.validate',
                'assessment.read',
                'assessment.run',
                'dashboard.read',
                'led.read',
                'lkps.read',
            ],
            UserRole::DOSEN => [
                'nilai_mahasiswa.create',
                'nilai_mahasiswa.read',
                'nilai_mahasiswa.update',
                'nilai_mahasiswa.delete',
                'enrollment_mata_kuliah.update',
                'kurikulum.read',
                'cpl.read',
                'mata_kuliah.read',
                'mahasiswa.read',
                'dosen.read',
                'penelitian.create',
                'penelitian.read',
                'penelitian.update',
                'pengabdian.create',
                'pengabdian.read',
                'pengabdian.update',
                'kegiatan.create',
                'kegiatan.read',
                'kegiatan.update',
                'luaran.create',
                'luaran.read',
                'luaran.update',
                'sertifikasi.create',
                'sertifikasi.read',
                'sertifikasi.update',
                'evidence.create',
                'evidence.read',
                'evidence.update',
                'dashboard.read',
            ],
            UserRole::REVIEWER => [
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
                'dosen.read',
                'kriteria.read',
                'kegiatan.read',
                'luaran.read',
                'kerjasama.read',
                'evidence.read',
                'evidence.validate',
                'assessment.read',
                'assessment.run',
                'dashboard.read',
                'led.read',
                'lkps.read',
            ],
            UserRole::ADMINISTRATOR => ['*'],
            default => [],
        };
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => UserRole::class,
        ];
    }
}
