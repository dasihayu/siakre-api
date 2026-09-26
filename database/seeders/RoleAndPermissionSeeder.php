<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // Daftar Fitur dalam Sistem Akreditasi LAM INFOKOM
        $features = [
            'dosen',
            'kriteria',
            'penelitian',
            'pengabdian',
            'led',
            'lkps',
            'user',
        ];

        // Actions CRUD
        $actions = ['create', 'read', 'update', 'delete'];

        // Create Permissions per Feature (Format: {feature}.{action}, misal: dosen.create)
        $allPermissions = [];
        foreach ($features as $feature) {
            foreach ($actions as $action) {
                $permissionName = "{$feature}.{$action}";
                Permission::findOrCreate($permissionName, 'api');
                $allPermissions[] = $permissionName;
            }
        }

        // Create Roles and Assign Permissions
        // 1. ADMINISTRATOR (Full Permissions)
        $adminRole = Role::findOrCreate(UserRole::ADMINISTRATOR->value, 'api');
        $adminRole->givePermissionTo(Permission::all());

        // 2. KAPRODI (Program Director - Full CRUD on Dosen, LED, LKPS, Penelitian, Pengabdian)
        $kaprodiRole = Role::findOrCreate(UserRole::KAPRODI->value, 'api');
        $kaprodiRole->givePermissionTo([
            'dosen.create', 'dosen.read', 'dosen.update', 'dosen.delete',
            'kriteria.read',
            'penelitian.create', 'penelitian.read', 'penelitian.update', 'penelitian.delete',
            'pengabdian.create', 'pengabdian.read', 'pengabdian.update', 'pengabdian.delete',
            'led.create', 'led.read', 'led.update', 'led.delete',
            'lkps.create', 'lkps.read', 'lkps.update', 'lkps.delete',
        ]);

        // 3. DOSEN (Lecturer - Read Dosen, CRUD own Penelitian & Pengabdian)
        $dosenRole = Role::findOrCreate(UserRole::DOSEN->value, 'api');
        $dosenRole->givePermissionTo([
            'dosen.read',
            'kriteria.read',
            'penelitian.create', 'penelitian.read', 'penelitian.update',
            'pengabdian.create', 'pengabdian.read', 'pengabdian.update',
        ]);

        // 4. OPERATOR (Data Entry)
        $operatorRole = Role::findOrCreate(UserRole::OPERATOR->value, 'api');
        $operatorRole->givePermissionTo([
            'dosen.create', 'dosen.read', 'dosen.update',
            'kriteria.read',
            'lkps.create', 'lkps.read', 'lkps.update',
        ]);

        // 5. REVIEWER (Assessor / Auditor - Read Only across LED, LKPS, Kriteria)
        $reviewerRole = Role::findOrCreate(UserRole::REVIEWER->value, 'api');
        $reviewerRole->givePermissionTo([
            'dosen.read',
            'kriteria.read',
            'penelitian.read',
            'pengabdian.read',
            'led.read',
            'lkps.read',
        ]);

        // 6. PIMPINAN (Executive / Dean - Read Only Dashboard & Reports)
        $pimpinanRole = Role::findOrCreate(UserRole::PIMPINAN->value, 'api');
        $pimpinanRole->givePermissionTo([
            'dosen.read',
            'kriteria.read',
            'led.read',
            'lkps.read',
        ]);
    }
}
