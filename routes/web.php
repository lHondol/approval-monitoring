<?php

use App\Http\Controllers\AreaController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DrawingTransactionController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\PrereleaseSoTransactionController;
use App\Http\Controllers\ReportingController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

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

    // View
    Route::middleware('permission:view_drawing_transaction|view_distributed_drawing_transaction')
        ->get('/drawing-transactions', 'view')
        ->name('drawingTransactionView');

    Route::middleware('permission:view_drawing_transaction|view_distributed_drawing_transaction')
        ->get('/drawing-transactions/data', 'getData')
        ->name('drawingTransactionData');

    Route::middleware('permission:view_drawing_transaction|view_distributed_drawing_transaction')
        ->get('/drawing-transactions/detail/{id}', 'getDetail')
        ->name('drawingTransactionDetail');

    Route::middleware('permission:view_drawing_transaction|view_distributed_drawing_transaction')
        ->get('/drawing-transactions/detail/steps/{drawing_transaction_id}', 'getSteps')
        ->name('drawingTransactionSteps');

    // Create
    Route::middleware('permission:create_drawing_transaction')
        ->get('/drawing-transactions/create', 'createForm')
        ->name('drawingTransactionCreateForm');

    Route::middleware('permission:create_drawing_transaction')
        ->post('/drawing-transactions/create', 'create')
        ->name('drawingTransactionCreate');

    // Revise
    Route::middleware('permission:revise_drawing_transaction')
        ->get('/drawing-transactions/revise/{id}', 'reviseForm')
        ->name('drawingTransactionReviseForm');

    Route::middleware('permission:revise_drawing_transaction')
        ->post('/drawing-transactions/revise/{id}', 'revise')
        ->name('drawingTransactionRevise');

    // Approval (first & second)
    Route::middleware('permission:first_approve_drawing_transaction|second_approve_drawing_transaction|bom_approve_distributed_drawing_transaction|costing_approve_distributed_drawing_transaction')
        ->get('/drawing-transactions/approval/{id}', 'approvalForm')
        ->name('drawingTransactionApprovalForm');

    Route::middleware('permission:first_approve_drawing_transaction|second_approve_drawing_transaction|bom_approve_distributed_drawing_transaction|costing_approve_distributed_drawing_transaction')
        ->post('/drawing-transactions/approval/{id}', 'approval')
        ->name('drawingTransactionApproval');

});

// PRERELEASE SO TRANSACTION
Route::middleware('auth')
->controller(PrereleaseSoTransactionController::class)
->group(function () {

    // View
    Route::middleware('permission:view_prerelease_so_transaction')
        ->get('/prerelease-so-transactions', 'view')
        ->name('prereleaseSoTransactionView');

    Route::middleware('permission:view_prerelease_so_transaction')
        ->get('/prerelease-so-transactions/data', 'getData')
        ->name('prereleaseSoTransactionData');

    Route::middleware('permission:view_prerelease_so_transaction')
        ->get('/prerelease-so-transactions/detail/{id}', 'getDetail')
        ->name('prereleaseSoTransactionDetail');

    Route::middleware('permission:view_prerelease_so_transaction')
        ->get('/prerelease-so-transactions/detail/steps/{prerelease_so_transaction_id}', 'getSteps')
        ->name('prereleaseSoTransactionSteps');

    // Create
    Route::middleware('permission:create_prerelease_so_transaction')
        ->get('/prerelease-so-transactions/create', 'createForm')
        ->name('prereleaseSoTransactionCreateForm');

    Route::middleware('permission:create_prerelease_so_transaction')
        ->post('/prerelease-so-transactions/create', 'create')
        ->name('prereleaseSoTransactionCreate');

    // Revise
    Route::middleware('permission:revise_prerelease_so_transaction')
        ->get('/prerelease-so-transactions/revise/{id}', 'reviseForm')
        ->name('prereleaseSoTransactionReviseForm');

    Route::middleware('permission:revise_prerelease_so_transaction')
        ->post('/prerelease-so-transactions/revise/{id}', 'revise')
        ->name('prereleaseSoTransactionRevise');

    // Approval (first & second)
    Route::middleware('permission:sales_area_approve_prerelease_so_transaction|rnd_drawing_approve_prerelease_so_transaction|rnd_bom_approve_prerelease_so_transaction|accounting_approve_prerelease_so_transaction|it_approve_prerelease_so_transaction')
        ->get('/prerelease-so-transactions/approval/{id}', 'approvalForm')
        ->name('prereleaseSoTransactionApprovalForm');

    Route::middleware('permission:sales_area_approve_prerelease_so_transaction|rnd_drawing_approve_prerelease_so_transaction|rnd_bom_approve_prerelease_so_transaction|accounting_approve_prerelease_so_transaction|it_approve_prerelease_so_transaction')
        ->post('/prerelease-so-transactions/approval/{id}', 'approval')
        ->name('prereleaseSoTransactionApproval');

});


Route::middleware('auth')
->controller(UserController::class)
->group(function () {

    Route::middleware('permission:view_user')
        ->get('/users', 'view')
        ->name('userView');

    Route::middleware('permission:view_user')
        ->get('/users/data', 'getData')
        ->name('userData');

    Route::middleware('permission:view_user')
        ->get('/users/detail/{id}', 'getDetail')
        ->name('userDetail');

    Route::middleware('permission:edit_user')
        ->get('/users/edit/{id}', 'editForm')
        ->name('userEditForm');

    Route::middleware('permission:edit_user')
        ->post('/users/edit/{id}', 'edit')
        ->name('userEdit');

    Route::middleware('permission:delete_user')
        ->get('/users/delete/{id}', 'remove')
        ->name('userDelete');
});

Route::middleware('auth')
->controller(RoleController::class)
->group(function () {

    Route::middleware('permission:view_role')
        ->get('/roles', 'view')
        ->name('roleView');

    Route::middleware('permission:view_role')
        ->get('/roles/data', 'getData')
        ->name('roleData');

    Route::middleware('permission:view_role')
        ->get('/roles/detail/{id}', 'getDetail')
        ->name('roleDetail');

    Route::middleware('permission:create_role')
        ->get('/roles/create', 'createForm')
        ->name('roleCreateForm');

    Route::middleware('permission:create_role')
        ->post('/roles/create', 'create')
        ->name('roleCreate');

    Route::middleware('permission:edit_role')
        ->get('/roles/edit/{id}', 'editForm')
        ->name('roleEditForm');

    Route::middleware('permission:edit_role')
        ->post('/roles/edit/{id}', 'edit')
        ->name('roleEdit');

    Route::middleware('permission:delete_role')
        ->get('/roles/delete/{id}', 'remove')
        ->name('roleDelete');
});

Route::middleware('auth')
->controller(CustomerController::class)
->group(function () {

    Route::middleware('permission:view_customer')
        ->get('/customers', 'view')
        ->name('customerView');

    Route::middleware('permission:view_customer')
        ->get('/customers/data', 'getData')
        ->name('customerData');

    Route::middleware('permission:view_customer')
        ->get('/customers/detail/{id}', 'getDetail')
        ->name('customerDetail');

    Route::middleware('permission:create_customer')
        ->get('/customers/create', 'createForm')
        ->name('customerCreateForm');

    Route::middleware('permission:create_customer')
        ->post('/customers/create', 'create')
        ->name('customerCreate');

    Route::middleware('permission:edit_customer')
        ->get('/customers/edit/{id}', 'editForm')
        ->name('customerEditForm');

    Route::middleware('permission:edit_customer')
        ->post('/customers/edit/{id}', 'edit')
        ->name('customerEdit');

    Route::middleware('permission:delete_customer')
        ->get('/customers/delete/{id}', 'remove')
        ->name('customerDelete');
});

Route::controller(PasswordController::class)
->group(function () {

    Route::get('/password/change', 'changePasswordForm')
        ->name('passwordChangeForm')->middleware('auth');

    Route::post('/password/change', 'changePassword')
        ->name('passwordChange')->middleware('auth');;

    Route::get('/password/forgot', 'forgotPasswordForm')
        ->name('passwordForgotForm');

    Route::post('/password/reset-link', 'sendResetLink')
        ->name('passwordSendResetLink');

    Route::get('/password/reset/{token}', 'showResetForm')
        ->name('passwordResetForm');

    Route::post('/password/reset', 'resetPassword')
        ->name('passwordUpdate');
});

Route::middleware('auth')
->controller(AreaController::class)
->group(function () {

    Route::middleware('permission:view_area')
        ->get('/areas', 'view')
        ->name('areaView');

    Route::middleware('permission:view_area')
        ->get('/areas/data', 'getData')
        ->name('areaData');

    Route::middleware('permission:view_area')
        ->get('/areas/detail/{id}', 'getDetail')
        ->name('areaDetail');

    Route::middleware('permission:create_area')
        ->get('/areas/create', 'createForm')
        ->name('areaCreateForm');

    Route::middleware('permission:create_area')
        ->post('/areas/create', 'create')
        ->name('areaCreate');

    Route::middleware('permission:edit_area')
        ->get('/areas/edit/{id}', 'editForm')
        ->name('areaEditForm');

    Route::middleware('permission:edit_area')
        ->post('/areas/edit/{id}', 'edit')
        ->name('areaEdit');

    Route::middleware('permission:delete_area')
        ->get('/areas/delete/{id}', 'remove')
        ->name('areaDelete');
});

Route::controller(ReportingController::class)
->group(function () {
    Route::get('/reportings', 'view')
        ->name('reportingView')->middleware('auth');
    Route::post('/reportings/export', 'export')
        ->name('reportingExport')->middleware('auth');
});

Route::get('/app-update', function () {
    // Optional: add a secret key for security
    if (!request()->has('key') || (request()->get('key') !== env('ARTISAN_KEY'))) {
        abort(403, 'Unauthorized');
    }

    // Run Seeder
    Artisan::call('db:seed --class=UserRolePermissionV2Seeder');
    Artisan::call('db:seed --class=UserRolePermissionV3Seeder');

    // Update V1 Distributed Status
    Artisan::call('app:update-distributed-drawing-transaction-status');

    // Run storage:link
    Artisan::call('storage:link');

    // Run optimize:clear
    Artisan::call('optimize:clear');

    return 'Commands executed: storage:link and optimize:clear';
});