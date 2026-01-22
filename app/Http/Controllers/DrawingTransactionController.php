<?php

namespace App\Http\Controllers;

use App\Http\Requests\DrawingTransaction\ApprovalRequest;
use App\Http\Requests\DrawingTransaction\CreateRequest;
use App\Http\Requests\DrawingTransaction\ReviseRequest;
use App\Models\User;
use App\Services\DrawingTransactionService;
use App\Services\EmailService;
use Illuminate\Http\Request;

class DrawingTransactionController extends Controller
{
    private $drawingTransactionService;
    private $emailService;
    public function __construct(DrawingTransactionService $drawingTransactionService, EmailService $emailService) {
        $this->drawingTransactionService = $drawingTransactionService;
        $this->emailService = $emailService;
    }

    public function view() {
        return view('drawing-transaction.view');
    }

    public function createForm() {
        $customers = $this->drawingTransactionService->getCustomers();
        return view('drawing-transaction.create', compact('customers'));
    }

    public function create(CreateRequest $request) {
        $data = $request->all();
        $data['files'] = $request->file('files');
        $drawingTransaction = $this->drawingTransactionService->create((object) $data);

        if (!$drawingTransaction)
            return redirect()->back()->withErrors(['files' => 'file(s) version not supported']);
        
        dispatch(function () use ($drawingTransaction) {
            $this->emailService->sendRequestApproval1DrawingTransaction($drawingTransaction->id, $drawingTransaction->so_number);
        })->afterResponse();

        return redirect()->route('drawingTransactionView');
    }

    public function getDetail(Request $request) {
        $id = $request->id;
        $data = $this->drawingTransactionService->getDetail($id);
        if (!$data)
            return redirect()->back();
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

    public function approvalForm(Request $request) {
        $id = $request->id;
        $data = $this->drawingTransactionService->getDetail($id);
        return view('drawing-transaction.approval', compact('data'));
    }

    public function approval(ApprovalRequest $request) {
        $data = array_merge(
            $request->all(),
            $request->route()->parameters()
        );

        if ($request->action == 'approve') {
            $this->drawingTransactionService->approve((object) $data);
        } else if ($request->action == 'reject') {
            $this->drawingTransactionService->reject((object) $data);
        }

        return redirect()->route('drawingTransactionView');
    }

    public function reviseForm(Request $request) {
        $customers = $this->drawingTransactionService->getCustomers();
        $id = $request->id;
        $data = $this->drawingTransactionService->getDetail($id);
        return view('drawing-transaction.revise', compact('data', 'customers'));
    }

    public function revise(ReviseRequest $request) {
        $data = array_merge(
            $request->all(),
            $request->route()->parameters()
        );

        $drawingTransaction = $this->drawingTransactionService->revise((object) $data);

        if (!$drawingTransaction)
            return redirect()->back()->withErrors(['files' => 'file(s) version not supported']);

        dispatch(function () use ($drawingTransaction) {
            $this->emailService->sendRequestApproval1DrawingTransaction($drawingTransaction->id);
        })->afterResponse();

        return redirect()->route('drawingTransactionView');
    }
}
