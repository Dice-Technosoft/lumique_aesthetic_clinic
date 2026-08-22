<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use App\Models\BlogPost;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BlogWebController extends Controller
{
    public function __construct(protected SettingsService $settingsService)
    {
    }

    public function index(Request $request): View
    {
        $settings = $this->settingsService->all();
        $categories = BlogCategory::where('status', true)->get();
        $selectedCategory = $request->query('category');
        $search = $request->query('search');

        $query = BlogPost::published()->with(['category', 'tags'])->latest('published_at');

        if ($selectedCategory) {
            $query->whereHas('category', fn($q) => $q->where('slug', $selectedCategory));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(6)->withQueryString();

        return view('frontend.blog', compact('settings', 'categories', 'selectedCategory', 'search', 'posts'));
    }

    public function show(string $slug): View
    {
        $settings = $this->settingsService->all();
        $post = BlogPost::published()->where('slug', $slug)->with(['category', 'tags', 'author'])->firstOrFail();
        $post->increment('view_count');

        $recentPosts = BlogPost::published()->where('id', '!=', $post->id)->latest('published_at')->limit(3)->get();
        $categories = BlogCategory::where('status', true)->get();

        return view('frontend.blog_detail', compact('settings', 'post', 'recentPosts', 'categories'));
    }
}
