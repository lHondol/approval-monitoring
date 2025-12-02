<?php

namespace App\Http\Controllers;

use App\Http\Requests\Role\CreateRequest;
use App\Http\Requests\Role\EditRequest;
use App\Services\RoleService;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    private $roleService;
    public function __construct(RoleService $roleService) {
        $this->roleService = $roleService;
    }

    public function view() {
        return view('role.view');
    }

    public function getData() {
        return $this->roleService->getData();
    }

    public function getDetail(Request $request) {
        $id = $request->id;
        $data = $this->roleService->getDetail($id);
        return view('role.detail', compact('data'));
    }

    public function createForm() {
        $permissions = $this->roleService->getPermissions();
        return view ('role.create', compact('permissions'));
    }

    public function create(CreateRequest $request) {
        $data = $request->all();
        $this->roleService->create((object) $data);
        return redirect()->route('roleView');
    }

    public function editForm() {

    }

    public function edit(EditRequest $request) {

    }

    public function remove() {

    }
}
