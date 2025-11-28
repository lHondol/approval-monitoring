<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DrawingTransactionController;
use Illuminate\Support\Facades\Route;

// DEFAULT
Route::get('/', function () {
    return redirect()->route('loginForm');
});

// AUTH
Route::controller(AuthController::class)
->group(function () {
    Route::middleware('guest')->get('/login', 'loginForm')->name('loginForm');
    Route::middleware('guest')->post('/login', 'login')->name('login');
    Route::middleware('auth')->post('/logout', 'logout')->name('logout');
});

// DASHBOARD
Route::middleware('auth')->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// DRAWING TRANSACTION
Route::middleware('auth')
->controller(DrawingTransactionController::class)
->group(function () {
    Route::get('/drawing-transaction', 'view')->name('drawingTransactionView');
    Route::get('/drawing-transaction/data', 'getData')->name('drawingTransactionData');
    Route::get('/drawing-transaction/create', 'createForm')->name('drawingTransactionCreateForm');
    Route::post('/drawing-transaction/post', 'create')->name('drawingTransactionCreate');
    Route::get('/drawing-transaction/detail/{id}', 'detailForm')->name('drawingTransactionDetailForm');
    Route::get('/drawing-transaction/detail/steps/{drawing_transaction_id}', 'getSteps')->name('drawingTransactionSteps');
    Route::post('/drawing-transaction/approval-1', 'approval1')->name('drawingTransactionApproval1Form');
    Route::post('/drawing-transaction/approval-2', 'approval2')->name('drawingTransactionApproval2Form');
});

