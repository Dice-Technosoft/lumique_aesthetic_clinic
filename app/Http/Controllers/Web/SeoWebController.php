<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\SeoService;
use Illuminate\Http\Response;

class SeoWebController extends Controller
{
    public function __construct(protected SeoService $seoService)
    {
    }

    public function sitemap(): Response
    {
        $xml = $this->seoService->generateSitemapXml();
        return response($xml, 200)->header('Content-Type', 'text/xml');
    }

    public function robots(): Response
    {
        $robots = $this->seoService->generateRobotsTxt();
        return response($robots, 200)->header('Content-Type', 'text/plain');
    }
}
