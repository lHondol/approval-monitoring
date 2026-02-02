<?php

namespace App\Http\Controllers;

use App\Http\Requests\Area\CreateRequest;
use App\Http\Requests\Area\EditRequest;
use App\Services\AreaService;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    private $areaService;
    public function __construct(AreaService $areaService) {
        $this->areaService = $areaService;
    }

    public function view() {
        return view('area.view');
    }

    public function getData() {
        return $this->areaService->getData();
    }

    public function getDetail(Request $request) {
        $id = $request->id;
        $data = $this->areaService->getDetail($id);
        return view('area.detail', compact('data'));
    }

    public function createForm() {
        $users = $this->areaService->getUsers();
        return view ('area.create', compact('users'));
    }

    public function create(CreateRequest $request) {
        $data = $request->all();
        $this->areaService->create((object) $data);
        return redirect()->route('areaView');
    }

    public function editForm(Request $request) {
        $id = $request->id;
        $data = $this->areaService->getDetail($id, true);
        $users = $this->areaService->getUsers();
        return view('area.edit', compact('data', 'users'));
    }

    public function edit(EditRequest $request) {
        $data = array_merge(
            $request->all(),
            $request->route()->parameters()
        );
        $this->areaService->edit((object) $data);
        return redirect()->route('areaView');
    }

    public function remove(Request $request) {
        $id = $request->id;
        $this->areaService->remove($id);
        return redirect()->route('areaView');
    }
}
