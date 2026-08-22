@extends('layouts.app')

@section('title', 'Contact Our Clinic Sanctuary | ' . ($settings['site_name'] ?? 'Lumique Aesthetic Clinic'))
@section('header_class', '')

@section('content')
<!-- Page Hero Banner -->
<section class="page-hero">
  <div class="floating-bg-container" data-particles="8"></div>
  <div class="container-luxury" style="position: relative; z-index: 10;">
    <div class="reveal" style="max-width: 44rem;">
      <span class="section-label">Contact & Location</span>
      <h1 class="heading-1 text-balance" style="margin-bottom: 1.5rem;">Begin Your Skin Transformation Journey</h1>
      <p class="body-text">
        Reach out to our concierge to schedule a private consultation or inquire about our clinical treatments in Bandra West, Mumbai.
      </p>
    </div>
  </div>
</section>

<!-- Contact Info & Form -->
<section class="section-padding" style="background-color: #ffffff;">
  <div class="container-luxury">
    <div class="grid-2" style="gap: 4rem; align-items: flex-start;">
      <!-- Contact Information & Map -->
      <div class="reveal">
        <span class="section-label">Visit Sanctuary</span>
        <h2 class="heading-2" style="margin-bottom: 1.5rem;">Lumique Aesthetic Sanctuary</h2>
        
        <div style="display: flex; flex-direction: column; gap: 1.5rem; margin-bottom: 2.5rem;">
          <div style="display: flex; gap: 1rem; align-items: flex-start;">
            <div style="width: 2.5rem; height: 2.5rem; background: var(--color-soft-red); color: var(--color-crimson); display: flex; align-items: center; justify-content: center; border-radius: 4px; flex-shrink: 0;">
              <i data-lucide="map-pin" style="width: 18px; height: 18px;"></i>
            </div>
            <div>
              <strong style="display: block; margin-bottom: 0.25rem;">Clinic Address</strong>
              <p class="body-text" style="font-size: 0.9rem;">{{ $settings['address'] ?? 'Ground Floor, Kenilworth Mall, Linking Road, Bandra West, Mumbai, Maharashtra 400050' }}</p>
            </div>
          </div>

          <div style="display: flex; gap: 1rem; align-items: flex-start;">
            <div style="width: 2.5rem; height: 2.5rem; background: var(--color-soft-red); color: var(--color-crimson); display: flex; align-items: center; justify-content: center; border-radius: 4px; flex-shrink: 0;">
              <i data-lucide="phone" style="width: 18px; height: 18px;"></i>
            </div>
            <div>
              <strong style="display: block; margin-bottom: 0.25rem;">Phone & WhatsApp</strong>
              <p class="body-text" style="font-size: 0.9rem;">
                <a href="tel:{{ $settings['phone'] ?? '+918879550581' }}" style="color: var(--color-crimson);">{{ $settings['phone'] ?? '+91 88795 50581' }}</a>
              </p>
            </div>
          </div>

          <div style="display: flex; gap: 1rem; align-items: flex-start;">
            <div style="width: 2.5rem; height: 2.5rem; background: var(--color-soft-red); color: var(--color-crimson); display: flex; align-items: center; justify-content: center; border-radius: 4px; flex-shrink: 0;">
              <i data-lucide="mail" style="width: 18px; height: 18px;"></i>
            </div>
            <div>
              <strong style="display: block; margin-bottom: 0.25rem;">Email Inquiries</strong>
              <p class="body-text" style="font-size: 0.9rem;">{{ $settings['email'] ?? 'contact@lumiqueclinic.com' }}</p>
            </div>
          </div>

          <div style="display: flex; gap: 1rem; align-items: flex-start;">
            <div style="width: 2.5rem; height: 2.5rem; background: var(--color-soft-red); color: var(--color-crimson); display: flex; align-items: center; justify-content: center; border-radius: 4px; flex-shrink: 0;">
              <i data-lucide="clock" style="width: 18px; height: 18px;"></i>
            </div>
            <div>
              <strong style="display: block; margin-bottom: 0.25rem;">Consultation Hours</strong>
              <p class="body-text" style="font-size: 0.9rem;">Monday – Saturday: 9:00 AM – 7:00 PM<br>Sunday: Closed</p>
            </div>
          </div>
        </div>

        <!-- Google Maps Embed Frame -->
        <div style="border-radius: 8px; overflow: hidden; border: 1px solid var(--color-border); box-shadow: var(--shadow-subtle);">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3771.189736856401!2d72.83350107593259!3d19.055375752627964!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be7c917b1897c8b%3A0xc4f5d688001fa385!2sLinking%20Rd%2C%20Bandra%20West%2C%20Mumbai%2C%20Maharashtra!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin" 
                  width="100%" 
                  height="260" 
                  style="border:0;" 
                  allowfullscreen="" 
                  loading="lazy">
          </iframe>
        </div>
      </div>

      <!-- Interactive Inquiry Form -->
      <div class="luxury-card reveal delay-1" style="padding: 2.5rem; border-radius: 4px;">
        <span class="section-label">Online Inquiries</span>
        <h3 class="heading-3" style="margin-bottom: 0.5rem;">Send a Consultation Message</h3>
        <p class="body-text" style="font-size: 0.875rem; margin-bottom: 1.5rem;">Our medical coordinator will review and reply within 2 business hours.</p>

        <form id="contactPageForm" onsubmit="handleContactSubmit(event)">
          <div style="margin-bottom: 1rem;">
            <label style="display: block; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 0.35rem; color: var(--color-charcoal);">Your Full Name *</label>
            <input type="text" id="contact_name" required placeholder="e.g. Maya Advani" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--color-border); font-size: 0.875rem; outline: none;">
          </div>
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
            <div>
              <label style="display: block; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 0.35rem; color: var(--color-charcoal);">Phone *</label>
              <input type="tel" id="contact_phone" required placeholder="+91 98200 00000" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--color-border); font-size: 0.875rem; outline: none;">
            </div>
            <div>
              <label style="display: block; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 0.35rem; color: var(--color-charcoal);">Email *</label>
              <input type="email" id="contact_email" required placeholder="maya@example.com" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--color-border); font-size: 0.875rem; outline: none;">
            </div>
          </div>
          <div style="margin-bottom: 1rem;">
            <label style="display: block; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 0.35rem; color: var(--color-charcoal);">Subject / Topic</label>
            <input type="text" id="contact_subject" placeholder="e.g. Consultation for Acne Scar Treatment" style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--color-border); font-size: 0.875rem; outline: none;">
          </div>
          <div style="margin-bottom: 1.5rem;">
            <label style="display: block; font-size: 0.75rem; font-weight: 600; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 0.35rem; color: var(--color-charcoal);">Your Message *</label>
            <textarea id="contact_message" rows="4" required placeholder="Describe your skincare or hair concerns..." style="width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--color-border); font-size: 0.875rem; outline: none;"></textarea>
          </div>
          <button type="submit" id="contactSubmitBtn" class="btn-primary" style="width: 100%; justify-content: center; padding: 1rem;">
            Send Inquiry Message
          </button>
          <div id="contactFormAlert" style="display: none; margin-top: 1rem; padding: 0.875rem; font-size: 0.875rem; border-radius: 4px;"></div>
        </form>
      </div>
    </div>
  </div>
</section>
@endsection

@section('scripts')
<script>
  async function handleContactSubmit(e) {
    e.preventDefault();
    const btn = document.getElementById('contactSubmitBtn');
    const alertBox = document.getElementById('contactFormAlert');
    btn.disabled = true;
    btn.innerText = 'Sending Message...';
    alertBox.style.display = 'none';

    const payload = {
      name: document.getElementById('contact_name').value,
      phone: document.getElementById('contact_phone').value,
      email: document.getElementById('contact_email').value,
      subject: document.getElementById('contact_subject').value,
      message: document.getElementById('contact_message').value,
    };

    try {
      const res = await fetch('/api/v1/inquiries', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(payload)
      });
      const data = await res.json();
      if (res.ok && data.success) {
        alertBox.style.background = '#eaf7ec';
        alertBox.style.color = '#1f6f2a';
        alertBox.style.border = '1px solid #c2e8c8';
        alertBox.innerHTML = '<strong>Message Sent!</strong><br>Thank you for contacting Lumique. We have sent a confirmation email to ' + data.data.email + '.';
        alertBox.style.display = 'block';
        document.getElementById('contactPageForm').reset();
      } else {
        alertBox.style.background = '#fdebee';
        alertBox.style.color = '#c8101e';
        alertBox.style.border = '1px solid #f9cdd3';
        alertBox.innerHTML = data.message || 'Please check your inputs and try again.';
        alertBox.style.display = 'block';
      }
    } catch(err) {
      alertBox.style.background = '#fdebee';
      alertBox.style.color = '#c8101e';
      alertBox.style.border = '1px solid #f9cdd3';
      alertBox.innerHTML = 'Failed to submit. Please call us directly at +91 88795 50581.';
      alertBox.style.display = 'block';
    } finally {
      btn.disabled = false;
      btn.innerText = 'Send Inquiry Message';
    }
  }
</script>
@endsection
