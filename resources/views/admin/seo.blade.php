@extends('layouts.admin')

@section('title', 'SEO & Search Optimization Manager')
@section('breadcrumb_parent', 'System Config')
@section('breadcrumb_current', 'SEO & Meta Management')
@section('page_title', 'SEO Management & Meta Configuration')

@section('content')
<!-- SEO Stats Strip -->
<div class="dashboard-stats-grid" style="margin-bottom: 2rem;">
    <div class="stat-widget-card">
        <div class="widget-icon-box bg-burgundy">🔍</div>
        <div class="widget-info">
            <span class="widget-label">Total Indexed URLs</span>
            <h3 class="widget-value">{{ count($staticPages) + $services->count() + $blogPosts->count() }}</h3>
            <span class="widget-delta delta-converted">Sitemap Active</span>
        </div>
    </div>

    <div class="stat-widget-card">
        <div class="widget-icon-box bg-gold">📄</div>
        <div class="widget-info">
            <span class="widget-label">Editable Core Pages</span>
            <h3 class="widget-value">{{ count($staticPages) }}</h3>
            <span class="widget-delta delta-active">Direct DB Sync</span>
        </div>
    </div>

    <div class="stat-widget-card">
        <div class="widget-icon-box bg-teal">✨</div>
        <div class="widget-info">
            <span class="widget-label">Auto-Generated Procedures</span>
            <h3 class="widget-value">{{ $services->count() }}</h3>
            <span class="widget-delta delta-converted">Auto Synced</span>
        </div>
    </div>

    <div class="stat-widget-card">
        <div class="widget-icon-box bg-coral">📝</div>
        <div class="widget-info">
            <span class="widget-label">Auto-Generated Articles</span>
            <h3 class="widget-value">{{ $blogPosts->count() }}</h3>
            <span class="widget-delta delta-active">Auto Synced</span>
        </div>
    </div>
</div>

<!-- Navigation Tabs for SEO -->
<div class="filter-header-row" style="margin-bottom: 1.5rem;">
    <div class="filter-pills-group">
        <button type="button" class="filter-pill active" onclick="switchSeoTab('core-pages', this)">Core Pages (Custom Meta & Edit)</button>
        <button type="button" class="filter-pill" onclick="switchSeoTab('global-seo', this)">Global Search Defaults</button>
    </div>
</div>

<!-- TAB 1: CORE PAGES SEO (DIRECTLY EDITABLE) -->
<div id="tab-core-pages" class="seo-tab-content">
    <div class="admin-panel-card">
        <div class="panel-card-header">
            <div>
                <h3>Individual Page SEO & Social Open Graph Tags</h3>
                <small class="text-muted">Directly manage custom meta titles, descriptions, keywords, and preview cards for core website routes</small>
            </div>
        </div>

        <div class="table-responsive" style="overflow-x: hidden;">
            <table class="admin-table" style="table-layout: fixed; width: 100%;">
                <thead>
                    <tr>
                        <th style="width: 32%;">Page / URL Path</th>
                        <th style="width: 18%;">Type</th>
                        <th style="width: 40%;">Meta Title</th>
                        <th style="width: 10%; text-align: right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($staticPages as $page)
                    @php
                        $meta = $seoMetas[$page['path']] ?? null;
                        $title = $meta->meta_title ?? ($settings['default_meta_title'] ?? $page['name']);
                    @endphp
                    <tr>
                        <td style="word-break: break-word;">
                            <strong>{{ $page['name'] }}</strong><br>
                            <code style="color: var(--color-crimson); font-size: 0.75rem;">{{ $page['path'] }}</code>
                        </td>
                        <td><span class="badge badge-gold">{{ $page['badge'] }}</span></td>
                        <td>
                            <div style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; line-height: 1.35; font-size: 0.85rem;" title="{{ $title }}">
                                {{ $title }}
                            </div>
                        </td>
                        <td style="text-align: right;">
                            <div class="table-actions-group">
                                <button type="button" class="action-icon-btn btn-edit" data-tooltip="Edit Page SEO" aria-label="Edit SEO" onclick='openSeoModal(@json($page["path"]), @json($page["name"]), @json($meta))'>
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- TAB 3: GLOBAL SEARCH ENGINE DEFAULTS -->
<div id="tab-global-seo" class="seo-tab-content" style="display: none;">
    <div class="admin-panel-card">
        <form onsubmit="handleGlobalSeoSubmit(event)" id="globalSeoForm" enctype="multipart/form-data">
            <div class="settings-group-section" style="border-bottom: none; margin-bottom: 0;">
                <h3 class="settings-group-title">GLOBAL META & SEARCH ENGINE DEFAULTS</h3>
                
                <div class="settings-inputs-grid">
                    <div class="form-group">
                        <label for="default_meta_title">Default Meta Title Pattern</label>
                        <input type="text" id="default_meta_title" name="default_meta_title" value="{{ $settings['default_meta_title'] ?? 'Lumique Aesthetic Clinic | Premier Dermatology Mumbai' }}" class="form-control" oninput="updateGlobalPreview()">
                    </div>

                    <div class="form-group">
                        <label for="default_meta_keywords">Global Meta Keywords</label>
                        <input type="text" id="default_meta_keywords" name="default_meta_keywords" value="{{ $settings['default_meta_keywords'] ?? 'dermatologist mumbai, hydrafacial bandra, laser hair removal' }}" class="form-control">
                    </div>
                </div>

                <div class="form-group" style="margin-top: 1rem;">
                    <label for="default_meta_description">Default Meta Description</label>
                    <textarea id="default_meta_description" name="default_meta_description" rows="3" class="form-control" oninput="updateGlobalPreview()">{{ $settings['default_meta_description'] ?? 'Experience bespoke luxury dermatology, laser hair reduction, skin rejuvenation, and hair restoration at Lumique Aesthetic Clinic, Bandra West, Mumbai.' }}</textarea>
                </div>

                <!-- Google Search Simulation Card -->
                <div style="margin: 2rem 0; background: #ffffff; border: 1px solid #dfe1e5; border-radius: 8px; padding: 1.5rem; max-width: 650px; box-shadow: 0 1px 6px rgba(32,33,36,0.08);">
                    <small style="text-transform: uppercase; letter-spacing: 0.08em; color: #70757a; font-weight: 700; display: block; margin-bottom: 0.75rem;">Google Search Preview (Desktop / Mobile SERP)</small>
                    <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                        <div style="width: 28px; height: 28px; border-radius: 50%; background: #f1f3f4; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1px solid rgba(0,0,0,0.08); flex-shrink: 0;">
                            <img src="{{ !empty($settings['logo_url']) ? $settings['logo_url'] : '/images/logo.jpeg' }}" alt="Site Logo" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div style="display: flex; flex-direction: column; justify-content: center; line-height: 1.2;">
                            <span style="font-size: 14px; font-weight: 500; color: #202124;">{{ $settings['site_name'] ?? 'Lumique Aesthetic Clinic' }}</span>
                            <div style="color: #4d5156; font-size: 12px; display: flex; align-items: center; gap: 4px;">
                                <span>{{ url('/') }}</span>
                                <span style="color: #70757a; font-size: 10px;">⋮</span>
                            </div>
                        </div>
                    </div>
                    <h3 id="google_preview_title" style="color: #1a0dab; font-size: 20px; font-weight: 400; line-height: 1.3; margin: 4px 0 6px; cursor: pointer; text-decoration: none;">
                        {{ $settings['default_meta_title'] ?? 'Lumique Aesthetic Clinic | Premier Dermatology Mumbai' }}
                    </h3>
                    <p id="google_preview_desc" style="color: #4d5156; font-size: 14px; line-height: 1.58; margin: 0;">
                        {{ $settings['default_meta_description'] ?? 'Experience bespoke luxury dermatology, laser hair reduction, skin rejuvenation, and hair restoration at Lumique Aesthetic Clinic, Bandra West, Mumbai.' }}
                    </p>
                </div>

                <div class="settings-inputs-grid">
                    <div class="form-group">
                        <label for="google_site_verification">Google Search Console Verification Tag</label>
                        <input type="text" id="google_site_verification" name="google_site_verification" value="{{ $settings['google_site_verification'] ?? '' }}" class="form-control" placeholder="e.g. google-site-verification=abc123xyz">
                    </div>

                    <div class="form-group">
                        <label for="google_analytics_id">Google Analytics Measurement ID (GA4)</label>
                        <input type="text" id="google_analytics_id" name="google_analytics_id" value="{{ $settings['google_analytics_id'] ?? '' }}" class="form-control" placeholder="e.g. G-XXXXXXXXXX">
                    </div>
                </div>

                <div class="settings-save-bar" style="display: flex; justify-content: flex-end;">
                    <button type="submit" id="saveGlobalSeoBtn" class="btn btn-gold btn-sm">Save Global Defaults</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Interactive Modal: Edit SEO for Specific Page -->
<div id="pageSeoModal" class="modal-overlay">
    <div class="modal-card" style="max-width: 700px;">
        <button type="button" class="modal-close" onclick="closeSeoModal()">&times;</button>
        <div class="modal-header">
            <h3 id="modalPageTitle">Edit Page SEO</h3>
            <p id="modalPagePath" style="color: var(--color-crimson); font-family: monospace; font-size: 0.85rem;"></p>
        </div>

        <form onsubmit="handlePageSeoSubmit(event)" id="pageSeoForm" enctype="multipart/form-data">
            <input type="hidden" id="seo_path" name="path">

            <div class="form-group">
                <label for="seo_meta_title">
                    Meta Title 
                    <span id="titleCharCount" style="float: right; color: var(--color-charcoal-muted); font-weight: normal; text-transform: none;">0 / 60</span>
                </label>
                <input type="text" id="seo_meta_title" name="meta_title" class="form-control" oninput="updatePageSnippet()" placeholder="Leave empty to inherit global title">
            </div>

            <div class="form-group">
                <label for="seo_meta_description">
                    Meta Description 
                    <span id="descCharCount" style="float: right; color: var(--color-charcoal-muted); font-weight: normal; text-transform: none;">0 / 160</span>
                </label>
                <textarea id="seo_meta_description" name="meta_description" rows="3" class="form-control" oninput="updatePageSnippet()" placeholder="Compelling summary for search engine results..."></textarea>
            </div>

            <!-- Modal Google SERP Preview with Dynamic Brand Logo -->
            <div style="background: #ffffff; border: 1px solid #dfe1e5; border-radius: 8px; padding: 1.25rem; margin-bottom: 1.5rem; box-shadow: 0 1px 6px rgba(32,33,36,0.08);">
                <small style="text-transform: uppercase; letter-spacing: 0.08em; font-weight: 700; color: #70757a; font-size: 0.68rem; display: block; margin-bottom: 0.65rem;">Live Google Search Snippet Preview (Desktop & Mobile SERP)</small>
                
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 6px;">
                    <div style="width: 28px; height: 28px; border-radius: 50%; background: #f1f3f4; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 1px solid rgba(0,0,0,0.08); flex-shrink: 0;">
                        <img src="{{ !empty($settings['logo_url']) ? $settings['logo_url'] : '/images/logo.jpeg' }}" alt="Site Logo" style="width: 100%; height: 100%; object-fit: cover;">
                    </div>
                    <div style="display: flex; flex-direction: column; justify-content: center; line-height: 1.2;">
                        <span style="font-size: 14px; font-weight: 500; color: #202124;">{{ $settings['site_name'] ?? 'Lumique Aesthetic Clinic' }}</span>
                        <div style="color: #4d5156; font-size: 12px; display: flex; align-items: center; gap: 4px;">
                            <span id="modal_preview_url">{{ url('/') }}</span>
                            <span style="color: #70757a; font-size: 10px;">⋮</span>
                        </div>
                    </div>
                </div>
                <h3 id="modal_preview_title" style="color: #1a0dab; font-size: 18px; font-weight: 400; line-height: 1.3; margin: 4px 0 5px; cursor: pointer; text-decoration: none;">Page Title</h3>
                <p id="modal_preview_desc" style="color: #4d5156; font-size: 13px; line-height: 1.5; margin: 0;">Meta description summary...</p>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="seo_meta_keywords">Target Keywords</label>
                    <input type="text" id="seo_meta_keywords" name="meta_keywords" class="form-control" placeholder="keyword1, keyword2">
                </div>
                <div class="form-group">
                    <label for="seo_robots">Robots Indexing</label>
                    <select id="seo_robots" name="robots" class="form-control">
                        <option value="index, follow">index, follow (Recommended)</option>
                        <option value="noindex, follow">noindex, follow</option>
                        <option value="noindex, nofollow">noindex, nofollow (Hidden)</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="seo_og_title">Social OG Title (Optional)</label>
                    <input type="text" id="seo_og_title" name="og_title" class="form-control" placeholder="Defaults to Meta Title">
                </div>
                <div class="form-group">
                    <label for="seo_canonical_url">Canonical URL (Optional)</label>
                    <input type="url" id="seo_canonical_url" name="canonical_url" class="form-control" placeholder="https://lumiqueclinic.com/...">
                </div>
            </div>

            <div class="form-group">
                <label for="og_image_file">Social Sharing Image (OG:Image)</label>
                <input type="file" id="og_image_file" name="og_image_file" accept="image/*" class="form-control form-control-sm">
                <input type="hidden" id="seo_og_image" name="og_image">
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--color-border);">
                <button type="button" class="btn btn-outline-gold btn-sm" onclick="closeSeoModal()">Cancel</button>
                <button type="submit" id="savePageSeoBtn" class="btn btn-gold btn-sm">Save Metadata</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function switchSeoTab(tabKey, btn) {
    document.querySelectorAll('.seo-tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.filter-pill').forEach(el => el.classList.remove('active'));
    
    const target = document.getElementById('tab-' + tabKey);
    if (target) target.style.display = 'block';
    btn.classList.add('active');
}

function updateGlobalPreview() {
    const title = document.getElementById('default_meta_title').value || 'Lumique Aesthetic Clinic';
    const desc = document.getElementById('default_meta_description').value || 'Dermatology & Aesthetic Clinic in Bandra West, Mumbai.';
    document.getElementById('google_preview_title').innerText = title;
    document.getElementById('google_preview_desc').innerText = desc;
}

async function openSeoModal(path, name, initialMeta) {
    document.getElementById('seo_path').value = path;
    document.getElementById('modalPageTitle').innerText = 'Edit SEO: ' + name;
    document.getElementById('modalPagePath').innerText = path;
    document.getElementById('modal_preview_url').innerText = window.location.origin + path;

    const titleInput = document.getElementById('seo_meta_title');
    const descInput = document.getElementById('seo_meta_description');
    const keywordsInput = document.getElementById('seo_meta_keywords');
    const robotsSelect = document.getElementById('seo_robots');
    const ogTitleInput = document.getElementById('seo_og_title');
    const canonicalInput = document.getElementById('seo_canonical_url');
    const ogImageInput = document.getElementById('seo_og_image');

    // Pre-fill with available meta while fetching fresh database data
    let meta = initialMeta || {};
    titleInput.value = meta.meta_title || '';
    descInput.value = meta.meta_description || '';
    keywordsInput.value = meta.meta_keywords || '';
    robotsSelect.value = meta.robots || 'index, follow';
    ogTitleInput.value = meta.og_title || '';
    canonicalInput.value = meta.canonical_url || '';
    ogImageInput.value = meta.og_image || '';
    updatePageSnippet();

    document.getElementById('pageSeoModal').classList.add('open');

    try {
        const res = await fetch('/admin/seo/get?path=' + encodeURIComponent(path), {
            headers: { 'Accept': 'application/json' }
        });
        const json = await res.json();
        if (json.success && json.data) {
            meta = json.data;
            titleInput.value = meta.meta_title || '';
            descInput.value = meta.meta_description || '';
            keywordsInput.value = meta.meta_keywords || '';
            robotsSelect.value = meta.robots || 'index, follow';
            ogTitleInput.value = meta.og_title || '';
            canonicalInput.value = meta.canonical_url || '';
            ogImageInput.value = meta.og_image || '';
            updatePageSnippet();
        }
    } catch(err) {
        console.error('Error fetching live SEO meta:', err);
    }
}

function closeSeoModal() {
    document.getElementById('pageSeoModal').classList.remove('open');
}

function updatePageSnippet() {
    const title = document.getElementById('seo_meta_title').value || 'Lumique Aesthetic Clinic | Premier Care';
    const desc = document.getElementById('seo_meta_description').value || 'Personalized clinical dermatology, hair restoration, and aesthetic treatments in Mumbai.';
    
    document.getElementById('modal_preview_title').innerText = title;
    document.getElementById('modal_preview_desc').innerText = desc;

    document.getElementById('titleCharCount').innerText = document.getElementById('seo_meta_title').value.length + ' / 60';
    document.getElementById('descCharCount').innerText = document.getElementById('seo_meta_description').value.length + ' / 160';
}

async function handlePageSeoSubmit(e) {
    e.preventDefault();
    const btn = document.getElementById('savePageSeoBtn');
    btn.disabled = true;
    btn.innerText = 'Saving...';

    const formData = new FormData(document.getElementById('pageSeoForm'));

    try {
        const res = await fetch('{{ route("admin.seo.save") }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        });

        const data = await res.json();
        if (res.ok && data.success) {
            closeSeoModal();
            showToast('Page SEO metadata saved directly to database!', 'success');
            setTimeout(() => { location.reload(); }, 1000);
        } else {
            showToast(data.message || 'Error saving page SEO', 'error');
        }
    } catch(err) {
        showToast('Network error saving metadata', 'error');
    } finally {
        btn.disabled = false;
        btn.innerText = 'Save Metadata';
    }
}

async function handleGlobalSeoSubmit(e) {
    e.preventDefault();
    const btn = document.getElementById('saveGlobalSeoBtn');
    btn.disabled = true;
    btn.innerText = 'Saving Defaults...';

    const formData = new FormData(document.getElementById('globalSeoForm'));

    try {
        const res = await fetch('{{ route("admin.seo.global") }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: formData
        });

        const data = await res.json();
        if (res.ok && data.success) {
            showToast('Global SEO defaults updated successfully!', 'success');
        } else {
            showToast(data.message || 'Error saving global SEO', 'error');
        }
    } catch(err) {
        showToast('Network error saving global SEO', 'error');
    } finally {
        btn.disabled = false;
        btn.innerText = 'Save Global Defaults';
    }
}
</script>
@endsection
