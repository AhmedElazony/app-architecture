<?php

namespace App\Support\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

trait DealsWithFiles
{
    public function storeFile(string $disk, string $path, UploadedFile $file): string
    {
        return Storage::disk($disk)->put($path, $file);
    }

    public function deleteFile(string $disk, string $filePath): bool
    {
        if (Storage::disk($disk)->exists($filePath)) {
            return Storage::disk($disk)->delete($filePath);
        }

        return false;
    }
}
