<?php

namespace App\Services;

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

    public function mergeDrawingPdf($sourceFiles, $outputName) {

        // Output path
        $outputFile = storage_path("app/public/pdfs/" . $outputName);

        $pdf = new Fpdi();

        // A4 dimensions in mm
        $a4Width = 210;
        $a4Height = 297;

        foreach ($sourceFiles as $sourceFile) {
            $sourcePath = storage_path('app/public/' . $sourceFile); // read from private storage
            $pageCount = $pdf->setSourceFile($sourcePath);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                // Calculate scale to target width
                $scale = min($a4Width / $size['width'], $a4Height / $size['height']);
                $scaledWidth = $size['width'] * $scale;
                $scaledHeight = $size['height'] * $scale;

                // Center on A4
                $x = ($a4Width - $scaledWidth) / 2;
                $y = ($a4Height - $scaledHeight) / 2;

                // Add new A4 page
                $pdf->AddPage('P', [$a4Width, $a4Height]);

                // Place imported page
                $pdf->useTemplate($templateId, $x, $y, $scaledWidth, $scaledHeight);

                // --------- ADD TEXT OUTSIDE THE SAFE AREA ---------
                $stampX = $x + $scaledWidth - 50;   // 50mm from right of PDF content
                $stampY = 0;   // 5mm below the imported page

                $pdf->SetFont('Helvetica', 'B', 12);
                $pdf->SetTextColor(255, 0, 0);
                $pdf->SetXY($stampX, $stampY);
                $pdf->Cell(40, 10, 'Approved', 0, 0, 'C');
                // ---------------------------------------------------
            }
        }

        // Save merged and scaled PDF
        $pdf->Output('F', $outputFile);

        return "pdfs/" . $outputName; 
    }
}
