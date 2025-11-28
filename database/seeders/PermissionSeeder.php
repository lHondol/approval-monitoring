<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'view_drawing_transaction']);
        Permission::create(['name' => 'upload_drawing_transaction']);
        Permission::create(['name' => 'revise_drawing_transaction']);
        Permission::create(['name' => 'first_approve_drawing_transaction']);
        Permission::create(['name' => 'second_approve_drawing_transaction']);
        Permission::create(['name' => 'reject_drawing_transaction']);
    }
}
