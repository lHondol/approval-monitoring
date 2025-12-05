<?php

namespace App\Http\Controllers;

use App\Http\Requests\User\EditRequest;
use App\Services\UserService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    private $userService;
    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    public function view() {
        return view('user.view');
    }

    public function getData() {
        return $this->userService->getData();
    }

    public function getDetail(Request $request) {
        $id = $request->id;
        $data = $this->userService->getDetail($id);
        return view('user.detail', compact('data'));
    }

    public function editForm(Request $request) {
        $id = $request->id;
        $data = $this->userService->getDetail($id, true);
        $roles = $this->userService->getRoles();
        return view('user.edit', compact('data', 'roles'));
    }

    public function edit(EditRequest $request) {
        $data = array_merge(
            $request->all(),
            $request->route()->parameters()
        );
        $this->userService->edit((object) $data);
        return redirect()->route('userView');
    }

    public function remove(Request $request) {
        $id = $request->id;
        $this->userService->remove($id);
        return redirect()->route(route: 'userView');
    }
}
