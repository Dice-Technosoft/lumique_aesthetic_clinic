<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServiceApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Service::orderBy('sort_order', 'asc');
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }
        if ($request->boolean('featured')) {
            $query->where('is_featured', true);
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }

    public function show(string $slug): JsonResponse
    {
        $service = Service::published()->where('slug', $slug)->with('seoMeta')->firstOrFail();
        return response()->json([
            'success' => true,
            'data' => $service,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'category' => 'required|string',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'price_starting_at' => 'nullable|string',
            'duration' => 'nullable|string',
            'downtime' => 'nullable|string|max:100',
            'featured_image' => 'nullable|string',
            'icon' => 'nullable|string',
            'status' => 'required|string|in:published,draft,archived',
            'is_featured' => 'boolean',
            'benefits' => 'nullable',
            'procedure_steps' => 'nullable',
            'video_url' => 'nullable|string',
            'video_title' => 'nullable|string',
            'gallery_images' => 'nullable',
            'gallery_files.*' => 'nullable|image|max:10240',
            'gallery_videos' => 'nullable',
            'video_files.*' => 'nullable|file|max:102400',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('services', 'public');
            $data['featured_image'] = '/storage/' . $path;
        }

        // Process Gallery / Sub-Images
        $galleryImages = [];
        if ($request->filled('gallery_images')) {
            if (is_string($request->gallery_images)) {
                $decoded = json_decode($request->gallery_images, true);
                $galleryImages = is_array($decoded) ? $decoded : array_filter(array_map('trim', explode("\n", $request->gallery_images)));
            } elseif (is_array($request->gallery_images)) {
                $galleryImages = $request->gallery_images;
            }
        }

        if ($request->hasFile('gallery_files')) {
            foreach ($request->file('gallery_files') as $gFile) {
                if ($gFile && $gFile->isValid()) {
                    $gPath = $gFile->store('services/gallery', 'public');
                    $galleryImages[] = '/storage/' . $gPath;
                }
            }
        }
        $data['gallery_images'] = array_values(array_unique(array_filter($galleryImages)));

        // Process Multiple Videos (Direct Media Upload & URLs)
        $galleryVideos = [];
        if ($request->filled('gallery_videos')) {
            if (is_string($request->gallery_videos)) {
                $decoded = json_decode($request->gallery_videos, true);
                $galleryVideos = is_array($decoded) ? $decoded : array_filter(array_map('trim', explode("\n", $request->gallery_videos)));
            } elseif (is_array($request->gallery_videos)) {
                $galleryVideos = $request->gallery_videos;
            }
        }

        if ($request->hasFile('video_files')) {
            foreach ($request->file('video_files') as $vFile) {
                if ($vFile && $vFile->isValid()) {
                    $vPath = $vFile->store('services/videos', 'public');
                    $galleryVideos[] = '/storage/' . $vPath;
                }
            }
        }
        if ($request->filled('video_url')) {
            $galleryVideos[] = $request->video_url;
        }
        $data['gallery_videos'] = array_values(array_unique(array_filter($galleryVideos)));
        if (!empty($data['gallery_videos'])) {
            $data['video_url'] = $data['gallery_videos'][0];
        }

        if ($request->filled('benefits') && is_string($request->benefits)) {
            $data['benefits'] = array_filter(array_map('trim', explode("\n", $request->benefits)));
        }

        if ($request->filled('procedure_steps') && is_string($request->procedure_steps)) {
            $data['procedure_steps'] = array_filter(array_map('trim', explode("\n", $request->procedure_steps)));
        }

        $data['slug'] = Str::slug($data['title']);
        $service = Service::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Service created successfully in database',
            'data' => $service,
        ], 201);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $service = is_numeric($id) ? Service::findOrFail($id) : Service::where('slug', $id)->firstOrFail();

        $data = $request->validate([
            'title' => 'required|string|max:200',
            'category' => 'required|string',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'price_starting_at' => 'nullable|string',
            'duration' => 'nullable|string',
            'downtime' => 'nullable|string|max:100',
            'featured_image' => 'nullable|string',
            'icon' => 'nullable|string',
            'status' => 'nullable|string|in:published,draft,archived',
            'is_featured' => 'nullable',
            'benefits' => 'nullable',
            'procedure_steps' => 'nullable',
            'video_url' => 'nullable|string',
            'video_title' => 'nullable|string',
            'gallery_images' => 'nullable',
            'gallery_files.*' => 'nullable|file|max:20480',
            'gallery_videos' => 'nullable',
            'video_files.*' => 'nullable|file|max:102400',
        ]);

        if (empty($data['status'])) {
            $data['status'] = 'published';
        }

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('services', 'public');
            $data['featured_image'] = '/storage/' . $path;
        }

        // Process Gallery / Sub-Images
        $galleryImages = is_array($service->gallery_images) ? $service->gallery_images : [];
        if ($request->has('gallery_images')) {
            if (is_string($request->gallery_images)) {
                $decoded = json_decode($request->gallery_images, true);
                $galleryImages = is_array($decoded) ? $decoded : array_filter(array_map('trim', explode("\n", $request->gallery_images)));
            } elseif (is_array($request->gallery_images)) {
                $galleryImages = $request->gallery_images;
            }
        }

        if ($request->hasFile('gallery_files')) {
            foreach ($request->file('gallery_files') as $gFile) {
                if ($gFile && $gFile->isValid()) {
                    $gPath = $gFile->store('services/gallery', 'public');
                    $galleryImages[] = '/storage/' . $gPath;
                }
            }
        }
        $data['gallery_images'] = array_values(array_unique(array_filter($galleryImages)));

        // Process Multiple Videos (Direct Media Upload & URLs)
        $galleryVideos = is_array($service->gallery_videos) ? $service->gallery_videos : [];
        if ($request->has('gallery_videos')) {
            if (is_string($request->gallery_videos)) {
                $decoded = json_decode($request->gallery_videos, true);
                $galleryVideos = is_array($decoded) ? $decoded : array_filter(array_map('trim', explode("\n", $request->gallery_videos)));
            } elseif (is_array($request->gallery_videos)) {
                $galleryVideos = $request->gallery_videos;
            }
        }

        if ($request->hasFile('video_files')) {
            foreach ($request->file('video_files') as $vFile) {
                if ($vFile && $vFile->isValid()) {
                    $vPath = $vFile->store('services/videos', 'public');
                    $galleryVideos[] = '/storage/' . $vPath;
                }
            }
        }
        if ($request->filled('video_url')) {
            $galleryVideos[] = $request->video_url;
        }
        $data['gallery_videos'] = array_values(array_unique(array_filter($galleryVideos)));
        if (!empty($data['gallery_videos'])) {
            $data['video_url'] = $data['gallery_videos'][0];
        } else {
            $data['video_url'] = null;
        }

        if ($request->filled('benefits') && is_string($request->benefits)) {
            $data['benefits'] = array_filter(array_map('trim', explode("\n", $request->benefits)));
        }

        if ($request->filled('procedure_steps') && is_string($request->procedure_steps)) {
            $data['procedure_steps'] = array_filter(array_map('trim', explode("\n", $request->procedure_steps)));
        }

        $data['slug'] = Str::slug($data['title']);
        $service->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Service updated successfully in database',
            'data' => $service,
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $service = is_numeric($id) ? Service::findOrFail($id) : Service::where('slug', $id)->firstOrFail();
        $service->delete();
        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully',
        ]);
    }
}
