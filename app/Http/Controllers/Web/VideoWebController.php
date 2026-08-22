<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Video;
use App\Services\SettingsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VideoWebController extends Controller
{
    public function __construct(protected SettingsService $settingsService)
    {
    }

    public function index(Request $request): View
    {
        $settings = $this->settingsService->all();
        $selectedCategory = $request->query('category', 'all');

        $query = Video::published()->orderBy('sort_order', 'asc');
        if ($selectedCategory !== 'all') {
            $query->where('category', $selectedCategory);
        }
        $videos = $query->get();

        return view('frontend.videos', compact('settings', 'videos', 'selectedCategory'));
    }
}
