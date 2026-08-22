<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Services\SettingsService;
use Illuminate\View\View;

class AboutController extends Controller
{
    public function __construct(protected SettingsService $settingsService)
    {
    }

    public function index(): View
    {
        $settings = $this->settingsService->all();
        $doctor = TeamMember::where('is_lead', true)->first();
        $team = TeamMember::active()->orderBy('sort_order', 'asc')->get();
        $testimonials = Testimonial::active()->orderBy('sort_order', 'asc')->get();

        return view('frontend.about', compact('settings', 'doctor', 'team', 'testimonials'));
    }
}
