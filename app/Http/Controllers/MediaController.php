<?php

namespace App\Http\Controllers;

use App\Models\MediaFile;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MediaController extends Controller
{
    /**
     * Dynamically serve storage media files from disk, Supabase PostgreSQL, or SVG fallback.
     *
     * @param Request $request
     * @param string $path
     * @return Response
     */
    public function serve(Request $request, string $path): Response
    {
        $path = ltrim($path, '/');

        // Prevent path traversal
        if (str_contains($path, '..')) {
            abort(403, 'Forbidden path.');
        }

        // Handle empty or invalid '0' paths gracefully
        if ($path === '' || $path === '0' || $path === 'null') {
            return $this->fallbackResponse('rooms');
        }

        // 1. Try public storage directory
        $publicDiskPath = public_path('storage/' . $path);
        if (file_exists($publicDiskPath) && is_file($publicDiskPath)) {
            return response()->file($publicDiskPath, [
                'Cache-Control' => 'public, max-age=86400, must-revalidate',
            ]);
        }

        // 2. Try storage/app/public directory
        $appDiskPath = storage_path('app/public/' . $path);
        if (file_exists($appDiskPath) && is_file($appDiskPath)) {
            return response()->file($appDiskPath, [
                'Cache-Control' => 'public, max-age=86400, must-revalidate',
            ]);
        }

        // 3. Query PostgreSQL database media_files table
        try {
            $media = MediaFile::where('path', $path)->first();
            if ($media && !empty($media->data)) {
                $content = base64_decode($media->data);
                $etag = '"' . md5($media->updated_at ?? $media->created_at ?? $media->id) . '"';

                if ($request->header('If-None-Match') === $etag) {
                    return response('', 304);
                }

                $mimeType = $media->mime_type ?: FileUploadService::guessMimeType(pathinfo($path, PATHINFO_EXTENSION));

                return response($content, 200, [
                    'Content-Type' => $mimeType,
                    'Content-Length' => strlen($content),
                    'Cache-Control' => 'public, max-age=86400, must-revalidate',
                    'ETag' => $etag,
                ]);
            }
        } catch (\Throwable $e) {
            // DB query fallback
        }

        // 4. Graceful fallback image instead of 404 broken icon
        return $this->fallbackResponse($path);
    }

    /**
     * Provide an appropriate fallback SVG response.
     *
     * @param string $path
     * @return Response
     */
    protected function fallbackResponse(string $path): Response
    {
        if (str_starts_with($path, 'avatars')) {
            $avatar = public_path('storage/avatars/admin.svg');
            if (file_exists($avatar)) {
                return response()->file($avatar, [
                    'Content-Type' => 'image/svg+xml',
                    'Cache-Control' => 'public, max-age=86400',
                ]);
            }
        }

        if (str_starts_with($path, 'settings') || str_contains($path, 'qr')) {
            $qr = public_path('storage/settings/default-gcash-qr.svg');
            if (file_exists($qr)) {
                return response()->file($qr, [
                    'Content-Type' => 'image/svg+xml',
                    'Cache-Control' => 'public, max-age=86400',
                ]);
            }
        }

        $room = public_path('images/room-placeholder.svg');
        if (file_exists($room)) {
            return response()->file($room, [
                'Content-Type' => 'image/svg+xml',
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        $sampleRoom = public_path('storage/rooms/sample-room-1.svg');
        if (file_exists($sampleRoom)) {
            return response()->file($sampleRoom, [
                'Content-Type' => 'image/svg+xml',
                'Cache-Control' => 'public, max-age=86400',
            ]);
        }

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="300" height="200" viewBox="0 0 300 200"><rect width="100%" height="100%" fill="#f1f5f9"/><text x="50%" y="50%" dominant-baseline="middle" text-anchor="middle" fill="#94a3b8" font-family="sans-serif" font-size="16">No Preview Available</text></svg>';
        return response($svg, 200, [
            'Content-Type' => 'image/svg+xml',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }
}
