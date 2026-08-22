<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\GalleryItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GalleryApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => GalleryItem::with('gallery')->orderBy('sort_order', 'asc')->get(),
        ]);
    }

    public function publicGallery(): JsonResponse
    {
        $gallery = Gallery::where('status', true)->with('items')->first();
        return response()->json([
            'success' => true,
            'data' => $gallery ? $gallery->items : [],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'category' => 'required|string',
            'treatment_name' => 'nullable|string',
            'image_before' => 'nullable|string',
            'image_after' => 'nullable|string',
            'image' => 'nullable|string',
            'alt_text' => 'nullable|string',
            'status' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if ($request->hasFile('before_file')) {
            $path = $request->file('before_file')->store('gallery', 'public');
            $data['image_before'] = '/storage/' . $path;
        }

        if ($request->hasFile('after_file')) {
            $path = $request->file('after_file')->store('gallery', 'public');
            $data['image_after'] = '/storage/' . $path;
        }

        $data['gallery_id'] = Gallery::first()->id ?? 1;
        $item = GalleryItem::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Gallery item added successfully',
            'data' => $item,
        ], 201);
    }

    public function update(Request $request, GalleryItem $gallery): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'category' => 'required|string',
            'treatment_name' => 'nullable|string',
            'image_before' => 'nullable|string',
            'image_after' => 'nullable|string',
            'image' => 'nullable|string',
            'alt_text' => 'nullable|string',
            'status' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if ($request->hasFile('before_file')) {
            $path = $request->file('before_file')->store('gallery', 'public');
            $data['image_before'] = '/storage/' . $path;
        }

        if ($request->hasFile('after_file')) {
            $path = $request->file('after_file')->store('gallery', 'public');
            $data['image_after'] = '/storage/' . $path;
        }

        $gallery->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Gallery item updated successfully',
            'data' => $gallery,
        ]);
    }

    public function destroy(GalleryItem $gallery): JsonResponse
    {
        $gallery->delete();
        return response()->json([
            'success' => true,
            'message' => 'Gallery item deleted successfully',
        ]);
    }
}
