@extends('layouts.app')

@section('title', 'Clinical Videos & Procedure Demonstrations | ' . ($settings['site_name'] ?? 'Lumique Aesthetic Clinic'))
@section('header_class', '')

@section('content')
<!-- Page Hero Banner -->
<section class="page-hero">
  <div class="floating-bg-container" data-particles="8"></div>
  <div class="container-luxury" style="position: relative; z-index: 10;">
    <div class="reveal" style="max-width: 44rem;">
      <span class="section-label">Video Library</span>
      <h1 class="heading-1 text-balance" style="margin-bottom: 1.5rem;">Procedure Demonstrations & Doctor Insights</h1>
      <p class="body-text">
        Watch Dr. Alisha Vance explain clinical treatment mechanics, patient transformations, and skincare science.
      </p>
    </div>
  </div>
</section>

<!-- Videos Grid Section -->
<section class="section-padding" style="background-color: #ffffff;">
  <div class="container-luxury">
    <div class="grid-3">
      @foreach($videos as $vid)
      @php
          $vYtId = $vid->youtube_video_id;
          if (!$vYtId && $vid->youtube_url) {
              preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $vid->youtube_url, $m);
              $vYtId = $m[1] ?? 'M7lc1UVf-VE';
          }
          $vThumb = (!empty($vid->thumbnail) && !str_contains($vid->thumbnail, 'pexels')) ? $vid->thumbnail : "https://img.youtube.com/vi/{$vYtId}/hqdefault.jpg";
      @endphp
      <div class="luxury-card reveal" style="overflow: hidden; border-radius: 6px; cursor: pointer; display: flex; flex-direction: column; height: 100%; border: 1px solid var(--color-border); box-shadow: var(--shadow-sm);" onclick="playVideoModal('{{ $vYtId }}', '{{ addslashes($vid->title) }}')">
        <div style="position: relative; overflow: hidden; height: 220px; background: #000;">
          <img src="{{ $vThumb }}" alt="{{ $vid->title }}" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.9; transition: transform 0.4s ease;">
          <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.25); display: flex; align-items: center; justify-content: center; pointer-events: none;">
            <div style="width: 52px; height: 52px; border-radius: 50%; background: #e50914; display: flex; align-items: center; justify-content: center; color: #fff; box-shadow: 0 4px 18px rgba(0,0,0,0.45); pointer-events: none;">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor"><polygon points="6 3 20 12 6 21 6 3"></polygon></svg>
            </div>
          </div>
          @if($vid->duration)
          <span style="position: absolute; bottom: 0.75rem; right: 0.75rem; background: rgba(0,0,0,0.85); color: #fff; font-size: 0.75rem; padding: 2px 8px; border-radius: 3px; font-weight: 500;">
            {{ $vid->duration }}
          </span>
          @endif
        </div>
        <div style="padding: 1.5rem; flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
          <div>
            <h3 style="font-family: var(--font-serif); font-size: 1.15rem; margin-bottom: 0.5rem; line-height: 1.4; color: var(--color-charcoal);">{{ $vid->title }}</h3>
            <p style="font-size: 0.85rem; color: var(--color-charcoal-muted); line-height: 1.6; margin-bottom: 0;">{{ $vid->description }}</p>
          </div>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- Luxury Video Lightbox Popup Modal -->
<div class="video-lightbox-overlay" id="videoLightboxModal" onclick="if(event.target === this) closeVideoModal()">
  <div class="video-lightbox-card">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.1);">
      <h4 id="videoModalTitle" style="color: #ffffff; font-family: var(--font-serif); font-size: 1.05rem; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 65%;">Clinical Video</h4>
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <a id="videoDirectLink" href="#" target="_blank" class="btn btn-outline-gold btn-xs" style="padding: 4px 10px; font-size: 0.75rem; text-decoration: none; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;">
          <span>Watch on YouTube</span>
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
        </a>
        <button type="button" class="video-lightbox-close" onclick="closeVideoModal()" aria-label="Close video player" style="position: static; font-size: 1.5rem; line-height: 1; color: #fff; background: transparent; border: none; cursor: pointer;">&times;</button>
      </div>
    </div>
    <div class="video-iframe-wrap">
      <iframe id="videoPlayerIframe" src="" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  window.playVideoModal = function(videoId, title) {
    if (!videoId) return;
    const match = String(videoId).match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=|shorts\/))([\w-]{11})/i);
    const cleanId = match ? match[1] : videoId;

    const overlay = document.getElementById('videoLightboxModal');
    const iframe = document.getElementById('videoPlayerIframe');
    const titleEl = document.getElementById('videoModalTitle');
    const directLink = document.getElementById('videoDirectLink');

    if (titleEl) titleEl.textContent = title || 'Clinical Video Demonstration';
    if (directLink) directLink.href = `https://www.youtube.com/watch?v=${cleanId}`;

    if (overlay && iframe) {
      iframe.src = `https://www.youtube.com/embed/${cleanId}?autoplay=1&enablejsapi=1&rel=0`;
      overlay.classList.add('open');
      document.body.style.overflow = 'hidden';
    }
  };

  window.closeVideoModal = function() {
    const overlay = document.getElementById('videoLightboxModal');
    const iframe = document.getElementById('videoPlayerIframe');
    if (overlay && iframe) {
      iframe.src = '';
      overlay.classList.remove('open');
      document.body.style.overflow = '';
    }
  };

  document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        window.closeVideoModal();
      }
    });
  });
</script>
@endpush
