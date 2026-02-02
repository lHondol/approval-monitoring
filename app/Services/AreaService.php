<?php

namespace App\Services;

use App\Models\Area;
use App\Models\User;
use Ramsey\Uuid\Uuid;
use Yajra\DataTables\DataTables;

class AreaService
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
        return view('area.datatables.actions', compact('data'));
    }

    public function getData() {
        return DataTables::of(
            Area::with('users') // eager load users
                ->select(['id', 'name'])
        )
        ->addColumn('users', function($area) {
            $users = $area->users->pluck('name');

            $html = "<div class='flex flex-wrap gap-3'>";

            foreach ($users as $permission) {
                $html .= "<span class='ui teal label'>{$permission}</span>";
            }

            $html .= "</div>";

            return $html;
        })
        ->filterColumn('users', function($query, $keyword) {
            if ($keyword !== '') {
                $query->whereHas('users', function($q) use ($keyword) {
                    $q->where('name', 'LIKE', "%{$keyword}%");
                });
            }
        })
        ->addColumn('actions', function($row) {
            return $this->renderActionButtons($row);
        })
        ->rawColumns(['users', 'actions'])
        ->make(true);
    }

    public function getDetail($id, $userIdInString = false) {
        $area = Area::with('users')->find($id);

        if (!$area) {
            return null;
        }

        if ($userIdInString) {
            $users = implode(',', $area->users->pluck('id')->toArray());
        } else {
            $users = $area->users->pluck('name');
        }

        return (object) [
            'id' => $area->id,
            'name' => $area->name,
            'users' => $users,
        ];
    }

    public function getUsers() {
        return User::select(['id', 'name'])->get();   
    }

    public function create($data) {
        $area = new Area();
        
        $uuid =  Uuid::uuid4()->toString();
        $area->id =$uuid;
        $area->name = $data->name;
        $area->save();

        $area->users()->sync(explode(',', $data->users));

        return $area;
    }

    public function edit($data) {
        $area = Area::where('id', $data->id)->first();

        if (!$area) {
            return null;
        }

        $area->name = $data->name;
        $area->save();

        $area->users()->sync(explode(',', $data->users));

        return $area;
    }

    public function remove($id) {
        $area = Area::where('id', $id)->first();

        if (!$area) {
            return null;
        }

        $area->delete();

        return $area;
    }
}
