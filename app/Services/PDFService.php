<?php

namespace App\Services;

use App\Customs\PdfWithRotation;
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

    public function mergePdf($sourceFiles, $outputName, $directory="drawing-pdfs")
    {
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $outputFile = storage_path("app/public/$directory/" . $outputName);

        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);

        // A4 size
        $a4Width = 297;
        $a4Height = 210;

        foreach ($sourceFiles as $file) {

            // Uploaded file → use getRealPath()
            $sourcePath = $file->getRealPath();

            $pageCount = $pdf->setSourceFile($sourcePath);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                // $scaleRatio = 1.05;
                $scaleRatio = 1;

                // Scale to fit A4
                $scale = min($a4Width / $size['width'], $a4Height / $size['height']) * $scaleRatio;
                $scaledWidth = $size['width'] * $scale;
                $scaledHeight = $size['height'] * $scale;

                // Center on A4
                $x = ($a4Width - $scaledWidth) / 2;
                $y = ($a4Height - $scaledHeight) / 2;

                // A4 page
                $pdf->AddPage('L', [$a4Width, $a4Height]);

                // Insert the PDF content
                $pdf->useTemplate($templateId, $x, $y, $scaledWidth, $scaledHeight);
            }
        }

        // Save file
        $pdf->Output('F', $outputFile);

        return "{$directory}/" . $outputName;
    }

    public function mergePdfWithNote($sourceFiles, $outputName, $positionX, $positionY, $note, $directory="drawing-pdfs")
    {
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $outputFile = storage_path("app/public/$directory/" . $outputName);

        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);

        // A4 size
        $a4Width = 297;
        $a4Height = 210;

        foreach ($sourceFiles as $file) {

            // Uploaded file → use getRealPath()
            $sourcePath = $file->getRealPath();

            $pageCount = $pdf->setSourceFile($sourcePath);

            for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {

                $templateId = $pdf->importPage($pageNo);
                $size = $pdf->getTemplateSize($templateId);

                // $scaleRatio = 1.05;
                $scaleRatio = 1;

                // Scale to fit A4
                $scale = min($a4Width / $size['width'], $a4Height / $size['height']) * $scaleRatio;
                $scaledWidth = $size['width'] * $scale;
                $scaledHeight = $size['height'] * $scale;

                // Center on A4
                $x = ($a4Width - $scaledWidth) / 2;
                $y = ($a4Height - $scaledHeight) / 2;

                // A4 page
                $pdf->AddPage('L', [$a4Width, $a4Height]);

                // Insert the PDF content
                $pdf->useTemplate($templateId, $x, $y, $scaledWidth, $scaledHeight);

                // Place text normally (inside rotated context)
                $blockWidth = 50;
                $lineHeight = 4;
                $pdf->SetFont('Helvetica', 'B', 8);
                $pdf->SetTextColor(255, 0, 0);
        
                $pdf->SetXY($positionX, $positionY);
                $pdf->MultiCell($blockWidth, $lineHeight, $note);
            }
        }

        // Save file
        $pdf->Output('F', $outputFile);

        return "{$directory}/" . $outputName;
    }

    // $otherTexts -> [[posX, posY, text], [posX, posY, text], [posX, posY, text], ...]
    public function signPdf($sourcePath, $positionX, $positionY, $stamp, $dateAt, $otherTexts = [])
    {
        if (!Storage::disk('public')->exists($sourcePath)) {
            throw new \Exception("PDF not found: " . $sourcePath);
        }
    
        $absolutePath = Storage::disk('public')->path($sourcePath);
    
        // Use rotation-capable FPDI class
        // $pdf = new PdfWithRotation();
        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);
    
        $pageCount = $pdf->setSourceFile($absolutePath);
    
        // Build the signature text block
        $userName = auth()->user()->name;
        $userRole = auth()->user()->roles[0]->name ?? 'Super Admin';
    
        $text = "{$stamp}\n{$userName}\n{$userRole}\nat {$dateAt}";
    
        // Block width & line height for MultiCell
        $blockWidth = 50;
        $lineHeight = 4;
    
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
    
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);
    
            // Create page same as original
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
    
            // Draw original PDF page
            $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);
    
            // ---------------------------
            //       ROTATED TEXT
            // ---------------------------
    
            // Rotate 90° clockwise around block center
            // $pdf->Rotate(-90, $positionX, $positionY);
    
            // Place text normally (inside rotated context)
            $pdf->SetFont('Helvetica', 'B', 8);
            $pdf->SetTextColor(255, 0, 0);
    
            if (count($otherTexts) > 0) {
                foreach ($otherTexts as $otherText) {
                    $otherPosX = $otherText["posX"];
                    $otherPosY = $otherText["posY"];
                    $otherText = $otherText["text"];
                    $pdf->SetXY($otherPosX, $otherPosY);
                    $pdf->MultiCell($blockWidth, $lineHeight, $otherText);
                }
            }

            $pdf->SetXY($positionX, $positionY);
            $pdf->MultiCell($blockWidth, $lineHeight, $text);
    
            // // Stop rotating
            // $pdf->Rotate(0);
        }
    
        // Save file back to same path
        $pdf->Output('F', $absolutePath);
    
        return $sourcePath;
    }

    public function writeTextPdf($sourcePath, $positionX, $positionY, $text)
    {
        if (!Storage::disk('public')->exists($sourcePath)) {
            throw new \Exception("PDF not found: " . $sourcePath);
        }
    
        $absolutePath = Storage::disk('public')->path($sourcePath);
    
        // Use rotation-capable FPDI class
        // $pdf = new PdfWithRotation();
        $pdf = new Fpdi();
        $pdf->SetAutoPageBreak(false);
    
        $pageCount = $pdf->setSourceFile($absolutePath);
    
        // Block width & line height for MultiCell
        $blockWidth = 50;
        $lineHeight = 4;
    
        for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
    
            $templateId = $pdf->importPage($pageNo);
            $size = $pdf->getTemplateSize($templateId);
    
            // Create page same as original
            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
    
            // Draw original PDF page
            $pdf->useTemplate($templateId, 0, 0, $size['width'], $size['height']);
    
            // ---------------------------
            //       ROTATED TEXT
            // ---------------------------
    
            // Rotate 90° clockwise around block center
            // $pdf->Rotate(-90, $positionX, $positionY);
    
            // Place text normally (inside rotated context)
            $pdf->SetFont('Helvetica', 'B', 8);
            $pdf->SetTextColor(255, 0, 0);
    
            $pdf->SetXY($positionX, $positionY);
            $pdf->MultiCell($blockWidth, $lineHeight, $text);
    
            // // Stop rotating
            // $pdf->Rotate(0);
        }
    
        // Save file back to same path
        $pdf->Output('F', $absolutePath);
    
        return $sourcePath;
    }
}
