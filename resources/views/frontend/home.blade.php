@extends('layouts.app')

@section('title', ($homePage->title ?? 'Home') . ' | ' . ($settings['site_name'] ?? 'Lumique Aesthetic Clinic'))

@section('content')
<!-- Dynamic Hero Section -->
@php
    $heroSection = $sections['hero'] ?? null;
    $heroImage = $banner->image ?? ($heroSection->image ?? 'https://images.pexels.com/photos/7446659/pexels-photo-7446659.jpeg?auto=compress&cs=tinysrgb&w=1920');
    $heroBadge = $banner->badge_text ?? ($heroSection->settings['tag_text'] ?? ($settings['site_name'] ?? 'Lumique Aesthetic Clinic'));
    $heroSubtitle = $banner->subtitle ?? ($heroSection->subtitle ?? 'Skin · Hair · Laser · Aesthetic');
    $heroTitle = $banner->title ?? ($heroSection->title ?? 'Advanced Dermatology & Aesthetic Care Designed Around You');
    $heroDesc = $heroSection->content ?? 'Personalized skin, hair, laser, and aesthetic treatments delivered by board-certified specialists in a serene, luxurious clinical environment in Bandra West, Mumbai.';
    $heroStats = $heroSection->settings['stats'] ?? [
        ['number' => '15k+', 'label' => 'Happy Patients'],
        ['number' => '50+', 'label' => 'Treatments'],
        ['number' => '12+', 'label' => 'Years Experience'],
    ];
@endphp

<section class="hero-section">
  <div class="hero-bg">
    <img src="{{ $heroImage }}" alt="{{ $heroTitle }}">
    <div class="hero-overlay-dark"></div>
    <div class="hero-overlay-bottom"></div>
  </div>

  <div class="container-luxury">
    <div class="hero-content">
      <div class="hero-tag">
        <span class="hero-tag-line"></span>
        <span class="hero-tag-text">{{ $heroBadge }}</span>
      </div>
      <p class="hero-subtitle">{{ $heroSubtitle }}</p>
      <h1 class="hero-title text-balance">
        {{ $heroTitle }}
      </h1>
      <p class="hero-desc">
        {{ $heroDesc }}
      </p>
      <div class="hero-actions">
        <button type="button" class="btn-primary open-appointment-modal" onclick="openAppointmentModal()">
          <i data-lucide="calendar" style="width: 18px; height: 18px;"></i>
          <span>{{ $banner->button_text ?? 'Book an Appointment' }}</span>
        </button>
        <a href="{{ $banner->secondary_button_url ?? route('services.index') }}" class="btn-secondary btn-secondary-white">
          <span>{{ $banner->secondary_button_text ?? 'Explore Treatments' }}</span>
          <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
        </a>
      </div>
    </div>
  </div>

  <!-- Dynamic Floating Stat Badges -->
  <div class="hero-stats-sidebar">
    @foreach($heroStats as $stat)
    <div class="hero-stat-card">
      <p class="hero-stat-number">{{ $stat['number'] ?? '10+' }}</p>
      <p class="hero-stat-label">{{ $stat['label'] ?? 'Clinical Metric' }}</p>
    </div>
    @endforeach
  </div>
</section>

<!-- Dynamic Clinic Snapshot Bar -->
@php
    $snapshotSection = $sections['snapshot'] ?? null;
    $snapshotItems = $snapshotSection->settings['items'] ?? [
        ['label' => 'Visit Lumique', 'title' => 'Your confidence, cared for.', 'desc' => 'A calm, elevated clinic sanctuary built around your goals.'],
        ['label' => 'Call Us', 'title' => $settings['phone'] ?? '+91 88795 50581', 'desc' => 'Personal guidance from our dermatological team.'],
        ['label' => 'Opening Hours', 'title' => 'Mon – Sat: 9AM – 7PM', 'desc' => 'Sunday: By Special Appointment'],
        ['label' => 'Start Your Journey', 'title' => 'Ready when you are.', 'cta' => 'Book a consultation', 'dark' => true],
    ];
@endphp
<section class="snapshot-section">
  <div class="snapshot-grid">
    @foreach($snapshotItems as $item)
    <div class="snapshot-item {{ !empty($item['dark']) ? 'dark' : '' }}">
      <p class="snapshot-label">{{ $item['label'] }}</p>
      @if(!empty($item['phone']) || str_contains($item['title'] ?? '', '+91'))
      <a href="tel:{{ preg_replace('/[^0-9]/', '', $item['title']) }}" class="snapshot-title" style="display: block;">{{ $item['title'] }}</a>
      @else
      <p class="snapshot-title">{{ $item['title'] }}</p>
      @endif
      @if(!empty($item['desc']))
      <p class="snapshot-desc">{{ $item['desc'] }}</p>
      @endif
      @if(!empty($item['cta']))
      <a href="{{ route('contact') }}" style="color: var(--color-gold); font-size: 0.875rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.5rem; margin-top: 0.5rem;">
        <span>{{ $item['cta'] }}</span>
        <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
      </a>
      @endif
    </div>
    @endforeach
  </div>
</section>

<!-- Dynamic Trust & Pillars (Why Us) -->
@php
    $whyUsSection = $sections['why_us'] ?? null;
    $whyUsTitle = $whyUsSection->title ?? 'Care that feels personal, results that feel natural';
    $whyUsSubtitle = $whyUsSection->subtitle ?? 'Why Lumique';
    $whyUsContent = $whyUsSection->content ?? 'Every treatment at Lumique is tailored to your skin biology and goals — supported by cutting-edge medical technology and empathetic physicians.';
    $pillars = $whyUsSection->settings['pillars'] ?? [
        ['icon' => 'shield-check', 'title' => 'Safety-First', 'desc' => 'Treatment Approach'],
        ['icon' => 'microscope', 'title' => 'Advanced Tech', 'desc' => 'US-FDA Approved Systems'],
        ['icon' => 'heart-handshake', 'title' => 'Personalized Plans', 'desc' => 'Tailored to You'],
        ['icon' => 'sparkles', 'title' => 'Natural Results', 'desc' => 'Subtle & Elegant'],
    ];
@endphp
<section class="section-padding" style="background-color: var(--color-ivory); position: relative;">
  <div class="floating-bg-container" data-particles="8"></div>
  <div class="container-luxury" style="position: relative; z-index: 10;">
    <div class="reveal text-center" style="max-width: 44rem; margin: 0 auto 3.5rem; text-align: center;">
      <span class="section-label">{{ $whyUsSubtitle }}</span>
      <h2 class="heading-2 text-balance">{{ $whyUsTitle }}</h2>
      <p class="body-text" style="margin-top: 1rem;">{{ $whyUsContent }}</p>
    </div>

    <div class="grid-4">
      @foreach($pillars as $idx => $pillar)
      <div class="reveal delay-{{ ($idx % 4) + 1 }}" style="display: flex; flex-direction: column; align-items: center; text-align: center; gap: 1rem;">
        <div style="width: 3.5rem; height: 3.5rem; background-color: var(--color-soft-red); color: var(--color-crimson); display: flex; align-items: center; justify-content: center; border-radius: 50%;">
          <i data-lucide="{{ $pillar['icon'] ?? 'sparkles' }}" style="width: 24px; height: 24px;"></i>
        </div>
        <div>
          <h3 style="font-family: var(--font-serif); font-size: 1.125rem;">{{ $pillar['title'] }}</h3>
          <p style="font-size: 0.85rem; color: var(--color-charcoal-muted); margin-top: 0.25rem;">{{ $pillar['desc'] }}</p>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- Dynamic Featured Treatments Showcase -->
<section class="section-padding" style="background-color: #ffffff; position: relative;">
  <div class="floating-bg-container" data-particles="8"></div>
  <div class="container-luxury" style="position: relative; z-index: 10;">
    <div class="reveal" style="display: flex; flex-direction: column; align-items: flex-start; justify-content: space-between; margin-bottom: 3rem; gap: 1rem;">
      <span class="section-label">Signature Procedures</span>
      <div style="display: flex; justify-content: space-between; align-items: flex-end; width: 100%; flex-wrap: wrap; gap: 1rem;">
        <h2 class="heading-2 text-balance">Most Requested Treatments</h2>
        <a href="{{ route('services.index') }}" style="display: inline-flex; align-items: center; gap: 0.5rem; color: var(--color-crimson); font-weight: 600; font-size: 0.875rem;">
          <span>View All Treatments</span>
          <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
        </a>
      </div>
    </div>

    <div class="grid-3">
      @foreach($featuredServices as $service)
      <div class="luxury-card reveal" style="overflow: hidden; border-radius: 4px;">
        <div style="position: relative; overflow: hidden; height: 220px;">
          <img src="{{ $service->featured_image }}" alt="{{ $service->title }}" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;">
          <span style="position: absolute; top: 1rem; left: 1rem; background: var(--color-crimson); color: #fff; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; padding: 0.25rem 0.65rem;">
            {{ strtoupper($service->category) }}
          </span>
        </div>
        <div style="padding: 1.5rem;">
          <h3 style="font-family: var(--font-serif); font-size: 1.25rem; margin-bottom: 0.5rem;">{{ $service->title }}</h3>
          <p style="font-size: 0.875rem; color: var(--color-charcoal-muted); line-height: 1.6; margin-bottom: 1.25rem;">
            {{ $service->short_description }}
          </p>
          <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 1rem; border-top: 1px solid var(--color-border);">
            <span style="font-weight: 700; color: var(--color-gold); font-size: 0.9rem;">{{ $service->price_starting_at ?? 'Consultation' }}</span>
            <a href="{{ route('services.show', $service->slug) }}" style="color: var(--color-crimson); font-size: 0.85rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">
              <span>Explore</span>
              <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
            </a>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- Dynamic Doctor Spotlight Section -->
@if($doctor)
<section class="section-padding" style="background-color: var(--color-ivory); overflow: hidden; position: relative;">
  <div class="floating-bg-container" data-particles="6"></div>
  <div class="container-luxury" style="position: relative; z-index: 10;">
    <div class="grid-2" style="align-items: center; gap: 4rem;">
      <div class="reveal">
        <div style="position: relative;">
          <div style="border-radius: 0.5rem; overflow: hidden; box-shadow: var(--shadow-luxury);">
            <img src="{{ $doctor->photo }}" alt="{{ $doctor->name }}" style="width: 100%; aspect-ratio: 3/4; object-fit: cover;">
          </div>
          <div style="position: absolute; bottom: -1.5rem; left: -1.5rem; background-color: var(--color-crimson); color: #ffffff; padding: 1.5rem; max-width: 220px;" class="animate-float">
            <p style="font-family: var(--font-serif); font-size: 2.25rem; font-weight: 700; line-height: 1; margin-bottom: 0.5rem;">{{ $doctor->experience_years ? $doctor->experience_years.'+' : '12+' }}</p>
            <p style="font-size: 0.8rem; line-height: 1.4; color: rgba(255, 255, 255, 0.9);">{{ $doctor->experience_years ? 'Years of clinical dermatology excellence' : 'Patient-focused approach to every procedure' }}</p>
          </div>
        </div>
      </div>

      <div class="reveal delay-1">
        <span class="section-label">Meet Your Specialist</span>
        <h2 class="heading-2" style="margin-bottom: 1.5rem;">{{ $doctor->name }}</h2>
        <p class="body-text" style="margin-bottom: 2rem;">
          {{ $doctor->short_bio ?? $doctor->full_bio ?? $doctor->bio }}
        </p>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; margin-bottom: 2.5rem;">
          <div style="background-color: #ffffff; border: 1px solid var(--color-border); padding: 1.25rem 1rem; display: flex; align-items: center; gap: 0.75rem; border-radius: 4px;">
            <i data-lucide="award" style="color: var(--color-crimson); width: 22px; height: 22px; flex-shrink: 0;"></i>
            <span style="font-size: 0.85rem; font-weight: 600; line-height: 1.4; color: var(--color-charcoal);">{{ $doctor->qualification ?: 'Board Certified MD' }}</span>
          </div>
          <div style="background-color: #ffffff; border: 1px solid var(--color-border); padding: 1.25rem 1rem; display: flex; align-items: center; gap: 0.75rem; border-radius: 4px;">
            <i data-lucide="stethoscope" style="color: var(--color-crimson); width: 22px; height: 22px; flex-shrink: 0;"></i>
            <span style="font-size: 0.85rem; font-weight: 600; line-height: 1.4; color: var(--color-charcoal);">{{ $doctor->designation ?: 'Specialist Dermatologist' }}</span>
          </div>
        </div>
        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
          <button type="button" class="btn-primary" onclick="openAppointmentModal()">Consult Dr.</button>
          <a href="{{ route('about') }}" class="btn-secondary">
            <span>Learn More About the Doctor</span>
            <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
          </a>
        </div>
      </div>
    </div>
  </div>
</section>
@endif

<!-- Dynamic Journey Steps (How It Works) -->
@php
    $howItWorks = $sections['how_it_works'] ?? null;
    $journeyTitle = $howItWorks->title ?? 'How Lumique Works';
    $journeySubtitle = $howItWorks->subtitle ?? 'Your Journey';
    $journeyContent = $howItWorks->content ?? 'A simple, guided path from your initial consultation to confident, lasting results.';
    $journeySteps = $howItWorks->settings['steps'] ?? [
        ['step' => '01', 'icon' => 'calendar', 'title' => 'Book Consultation', 'desc' => 'Reserve your appointment online or call us directly to select a comfortable slot.'],
        ['step' => '02', 'icon' => 'stethoscope', 'title' => 'Personal Assessment', 'desc' => 'Your specialist reviews your skin/hair type and concerns to formulate a tailored plan.'],
        ['step' => '03', 'icon' => 'microscope', 'title' => 'Expert Procedure', 'desc' => 'Receive treatment in a medical-grade, serene environment using advanced technology.'],
        ['step' => '04', 'icon' => 'sparkles', 'title' => 'Lasting Radiance', 'desc' => 'We guide your aftercare protocol so your results stay natural, fresh, and sustained.'],
    ];
@endphp
<section class="section-padding" style="background-color: #ffffff; position: relative;">
  <div class="floating-bg-container" data-particles="6"></div>
  <div class="container-luxury" style="position: relative; z-index: 10;">
    <div class="reveal text-center" style="max-width: 40rem; margin: 0 auto 3.5rem; text-align: center;">
      <span class="section-label">{{ $journeySubtitle }}</span>
      <h2 class="heading-2">{{ $journeyTitle }}</h2>
      <p class="body-text" style="margin-top: 0.75rem;">{{ $journeyContent }}</p>
    </div>

    <div class="grid-4">
      @foreach($journeySteps as $idx => $step)
      <div class="luxury-card reveal delay-{{ ($idx % 4) + 1 }}" style="padding: 2rem; position: relative;">
        <span style="position: absolute; top: 1.25rem; right: 1.25rem; font-family: var(--font-serif); font-size: 2.25rem; font-weight: 700; color: var(--color-soft-red);">{{ $step['step'] ?? ('0'.($idx+1)) }}</span>
        <div style="width: 3rem; height: 3rem; background-color: var(--color-crimson); color: #fff; border-radius: 0.5rem; display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem;">
          <i data-lucide="{{ $step['icon'] ?? 'sparkles' }}" style="width: 20px; height: 20px;"></i>
        </div>
        <h3 style="font-family: var(--font-serif); font-size: 1.15rem; margin-bottom: 0.5rem;">{{ $step['title'] }}</h3>
        <p style="font-size: 0.85rem; color: var(--color-charcoal-muted); line-height: 1.6;">{{ $step['desc'] }}</p>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- Dynamic Patient Testimonials -->
<section class="section-padding" style="background-color: var(--color-ivory); position: relative;">
  <div class="floating-bg-container" data-particles="6"></div>
  <div class="container-luxury" style="position: relative; z-index: 10;">
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-end; margin-bottom: 3rem; gap: 1.5rem;">
      <div class="reveal" style="max-width: 40rem;">
        <span class="section-label">Patient Stories</span>
        <h2 class="heading-2">Trusted by People Like You</h2>
      </div>

      @if($testimonials->count() > 3)
      <div class="reveal" style="display: flex; gap: 0.75rem; align-items: center;">
        <button type="button" class="carousel-nav-btn" onclick="window.slideTestimonialCarousel(-1)" aria-label="Previous Testimonials" style="background: rgba(0,0,0,0.06); border-color: rgba(0,0,0,0.12); color: var(--color-charcoal);">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
        </button>
        <button type="button" class="carousel-nav-btn" onclick="window.slideTestimonialCarousel(1)" aria-label="Next Testimonials" style="background: rgba(0,0,0,0.06); border-color: rgba(0,0,0,0.12); color: var(--color-charcoal);">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </button>
      </div>
      @endif
    </div>

    @if($testimonials->count() > 3)
      <!-- Carousel format (more than 3 patient reviews) -->
      <div class="testimonial-carousel-container" style="position: relative; width: 100%;">
        <div class="testimonial-carousel-track" id="homeTestimonialCarouselTrack">
          @foreach($testimonials as $index => $testimonial)
          <div class="testimonial-carousel-slide" data-slide-index="{{ $index }}">
            <div class="luxury-card" style="padding: 2rem; border-radius: 6px; display: flex; flex-direction: column; justify-content: space-between; height: 100%; min-height: 250px; background: #ffffff; box-shadow: var(--shadow-sm); border: 1px solid var(--color-border);">
              <div>
                <div style="color: var(--color-gold); font-size: 1.1rem; margin-bottom: 1rem;">
                  @for($i=0; $i<$testimonial->rating; $i++) ★ @endfor
                </div>
                <p style="font-style: italic; font-size: 0.95rem; color: var(--color-charcoal); line-height: 1.7; margin-bottom: 1.5rem;">
                  "{{ $testimonial->content ?? $testimonial->feedback }}"
                </p>
              </div>
              <div style="display: flex; align-items: center; gap: 0.75rem; border-top: 1px solid var(--color-border); padding-top: 1rem;">
                @php
                    $words = preg_split('/\s+/', trim($testimonial->name ?? 'Patient'));
                    $firstL = substr($words[0] ?? 'P', 0, 1);
                    $lastL = isset($words[1]) ? substr($words[count($words) - 1], 0, 1) : '';
                    $initials = strtoupper($firstL . $lastL);
                    if (empty($initials)) { $initials = 'PT'; }
                    $colorPalette = [
                        ['bg' => '#8B1538', 'text' => '#ffffff'],
                        ['bg' => '#1A446C', 'text' => '#ffffff'],
                        ['bg' => '#1B634B', 'text' => '#ffffff'],
                        ['bg' => '#B8860B', 'text' => '#ffffff'],
                        ['bg' => '#5E2B7A', 'text' => '#ffffff'],
                        ['bg' => '#A04D2D', 'text' => '#ffffff'],
                        ['bg' => '#0F6B75', 'text' => '#ffffff'],
                        ['bg' => '#A82442', 'text' => '#ffffff'],
                        ['bg' => '#3E4B5E', 'text' => '#ffffff'],
                    ];
                    $colorIdx = abs(crc32($testimonial->name ?? 'P')) % count($colorPalette);
                    $avatar = $colorPalette[$colorIdx];
                @endphp
                <div style="width: 44px; height: 44px; border-radius: 50%; background-color: {{ $avatar['bg'] }}; color: {{ $avatar['text'] }}; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; letter-spacing: 0.5px; flex-shrink: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.12);">
                  {{ $initials }}
                </div>
                <div>
                  <strong style="display: block; font-size: 0.9rem; color: var(--color-charcoal);">{{ $testimonial->name ?? $testimonial->patient_name }}</strong>
                  <small style="color: var(--color-crimson); font-size: 0.8rem; font-weight: 500;">{{ $testimonial->treatment_taken }} &bull; {{ $testimonial->designation ?? 'Mumbai' }}</small>
                </div>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    @else
      <!-- 3 col-4 grid format (3 or fewer reviews) -->
      <div class="grid-3">
        @foreach($testimonials as $testimonial)
        <div class="luxury-card reveal" style="padding: 2rem; border-radius: 4px; display: flex; flex-direction: column; justify-content: space-between; background: #ffffff;">
          <div>
            <div style="color: var(--color-gold); font-size: 1.1rem; margin-bottom: 1rem;">
              @for($i=0; $i<$testimonial->rating; $i++) ★ @endfor
            </div>
            <p style="font-style: italic; font-size: 0.95rem; color: var(--color-charcoal); line-height: 1.7; margin-bottom: 1.5rem;">
              "{{ $testimonial->content ?? $testimonial->feedback }}"
            </p>
          </div>
          <div style="display: flex; align-items: center; gap: 0.75rem; border-top: 1px solid var(--color-border); padding-top: 1rem;">
            @php
                $words = preg_split('/\s+/', trim($testimonial->name ?? 'Patient'));
                $firstL = substr($words[0] ?? 'P', 0, 1);
                $lastL = isset($words[1]) ? substr($words[count($words) - 1], 0, 1) : '';
                $initials = strtoupper($firstL . $lastL);
                if (empty($initials)) { $initials = 'PT'; }
                $colorPalette = [
                    ['bg' => '#8B1538', 'text' => '#ffffff'],
                    ['bg' => '#1A446C', 'text' => '#ffffff'],
                    ['bg' => '#1B634B', 'text' => '#ffffff'],
                    ['bg' => '#B8860B', 'text' => '#ffffff'],
                    ['bg' => '#5E2B7A', 'text' => '#ffffff'],
                    ['bg' => '#A04D2D', 'text' => '#ffffff'],
                    ['bg' => '#0F6B75', 'text' => '#ffffff'],
                    ['bg' => '#A82442', 'text' => '#ffffff'],
                    ['bg' => '#3E4B5E', 'text' => '#ffffff'],
                ];
                $colorIdx = abs(crc32($testimonial->name ?? 'P')) % count($colorPalette);
                $avatar = $colorPalette[$colorIdx];
            @endphp
            <div style="width: 44px; height: 44px; border-radius: 50%; background-color: {{ $avatar['bg'] }}; color: {{ $avatar['text'] }}; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.9rem; letter-spacing: 0.5px; flex-shrink: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.12);">
              {{ $initials }}
            </div>
            <div>
              <strong style="display: block; font-size: 0.9rem; color: var(--color-charcoal);">{{ $testimonial->name ?? $testimonial->patient_name }}</strong>
              <small style="color: var(--color-crimson); font-size: 0.8rem; font-weight: 500;">{{ $testimonial->treatment_taken }} &bull; {{ $testimonial->designation ?? 'Mumbai' }}</small>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    @endif
  </div>
</section>

@if($videos->isNotEmpty())
<!-- Dynamic YouTube Clinical Videos Section -->
<section class="section-padding" style="background-color: var(--color-charcoal, #121216); color: #ffffff; position: relative;">
  <div class="floating-bg-container" data-particles="4"></div>
  <div class="container-luxury" style="position: relative; z-index: 10;">
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-end; margin-bottom: 3rem; gap: 1.5rem;">
      <div class="reveal" style="max-width: 38rem;">
        <span class="section-label" style="color: var(--color-gold);">Clinical Insights & Media</span>
        <h2 class="heading-2" style="color: #ffffff; margin-top: 0.5rem;">Watch Our Procedures & Results</h2>
        <p style="color: rgba(255, 255, 255, 0.7); font-size: 0.95rem; margin-top: 0.5rem; line-height: 1.6;">
          Real clinical demonstrations, procedure walkthroughs, and doctor insights filmed inside Lumique Aesthetic Clinic.
        </p>
      </div>

      @if($videos->count() > 3)
      <div class="reveal" style="display: flex; gap: 0.75rem; align-items: center;">
        <button type="button" class="carousel-nav-btn" onclick="slideVideoCarousel(-1)" aria-label="Previous Videos">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
        </button>
        <button type="button" class="carousel-nav-btn" onclick="slideVideoCarousel(1)" aria-label="Next Videos">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </button>
      </div>
      @endif
    </div>

    @if($videos->count() > 3)
      <!-- Carousel format (more than 3 videos) -->
      <div class="video-carousel-container">
        <div class="video-carousel-track" id="homeVideoCarouselTrack">
          @foreach($videos as $index => $video)
          @php
              $vYtId = $video->youtube_video_id;
              if (!$vYtId && $video->youtube_url) {
                  preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $video->youtube_url, $m);
                  $vYtId = $m[1] ?? 'dQw4w9WgXcQ';
              }
              $vThumb = (!empty($video->thumbnail) && !str_contains($video->thumbnail, 'pexels')) ? $video->thumbnail : "https://img.youtube.com/vi/{$vYtId}/hqdefault.jpg";
          @endphp
          <div class="video-carousel-slide" data-slide-index="{{ $index }}">
            <div class="media-video-card" data-youtube-id="{{ $vYtId }}" data-video-title="{{ $video->title }}" onclick="window.openHomeVideoModal('{{ $vYtId }}', '{{ addslashes($video->title) }}')" style="border: 1px solid rgba(255,255,255,0.12);">
              <img src="{{ $vThumb }}" alt="{{ $video->title }}" loading="lazy">
              <div class="media-play-badge" onclick="window.openHomeVideoModal('{{ $vYtId }}', '{{ addslashes($video->title) }}')">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="margin-left: 2px;"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
              </div>
              <div class="media-video-overlay">
                <span class="badge badge-gold" style="align-self: flex-start; margin-bottom: 0.5rem; font-size: 0.7rem;">{{ ucfirst(str_replace('-', ' ', $video->category ?? 'Clinical')) }}</span>
                <h4 style="font-family: var(--font-serif); font-size: 1.05rem; color: #ffffff; margin-bottom: 0.25rem; line-height: 1.35;">{{ $video->title }}</h4>
                <p style="font-size: 0.78rem; color: rgba(255,255,255,0.75); line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin: 0;">{{ $video->description }}</p>
              </div>
            </div>
          </div>
          @endforeach
        </div>
      </div>
    @else
      <!-- 3 col-4 grid format (3 or fewer videos) -->
      <div class="grid-3">
        @foreach($videos as $video)
        @php
            $vYtId = $video->youtube_video_id;
            if (!$vYtId && $video->youtube_url) {
                preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $video->youtube_url, $m);
                $vYtId = $m[1] ?? 'dQw4w9WgXcQ';
            }
            $vThumb = (!empty($video->thumbnail) && !str_contains($video->thumbnail, 'pexels')) ? $video->thumbnail : "https://img.youtube.com/vi/{$vYtId}/hqdefault.jpg";
        @endphp
        <div class="reveal">
          <div class="media-video-card" data-youtube-id="{{ $vYtId }}" data-video-title="{{ $video->title }}" onclick="window.openHomeVideoModal('{{ $vYtId }}', '{{ addslashes($video->title) }}')" style="border: 1px solid rgba(255,255,255,0.12);">
            <img src="{{ $vThumb }}" alt="{{ $video->title }}" loading="lazy">
            <div class="media-play-badge" onclick="window.openHomeVideoModal('{{ $vYtId }}', '{{ addslashes($video->title) }}')">
              <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" style="margin-left: 2px;"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
            </div>
            <div class="media-video-overlay">
              <span class="badge badge-gold" style="align-self: flex-start; margin-bottom: 0.5rem; font-size: 0.7rem;">{{ ucfirst(str_replace('-', ' ', $video->category ?? 'Clinical')) }}</span>
              <h4 style="font-family: var(--font-serif); font-size: 1.05rem; color: #ffffff; margin-bottom: 0.25rem; line-height: 1.35;">{{ $video->title }}</h4>
              <p style="font-size: 0.78rem; color: rgba(255,255,255,0.75); line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; margin: 0;">{{ $video->description }}</p>
            </div>
          </div>
        </div>
        @endforeach
      </div>
    @endif
  </div>
</section>
@endif

<!-- Dynamic CTA Section -->
@php
    $ctaSection = $sections['cta_banner'] ?? null;
    $ctaTitle = $ctaSection->title ?? 'Ready to Transform Your Skin, Hair, and Confidence?';
    $ctaSubtitle = $ctaSection->subtitle ?? 'Start Your Journey';
    $ctaContent = $ctaSection->content ?? 'Book your consultation today and discover personalized treatments designed around you in Bandra West, Mumbai.';
    $ctaPrimaryBtn = $ctaSection->settings['primary_btn'] ?? 'Book an Appointment';
    $ctaSecondaryBtn = $ctaSection->settings['secondary_btn'] ?? 'Call Us Directly';
@endphp
<section class="cta-banner">
  <div class="cta-banner-bg-glow"></div>
  <div class="container-luxury" style="position: relative; z-index: 10;">
    <p style="font-size: 0.75rem; font-weight: 600; letter-spacing: 0.3em; text-transform: uppercase; color: rgba(255, 255, 255, 0.7); margin-bottom: 1rem;">{{ $ctaSubtitle }}</p>
    <h2 class="cta-banner-title text-balance">{{ $ctaTitle }}</h2>
    <p class="cta-banner-desc">{{ $ctaContent }}</p>
    <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 1rem;">
      <button type="button" class="btn-white" onclick="openAppointmentModal()">
        <i data-lucide="calendar" style="width: 18px; height: 18px;"></i>
        <span>{{ $ctaPrimaryBtn }}</span>
      </button>
      <a href="tel:{{ $settings['phone'] ?? '+918879550581' }}" class="btn-secondary btn-secondary-white">
        <i data-lucide="phone" style="width: 18px; height: 18px;"></i>
        <span>{{ $ctaSecondaryBtn }}</span>
      </a>
    </div>
  </div>
</section>

<!-- Video Lightbox Popup Modal -->
<div class="video-lightbox-overlay" id="homeVideoLightbox" onclick="if(event.target === this) closeHomeVideoModal()">
  <div class="video-lightbox-card">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.1);">
      <h4 id="homeVideoModalTitle" style="color: #ffffff; font-family: var(--font-serif); font-size: 1.05rem; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 65%;">Clinical Video</h4>
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <a id="homeVideoDirectLink" href="#" target="_blank" class="btn btn-outline-gold btn-xs" style="padding: 4px 10px; font-size: 0.75rem; text-decoration: none; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;">
          <span>Watch on YouTube</span>
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
        </a>
        <button type="button" class="video-lightbox-close" onclick="closeHomeVideoModal()" aria-label="Close video player" style="position: static; font-size: 1.5rem; line-height: 1; color: #fff;">&times;</button>
      </div>
    </div>
    <div class="video-iframe-wrap">
      <iframe id="homeVideoIframe" src="" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
let videoAutoplayInterval = null;
let currentSlideIndex = 0;

window.goToVideoSlide = function(index) {
    const track = document.getElementById('homeVideoCarouselTrack');
    if (!track) return;
    const slides = track.querySelectorAll('.video-carousel-slide');
    if (!slides.length) return;
    
    currentSlideIndex = (index + slides.length) % slides.length;
    const targetSlide = slides[currentSlideIndex];
    if (targetSlide) {
        track.scrollTo({ left: targetSlide.offsetLeft - track.offsetLeft, behavior: 'smooth' });
    }
    window.updateCarouselDots();
};

window.slideVideoCarousel = function(direction) {
    window.goToVideoSlide(currentSlideIndex + direction);
};

window.updateCarouselDots = function() {
    const track = document.getElementById('homeVideoCarouselTrack');
    const dotsContainer = document.getElementById('homeVideoCarouselDots');
    if (!track || !dotsContainer) return;
    
    const dots = dotsContainer.querySelectorAll('.carousel-dot');
    const slides = track.querySelectorAll('.video-carousel-slide');
    if (!slides.length || !dots.length) return;

    const scrollLeft = track.scrollLeft;
    let closestIdx = 0;
    let minDistance = Infinity;

    slides.forEach((slide, idx) => {
        const slideLeft = slide.offsetLeft - track.offsetLeft;
        const dist = Math.abs(scrollLeft - slideLeft);
        if (dist < minDistance) {
            minDistance = dist;
            closestIdx = idx;
        }
    });

    currentSlideIndex = closestIdx;
    dots.forEach((dot, idx) => {
        dot.classList.toggle('active', idx === closestIdx);
    });
};

window.startVideoAutoplay = function() {
    window.stopVideoAutoplay();
    const track = document.getElementById('homeVideoCarouselTrack');
    if (!track) return;
    const slides = track.querySelectorAll('.video-carousel-slide');
    if (slides.length <= 3) return; // Only autoplay if more than 3

    videoAutoplayInterval = setInterval(() => {
        window.slideVideoCarousel(1);
    }, 4000);
};

window.stopVideoAutoplay = function() {
    if (videoAutoplayInterval) {
        clearInterval(videoAutoplayInterval);
        videoAutoplayInterval = null;
    }
};

window.openHomeVideoModal = function(youtubeId, title) {
    if (!youtubeId) return;
    const match = String(youtubeId).match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=|shorts\/))([\w-]{11})/i);
    const cleanId = match ? match[1] : youtubeId;

    const overlay = document.getElementById('homeVideoLightbox');
    const iframe = document.getElementById('homeVideoIframe');
    const titleEl = document.getElementById('homeVideoModalTitle');
    const directLink = document.getElementById('homeVideoDirectLink');

    if (titleEl) titleEl.textContent = title || 'Clinical Video Demonstration';
    if (directLink) directLink.href = `https://www.youtube.com/watch?v=${cleanId}`;

    if (overlay && iframe) {
        iframe.src = `https://www.youtube.com/embed/${cleanId}?autoplay=1&enablejsapi=1&rel=0`;
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
        window.stopVideoAutoplay();
    }
};

window.closeHomeVideoModal = function() {
    const overlay = document.getElementById('homeVideoLightbox');
    const iframe = document.getElementById('homeVideoIframe');
    if (overlay && iframe) {
        iframe.src = '';
        overlay.classList.remove('open');
        document.body.style.overflow = '';
        window.startVideoAutoplay();
    }
};

// --- TESTIMONIALS CAROUSEL (10s AUTOPLAY) ---
let testimonialAutoplayInterval = null;
let currentTestimonialIndex = 0;

window.goToTestimonialSlide = function(index) {
    const track = document.getElementById('homeTestimonialCarouselTrack');
    if (!track) return;
    const slides = track.querySelectorAll('.testimonial-carousel-slide');
    if (!slides.length) return;

    currentTestimonialIndex = (index + slides.length) % slides.length;
    const targetSlide = slides[currentTestimonialIndex];
    if (targetSlide) {
        track.scrollTo({ left: targetSlide.offsetLeft - track.offsetLeft, behavior: 'smooth' });
    }
    window.updateTestimonialDots();
};

window.slideTestimonialCarousel = function(direction) {
    window.goToTestimonialSlide(currentTestimonialIndex + direction);
};

window.updateTestimonialDots = function() {
    const track = document.getElementById('homeTestimonialCarouselTrack');
    const dotsContainer = document.getElementById('homeTestimonialCarouselDots');
    if (!track || !dotsContainer) return;

    const dots = dotsContainer.querySelectorAll('.carousel-dot');
    const slides = track.querySelectorAll('.testimonial-carousel-slide');
    if (!slides.length || !dots.length) return;

    const scrollLeft = track.scrollLeft;
    let closestIdx = 0;
    let minDistance = Infinity;

    slides.forEach((slide, idx) => {
        const slideLeft = slide.offsetLeft - track.offsetLeft;
        const dist = Math.abs(scrollLeft - slideLeft);
        if (dist < minDistance) {
            minDistance = dist;
            closestIdx = idx;
        }
    });

    currentTestimonialIndex = closestIdx;
    dots.forEach((dot, idx) => {
        dot.classList.toggle('active', idx === closestIdx);
    });
};

window.startTestimonialAutoplay = function() {
    window.stopTestimonialAutoplay();
    const track = document.getElementById('homeTestimonialCarouselTrack');
    if (!track) return;
    const slides = track.querySelectorAll('.testimonial-carousel-slide');
    if (slides.length <= 3) return; // Only autoplay if more than 3

    testimonialAutoplayInterval = setInterval(() => {
        window.slideTestimonialCarousel(1);
    }, 10000); // 10 seconds auto transition
};

window.stopTestimonialAutoplay = function() {
    if (testimonialAutoplayInterval) {
        clearInterval(testimonialAutoplayInterval);
        testimonialAutoplayInterval = null;
    }
};

document.addEventListener('DOMContentLoaded', function() {
    // Video Carousel Listeners
    const vTrack = document.getElementById('homeVideoCarouselTrack');
    const vContainer = document.querySelector('.video-carousel-container');

    if (vTrack) {
        vTrack.addEventListener('scroll', window.updateCarouselDots, { passive: true });
        window.updateCarouselDots();
    }

    if (vContainer) {
        vContainer.addEventListener('mouseenter', window.stopVideoAutoplay);
        vContainer.addEventListener('mouseleave', window.startVideoAutoplay);
        vContainer.addEventListener('touchstart', window.stopVideoAutoplay, { passive: true });
        vContainer.addEventListener('touchend', window.startVideoAutoplay, { passive: true });
    }

    window.startVideoAutoplay();

    // Testimonial Carousel Listeners
    const tTrack = document.getElementById('homeTestimonialCarouselTrack');
    const tContainer = document.querySelector('.testimonial-carousel-container');

    if (tTrack) {
        tTrack.addEventListener('scroll', window.updateTestimonialDots, { passive: true });
        window.updateTestimonialDots();
    }

    if (tContainer) {
        tContainer.addEventListener('mouseenter', window.stopTestimonialAutoplay);
        tContainer.addEventListener('mouseleave', window.startTestimonialAutoplay);
        tContainer.addEventListener('touchstart', window.stopTestimonialAutoplay, { passive: true });
        tContainer.addEventListener('touchend', window.startTestimonialAutoplay, { passive: true });
    }

    window.startTestimonialAutoplay();

    // Close Lightbox on Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            window.closeHomeVideoModal();
        }
    });
});
</script>
@endpush

