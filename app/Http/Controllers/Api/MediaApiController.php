<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MediaApiController extends Controller
{
    public function __construct(
        protected MediaService $mediaService
    ) {
    }

    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Media::latest()->paginate(24),
        ]);
    }

    public function upload(Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB
            'title' => 'nullable|string',
            'alt_text' => 'nullable|string',
            'folder' => 'nullable|string',
        ]);

        $media = $this->mediaService->uploadFile(
            $request->file('file'),
            $request->input('folder', 'uploads'),
            $request->user(),
            $request->input('title'),
            $request->input('alt_text')
        );

        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully',
            'data' => $media,
        ], 201);
    }
}
