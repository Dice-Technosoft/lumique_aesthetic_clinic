<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonialApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Testimonial::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('treatment_taken', 'like', "%{$s}%")
                    ->orWhere('designation', 'like', "%{$s}%")
                    ->orWhere('content', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', (bool) $request->status);
        }

        $testimonials = $query->orderBy('sort_order', 'asc')
            ->orderBy('id', 'desc')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $testimonials->items(),
            'pagination' => [
                'total' => $testimonials->total(),
                'per_page' => $testimonials->perPage(),
                'current_page' => $testimonials->currentPage(),
                'last_page' => $testimonials->lastPage(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'treatment_taken' => 'nullable|string|max:255',
            'rating' => 'required|integer|min:1|max:5',
            'content' => 'required|string',
            'source' => 'nullable|string|max:255',
            'status' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'photo_file' => 'nullable|image|max:3072',
            'photo' => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('photo_file')) {
            $path = $request->file('photo_file')->store('testimonials', 'public');
            $validated['photo'] = '/storage/' . $path;
        }

        $validated['status'] = $request->boolean('status', true);
        $validated['is_featured'] = $request->boolean('is_featured', false);
        $validated['sort_order'] = $request->input('sort_order', 0);

        $testimonial = Testimonial::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Patient story created successfully.',
            'data' => $testimonial,
        ], 201);
    }

    public function show(Testimonial $testimonial): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $testimonial,
        ]);
    }

    public function update(Request $request, Testimonial $testimonial): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'designation' => 'nullable|string|max:255',
            'treatment_taken' => 'nullable|string|max:255',
            'rating' => 'sometimes|required|integer|min:1|max:5',
            'content' => 'sometimes|required|string',
            'source' => 'nullable|string|max:255',
            'status' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'photo_file' => 'nullable|image|max:3072',
            'photo' => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('photo_file')) {
            $path = $request->file('photo_file')->store('testimonials', 'public');
            $validated['photo'] = '/storage/' . $path;
        }

        if ($request->has('status')) {
            $validated['status'] = $request->boolean('status');
        }
        if ($request->has('is_featured')) {
            $validated['is_featured'] = $request->boolean('is_featured');
        }

        $testimonial->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Patient story updated successfully.',
            'data' => $testimonial,
        ]);
    }

    public function destroy(Testimonial $testimonial): JsonResponse
    {
        $testimonial->delete();

        return response()->json([
            'success' => true,
            'message' => 'Patient story deleted successfully.',
        ]);
    }
}
