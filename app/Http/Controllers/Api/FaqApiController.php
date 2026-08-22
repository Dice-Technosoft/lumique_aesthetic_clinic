<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FaqApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Faq::with(['category', 'service'])->orderBy('sort_order', 'asc');
        
        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->has('service_id')) {
            $query->where('service_id', $request->service_id);
        }
        if ($request->has('status')) {
            $query->where('status', $request->boolean('status'));
        }

        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'question' => 'required|string|max:300',
            'answer' => 'required|string',
            'category_id' => 'nullable|exists:faq_categories,id',
            'service_id' => 'nullable|exists:services,id',
            'status' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        if (!isset($data['status'])) {
            $data['status'] = true;
        }
        if (!isset($data['sort_order'])) {
            $data['sort_order'] = 0;
        }

        $faq = Faq::create($data);

        return response()->json([
            'success' => true,
            'message' => 'FAQ question created successfully',
            'data' => $faq,
        ], 201);
    }

    public function show(Faq $faq): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $faq->load(['category', 'service']),
        ]);
    }

    public function update(Request $request, Faq $faq): JsonResponse
    {
        $data = $request->validate([
            'question' => 'required|string|max:300',
            'answer' => 'required|string',
            'category_id' => 'nullable|exists:faq_categories,id',
            'service_id' => 'nullable|exists:services,id',
            'status' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);

        $faq->update($data);

        return response()->json([
            'success' => true,
            'message' => 'FAQ question updated successfully',
            'data' => $faq,
        ]);
    }

    public function destroy(Faq $faq): JsonResponse
    {
        $faq->delete();

        return response()->json([
            'success' => true,
            'message' => 'FAQ question deleted successfully',
        ]);
    }
}
