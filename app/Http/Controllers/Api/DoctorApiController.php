<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DoctorApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = TeamMember::query();

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                    ->orWhere('designation', 'like', "%{$s}%")
                    ->orWhere('qualification', 'like', "%{$s}%")
                    ->orWhere('department', 'like', "%{$s}%");
            });
        }

        if ($request->filled('status')) {
            $query->where('status', (bool) $request->status);
        }

        $doctors = $query->orderBy('is_lead', 'desc')
            ->orderBy('sort_order', 'asc')
            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'data' => $doctors->items(),
            'pagination' => [
                'total' => $doctors->total(),
                'per_page' => $doctors->perPage(),
                'current_page' => $doctors->currentPage(),
                'last_page' => $doctors->lastPage(),
            ],
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'designation' => 'required|string|max:255',
            'qualification' => 'required|string|max:500',
            'department' => 'nullable|string|max:255',
            'short_bio' => 'nullable|string',
            'full_bio' => 'nullable|string',
            'experience_years' => 'nullable|integer|min:0|max:60',
            'status' => 'nullable|boolean',
            'is_lead' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'photo_file' => 'nullable|image|max:4096',
            'photo' => 'nullable|string|max:500',
        ]);

        $validated['slug'] = Str::slug($validated['name']);

        if ($request->hasFile('photo_file')) {
            $path = $request->file('photo_file')->store('doctors', 'public');
            $validated['photo'] = '/storage/' . $path;
        }

        $validated['status'] = $request->boolean('status', true);
        $validated['is_lead'] = $request->boolean('is_lead', false);
        $validated['sort_order'] = $request->input('sort_order', 0);

        // If this doctor is set as lead, unset others if needed
        if ($validated['is_lead']) {
            TeamMember::where('is_lead', true)->update(['is_lead' => false]);
        }

        $doctor = TeamMember::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Doctor specialist profile created successfully.',
            'data' => $doctor,
        ], 201);
    }

    public function show(TeamMember $doctor): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $doctor,
        ]);
    }

    public function update(Request $request, TeamMember $doctor): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'designation' => 'sometimes|required|string|max:255',
            'qualification' => 'sometimes|required|string|max:500',
            'department' => 'nullable|string|max:255',
            'short_bio' => 'nullable|string',
            'full_bio' => 'nullable|string',
            'experience_years' => 'nullable|integer|min:0|max:60',
            'status' => 'nullable|boolean',
            'is_lead' => 'nullable|boolean',
            'sort_order' => 'nullable|integer',
            'photo_file' => 'nullable|image|max:4096',
            'photo' => 'nullable|string|max:500',
        ]);

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        if ($request->hasFile('photo_file')) {
            $path = $request->file('photo_file')->store('doctors', 'public');
            $validated['photo'] = '/storage/' . $path;
        }

        if ($request->has('status')) {
            $validated['status'] = $request->boolean('status');
        }

        if ($request->has('is_lead')) {
            $isLead = $request->boolean('is_lead');
            $validated['is_lead'] = $isLead;
            if ($isLead) {
                TeamMember::where('id', '!=', $doctor->id)->where('is_lead', true)->update(['is_lead' => false]);
            }
        }

        $doctor->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Doctor specialist profile updated successfully.',
            'data' => $doctor,
        ]);
    }

    public function destroy(TeamMember $doctor): JsonResponse
    {
        $doctor->delete();

        return response()->json([
            'success' => true,
            'message' => 'Doctor specialist profile deleted successfully.',
        ]);
    }
}
