<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Services\SettingsService;
use Illuminate\View\View;

class ContactWebController extends Controller
{
    public function __construct(protected SettingsService $settingsService)
    {
    }

    public function index(): View
    {
        $settings = $this->settingsService->all();
        $services = Service::published()->orderBy('title', 'asc')->get();

        return view('frontend.contact', compact('settings', 'services'));
    }
}
