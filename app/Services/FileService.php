<?php

namespace App\Services;

use App\Customs\PdfWithRotation;
use Illuminate\Support\Facades\Storage;
use setasign\Fpdi\Fpdi;

class FileService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function storeFiles($files, $directory = "sample-pictures")
    {
        if (!$files) return [];

        // normalize jadi array (biar bisa handle single & multiple)
        $files = is_array($files) ? $files : [$files];

        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        $storedFiles = [];

        foreach ($files as $file) {
            if (!$file) continue;

            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $path = $file->storeAs($directory, $filename, 'public');

            $storedFiles[] = $path;
        }

        return $storedFiles;
    }
}
