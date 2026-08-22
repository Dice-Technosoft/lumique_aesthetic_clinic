<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Page;
use App\Models\SeoMeta;
use App\Models\Service;
use App\Models\SiteSetting;

class SeoService
{
    public function getMetaForUrl(string $path): ?SeoMeta
    {
        return SeoMeta::where('path', $path)->first();
    }

    public function generateSitemapXml(): string
    {
        $urls = [];

        // Static core routes
        $urls[] = ['loc' => url('/'), 'lastmod' => now()->toIso8601String(), 'changefreq' => 'daily', 'priority' => '1.0'];
        $urls[] = ['loc' => url('/about'), 'lastmod' => now()->toIso8601String(), 'changefreq' => 'weekly', 'priority' => '0.8'];
        $urls[] = ['loc' => url('/services'), 'lastmod' => now()->toIso8601String(), 'changefreq' => 'weekly', 'priority' => '0.9'];
        $urls[] = ['loc' => url('/videos'), 'lastmod' => now()->toIso8601String(), 'changefreq' => 'weekly', 'priority' => '0.7'];
        $urls[] = ['loc' => url('/gallery'), 'lastmod' => now()->toIso8601String(), 'changefreq' => 'weekly', 'priority' => '0.7'];
        $urls[] = ['loc' => url('/blog'), 'lastmod' => now()->toIso8601String(), 'changefreq' => 'daily', 'priority' => '0.8'];
        $urls[] = ['loc' => url('/contact'), 'lastmod' => now()->toIso8601String(), 'changefreq' => 'monthly', 'priority' => '0.8'];

        // Dynamic Services
        foreach (Service::published()->get() as $svc) {
            $urls[] = [
                'loc' => url('/services/' . $svc->slug),
                'lastmod' => $svc->updated_at->toIso8601String(),
                'changefreq' => 'weekly',
                'priority' => '0.8',
            ];
        }

        // Dynamic Blog Posts
        foreach (BlogPost::published()->get() as $post) {
            $urls[] = [
                'loc' => url('/blog/' . $post->slug),
                'lastmod' => $post->updated_at->toIso8601String(),
                'changefreq' => 'monthly',
                'priority' => '0.7',
            ];
        }

        // Dynamic Custom Pages
        foreach (Page::published()->whereNotIn('slug', ['home', 'about', 'contact'])->get() as $pg) {
            $urls[] = [
                'loc' => url('/' . $pg->slug),
                'lastmod' => $pg->updated_at->toIso8601String(),
                'changefreq' => 'monthly',
                'priority' => '0.6',
            ];
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $u) {
            $xml .= "  <url>\n";
            $xml .= "    <loc>" . htmlspecialchars($u['loc']) . "</loc>\n";
            $xml .= "    <lastmod>" . $u['lastmod'] . "</lastmod>\n";
            $xml .= "    <changefreq>" . $u['changefreq'] . "</changefreq>\n";
            $xml .= "    <priority>" . $u['priority'] . "</priority>\n";
            $xml .= "  </url>\n";
        }

        $xml .= '</urlset>';
        return $xml;
    }

    public function generateRobotsTxt(): string
    {
        $siteUrl = url('/');
        return "User-agent: *\nDisallow: /admin\nDisallow: /api/\nAllow: /\n\nSitemap: {$siteUrl}/sitemap.xml\n";
    }

    public function getClinicSchema(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'MedicalBusiness',
            'name' => SiteSetting::get('site_name', 'Lumique Aesthetic Clinic'),
            'image' => url('/images/logo.jpeg'),
            '@id' => url('/'),
            'url' => url('/'),
            'telephone' => SiteSetting::get('phone', '+91 88795 50581'),
            'priceRange' => '₹₹₹',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => 'Ground Floor, Kenilworth Mall, Linking Road',
                'addressLocality' => 'Bandra West, Mumbai',
                'postalCode' => '400050',
                'addressCountry' => 'IN',
            ],
            'geo' => [
                '@type' => 'GeoCoordinates',
                'latitude' => 19.04432,
                'longitude' => 72.83355,
            ],
            'openingHoursSpecification' => [
                [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
                    'opens' => '09:00',
                    'closes' => '19:00',
                ],
            ],
        ];
    }
}
