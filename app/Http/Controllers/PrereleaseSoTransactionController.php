<?php

namespace App\Http\Controllers;

use App\Http\Requests\PrereleaseSoTransaction\ApprovalRequest;
use App\Http\Requests\PrereleaseSoTransaction\CreateRequest;
use App\Http\Requests\PrereleaseSoTransaction\ReviseRequest;
use App\Services\PrereleaseSoTransactionService;
use App\Services\EmailService;
use App\Services\PrereleaseSoNotificationReadService;
use Illuminate\Http\Request;

class PrereleaseSoTransactionController extends Controller
{
    private $prereleaseSoTransactionService;
    private $prereleaseSoNotificationReadService;
    public function __construct(
        PrereleaseSoTransactionService $prereleaseSoTransactionService,
        PrereleaseSoNotificationReadService $prereleaseSoNotificationReadService
    ) 
    {
        $this->prereleaseSoTransactionService = $prereleaseSoTransactionService;
        $this->prereleaseSoNotificationReadService = $prereleaseSoNotificationReadService;
    }

    public function view() {
        return view('prerelease-so-transaction.view');
    }

    public function createForm() {
        $customers = $this->prereleaseSoTransactionService->getCustomers();
        $areas = $this->prereleaseSoTransactionService->getAreas();
        $months = $this->prereleaseSoTransactionService->getMonths();
        return view('prerelease-so-transaction.create', compact('customers', 'areas', 'months'));
    }

    public function create(CreateRequest $request) {
        $data = $request->all();
        $data['files'] = $request->file('files');
        $prereleaseSoTransaction = $this->prereleaseSoTransactionService->create((object) $data);

        if (!$prereleaseSoTransaction)
            return redirect()->back()->withErrors(['files' => 'file(s) version not supported']);

        dispatch(function () use ($prereleaseSoTransaction) {
            app(EmailService::class)->sendRequestPrereleaseSoApprovalGeneral(
                $prereleaseSoTransaction->id, 
                $prereleaseSoTransaction->so_number,
                ['rnd_drawing_approve_prerelease_so_transaction']
            );
        })->afterResponse();

        return redirect()->route('prereleaseSoTransactionView');
    }

    public function getDetail(Request $request) {
        $id = $request->id;
        $data = $this->prereleaseSoTransactionService->getDetail($id);
        if (!$data)
            return redirect()->back();
        
        $this->prereleaseSoNotificationReadService->markAsRead($data->id);
        
        return view('prerelease-so-transaction.detail', compact('data'));
    }

    public function getData() {
        return $this->prereleaseSoTransactionService->getData();
    }

    public function getSteps(Request $request) {
        $prereleaseSoTransactionId = $request->prerelease_so_transaction_id;
        $data = $this->prereleaseSoTransactionService->getSteps($prereleaseSoTransactionId);
        return view('prerelease-so-transaction.tabs.step-tab', compact('data'));
    }

    public function approvalForm(Request $request) {
        $id = $request->id;
        $data = $this->prereleaseSoTransactionService->getDetail($id);
        if (!$data)
            return redirect()->back();

        $this->prereleaseSoNotificationReadService->markAsRead($data->id);
        return view('prerelease-so-transaction.approval', compact('data'));
    }

    public function approval(ApprovalRequest $request) {
        $data = array_merge(
            $request->all(),
            $request->route()->parameters()
        );

        if ($request->action == 'approve') {
            $this->prereleaseSoTransactionService->approve((object) $data);
        } else if ($request->action == 'reject') {
            $this->prereleaseSoTransactionService->reject((object) $data);
        } else if ($request->action == 'request-confirm-margin') {
            $this->prereleaseSoTransactionService->requestConfirmMargin((object) $data);
        }

        return redirect()->route('prereleaseSoTransactionView');
    }

    public function reviseForm(Request $request) {
        $customers = $this->prereleaseSoTransactionService->getCustomers();
        $areas = $this->prereleaseSoTransactionService->getAreas();
        $months = $this->prereleaseSoTransactionService->getMonths();
        $id = $request->id;
        $data = $this->prereleaseSoTransactionService->getDetail($id);

        if (!$data)
            return redirect()->back();
        
        $this->prereleaseSoNotificationReadService->markAsRead($data->id);
        return view('prerelease-so-transaction.revise', compact('data', 'customers', 'areas', 'months'));
    }

    public function revise(ReviseRequest $request) {
        $data = array_merge(
            $request->all(),
            $request->route()->parameters()
        );

        $prereleaseSoTransaction = $this->prereleaseSoTransactionService->revise((object) $data);

        if (!$prereleaseSoTransaction)
            return redirect()->back()->withErrors(['files' => 'file(s) version not supported']);

        dispatch(function () use ($prereleaseSoTransaction) {
            app(EmailService::class)->sendRequestPrereleaseSoApprovalGeneral(
                $prereleaseSoTransaction->id, 
                $prereleaseSoTransaction->so_number,
                ['rnd_drawing_approve_prerelease_so_transaction']
            );
        })->afterResponse();

        return redirect()->route('prereleaseSoTransactionView');
    }

    public function getBadgeCount() {
        $count = $this->prereleaseSoTransactionService->getBadgeCount();

        return response()->json([
            'count' => $count
        ]);
    }
}
