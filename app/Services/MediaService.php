<?php

namespace App\Services;

use App\Models\Media;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MediaService
{
    public function uploadFile(UploadedFile $file, string $folder = 'uploads', ?User $user = null, ?string $title = null, ?string $altText = null): Media
    {
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $extension;
        $path = $file->storeAs($folder, $fileName, 'public');

        return Media::create([
            'original_name' => $originalName,
            'file_name' => $fileName,
            'path' => $path,
            'url' => Storage::disk('public')->url($path),
            'disk' => 'public',
            'mime_type' => $file->getMimeType(),
            'extension' => $extension,
            'size' => $file->getSize(),
            'alt_text' => $altText ?: pathinfo($originalName, PATHINFO_FILENAME),
            'title' => $title ?: pathinfo($originalName, PATHINFO_FILENAME),
            'folder' => $folder,
            'uploaded_by' => $user?->id,
        ]);
    }

    public function deleteMedia(Media $media): bool
    {
        if (Storage::disk($media->disk)->exists($media->path)) {
            Storage::disk($media->disk)->delete($media->path);
        }
        return $media->delete();
    }
}
