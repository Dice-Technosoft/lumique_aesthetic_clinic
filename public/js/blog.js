/* ==========================================================================
   Lumique Aesthetic Clinic - Blog & Educational Article Engine
   ========================================================================== */

document.addEventListener('DOMContentLoaded', () => {
  if (document.getElementById('blog-posts-grid')) {
    initBlogCatalog();
  }
  if (document.getElementById('blog-post-detail')) {
    renderBlogPost();
  }
});

// Blog Catalog List & Live Search/Filter
function initBlogCatalog() {
  const container = document.getElementById('blog-posts-grid');
  const searchInput = document.getElementById('blog-search-input');
  const filterButtons = document.querySelectorAll('.blog-filter-btn');
  if (!container || !window.LUMIQUE_DATA) return;

  const { blogPosts } = window.LUMIQUE_DATA;
  let currentCategory = 'all';
  let searchQuery = '';

  const render = () => {
    const filtered = blogPosts.filter(post => {
      const matchCat = currentCategory === 'all' || post.category_slug === currentCategory;
      const matchSearch = !searchQuery || 
        post.title.toLowerCase().includes(searchQuery.toLowerCase()) || 
        post.excerpt.toLowerCase().includes(searchQuery.toLowerCase());
      return matchCat && matchSearch;
    });

    if (!filtered.length) {
      container.innerHTML = `
        <div style="grid-column: 1 / -1; text-align: center; padding: 4rem 1rem;">
          <p style="font-size: 1.125rem; color: var(--color-charcoal-muted); margin-bottom: 1rem;">No articles found matching your criteria.</p>
          <button class="btn-secondary" onclick="resetBlogSearch()">Clear Search</button>
        </div>
      `;
      return;
    }

    container.innerHTML = filtered.map((post, idx) => `
      <div class="reveal delay-${(idx % 3) + 1}">
        <a href="blog-post.html?slug=${post.slug}" class="blog-card">
          <div class="blog-card-img-wrapper">
            <img src="${post.featured_image}" alt="${post.title}" class="blog-card-img" loading="lazy">
          </div>
          <div class="blog-card-body">
            <span class="blog-card-category">${post.category}</span>
            <h3 class="blog-card-title">${post.title}</h3>
            <p class="blog-card-excerpt">${post.excerpt}</p>
            <div class="blog-card-meta" style="display: flex; align-items: center; justify-content: space-between; margin-top: auto; padding-top: 1rem; border-top: 1px solid var(--color-border);">
              <span>${post.author}</span>
              <span>${new Date(post.published_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</span>
            </div>
          </div>
        </a>
      </div>
    `).join('');

    if (window.initLucideIcons) window.initLucideIcons();
    if (window.initScrollReveal) window.initScrollReveal();
  };

  filterButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      filterButtons.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentCategory = btn.getAttribute('data-category') || 'all';
      render();
    });
  });

  if (searchInput) {
    searchInput.addEventListener('input', (e) => {
      searchQuery = e.target.value.trim();
      render();
    });
  }

  window.resetBlogSearch = () => {
    if (searchInput) searchInput.value = '';
    searchQuery = '';
    currentCategory = 'all';
    filterButtons.forEach(b => b.classList.toggle('active', b.getAttribute('data-category') === 'all'));
    render();
  };

  render();
}

// Render Single Blog Post Reader
function renderBlogPost() {
  const container = document.getElementById('blog-post-detail');
  if (!container || !window.LUMIQUE_DATA) return;

  const urlParams = new URLSearchParams(window.location.search);
  const slug = urlParams.get('slug') || 'hyaluronic-acid-vs-retinol-skincare-guide';
  const { blogPosts } = window.LUMIQUE_DATA;

  const post = blogPosts.find(p => p.slug === slug) || blogPosts[0];
  const relatedPosts = blogPosts.filter(p => p.id !== post.id).slice(0, 2);

  document.title = `${post.title} | Lumique Aesthetic Clinic`;

  // Parse simple markdown headings & lists
  const formatContent = (content) => {
    return content
      .split('\n')
      .map(line => {
        const trimmed = line.trim();
        if (trimmed.startsWith('### ')) {
          return `<h3 style="font-family: var(--font-serif); font-size: 1.5rem; font-weight: 600; color: var(--color-charcoal); margin: 2rem 0 1rem;">${trimmed.slice(4)}</h3>`;
        }
        if (trimmed.startsWith('## ')) {
          return `<h2 style="font-family: var(--font-serif); font-size: 1.75rem; font-weight: 700; color: var(--color-charcoal); margin: 2.5rem 0 1rem;">${trimmed.slice(3)}</h2>`;
        }
        if (trimmed.startsWith('- ')) {
          return `<li style="margin-left: 1.5rem; list-style-type: disc; color: var(--color-charcoal-muted); margin-bottom: 0.5rem; line-height: 1.7;">${trimmed.slice(2)}</li>`;
        }
        if (/^\d+\.\s/.test(trimmed)) {
          return `<li style="margin-left: 1.5rem; list-style-type: decimal; color: var(--color-charcoal-muted); margin-bottom: 0.5rem; line-height: 1.7;">${trimmed.replace(/^\d+\.\s/, '')}</li>`;
        }
        if (trimmed === '---') {
          return `<hr style="border: none; border-top: 1px solid var(--color-border); margin: 2rem 0;">`;
        }
        if (!trimmed) {
          return `<div style="height: 0.75rem;"></div>`;
        }
        return `<p style="font-size: 1.05rem; color: var(--color-charcoal-muted); line-height: 1.8; margin-bottom: 1rem;">${trimmed}</p>`;
      })
      .join('');
  };

  container.innerHTML = `
    <!-- Post Header -->
    <section class="page-hero" style="padding-top: 9rem; padding-bottom: 4rem;">
      <div class="floating-bg-container" data-particles="6"></div>
      <div class="container-luxury" style="max-width: 50rem; position: relative; z-index: 10;">
        <a href="blog.html" style="display: inline-flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.1em; color: var(--color-crimson); font-weight: 600; margin-bottom: 1.5rem;">
          <i data-lucide="arrow-left" style="width: 16px; height: 16px;"></i>
          <span>All Educational Articles</span>
        </a>
        <span class="section-label" style="margin-bottom: 0.5rem;">${post.category}</span>
        <h1 class="heading-1 text-balance" style="font-size: 2.25rem; margin-bottom: 1.5rem;">${post.title}</h1>
        <div style="display: flex; align-items: center; gap: 1.5rem; font-size: 0.875rem; color: var(--color-charcoal-muted);">
          <span>By <strong>${post.author}</strong></span>
          <span>•</span>
          <span>${new Date(post.published_at).toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' })}</span>
        </div>
      </div>
    </section>

    <!-- Post Body -->
    <article class="section-padding" style="background-color: #ffffff; padding-top: 3rem;">
      <div class="container-luxury" style="max-width: 50rem;">
        <div style="border-radius: 0.5rem; overflow: hidden; margin-bottom: 3rem; box-shadow: var(--shadow-subtle);">
          <img src="${post.featured_image}" alt="${post.title}" style="width: 100%; aspect-ratio: 16/9; object-fit: cover;">
        </div>
        <div class="article-content-body">
          ${formatContent(post.content)}
        </div>

        <!-- Share / Author Card -->
        <div style="margin-top: 4rem; padding: 2rem; background-color: var(--color-ivory); border-left: 3px solid var(--color-crimson); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem;">
          <div>
            <h4 style="font-family: var(--font-serif); font-size: 1.1rem; margin-bottom: 0.25rem;">Medical Review by ${post.author}</h4>
            <p style="font-size: 0.85rem; color: var(--color-charcoal-muted);">Board Certified in Clinical Dermatology & Cutaneous Aesthetics</p>
          </div>
          <a href="contact.html" class="btn-primary" style="padding: 0.6rem 1.25rem; font-size: 0.75rem;">Book Consultation</a>
        </div>
      </div>
    </article>

    <!-- Related Articles -->
    ${relatedPosts.length ? `
      <section class="section-padding" style="background-color: var(--color-ivory);">
        <div class="container-luxury" style="max-width: 50rem;">
          <h3 class="heading-3" style="margin-bottom: 2rem;">Related Reading</h3>
          <div class="grid-2">
            ${relatedPosts.map(rel => `
              <a href="blog-post.html?slug=${rel.slug}" class="blog-card">
                <div class="blog-card-img-wrapper" style="aspect-ratio: 16/9;">
                  <img src="${rel.featured_image}" alt="${rel.title}" class="blog-card-img">
                </div>
                <div class="blog-card-body">
                  <span class="blog-card-category">${rel.category}</span>
                  <h4 class="blog-card-title" style="font-size: 1.1rem;">${rel.title}</h4>
                  <p class="blog-card-excerpt" style="font-size: 0.8rem;">${rel.excerpt}</p>
                </div>
              </a>
            `).join('')}
          </div>
        </div>
      </section>
    ` : ''}
  `;

  if (window.lucide && typeof window.lucide.createIcons === 'function') {
    window.lucide.createIcons();
  }
}
