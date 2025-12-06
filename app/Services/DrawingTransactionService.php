<?php

namespace App\Services;

use App\Enums\ActionDrawingTransactionStep;
use App\Enums\StatusDrawingTransaction;
use App\Models\DrawingTransaction;
use App\Models\DrawingTransactionStep;
use Carbon\Carbon;
use Ramsey\Uuid\Uuid;
use Str;
use Yajra\DataTables\DataTables;

class DrawingTransactionService
{
    private PDFService $pdfService;
    private DrawingTransactionStepService $drawingTransactionStepService;
    private DrawingTransactionRejectedImageService $drawingTransactionRejectedImageService;
    /**
     * Create a new class instance.
     */
    public function __construct(
        PDFService $pdfService, 
        DrawingTransactionStepService $drawingTransactionStepService,
        DrawingTransactionRejectedImageService $drawingTransactionRejectedImageService
    )
    {
        $this->pdfService = $pdfService;
        $this->drawingTransactionStepService = $drawingTransactionStepService;
        $this->drawingTransactionRejectedImageService = $drawingTransactionRejectedImageService;
    }

    private function renderActionButtons($row)
    {
        $data = $row;
        return view('drawing-transaction.datatables.actions', compact('data'));
    }

    public function renderStatusColor($status) {
        return match ($status) {
          StatusDrawingTransaction::WAITING_1ST_APPROVAL->value  => "teal",
          StatusDrawingTransaction::WAITING_2ND_APPROVAL->value   => "orange",
          StatusDrawingTransaction::REVISE_NEEDED->value   => "yellow",
          StatusDrawingTransaction::DISTRIBUTED->value   => "purple",
        };
    }

    public function getData() {
        return DataTables::of(DrawingTransaction::select([
            'id',
            'customer_name',
            'so_number',
            'po_number',
            'description',
            'created_at',
            'distributed_at',
            'status',
            'as_additional_data',
            'done_revised',
        ]))
        ->addColumn('actions', function($row) {
            return $this->renderActionButtons($row);
        })
        ->editColumn('status', function($row) {
            $renderStatusColor = $this->renderStatusColor($row->status);
            $statusHtml = "<div class='flex gap-2 flex-wrap'>";

            if ($row->as_additional_data) {
                $statusHtml .= "<span class='ui green label'>Additional Data</span>";
            }

            if ($row->done_revised) {
                $statusHtml .= "<span class='ui green label'>Revised</span>";
            }

            $statusHtml .= "<span class='ui label {$renderStatusColor}'>{$row->status}</span>";

            $statusHtml .= "</div>";

            return $statusHtml;
        })
        ->editColumn('created_at', function($row) {
            return Carbon::parse($row->created_at)->format('d M Y H:i:s');
        })
        ->editColumn('distributed_at', function($row) {
            if ($row->distributed_at)
                return Carbon::parse($row->distributed_at)->format('d M Y H:i:s');
            return '';
        })
        ->rawColumns(['status', 'actions'])
        ->make(true);
    }

    public function getSteps($drawingTransactionId) {
        return DrawingTransactionStep::where('drawing_transaction_id', $drawingTransactionId)
            ->with(['user', 'rejected_file'])
            ->get();
    }

    public function getDetail($id) {
        $drawingTransaction = DrawingTransaction::where('id', $id)->first();
        return $drawingTransaction;
    }

    public function create($data) {
        $drawingTransaction = new DrawingTransaction();
        
        $uuid =  Uuid::uuid4()->toString();
        $drawingTransaction->id =$uuid;

        $drawingTransaction->customer_name = $data->customer_name;
        $drawingTransaction->po_number = $data->po_number;

        $status = StatusDrawingTransaction::WAITING_1ST_APPROVAL;
        $drawingTransaction->status = $status->value;

        if (isset($data->description))
            $drawingTransaction->description = $data->description; 

        if (isset($data->as_additional_data)) {
            $drawingTransaction->as_additional_data = !!$data->as_additional_data;
            $drawingTransaction->additional_data_note = $data->additional_data_note;
        }

        $mergedFilePath = $this->mergePdf(
            $data->files, 
            $uuid,
            $status->value
        );

        $drawingTransaction->filepath = $mergedFilePath;
        $drawingTransaction->save();

        $this->drawingTransactionStepService->createStep($drawingTransaction, ActionDrawingTransactionStep::UPLOAD);

        return $drawingTransaction;
    }

    public function mergePdf($files, $drawingTransactionId, $status) {
        $timestamp = now()->format('Ymd_His');

        $newFileName = "{$drawingTransactionId}_{$timestamp}.pdf";

        return $this->pdfService->mergeDrawingPdf($files, $newFileName);
    }

    public function approve($data) {
        $drawingTransaction = DrawingTransaction::where('id', $data->id)->first();
        $drawingTransaction->state->next($data);
    }

    public function reject($data) {
        $drawingTransaction = DrawingTransaction::where('id', $data->id)->first();
        $drawingTransaction->state->reject($data);
    }

    public function revise($data) {
        $drawingTransaction = DrawingTransaction::where('id', $data->id)->first();
        $drawingTransaction->state->next($data);
    }
}
