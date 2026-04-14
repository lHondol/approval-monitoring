<?php

namespace App\Http\Controllers;

use App\Http\Requests\SampleTransaction\CreateProcessRequest;
use App\Http\Requests\SampleTransaction\CreateRequest;
use App\Http\Requests\SampleTransaction\EditRequest;
use App\Services\SampleTransactionService;
use Illuminate\Http\Request;

class SampleTransactionController extends Controller
{
    private $sampleTransactionService;
    public function __construct(SampleTransactionService $sampleTransactionService) {
        $this->sampleTransactionService = $sampleTransactionService;
    }

    public function view() {
        return view('sample-transaction.view');
    }

    public function getData() {
        return $this->sampleTransactionService->getData();
    }

    public function getDetail(Request $request) {
        $id = $request->id;
        $data = $this->sampleTransactionService->getDetail($id);
        return view('sample-transaction.detail', compact('data'));
    }

    public function createForm() {
        $customers = $this->sampleTransactionService->getCustomers();
        return view ('sample-transaction.create', compact('customers'));
    }

    public function create(CreateRequest $request) {
        $data = $request->all();
        $this->sampleTransactionService->create((object) $data);
        return redirect()->route('sampleTransactionView');
    }

    public function editForm(Request $request) {
        $customers = $this->sampleTransactionService->getCustomers();
        $id = $request->id;
        $data = $this->sampleTransactionService->getDetail($id, true);
        return view('sample-transaction.edit', compact('data', 'customers'));
    }

    public function edit(EditRequest $request) {
        $data = array_merge(
            $request->all(),
            $request->route()->parameters()
        );
        $this->sampleTransactionService->edit((object) $data);
        return redirect()->route('sampleTransactionView');
    }

    public function remove(Request $request) {
        $id = $request->id;
        $this->sampleTransactionService->remove($id);
        return redirect()->route('sampleTransactionView');
    }

    public function createProcessForm(Request $request) {
        $processes = $this->sampleTransactionService->getProcesses();
        $sampleTransaction = $this->sampleTransactionService->getSimpleTransaction($request->sampleTransactionId);
        return view ('sample-transaction.create-process', compact('processes', 'sampleTransaction'));
    }

    public function createProcess(CreateProcessRequest $request) {
        $data = array_merge(
            $request->all(),
            $request->route()->parameters()
        );
        $data['files'] = $request->file('files');
        $this->sampleTransactionService->createProcess((object) $data);
        return redirect()->route('sampleTransactionView');
    }

    public function editProcessForm(Request $request) {
        $customers = $this->sampleTransactionService->getCustomers();
        $id = $request->id;
        $data = $this->sampleTransactionService->getDetail($id, true);
        return view('sample-transaction.edit', compact('data', 'customers'));
    }

    public function editProcess(EditRequest $request) {
        $data = array_merge(
            $request->all(),
            $request->route()->parameters()
        );
        $this->sampleTransactionService->editProcess((object) $data);
        return redirect()->route('sampleTransactionView');
    }

    public function removeProcess(Request $request) {
        $id = $request->id;
        $this->sampleTransactionService->removeProcess($id);
        return redirect()->route('sampleTransactionView');
    }
}
