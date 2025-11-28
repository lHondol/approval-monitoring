<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class PDFService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function mergeDrawingPdf($sourceFiles, $outputName)
    {
        $directory = 'drawing-pdfs';

        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $outputFile = storage_path("app/public/drawing-pdfs/" . $outputName);

        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);

        // A4 size
        $a4Width = 210;
        $a4Height = 297;

        foreach ($sourceFiles as $file) {

            // Uploaded file → use getRealPath()
            $sourcePath = $file->getRealPath();

            $pageCount = $pdf->setSourceFile($sourcePath);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                // Scale to fit A4
                $scale = min($a4Width / $size['width'], $a4Height / $size['height']);
                $scaledWidth = $size['width'] * $scale;
                $scaledHeight = $size['height'] * $scale;

                // Center on A4
                $x = ($a4Width - $scaledWidth) / 2;
                $y = ($a4Height - $scaledHeight) / 2;

                // A4 page
                $pdf->AddPage('P', [$a4Width, $a4Height]);

                // Insert the PDF content
                $pdf->useTemplate($templateId, $x, $y, $scaledWidth, $scaledHeight);

                // ---- Add stamp at top-right of page ----
                $pdf->SetFont('Helvetica', 'B', 12);
                $pdf->SetTextColor(255, 0, 0);

                $pdf->SetXY($a4Width - 60, 10); // safe position
                $pdf->Cell(50, 10, 'Approved', 0, 0, 'C');
            }
        }

        // Save file
        $pdf->Output('F', $outputFile);

        return "drawing-pdfs/" . $outputName;
    }
}
