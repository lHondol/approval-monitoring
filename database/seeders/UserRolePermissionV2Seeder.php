<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class UserRolePermissionV2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $viewDrawingTransactionPermission = Permission::where('name', 'view_drawing_transaction')->first();
        $rejectDrawingTransactionPermission = Permission::where('name', 'reject_drawing_transaction')->first();

        $bomApprovePermission = Permission::updateOrCreate(
            ['name' => 'bom_approve_distributed_drawing_transaction', 'guard_name' => 'web'],
            ['ui_name' => 'Can BOM Approve Distributed Drawing Transaction']
        );

        $costingApprovePermission = Permission::updateOrCreate(
            ['name' => 'costing_approve_distributed_drawing_transaction', 'guard_name' => 'web'],
            ['ui_name' => 'Can Costing Approve Distributed Drawing Transaction']
        );

        $bomRole = Role::firstOrCreate(
            ['name' => 'BOM', 'guard_name' => 'web']
        );

        $bomRole->syncPermissions([
            $viewDrawingTransactionPermission,
            $rejectDrawingTransactionPermission,
            $bomApprovePermission,
        ]);

        $costingRole = Role::firstOrCreate(
            ['name' => 'Costing', 'guard_name' => 'web']
        );

        $costingRole->syncPermissions([
            $viewDrawingTransactionPermission,
            $rejectDrawingTransactionPermission,
            $costingApprovePermission,
        ]);

        $bomUser = User::updateOrCreate(
            ['email' => 'bom@email.com'],
            [
                'name' => 'General Account (BOM)',
                'password' => bcrypt('password'),
            ]
        );

        $bomUser->syncRoles([$bomRole]);

        $costingUser = User::updateOrCreate(
            ['email' => 'costing@email.com'],
            [
                'name' => 'General Account (Costing)',
                'password' => bcrypt('password'),
            ]
        );

        $costingUser->syncRoles([$costingRole]);
    }
}
