@extends('layouts.app')

@section('title', 'Skincare & Hair Science Journal | ' . ($settings['site_name'] ?? 'Lumique Aesthetic Clinic'))
@section('header_class', '')

@section('content')
<!-- Page Hero Banner -->
<section class="page-hero">
  <div class="floating-bg-container" data-particles="8"></div>
  <div class="container-luxury" style="position: relative; z-index: 10;">
    <div class="reveal" style="max-width: 44rem;">
      <span class="section-label">Medical Journal</span>
      <h1 class="heading-1 text-balance" style="margin-bottom: 1.5rem;">Dermatological Science, Insights & Homecare Advice</h1>
      <p class="body-text">
        Expert articles written and clinically reviewed by board-certified dermatologists.
      </p>
    </div>
  </div>
</section>

<!-- Blog Articles Grid -->
<section class="section-padding" style="background-color: #ffffff;">
  <div class="container-luxury">
    <div class="grid-3">
      @forelse($posts as $post)
      <article class="luxury-card reveal" style="overflow: hidden; border-radius: 4px;">
        <div style="position: relative; overflow: hidden; height: 220px;">
          <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" style="width: 100%; height: 100%; object-fit: cover;">
          <span style="position: absolute; top: 1rem; left: 1rem; background: var(--color-crimson); color: #fff; font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em; padding: 0.25rem 0.65rem;">
            {{ $post->category->name ?? 'Clinical Insights' }}
          </span>
        </div>
        <div style="padding: 1.75rem;">
          <div style="font-size: 0.75rem; color: var(--color-charcoal-muted); margin-bottom: 0.5rem;">
            {{ $post->published_at ? $post->published_at->format('M d, Y') : $post->created_at->format('M d, Y') }} &bull; {{ $post->read_time_minutes ?? 5 }} min read
          </div>
          <h3 style="font-family: var(--font-serif); font-size: 1.2rem; margin-bottom: 0.75rem; line-height: 1.35;">
            <a href="{{ route('blog.show', $post->slug) }}" style="color: var(--color-charcoal);">{{ $post->title }}</a>
          </h3>
          <p style="font-size: 0.875rem; color: var(--color-charcoal-muted); line-height: 1.6; margin-bottom: 1.25rem;">
            {{ $post->excerpt }}
          </p>
          <a href="{{ route('blog.show', $post->slug) }}" style="color: var(--color-crimson); font-size: 0.85rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.25rem;">
            <span>Read Full Article</span>
            <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
          </a>
        </div>
      </article>
      @empty
      <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 0;">
        <p class="body-text">No articles found in the skincare journal.</p>
      </div>
      @endforelse
    </div>
  </div>
</section>
@endsection
