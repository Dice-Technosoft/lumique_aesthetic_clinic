@extends('layouts.admin')

@section('title', 'Blog & Articles Management - Lumique Admin')
@section('breadcrumb_parent', 'Website CMS')
@section('breadcrumb_current', 'Blog & Articles')
@section('page_title', 'Clinical Articles & Educational Journal')

@section('content')
<div class="admin-panel-card">
    <div class="filter-header-row" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
            <h3>All Published Articles & Journal Posts ({{ $posts->total() }})</h3>
            <small class="text-muted">Dynamic database management for doctor insights, clinical guides, and rich educational articles</small>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <form action="{{ route('admin.blogs') }}" method="GET" class="admin-search-wrapper">
                <span class="search-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" id="blogSearchInput" name="search" value="{{ $search ?? '' }}" placeholder="Search articles, topics..." class="admin-search-input" oninput="filterBlogsLive(this.value)">
                @if(!empty($search))
                    <a href="{{ route('admin.blogs') }}" class="search-clear-link" title="Clear search">&times;</a>
                @endif
            </form>
            <button class="btn btn-gold btn-sm" onclick="openNewBlogModal()">+ Add</button>
        </div>
    </div>

    <div class="table-responsive" style="overflow-x: hidden;">
        <table class="admin-table" style="table-layout: fixed; width: 100%;">
            <thead>
                <tr>
                    <th style="width: 8%;">Image</th>
                    <th style="width: 46%;">Article Title / Slug</th>
                    <th style="width: 20%;">Category</th>
                    <th style="width: 12%;">Status</th>
                    <th style="width: 14%; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                <tr id="blog_row_{{ $post->id }}" class="blog-data-row" data-search="{{ strtolower($post->title . ' ' . ($post->category->name ?? '') . ' ' . $post->slug) }}">
                    <td>
                        <img src="{{ $post->featured_image ?: '/images/logo.jpeg' }}" alt="{{ $post->title }}" style="width: 48px; height: 48px; border-radius: 6px; object-fit: cover; border: 1px solid var(--color-border);">
                    </td>
                    <td style="word-break: break-word;">
                        <strong style="color: var(--color-charcoal);">{{ $post->title }}</strong><br>
                        <code style="color: var(--color-crimson); font-size: 0.75rem;">/blog/{{ $post->slug }}</code>
                    </td>
                    <td><span class="badge badge-gold">{{ $post->category->name ?? 'Dermatology' }}</span></td>
                    <td>
                        <span class="status-badge status-{{ $post->status === 'published' ? 'published' : 'draft' }}">{{ ucfirst($post->status) }}</span>
                    </td>
                    <td style="text-align: right;">
                        <div class="table-actions-group" style="justify-content: flex-end;">
                            <a href="{{ route('blog.show', $post->slug) }}" target="_blank" class="action-icon-btn btn-view" data-tooltip="Live Article Preview" aria-label="View Live Article">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"></path><polyline points="15 3 21 3 21 9"></polyline><line x1="10" y1="14" x2="21" y2="3"></line></svg>
                            </a>
                            <button type="button" class="action-icon-btn btn-edit" data-tooltip="Edit Article" aria-label="Edit Article" onclick='openEditBlogModal(@json($post))'>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button type="button" class="action-icon-btn btn-delete" data-tooltip="Delete Article" aria-label="Delete Article" onclick="deleteBlog({{ $post->id }}, '{{ addslashes($post->title) }}')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">No articles found in database.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-pagination-row">
        {{ $posts->links() }}
    </div>
</div>

<!-- Modal: Add / Edit Blog Post with Rich Text WYSIWYG Editor -->
<div class="modal-overlay" id="blogModal">
    <div class="modal-card" style="max-width: 860px; max-height: 92vh; overflow-y: auto;">
        <button class="modal-close" onclick="closeBlogModal()">&times;</button>
        <div class="modal-header">
            <h3 id="blogModalTitle">Write Clinical Article</h3>
            <p id="blogModalSub" class="text-muted" style="font-size: 0.85rem;">Create rich formatted educational journal post</p>
        </div>
        <form onsubmit="handleBlogSubmit(event)" id="blogForm" enctype="multipart/form-data">
            <input type="hidden" id="post_id" name="id">

            <div class="form-group mb-3">
                <label for="post_title">Article Title *</label>
                <input type="text" id="post_title" name="title" required class="form-control" placeholder="e.g. Polynucleotides vs Hyaluronic Acid Skin Boosters">
            </div>

            <div class="form-row" style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="flex: 1.5;">
                    <label for="post_category_id">Category *</label>
                    <select id="post_category_id" name="category_id" class="form-control" required>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="post_read_time">Read Time (Mins)</label>
                    <input type="number" id="post_read_time" name="read_time_minutes" class="form-control" value="5" min="1">
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="post_status">Publish Status</label>
                    <select id="post_status" name="status" class="form-control">
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
            </div>

            <!-- Cover Image (File Upload or Direct URL Input with Live Preview) -->
            <div class="form-group mb-3" style="background: var(--color-ivory); padding: 1rem; border-radius: 6px; border: 1px solid var(--color-border);">
                <label style="font-weight: 600; display: block; margin-bottom: 0.35rem;">Article Cover Image (Upload or URL) *</label>
                <div style="display: flex; gap: 12px; align-items: center;">
                    <img id="post_image_preview" src="/images/logo.jpeg" alt="Cover Preview" style="width: 70px; height: 50px; object-fit: cover; border-radius: 4px; border: 1px solid #ccc; flex-shrink: 0; background: #fff;">
                    <div style="flex: 1; display: flex; flex-direction: column; gap: 6px;">
                        <input type="file" id="post_image_file" name="image_file" accept="image/*" class="form-control form-control-sm" onchange="previewBlogImage(this)">
                        <input type="text" id="post_featured_image" name="featured_image" placeholder="Or enter image URL here" class="form-control form-control-sm" oninput="updateBlogImageFromUrl(this.value)">
                    </div>
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="post_excerpt">Article Summary / Excerpt *</label>
                <textarea id="post_excerpt" name="excerpt" rows="2" required class="form-control" placeholder="Brief 1-2 sentence summary displayed on blog index cards..."></textarea>
            </div>

            <!-- RICH TEXT WYSIWYG EDITOR TOOLBAR & CONTENT AREA -->
            <div class="form-group mb-3">
                <label style="font-weight: 600; display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                    <span>Article Content (Rich Text Description) *</span>
                    <button type="button" class="btn btn-outline-gold btn-xs" onclick="toggleHtmlSourceMode()" id="htmlToggleBtn" style="font-size: 0.72rem; padding: 2px 8px;">
                        &lt;/&gt; Source View
                    </button>
                </label>

                <div class="rich-editor-wrapper" style="border: 1px solid var(--color-border); border-radius: 6px; overflow: hidden; background: #fff;">
                    <!-- Formatting Toolbar -->
                    <div class="rich-editor-toolbar" style="display: flex; flex-wrap: wrap; gap: 4px; padding: 6px 8px; background: #f8fafc; border-bottom: 1px solid #e2e8f0; align-items: center;">
                        
                        <!-- Heading Dropdown -->
                        <select onchange="execCmd('formatBlock', this.value); this.value='';" style="height: 28px; font-size: 0.78rem; padding: 2px 6px; border: 1px solid #cbd5e1; border-radius: 3px; background: #fff;">
                            <option value="">Heading / Paragraph</option>
                            <option value="<h1>">Heading 1</option>
                            <option value="<h2>">Heading 2</option>
                            <option value="<h3>">Heading 3</option>
                            <option value="<h4>">Heading 4</option>
                            <option value="<p>">Normal Paragraph</option>
                            <option value="<blockquote>">Blockquote</option>
                        </select>

                        <!-- Font Family -->
                        <select onchange="execCmd('fontName', this.value); this.value='';" style="height: 28px; font-size: 0.78rem; padding: 2px 6px; border: 1px solid #cbd5e1; border-radius: 3px; background: #fff;">
                            <option value="">Font Family</option>
                            <option value="Inter, sans-serif">Modern Sans</option>
                            <option value="'Playfair Display', serif">Luxury Serif</option>
                            <option value="Georgia, serif">Classic Georgia</option>
                            <option value="Courier New, monospace">Monospace</option>
                        </select>

                        <!-- Font Size -->
                        <select onchange="execFontSize(this.value); this.value='';" style="height: 28px; font-size: 0.78rem; padding: 2px 6px; border: 1px solid #cbd5e1; border-radius: 3px; background: #fff;">
                            <option value="">Font Size</option>
                            <option value="1">Small (12px)</option>
                            <option value="3">Normal (15px)</option>
                            <option value="4">Medium (18px)</option>
                            <option value="5">Large (22px)</option>
                            <option value="6">Extra Large (28px)</option>
                        </select>

                        <div style="width: 1px; height: 22px; background: #cbd5e1; margin: 0 4px;"></div>

                        <!-- Basic Formatting Buttons -->
                        <button type="button" class="toolbar-btn" onclick="execCmd('bold')" title="Bold (Ctrl+B)" style="font-weight: 700; width: 28px; height: 28px;">B</button>
                        <button type="button" class="toolbar-btn" onclick="execCmd('italic')" title="Italic (Ctrl+I)" style="font-style: italic; width: 28px; height: 28px;">I</button>
                        <button type="button" class="toolbar-btn" onclick="execCmd('underline')" title="Underline (Ctrl+U)" style="text-decoration: underline; width: 28px; height: 28px;">U</button>
                        <button type="button" class="toolbar-btn" onclick="execCmd('strikeThrough')" title="Strikethrough" style="text-decoration: line-through; width: 28px; height: 28px;">S</button>

                        <div style="width: 1px; height: 22px; background: #cbd5e1; margin: 0 4px;"></div>

                        <!-- Text Color Picker -->
                        <div style="position: relative; display: inline-flex; align-items: center;" title="Text Color">
                            <label style="margin: 0; cursor: pointer; display: flex; align-items: center; gap: 2px; font-size: 0.78rem; font-weight: 700; height: 28px; padding: 0 6px; border: 1px solid #cbd5e1; border-radius: 3px; background: #fff;">
                                <span style="border-bottom: 3px solid #8B1538;">A</span>
                                <input type="color" onchange="execCmd('foreColor', this.value)" style="opacity: 0; width: 0; height: 0; position: absolute;">
                            </label>
                        </div>

                        <!-- Highlight Background Color Picker -->
                        <div style="position: relative; display: inline-flex; align-items: center;" title="Highlight Background Color">
                            <label style="margin: 0; cursor: pointer; display: flex; align-items: center; gap: 2px; font-size: 0.78rem; font-weight: 700; height: 28px; padding: 0 6px; border: 1px solid #cbd5e1; border-radius: 3px; background: #fff;">
                                <span style="background: #fff3b0; padding: 0 2px;">🖍️</span>
                                <input type="color" value="#fff3b0" onchange="execCmd('hiliteColor', this.value)" style="opacity: 0; width: 0; height: 0; position: absolute;">
                            </label>
                        </div>

                        <div style="width: 1px; height: 22px; background: #cbd5e1; margin: 0 4px;"></div>

                        <!-- Alignment -->
                        <button type="button" class="toolbar-btn" onclick="execCmd('justifyLeft')" title="Align Left" style="width: 28px; height: 28px;">⇤</button>
                        <button type="button" class="toolbar-btn" onclick="execCmd('justifyCenter')" title="Align Center" style="width: 28px; height: 28px;">⇥⇤</button>
                        <button type="button" class="toolbar-btn" onclick="execCmd('justifyRight')" title="Align Right" style="width: 28px; height: 28px;">⇥</button>
                        <button type="button" class="toolbar-btn" onclick="execCmd('justifyFull')" title="Justify" style="width: 28px; height: 28px;">≡</button>

                        <div style="width: 1px; height: 22px; background: #cbd5e1; margin: 0 4px;"></div>

                        <!-- Lists & Inserts -->
                        <button type="button" class="toolbar-btn" onclick="execCmd('insertUnorderedList')" title="Bullet List" style="width: 28px; height: 28px;">• List</button>
                        <button type="button" class="toolbar-btn" onclick="execCmd('insertOrderedList')" title="Numbered List" style="width: 28px; height: 28px;">1. List</button>
                        <button type="button" class="toolbar-btn" onclick="insertEditorLink()" title="Insert Link" style="width: 28px; height: 28px;">🔗</button>
                        <button type="button" class="toolbar-btn" onclick="insertEditorImage()" title="Insert Image URL" style="width: 28px; height: 28px;">🖼️</button>
                        <button type="button" class="toolbar-btn" onclick="execCmd('insertHorizontalRule')" title="Divider Line" style="width: 28px; height: 28px;">—</button>
                        <button type="button" class="toolbar-btn" onclick="execCmd('removeFormat')" title="Clear Formatting" style="width: 28px; height: 28px;">✕ Tx</button>
                        <button type="button" class="toolbar-btn" onclick="execCmd('undo')" title="Undo" style="width: 28px; height: 28px;">↺</button>
                        <button type="button" class="toolbar-btn" onclick="execCmd('redo')" title="Redo" style="width: 28px; height: 28px;">↻</button>
                    </div>

                    <!-- Visual Editable Area -->
                    <div id="richTextEditor" contenteditable="true" style="min-height: 280px; max-height: 480px; overflow-y: auto; padding: 1.25rem; font-family: var(--font-sans); font-size: 0.95rem; line-height: 1.8; color: var(--color-charcoal); outline: none;"></div>
                    
                    <!-- Raw HTML Textarea (hidden by default, toggled via source view) -->
                    <textarea id="post_content" name="content" style="display: none; width: 100%; min-height: 280px; font-family: monospace; font-size: 0.85rem; padding: 1rem; border: none; outline: none; background: #222; color: #a5f3fc;"></textarea>
                </div>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--color-border);">
                <button type="button" class="btn btn-outline-gold btn-sm" onclick="closeBlogModal()">Cancel</button>
                <button type="submit" id="savePostBtn" class="btn btn-gold btn-sm">Save</button>
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
#richTextEditor:focus {
    box-shadow: inset 0 0 0 2px rgba(122, 28, 46, 0.2);
}
#richTextEditor blockquote {
    border-left: 4px solid var(--color-crimson, #8B1538);
    padding-left: 1rem;
    margin: 1rem 0;
    font-style: italic;
    color: #555;
}
#richTextEditor h1, #richTextEditor h2, #richTextEditor h3, #richTextEditor h4 {
    font-family: var(--font-serif);
    margin: 1rem 0 0.5rem;
    color: var(--color-charcoal);
}
#richTextEditor img {
    max-width: 100%;
    border-radius: 6px;
    margin: 0.75rem 0;
}
</style>
@endsection

@section('scripts')
<script>
    let isHtmlSourceMode = false;

    // Live Client-Side Realtime Search
    function filterBlogsLive(query) {
        query = query.toLowerCase().trim();
        const rows = document.querySelectorAll('.blog-data-row');
        rows.forEach(row => {
            const rowData = row.getAttribute('data-search') || '';
            row.style.display = (!query || rowData.includes(query)) ? '' : 'none';
        });
    }

    // Rich Text WYSIWYG helper functions
    function execCmd(command, value = null) {
        if (isHtmlSourceMode) toggleHtmlSourceMode();
        document.getElementById('richTextEditor').focus();
        document.execCommand(command, false, value);
    }

    function execFontSize(size) {
        if (!size) return;
        execCmd('fontSize', size);
    }

    function insertEditorLink() {
        const url = prompt('Enter web hyperlink URL:', 'https://');
        if (url && url !== 'https://') {
            execCmd('createLink', url);
        }
    }

    function insertEditorImage() {
        const url = prompt('Enter Image URL:', 'https://');
        if (url && url !== 'https://') {
            execCmd('insertImage', url);
        }
    }

    function toggleHtmlSourceMode() {
        const editor = document.getElementById('richTextEditor');
        const textarea = document.getElementById('post_content');
        const btn = document.getElementById('htmlToggleBtn');

        if (!isHtmlSourceMode) {
            textarea.value = editor.innerHTML;
            editor.style.display = 'none';
            textarea.style.display = 'block';
            btn.innerText = '👁️ Visual View';
            isHtmlSourceMode = true;
        } else {
            editor.innerHTML = textarea.value;
            textarea.style.display = 'none';
            editor.style.display = 'block';
            btn.innerText = '</> Source View';
            isHtmlSourceMode = false;
        }
    }

    function previewBlogImage(input) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('post_image_preview').src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }

    function updateBlogImageFromUrl(url) {
        if (url && url.trim()) {
            document.getElementById('post_image_preview').src = url.trim();
        }
    }

    function openNewBlogModal() {
        document.getElementById('blogForm').reset();
        document.getElementById('post_id').value = '';
        document.getElementById('post_featured_image').value = '';
        document.getElementById('post_image_preview').src = '/images/logo.jpeg';
        document.getElementById('richTextEditor').innerHTML = '<p>Write your medical article description here...</p>';
        document.getElementById('post_content').value = '';
        if (isHtmlSourceMode) toggleHtmlSourceMode();

        document.getElementById('blogModalTitle').innerText = 'Write Clinical Article';
        document.getElementById('blogModalSub').innerText = 'Publish a new doctor journal post directly to database';
        document.getElementById('savePostBtn').innerText = 'Save';
        document.getElementById('blogModal').classList.add('open');
    }

    function openEditBlogModal(post) {
        document.getElementById('post_id').value = post.id;
        document.getElementById('post_title').value = post.title || '';
        document.getElementById('post_category_id').value = post.category_id || '';
        document.getElementById('post_read_time').value = post.read_time_minutes || 5;
        document.getElementById('post_status').value = post.status || 'published';
        document.getElementById('post_featured_image').value = post.featured_image || '';
        document.getElementById('post_image_preview').src = post.featured_image || '/images/logo.jpeg';
        document.getElementById('post_excerpt').value = post.excerpt || '';
        
        const content = post.content || '';
        document.getElementById('richTextEditor').innerHTML = content;
        document.getElementById('post_content').value = content;
        if (isHtmlSourceMode) toggleHtmlSourceMode();

        document.getElementById('blogModalTitle').innerText = 'Edit Article: ' + post.title;
        document.getElementById('blogModalSub').innerText = 'Update article content, excerpt, and formatting in database';
        document.getElementById('savePostBtn').innerText = 'Update';
        document.getElementById('blogModal').classList.add('open');
    }

    function closeBlogModal() {
        document.getElementById('blogModal').classList.remove('open');
    }

    async function handleBlogSubmit(e) {
        e.preventDefault();
        const btn = document.getElementById('savePostBtn');
        const id = document.getElementById('post_id').value;
        btn.disabled = true;
        btn.innerText = id ? 'Updating...' : 'Saving...';

        // Sync visual rich text to hidden content textarea
        const editor = document.getElementById('richTextEditor');
        const textarea = document.getElementById('post_content');
        if (isHtmlSourceMode) {
            editor.innerHTML = textarea.value;
        } else {
            textarea.value = editor.innerHTML;
        }

        const formData = new FormData(document.getElementById('blogForm'));
        // Ensure content is set from current HTML
        formData.set('content', textarea.value);

        if (id) {
            formData.append('_method', 'PUT');
        }
        const url = id ? `/api/v1/admin/blogs/${id}` : '/api/v1/admin/blogs';

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            });

            const data = await res.json();
            if (res.ok && data.success) {
                closeBlogModal();
                showToast(id ? 'Article updated successfully!' : 'Article published successfully!', 'success');
                setTimeout(() => location.reload(), 800);
            } else {
                showToast(data.message || 'Failed to save article', 'error');
            }
        } catch(err) {
            showToast('Network error saving article', 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = id ? 'Update' : 'Save';
        }
    }

    function deleteBlog(id, title) {
        confirmDeleteModal('Delete Article', title, async () => {
            try {
                const res = await fetch(`/api/v1/admin/blogs/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    const row = document.getElementById(`blog_row_${id}`);
                    if (row) row.remove();
                    showToast(`"${title}" deleted from database!`, 'success');
                } else {
                    showToast(data.message || 'Failed to delete article', 'error');
                }
            } catch(err) {
                showToast('Network error deleting article', 'error');
            }
        });
    }
</script>
@endsection
