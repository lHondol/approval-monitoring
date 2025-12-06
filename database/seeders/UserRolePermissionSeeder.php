<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultPermission1 = Permission::create([
            'ui_name' => 'Can View Drawing Transaction', 
            'name' => 'view_drawing_transaction',
        ]);
        $defaultPermission2 = Permission::create([
            'ui_name' => 'Can Create Drawing Transaction', 
            'name' => 'create_drawing_transaction',
        ]);
        $defaultPermission3 = Permission::create([
            'ui_name' => 'Can Upload Drawing Transaction', 
            'name' => 'upload_drawing_transaction',
        ]);
        $defaultPermission4 = Permission::create([
            'ui_name' => 'Can Revise Drawing Transaction', 
            'name' => 'revise_drawing_transaction',
        ]);
        $defaultPermission5 = Permission::create([
            'ui_name' => 'Can First Approve Drawing Transaction', 
            'name' => 'first_approve_drawing_transaction',
        ]);
        $defaultPermission6 = Permission::create([
            'ui_name' => 'Can Second Approve Drawing Transaction', 
            'name' => 'second_approve_drawing_transaction',
        ]);
        $defaultPermission7 = Permission::create([
            'ui_name' => 'Can Reject Drawing Transaction', 
            'name' => 'reject_drawing_transaction',
        ]);
        $defaultPermission8 = Permission::create([
            'ui_name' => 'Can View User', 
            'name' => 'view_user',
        ]);
        $defaultPermission9 = Permission::create([
            'ui_name' => 'Can Edit User', 
            'name' => 'edit_user',
        ]);
        $defaultPermission10 = Permission::create([
            'ui_name' => 'Can Delete User', 
            'name' => 'delete_user',
        ]);
        $defaultPermission11 = Permission::create([
            'ui_name' => 'Can View Role', 
            'name' => 'view_role',
        ]);
        $defaultPermission12 = Permission::create([
            'ui_name' => 'Can Create Role', 
            'name' => 'create_role',
        ]);
        $defaultPermission13 = Permission::create([
            'ui_name' => 'Can Edit Role', 
            'name' => 'edit_role',
        ]);
        $defaultPermission14 = Permission::create([
            'ui_name' => 'Can Delete Role', 
            'name' => 'delete_role',
        ]);

        $defaultRole1 = Role::create(['name' => 'R&D Staff']);
        $defaultRole1->givePermissionTo([
            $defaultPermission1, $defaultPermission2, $defaultPermission3, $defaultPermission4
        ]);

        $defaultRole2 = Role::create(['name' => 'R&D Spv']);
        $defaultRole2->givePermissionTo([
            $defaultPermission1, $defaultPermission7, $defaultPermission5
        ]);

        $defaultRole3 = Role::create(['name' => 'R&D Manager']);
        $defaultRole3->givePermissionTo([
            $defaultPermission1, $defaultPermission7, $defaultPermission5
        ]);

        $defaultRole4 = Role::create(['name' => 'Marketing']);
        $defaultRole4->givePermissionTo([
            $defaultPermission1, $defaultPermission7, $defaultPermission6
        ]);

        $defaultRole5 = Role::create(['name' => 'PPIC']);
        $defaultRole5->givePermissionTo([
            $defaultPermission1
        ]);

        $defaultRole6 = Role::create(['name' => 'Produksi']);
        $defaultRole6->givePermissionTo([
            $defaultPermission1
        ]);

        $user1 = User::create([
            "name" => "General Account (R&D Staff)",
            "email" => "rndstaff@email.com",
            "password" => bcrypt('password')
        ]);
        $user1->assignRole($defaultRole1);

        $user2 = User::create([
            "name" => "General Account (R&D Spv)",
            "email" => "rndspv@email.com",
            "password" => bcrypt('password')
        ]);
        $user2->assignRole($defaultRole2);

        $user3 = User::create([
            "name" => "General Account (R&D Mgr)",
            "email" => "rndmgr@email.com",
            "password" => bcrypt('password')
        ]);
        $user3->assignRole($defaultRole3);

        $user4 = User::create([
            "name" => "General Account (Marketing)",
            "email" => "marketing@email.com",
            "password" => bcrypt('password')
        ]);
        $user4->assignRole($defaultRole4);

        $user5 = User::create([
            "name" => "General Account (PPIC)",
            "email" => "ppic@email.com",
            "password" => bcrypt('password')
        ]);
        $user5->assignRole($defaultRole5);

        $user6 = User::create([
            "name" => "General Account (Produksi)",
            "email" => "produksi@email.com",
            "password" => bcrypt('password')
        ]);
        $user6->assignRole($defaultRole6);

        $superAdmin = User::create([
            "name" => "Super Admin",
            "email" => "superadmin@email.com",
            "password" => bcrypt('superadminpassword')
        ]);
        $superAdmin->givePermissionTo([
            $defaultPermission1,
            $defaultPermission2,
            $defaultPermission3,
            $defaultPermission4,
            $defaultPermission5,
            $defaultPermission6,
            $defaultPermission7,
            $defaultPermission8,
            $defaultPermission9,
            $defaultPermission10,
            $defaultPermission11,
            $defaultPermission12,
            $defaultPermission13,
        ]);
    }
}
