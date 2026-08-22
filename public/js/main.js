/* ==========================================================================
   Lumique Aesthetic Clinic - Global Script & Interactive Utilities
   ========================================================================== */

let scrollRevealObserver = null;

document.addEventListener('DOMContentLoaded', () => {
  initHeaderScroll();
  initMobileNav();
  initScrollReveal();
  initFloatingBackground();
  initActiveNavLink();
  initLucideIcons();
  initImageFallbacks();
});

// Initialize Lucide Icons safely (with check to avoid redundant conversions)
function initLucideIcons() {
  if (window.lucide && typeof window.lucide.createIcons === 'function') {
    window.lucide.createIcons();
  }
}

// Fallback handling for images
function initImageFallbacks() {
  const fallbackUrl = 'https://images.pexels.com/photos/3997989/pexels-photo-3997989.jpeg?auto=compress&cs=tinysrgb&w=800';
  document.querySelectorAll('img').forEach(img => {
    img.addEventListener('error', function onError() {
      this.removeEventListener('error', onError);
      if (this.src !== fallbackUrl) {
        this.src = fallbackUrl;
      }
    });
  });
}

// Header Scroll Animation
function initHeaderScroll() {
  const header = document.querySelector('.site-header');
  if (!header) return;

  const handleScroll = () => {
    if (window.scrollY > 20) {
      header.classList.add('scrolled');
    } else {
      header.classList.remove('scrolled');
    }
  };

  window.addEventListener('scroll', handleScroll, { passive: true });
  handleScroll();
}

// Mobile Navigation Drawer Toggle
function initMobileNav() {
  const openBtn = document.querySelector('.mobile-menu-btn');
  const closeBtn = document.querySelector('.mobile-drawer-close');
  const drawer = document.querySelector('.mobile-drawer');
  const backdrop = document.querySelector('.mobile-drawer-backdrop');
  const links = document.querySelectorAll('.mobile-nav-link');

  if (!drawer) return;

  const openDrawer = () => {
    drawer.classList.add('open');
    document.body.classList.add('menu-open');
    document.body.style.overflow = 'hidden';
  };

  const closeDrawer = () => {
    drawer.classList.remove('open');
    document.body.classList.remove('menu-open');
    document.body.style.overflow = '';
  };

  if (openBtn) openBtn.addEventListener('click', openDrawer);
  if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
  if (backdrop) backdrop.addEventListener('click', closeDrawer);

  links.forEach(link => {
    link.addEventListener('click', closeDrawer);
  });
}

// Scroll Reveal with Intersection Observer
function initScrollReveal() {
  const revealElements = document.querySelectorAll('.reveal:not(.active)');
  if (!revealElements.length) return;

  if (!scrollRevealObserver && 'IntersectionObserver' in window) {
    scrollRevealObserver = new IntersectionObserver((entries, obs) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('active');
          obs.unobserve(entry.target);
        }
      });
    }, {
      threshold: 0.05,
      rootMargin: '0px 0px -20px 0px'
    });
  }

  revealElements.forEach(el => {
    // If element is already in viewport or IntersectionObserver not supported, make it active immediately
    const rect = el.getBoundingClientRect();
    if (rect.top < window.innerHeight + 100 || !scrollRevealObserver) {
      el.classList.add('active');
    } else {
      scrollRevealObserver.observe(el);
    }
  });
}

// Floating Background Ambient Glow Particles & Decorative Orbs
function initFloatingBackground() {
  const containers = document.querySelectorAll('.floating-bg-container');
  containers.forEach(container => {
    if (container.dataset.initialized) return;
    container.dataset.initialized = 'true';

    const count = parseInt(container.getAttribute('data-particles') || '6', 10);
    for (let i = 0; i < count; i++) {
      const particle = document.createElement('div');
      particle.className = 'floating-particle';
      const size = Math.random() * 180 + 90;
      particle.style.width = `${size}px`;
      particle.style.height = `${size}px`;
      particle.style.left = `${Math.random() * 85 + 5}%`;
      particle.style.top = `${Math.random() * 85 + 5}%`;
      particle.style.animationDelay = `${Math.random() * 6}s`;
      particle.style.animationDuration = `${Math.random() * 8 + 10}s`;
      container.appendChild(particle);
    }
  });
}

// Active Nav Link Matcher
function initActiveNavLink() {
  const currentPath = window.location.pathname.split('/').pop() || 'index.html';
  const navLinks = document.querySelectorAll('.nav-link, .mobile-nav-link');

  navLinks.forEach(link => {
    const href = link.getAttribute('href');
    if (!href) return;
    const linkPath = href.split('/').pop() || 'index.html';
    if (linkPath === currentPath || (currentPath === '' && linkPath === 'index.html')) {
      link.classList.add('active');
    }
  });
}

window.initScrollReveal = initScrollReveal;
window.initLucideIcons = initLucideIcons;
window.initFloatingBackground = initFloatingBackground;
window.initImageFallbacks = initImageFallbacks;
