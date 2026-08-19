/* ==========================================================================
   Lumique Aesthetic Clinic - Universal Appointment Booking Modal & Form
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
  initAppointmentForm();
  initGlobalAppointmentModal();
  initGlobalVideoModal();
});

// 1. Contact Page Inline Form
function initAppointmentForm() {
  const form = document.getElementById('appointment-booking-form');
  const treatmentSelect = document.getElementById('appointment-treatment-select');
  if (!form) return;

  if (treatmentSelect && window.LUMIQUE_DATA) {
    const { treatments } = window.LUMIQUE_DATA;
    treatmentSelect.innerHTML = `
      <option value="">Select a Treatment or Consultation</option>
      ${treatments.map(t => `<option value="${t.id}">${t.title} (${t.category_id.toUpperCase()})</option>`).join('')}
    `;

    const urlParams = new URLSearchParams(window.location.search);
    const selectedTreatment = urlParams.get('treatment');
    if (selectedTreatment) {
      treatmentSelect.value = selectedTreatment;
    }
  }

  const dateInput = document.getElementById('appointment-date-input');
  if (dateInput) {
    const today = new Date().toISOString().split('T')[0];
    dateInput.setAttribute('min', today);
  }

  form.addEventListener('submit', (e) => {
    e.preventDefault();

    const honeypot = form.querySelector('input[name="website_url"]');
    if (honeypot && honeypot.value.trim() !== '') return;

    const name = form.querySelector('[name="name"]')?.value.trim();
    const phone = form.querySelector('[name="phone"]')?.value.trim();
    const email = form.querySelector('[name="email"]')?.value.trim();
    const treatmentId = form.querySelector('[name="treatment_id"]')?.value;
    const preferredDate = form.querySelector('[name="preferred_date"]')?.value;
    const preferredTime = form.querySelector('[name="preferred_time"]')?.value;
    const message = form.querySelector('[name="message"]')?.value.trim();

    const alertContainer = document.getElementById('appointment-form-alert');

    if (!name || !phone) {
      if (alertContainer) {
        alertContainer.className = 'form-alert form-alert-error';
        alertContainer.innerHTML = '<i data-lucide="alert-circle" style="width: 18px; height: 18px;"></i><span>Please enter your full name and valid phone number.</span>';
        alertContainer.style.display = 'flex';
        if (window.initLucideIcons) window.initLucideIcons();
      }
      return;
    }

    let treatmentTitle = 'General Consultation (Bandra West, Mumbai)';
    if (treatmentId && window.LUMIQUE_DATA) {
      const match = window.LUMIQUE_DATA.treatments.find(t => t.id === treatmentId);
      if (match) treatmentTitle = match.title;
    }

    if (window.LumiqueStore) {
      window.LumiqueStore.saveAppointment({
        name,
        phone,
        email,
        treatment_id: treatmentId,
        treatment_title: treatmentTitle,
        preferred_date: preferredDate,
        preferred_time: preferredTime,
        message
      });
    }

    const formCard = document.getElementById('appointment-form-container');
    if (formCard) {
      formCard.innerHTML = `
        <div style="text-align: center; padding: 3rem 1.5rem;">
          <div style="width: 4rem; height: 4rem; background-color: var(--color-soft-red); color: var(--color-crimson); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem;">
            <i data-lucide="check-circle" style="width: 32px; height: 32px;"></i>
          </div>
          <h3 class="heading-3" style="margin-bottom: 0.75rem;">Appointment Request Received!</h3>
          <p class="body-text" style="max-width: 28rem; margin: 0 auto 1.5rem; font-size: 0.95rem;">
            Thank you, <strong>${name}</strong>. Our clinical concierge in Bandra West, Mumbai will contact you at <strong>${phone}</strong> shortly to confirm your scheduled slot.
          </p>
          <div style="display: flex; justify-content: center; gap: 1rem;">
            <a href="index.html" class="btn-secondary" style="font-size: 0.8rem;">Return to Home</a>
            <a href="treatments.html" class="btn-primary" style="font-size: 0.8rem;">Explore Treatments</a>
          </div>
        </div>
      `;
      if (window.initLucideIcons) window.initLucideIcons();
    }
  });
}

// 2. Global Interactive Appointment Booking Modal
function initGlobalAppointmentModal() {
  if (document.getElementById('lumique-appointment-modal')) return;

  const modalHtml = `
    <div id="lumique-appointment-modal" class="lumique-modal-backdrop" aria-hidden="true" role="dialog">
      <div class="lumique-modal-dialog">
        <div class="lumique-modal-header">
          <div>
            <h2 class="lumique-modal-title">Book an Appointment</h2>
            <p class="lumique-modal-subtitle">Lumique Aesthetic Clinic · Bandra West, Mumbai</p>
          </div>
          <button type="button" class="lumique-modal-close" id="modal-close-btn" aria-label="Close modal">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
          </button>
        </div>
        <div class="lumique-modal-body" id="modal-form-content">
          <form id="modal-appointment-form">
            <div id="modal-form-alert" class="form-alert" style="display: none;"></div>

            <!-- Honeypot -->
            <div style="display: none;">
              <input type="text" name="website_url" tabindex="-1" autocomplete="off">
            </div>

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
              <label class="form-label" for="modal-email">Email Address (Optional)</label>
              <input type="email" id="modal-email" name="email" class="form-control" placeholder="you@example.com">
            </div>

            <div class="form-group">
              <label class="form-label" for="modal-treatment">Desired Treatment or Concern</label>
              <select id="modal-treatment" name="treatment_id" class="form-control">
                <option value="">General Dermatological Consultation</option>
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
                  <option value="morning">Morning (10:00 AM – 1:00 PM)</option>
                  <option value="afternoon">Afternoon (1:00 PM – 4:00 PM)</option>
                  <option value="evening">Evening (4:00 PM – 7:00 PM)</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label class="form-label" for="modal-message">Notes or Specific Concerns</label>
              <textarea id="modal-message" name="message" class="form-control" rows="2" placeholder="Tell us about your skin goals or any prior treatments..."></textarea>
            </div>

            <button type="submit" class="btn-primary" style="width: 100%; justify-content: center; padding: 1rem;">
              <span>Confirm Appointment Request</span>
            </button>
            <p style="font-size: 0.75rem; color: var(--color-charcoal-muted); text-align: center; margin-top: 0.75rem;">
              🔒 We respect your privacy. No spam. A clinical advisor will call you to confirm.
            </p>
          </form>
        </div>
      </div>
    </div>
  `;

  document.body.insertAdjacentHTML('beforeend', modalHtml);

  const modal = document.getElementById('lumique-appointment-modal');
  const closeBtn = document.getElementById('modal-close-btn');
  const form = document.getElementById('modal-appointment-form');
  const treatmentSelect = document.getElementById('modal-treatment');
  const dateInput = document.getElementById('modal-date');

  if (dateInput) {
    const today = new Date().toISOString().split('T')[0];
    dateInput.setAttribute('min', today);
  }

  // Populate treatments
  if (treatmentSelect && window.LUMIQUE_DATA) {
    const { treatments } = window.LUMIQUE_DATA;
    treatmentSelect.innerHTML = `
      <option value="">General Dermatological Consultation</option>
      ${treatments.map(t => `<option value="${t.id}">${t.title} (${t.category_id.toUpperCase()})</option>`).join('')}
    `;
  }

  // Open modal trigger handler
  const openModal = (preselectedTreatmentId) => {
    if (preselectedTreatmentId && treatmentSelect) {
      treatmentSelect.value = preselectedTreatmentId;
    }
    modal.classList.add('open');
    document.body.style.overflow = 'hidden';
  };

  // Close modal trigger handler
  const closeModal = () => {
    modal.classList.remove('open');
    document.body.style.overflow = '';
  };

  if (closeBtn) closeBtn.addEventListener('click', closeModal);
  modal.addEventListener('click', (e) => {
    if (e.target === modal) closeModal();
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modal.classList.contains('open')) closeModal();
  });

  // Attach to explicit modal trigger elements (e.g. data-open-modal="appointment" or .open-appointment-modal)
  document.addEventListener('click', (e) => {
    const target = e.target.closest('[data-open-modal="appointment"], .open-appointment-modal');
    if (target) {
      e.preventDefault();
      const treatmentId = target.getAttribute('data-treatment-id') || '';
      openModal(treatmentId);
    }
  });

  // Form submission
  if (form) {
    form.addEventListener('submit', (e) => {
      e.preventDefault();

      const name = form.querySelector('[name="name"]')?.value.trim();
      const phone = form.querySelector('[name="phone"]')?.value.trim();
      const email = form.querySelector('[name="email"]')?.value.trim();
      const treatmentId = form.querySelector('[name="treatment_id"]')?.value;
      const preferredDate = form.querySelector('[name="preferred_date"]')?.value;
      const preferredTime = form.querySelector('[name="preferred_time"]')?.value;
      const message = form.querySelector('[name="message"]')?.value.trim();

      const alertEl = document.getElementById('modal-form-alert');

      if (!name || !phone) {
        if (alertEl) {
          alertEl.className = 'form-alert form-alert-error';
          alertEl.innerHTML = '<span>Please provide your full name and phone number.</span>';
          alertEl.style.display = 'block';
        }
        return;
      }

      let treatmentTitle = 'General Consultation (Bandra West, Mumbai)';
      if (treatmentId && window.LUMIQUE_DATA) {
        const match = window.LUMIQUE_DATA.treatments.find(t => t.id === treatmentId);
        if (match) treatmentTitle = match.title;
      }

      if (window.LumiqueStore) {
        window.LumiqueStore.saveAppointment({
          name,
          phone,
          email,
          treatment_id: treatmentId,
          treatment_title: treatmentTitle,
          preferred_date: preferredDate,
          preferred_time: preferredTime,
          message
        });
      }

      const content = document.getElementById('modal-form-content');
      if (content) {
        content.innerHTML = `
          <div style="text-align: center; padding: 2rem 1rem;">
            <div style="width: 4rem; height: 4rem; background-color: var(--color-soft-red); color: var(--color-crimson); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.25rem;">
              <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path><polyline points="22 4 12 14.01 9 11.01"></polyline></svg>
            </div>
            <h3 class="heading-3" style="margin-bottom: 0.5rem; font-size: 1.35rem;">Appointment Received!</h3>
            <p class="body-text" style="font-size: 0.95rem; margin-bottom: 1.5rem;">
              Thank you, <strong>${name}</strong>. Our clinical team at Linking Road, Bandra West will connect with you at <strong>${phone}</strong> to confirm your slot.
            </p>
            <button type="button" class="btn-primary" onclick="document.getElementById('lumique-appointment-modal').classList.remove('open'); document.body.style.overflow='';" style="width: 100%; justify-content: center;">
              Done
            </button>
          </div>
        `;
      }
    });
  }

  window.openAppointmentModal = openModal;
  window.closeAppointmentModal = closeModal;
}

// 3. Global Video Player Modal
function initGlobalVideoModal() {
  if (document.getElementById('lumique-video-modal')) return;

  const videoModalHtml = `
    <div id="lumique-video-modal" class="lumique-modal-backdrop" aria-hidden="true">
      <div class="lumique-modal-dialog video-modal-dialog">
        <div style="position: relative;">
          <button type="button" id="video-modal-close" style="position: absolute; top: 1rem; right: 1rem; z-index: 20; background: rgba(0,0,0,0.6); color: #fff; border: none; border-radius: 50%; width: 2.5rem; height: 2.5rem; display: flex; align-items: center; justify-content: center; cursor: pointer;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
          </button>
          <video id="lumique-modal-video-player" controls style="width: 100%; aspect-ratio: 16/9; background: #000;"></video>
        </div>
      </div>
    </div>
  `;

  document.body.insertAdjacentHTML('beforeend', videoModalHtml);

  const videoModal = document.getElementById('lumique-video-modal');
  const videoCloseBtn = document.getElementById('video-modal-close');
  const player = document.getElementById('lumique-modal-video-player');

  const closeVideo = () => {
    if (player) {
      player.pause();
      player.src = '';
    }
    videoModal.classList.remove('open');
    document.body.style.overflow = '';
  };

  if (videoCloseBtn) videoCloseBtn.addEventListener('click', closeVideo);
  videoModal.addEventListener('click', (e) => {
    if (e.target === videoModal) closeVideo();
  });

  window.openVideoModal = (url) => {
    if (player && url) {
      player.src = url;
      player.play().catch(() => {});
    }
    videoModal.classList.add('open');
    document.body.style.overflow = 'hidden';
  };
}
