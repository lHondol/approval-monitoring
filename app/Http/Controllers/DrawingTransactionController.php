<?php

namespace App\Http\Controllers;

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

    public function getData() {
        return $this->drawingTransactionService->getData();
    }
}
