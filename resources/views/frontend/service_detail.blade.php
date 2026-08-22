@extends('layouts.app')

@section('title', $service->title . ' in Bandra West Mumbai | ' . ($settings['site_name'] ?? 'Lumique Aesthetic Clinic'))
@section('meta_description', $service->short_description)
@section('header_class', '')

@section('content')
<!-- Page Hero Banner -->
<section class="page-hero">
  <div class="floating-bg-container" data-particles="8"></div>
  <div class="container-luxury" style="position: relative; z-index: 10;">
    <div style="margin-bottom: 1.25rem;">
      <a href="{{ route('services.index') }}" style="color: var(--color-crimson); font-size: 0.875rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.35rem; text-decoration: none;">
        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
        <span>Back to All Treatments</span>
      </a>
    </div>
    <div class="grid-2" style="align-items: center; gap: 3rem;">
      <div class="reveal">
        <span class="section-label">{{ strtoupper($service->category) }} PROCEDURE</span>
        <h1 class="heading-1 text-balance" style="margin-bottom: 1rem;">{{ $service->title }}</h1>
        <p class="body-text" style="margin-bottom: 1.5rem;">{{ $service->short_description }}</p>
        
        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-bottom: 2rem;">
          <div style="background: #fff; border: 1px solid var(--color-border); padding: 0.75rem 1rem; border-radius: 4px;">
            <span style="display: block; font-size: 0.7rem; color: var(--color-charcoal-muted); text-transform: uppercase;">Duration</span>
            <strong style="font-size: 0.9rem;">{{ $service->duration ?? '45–60 Mins' }}</strong>
          </div>
          <div style="background: #fff; border: 1px solid var(--color-border); padding: 0.75rem 1rem; border-radius: 4px;">
            <span style="display: block; font-size: 0.7rem; color: var(--color-charcoal-muted); text-transform: uppercase;">Starting At</span>
            <strong style="font-size: 0.9rem; color: var(--color-gold);">{{ $service->price_starting_at ?? 'Consultation' }}</strong>
          </div>
          <div style="background: #fff; border: 1px solid var(--color-border); padding: 0.75rem 1rem; border-radius: 4px;">
            <span style="display: block; font-size: 0.7rem; color: var(--color-charcoal-muted); text-transform: uppercase;">Downtime</span>
            <strong style="font-size: 0.9rem;">{{ $service->downtime ?: 'Minimal' }}</strong>
          </div>
        </div>

        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
          <button type="button" class="btn-primary" onclick="openAppointmentModal('{{ addslashes($service->title) }}')">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
            <span>Book This Treatment</span>
          </button>
          <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $settings['whatsapp'] ?? '918879550581') }}?text=Hello%20Lumique%20Clinic,%20I%20have%20questions%20regarding%20{{ urlencode($service->title) }}." class="btn-secondary" target="_blank">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
            <span>WhatsApp Concierge</span>
          </a>
        </div>
      </div>

      <div class="reveal delay-1">
        <div style="border-radius: 8px; overflow: hidden; box-shadow: var(--shadow-luxury); border: 1px solid var(--color-border);">
          <img src="{{ $service->featured_image }}" alt="{{ $service->title }}" style="width: 100%; aspect-ratio: 4/3; object-fit: cover; display: block;">
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Deep Dive Content -->
<section class="section-padding" style="background-color: #ffffff;">
  <div class="container-luxury">
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 4rem; align-items: flex-start;">
      <div>
        <!-- Medical Overview -->
        <div style="margin-bottom: 3rem;">
          <span class="section-label">Medical Overview</span>
          <h2 class="heading-2" style="margin-bottom: 1rem;">Procedure Description & Protocol</h2>
          <p class="body-text" style="white-space: pre-line; line-height: 1.8;">{{ $service->description }}</p>
        </div>

        <!-- Treatment Sub-Images / Clinical Procedure Gallery -->
        @if(!empty($service->gallery_images) && count($service->gallery_images) > 0)
        <div style="margin-bottom: 3.5rem;">
          <span class="section-label">Clinical Gallery</span>
          <h2 class="heading-2" style="margin-bottom: 1.25rem;">Procedure Steps & Clinical Sub-Images</h2>
          <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 1rem;">
            @foreach($service->gallery_images as $gIdx => $gImg)
            <div class="luxury-card" style="border-radius: 6px; overflow: hidden; height: 170px; cursor: pointer; border: 1px solid var(--color-border); position: relative; transition: transform 0.25s ease;" onclick="openImageLightbox('{{ $gImg }}', '{{ addslashes($service->title) }} - Image {{ $gIdx + 1 }}')">
              <img src="{{ $gImg }}" alt="{{ $service->title }} sub image" style="width: 100%; height: 100%; object-fit: cover; display: block;">
              <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.2); opacity: 0; transition: opacity 0.2s ease; display: flex; align-items: center; justify-content: center; color: #fff;" onmouseenter="this.style.opacity=1" onmouseleave="this.style.opacity=0">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line><line x1="11" y1="8" x2="11" y2="14"></line><line x1="8" y1="11" x2="14" y2="11"></line></svg>
              </div>
            </div>
            @endforeach
          </div>
        </div>
        @endif

        <!-- Treatment Procedure Videos (Direct Media Upload & URLs) -->
        @php
            $allVideos = (!empty($service->gallery_videos) && is_array($service->gallery_videos)) ? $service->gallery_videos : (!empty($service->video_url) ? [$service->video_url] : []);
        @endphp
        @if(count($allVideos) > 0)
        <div style="margin-bottom: 3.5rem;">
          <span class="section-label">Video Demonstrations</span>
          <h2 class="heading-2" style="margin-bottom: 1.25rem;">{{ $service->video_title ?: 'Clinical Video Demonstrations' }}</h2>
          <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 1.25rem;">
            @foreach($allVideos as $vIdx => $vItem)
              @php
                  preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $vItem, $vMatch);
                  $vYtId = $vMatch[1] ?? (strlen($vItem) === 11 ? $vItem : null);
              @endphp
              @if($vYtId)
                <div class="luxury-card" style="position: relative; border-radius: 8px; overflow: hidden; background: #000; box-shadow: var(--shadow-luxury); cursor: pointer; border: 1px solid var(--color-border); aspect-ratio: 16/9; transition: transform 0.25s ease;" onclick="playServiceDetailVideo('{{ $vYtId }}', '{{ addslashes($service->video_title ?: $service->title) }}')">
                  <img src="https://img.youtube.com/vi/{{ $vYtId }}/hqdefault.jpg" alt="Video Demonstration" style="width: 100%; height: 100%; object-fit: cover; opacity: 0.88; display: block;">
                  <div style="position: absolute; inset: 0; background: rgba(0,0,0,0.3); display: flex; align-items: center; justify-content: center; pointer-events: none;">
                    <div style="width: 44px; height: 44px; border-radius: 50%; background: #e50914; display: flex; align-items: center; justify-content: center; color: #fff; box-shadow: 0 4px 15px rgba(0,0,0,0.5); pointer-events: none;">
                      <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor"><polygon points="6 3 20 12 6 21 6 3"></polygon></svg>
                    </div>
                  </div>
                </div>
              @else
                <div class="luxury-card" style="position: relative; border-radius: 8px; overflow: hidden; background: #000; box-shadow: var(--shadow-luxury); border: 1px solid var(--color-border); aspect-ratio: 16/9;">
                  <video src="{{ $vItem }}" controls playsinline style="width: 100%; height: 100%; object-fit: cover; display: block; background: #000;"></video>
                </div>
              @endif
            @endforeach
          </div>
        </div>
        @endif

        <!-- Clinical Benefits -->
        @if($service->benefits)
        <div style="margin-bottom: 3rem;">
          <span class="section-label">Clinical Benefits</span>
          <h2 class="heading-2" style="margin-bottom: 1.25rem;">Expected Outcomes & Results</h2>
          <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            @foreach($service->benefits as $benefit)
            <div style="display: flex; gap: 0.75rem; align-items: center; background: var(--color-ivory); padding: 0.85rem 1.25rem; border: 1px solid var(--color-border); border-radius: 4px;">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--color-crimson)" stroke-width="2" style="flex-shrink: 0;"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
              <span style="font-weight: 500; font-size: 0.95rem;">{{ $benefit }}</span>
            </div>
            @endforeach
          </div>
        </div>
        @endif

        <!-- Frequently Asked Questions -->
        @if($faqs->isNotEmpty())
        <div>
          <span class="section-label">FAQ</span>
          <h2 class="heading-2" style="margin-bottom: 1.25rem;">Frequently Asked Questions</h2>
          <div style="display: flex; flex-direction: column; gap: 1rem;">
            @foreach($faqs as $faq)
            <details style="background: var(--color-ivory); border: 1px solid var(--color-border); padding: 1.25rem; border-radius: 4px;">
              <summary style="font-family: var(--font-serif); font-size: 1.1rem; font-weight: 600; cursor: pointer; color: var(--color-charcoal);">
                {{ $faq->question }}
              </summary>
              <div style="margin-top: 0.75rem; font-size: 0.9rem; color: var(--color-charcoal-muted); line-height: 1.6;">
                {{ $faq->answer }}
              </div>
            </details>
            @endforeach
          </div>
        </div>
        @endif
      </div>

      <!-- Sticky Booking Sidebar -->
      <div class="luxury-card" style="padding: 2rem; position: sticky; top: calc(var(--header-height) + 2rem); border-radius: 4px;">
        <h3 class="heading-3" style="margin-bottom: 0.5rem;">Reserve Appointment</h3>
        <p class="body-text" style="font-size: 0.85rem; margin-bottom: 1.5rem;">Consult directly with board-certified dermatologists at our Mumbai clinic.</p>
        <button type="button" class="btn-primary" style="width: 100%; justify-content: center; margin-bottom: 1rem;" onclick="openAppointmentModal('{{ addslashes($service->title) }}')">
          Book This Treatment
        </button>
        <a href="tel:{{ $settings['phone'] ?? '+918879550581' }}" class="btn-secondary" style="width: 100%; justify-content: center;">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right: 4px;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
          <span>{{ $settings['phone'] ?? '+91 88795 50581' }}</span>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Luxury Video Lightbox Popup Modal -->
<div class="video-lightbox-overlay" id="serviceDetailVideoModal" onclick="if(event.target === this) closeServiceDetailVideo()">
  <div class="video-lightbox-card">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.25rem; border-bottom: 1px solid rgba(255,255,255,0.1);">
      <h4 id="detailVideoTitle" style="color: #ffffff; font-family: var(--font-serif); font-size: 1.05rem; margin: 0; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 65%;">Clinical Video</h4>
      <div style="display: flex; align-items: center; gap: 0.75rem;">
        <a id="detailVideoDirectLink" href="#" target="_blank" class="btn btn-outline-gold btn-xs" style="padding: 4px 10px; font-size: 0.75rem; text-decoration: none; border-radius: 4px; display: inline-flex; align-items: center; gap: 4px;">
          <span>Watch on YouTube</span>
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
        </a>
        <button type="button" class="video-lightbox-close" onclick="closeServiceDetailVideo()" aria-label="Close video player" style="position: static; font-size: 1.5rem; line-height: 1; color: #fff; background: transparent; border: none; cursor: pointer;">&times;</button>
      </div>
    </div>
    <div class="video-iframe-wrap">
      <iframe id="detailVideoIframe" src="" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>
    </div>
  </div>
</div>

<!-- Image Lightbox Modal -->
<div class="video-lightbox-overlay" id="serviceDetailImageModal" onclick="if(event.target === this) closeImageLightbox()">
  <div style="position: relative; max-width: 850px; width: 90%; margin: 2rem auto; background: #000; border-radius: 8px; overflow: hidden; border: 1px solid rgba(255,255,255,0.15);">
    <div style="display: flex; justify-content: space-between; align-items: center; padding: 0.75rem 1rem; border-bottom: 1px solid rgba(255,255,255,0.1);">
      <span id="detailImageTitle" style="color: #fff; font-size: 0.85rem;">Clinical Photo</span>
      <button type="button" onclick="closeImageLightbox()" style="color: #fff; background: transparent; border: none; font-size: 1.5rem; cursor: pointer; line-height: 1;">&times;</button>
    </div>
    <div style="display: flex; align-items: center; justify-content: center; max-height: 80vh; background: #000;">
      <img id="detailImageEl" src="" alt="Enlarged clinical photo" style="max-width: 100%; max-height: 75vh; object-fit: contain; display: block;">
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  window.playServiceDetailVideo = function(videoId, title) {
    if (!videoId) return;
    const match = String(videoId).match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=|shorts\/))([\w-]{11})/i);
    const cleanId = match ? match[1] : videoId;

    const overlay = document.getElementById('serviceDetailVideoModal');
    const iframe = document.getElementById('detailVideoIframe');
    const titleEl = document.getElementById('detailVideoTitle');
    const directLink = document.getElementById('detailVideoDirectLink');

    if (titleEl) titleEl.textContent = title || 'Clinical Video Demonstration';
    if (directLink) directLink.href = `https://www.youtube.com/watch?v=${cleanId}`;

    if (overlay && iframe) {
      iframe.src = `https://www.youtube.com/embed/${cleanId}?autoplay=1&enablejsapi=1&rel=0`;
      overlay.classList.add('open');
      document.body.style.overflow = 'hidden';
    }
  };

  window.closeServiceDetailVideo = function() {
    const overlay = document.getElementById('serviceDetailVideoModal');
    const iframe = document.getElementById('detailVideoIframe');
    if (overlay && iframe) {
      iframe.src = '';
      overlay.classList.remove('open');
      document.body.style.overflow = '';
    }
  };

  window.openImageLightbox = function(imgSrc, title) {
    const overlay = document.getElementById('serviceDetailImageModal');
    const imgEl = document.getElementById('detailImageEl');
    const titleEl = document.getElementById('detailImageTitle');
    if (overlay && imgEl) {
      imgEl.src = imgSrc;
      if (titleEl) titleEl.textContent = title || 'Clinical Photo';
      overlay.classList.add('open');
      document.body.style.overflow = 'hidden';
    }
  };

  window.closeImageLightbox = function() {
    const overlay = document.getElementById('serviceDetailImageModal');
    const imgEl = document.getElementById('detailImageEl');
    if (overlay && imgEl) {
      imgEl.src = '';
      overlay.classList.remove('open');
      document.body.style.overflow = '';
    }
  };

  document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape') {
        window.closeServiceDetailVideo();
        window.closeImageLightbox();
      }
    });
  });
</script>
@endpush
