<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\BlogPost;
use App\Models\GalleryItem;
use App\Models\Page;
use App\Models\Service;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\Video;
use App\Services\SettingsService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(protected SettingsService $settingsService)
    {
    }

    public function index(): View
    {
        $settings = $this->settingsService->all();
        $homePage = Page::where('slug', 'home')->with('sections')->first();
        $sections = $homePage ? $homePage->sections->where('status', true)->keyBy('section_type_key') : collect();
        $banner = Banner::where('status', true)->orderBy('sort_order', 'asc')->first();
        
        $doctor = TeamMember::where('is_lead', true)->first() ?? TeamMember::first();
        $featuredServices = Service::published()->featured()->orderBy('sort_order', 'asc')->get();
        if ($featuredServices->isEmpty()) {
            $featuredServices = Service::published()->orderBy('sort_order', 'asc')->limit(6)->get();
        }
        
        $videos = Video::published()->orderBy('sort_order', 'asc')->get();
        $galleryItems = GalleryItem::where('status', true)->orderBy('sort_order', 'asc')->limit(4)->get();
        $testimonials = Testimonial::active()->orderBy('sort_order', 'asc')->get();
        $recentPosts = BlogPost::published()->with('category')->latest('published_at')->limit(3)->get();

        return view('frontend.home', compact(
            'settings',
            'homePage',
            'sections',
            'banner',
            'doctor',
            'featuredServices',
            'videos',
            'galleryItems',
            'testimonials',
            'recentPosts'
        ));
    }
}
