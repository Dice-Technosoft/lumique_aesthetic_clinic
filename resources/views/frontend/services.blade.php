@extends('layouts.app')

@section('title', 'Clinical Treatments & Procedures | ' . ($settings['site_name'] ?? 'Lumique Aesthetic Clinic'))
@section('header_class', '')

@section('content')
<!-- Page Hero Banner -->
<section class="page-hero">
  <div class="floating-bg-container" data-particles="8"></div>
  <div class="container-luxury" style="position: relative; z-index: 10;">
    <div class="reveal" style="max-width: 44rem;">
      <span class="section-label">Treatments & Procedures</span>
      <h1 class="heading-1 text-balance" style="margin-bottom: 1.5rem;">Evidence-Based Dermatology & Laser Therapies</h1>
      <p class="body-text">
        Explore our curated portfolio of US-FDA approved clinical skin, hair, laser, and restorative aesthetic treatments administered in our Bandra West sanctuary.
      </p>
    </div>
  </div>
</section>

<!-- Filterable Treatments Catalog -->
<section class="section-padding" style="background-color: #ffffff;">
  <div class="container-luxury">
    <!-- Category Filter Bar (Only categories with published treatments) -->
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap; justify-content: center; margin-bottom: 3.5rem;">
      <a href="{{ route('services.index') }}" class="{{ in_array($selectedCategory, ['all', '', null]) ? 'btn-primary' : 'btn-secondary' }} btn-sm">All Treatments</a>
      @foreach($categories as $cat)
        @if(in_array($cat->slug, $activeCategorySlugs ?? []))
        <a href="{{ route('services.index', ['category' => $cat->slug]) }}" class="{{ $selectedCategory === $cat->slug ? 'btn-primary' : 'btn-secondary' }} btn-sm">
          {{ $cat->name }}
        </a>
        @endif
      @endforeach
    </div>

    <!-- Services Grid -->
    <div class="grid-3">
      @forelse($services as $service)
      <div class="luxury-card reveal" style="overflow: hidden; border-radius: 4px;">
        <div style="position: relative; overflow: hidden; height: 240px;">
          <img src="{{ $service->featured_image }}" alt="{{ $service->title }}" style="width: 100%; height: 100%; object-fit: cover;">
          <span style="position: absolute; top: 1rem; left: 1rem; background: var(--color-crimson); color: #fff; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; padding: 0.25rem 0.65rem;">
            {{ strtoupper($service->category) }}
          </span>
        </div>
        <div style="padding: 1.75rem;">
          <h3 style="font-family: var(--font-serif); font-size: 1.25rem; margin-bottom: 0.5rem;">{{ $service->title }}</h3>
          <p style="font-size: 0.875rem; color: var(--color-charcoal-muted); line-height: 1.6; margin-bottom: 1.25rem;">
            {{ $service->short_description }}
          </p>
          <div style="display: flex; align-items: center; justify-content: space-between; padding-top: 1rem; border-top: 1px solid var(--color-border);">
            <span style="font-weight: 700; color: var(--color-gold); font-size: 0.9rem;">{{ $service->price_starting_at ?? 'Consultation' }}</span>
            <a href="{{ route('services.show', $service->slug) }}" style="color: var(--color-crimson); font-size: 0.85rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">
              <span>Explore Procedure</span>
              <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
            </a>
          </div>
        </div>
      </div>
      @empty
      <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 0;">
        <p class="body-text">No clinical services found in this category.</p>
      </div>
      @endforelse
    </div>
  </div>
</section>
@endsection
