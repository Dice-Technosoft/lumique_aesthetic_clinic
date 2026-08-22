<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FaqCategory;
use App\Models\TeamMember;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;

class PublicContentApiController extends Controller
{
    public function team(): JsonResponse
    {
        $team = TeamMember::active()->orderBy('sort_order', 'asc')->get();
        return response()->json([
            'success' => true,
            'data' => $team,
        ]);
    }

    public function testimonials(): JsonResponse
    {
        $testimonials = Testimonial::active()->orderBy('sort_order', 'asc')->get();
        return response()->json([
            'success' => true,
            'data' => $testimonials,
        ]);
    }

    public function faqs(): JsonResponse
    {
        $categories = FaqCategory::where('status', true)->with(['faqs' => function ($q) {
            $q->where('status', true)->orderBy('sort_order', 'asc');
        }])->orderBy('sort_order', 'asc')->get();

        return response()->json([
            'success' => true,
            'data' => $categories,
        ]);
    }
}
