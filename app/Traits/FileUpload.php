<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
trait FileUpload
{
    protected function uploadFile(UploadedFile $file, string $folder, ?string $oldPath = null, string $disk = 'local'): string
    {
        if ($oldPath && Storage::disk($disk)->exists($oldPath)) {
            Storage::disk($disk)->delete($oldPath);
        }
        return $file->store($folder, ['disk' => $disk]);
    }
    protected function deleteFile(?string $path, string $disk = 'local'): void
    {
        if ($path && Storage::disk($disk)->exists($path)) {
            Storage::disk($disk)->delete($path);
        }
    }
}