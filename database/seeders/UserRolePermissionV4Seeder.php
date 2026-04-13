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
        $canCreateSampleProcess = Permission::updateOrCreate(
            ['name' => 'create_sample_transaction_process', 'guard_name' => 'web'],
            ['ui_name' => 'Can Create Sample Transaction Process']
        );
    }
}
