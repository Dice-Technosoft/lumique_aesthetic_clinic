<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VideoApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Video::orderBy('sort_order', 'asc');
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }
        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'youtube_url' => 'required|string',
            'category' => 'nullable|string',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|string',
            'status' => 'required|string|in:published,draft,archived',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=|shorts\/))([\w-]{11})/i', $data['youtube_url'], $matches)) {
            $data['youtube_video_id'] = $matches[1];
        }

        if ($request->hasFile('thumbnail_file')) {
            $path = $request->file('thumbnail_file')->store('videos', 'public');
            $data['thumbnail'] = '/storage/' . $path;
        } elseif (empty($data['thumbnail']) || !str_starts_with($data['thumbnail'], '/storage/')) {
            if (!empty($data['youtube_video_id'])) {
                $data['thumbnail'] = "https://img.youtube.com/vi/{$data['youtube_video_id']}/hqdefault.jpg";
            }
        }

        $data['slug'] = Str::slug($data['title']);
        $video = Video::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Clinical video created successfully',
            'data' => $video,
        ], 201);
    }

    public function update(Request $request, Video $video): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'youtube_url' => 'required|string',
            'category' => 'nullable|string',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|string',
            'status' => 'required|string|in:published,draft,archived',
            'is_featured' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=|shorts\/))([\w-]{11})/i', $data['youtube_url'], $matches)) {
            $data['youtube_video_id'] = $matches[1];
        }

        if ($request->hasFile('thumbnail_file')) {
            $path = $request->file('thumbnail_file')->store('videos', 'public');
            $data['thumbnail'] = '/storage/' . $path;
        } elseif (empty($data['thumbnail']) || !str_starts_with($data['thumbnail'], '/storage/')) {
            if (!empty($data['youtube_video_id'])) {
                $data['thumbnail'] = "https://img.youtube.com/vi/{$data['youtube_video_id']}/hqdefault.jpg";
            }
        }

        $data['slug'] = Str::slug($data['title']);
        $video->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Clinical video updated successfully',
            'data' => $video,
        ]);
    }

    public function destroy(Video $video): JsonResponse
    {
        $video->delete();
        return response()->json([
            'success' => true,
            'message' => 'Video deleted successfully',
        ]);
    }
}
