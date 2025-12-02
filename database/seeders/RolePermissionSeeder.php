<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPermission1 = Permission::create(['name' => 'view_drawing_transaction']);
        $defaultPermission2 = Permission::create(['name' => 'upload_drawing_transaction']);
        $defaultPermission3 = Permission::create(['name' => 'revise_drawing_transaction']);
        $defaultPermission4 = Permission::create(['name' => 'first_approve_drawing_transaction']);
        $defaultPermission5 = Permission::create(['name' => 'second_approve_drawing_transaction']);
        $defaultPermission6 = Permission::create(['name' => 'reject_drawing_transaction']);

        $defaultRole1 = Role::create(['name' => 'R&D Staff']);
        $defaultRole1->givePermissionTo([
            $defaultPermission1, $defaultPermission2, $defaultPermission3
        ]);

        $defaultRole2 = Role::create(['name' => 'R&D Spv']);
        $defaultRole2->givePermissionTo([
            $defaultPermission1, $defaultPermission4, $defaultPermission6
        ]);

        $defaultRole3 = Role::create(['name' => 'R&D Manager']);
        $defaultRole3->givePermissionTo([
            $defaultPermission1, $defaultPermission4, $defaultPermission6
        ]);

        $defaultRole4 = Role::create(['name' => 'Marketing']);
        $defaultRole4->givePermissionTo([
            $defaultPermission1, $defaultPermission5, $defaultPermission6
        ]);

        $defaultRole5 = Role::create(['name' => 'PPIC']);
        $defaultRole5->givePermissionTo([
            $defaultPermission1
        ]);

        $defaultRole6 = Role::create(['name' => 'Produksi']);
        $defaultRole6->givePermissionTo([
            $defaultPermission1
        ]);

    }
}
