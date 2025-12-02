<?php

namespace App\Services;

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
            'id',
            'name',
            'email',
        ])->orderBy('created_at', 'desc'))
        ->addColumn('actions', function($row) {
            return $this->renderActionButtons($row);
        })
        ->rawColumns(['actions'])
        ->make(true);
    }

    public function getDetail($id) {
        $user = User::where('id', $id)->first();
        return $user;
    }

}
