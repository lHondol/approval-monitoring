<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRolePermissionV4Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $canViewSample = Permission::updateOrCreate(
            ['name' => 'view_sample_transaction', 'guard_name' => 'web'],
            ['ui_name' => 'Can View Sample Transaction']
        );
        $canCreateSample = Permission::updateOrCreate(
            ['name' => 'create_sample_transaction', 'guard_name' => 'web'],
            ['ui_name' => 'Can Create Sample Transaction']
        );
        $canEditSample = Permission::updateOrCreate(
            ['name' => 'edit_sample_transaction', 'guard_name' => 'web'],
            ['ui_name' => 'Can Edit Sample Transaction']
        );
        $canDeleteSample = Permission::updateOrCreate(
            ['name' => 'delete_sample_transaction', 'guard_name' => 'web'],
            ['ui_name' => 'Can Delete Sample Transaction']
        );
        $canCreateSampleProcess = Permission::updateOrCreate(
            ['name' => 'create_sample_transaction_process', 'guard_name' => 'web'],
            ['ui_name' => 'Can Create Sample Transaction Process']
        );
        $canEditSampleProcess = Permission::updateOrCreate(
            ['name' => 'edit_sample_transaction_process', 'guard_name' => 'web'],
            ['ui_name' => 'Can Edit Sample Transaction Process']
        );
        $canDeleteSampleProcess = Permission::updateOrCreate(
            ['name' => 'delete_sample_transaction_process', 'guard_name' => 'web'],
            ['ui_name' => 'Can Delete Sample Transaction Process']
        );
        $canApproveSample = Permission::updateOrCreate(
            ['name' => 'approve_sample_transaction', 'guard_name' => 'web'],
            ['ui_name' => 'Can Approve Sample Transaction']
        );
    }
}
