<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceCategory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = ServiceCategory::orderBy('sort_order', 'asc');
        if ($request->has('search') && !empty($request->search)) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('slug', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }
        if ($request->boolean('active_only')) {
            $query->where('status', true);
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'slug' => 'nullable|string|max:150|unique:service_categories,slug',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'image' => 'nullable|string',
            'status' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        } else {
            $data['slug'] = Str::slug($data['slug']);
        }

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('categories', 'public');
            $data['image'] = '/storage/' . $path;
        }

        $category = ServiceCategory::create($data);

        // Keep BlogCategory in sync
        \App\Models\BlogCategory::updateOrCreate(
            ['slug' => $category->slug],
            ['name' => $category->name, 'description' => $category->description]
        );

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully in database',
            'data' => $category,
        ], 201);
    }

    public function update(Request $request, ServiceCategory $category): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'slug' => 'nullable|string|max:150|unique:service_categories,slug,' . $category->id,
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:50',
            'image' => 'nullable|string',
            'status' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if (!empty($data['slug'])) {
            $data['slug'] = Str::slug($data['slug']);
        }

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('categories', 'public');
            $data['image'] = '/storage/' . $path;
        }

        $oldSlug = $category->slug;
        $category->update($data);

        // Keep BlogCategory in sync
        \App\Models\BlogCategory::updateOrCreate(
            ['slug' => $category->slug],
            ['name' => $category->name, 'description' => $category->description]
        );

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully in database',
            'data' => $category,
        ]);
    }

    public function destroy(ServiceCategory $category): JsonResponse
    {
        \App\Models\BlogCategory::where('slug', $category->slug)->delete();
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully',
        ]);
    }
}
