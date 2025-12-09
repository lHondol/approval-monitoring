<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Yajra\DataTables\DataTables;

class UserService
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
        return view('user.datatables.actions', compact('data'));
    }

    public function getData() {
        return DataTables::of(User::select([
            'users.id',
            'users.name',
            'users.email',
        ])
        ->with('roles')
        ->whereNot('users.name', 'Super Admin'))
        ->orderColumn('role', function($query, $order) {
            $query->leftJoin('model_has_roles', function($join) {
                      $join->on('users.id', '=', 'model_has_roles.model_id')
                           ->where('model_has_roles.model_type', User::class);
                  })
                  ->leftJoin('roles', 'roles.id', '=', 'model_has_roles.role_id')
                  ->orderBy('roles.name', $order)
                  ->groupBy('users.id', 'roles.name');
        })
        ->addColumn('role', function($row) {
            return $row->roles[0]?->name ?? '';
        })
        ->addColumn('actions', function($row) {
            return $this->renderActionButtons($row);
        })
        ->rawColumns(['role', 'actions'])
        ->make(true);
    }

    public function getDetail($id, $roleIdInString = false) {
        $user = User::where('id', $id)->first();

        if (!$user) {
            return null;
        }

        if ($roleIdInString) {
            $roles = implode(',', $user->roles->pluck('id')->toArray());
        } else {
            $roles = $user->roles->pluck('name');
        }

        return (object) [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => is_array($roles) ? $roles[0] : $roles ?? null,
        ];
    }

    public function getRoles() {
        return Role::select(['id', 'name'])->get();   
    }

    public function edit($data) {
        $user = User::where('id', $data->id)->first();

        if (!$user) {
            return null;
        }

        $user->name = $data->name;
        $user->email = $data->email;
        $user->save();
        
        $user->syncRoles($data->role);

        return $user;
    }

    public function remove($id) {
        $user = User::where('id', $id)->first();

        if (!$user) {
            return null;
        }

        $user->delete();

        return $user;
    }
}
