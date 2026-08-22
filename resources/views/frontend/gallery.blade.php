@extends('layouts.app')

@section('title', 'Before & After Transformations Gallery | ' . ($settings['site_name'] ?? 'Lumique Aesthetic Clinic'))
@section('header_class', '')

@section('content')
<!-- Page Hero Banner -->
<section class="page-hero">
  <div class="floating-bg-container" data-particles="8"></div>
  <div class="container-luxury" style="position: relative; z-index: 10;">
    <div class="reveal" style="max-width: 44rem;">
      <span class="section-label">Transformations Gallery</span>
      <h1 class="heading-1 text-balance" style="margin-bottom: 1.5rem;">Authentic Clinical Results & Patient Outcomes</h1>
      <p class="body-text">
        Every case represents real patients treated with customized medical protocols under board-certified dermatological supervision.
      </p>
    </div>
  </div>
</section>

<!-- Gallery Cases -->
<section class="section-padding" style="background-color: #ffffff;">
  <div class="container-luxury">
    <!-- Category Filter Tabs (Only categories with active cases) -->
    <div style="display: flex; gap: 0.75rem; justify-content: center; flex-wrap: wrap; margin-bottom: 3.5rem;">
      <button type="button" class="btn-primary btn-sm gallery-filter-btn active" onclick="filterGalleryFrontend('all', this)">
        All Transformations ({{ $galleryItems->count() }})
      </button>
      @php
          $activeCategorySlugs = $galleryItems->pluck('category')->unique()->filter()->toArray();
      @endphp
      @foreach($categories as $cat)
        @if(in_array($cat->slug, $activeCategorySlugs))
        <button type="button" class="btn-secondary btn-sm gallery-filter-btn" onclick="filterGalleryFrontend('{{ strtolower($cat->slug) }}', this)">
          {{ $cat->name }}
        </button>
        @endif
      @endforeach
    </div>

    <!-- 3-Column Luxury Case Grid -->
    <div class="grid-3" id="galleryGrid">
      @forelse($galleryItems as $case)
      <div class="luxury-card reveal gallery-case-card" data-category="{{ strtolower($case->category ?? 'general') }}" style="overflow: hidden; border-radius: 6px; display: flex; flex-direction: column; justify-content: space-between; border: 1px solid var(--color-border); background: #ffffff;">
        <div>
          <!-- Before & After Comparison Photos -->
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 3px; background: #e2e8f0;">
            <div style="position: relative; height: 210px; overflow: hidden;">
              <img src="{{ $case->image_before ?: ($case->image ?: '/images/logo.jpeg') }}" alt="Before {{ $case->title }}" style="width: 100%; height: 100%; object-fit: cover; display: block;" loading="lazy">
              <span style="position: absolute; bottom: 0.6rem; left: 0.6rem; background: rgba(18, 18, 22, 0.85); color: #ffffff; font-size: 0.65rem; font-weight: 700; padding: 2px 7px; border-radius: 3px; letter-spacing: 0.08em;">BEFORE</span>
            </div>
            <div style="position: relative; height: 210px; overflow: hidden;">
              <img src="{{ $case->image_after ?: ($case->image ?: '/images/logo.jpeg') }}" alt="After {{ $case->title }}" style="width: 100%; height: 100%; object-fit: cover; display: block;" loading="lazy">
              <span style="position: absolute; bottom: 0.6rem; right: 0.6rem; background: var(--color-crimson); color: #ffffff; font-size: 0.65rem; font-weight: 700; padding: 2px 7px; border-radius: 3px; letter-spacing: 0.08em;">AFTER</span>
            </div>
          </div>

          <div style="padding: 1.5rem;">
            <span style="font-size: 0.75rem; color: var(--color-crimson); font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 0.35rem;">
              {{ $case->treatment_name ?: ($categories->firstWhere('slug', $case->category)->name ?? ucfirst(str_replace('-', ' ', $case->category ?? 'Clinical Case'))) }}
            </span>
            <h3 style="font-family: var(--font-serif); font-size: 1.2rem; color: var(--color-charcoal); margin-bottom: 0.5rem; line-height: 1.35;">
              {{ $case->title }}
            </h3>
            @if(!empty($case->description))
            <p style="font-size: 0.85rem; color: var(--color-charcoal-muted); line-height: 1.6; margin-bottom: 0;">
              {{ $case->description }}
            </p>
            @endif
          </div>
        </div>

        <div style="padding: 1rem 1.5rem; border-top: 1px solid var(--color-border); background: var(--color-ivory); display: flex; justify-content: space-between; align-items: center; font-size: 0.8rem;">
          <span style="color: var(--color-charcoal-muted);">Category: <strong style="color: var(--color-charcoal);">{{ $categories->firstWhere('slug', $case->category)->name ?? ucfirst(str_replace('-', ' ', $case->category ?? 'General')) }}</strong></span>
          <button type="button" class="btn btn-outline-gold btn-xs" onclick="openAppointmentModal('{{ addslashes($case->treatment_name ?: $case->title) }}')" style="font-size: 0.75rem; padding: 3px 8px;">
            Book Consultation
          </button>
        </div>
      </div>
      @empty
      <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 0;">
        <p class="body-text">No clinical transformations found in the gallery.</p>
      </div>
      @endforelse
    </div>
  </div>
</section>

@push('scripts')
<script>
function filterGalleryFrontend(category, btn) {
    document.querySelectorAll('.gallery-filter-btn').forEach(b => {
        b.classList.remove('btn-primary', 'active');
        b.classList.add('btn-secondary');
    });
    btn.classList.remove('btn-secondary');
    btn.classList.add('btn-primary', 'active');

    const cards = document.querySelectorAll('.gallery-case-card');
    cards.forEach(card => {
        const cardCat = card.getAttribute('data-category') || '';
        if (category === 'all' || cardCat === category) {
            card.style.display = 'flex';
        } else {
            card.style.display = 'none';
        }
    });
}
</script>
@endpush
@endsection
