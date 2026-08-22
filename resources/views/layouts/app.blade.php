@php
    $currentPath = '/' . ltrim(request()->path(), '/');
    if ($currentPath === '/home') $currentPath = '/';
    $customMeta = \App\Models\SeoMeta::where('path', $currentPath)->first();

    $siteFavicon = !empty($settings['favicon_url']) ? (str_starts_with($settings['favicon_url'], 'http') || str_starts_with($settings['favicon_url'], '/') ? $settings['favicon_url'] : asset('storage/' . $settings['favicon_url'])) : '/images/favicon.png';
    $siteLogo = !empty($settings['logo_url']) ? (str_starts_with($settings['logo_url'], 'http') || str_starts_with($settings['logo_url'], '/') ? $settings['logo_url'] : asset('storage/' . $settings['logo_url'])) : '/images/logo.jpeg';

    $pageTitle = $customMeta->meta_title ?? null;
    $pageDesc = $customMeta->meta_description ?? ($settings['default_meta_description'] ?? 'Personalized skin, hair, laser, and aesthetic treatments delivered by expert board-certified dermatologists in Bandra West, Mumbai.');
    $pageKeywords = $customMeta->meta_keywords ?? ($settings['default_meta_keywords'] ?? 'dermatologist, skin clinic, aesthetic clinic mumbai, hydrafacial bandra');
    $pageOgImage = !empty($customMeta->og_image) ? (str_starts_with($customMeta->og_image, 'http') ? $customMeta->og_image : url($customMeta->og_image)) : url($siteLogo);
    $pageRobots = $customMeta->robots ?? 'index, follow';

    $schemaData = [
        '@context' => 'https://schema.org',
        '@graph' => [
            [
                '@type' => 'MedicalBusiness',
                '@id' => url('/') . '#organization',
                'name' => $settings['site_name'] ?? 'Lumique Aesthetic Clinic',
                'url' => url('/'),
                'logo' => [
                    '@type' => 'ImageObject',
                    '@id' => url('/') . '#logo',
                    'inLanguage' => 'en-US',
                    'url' => url($siteLogo),
                    'contentUrl' => url($siteLogo),
                    'caption' => $settings['site_name'] ?? 'Lumique Aesthetic Clinic',
                ],
                'image' => url($siteLogo),
                'description' => $settings['default_meta_description'] ?? 'Personalized skin, hair, laser, and aesthetic treatments delivered by expert board-certified dermatologists.',
                'telephone' => $settings['contact_phone'] ?? '+91 98765 43210',
                'email' => $settings['contact_email'] ?? 'contact@lumiqueclinic.com',
                'priceRange' => '$$$',
                'address' => [
                    '@type' => 'PostalAddress',
                    'streetAddress' => $settings['clinic_address'] ?? 'Ground Floor, Luxury Promenade, Bandra West',
                    'addressLocality' => 'Mumbai',
                    'addressRegion' => 'Maharashtra',
                    'postalCode' => '400050',
                    'addressCountry' => 'IN',
                ],
            ],
            [
                '@type' => 'WebSite',
                '@id' => url('/') . '#website',
                'url' => url('/'),
                'name' => $settings['site_name'] ?? 'Lumique Aesthetic Clinic',
                'publisher' => [
                    '@id' => url('/') . '#organization',
                ],
            ],
        ],
    ];
    $schemaJson = json_encode($schemaData, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', $pageTitle ?: (($settings['site_name'] ?? 'Lumique Aesthetic Clinic') . ' | Advanced Dermatology & Aesthetic Care'))</title>
    <meta name="description" content="@yield('meta_description', $pageDesc)">
    <meta name="keywords" content="@yield('meta_keywords', $pageKeywords)">
    <meta name="robots" content="{{ $pageRobots }}">
    
    <!-- Favicon & Touch Icons for Google Search Results & Browsers -->
    <link rel="icon" type="image/png" sizes="32x32" href="{{ url($siteFavicon) }}">
    <link rel="icon" type="image/png" sizes="192x192" href="{{ url($siteLogo) }}">
    <link rel="shortcut icon" href="{{ url($siteFavicon) }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url($siteLogo) }}">
    <meta name="theme-color" content="#C8101E">
    
    <!-- Open Graph / Social Link Previews -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ $settings['site_name'] ?? 'Lumique Aesthetic Clinic' }}">
    <meta property="og:title" content="@yield('title', $pageTitle ?: ($settings['site_name'] ?? 'Lumique Aesthetic Clinic'))">
    <meta property="og:description" content="@yield('meta_description', $pageDesc)">
    <meta property="og:image" content="@yield('og_image', $pageOgImage)">
    <meta property="og:url" content="{{ url()->current() }}">

    @if(!empty($settings['google_site_verification']))
    <meta name="google-site-verification" content="{{ $settings['google_site_verification'] }}">
    @endif

    <!-- Google Structured Data / JSON-LD for Google Search Results Logo & Knowledge Graph -->
    <script type="application/ld+json">
    {!! $schemaJson !!}
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <!-- Master CSS Stylesheet -->
    <link rel="stylesheet" href="/css/style.css">

    <!-- Lucide Icons CDN -->
    <script src="https://unpkg.com/lucide@latest"></script>

    @yield('styles')
</head>
<body>

    <!-- Site Header -->
    <header class="site-header @yield('header_class', 'site-header-transparent-dark')">
        <div class="container-luxury">
            <div class="header-inner">
                <!-- Logo -->
                <a href="{{ route('home') }}" class="brand-logo">
                    <span class="brand-logo-img-wrapper">
                        <img src="{{ $siteLogo }}" alt="{{ $settings['site_name'] ?? 'Lumique Aesthetic Clinic' }} logo" class="brand-logo-img">
                    </span>
                    <div class="brand-logo-text">
                        <span class="brand-logo-name">LUMIQUE</span>
                        <span class="brand-logo-sub">Aesthetic Clinic</span>
                    </div>
                </a>

                <!-- Desktop Navigation -->
                <nav class="nav-menu">
                    <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                    <a href="{{ route('about') }}" class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}">About Us</a>
                    <a href="{{ route('services.index') }}" class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}">Treatments</a>
                    <a href="{{ route('videos.index') }}" class="nav-link {{ request()->routeIs('videos.*') ? 'active' : '' }}">Videos</a>
                    <a href="{{ route('gallery.index') }}" class="nav-link {{ request()->routeIs('gallery.*') ? 'active' : '' }}">Gallery</a>
                    <a href="{{ route('blog.index') }}" class="nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}">Articles</a>
                    <a href="{{ route('contact') }}" class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
                </nav>

                <!-- Header Actions -->
                <div class="header-actions">
                    <div class="social-icons-group">
                        <a href="{{ $settings['instagram_url'] ?? '#' }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="social-icon-btn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                        </a>
                        <a href="{{ $settings['youtube_url'] ?? '#' }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="social-icon-btn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['whatsapp'] ?? '918879550581') }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp" class="social-icon-btn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.247-.694.247-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" /></svg>
                        </a>
                    </div>
                    <a href="tel:{{ $settings['phone'] ?? '+918879550581' }}" class="header-phone">
                        <i data-lucide="phone" style="width: 16px; height: 16px; color: var(--color-crimson);"></i>
                        <span>{{ $settings['phone'] ?? '+91 88795 50581' }}</span>
                    </a>
                    <button type="button" class="btn-primary btn-sm open-appointment-modal" onclick="openAppointmentModal()">
                        <i data-lucide="calendar" style="width: 14px; height: 14px;"></i>
                        <span>Book an Appointment</span>
                    </button>
                </div>

                <!-- Mobile Menu Button -->
                <button class="mobile-menu-btn" aria-label="Toggle navigation menu">
                    <i data-lucide="menu" style="width: 24px; height: 24px;"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Mobile Drawer -->
    <div class="mobile-drawer">
        <div class="mobile-drawer-backdrop"></div>
        <div class="mobile-drawer-panel">
            <button class="mobile-drawer-close" aria-label="Close menu">
                <i data-lucide="x" style="width: 24px; height: 24px;"></i>
            </button>
            <nav class="mobile-nav-list">
                <a href="{{ route('home') }}" class="mobile-nav-link {{ request()->routeIs('home') ? 'active' : '' }}">Home</a>
                <a href="{{ route('about') }}" class="mobile-nav-link {{ request()->routeIs('about') ? 'active' : '' }}">About Us</a>
                <a href="{{ route('services.index') }}" class="mobile-nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}">Treatments</a>
                <a href="{{ route('videos.index') }}" class="mobile-nav-link {{ request()->routeIs('videos.*') ? 'active' : '' }}">Videos</a>
                <a href="{{ route('gallery.index') }}" class="mobile-nav-link {{ request()->routeIs('gallery.*') ? 'active' : '' }}">Gallery</a>
                <a href="{{ route('blog.index') }}" class="mobile-nav-link {{ request()->routeIs('blog.*') ? 'active' : '' }}">Articles</a>
                <a href="{{ route('contact') }}" class="mobile-nav-link {{ request()->routeIs('contact') ? 'active' : '' }}">Contact</a>
            </nav>
            <div class="mobile-drawer-footer">
                <a href="tel:{{ $settings['phone'] ?? '+918879550581' }}" class="header-phone" style="margin-bottom: 0.75rem;">
                    <i data-lucide="phone" style="width: 18px; height: 18px; color: var(--color-crimson);"></i>
                    <span>{{ $settings['phone'] ?? '+91 88795 50581' }}</span>
                </a>
                <button type="button" class="btn-primary open-appointment-modal" style="width: 100%; justify-content: center;" onclick="document.querySelector('.mobile-drawer')?.classList.remove('open'); document.body.classList.remove('menu-open'); document.body.style.overflow=''; openAppointmentModal();">
                    <i data-lucide="calendar" style="width: 18px; height: 18px;"></i>
                    <span>Book Appointment</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Main Content Area -->
    <main>
        @yield('content')
    </main>

    <!-- Master Footer -->
    <footer class="site-footer">
        <div class="container-luxury">
            <div class="footer-grid">
                <!-- Col 1: Brand -->
                <div>
                    <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;">
                        <span class="brand-logo-img-wrapper" style="width: 2.5rem; height: 2.5rem;">
                            <img src="{{ $siteLogo }}" alt="{{ $settings['site_name'] ?? 'Lumique' }} logo" class="brand-logo-img">
                        </span>
                        <div class="brand-logo-text">
                            <span class="brand-logo-name" style="color: #ffffff; font-size: 1.125rem;">LUMIQUE</span>
                            <span class="brand-logo-sub" style="color: rgba(255, 255, 255, 0.5);">Aesthetic Clinic</span>
                        </div>
                    </div>
                    <p style="font-size: 0.875rem; color: rgba(255, 255, 255, 0.65); line-height: 1.7; margin-bottom: 1.5rem;">
                        {{ $settings['tagline'] ?? 'Advanced dermatology and aesthetic care designed around you. Personalized treatments for skin, hair, laser, and aesthetic enhancement.' }}
                    </p>
                    <div class="social-icons-group">
                        <a href="{{ $settings['instagram_url'] ?? '#' }}" target="_blank" rel="noopener noreferrer" aria-label="Instagram" class="social-icon-btn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                        </a>
                        <a href="{{ $settings['youtube_url'] ?? '#' }}" target="_blank" rel="noopener noreferrer" aria-label="YouTube" class="social-icon-btn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                        </a>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['whatsapp'] ?? '918879550581') }}" target="_blank" rel="noopener noreferrer" aria-label="WhatsApp" class="social-icon-btn">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.247-.694.247-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" /></svg>
                        </a>
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div>
                    <h4 class="footer-title">Quick Links</h4>
                    <div class="footer-links">
                        <a href="{{ route('home') }}" class="footer-link">Home</a>
                        <a href="{{ route('about') }}" class="footer-link">About Doctor</a>
                        <a href="{{ route('services.index') }}" class="footer-link">Treatments</a>
                        <a href="{{ route('blog.index') }}" class="footer-link">Educational Articles</a>
                        <a href="{{ route('contact') }}" class="footer-link">Book Appointment</a>
                    </div>
                </div>

                <!-- Col 3: Treatments -->
                <div>
                    <h4 class="footer-title">Treatments</h4>
                    <div class="footer-links">
                        <a href="/services?category=skin" class="footer-link">Skin Treatments</a>
                        <a href="/services?category=hair" class="footer-link">Hair Restoration</a>
                        <a href="/services?category=laser" class="footer-link">Laser Treatments</a>
                        <a href="/services?category=tattoo-removal" class="footer-link">Tattoo Removal</a>
                        <a href="/services?category=aesthetic-treatments" class="footer-link">Aesthetic Enhancements</a>
                    </div>
                </div>

                <!-- Col 4: Get In Touch -->
                <div>
                    <h4 class="footer-title">Get in Touch</h4>
                    <div class="footer-contact-item">
                        <i data-lucide="map-pin" class="footer-contact-icon" style="width: 16px; height: 16px;"></i>
                        <span>{{ $settings['address'] ?? 'Ground Floor, Kenilworth Mall, Linking Road, Bandra West, Mumbai 400050' }}</span>
                    </div>
                    <div class="footer-contact-item">
                        <i data-lucide="phone" class="footer-contact-icon" style="width: 16px; height: 16px;"></i>
                        <a href="tel:{{ $settings['phone'] ?? '+918879550581' }}" style="color: rgba(255, 255, 255, 0.8);">{{ $settings['phone'] ?? '+91 88795 50581' }}</a>
                    </div>
                    <div class="footer-contact-item">
                        <i data-lucide="mail" class="footer-contact-icon" style="width: 16px; height: 16px;"></i>
                        <a href="mailto:{{ $settings['email'] ?? 'info@lumiqueclinic.com' }}" style="color: rgba(255, 255, 255, 0.8);">{{ $settings['email'] ?? 'info@lumiqueclinic.com' }}</a>
                    </div>
                    <div class="footer-contact-item">
                        <i data-lucide="clock" class="footer-contact-icon" style="width: 16px; height: 16px;"></i>
                        <span>Mon – Sat: 9:00 AM – 7:00 PM</span>
                    </div>
                </div>
            </div>

            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} {{ $settings['site_name'] ?? 'Lumique Aesthetic Clinic' }}. All rights reserved.</p>
                <p style="font-size: 0.8rem; color: rgba(255, 255, 255, 0.65);">
                    Designed & Developed by <a href="https://dicetechnosoft.cloud" target="_blank" rel="noopener noreferrer" style="color: #F5D67D; font-weight: 600; text-decoration: none;">Dice Technosoft</a>
                </p>
            </div>
        </div>
    </footer>

    <!-- Universal Floating WhatsApp Button -->
    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['whatsapp'] ?? '918879550581') }}?text=Hello%20Lumique%20Clinic,%20I%20would%20like%20to%20inquire%20about%20a%20consultation." target="_blank" rel="noopener noreferrer" class="whatsapp-floating-btn" aria-label="Chat on WhatsApp">
        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51l-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.247-.694.247-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" /></svg>
        <span class="whatsapp-tooltip">Chat with Concierge</span>
    </a>

    <!-- Universal Interactive Appointment Modal (Uses .lumique-modal-backdrop from style.css) -->
    <div id="lumique-appointment-modal" class="lumique-modal-backdrop" aria-hidden="true" role="dialog">
        <div class="lumique-modal-dialog">
            <div class="lumique-modal-header">
                <div>
                    <h2 class="lumique-modal-title">Book an Appointment</h2>
                    <p class="lumique-modal-subtitle">{{ $settings['site_name'] ?? 'Lumique Aesthetic Clinic' }} · Bandra West, Mumbai</p>
                </div>
                <button type="button" class="lumique-modal-close" onclick="closeAppointmentModal()" aria-label="Close modal">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                </button>
            </div>
            <div class="lumique-modal-body" id="modal-form-content">
                <form id="modal-appointment-form" onsubmit="handleModalAppointmentSubmit(event)">
                    <div id="modal-form-alert" class="form-alert" style="display: none; margin-bottom: 1rem; padding: 0.875rem; border-radius: 4px; font-size: 0.875rem;"></div>

                    <div class="grid-2" style="gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label" for="modal-name">Full Name *</label>
                            <input type="text" id="modal-name" name="name" class="form-control" placeholder="e.g. Priya Sharma" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="modal-phone">Phone Number *</label>
                            <input type="tel" id="modal-phone" name="phone" class="form-control" placeholder="+91 98765 43210" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="modal-email">Email Address *</label>
                        <input type="email" id="modal-email" name="email" class="form-control" placeholder="you@example.com" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="modal-treatment">Desired Treatment or Concern</label>
                        <select id="modal-treatment" name="service_name" class="form-control">
                            <option value="General Consultation">General Dermatological Consultation</option>
                            <option value="Medical HydraFacial MD®">Medical HydraFacial MD®</option>
                            <option value="Hollywood Carbon Laser Peel">Hollywood Carbon Laser Peel</option>
                            <option value="Advanced PRP / GFC Hair Restoration">Advanced PRP / GFC Hair Restoration</option>
                            <option value="Picosecond Laser Tattoo Removal">Picosecond Laser Tattoo Removal</option>
                            <option value="Subtle Facial Contouring & Fillers">Subtle Facial Contouring & Fillers</option>
                            <option value="Triple-Wavelength Laser Hair Reduction">Triple-Wavelength Laser Hair Reduction</option>
                        </select>
                    </div>

                    <div class="grid-2" style="gap: 1rem;">
                        <div class="form-group">
                            <label class="form-label" for="modal-date">Preferred Date</label>
                            <input type="date" id="modal-date" name="preferred_date" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="modal-time">Preferred Time</label>
                            <select id="modal-time" name="preferred_time" class="form-control">
                                <option value="Morning (10:00 AM – 1:00 PM)">Morning (10:00 AM – 1:00 PM)</option>
                                <option value="Afternoon (1:00 PM – 4:00 PM)">Afternoon (1:00 PM – 4:00 PM)</option>
                                <option value="Evening (4:00 PM – 7:00 PM)">Evening (4:00 PM – 7:00 PM)</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="modal-message">Notes or Specific Concerns</label>
                        <textarea id="modal-message" name="message" class="form-control" rows="2" placeholder="Tell us about your skin goals or any prior treatments..."></textarea>
                    </div>

                    <button type="submit" id="modalSubmitBtn" class="btn-primary" style="width: 100%; justify-content: center; padding: 1rem;">
                        <span>Confirm Appointment Request</span>
                    </button>
                    <p style="font-size: 0.75rem; color: var(--color-charcoal-muted); text-align: center; margin-top: 0.75rem;">
                        🔒 We respect your privacy. No spam. A clinical advisor will call you to confirm.
                    </p>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="/js/main.js"></script>
    <script>
        // Modal Open / Close Logic
        function openAppointmentModal(treatmentTitle = '') {
            const modal = document.getElementById('lumique-appointment-modal');
            const select = document.getElementById('modal-treatment');
            if (treatmentTitle && select) {
                for (let i = 0; i < select.options.length; i++) {
                    if (select.options[i].text.toLowerCase().includes(treatmentTitle.toLowerCase()) || 
                        select.options[i].value.toLowerCase().includes(treatmentTitle.toLowerCase())) {
                        select.selectedIndex = i;
                        break;
                    }
                }
            }
            if (modal) {
                modal.classList.add('open');
                document.body.style.overflow = 'hidden';
            }
        }

        function closeAppointmentModal() {
            const modal = document.getElementById('lumique-appointment-modal');
            if (modal) {
                modal.classList.remove('open');
                document.body.style.overflow = '';
            }
        }

        window.openAppointmentModal = openAppointmentModal;
        window.closeAppointmentModal = closeAppointmentModal;

        // Modal backdrop click to close
        document.addEventListener('click', (e) => {
            const modal = document.getElementById('lumique-appointment-modal');
            if (e.target === modal) {
                closeAppointmentModal();
            }
        });

        // ESC key to close
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeAppointmentModal();
            }
        });

        // Set min date to today
        const modalDate = document.getElementById('modal-date');
        if (modalDate) {
            modalDate.min = new Date().toISOString().split('T')[0];
        }

        // Live API Appointment Submission
        async function handleModalAppointmentSubmit(e) {
            e.preventDefault();
            const btn = document.getElementById('modalSubmitBtn');
            const alertBox = document.getElementById('modal-form-alert');
            btn.disabled = true;
            btn.innerHTML = '<span>Submitting Request...</span>';
            alertBox.style.display = 'none';

            const payload = {
                name: document.getElementById('modal-name').value,
                phone: document.getElementById('modal-phone').value,
                email: document.getElementById('modal-email').value,
                service_name: document.getElementById('modal-treatment').value,
                preferred_date: document.getElementById('modal-date').value || null,
                preferred_time: document.getElementById('modal-time').value,
                message: document.getElementById('modal-message').value,
            };

            try {
                const response = await fetch('/api/v1/appointments', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();
                if (response.ok && data.success) {
                    const content = document.getElementById('modal-form-content');
                    content.innerHTML = `
                        <div style="text-align: center; padding: 2rem 1rem;">
                            <div style="width: 4rem; height: 4rem; background-color: var(--color-soft-red); color: var(--color-crimson); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
                            </div>
                            <h3 class="heading-3" style="margin-bottom: 0.5rem; font-size: 1.35rem;">Appointment Received!</h3>
                            <p class="body-text" style="font-size: 0.95rem; margin-bottom: 1.5rem;">
                                Thank you, <strong>${payload.name}</strong>. Our clinical concierge in Bandra West will connect with you at <strong>${payload.phone}</strong> shortly to confirm your consultation.
                            </p>
                            <button type="button" class="btn-primary" onclick="closeAppointmentModal()" style="width: 100%; justify-content: center;">
                                Done
                            </button>
                        </div>
                    `;
                } else {
                    alertBox.style.background = '#fdebee';
                    alertBox.style.color = '#c8101e';
                    alertBox.style.border = '1px solid #f9cdd3';
                    alertBox.innerHTML = data.message || 'Please check your inputs and try again.';
                    alertBox.style.display = 'block';
                }
            } catch (err) {
                alertBox.style.background = '#fdebee';
                alertBox.style.color = '#c8101e';
                alertBox.style.border = '1px solid #f9cdd3';
                alertBox.innerHTML = 'Unable to connect to server. Please call us at +91 88795 50581.';
                alertBox.style.display = 'block';
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<span>Confirm Appointment Request</span>';
            }
        }
    </script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
