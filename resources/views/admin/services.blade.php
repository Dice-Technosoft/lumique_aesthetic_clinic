@extends('layouts.admin')

@section('title', 'Treatments & Services Management - Lumique Admin')
@section('breadcrumb_parent', 'Clinical Catalog')
@section('breadcrumb_current', 'Treatments & Services')
@section('page_title', 'Clinical Treatments & Procedures')

@section('content')
<div class="admin-panel-card">
    <div class="filter-header-row" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
            <h3>All Medical Procedures ({{ $services->total() }})</h3>
            <small class="text-muted">Manage clinical procedures, protocol steps, sub-images, videos, duration, downtime and pricing</small>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <form action="{{ route('admin.services') }}" method="GET" class="admin-search-wrapper">
                <span class="search-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" id="serviceSearchInput" name="search" value="{{ $search ?? '' }}" placeholder="Search procedures, benefits..." class="admin-search-input" oninput="filterServicesLive(this.value)">
                @if(!empty($search))
                    <a href="{{ route('admin.services') }}" class="search-clear-link" title="Clear search">&times;</a>
                @endif
            </form>
            <button class="btn btn-gold btn-sm" onclick="openNewServiceModal()">+ Add</button>
        </div>
    </div>

    <div class="table-responsive" style="overflow-x: hidden;">
        <table class="admin-table" style="table-layout: fixed; width: 100%;">
            <thead>
                <tr>
                    <th style="width: 6%;">Image</th>
                    <th style="width: 28%;">Treatment / Slug</th>
                    <th style="width: 14%;">Category</th>
                    <th style="width: 10%;">Duration</th>
                    <th style="width: 10%;">Price</th>
                    <th style="width: 8%; text-align: center;">Media</th>
                    <th style="width: 10%; text-align: center;">Status</th>
                    <th style="width: 14%; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $svc)
                <tr id="service_row_{{ $svc->id }}" class="service-data-row" data-search="{{ strtolower($svc->title . ' ' . $svc->category . ' ' . $svc->short_description . ' ' . $svc->slug) }}">
                    <td>
                        <img src="{{ $svc->featured_image ?: '/images/logo.jpeg' }}" alt="{{ $svc->title }}" style="width: 44px; height: 44px; border-radius: 6px; object-fit: cover; border: 1px solid var(--color-border);">
                    </td>
                    <td style="word-break: break-word;">
                        <strong style="color: var(--color-charcoal); font-size: 0.9rem;">{{ $svc->title }}</strong>
                        <div style="font-size: 0.75rem; color: var(--color-charcoal-muted);">/treatment/{{ $svc->slug }}</div>
                    </td>
                    <td>
                        @php
                            $catObj = $categories->firstWhere('slug', $svc->category);
                        @endphp
                        <span class="badge badge-neutral">{{ $catObj ? $catObj->name : strtoupper($svc->category) }}</span>
                    </td>
                    <td>
                        <span style="font-size: 0.85rem;">{{ $svc->duration ?? '45 Mins' }}</span>
                        @if(!empty($svc->downtime))
                            <div style="font-size: 0.7rem; color: var(--color-charcoal-muted);">DT: {{ $svc->downtime }}</div>
                        @endif
                    </td>
                    <td><strong class="gold-text">{{ $svc->price_starting_at ?? 'Consultation' }}</strong></td>
                    <td style="text-align: center;">
                        @php
                            $imgCount = (!empty($svc->gallery_images) && is_array($svc->gallery_images)) ? count($svc->gallery_images) : 0;
                            $vidCount = (!empty($svc->gallery_videos) && is_array($svc->gallery_videos)) ? count($svc->gallery_videos) : (!empty($svc->video_url) ? 1 : 0);
                            $totalMedia = $imgCount + $vidCount;
                        @endphp
                        @if($totalMedia > 0)
                            <span class="badge badge-gold" style="font-weight: 700; font-size: 0.82rem; padding: 3px 8px; display: inline-flex; align-items: center; gap: 3px;" title="{{ $imgCount }} Images &bull; {{ $vidCount }} Videos">
                                🎬 {{ $totalMedia }}
                            </span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <span class="status-badge status-{{ $svc->status }}" style="white-space: nowrap;">{{ ucfirst($svc->status) }}</span>
                    </td>
                    <td style="text-align: right;">
                        <div class="table-actions-group" style="justify-content: flex-end; flex-wrap: nowrap; gap: 5px;">
                            <a href="{{ route('services.show', $svc->slug) }}" target="_blank" class="action-icon-btn btn-view" data-tooltip="Live Website Preview" aria-label="Live Preview">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                            </a>
                            <button type="button" class="action-icon-btn btn-edit" data-tooltip="Edit Treatment" aria-label="Edit Treatment" onclick='openEditServiceModal(@json($svc))'>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button type="button" class="action-icon-btn btn-delete" data-tooltip="Delete Treatment" aria-label="Delete Treatment" onclick="deleteService({{ $svc->id }}, '{{ addslashes($svc->title) }}')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="empty-services-row">
                    <td colspan="8" class="text-center py-5 text-muted">No treatments found in database.</td>
                </tr>
                @endforelse
                <tr id="no-services-live-matches-row" style="display: none;">
                    <td colspan="8" class="text-center py-5 text-muted">
                        No clinical treatments found matching "<span id="liveServiceSearchQuery"></span>".
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Luxury Styled Pagination -->
    <div class="admin-pagination-row">
        {{ $services->links() }}
    </div>
</div>

<!-- Modal: Add / Edit Clinical Service -->
<div class="modal-overlay" id="serviceModal">
    <div class="modal-card" style="max-width: 820px; max-height: 92vh; overflow-y: auto;">
        <button type="button" class="modal-close" onclick="closeServiceModal()">&times;</button>
        <div class="modal-header">
            <h3 id="serviceModalTitle">Add Clinical Treatment</h3>
            <p id="serviceModalSub" class="text-muted" style="font-size: 0.85rem;">Manage procedure pricing, sub-images, videos, duration, downtime and clinical details</p>
        </div>
        <form onsubmit="handleServiceSubmit(event)" id="serviceForm" enctype="multipart/form-data">
            <input type="hidden" id="svc_id" name="id">

            <div class="form-group mb-3">
                <label for="svc_title">Treatment Title *</label>
                <input type="text" id="svc_title" name="title" required class="form-control" placeholder="e.g. Medical HydraFacial MD®">
            </div>

            <div class="form-row" style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label for="svc_category">Category *</label>
                    <select id="svc_category" name="category" class="form-control" required>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->slug }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="svc_price">Starting Price</label>
                    <input type="text" id="svc_price" name="price_starting_at" class="form-control" placeholder="e.g. ₹4,999">
                </div>
            </div>

            <!-- Duration, Downtime & Status -->
            <div class="form-row" style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label for="svc_duration">Duration</label>
                    <input type="text" id="svc_duration" name="duration" class="form-control" placeholder="e.g. 45 Minutes">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="svc_downtime">Downtime (Recovery Period)</label>
                    <input type="text" id="svc_downtime" name="downtime" class="form-control" placeholder="e.g. Minimal, Zero Downtime, 1-2 Days">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="svc_status">Publish Status</label>
                    <select id="svc_status" name="status" class="form-control">
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
            </div>

            <!-- Primary Featured Image -->
            <div class="form-group mb-3">
                <label for="image_file">Primary Cover Image *</label>
                <div style="display: flex; gap: 10px; align-items: center; background: var(--color-ivory); padding: 0.75rem; border-radius: 6px; border: 1px solid var(--color-border);">
                    <img id="svc_image_preview" src="/images/logo.jpeg" alt="Preview" style="width: 54px; height: 54px; object-fit: cover; border-radius: 4px; border: 1px solid #ddd; background: #fff;">
                    <div style="flex: 1;">
                        <input type="file" id="image_file" name="image_file" accept="image/*" class="form-control form-control-sm" onchange="previewSvcImage(this)">
                    </div>
                </div>
                <input type="hidden" id="svc_featured_image" name="featured_image">
            </div>

            <!-- Multiple Sub-Images / Procedure Clinical Gallery -->
            <div class="form-group mb-3" style="background: #fdfaf6; padding: 1rem; border-radius: 6px; border: 1px solid var(--color-border);">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                    <span style="font-size: 1.1rem;">📸</span>
                    <label style="font-weight: 600; margin: 0; color: var(--color-charcoal);">Treatment Sub-Images / Clinical Gallery</label>
                </div>
                <small class="text-muted" style="display: block; margin-bottom: 0.75rem;">Upload multiple procedure sub-photos or before/after gallery images for the detail page</small>
                
                <input type="file" id="gallery_files" name="gallery_files[]" accept="image/*" multiple class="form-control form-control-sm mb-2" onchange="previewGalleryFiles(this)">
                
                <div id="gallery_preview_container" style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 0.5rem;"></div>
                <input type="hidden" id="svc_gallery_images" name="gallery_images">
            </div>

            <!-- Multiple Treatment Videos (Direct Media Upload & URLs) -->
            <div class="form-group mb-3" style="background: #f8fafc; padding: 1rem; border-radius: 6px; border: 1px solid var(--color-border);">
                <div style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 0.35rem;">
                    <span style="font-size: 1.1rem; color: #e50914;">▶</span>
                    <label style="font-weight: 600; margin: 0; color: var(--color-charcoal);">Treatment Procedure Videos (Direct Media Upload & URLs)</label>
                </div>
                <small class="text-muted" style="display: block; margin-bottom: 0.75rem;">Upload multiple direct video files (MP4, WEBM, MOV) or enter online/YouTube video URLs</small>
                
                <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-bottom: 0.5rem;">
                    <div>
                        <label style="font-size: 0.78rem; font-weight: 600; color: #475569;">1. Direct Video Media File Upload (MP4, MOV, WEBM):</label>
                        <input type="file" id="video_files" name="video_files[]" accept="video/mp4,video/webm,video/mov,video/quicktime,video/*" multiple class="form-control form-control-sm mb-1" onchange="previewVideoFiles(this)">
                    </div>

                    <div>
                        <label style="font-size: 0.78rem; font-weight: 600; color: #475569;">2. Or Online Video / YouTube Links (Add Multiple):</label>
                        <div style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem;">
                            <input type="text" id="svc_add_video_link" class="form-control form-control-sm" placeholder="Paste YouTube link (e.g. https://www.youtube.com/watch?v=M7lc1UVf-VE)" onkeydown="if(event.key==='Enter'){event.preventDefault();addYouTubeVideoLink();}">
                            <button type="button" class="btn btn-gold btn-sm" onclick="addYouTubeVideoLink()" style="white-space: nowrap; padding: 4px 12px; font-size: 0.8rem;">+ Add Link</button>
                        </div>
                        <input type="text" id="svc_video_title" name="video_title" class="form-control form-control-sm" placeholder="Primary Video Title / Demonstration Heading (Optional)">
                    </div>
                </div>

                <!-- Existing & Newly Added Videos Preview Container -->
                <div id="video_preview_container" style="display: flex; gap: 0.5rem; flex-wrap: wrap; margin-top: 0.75rem;"></div>
                <input type="hidden" id="svc_gallery_videos" name="gallery_videos">
            </div>

            <div class="form-group mb-3">
                <label for="svc_short_desc">Short Summary *</label>
                <textarea id="svc_short_desc" name="short_description" rows="2" required class="form-control" placeholder="Brief summary for card display on frontend..."></textarea>
            </div>

            <!-- RICH TEXT WYSIWYG MEDICAL DESCRIPTION -->
            <div class="form-group mb-3">
                <label style="font-weight: 600; display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                    <span>Full Medical Description (Rich Text)</span>
                    <button type="button" class="btn btn-outline-gold btn-xs" onclick="toggleServiceHtmlSourceMode()" id="svcHtmlToggleBtn" style="font-size: 0.72rem; padding: 2px 8px;">
                        &lt;/&gt; Source View
                    </button>
                </label>

                <div class="rich-editor-wrapper" style="border: 1px solid var(--color-border); border-radius: 6px; overflow: hidden; background: #fff;">
                    <!-- Formatting Toolbar -->
                    <div class="rich-editor-toolbar" style="display: flex; flex-wrap: wrap; gap: 4px; padding: 6px 8px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; align-items: center;">
                        
                        <!-- Heading / Block Dropdown -->
                        <select onchange="execSvcCmd('formatBlock', this.value); this.value='';" style="height: 28px; font-size: 0.78rem; padding: 2px 6px; border: 1px solid #cbd5e1; border-radius: 3px; background: #fff;" title="Heading / Block style">
                            <option value="">Heading / Paragraph</option>
                            <option value="<h2>">Heading 2</option>
                            <option value="<h3>">Heading 3</option>
                            <option value="<h4>">Heading 4</option>
                            <option value="<p>">Normal Paragraph</option>
                            <option value="<blockquote>">Quote Block</option>
                        </select>

                        <!-- Sizing Option (Font Size) -->
                        <select onchange="execSvcFontSize(this.value); this.value='';" style="height: 28px; font-size: 0.78rem; padding: 2px 6px; border: 1px solid #cbd5e1; border-radius: 3px; background: #fff;" title="Font Size">
                            <option value="">Font Size</option>
                            <option value="1">Small (12px)</option>
                            <option value="3">Normal (15px)</option>
                            <option value="4">Medium (18px)</option>
                            <option value="5">Large (22px)</option>
                            <option value="6">Extra Large (28px)</option>
                        </select>

                        <div style="width: 1px; height: 22px; background: #cbd5e1; margin: 0 4px;"></div>

                        <!-- Basic Formatting Buttons -->
                        <button type="button" class="toolbar-btn" onclick="execSvcCmd('bold')" title="Bold (Ctrl+B)" style="font-weight: 700; width: 28px; height: 28px;">B</button>
                        <button type="button" class="toolbar-btn" onclick="execSvcCmd('italic')" title="Italic (Ctrl+I)" style="font-style: italic; width: 28px; height: 28px;">I</button>
                        <button type="button" class="toolbar-btn" onclick="execSvcCmd('underline')" title="Underline (Ctrl+U)" style="text-decoration: underline; width: 28px; height: 28px;">U</button>
                        <button type="button" class="toolbar-btn" onclick="execSvcCmd('strikeThrough')" title="Strikethrough" style="text-decoration: line-through; width: 28px; height: 28px;">S</button>

                        <div style="width: 1px; height: 22px; background: #cbd5e1; margin: 0 4px;"></div>

                        <!-- Text Color Picker -->
                        <div style="position: relative; display: inline-flex; align-items: center;" title="Text Color">
                            <label style="margin: 0; cursor: pointer; display: flex; align-items: center; gap: 2px; font-size: 0.78rem; font-weight: 700; height: 28px; padding: 0 6px; border: 1px solid #cbd5e1; border-radius: 3px; background: #fff;">
                                <span style="border-bottom: 3px solid #8B1538;">A</span>
                                <input type="color" onchange="execSvcCmd('foreColor', this.value)" style="opacity: 0; width: 0; height: 0; position: absolute;">
                            </label>
                        </div>

                        <!-- Highlight Background Color Picker -->
                        <div style="position: relative; display: inline-flex; align-items: center;" title="Highlight Color">
                            <label style="margin: 0; cursor: pointer; display: flex; align-items: center; gap: 2px; font-size: 0.78rem; font-weight: 700; height: 28px; padding: 0 6px; border: 1px solid #cbd5e1; border-radius: 3px; background: #fff;">
                                <span style="background: #fff3b0; padding: 0 2px;">🖍️</span>
                                <input type="color" value="#fff3b0" onchange="execSvcCmd('hiliteColor', this.value)" style="opacity: 0; width: 0; height: 0; position: absolute;">
                            </label>
                        </div>

                        <div style="width: 1px; height: 22px; background: #cbd5e1; margin: 0 4px;"></div>

                        <!-- Alignment -->
                        <button type="button" class="toolbar-btn" onclick="execSvcCmd('justifyLeft')" title="Align Left" style="width: 28px; height: 28px;">⇤</button>
                        <button type="button" class="toolbar-btn" onclick="execSvcCmd('justifyCenter')" title="Align Center" style="width: 28px; height: 28px;">⇥⇤</button>
                        <button type="button" class="toolbar-btn" onclick="execSvcCmd('justifyRight')" title="Align Right" style="width: 28px; height: 28px;">⇥</button>

                        <div style="width: 1px; height: 22px; background: #cbd5e1; margin: 0 4px;"></div>

                        <!-- Bullet Points & Numbered Lists -->
                        <button type="button" class="toolbar-btn" onclick="execSvcCmd('insertUnorderedList')" title="Bullet Points List" style="padding: 0 8px; height: 28px; font-weight: 600; font-size: 0.75rem;">• Bullets</button>
                        <button type="button" class="toolbar-btn" onclick="execSvcCmd('insertOrderedList')" title="Numbered List" style="padding: 0 8px; height: 28px; font-weight: 600; font-size: 0.75rem;">1. Numbered</button>

                        <div style="width: 1px; height: 22px; background: #cbd5e1; margin: 0 4px;"></div>

                        <!-- Extras -->
                        <button type="button" class="toolbar-btn" onclick="insertSvcLink()" title="Insert Link" style="width: 28px; height: 28px;">🔗</button>
                        <button type="button" class="toolbar-btn" onclick="execSvcCmd('removeFormat')" title="Clear Formatting" style="width: 28px; height: 28px;">✕ Tx</button>
                        <button type="button" class="toolbar-btn" onclick="execSvcCmd('undo')" title="Undo" style="width: 28px; height: 28px;">↺</button>
                        <button type="button" class="toolbar-btn" onclick="execSvcCmd('redo')" title="Redo" style="width: 28px; height: 28px;">↻</button>
                    </div>

                    <!-- Visual Editable Area -->
                    <div id="richServiceEditor" contenteditable="true" style="min-height: 200px; max-height: 420px; overflow-y: auto; padding: 1rem; font-family: var(--font-sans); font-size: 0.95rem; line-height: 1.8; color: var(--color-charcoal); outline: none;"></div>
                    
                    <!-- Raw HTML Textarea (hidden by default, toggled via source view) -->
                    <textarea id="svc_desc" name="description" style="display: none; width: 100%; min-height: 200px; font-family: monospace; font-size: 0.85rem; padding: 1rem; border: none; outline: none; background: #222; color: #a5f3fc;"></textarea>
                </div>
                <small class="text-muted" style="font-size: 0.75rem; margin-top: 4px; display: block;">Supports bold text, bullet points, font sizing, headings, colors, and links for medical descriptions.</small>
            </div>

            <div class="form-group mb-3">
                <label for="svc_benefits">Key Clinical Benefits (One per line)</label>
                <textarea id="svc_benefits" name="benefits" rows="2" class="form-control" placeholder="Visible radiance within 24 hours&#10;Painless medical-grade extraction&#10;Stimulates collagen renewal"></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--color-border);">
                <button type="button" class="btn btn-outline-gold btn-sm" onclick="closeServiceModal()">Cancel</button>
                <button type="submit" id="saveSvcBtn" class="btn btn-gold btn-sm">Save</button>
            </div>
        </form>
    </div>
</div>

<style>
.toolbar-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border: 1px solid #cbd5e1;
    background: #ffffff;
    border-radius: 3px;
    font-size: 0.78rem;
    color: #334155;
    cursor: pointer;
    transition: all 0.15s ease;
    padding: 0 4px;
}
.toolbar-btn:hover {
    background: #e2e8f0;
    color: #0f172a;
    border-color: #94a3b8;
}
#richServiceEditor:focus {
    box-shadow: inset 0 0 0 2px rgba(184, 134, 11, 0.25);
}
#richServiceEditor blockquote {
    border-left: 4px solid var(--color-gold, #B8860B);
    padding-left: 1rem;
    margin: 1rem 0;
    font-style: italic;
    color: #555;
}
#richServiceEditor h2, #richServiceEditor h3, #richServiceEditor h4 {
    font-family: var(--font-serif);
    margin: 1rem 0 0.5rem;
    color: var(--color-charcoal);
}
#richServiceEditor ul {
    list-style-type: disc !important;
    margin: 0.75rem 0 0.75rem 1.5rem !important;
    padding-left: 0.5rem !important;
}
#richServiceEditor ol {
    list-style-type: decimal !important;
    margin: 0.75rem 0 0.75rem 1.5rem !important;
    padding-left: 0.5rem !important;
}
#richServiceEditor li {
    margin-bottom: 0.35rem;
}
</style>
@endsection

@section('scripts')
<script>
    let isServiceHtmlSourceMode = false;
    let currentGalleryImages = [];
    let currentGalleryVideos = [];

    // Rich Text WYSIWYG helper functions for Treatment Description
    function execSvcCmd(command, value = null) {
        if (isServiceHtmlSourceMode) toggleServiceHtmlSourceMode();
        const editor = document.getElementById('richServiceEditor');
        editor.focus();
        document.execCommand(command, false, value);
    }

    function execSvcFontSize(size) {
        if (!size) return;
        execSvcCmd('fontSize', size);
    }

    function insertSvcLink() {
        const url = prompt('Enter web hyperlink URL:', 'https://');
        if (url && url !== 'https://') {
            execSvcCmd('createLink', url);
        }
    }

    function toggleServiceHtmlSourceMode() {
        const editor = document.getElementById('richServiceEditor');
        const textarea = document.getElementById('svc_desc');
        const btn = document.getElementById('svcHtmlToggleBtn');

        if (!isServiceHtmlSourceMode) {
            textarea.value = editor.innerHTML;
            editor.style.display = 'none';
            textarea.style.display = 'block';
            btn.innerText = '👁️ Visual View';
            isServiceHtmlSourceMode = true;
        } else {
            editor.innerHTML = textarea.value;
            textarea.style.display = 'none';
            editor.style.display = 'block';
            btn.innerText = '</> Source View';
            isServiceHtmlSourceMode = false;
        }
    }

    // Live Client-Side Realtime Search
    function filterServicesLive(query) {
        query = query.toLowerCase().trim();
        const rows = document.querySelectorAll('.service-data-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const rowData = row.getAttribute('data-search') || '';
            if (!query || rowData.includes(query)) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        const noMatchesRow = document.getElementById('no-services-live-matches-row');
        if (noMatchesRow) {
            if (visibleCount === 0 && rows.length > 0) {
                document.getElementById('liveServiceSearchQuery').textContent = query;
                noMatchesRow.style.display = '';
            } else {
                noMatchesRow.style.display = 'none';
            }
        }
    }

    function previewSvcImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('svc_image_preview').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewSvcVideo(val) {
        val = val ? val.trim() : '';
        if (val) {
            renderVideoPreviews();
        }
    }

    function renderGalleryPreviews() {
        const container = document.getElementById('gallery_preview_container');
        container.innerHTML = '';
        currentGalleryImages.forEach((imgSrc, idx) => {
            const wrapper = document.createElement('div');
            wrapper.style.position = 'relative';
            wrapper.style.width = '68px';
            wrapper.style.height = '68px';
            wrapper.style.borderRadius = '4px';
            wrapper.style.overflow = 'hidden';
            wrapper.style.border = '1px solid #cbd5e1';
            wrapper.style.background = '#fff';

            wrapper.innerHTML = `
                <img src="${imgSrc}" style="width:100%;height:100%;object-fit:cover;">
                <button type="button" onclick="removeGalleryImg(${idx})" style="position:absolute;top:2px;right:2px;background:rgba(0,0,0,0.75);color:#fff;border:none;border-radius:50%;width:18px;height:18px;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;">&times;</button>
            `;
            container.appendChild(wrapper);
        });
        document.getElementById('svc_gallery_images').value = JSON.stringify(currentGalleryImages.filter(url => !url.startsWith('blob:')));
    }

    function removeGalleryImg(idx) {
        currentGalleryImages.splice(idx, 1);
        renderGalleryPreviews();
    }

    function previewGalleryFiles(input) {
        if (input.files && input.files.length) {
            Array.from(input.files).forEach(file => {
                const blobUrl = URL.createObjectURL(file);
                currentGalleryImages.push(blobUrl);
                renderGalleryPreviews();
            });
        }
    }

    function renderVideoPreviews() {
        const container = document.getElementById('video_preview_container');
        container.innerHTML = '';

        currentGalleryVideos.forEach((vidSrc, idx) => {
            const wrapper = document.createElement('div');
            wrapper.style.position = 'relative';
            wrapper.style.width = '120px';
            wrapper.style.height = '72px';
            wrapper.style.borderRadius = '4px';
            wrapper.style.overflow = 'hidden';
            wrapper.style.border = '1px solid #cbd5e1';
            wrapper.style.background = '#000';

            const match = vidSrc.match(/(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=|shorts\/))([\w-]{11})/i);
            const ytid = match ? match[1] : (vidSrc.length === 11 ? vidSrc : null);

            if (ytid) {
                wrapper.innerHTML = `
                    <img src="https://img.youtube.com/vi/${ytid}/hqdefault.jpg" style="width:100%;height:100%;object-fit:cover;opacity:0.85;">
                    <span style="position:absolute;bottom:2px;left:4px;color:#fff;font-size:10px;background:rgba(229,9,20,0.85);padding:1px 4px;border-radius:2px;font-weight:600;">YouTube</span>
                    <button type="button" onclick="removeGalleryVideo(${idx})" style="position:absolute;top:2px;right:2px;background:rgba(0,0,0,0.8);color:#fff;border:none;border-radius:50%;width:18px;height:18px;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;">&times;</button>
                `;
            } else {
                wrapper.innerHTML = `
                    <video src="${vidSrc}" style="width:100%;height:100%;object-fit:cover;" preload="metadata"></video>
                    <span style="position:absolute;bottom:2px;left:4px;color:#fff;font-size:10px;background:rgba(184,134,11,0.85);padding:1px 4px;border-radius:2px;font-weight:600;">Media Video</span>
                    <button type="button" onclick="removeGalleryVideo(${idx})" style="position:absolute;top:2px;right:2px;background:rgba(0,0,0,0.8);color:#fff;border:none;border-radius:50%;width:18px;height:18px;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1;">&times;</button>
                `;
            }
            container.appendChild(wrapper);
        });

        document.getElementById('svc_gallery_videos').value = JSON.stringify(currentGalleryVideos.filter(url => !url.startsWith('blob:')));
    }

    function removeGalleryVideo(idx) {
        currentGalleryVideos.splice(idx, 1);
        renderVideoPreviews();
    }

    function addYouTubeVideoLink() {
        const input = document.getElementById('svc_add_video_link');
        const val = input.value.trim();
        if (!val) return;

        // Support comma, space, or newline separated links
        const urls = val.split(/[\n,]+/).map(u => u.trim()).filter(Boolean);
        urls.forEach(u => {
            if (!currentGalleryVideos.includes(u)) {
                currentGalleryVideos.push(u);
            }
        });
        input.value = '';
        renderVideoPreviews();
    }

    function previewVideoFiles(input) {
        if (input.files && input.files.length) {
            Array.from(input.files).forEach(file => {
                const blobUrl = URL.createObjectURL(file);
                currentGalleryVideos.push(blobUrl);
                renderVideoPreviews();
            });
        }
    }

    function openNewServiceModal() {
        if (isServiceHtmlSourceMode) {
            toggleServiceHtmlSourceMode();
        }
        document.getElementById('serviceForm').reset();
        document.getElementById('svc_id').value = '';
        document.getElementById('svc_featured_image').value = '';
        document.getElementById('svc_image_preview').src = '/images/logo.jpeg';
        document.getElementById('svc_downtime').value = 'Minimal';
        document.getElementById('svc_duration').value = '45 Minutes';
        document.getElementById('richServiceEditor').innerHTML = '';
        document.getElementById('svc_desc').value = '';
        if (document.getElementById('svc_add_video_link')) {
            document.getElementById('svc_add_video_link').value = '';
        }
        if (document.getElementById('svc_video_title')) {
            document.getElementById('svc_video_title').value = '';
        }
        currentGalleryImages = [];
        currentGalleryVideos = [];
        renderGalleryPreviews();
        renderVideoPreviews();
        document.getElementById('serviceModalTitle').innerText = 'Add Clinical Treatment';
        document.getElementById('serviceModalSub').innerText = 'Publish a new procedure directly to database';
        document.getElementById('saveSvcBtn').innerText = 'Save';
        document.getElementById('serviceModal').classList.add('open');
    }

    function openEditServiceModal(svc) {
        if (isServiceHtmlSourceMode) {
            toggleServiceHtmlSourceMode();
        }
        document.getElementById('svc_id').value = svc.id;
        document.getElementById('svc_title').value = svc.title || '';
        document.getElementById('svc_category').value = svc.category || 'skin';
        document.getElementById('svc_price').value = svc.price_starting_at || '';
        document.getElementById('svc_duration').value = svc.duration || '45 Minutes';
        document.getElementById('svc_downtime').value = svc.downtime || 'Minimal';
        document.getElementById('svc_status').value = svc.status || 'published';
        document.getElementById('svc_featured_image').value = svc.featured_image || '';
        document.getElementById('svc_image_preview').src = svc.featured_image || '/images/logo.jpeg';
        document.getElementById('svc_short_desc').value = svc.short_description || '';
        
        let descContent = svc.description || '';
        // If existing description is plain text with newlines (legacy format), convert newlines to paragraphs
        if (descContent && !/<[a-z][\s\S]*>/i.test(descContent)) {
            descContent = descContent.split(/\r\n|\r|\n/).map(line => line.trim() ? `<p>${line}</p>` : '').join('');
        }
        document.getElementById('richServiceEditor').innerHTML = descContent;
        document.getElementById('svc_desc').value = descContent;

        if (document.getElementById('svc_add_video_link')) {
            document.getElementById('svc_add_video_link').value = '';
        }
        if (document.getElementById('svc_video_title')) {
            document.getElementById('svc_video_title').value = svc.video_title || '';
        }

        currentGalleryImages = Array.isArray(svc.gallery_images) ? [...svc.gallery_images] : [];
        renderGalleryPreviews();

        currentGalleryVideos = Array.isArray(svc.gallery_videos) ? [...svc.gallery_videos] : (svc.video_url ? [svc.video_url] : []);
        renderVideoPreviews();

        if (Array.isArray(svc.benefits)) {
            document.getElementById('svc_benefits').value = svc.benefits.join('\n');
        } else {
            document.getElementById('svc_benefits').value = '';
        }

        document.getElementById('serviceModalTitle').innerText = 'Edit Treatment: ' + svc.title;
        document.getElementById('serviceModalSub').innerText = 'Update pricing, downtime, sub-images, video, and clinical details';
        document.getElementById('saveSvcBtn').innerText = 'Update';
        document.getElementById('serviceModal').classList.add('open');
    }

    function closeServiceModal() {
        if (isServiceHtmlSourceMode) {
            toggleServiceHtmlSourceMode();
        }
        document.getElementById('serviceModal').classList.remove('open');
    }

    async function handleServiceSubmit(e) {
        e.preventDefault();

        // Auto-add any uncommitted typed video link before submitting
        if (typeof addYouTubeVideoLink === 'function') {
            addYouTubeVideoLink();
        }

        // Sync rich text editor content with hidden svc_desc textarea
        const editor = document.getElementById('richServiceEditor');
        const textarea = document.getElementById('svc_desc');
        if (!isServiceHtmlSourceMode && editor) {
            textarea.value = editor.innerHTML;
        } else if (isServiceHtmlSourceMode && textarea) {
            editor.innerHTML = textarea.value;
        }

        document.getElementById('svc_gallery_images').value = JSON.stringify(currentGalleryImages.filter(url => !url.startsWith('blob:')));
        document.getElementById('svc_gallery_videos').value = JSON.stringify(currentGalleryVideos.filter(url => !url.startsWith('blob:')));

        const btn = document.getElementById('saveSvcBtn');
        const id = document.getElementById('svc_id').value;
        btn.disabled = true;
        btn.innerText = id ? 'Updating...' : 'Saving...';

        const formData = new FormData(document.getElementById('serviceForm'));
        // Explicitly set synchronized HTML description
        formData.set('description', textarea.value);
        if (id) {
            formData.append('_method', 'PUT');
        }
        const url = id ? `/api/v1/admin/services/${id}` : '/api/v1/admin/services';

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });

            const text = await res.text();
            let data = null;
            try {
                data = JSON.parse(text);
            } catch(e) {
                console.error("Non-JSON server response:", text);
            }

            if (res.ok && data && data.success) {
                closeServiceModal();
                showToast(id ? 'Treatment updated successfully!' : 'Treatment published successfully!', 'success');
                setTimeout(() => location.reload(), 800);
            } else {
                let errMsg = 'Failed to save treatment';
                if (data) {
                    if (data.errors) {
                        const allErrors = Object.values(data.errors).flat();
                        errMsg = allErrors.join(' • ');
                    } else if (data.message) {
                        errMsg = data.message;
                    }
                } else if (res.status === 413) {
                    errMsg = 'Uploaded files are too large for server limits. Please restart server.';
                } else if (res.status === 422) {
                    errMsg = 'Validation failed. Please check all required fields.';
                } else {
                    errMsg = `Server error (${res.status}): Please check upload size or server logs.`;
                }
                showToast(errMsg, 'error');
            }
        } catch(err) {
            showToast('Network error saving treatment: ' + (err.message || 'Please check connection or file size'), 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = id ? 'Update' : 'Save';
        }
    }

    function deleteService(id, title) {
        confirmDeleteModal('Delete Treatment', title, async () => {
            try {
                const res = await fetch(`/api/v1/admin/services/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    const row = document.getElementById(`service_row_${id}`);
                    if (row) row.remove();
                    showToast(`Treatment "${title}" deleted successfully!`, 'success');
                } else {
                    showToast(data.message || 'Failed to delete treatment', 'error');
                }
            } catch(err) {
                showToast('Network error deleting treatment', 'error');
            }
        });
    }
</script>
@endsection
