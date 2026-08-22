<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Service;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ServiceWebController extends Controller
{
    public function __construct(protected SettingsService $settingsService)
    {
    }

    public function index(Request $request): View
    {
        $settings = $this->settingsService->all();
        $selectedCategory = $request->query('category', 'all');

        $query = Service::published()->orderBy('sort_order', 'asc');
        if ($selectedCategory !== 'all') {
            $query->where('category', $selectedCategory);
        }
        $services = $query->get();
        $categories = \App\Models\ServiceCategory::where('status', true)->orderBy('sort_order', 'asc')->get();
        $activeCategorySlugs = Service::published()->pluck('category')->unique()->filter()->toArray();

        return view('frontend.services', compact('settings', 'services', 'categories', 'selectedCategory', 'activeCategorySlugs'));
    }

    public function show(string $slug): View
    {
        $settings = $this->settingsService->all();
        $service = Service::published()->where('slug', $slug)->firstOrFail();
        $relatedServices = Service::published()->where('id', '!=', $service->id)->where('category', $service->category)->limit(3)->get();
        if ($relatedServices->isEmpty()) {
            $relatedServices = Service::published()->where('id', '!=', $service->id)->limit(3)->get();
        }
        $faqs = Faq::where('service_id', $service->id)->orWhere('status', true)->limit(5)->get();

        return view('frontend.service_detail', compact('settings', 'service', 'relatedServices', 'faqs'));
    }
}
