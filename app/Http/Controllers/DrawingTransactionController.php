<?php

namespace App\Http\Controllers;

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

    public function getData() {
        return $this->drawingTransactionService->getData();
    }
}
