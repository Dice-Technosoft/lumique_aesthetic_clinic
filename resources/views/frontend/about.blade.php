@extends('layouts.app')

@section('title', 'About Our Medical Team | ' . ($settings['site_name'] ?? 'Lumique Aesthetic Clinic'))
@section('header_class', '')

@section('content')
<!-- Page Hero Banner -->
<section class="page-hero">
  <div class="floating-bg-container" data-particles="8"></div>
  <div class="container-luxury" style="position: relative; z-index: 10;">
    <div class="reveal" style="max-width: 46rem;">
      <span class="section-label">About Lumique</span>
      <h1 class="heading-1 text-balance" style="margin-bottom: 1.5rem;">
        {{ $settings['about_hero_title'] ?? 'A Clinic Built on Trust, Medical Science & Compassionate Artistry' }}
      </h1>
      <p class="body-text">
        {{ $settings['about_hero_description'] ?? (($settings['site_name'] ?? 'Lumique Aesthetic Clinic') . ' was founded with a singular vision: to bring together board-certified dermatological science with refined aesthetic artistry in an uplifting, patient-first sanctuary in Bandra West, Mumbai.') }}
      </p>
    </div>
  </div>
</section>

<!-- Clinic Story Section -->
<section class="section-padding" style="background-color: #ffffff; position: relative;">
  <div class="floating-bg-container" data-particles="6"></div>
  <div class="container-luxury" style="position: relative; z-index: 10;">
    <div class="grid-2" style="align-items: center; gap: 4rem;">
      <div class="reveal">
        <div style="position: relative;">
          <div style="border-radius: 0.5rem; overflow: hidden; box-shadow: var(--shadow-luxury);">
            <img src="{{ $settings['about_image_1'] ?? 'https://images.pexels.com/photos/11024139/pexels-photo-11024139.jpeg?auto=compress&cs=tinysrgb&w=800' }}" alt="Clinic Ambience" style="width: 100%; aspect-ratio: 4/5; object-fit: cover;">
          </div>
          <div style="position: absolute; bottom: -2rem; right: -2rem; width: 190px; height: 190px; border: 6px solid #fff; border-radius: 0.5rem; overflow: hidden; box-shadow: var(--shadow-hover);" class="animate-float">
            <img src="{{ $settings['about_image_2'] ?? 'https://images.pexels.com/photos/7108264/pexels-photo-7108264.jpeg?auto=compress&cs=tinysrgb&w=400' }}" alt="Doctor Consultation" style="width: 100%; height: 100%; object-fit: cover;">
          </div>
        </div>
      </div>

      <div class="reveal delay-1">
        <span class="section-label">{{ $settings['about_story_subtitle'] ?? 'Our Story' }}</span>
        <h2 class="heading-2" style="margin-bottom: 1.5rem;">{{ $settings['about_story_title'] ?? 'Aesthetic Medicine Refined' }}</h2>
        <p class="body-text" style="margin-bottom: 1.25rem;">
          {{ $settings['about_story_p1'] ?? 'We believe that true aesthetic confidence does not stem from dramatic alterations, but rather from celebrating and rejuvenating your authentic self.' }}
        </p>
        <p class="body-text" style="margin-bottom: 2rem;">
          {{ $settings['about_story_p2'] ?? 'From multi-wavelength laser technologies to autologous hair therapy and gentle facial contouring, every protocol is backed by strict medical guidelines, cutting-edge equipment, and continuous patient dialogue.' }}
        </p>

        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem;">
          <div style="background-color: var(--color-ivory); padding: 1.25rem; border: 1px solid var(--color-border); border-radius: 4px;">
            <div style="width: 2.25rem; height: 2.25rem; background-color: var(--color-soft-red); color: var(--color-crimson); display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem; border-radius: 4px;">
              <i data-lucide="target" style="width: 16px; height: 16px;"></i>
            </div>
            <h4 style="font-family: var(--font-serif); font-size: 0.95rem; margin-bottom: 0.25rem; color: var(--color-charcoal);">{{ $settings['about_mission_title'] ?? 'Our Mission' }}</h4>
            <p style="font-size: 0.8rem; color: var(--color-charcoal-muted); line-height: 1.5;">{{ $settings['about_mission_desc'] ?? 'Deliver safe, natural, and personalized dermatological outcomes.' }}</p>
          </div>

          <div style="background-color: var(--color-ivory); padding: 1.25rem; border: 1px solid var(--color-border); border-radius: 4px;">
            <div style="width: 2.25rem; height: 2.25rem; background-color: var(--color-soft-red); color: var(--color-crimson); display: flex; align-items: center; justify-content: center; margin-bottom: 0.75rem; border-radius: 4px;">
              <i data-lucide="eye" style="width: 16px; height: 16px;"></i>
            </div>
            <h4 style="font-family: var(--font-serif); font-size: 0.95rem; margin-bottom: 0.25rem; color: var(--color-charcoal);">{{ $settings['about_vision_title'] ?? 'Our Vision' }}</h4>
            <p style="font-size: 0.8rem; color: var(--color-charcoal-muted); line-height: 1.5;">{{ $settings['about_vision_desc'] ?? 'Be the most trusted, evidence-based aesthetic clinic in Mumbai.' }}</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Dynamic Medical Specialists Section -->
<section class="section-padding" style="background-color: var(--color-ivory); position: relative;">
  <div class="floating-bg-container" data-particles="6"></div>
  <div class="container-luxury" style="position: relative; z-index: 10;">
    <div class="reveal text-center" style="max-width: 42rem; margin: 0 auto 3.5rem; text-align: center;">
      <span class="section-label">Medical Specialists</span>
      <h2 class="heading-2">Led by Board-Certified Dermatologists</h2>
      <p style="color: var(--color-charcoal-muted); font-size: 0.95rem; margin-top: 0.5rem;">
        Meet the seasoned dermatologists and trichology specialists dedicated to your transformation.
      </p>
    </div>

    <div class="grid-2" style="gap: 2.5rem;">
      @foreach($team as $member)
      <div class="luxury-card reveal" style="padding: 2.5rem; display: flex; flex-direction: column; justify-content: space-between; border-radius: 8px;">
        <div>
          <div style="display: flex; gap: 1.5rem; align-items: center; margin-bottom: 1.5rem;">
            <div style="width: 90px; height: 90px; border-radius: 50%; overflow: hidden; flex-shrink: 0; border: 3px solid var(--color-gold); box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
              @if($member->photo)
                <img src="{{ $member->photo }}" alt="{{ $member->name }}" style="width: 100%; height: 100%; object-fit: cover;">
              @else
                <div style="width: 100%; height: 100%; background: rgba(139, 21, 56, 0.1); color: var(--color-crimson); display: flex; align-items: center; justify-content: center; font-size: 2rem;">
                  🩺
                </div>
              @endif
            </div>
            <div>
              <h3 style="font-family: var(--font-serif); font-size: 1.25rem; margin-bottom: 0.25rem; color: var(--color-charcoal);">{{ $member->name }}</h3>
              <p style="color: var(--color-crimson); font-size: 0.85rem; font-weight: 600; margin-bottom: 0.25rem;">{{ $member->designation }}</p>
              <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                <span class="badge badge-gold" style="font-size: 0.7rem;">{{ $member->experience_years ? $member->experience_years.'+ Years Exp.' : '10+ Years Exp.' }}</span>
                @if($member->department)
                  <span class="text-muted" style="font-size: 0.75rem;">&bull; {{ $member->department }}</span>
                @endif
              </div>
            </div>
          </div>

          <!-- Credentials / Degrees -->
          <div style="background: rgba(197, 160, 89, 0.08); border-left: 3px solid var(--color-gold); padding: 0.65rem 0.85rem; border-radius: 0 4px 4px 0; margin-bottom: 1.25rem;">
            <small style="display: block; font-weight: 600; font-size: 0.78rem; color: var(--color-burgundy);">
              🎓 {{ $member->qualification }}
            </small>
          </div>

          <p class="body-text" style="font-size: 0.875rem; line-height: 1.6; margin-bottom: 1.5rem;">
            {{ $member->short_bio ?? $member->full_bio ?? $member->bio }}
          </p>
        </div>

        <div style="border-top: 1px solid var(--color-border); padding-top: 1.25rem; display: flex; justify-content: space-between; align-items: center;">
          <button type="button" class="btn-primary btn-sm" onclick="openAppointmentModal()">Consult Specialist</button>
          <button type="button" class="btn-secondary btn-sm" onclick="openAppointmentModal()">Book Slot</button>
        </div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- Dynamic Patient Stories on About Page -->
@if(isset($testimonials) && $testimonials->isNotEmpty())
<section class="section-padding" style="background-color: #ffffff; position: relative;">
  <div class="floating-bg-container" data-particles="6"></div>
  <div class="container-luxury" style="position: relative; z-index: 10;">
    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: flex-end; margin-bottom: 3rem; gap: 1.5rem;">
      <div class="reveal" style="max-width: 40rem;">
        <span class="section-label">Patient Stories</span>
        <h2 class="heading-2">Trusted by People Like You</h2>
      </div>

      @if($testimonials->count() > 3)
      <div class="reveal" style="display: flex; gap: 0.75rem; align-items: center;">
        <button type="button" class="carousel-nav-btn" onclick="window.slideAboutTestimonialCarousel(-1)" aria-label="Previous Testimonials" style="background: rgba(0,0,0,0.06); border-color: rgba(0,0,0,0.12); color: var(--color-charcoal);">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
        </button>
        <button type="button" class="carousel-nav-btn" onclick="window.slideAboutTestimonialCarousel(1)" aria-label="Next Testimonials" style="background: rgba(0,0,0,0.06); border-color: rgba(0,0,0,0.12); color: var(--color-charcoal);">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
        </button>
      </div>
      @endif
    </div>

    @if($testimonials->count() > 3)
      <!-- Carousel format (more than 3 patient reviews) -->
      <div class="testimonial-carousel-container" style="position: relative; width: 100%;">
        <div class="testimonial-carousel-track" id="aboutTestimonialCarouselTrack">
          @foreach($testimonials as $index => $testimonial)
          <div class="testimonial-carousel-slide" data-slide-index="{{ $index }}">
            <div class="luxury-card" style="padding: 2rem; border-radius: 6px; display: flex; flex-direction: column; justify-content: space-between; height: 100%; min-height: 250px; background: var(--color-ivory); box-shadow: var(--shadow-sm); border: 1px solid var(--color-border);">
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
                <div style="width: 42px; height: 42px; border-radius: 50%; background-color: {{ $avatar['bg'] }}; color: {{ $avatar['text'] }}; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; letter-spacing: 0.5px; flex-shrink: 0; box-shadow: 0 2px 6px rgba(0,0,0,0.12);">
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
        <div class="luxury-card reveal" style="padding: 2rem; border-radius: 4px; display: flex; flex-direction: column; justify-content: space-between; background: var(--color-ivory);">
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
            <div style="width: 42px; height: 42px; border-radius: 50%; background-color: {{ $avatar['bg'] }}; color: {{ $avatar['text'] }}; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 0.85rem; letter-spacing: 0.5px; flex-shrink: 0; box-shadow: 0 2px 6px rgba(0,0,0,0.12);">
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
@endif
@endsection

@push('scripts')
<script>
let aboutTestimonialAutoplayInterval = null;
let currentAboutTestimonialIndex = 0;

window.goToAboutTestimonialSlide = function(index) {
    const track = document.getElementById('aboutTestimonialCarouselTrack');
    if (!track) return;
    const slides = track.querySelectorAll('.testimonial-carousel-slide');
    if (!slides.length) return;

    currentAboutTestimonialIndex = (index + slides.length) % slides.length;
    const targetSlide = slides[currentAboutTestimonialIndex];
    if (targetSlide) {
        track.scrollTo({ left: targetSlide.offsetLeft - track.offsetLeft, behavior: 'smooth' });
    }
    window.updateAboutTestimonialDots();
};

window.slideAboutTestimonialCarousel = function(direction) {
    window.goToAboutTestimonialSlide(currentAboutTestimonialIndex + direction);
};

window.updateAboutTestimonialDots = function() {
    const track = document.getElementById('aboutTestimonialCarouselTrack');
    const dotsContainer = document.getElementById('aboutTestimonialCarouselDots');
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

    currentAboutTestimonialIndex = closestIdx;
    dots.forEach((dot, idx) => {
        dot.classList.toggle('active', idx === closestIdx);
    });
};

window.startAboutTestimonialAutoplay = function() {
    window.stopAboutTestimonialAutoplay();
    const track = document.getElementById('aboutTestimonialCarouselTrack');
    if (!track) return;
    const slides = track.querySelectorAll('.testimonial-carousel-slide');
    if (slides.length <= 3) return;

    aboutTestimonialAutoplayInterval = setInterval(() => {
        window.slideAboutTestimonialCarousel(1);
    }, 10000); // 10s auto transition
};

window.stopAboutTestimonialAutoplay = function() {
    if (aboutTestimonialAutoplayInterval) {
        clearInterval(aboutTestimonialAutoplayInterval);
        aboutTestimonialAutoplayInterval = null;
    }
};

document.addEventListener('DOMContentLoaded', function() {
    const track = document.getElementById('aboutTestimonialCarouselTrack');
    const container = document.querySelector('.testimonial-carousel-container');

    if (track) {
        track.addEventListener('scroll', window.updateAboutTestimonialDots, { passive: true });
        window.updateAboutTestimonialDots();
    }

    if (container) {
        container.addEventListener('mouseenter', window.stopAboutTestimonialAutoplay);
        container.addEventListener('mouseleave', window.startAboutTestimonialAutoplay);
        container.addEventListener('touchstart', window.stopAboutTestimonialAutoplay, { passive: true });
        container.addEventListener('touchend', window.startAboutTestimonialAutoplay, { passive: true });
    }

    window.startAboutTestimonialAutoplay();
});
</script>
@endpush

