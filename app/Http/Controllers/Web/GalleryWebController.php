<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\GalleryItem;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GalleryWebController extends Controller
{
    public function __construct(protected SettingsService $settingsService)
    {
    }

    public function index(Request $request): View
    {
        $settings = $this->settingsService->all();
        $galleryItems = GalleryItem::where('status', true)->orderBy('sort_order', 'asc')->get();
        $categories = \App\Models\ServiceCategory::where('status', true)->orderBy('sort_order', 'asc')->get();

        return view('frontend.gallery', compact('settings', 'galleryItems', 'categories'));
    }
}
