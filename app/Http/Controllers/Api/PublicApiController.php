<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\Gallery;
use App\Models\Menu;
use App\Models\Page;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\Video;
use App\Services\InquiryService;
use App\Services\SettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PublicApiController extends Controller
{
    public function __construct(
        protected SettingsService $settingsService,
        protected InquiryService $inquiryService
    ) {
    }

    public function settings(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Settings fetched successfully',
            'data' => $this->settingsService->getPublic(),
        ]);
    }

    public function navigation(string $location = 'header'): JsonResponse
    {
        $menu = Menu::with(['items.children'])->where('location', $location)->where('status', true)->first();
        return response()->json([
            'success' => true,
            'data' => $menu ? $menu->items : [],
        ]);
    }

    public function services(Request $request): JsonResponse
    {
        $query = Service::published()->orderBy('sort_order', 'asc');
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

    public function serviceDetail(string $slug): JsonResponse
    {
        $service = Service::published()->where('slug', $slug)->with('seoMeta')->firstOrFail();
        return response()->json([
            'success' => true,
            'data' => $service,
        ]);
    }

    public function team(): JsonResponse
    {
        $team = TeamMember::active()->orderBy('sort_order', 'asc')->get();
        return response()->json([
            'success' => true,
            'data' => $team,
        ]);
    }

    public function videos(Request $request): JsonResponse
    {
        $query = Video::published()->orderBy('sort_order', 'asc');
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }
        return response()->json([
            'success' => true,
            'data' => $query->get(),
        ]);
    }

    public function gallery(Request $request): JsonResponse
    {
        $gallery = Gallery::where('status', true)->with('items')->first();
        return response()->json([
            'success' => true,
            'data' => $gallery ? $gallery->items : [],
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

    public function blogPosts(Request $request): JsonResponse
    {
        $query = BlogPost::published()->with(['category', 'tags', 'author:id,name'])->latest('published_at');
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate($request->integer('per_page', 9));

        return response()->json([
            'success' => true,
            'data' => $posts->items(),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
                'last_page' => $posts->lastPage(),
            ],
        ]);
    }

    public function blogDetail(string $slug): JsonResponse
    {
        $post = BlogPost::published()->where('slug', $slug)->with(['category', 'tags', 'author:id,name', 'seoMeta'])->firstOrFail();
        $post->increment('view_count');

        return response()->json([
            'success' => true,
            'data' => $post,
        ]);
    }

    public function submitInquiry(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:30',
            'subject' => 'nullable|string|max:255',
            'message' => 'nullable|string|max:3000',
            'service_id' => 'nullable|exists:services,id',
            'service_name' => 'nullable|string|max:150',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['type'] = 'contact';
        $inquiry = $this->inquiryService->createInquiry($data, $request->ip(), $request->userAgent());

        return response()->json([
            'success' => true,
            'message' => 'Thank you for reaching out! We have received your inquiry and will contact you shortly.',
            'data' => [
                'id' => $inquiry->id,
                'name' => $inquiry->name,
                'email' => $inquiry->email,
            ],
        ], 201);
    }

    public function bookAppointment(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:30',
            'service_id' => 'nullable|exists:services,id',
            'service_name' => 'nullable|string|max:150',
            'preferred_date' => 'nullable|date',
            'preferred_time' => 'nullable|string|max:50',
            'message' => 'nullable|string|max:3000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['type'] = 'appointment';
        $inquiry = $this->inquiryService->createInquiry($data, $request->ip(), $request->userAgent());

        return response()->json([
            'success' => true,
            'message' => 'Your appointment request has been submitted successfully. Our concierge will contact you shortly to confirm.',
            'data' => [
                'id' => $inquiry->id,
                'name' => $inquiry->name,
                'email' => $inquiry->email,
            ],
        ], 201);
    }
}
