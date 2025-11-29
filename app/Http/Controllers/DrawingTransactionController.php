<?php

namespace App\Http\Controllers;

use App\Http\Requests\DrawingTransaction\ApprovalRequest;
use App\Http\Requests\DrawingTransaction\CreateRequest;
use App\Services\DrawingTransactionService;
use Illuminate\Http\Request;

class DrawingTransactionController extends Controller
{
    private $drawingTransactionService;
    public function __construct(DrawingTransactionService $drawingTransactionService) {
        $this->drawingTransactionService = $drawingTransactionService;
    }

    public function view() {
        return view('drawing-transaction.view');
    }

    public function createForm() {
        return view('drawing-transaction.create');
    }

    public function create(CreateRequest $request) {
        $data = $request->all();
        $data['files'] = $request->file('files');
        $this->drawingTransactionService->create((object) $data);
        return redirect()->route('drawingTransactionView');
    }

    public function detailForm(Request $request) {
        $id = $request->id;
        $data = $this->drawingTransactionService->getDetail($id);
        return view('drawing-transaction.detail', compact('data'));
    }

    public function getData() {
        return $this->drawingTransactionService->getData();
    }

    public function getSteps(Request $request) {
        $drawingTransactionId = $request->drawing_transaction_id;
        $data = $this->drawingTransactionService->getSteps($drawingTransactionId);
        return view('drawing-transaction.tabs.step-tab', compact('data'));
    }

    public function approval(ApprovalRequest $request) {
        $data = array_merge(
            $request->all(),
            $request->route()->parameters()
        );
        $this->drawingTransactionService->approval((object) $data);
        return redirect()->route('drawingTransactionView');
    }
}
