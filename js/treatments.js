/* ==========================================================================
   Lumique Aesthetic Clinic - Treatments Catalog & Detail Viewer
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('treatments-catalog-container')) {
    renderTreatmentsCatalog();
  }
  if (document.getElementById('treatment-detail-view')) {
    renderTreatmentDetail();
  }
});

// Render Treatments on treatments.html with All Filter + Photo/Video Showcase
function renderTreatmentsCatalog() {
  const container = document.getElementById('treatments-catalog-container');
  const navContainer = document.getElementById('category-filter-nav');
  if (!container || !window.LUMIQUE_DATA) return;

  const { categories, treatments } = window.LUMIQUE_DATA;
  let activeFilter = 'all';

  // Render Category Sub-navigation (including 'All')
  if (navContainer) {
    navContainer.innerHTML = `
      <button type="button" class="category-nav-btn active" data-category="all">
        <i data-lucide="layers" style="width: 16px; height: 16px;"></i>
        <span>All Treatments</span>
      </button>
      ${categories.map(cat => `
        <button type="button" class="category-nav-btn" data-category="${cat.slug}">
          <i data-lucide="${cat.icon || 'sparkles'}" style="width: 16px; height: 16px;"></i>
          <span>${cat.name}</span>
        </button>
      `).join('')}
    `;

    // Filter Click Handlers
    const filterButtons = navContainer.querySelectorAll('.category-nav-btn');
    filterButtons.forEach(btn => {
      btn.addEventListener('click', () => {
        filterButtons.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        activeFilter = btn.getAttribute('data-category');
        renderCategories();
      });
    });
  }

  // Render Categories Function
  const renderCategories = () => {
    const visibleCategories = activeFilter === 'all' 
      ? categories 
      : categories.filter(c => c.slug === activeFilter);

    container.innerHTML = visibleCategories.map(cat => {
      const catTreatments = treatments.filter(t => t.category_id === cat.id);
      const media = cat.media || { photos: [], videos: [], beforeAfter: [] };

      return `
        <div id="${cat.slug}" class="treatment-category-block reveal active" style="margin-bottom: 5rem; scroll-margin-top: 140px;">
          <!-- Category Header -->
          <div style="border-bottom: 1px solid var(--color-border); padding-bottom: 1.5rem; margin-bottom: 2.5rem; display: flex; justify-content: space-between; align-items: flex-end;">
            <div style="max-width: 44rem;">
              <div style="display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.75rem;">
                <div style="width: 2.75rem; height: 2.75rem; background-color: var(--color-soft-red); display: flex; align-items: center; justify-content: center; color: var(--color-crimson); border-radius: 4px;">
                  <i data-lucide="${cat.icon || 'sparkles'}" style="width: 22px; height: 22px;"></i>
                </div>
                <h2 class="heading-2">${cat.name}</h2>
              </div>
              <p class="body-text" style="font-size: 0.95rem;">${cat.description}</p>
            </div>
            <span style="font-family: var(--font-serif); font-size: 2.5rem; font-weight: 700; color: var(--color-soft-red);" class="category-count">
              ${String(catTreatments.length).padStart(2, '0')}
            </span>
          </div>

          <!-- Treatment Cards Grid -->
          <div class="grid-3" style="margin-bottom: 2.5rem;">
            ${catTreatments.map(treatment => `
              <div class="treatment-card">
                <div class="treatment-card-img-wrapper">
                  <img src="${treatment.hero_image}" alt="${treatment.title}" class="treatment-card-img" loading="lazy" onerror="this.onerror=null; this.src='https://images.pexels.com/photos/3997989/pexels-photo-3997989.jpeg?auto=compress&cs=tinysrgb&w=800';">
                  <span class="treatment-card-badge">${cat.name}</span>
                </div>
                <div class="treatment-card-body">
                  <h3 class="treatment-card-title">${treatment.title}</h3>
                  <p class="treatment-card-desc">${treatment.short_intro}</p>
                  <div style="display: flex; align-items: center; justify-content: space-between; margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--color-border);">
                    <a href="treatment-detail.html?slug=${treatment.slug}" class="treatment-card-link">
                      <span>Details</span>
                      <i data-lucide="chevron-right" style="width: 14px; height: 14px;"></i>
                    </a>
                    <button type="button" class="btn-primary btn-sm" onclick="if(window.openAppointmentModal) window.openAppointmentModal('${treatment.id}')">
                      <span>Book</span>
                    </button>
                  </div>
                </div>
              </div>
            `).join('')}
          </div>

          <!-- Category Media Showcase: Photos, Videos & Results -->
          <div class="category-media-showcase">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
              <h3 class="media-section-title" style="margin-bottom: 0;">
                <i data-lucide="video" style="width: 20px; height: 20px; color: var(--color-crimson);"></i>
                <span>${cat.name} — Clinical Videos & Results</span>
              </h3>
              <span style="font-size: 0.75rem; color: var(--color-crimson); font-weight: 600; text-transform: uppercase; letter-spacing: 0.1em;">
                In-Clinic Mumbai Demonstration
              </span>
            </div>

            <div class="grid-3" style="gap: 1.5rem;">
              <!-- Video Demonstration Card -->
              ${media.videos && media.videos.length > 0 ? media.videos.map(v => `
                <div class="media-video-card" onclick="if(window.openVideoModal) window.openVideoModal('${v.videoUrl}')" title="Play Video: ${v.title}">
                  <img src="${v.thumbnail}" alt="${v.title}" loading="lazy" onerror="this.onerror=null; this.src='https://images.pexels.com/photos/3997989/pexels-photo-3997989.jpeg?auto=compress&cs=tinysrgb&w=800';">
                  <div class="media-play-badge">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><polygon points="5 3 19 12 5 21 5 3"></polygon></svg>
                  </div>
                  <div class="media-video-overlay">
                    <span style="font-size: 0.7rem; font-weight: 700; color: #F5D67D; text-transform: uppercase;">▶ Video Demo (${v.duration})</span>
                    <h4 style="font-size: 0.95rem; font-weight: 600; color: #fff; margin-top: 0.25rem;">${v.title}</h4>
                  </div>
                </div>
              `).join('') : ''}

              <!-- Clinical Photo Highlight -->
              ${media.photos && media.photos.length > 0 ? media.photos.slice(0, 1).map(p => `
                <div class="media-photo-card">
                  <img src="${p.url}" alt="${p.title}" loading="lazy" onerror="this.onerror=null; this.src='https://images.pexels.com/photos/7789640/pexels-photo-7789640.jpeg?auto=compress&cs=tinysrgb&w=800';">
                  <div style="position: absolute; bottom: 0; inset-inline: 0; background: linear-gradient(to top, rgba(0,0,0,0.85), transparent); padding: 1rem; color: #fff;">
                    <span style="font-size: 0.7rem; font-weight: 700; color: #F5D67D; text-transform: uppercase;">📷 Clinical Step</span>
                    <p style="font-size: 0.85rem; margin-top: 0.2rem; font-weight: 500;">${p.caption}</p>
                  </div>
                </div>
              `).join('') : ''}

              <!-- Clinical Before / After -->
              ${media.beforeAfter && media.beforeAfter.length > 0 ? media.beforeAfter.map(ba => `
                <div class="before-after-card">
                  <p style="font-size: 0.75rem; font-weight: 700; color: var(--color-crimson); text-transform: uppercase; margin-bottom: 0.5rem; letter-spacing: 0.05em;">
                    Clinical Outcome
                  </p>
                  <div class="before-after-grid">
                    <div class="before-after-img-wrap">
                      <img src="${ba.before}" alt="Before" loading="lazy" onerror="this.onerror=null; this.src='https://images.pexels.com/photos/3997989/pexels-photo-3997989.jpeg?auto=compress&cs=tinysrgb&w=800';">
                      <span class="before-after-tag before">Before</span>
                    </div>
                    <div class="before-after-img-wrap">
                      <img src="${ba.after}" alt="After" loading="lazy" onerror="this.onerror=null; this.src='https://images.pexels.com/photos/7789640/pexels-photo-7789640.jpeg?auto=compress&cs=tinysrgb&w=800';">
                      <span class="before-after-tag after">After</span>
                    </div>
                  </div>
                  <p style="font-size: 0.75rem; color: var(--color-charcoal-muted); margin-top: 0.5rem; text-align: center;">${ba.label}</p>
                </div>
              `).join('') : ''}
            </div>
          </div>
        </div>
      `;
    }).join('');

    if (window.initLucideIcons) window.initLucideIcons();
    if (window.initScrollReveal) window.initScrollReveal();
  };

  renderCategories();
}

// Render Treatment Detail on treatment-detail.html
function renderTreatmentDetail() {
  const container = document.getElementById('treatment-detail-view');
  if (!container || !window.LUMIQUE_DATA) return;

  const urlParams = new URLSearchParams(window.location.search);
  const slug = urlParams.get('slug') || 'hydrafacial-glow';
  const { treatments, categories } = window.LUMIQUE_DATA;

  const treatment = treatments.find(t => t.slug === slug) || treatments[0];
  const category = categories.find(c => c.id === treatment.category_id) || categories[0];
  const relatedTreatments = treatments.filter(t => t.category_id === treatment.category_id && t.id !== treatment.id).slice(0, 3);

  document.title = `${treatment.title} | Lumique Aesthetic Clinic Mumbai`;

  container.innerHTML = `
    <!-- Detail Hero -->
    <section class="page-hero" style="padding-top: 9rem; padding-bottom: 5rem;">
      <div class="floating-bg-container" data-particles="6"></div>
      <div class="container-luxury" style="position: relative; z-index: 10;">
        <div style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-charcoal-muted); margin-bottom: 1.5rem;">
          <a href="treatments.html" style="color: var(--color-crimson); font-weight: 600;">Treatments</a>
          <span>/</span>
          <span>${category.name}</span>
        </div>
        <h1 class="heading-1 text-balance" style="margin-bottom: 1.5rem; max-width: 50rem;">${treatment.title}</h1>
        <p class="body-text" style="max-width: 44rem; font-size: 1.15rem; margin-bottom: 2rem;">${treatment.short_intro}</p>
        <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
          <button type="button" class="btn-primary" onclick="if(window.openAppointmentModal) window.openAppointmentModal('${treatment.id}')">
            <i data-lucide="calendar" style="width: 18px; height: 18px;"></i>
            <span>Book Consultation for this Treatment</span>
          </button>
          <a href="https://wa.me/918879550581?text=Hi%20Lumique%20Clinic,%20I%20am%20interested%20in%20${encodeURIComponent(treatment.title)}" target="_blank" rel="noopener noreferrer" class="btn-secondary">
            <i data-lucide="message-circle" style="width: 18px; height: 18px; color: var(--color-crimson);"></i>
            <span>Inquire on WhatsApp</span>
          </a>
        </div>
      </div>
    </section>

    <!-- Treatment Overview & Visuals -->
    <section class="section-padding" style="background-color: #ffffff;">
      <div class="container-luxury">
        <div class="grid-2" style="align-items: center; gap: 4rem; margin-bottom: 5rem;">
          <div>
            <span class="section-label">Procedure Overview</span>
            <h2 class="heading-2" style="margin-bottom: 1.5rem;">Medical Precision & Tailored Delivery</h2>
            <p class="body-text" style="margin-bottom: 1.5rem;">${treatment.procedure_overview}</p>
            <div class="glass-card" style="padding: 1.5rem; border-left: 4px solid var(--color-crimson); margin-bottom: 1.5rem;">
              <h4 style="font-family: var(--font-serif); font-size: 1.1rem; margin-bottom: 0.5rem;">Who Is It For?</h4>
              <p style="font-size: 0.9rem; color: var(--color-charcoal-muted);">${treatment.who_is_it_for}</p>
            </div>
          </div>
          <div>
            <div style="border-radius: 0.75rem; overflow: hidden; box-shadow: var(--shadow-luxury); position: relative;">
              <img src="${treatment.hero_image}" alt="${treatment.title}" style="width: 100%; aspect-ratio: 4/3; object-fit: cover;" onerror="this.onerror=null; this.src='https://images.pexels.com/photos/3997989/pexels-photo-3997989.jpeg?auto=compress&cs=tinysrgb&w=800';">
              <div style="position: absolute; bottom: 1rem; right: 1rem; background-color: rgba(31, 31, 31, 0.85); color: #fff; padding: 0.5rem 1rem; font-size: 0.75rem; border-radius: 4px; backdrop-filter: blur(4px);">
                Lumique Clinical Standard
              </div>
            </div>
          </div>
        </div>

        <!-- Treatment Journey / Steps -->
        <div style="margin-bottom: 5rem;">
          <div style="text-align: center; max-width: 36rem; margin: 0 auto 3rem;">
            <span class="section-label">Step by Step</span>
            <h2 class="heading-2">The Treatment Process</h2>
          </div>
          <div class="luxury-card" style="padding: 2.5rem; border-radius: 0.75rem;">
            <div style="white-space: pre-line; line-height: 2; font-size: 1rem; color: var(--color-charcoal-muted);">
              ${treatment.treatment_process}
            </div>
          </div>
        </div>

        <!-- Key Details Grid: Benefits, Sessions, Recovery -->
        <div class="grid-3" style="margin-bottom: 5rem;">
          <div class="luxury-card" style="padding: 2rem;">
            <div style="width: 3rem; height: 3rem; background-color: var(--color-soft-red); color: var(--color-crimson); display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem; border-radius: 4px;">
              <i data-lucide="check-circle-2" style="width: 22px; height: 22px;"></i>
            </div>
            <h3 style="font-family: var(--font-serif); font-size: 1.25rem; margin-bottom: 0.75rem;">Key Benefits</h3>
            <div style="white-space: pre-line; font-size: 0.875rem; color: var(--color-charcoal-muted); line-height: 1.7;">
              ${treatment.benefits}
            </div>
          </div>

          <div class="luxury-card" style="padding: 2rem;">
            <div style="width: 3rem; height: 3rem; background-color: var(--color-soft-red); color: var(--color-crimson); display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem; border-radius: 4px;">
              <i data-lucide="clock" style="width: 22px; height: 22px;"></i>
            </div>
            <h3 style="font-family: var(--font-serif); font-size: 1.25rem; margin-bottom: 0.75rem;">Recommended Sessions</h3>
            <p style="font-size: 0.875rem; color: var(--color-charcoal-muted); line-height: 1.7;">
              ${treatment.num_sessions}
            </p>
          </div>

          <div class="luxury-card" style="padding: 2rem;">
            <div style="width: 3rem; height: 3rem; background-color: var(--color-soft-red); color: var(--color-crimson); display: flex; align-items: center; justify-content: center; margin-bottom: 1.25rem; border-radius: 4px;">
              <i data-lucide="shield" style="width: 22px; height: 22px;"></i>
            </div>
            <h3 style="font-family: var(--font-serif); font-size: 1.25rem; margin-bottom: 0.75rem;">Recovery & Aftercare</h3>
            <p style="font-size: 0.875rem; color: var(--color-charcoal-muted); line-height: 1.7;">
              ${treatment.recovery_info}
            </p>
          </div>
        </div>

        <!-- FAQs Accordion -->
        <div style="max-width: 48rem; margin: 0 auto 5rem;">
          <div style="text-align: center; margin-bottom: 2.5rem;">
            <span class="section-label">Questions & Answers</span>
            <h2 class="heading-2">Frequently Asked Questions</h2>
          </div>
          <div class="faq-list">
            ${treatment.faqs.map((faq, index) => `
              <div class="faq-item ${index === 0 ? 'active' : ''}">
                <button type="button" class="faq-question">
                  <span>${faq.question}</span>
                  <i data-lucide="chevron-down" class="faq-icon" style="width: 20px; height: 20px;"></i>
                </button>
                <div class="faq-answer">
                  <p>${faq.answer}</p>
                </div>
              </div>
            `).join('')}
          </div>
        </div>

        <!-- Related Treatments -->
        ${relatedTreatments.length ? `
          <div style="margin-top: 5rem; padding-top: 4rem; border-top: 1px solid var(--color-border);">
            <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 2.5rem;">
              <div>
                <span class="section-label">Complementary Care</span>
                <h3 class="heading-2">Related Treatments in ${category.name}</h3>
              </div>
              <a href="treatments.html#${category.slug}" style="color: var(--color-crimson); font-weight: 600; font-size: 0.875rem; display: inline-flex; align-items: center; gap: 0.5rem;">
                <span>View all ${category.name}</span>
                <i data-lucide="arrow-right" style="width: 16px; height: 16px;"></i>
              </a>
            </div>
            <div class="grid-3">
              ${relatedTreatments.map(rel => `
                <a href="treatment-detail.html?slug=${rel.slug}" class="treatment-card">
                  <div class="treatment-card-img-wrapper">
                    <img src="${rel.hero_image}" alt="${rel.title}" class="treatment-card-img" loading="lazy" onerror="this.onerror=null; this.src='https://images.pexels.com/photos/3997989/pexels-photo-3997989.jpeg?auto=compress&cs=tinysrgb&w=800';">
                  </div>
                  <div class="treatment-card-body">
                    <h4 class="treatment-card-title">${rel.title}</h4>
                    <p class="treatment-card-desc">${rel.short_intro}</p>
                  </div>
                </a>
              `).join('')}
            </div>
          </div>
        ` : ''}
      </div>
    </section>

    <!-- Consultation CTA -->
    <section class="cta-banner">
      <div class="container-luxury" style="position: relative; z-index: 10;">
        <h2 class="cta-banner-title text-balance">Ready for ${treatment.title}?</h2>
        <p class="cta-banner-desc">Schedule your personalized consultation with our board-certified dermatologists at our Mumbai clinic.</p>
        <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
          <button type="button" class="btn-white" onclick="if(window.openAppointmentModal) window.openAppointmentModal('${treatment.id}')">
            <i data-lucide="calendar" style="width: 18px; height: 18px;"></i>
            <span>Book Your Appointment</span>
          </button>
          <a href="tel:+918879550581" class="btn-secondary btn-secondary-white">
            <i data-lucide="phone" style="width: 18px; height: 18px;"></i>
            <span>+91 88795 50581</span>
          </a>
        </div>
      </div>
    </section>
  `;

  // Attach Accordion Toggle Listeners
  const faqItems = container.querySelectorAll('.faq-item');
  faqItems.forEach(item => {
    const questionBtn = item.querySelector('.faq-question');
    if (questionBtn) {
      questionBtn.addEventListener('click', () => {
        const isActive = item.classList.contains('active');
        faqItems.forEach(i => i.classList.remove('active'));
        if (!isActive) item.classList.add('active');
      });
    }
  });

  if (window.initLucideIcons) window.initLucideIcons();
  if (window.initScrollReveal) window.initScrollReveal();
}
