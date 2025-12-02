<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\DataTables;

class RoleService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    private function renderActionButtons($row)
    {
        $data = $row;
        return view('role.datatables.actions', compact('data'));
    }

    public function getData() {
        return DataTables::of(
            Role::with('permissions') // eager load permissions
                ->select(['id', 'name'])
                ->orderBy('created_at', 'desc')
        )
                ->addColumn('permissions', function($role) {
                    $permissions = $role->permissions->pluck('name');

                    $html = "<div class='flex flex-wrap gap-3'>";

                    foreach ($permissions as $permission) {
                        $html .= "<span class='ui green label'>{$permission}</span>";
                    }

                    $html .= "</div>";

                    return $html;
                })
        ->addColumn('actions', function($row) {
            return $this->renderActionButtons($row);
        })
        ->rawColumns(['permissions', 'actions'])
        ->make(true);
    }

    public function getDetail($id, $permissionIdInString = false) {
        $role = Role::with('permissions')->find($id);

        if (!$role) {
            return null;
        }

        if ($permissionIdInString) {
            $permissions = implode(',', $role->permissions->pluck('id')->toArray());
        } else {
            $permissions = $role->permissions->pluck('name');
        }

        return (object) [
            'id' => $role->id,
            'name' => $role->name,
            'permissions' => $permissions,
        ];
    }

    public function getPermissions() {
        return Permission::select(['id', 'name'])->get();   
    }

    public function create($data) {
        $role = new role();
        
        $uuid =  Uuid::uuid4()->toString();
        $role->id =$uuid;
        $role->name = $data->name;
        $role->save();

        $role->syncPermissions(explode(',', $data->permissions));

        return $role;
    }

    public function edit($data) {
        $role = Role::where('id', $data->id)->first();

        if (!$role) {
            return null;
        }

        $role->name = $data->name;
        $role->save();

        $role->syncPermissions(explode(',', $data->permissions));

        return $role;
    }

    public function remove($id) {
        $role = Role::where('id', $id)->first();

        if (!$role) {
            return null;
        }

        $role->delete();

        return $role;
    }
}
