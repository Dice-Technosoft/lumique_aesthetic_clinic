<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\SiteSetting;
use App\Services\SettingsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SettingApiController extends Controller
{
    public function __construct(
        protected SettingsService $settingsService
    ) {
    }

    public function publicSettings(): JsonResponse
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

    public function index(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => SiteSetting::all()->groupBy('group'),
        ]);
    }

    public function update(Request $request): JsonResponse
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
}
