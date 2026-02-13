<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRolePermissionV3Seeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $canViewPrereleaseSo = Permission::updateOrCreate(
            ['name' => 'view_prerelease_so_transaction', 'guard_name' => 'web'],
            ['ui_name' => 'Can View Prerelease SO Transaction']
        );
        $canCreatePrereleaseSo = Permission::updateOrCreate(
            ['name' => 'create_prerelease_so_transaction', 'guard_name' => 'web'],
            ['ui_name' => 'Can Create Prerelease SO Transaction']
        );
        $canUploadPrereleaseSo = Permission::updateOrCreate(
            ['name' => 'upload_prerelease_so_transaction', 'guard_name' => 'web'],
            ['ui_name' => 'Can Upload Prerelease SO Transaction']
        );
        $canRevisePrereleaseSo = Permission::updateOrCreate(
            ['name' => 'revise_prerelease_so_transaction', 'guard_name' => 'web'],
            ['ui_name' => 'Can Revise Prerelease SO Transaction']
        );
        $canSalesAreaApprovePrereleaseSo = Permission::updateOrCreate(
            ['name' => 'sales_area_approve_prerelease_so_transaction', 'guard_name' => 'web'],
            ['ui_name' => 'Can Sales Area Approve Prerelease SO Transaction']
        );
        $canRndDrawingApprovePrereleaseSo = Permission::updateOrCreate(
            ['name' => 'rnd_drawing_approve_prerelease_so_transaction', 'guard_name' => 'web'],
            ['ui_name' => 'Can RnD Drawing Approve Prerelease SO Transaction']
        );
        $canRndBomApprovePrereleaseSo = Permission::updateOrCreate(
            ['name' => 'rnd_bom_approve_prerelease_so_transaction', 'guard_name' => 'web'],
            ['ui_name' => 'Can RnD BOM Approve Prerelease SO Transaction']
        );
        $canAccountingApprovePrereleaseSo = Permission::updateOrCreate(
            ['name' => 'accounting_approve_prerelease_so_transaction', 'guard_name' => 'web'],
            ['ui_name' => 'Can Accounting Approve Prerelease SO Transaction']
        );
        $canITApprovePrereleaseSo = Permission::updateOrCreate(
            ['name' => 'it_approve_prerelease_so_transaction', 'guard_name' => 'web'],
            ['ui_name' => 'Can IT Approve Prerelease SO Transaction']
        );
        $canAccountingRequestConfirmMarginPrereleaseSo = Permission::updateOrCreate(
            ['name' => 'accounting_request_confirm_margin_prerelease_so_transaction', 'guard_name' => 'web'],
            ['ui_name' => 'Can Accounting Request Confirm Margin Prerelease SO Transaction']
        );
        $canMktManagerConfirmMarginPrereleaseSo = Permission::updateOrCreate(
            ['name' => 'mkt_manager_confirm_margin_prerelease_so_transaction', 'guard_name' => 'web'],
            ['ui_name' => 'Can Mkt Manager Confirm Margin Prerelease SO Transaction']
        );

        $canMktStaffReleasePrereleaseSo =  Permission::where('name', 'mkt_staff_finalize_prerelease_so_transaction')
        ->where('guard_name', 'web')
        ->first();

        if ($canMktStaffReleasePrereleaseSo) {
            $canMktStaffReleasePrereleaseSo = tap(
                $canMktStaffReleasePrereleaseSo->update([
                    'name' => 'mkt_staff_release_prerelease_so_transaction',
                    'ui_name' => 'Can MKT Staff Release Prerelease SO Transaction',
                ])
            );
        } else {
            $canMktStaffReleasePrereleaseSo = Permission::firstOrCreate(
                [
                    'name' => 'mkt_staff_release_prerelease_so_transaction',
                    'guard_name' => 'web'
                ],
                [
                    'ui_name' => 'Can MKT Staff Release Prerelease SO Transaction'
                ]
            );
        }

        $canRejectPrereleaseSo = Permission::updateOrCreate(
            ['name' => 'reject_prerelease_so_transaction', 'guard_name' => 'web'],
            ['ui_name' => 'Can Reject Prerelease SO Transaction']
        );
        $canViewArea = Permission::updateOrCreate(
            ['name' => 'view_area', 'guard_name' => 'web'],
            ['ui_name' => 'Can View Area']
        );
        $canCreateArea = Permission::updateOrCreate(
            ['name' => 'create_area', 'guard_name' => 'web'],
            ['ui_name' => 'Can Create Area']
        );
        $canEditArea = Permission::updateOrCreate(
            ['name' => 'edit_area', 'guard_name' => 'web'],
            ['ui_name' => 'Can Edit Area']
        );
        $canDeleteArea = Permission::updateOrCreate(
            ['name' => 'delete_area', 'guard_name' => 'web'],
            ['ui_name' => 'Can Delete Area']
        );
    }
}
