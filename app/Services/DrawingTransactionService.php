<?php

namespace App\Services;

use App\Enums\StatusEnum;
use App\Models\DrawingTransaction;
use App\Models\DrawingTransactionImage;
use App\Models\DrawingTransactionStep;
use Carbon\Carbon;
use Str;
use Yajra\DataTables\DataTables;

class DrawingTransactionService
{
    private $pdfService;
    /**
     * Create a new class instance.
     */
    public function __construct(PDFService $pdfService)
    {
        $this->pdfService = $pdfService;
    }

    public function getData() {
        return DataTables::of(DrawingTransaction::select([
            'customer_name',
            'so_number',
            'po_number',
            'created_at',
            'status',
        ]))
        ->editColumn('created_at', function($row) {
            return Carbon::parse($row->created_at)->format('d M Y H:i:s');
        })
        ->make(true);
    }

    public function create($data) {
        $drawingTransaction = new DrawingTransaction();
        $drawingTransaction->customer_name = $data->customer_name;
        $drawingTransaction->so_number = $data->so_number;
        $drawingTransaction->po_number = $data->po_number;

        $status = StatusEnum::WAITING_1ST_APPROVAL;
        $drawingTransaction->status = $status->value;

        if (isset($data->description))
            $drawingTransaction->description = $data->description; 

        $filePaths = $this->uploadPdf(
            $data->files, 
            $data->customer_name, 
            $data->so_number, 
            $data->po_number
        );

        $mergedFilePath = $this->mergePdf($filePaths,           
            $data->customer_name, 
            $data->so_number, 
            $data->po_number,
            $status->value
        );

        $drawingTransaction->filepath = $mergedFilePath;
        $drawingTransaction->save();

        $this->createImages($drawingTransaction->id, $filePaths);
        $this->createStep($drawingTransaction);
    }

    public function uploadPdf($files, $customerName, $soNumber, $poNumber) {
        $uploadedFiles = [];

        foreach ($files as $index => $file) {
            if (!$file->isValid()) continue;

            // Clean strings to remove spaces/special characters
            $customerNameClean = Str::slug($customerName, '_');
            $soNumberClean = Str::slug($soNumber, '_');
            $poNumberClean = Str::slug($poNumber, '');

            $timestamp = now()->format('Ymd_His');
            $extension = $file->getClientOriginalExtension();

            $newFileName = "{$customerNameClean}_{$soNumberClean}_{$poNumberClean}_{$timestamp}_{$index}.{$extension}";

            // Save to storage/app/public/pdfs
            $path = $file->storeAs('pdfs', $newFileName);

            // Only return the filename (without "public/") if needed
            $uploadedFiles[] = 'pdfs/' . $newFileName;
        }

        return $uploadedFiles;
    }

    public function mergePdf($filepaths, $customerName, $soNumber, $poNumber, $status) {
        $customerNameClean = Str::slug($customerName, '_');
        $soNumberClean = Str::slug($soNumber, '_');
        $poNumberClean = Str::slug($poNumber, '');
        $status = Str::slug($status, '_');

        $timestamp = now()->format('Ymd_His');

        $newFileName = "{$customerNameClean}_{$soNumberClean}_{$poNumberClean}_{$timestamp}_{$status}.pdf";

        return $this->pdfService->mergeDrawingPdf($filepaths, $newFileName);
    }

    public function createImages($drawingTransactionId, $filepaths) {
        foreach ($filepaths as $filepath) {
            $drawingTransactionImage = new DrawingTransactionImage();
            $drawingTransactionImage->drawing_transaction_id = $drawingTransactionId;
            $drawingTransactionImage->filepath = $filepath;
            $drawingTransactionImage->save();
        }
    }

    public function createStep($drawingTransaction) {
        $drawingTransactionStep = new DrawingTransactionStep();
        $drawingTransactionStep->drawing_transaction_id = $drawingTransaction->id;
        $drawingTransactionStep->do_by_user = auth()->user()->id;
        $drawingTransactionStep->do_at = $drawingTransaction->updated_at;
        $drawingTransactionStep->status = $drawingTransaction->status;
        $drawingTransactionStep->reject_reason = $drawingTransaction->revise_reason;
        $drawingTransactionStep->filepath = $drawingTransaction->filepath;
        $drawingTransactionStep->save();
    }
}
