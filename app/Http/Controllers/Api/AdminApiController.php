<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\Gallery;
use App\Models\GalleryItem;
use App\Models\Inquiry;
use App\Models\Lead;
use App\Models\LeadFollowUp;
use App\Models\Media;
use App\Models\Page;
use App\Models\Service;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\Video;
use App\Services\InquiryService;
use App\Services\LeadService;
use App\Services\MediaService;
use App\Services\ReportService;
use App\Services\SettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AdminApiController extends Controller
{
    public function __construct(
        protected ReportService $reportService,
        protected SettingsService $settingsService,
        protected InquiryService $inquiryService,
        protected LeadService $leadService,
        protected MediaService $mediaService
    ) {
    }

    public function dashboard(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => $this->reportService->getDashboardSummary(),
        ]);
    }

    // --- INQUIRIES & CRM ---
    public function inquiries(Request $request): JsonResponse
    {
        $query = Inquiry::with('assignedUser:id,name', 'service:id,title')->latest();
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('type')) {
            $query->where('type', $request->type);
        }
        if ($request->has('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%");
            });
        }

        $inquiries = $query->paginate($request->integer('per_page', 15));
        return response()->json([
            'success' => true,
            'data' => $inquiries->items(),
            'meta' => [
                'current_page' => $inquiries->currentPage(),
                'per_page' => $inquiries->perPage(),
                'total' => $inquiries->total(),
            ],
        ]);
    }

    public function updateInquiryStatus(Request $request, Inquiry $inquiry): JsonResponse
    {
        $request->validate([
            'status' => 'required|string|in:new,contacted,in_progress,converted,closed,spam',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $updated = $this->inquiryService->updateStatus($inquiry, $request->status, $request->assigned_to);

        ActivityLog::create([
            'user_id' => $request->user()?->id,
            'module' => 'inquiries',
            'action' => 'status_updated',
            'record_id' => $inquiry->id,
            'new_values' => ['status' => $request->status],
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Inquiry updated successfully',
            'data' => $updated,
        ]);
    }

    public function leads(Request $request): JsonResponse
    {
        $query = Lead::with('assignedUser:id,name', 'leadSource:id,name', 'notesList.author:id,name', 'followups')->latest();
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        $leads = $query->paginate($request->integer('per_page', 15));
        return response()->json([
            'success' => true,
            'data' => $leads->items(),
            'meta' => [
                'current_page' => $leads->currentPage(),
                'per_page' => $leads->perPage(),
                'total' => $leads->total(),
            ],
        ]);
    }

    public function storeLead(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:30',
            'service_name' => 'nullable|string',
            'service_id' => 'nullable|exists:services,id',
            'lead_source_id' => 'nullable|exists:lead_sources,id',
            'status' => 'nullable|string',
            'priority' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'estimated_value' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        $lead = $this->leadService->createLead($data, $request->user());
        return response()->json([
            'success' => true,
            'message' => 'Lead created successfully',
            'data' => $lead,
        ], 201);
    }

    public function updateLead(Request $request, Lead $lead): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:30',
            'service_name' => 'nullable|string',
            'status' => 'nullable|string',
            'estimated_value' => 'nullable|numeric',
        ]);

        $lead->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Lead updated successfully in database',
            'data' => $lead,
        ]);
    }

    public function deleteLead(Lead $lead): JsonResponse
    {
        $lead->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lead deleted successfully from database',
        ]);
    }

    public function addLeadNote(Request $request, Lead $lead): JsonResponse
    {
        $request->validate(['note' => 'required|string|max:3000']);
        $note = $this->leadService->addNote($lead, $request->note, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Note added successfully',
            'data' => $note,
        ]);
    }

    public function scheduleFollowUp(Request $request, Lead $lead): JsonResponse
    {
        $data = $request->validate([
            'follow_up_date' => 'required|date',
            'follow_up_time' => 'nullable|string',
            'note' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $followUp = $this->leadService->scheduleFollowUp($lead, $data, $request->user());
        return response()->json([
            'success' => true,
            'message' => 'Follow-up scheduled successfully',
            'data' => $followUp,
        ]);
    }

    // --- SERVICES MANAGEMENT ---
    public function services(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Service::orderBy('sort_order', 'asc')->get(),
        ]);
    }

    public function storeService(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'category' => 'required|string',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'price_starting_at' => 'nullable|string',
            'duration' => 'nullable|string',
            'featured_image' => 'nullable|string',
            'icon' => 'nullable|string',
            'status' => 'required|string|in:published,draft,archived',
            'is_featured' => 'boolean',
            'benefits' => 'nullable',
            'procedure_steps' => 'nullable',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('services', 'public');
            $data['featured_image'] = '/storage/' . $path;
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

    public function updateService(Request $request, Service $service): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:200',
            'category' => 'required|string',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'price_starting_at' => 'nullable|string',
            'duration' => 'nullable|string',
            'featured_image' => 'nullable|string',
            'icon' => 'nullable|string',
            'status' => 'required|string|in:published,draft,archived',
            'is_featured' => 'boolean',
            'benefits' => 'nullable',
            'procedure_steps' => 'nullable',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('services', 'public');
            $data['featured_image'] = '/storage/' . $path;
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

    public function deleteService(Service $service): JsonResponse
    {
        $service->delete();
        return response()->json([
            'success' => true,
            'message' => 'Service deleted successfully',
        ]);
    }

    // --- VIDEOS CRUD ---
    public function videos(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Video::orderBy('sort_order', 'asc')->get(),
        ]);
    }

    public function storeVideo(Request $request): JsonResponse
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

        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/', $data['youtube_url'], $matches)) {
            $data['youtube_video_id'] = $matches[1];
        }

        if ($request->hasFile('thumbnail_file')) {
            $path = $request->file('thumbnail_file')->store('videos', 'public');
            $data['thumbnail'] = '/storage/' . $path;
        }

        $data['slug'] = Str::slug($data['title']);
        $video = Video::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Clinical video created successfully',
            'data' => $video,
        ], 201);
    }

    public function updateVideo(Request $request, Video $video): JsonResponse
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

        if (preg_match('/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))([\w-]{11})/', $data['youtube_url'], $matches)) {
            $data['youtube_video_id'] = $matches[1];
        }

        if ($request->hasFile('thumbnail_file')) {
            $path = $request->file('thumbnail_file')->store('videos', 'public');
            $data['thumbnail'] = '/storage/' . $path;
        }

        $data['slug'] = Str::slug($data['title']);
        $video->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Clinical video updated successfully',
            'data' => $video,
        ]);
    }

    public function deleteVideo(Video $video): JsonResponse
    {
        $video->delete();
        return response()->json([
            'success' => true,
            'message' => 'Video deleted successfully',
        ]);
    }

    // --- GALLERY CRUD ---
    public function galleryItems(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => GalleryItem::with('gallery')->orderBy('sort_order', 'asc')->get(),
        ]);
    }

    public function storeGalleryItem(Request $request): JsonResponse
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

    public function updateGalleryItem(Request $request, $item): JsonResponse
    {
        $gallery = $item instanceof GalleryItem ? $item : GalleryItem::findOrFail($item);

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
            'data' => $gallery->fresh(),
        ]);
    }

    public function deleteGalleryItem($item): JsonResponse
    {
        $gallery = $item instanceof GalleryItem ? $item : GalleryItem::findOrFail($item);
        $gallery->delete();
        return response()->json([
            'success' => true,
            'message' => 'Gallery item deleted successfully',
        ]);
    }

    // --- BLOGS CRUD ---
    public function blogs(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => BlogPost::with(['category', 'tags'])->orderBy('published_at', 'desc')->get(),
        ]);
    }

    public function storeBlog(Request $request): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:250',
            'category_id' => 'required|exists:blog_categories,id',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|string',
            'status' => 'required|string|in:published,draft,archived',
            'read_time_minutes' => 'nullable|integer',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('blogs', 'public');
            $data['featured_image'] = '/storage/' . $path;
        }

        $data['author_id'] = User::first()->id ?? 1;
        $data['slug'] = Str::slug($data['title']);
        $data['published_at'] = now();
        $data['view_count'] = 1;

        $post = BlogPost::create($data);

        // Sync SEO
        SeoMeta::updateOrCreate(
            ['path' => '/blog/' . $post->slug],
            [
                'meta_title' => $post->title . ' | Lumique Journal',
                'meta_description' => $post->excerpt,
                'canonical_url' => url('/blog/' . $post->slug),
                'og_title' => $post->title,
                'og_description' => $post->excerpt,
                'og_image' => $post->featured_image,
                'robots' => 'index, follow',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Article published successfully to database',
            'data' => $post,
        ], 201);
    }

    public function updateBlog(Request $request, BlogPost $blog): JsonResponse
    {
        $data = $request->validate([
            'title' => 'required|string|max:250',
            'category_id' => 'required|exists:blog_categories,id',
            'excerpt' => 'required|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|string',
            'status' => 'required|string|in:published,draft,archived',
            'read_time_minutes' => 'nullable|integer',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('blogs', 'public');
            $data['featured_image'] = '/storage/' . $path;
        }

        $data['slug'] = Str::slug($data['title']);
        $blog->update($data);

        // Sync SEO
        SeoMeta::updateOrCreate(
            ['path' => '/blog/' . $blog->slug],
            [
                'meta_title' => $blog->title . ' | Lumique Journal',
                'meta_description' => $blog->excerpt,
                'canonical_url' => url('/blog/' . $blog->slug),
                'og_title' => $blog->title,
                'og_description' => $blog->excerpt,
                'og_image' => $blog->featured_image,
                'robots' => 'index, follow',
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Article updated successfully in database',
            'data' => $blog,
        ]);
    }

    public function deleteBlog(BlogPost $blog): JsonResponse
    {
        $blog->delete();
        return response()->json([
            'success' => true,
            'message' => 'Article deleted successfully',
        ]);
    }

    // --- SITE SETTINGS ---
    public function settings(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => SiteSetting::all()->groupBy('group'),
        ]);
    }

    public function updateSettings(Request $request): JsonResponse
    {
        $allData = $request->except(['_token', 'files']);

        // Handle File Uploads (Dynamic Logo & Favicon)
        if ($request->hasFile('file_logo_url') || $request->hasFile('logo')) {
            $logoFile = $request->file('file_logo_url') ?? $request->file('logo');
            $path = $logoFile->store('branding', 'public');
            $allData['logo_url'] = '/storage/' . $path;
        }

        if ($request->hasFile('file_favicon_url') || $request->hasFile('favicon')) {
            $faviconFile = $request->file('file_favicon_url') ?? $request->file('favicon');
            $path = $faviconFile->store('branding', 'public');
            $allData['favicon_url'] = '/storage/' . $path;
        }

        // Process any other generic file uploads with prefix file_
        foreach ($request->allFiles() as $fileKey => $uploadedFile) {
            if (str_starts_with($fileKey, 'file_')) {
                $actualKey = substr($fileKey, 5);
                $path = $uploadedFile->store('branding', 'public');
                $allData[$actualKey] = '/storage/' . $path;
            }
        }

        // Filter out temporary file keys
        $cleanSettings = [];
        foreach ($allData as $k => $v) {
            if (!str_starts_with($k, 'file_')) {
                $cleanSettings[$k] = $v;
            }
        }

        $this->settingsService->updateBulk($cleanSettings);

        return response()->json([
            'success' => true,
            'message' => 'Site settings and uploaded brand assets saved successfully',
            'data' => $this->settingsService->all(),
        ]);
    }

    // --- ADMIN PROFILE ---
    public function profile(): JsonResponse
    {
        $user = Auth::user() ?? User::first();
        return response()->json([
            'success' => true,
            'data' => $user,
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = Auth::user() ?? User::first();

        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:30',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,webp,gif|max:3072',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $filename = 'admin_' . time() . '.' . $file->getClientOriginalExtension();
            $destPath = public_path('uploads/avatars');
            if (!file_exists($destPath)) {
                mkdir($destPath, 0755, true);
            }
            $file->move($destPath, $filename);
            $user->avatar_url = '/uploads/avatars/' . $filename;
        }

        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'] ?? $user->phone;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Administrator profile updated successfully in database.',
            'data' => $user,
        ]);
    }

    // --- MEDIA UPLOAD ---
    public function mediaList(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => Media::latest()->paginate(24),
        ]);
    }

    public function uploadMedia(Request $request): JsonResponse
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
