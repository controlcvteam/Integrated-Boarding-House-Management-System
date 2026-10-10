<?php

namespace App\Services;

use App\Models\MediaFile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileUploadService
{
    /**
     * Store an uploaded file into database media_files table and optionally to local storage.
     *
     * @param UploadedFile $file
     * @param string $folder e.g. 'rooms', 'avatars', 'settings', 'receipts', 'maintenance'
     * @return string The relative storage path e.g. 'rooms/randomhash.jpg'
     */
    public static function store(UploadedFile $file, string $folder): string
    {
        $folder = trim($folder, '/');
        $extension = $file->getClientOriginalExtension() ?: $file->extension() ?: 'jpg';
        $filename = Str::random(40) . '.' . strtolower($extension);
        $path = $folder . '/' . $filename;
        $mimeType = $file->getMimeType() ?: self::guessMimeType($extension);
        $fileSize = $file->getSize() ?: 0;

        $contents = file_get_contents($file->getRealPath());
        $base64Data = base64_encode($contents);

        // 1. Persist permanently in Supabase PostgreSQL
        MediaFile::updateOrCreate(
            ['path' => $path],
            [
                'mime_type' => $mimeType,
                'file_size' => $fileSize,
                'data' => $base64Data,
            ]
        );

        // 2. Best-effort write to local disk (succeeds in local dev, gracefully bypassed on read-only serverless)
        try {
            Storage::disk('public')->put($path, $contents);
        } catch (\Throwable $e) {
            // Read-only serverless filesystem silently caught
        }

        return $path;
    }

    /**
     * Store raw binary or string content (e.g. generated SVG or pre-existing asset).
     *
     * @param string $content
     * @param string $path e.g. 'rooms/sample-room-1.svg'
     * @param string|null $mimeType
     * @return string
     */
    public static function storeRaw(string $content, string $path, ?string $mimeType = null): string
    {
        $path = ltrim($path, '/');
        $mimeType = $mimeType ?: self::guessMimeType(pathinfo($path, PATHINFO_EXTENSION));

        MediaFile::updateOrCreate(
            ['path' => $path],
            [
                'mime_type' => $mimeType,
                'file_size' => strlen($content),
                'data' => base64_encode($content),
            ]
        );

        try {
            Storage::disk('public')->put($path, $content);
        } catch (\Throwable $e) {
            // Ignored
        }

        return $path;
    }

    /**
     * Delete a file from both the database and local disk.
     *
     * @param string|null $path
     * @return void
     */
    public static function delete(?string $path): void
    {
        if (!$path || $path === '0' || $path === 'null') {
            return;
        }

        $path = ltrim($path, '/');

        // Protect default demo SVGs from accidental deletion
        if (str_contains($path, 'default') || str_contains($path, 'sample-room') || str_ends_with($path, '.svg')) {
            return;
        }

        try {
            MediaFile::where('path', $path)->delete();
        } catch (\Throwable $e) {
            // Ignored
        }

        try {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        } catch (\Throwable $e) {
            // Ignored
        }
    }

    /**
     * Guess MIME type from extension.
     */
    public static function guessMimeType(string $extension): string
    {
        return match (strtolower($extension)) {
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'svg' => 'image/svg+xml',
            'pdf' => 'application/pdf',
            default => 'image/jpeg',
        };
    }
}
