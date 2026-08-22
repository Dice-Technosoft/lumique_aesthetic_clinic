<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Models\Faq;
use App\Models\FaqCategory;
use App\Models\Gallery;
use App\Models\GalleryItem;
use App\Models\Inquiry;
use App\Models\Lead;
use App\Models\LeadSource;
use App\Models\Media;
use App\Models\Page;
use App\Models\SeoMeta;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\SiteSetting;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\User;
use App\Models\Video;
use App\Services\ReportService;
use App\Services\SettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminWebController extends Controller
{
    public function __construct(
        protected ReportService $reportService,
        protected SettingsService $settingsService
    ) {
    }

    public function dashboard(): View
    {
        $summary = $this->reportService->getDashboardSummary();
        $settings = $this->settingsService->all();
        return view('admin.dashboard', compact('summary', 'settings'));
    }

    public function inquiries(Request $request): View
    {
        $settings = $this->settingsService->all();
        $status = $request->query('status');
        $type = $request->query('type');
        $search = $request->query('search');

        $query = Inquiry::with('assignedUser', 'service')->latest();
        if ($status) {
            $query->where('status', $status);
        }
        if ($type) {
            $query->where('type', $type);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $inquiries = $query->paginate(15)->withQueryString();
        $users = User::all();

        return view('admin.inquiries', compact('settings', 'inquiries', 'users', 'status', 'type', 'search'));
    }

    public function leads(Request $request): View
    {
        $settings = $this->settingsService->all();
        $status = $request->query('status');
        $search = $request->query('search');

        $query = Lead::with('assignedUser', 'leadSource', 'notesList.author', 'followups')->latest();
        if ($status) {
            $query->where('status', $status);
        }
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('service_name', 'like', "%{$search}%");
            });
        }
        $leads = $query->paginate(10)->withQueryString();
        $users = User::all();
        $leadSources = LeadSource::where('status', true)->get();
        $services = Service::all();

        return view('admin.leads', compact('settings', 'leads', 'users', 'leadSources', 'services', 'status', 'search'));
    }

    public function categories(Request $request): View
    {
        $search = $request->query('search');
        $query = ServiceCategory::withCount('services')->orderBy('sort_order', 'asc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $categories = $query->paginate(10)->withQueryString();
        return view('admin.categories', compact('categories', 'search'));
    }

    public function services(Request $request): View
    {
        $search = $request->query('search');
        $category = $request->query('category');
        $query = Service::orderBy('sort_order', 'asc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%");
            });
        }
        if ($category) {
            $query->where('category', $category);
        }

        $services = $query->paginate(10)->withQueryString();
        $categories = ServiceCategory::where('status', true)->orderBy('sort_order', 'asc')->get();
        return view('admin.services', compact('services', 'categories', 'search', 'category'));
    }

    public function videos(Request $request): View
    {
        $search = $request->query('search');
        $query = Video::orderBy('sort_order', 'asc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $videos = $query->paginate(10)->withQueryString();
        return view('admin.videos', compact('videos', 'search'));
    }

    public function gallery(Request $request): View
    {
        $search = $request->query('search');
        $query = GalleryItem::with('gallery')->orderBy('sort_order', 'asc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('treatment_name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        $items = $query->paginate(10)->withQueryString();
        $galleries = Gallery::all();
        $categories = ServiceCategory::where('status', true)->orderBy('sort_order', 'asc')->get();
        return view('admin.gallery', compact('items', 'galleries', 'categories', 'search'));
    }

    public function blogs(Request $request): View
    {
        $search = $request->query('search');
        $query = BlogPost::with(['category', 'tags'])->orderBy('published_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(10)->withQueryString();
        $categories = BlogCategory::orderBy('name', 'asc')->get();
        return view('admin.blogs', compact('posts', 'categories', 'search'));
    }

    public function testimonials(Request $request): View
    {
        $search = $request->query('search');
        $query = Testimonial::orderBy('sort_order', 'asc')->orderBy('id', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('treatment_taken', 'like', "%{$search}%")
                  ->orWhere('designation', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $testimonials = $query->paginate(10)->withQueryString();
        return view('admin.testimonials', compact('testimonials', 'search'));
    }

    public function doctors(Request $request): View
    {
        $search = $request->query('search');
        $query = TeamMember::orderBy('is_lead', 'desc')->orderBy('sort_order', 'asc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('designation', 'like', "%{$search}%")
                  ->orWhere('qualification', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        $doctors = $query->paginate(10)->withQueryString();
        return view('admin.doctors', compact('doctors', 'search'));
    }

    public function faqs(Request $request): View
    {
        $search = $request->query('search');
        $query = Faq::with(['category', 'service'])->orderBy('sort_order', 'asc')->latest('id');

        if (!empty($search)) {
            $query->where('question', 'like', "%{$search}%")
                  ->orWhere('answer', 'like', "%{$search}%");
        }

        $faqs = $query->paginate(10)->withQueryString();
        $faqCategories = FaqCategory::where('status', true)->get();
        $services = Service::published()->get();

        return view('admin.faqs', compact('faqs', 'faqCategories', 'services', 'search'));
    }

    public function aboutPage(): View
    {
        $settings = $this->settingsService->all();
        return view('admin.about', compact('settings'));
    }

    public function settings(): View
    {
        $settings = $this->settingsService->all();
        $groupedSettings = SiteSetting::all()->groupBy('group');
        return view('admin.settings', compact('settings', 'groupedSettings'));
    }

    public function profile(): View
    {
        $user = Auth::user() ?? User::first();
        return view('admin.profile', compact('user'));
    }

    public function updateProfile(Request $request): JsonResponse|RedirectResponse
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

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Administrator profile updated successfully in database.',
                'data' => $user,
            ]);
        }

        return back()->with('success', 'Administrator profile updated successfully in database.');
    }

    public function seo(): View
    {
        $settings = $this->settingsService->all();
        $seoMetas = SeoMeta::all()->keyBy('path');

        $staticPages = [
            ['name' => 'Home Page', 'path' => '/', 'badge' => 'Core Page'],
            ['name' => 'About Clinic & Doctor', 'path' => '/about', 'badge' => 'Core Page'],
            ['name' => 'Treatments Hub', 'path' => '/services', 'badge' => 'Services Hub'],
            ['name' => 'Clinical Video Library', 'path' => '/videos', 'badge' => 'Media'],
            ['name' => 'Results Gallery', 'path' => '/gallery', 'badge' => 'Media'],
            ['name' => 'Educational Blog Journal', 'path' => '/blog', 'badge' => 'Articles Hub'],
            ['name' => 'Contact', 'path' => '/contact', 'badge' => 'Contact'],
        ];

        $services = Service::published()->get(['id', 'title', 'slug', 'category']);
        $blogPosts = BlogPost::published()->get(['id', 'title', 'slug']);

        return view('admin.seo', compact('settings', 'seoMetas', 'staticPages', 'services', 'blogPosts'));
    }

    public function getSeoMeta(Request $request): JsonResponse
    {
        $path = $request->query('path', '/');
        $meta = SeoMeta::where('path', $path)->first();

        if (!$meta) {
            $settings = $this->settingsService->getAllGrouped()['seo'] ?? [];
            $general = $this->settingsService->getAllGrouped()['general'] ?? [];
            $settings = array_merge($general, $settings);

            $pageNames = [
                '/' => 'Home Page',
                '/about' => 'About Us',
                '/services' => 'Treatments Hub',
                '/videos' => 'Video Demonstrations',
                '/gallery' => 'Results Gallery',
                '/blog' => 'Dermatology Journal',
                '/contact' => 'Contact',
            ];
            $name = $pageNames[$path] ?? 'Page';

            $meta = SeoMeta::create([
                'path' => $path,
                'meta_title' => ($settings['site_name'] ?? 'Lumique Aesthetic Clinic') . ' | ' . $name,
                'meta_description' => $settings['default_meta_description'] ?? 'Bespoke clinical dermatology, laser treatments, and hair restoration in Mumbai.',
                'meta_keywords' => $settings['default_meta_keywords'] ?? 'dermatologist mumbai, laser clinic, skin treatments',
                'canonical_url' => url($path),
                'og_title' => ($settings['site_name'] ?? 'Lumique Aesthetic Clinic') . ' | ' . $name,
                'og_description' => $settings['default_meta_description'] ?? 'Bespoke clinical dermatology, laser treatments, and hair restoration in Mumbai.',
                'og_image' => $settings['logo_url'] ?? '/images/logo.jpeg',
                'robots' => 'index, follow',
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => $meta,
        ]);
    }

    public function saveSeoMeta(Request $request): JsonResponse
    {
        $request->validate([
            'path' => 'required|string',
            'meta_title' => 'nullable|string|max:200',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:300',
            'canonical_url' => 'nullable|url',
            'og_title' => 'nullable|string|max:200',
            'og_description' => 'nullable|string|max:500',
            'robots' => 'nullable|string',
        ]);

        $data = [
            'meta_title' => $request->meta_title,
            'meta_description' => $request->meta_description,
            'meta_keywords' => $request->meta_keywords,
            'canonical_url' => $request->canonical_url,
            'og_title' => $request->og_title ?: $request->meta_title,
            'og_description' => $request->og_description ?: $request->meta_description,
            'robots' => $request->robots ?: 'index, follow',
        ];

        if ($request->hasFile('og_image_file')) {
            $path = $request->file('og_image_file')->store('seo', 'public');
            $data['og_image'] = '/storage/' . $path;
        } elseif ($request->filled('og_image')) {
            $data['og_image'] = $request->og_image;
        }

        if ($request->filled('schema_json')) {
            $decoded = json_decode($request->schema_json, true);
            $data['schema_json'] = $decoded ?: null;
        }

        $meta = SeoMeta::updateOrCreate(['path' => $request->path], $data);

        return response()->json([
            'success' => true,
            'message' => 'SEO Metadata saved for ' . $request->path,
            'data' => $meta,
        ]);
    }

    public function saveGlobalSeo(Request $request): JsonResponse
    {
        $keys = [
            'default_meta_title' => $request->default_meta_title,
            'default_meta_description' => $request->default_meta_description,
            'default_meta_keywords' => $request->default_meta_keywords,
            'google_analytics_id' => $request->google_analytics_id,
            'google_site_verification' => $request->google_site_verification,
        ];

        if ($request->hasFile('default_og_image_file')) {
            $path = $request->file('default_og_image_file')->store('seo', 'public');
            $keys['default_og_image'] = '/storage/' . $path;
        }

        $this->settingsService->updateBulk(array_filter($keys, fn($v) => !is_null($v)));

        return response()->json([
            'success' => true,
            'message' => 'Global SEO defaults saved successfully',
            'data' => $this->settingsService->all(),
        ]);
    }
}
