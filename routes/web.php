<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DrawingTransactionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
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
    Route::middleware('guest')->get('/register', 'registerForm')->name('registerForm');
    Route::middleware('guest')->post('/register', 'register')->name('register');
});

// DASHBOARD
Route::middleware('auth')->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

// DRAWING TRANSACTION
Route::middleware('auth')
->controller(DrawingTransactionController::class)
->group(function () {
    Route::get('/drawing-transactions', 'view')->name('drawingTransactionView');
    Route::get('/drawing-transactions/data', 'getData')->name('drawingTransactionData');
    Route::get('/drawing-transactions/create', 'createForm')->name('drawingTransactionCreateForm');
    Route::post('/drawing-transactions/create', 'create')->name('drawingTransactionCreate');
    Route::get('/drawing-transactions/detail/{id}', 'getDetail')->name('drawingTransactionDetail');
    Route::get('/drawing-transactions/detail/steps/{drawing_transaction_id}', 'getSteps')->name('drawingTransactionSteps');
    Route::get('/drawing-transactions/approval/{id}', 'approvalForm')->name('drawingTransactionApprovalForm');
    Route::post('/drawing-transactions/approval/{id}', 'approval')->name('drawingTransactionApproval');
    Route::get('/drawing-transactions/revise/{id}', 'reviseForm')->name('drawingTransactionReviseForm');
    Route::post('/drawing-transactions/revise/{id}', 'revise')->name('drawingTransactionRevise');
});

Route::middleware('auth')
->controller(UserController::class)
->group(function () {
    Route::get('/users', 'view')->name('userView');
    Route::get('/users/data', 'getData')->name('userData');
    Route::get('/users/detail/{id}', 'getDetail')->name('userDetail');
    Route::get('/users/edit/{id}', 'editForm')->name('userEditForm');
    Route::post('/users/edit/{id}', 'edit')->name('userEdit');
    Route::get('/users/delete/{id}', 'remove')->name('userDelete');
});


Route::middleware('auth')
->controller(RoleController::class)
->group(function () {
    Route::get('/roles', 'view')->name('roleView');
    Route::get('/roles/data', 'getData')->name('roleData');
    Route::get('/roles/detail/{id}', 'getDetail')->name('roleDetail');
    Route::get('/roles/create', 'createForm')->name('roleCreateForm');
    Route::post('/roles/create', 'create')->name('roleCreate');
    Route::get('/roles/edit/{id}', 'editForm')->name('roleEditForm');
    Route::post('/roles/edit/{id}', 'edit')->name('roleEdit');
    Route::get('/roles/delete/{id}', 'remove')->name('roleDelete');
});

Route::middleware('auth')
->controller(CustomerController::class)
->group(function () {
    Route::get('/customers', 'view')->name('customerView');
    Route::get('/customers/data', 'getData')->name('customerData');
    Route::get('/customers/detail/{id}', 'getDetail')->name('customerDetail');
    Route::get('/customers/create', 'createForm')->name('customerCreateForm');
    Route::post('/customers/create', 'create')->name('customerCreate');
    Route::get('/customers/edit/{id}', 'editForm')->name('customerEditForm');
    Route::post('/customers/edit/{id}', 'edit')->name('customerEdit');
    Route::get('/customers/delete/{id}', 'remove')->name('customerDelete');
});

