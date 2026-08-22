@extends('layouts.app')

@section('title', $post->title . ' | ' . ($settings['site_name'] ?? 'Lumique Aesthetic Clinic'))
@section('meta_description', $post->excerpt)
@section('header_class', '')

@section('content')
<!-- Page Hero Banner -->
<section class="page-hero">
  <div class="floating-bg-container" data-particles="8"></div>
  <div class="container-luxury" style="position: relative; z-index: 10;">
    <div style="margin-bottom: 1.25rem;">
      <a href="{{ route('blog.index') }}" style="color: var(--color-crimson); font-size: 0.875rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.35rem; text-decoration: none;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        <span>Back to All Articles</span>
      </a>
    </div>
    <div style="max-width: 50rem;">
      <span class="section-label">{{ strtoupper($post->category->name ?? 'SKINCARE SCIENCE') }}</span>
      <h1 class="heading-1 text-balance" style="margin-bottom: 1.5rem;">{{ $post->title }}</h1>
      <div style="display: flex; gap: 1.5rem; color: var(--color-charcoal-muted); font-size: 0.875rem; flex-wrap: wrap;">
        <span>Published {{ $post->published_at ? $post->published_at->format('F d, Y') : $post->created_at->format('F d, Y') }}</span>
        <span>&bull;</span>
        <span>{{ $post->read_time_minutes ?? 5 }} min read</span>
        <span>&bull;</span>
        <span>By {{ $post->author->name ?? 'Dr. Alisha Vance' }}</span>
      </div>
    </div>
  </div>
</section>

<!-- Article Reader Content -->
<section class="section-padding" style="background-color: #ffffff;">
  <div class="container-luxury">
    <div style="max-width: 52rem; margin: 0 auto;">
      @if($post->featured_image)
      <div style="border-radius: 8px; overflow: hidden; margin-bottom: 2.5rem; box-shadow: var(--shadow-luxury); border: 1px solid var(--color-border);">
        <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" style="width: 100%; height: auto; max-height: 480px; object-fit: cover; display: block;">
      </div>
      @endif

      <!-- Rich Formatted Article Content -->
      <div class="article-rich-content" style="font-size: 1.05rem; line-height: 1.9; color: var(--color-charcoal);">
        {!! $post->content !!}
      </div>

      <!-- Author Bio Box -->
      <div class="luxury-card" style="margin-top: 3.5rem; padding: 2rem; display: flex; gap: 1.5rem; align-items: center; border-radius: 6px; border: 1px solid var(--color-border); background: var(--color-ivory);">
        <img src="https://images.pexels.com/photos/32160039/pexels-photo-32160039.jpeg?auto=compress&cs=tinysrgb&w=400" alt="Author" style="width: 72px; height: 72px; border-radius: 50%; object-fit: cover; border: 2px solid var(--color-gold); flex-shrink: 0;">
        <div>
          <strong style="font-family: var(--font-serif); font-size: 1.15rem; display: block; color: var(--color-charcoal);">Clinically Reviewed by Dr. Alisha Vance, MD</strong>
          <p class="body-text" style="font-size: 0.85rem; margin-top: 0.25rem; color: var(--color-charcoal-muted); line-height: 1.5;">Medical Director & Lead Dermatologist at Lumique Aesthetic Clinic, Bandra West, Mumbai.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<style>
.article-rich-content h1,
.article-rich-content h2,
.article-rich-content h3,
.article-rich-content h4 {
    font-family: var(--font-serif);
    color: var(--color-charcoal);
    margin-top: 2rem;
    margin-bottom: 0.75rem;
    line-height: 1.35;
}
.article-rich-content h1 { font-size: 1.8rem; }
.article-rich-content h2 { font-size: 1.5rem; color: var(--color-burgundy); }
.article-rich-content h3 { font-size: 1.25rem; }
.article-rich-content p {
    margin-bottom: 1.25rem;
    line-height: 1.85;
}
.article-rich-content ul,
.article-rich-content ol {
    margin: 1rem 0 1.5rem 1.5rem;
    padding-left: 1rem;
}
.article-rich-content li {
    margin-bottom: 0.5rem;
}
.article-rich-content blockquote {
    border-left: 4px solid var(--color-crimson, #8B1538);
    background: var(--color-ivory);
    padding: 1rem 1.5rem;
    margin: 1.75rem 0;
    font-style: italic;
    font-family: var(--font-serif);
    border-radius: 0 6px 6px 0;
}
.article-rich-content hr {
    border: none;
    border-top: 1px solid var(--color-border);
    margin: 2rem 0;
}
.article-rich-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 1.5rem 0;
    box-shadow: var(--shadow-sm);
}
.article-rich-content a {
    color: var(--color-crimson);
    text-decoration: underline;
    font-weight: 500;
}
</style>
@endsection
