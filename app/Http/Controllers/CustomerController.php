<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\CreateRequest;
use App\Http\Requests\Customer\EditRequest;
use App\Services\CustomerService;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    private $customerService;
    public function __construct(CustomerService $customerService) {
        $this->customerService = $customerService;
    }

    public function view() {
        return view('customer.view');
    }

    public function getData() {
        return $this->customerService->getData();
    }

    public function getDetail(Request $request) {
        $id = $request->id;
        $data = $this->customerService->getDetail($id);
        return view('customer.detail', compact('data'));
    }

    public function createForm() {
        return view ('customer.create');
    }

    public function create(CreateRequest $request) {
        $data = $request->all();
        $this->customerService->create((object) $data);
        return redirect()->route('customerView');
    }

    public function editForm(Request $request) {
        $id = $request->id;
        $data = $this->customerService->getDetail($id, true);
        return view('customer.edit', compact('data'));
    }

    public function edit(EditRequest $request) {
        $data = array_merge(
            $request->all(),
            $request->route()->parameters()
        );
        $this->customerService->edit((object) $data);
        return redirect()->route('customerView');
    }

    public function remove(Request $request) {
        $id = $request->id;
        $this->customerService->remove($id);
        return redirect()->route('customerView');
    }
}
