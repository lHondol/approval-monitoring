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
                $scale = min($a4Width / $size['width'], $a4Height / $size['height']) * 1.05;
                $scaledWidth = $size['width'] * $scale;
                $scaledHeight = $size['height'] * $scale;

                // Center on A4
                $x = ($a4Width - $scaledWidth) / 2;
                $y = ($a4Height - $scaledHeight) / 2;

                // A4 page
                $pdf->AddPage('P', [$a4Width, $a4Height]);

                // Insert the PDF content
                $pdf->useTemplate($templateId, $x, $y, $scaledWidth, $scaledHeight);
            }
        }

        // Save file
        $pdf->Output('F', $outputFile);

        return "{$directory}/" . $outputName;
    }

    public function signPdf($sourcePath, $positionX, $positionY, $stamp, $dateAt)
    {
        if (!Storage::disk('public')->exists($sourcePath)) {
             throw new \Exception("PDF not found: " . $sourcePath);
        }

        $absolutePath = Storage::disk('public')->path($sourcePath);

        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);

        $pageCount = $pdf->setSourceFile($absolutePath);

        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

            // Import the page
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);

            // Make a page with EXACT same size as the original
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);

            // Render it FULL PAGE (0,0 and full width/height)
            $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);

            // --------------------------------------------------
            // Add stamp or signature here if needed
            // Example:
            $pdf->SetFont('Helvetica', 'B', 8);
            $pdf->SetTextColor(255, 0, 0);

            $pdf->SetXY($positionX, $positionY);
            $pdf->Cell(50, 10, $stamp);

            $userName = auth()->user()->name;
            $userRole = auth()->user()->roles[0]->name ?? 'Super Admin';

            $pdf->SetXY($positionX, $positionY + 6);
            $pdf->Cell(50, 6, "{$userName}");

            $pdf->SetXY($positionX, $positionY + 9);
            $pdf->Cell(50, 6, "{$userRole}");

            $pdf->SetXY($positionX, $positionY + 12);
            $pdf->Cell(50, 6, "at {$dateAt}");
            // --------------------------------------------------
        }

        $pdf->Output('F', $absolutePath);

        return $sourcePath;
    }
}
