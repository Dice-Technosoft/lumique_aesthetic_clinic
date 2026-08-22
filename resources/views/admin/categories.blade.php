@extends('layouts.admin')

@section('title', 'Treatment Categories | Lumique Admin')
@section('header_title', 'Treatment Categories')
@section('breadcrumb_parent', 'Website CMS')
@section('breadcrumb_current', 'Categories')

@section('content')
<div class="admin-panel-card">
    <div class="filter-header-row" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
            <h3>All Treatment Categories ({{ $categories->total() }})</h3>
            <small class="text-muted">Manage clinical service categories dynamically with real-time REST API CRUD</small>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <form action="{{ route('admin.categories') }}" method="GET" class="admin-search-wrapper">
                <span class="search-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" id="categorySearchInput" name="search" value="{{ $search ?? '' }}" placeholder="Search categories, slug..." class="admin-search-input" oninput="filterCategoriesLive(this.value)">
                @if(!empty($search))
                    <a href="{{ route('admin.categories') }}" class="search-clear-link" title="Clear search">&times;</a>
                @endif
            </form>
            <button class="btn btn-gold btn-sm" onclick="openNewCategoryModal()">+ Add</button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="admin-table" id="categoriesTable" style="table-layout: fixed; width: 100%;">
            <thead>
                <tr>
                    <th style="width: 22%;">Category Name</th>
                    <th style="width: 16%;">URL Slug</th>
                    <th style="width: 30%;">Description</th>
                    <th style="width: 10%; text-align: center;">Treatments</th>
                    <th style="width: 10%; text-align: center;">Status</th>
                    <th style="width: 12%; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                <tr id="cat-row-{{ $category->id }}" class="category-data-row" data-search="{{ strtolower($category->name . ' ' . $category->slug . ' ' . $category->description) }}">
                    <td>
                        <strong>{{ $category->name }}</strong>
                    </td>
                    <td>
                        <code style="font-size: 0.8rem; background: rgba(197, 160, 89, 0.08); padding: 2px 6px; border-radius: 4px; color: var(--burgundy-deep);">{{ $category->slug }}</code>
                    </td>
                    <td>
                        <small class="text-muted" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                            {{ $category->description ?? 'No description provided.' }}
                        </small>
                    </td>
                    <td style="text-align: center;">
                        <span class="badge" style="background: rgba(197, 160, 89, 0.15); color: var(--burgundy-deep); font-weight: 600; padding: 4px 8px; border-radius: 12px;">
                            {{ $category->services_count ?? 0 }}
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <span class="badge {{ $category->status ? 'badge-success' : 'badge-warning' }}" style="white-space: nowrap;">
                            {{ $category->status ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <div class="table-actions-group" style="justify-content: flex-end; flex-wrap: nowrap; gap: 6px;">
                            <button class="action-icon-btn btn-edit" data-tooltip="Edit Category" onclick='openEditCategoryModal(@json($category))' aria-label="Edit">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button class="action-icon-btn btn-delete" data-tooltip="Delete Category" onclick="deleteCategory({{ $category->id }}, '{{ addslashes($category->name) }}')" aria-label="Delete">
                                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="empty-categories-row">
                    <td colspan="6" class="text-center text-muted" style="padding: 2.5rem;">
                        No treatment categories found matching your query.
                    </td>
                </tr>
                @endforelse
                <tr id="no-live-matches-row" style="display: none;">
                    <td colspan="6" class="text-center text-muted" style="padding: 2.5rem;">
                        No categories found matching "<span id="liveSearchQuery"></span>".
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Luxury Styled Pagination -->
    <div class="admin-pagination-wrapper">
        {{ $categories->links() }}
    </div>
</div>

<!-- Add/Edit Category Modal -->
<div class="modal-overlay" id="categoryModal">
    <div class="modal-card" style="max-width: 620px;">
        <button type="button" class="modal-close" onclick="closeCategoryModal()">&times;</button>
        <div class="modal-header">
            <h3 id="categoryModalTitle">Add New Category</h3>
            <p class="text-muted" style="font-size: 0.85rem;">Manage clinical service categories dynamically</p>
        </div>
        <form id="categoryForm" onsubmit="saveCategory(event)">
            <input type="hidden" id="category_id">
            
            <div class="form-group mb-3">
                <label for="cat_name">Category Name *</label>
                <input type="text" id="cat_name" class="form-control" required placeholder="e.g. Skin Rejuvenation" oninput="autoGenerateSlug(this.value)">
            </div>

            <div class="form-group mb-3">
                <label for="cat_slug">URL Slug *</label>
                <input type="text" id="cat_slug" class="form-control" required placeholder="e.g. skin-rejuvenation">
                <small class="text-muted">Unique URL identifier used in filter tabs and links</small>
            </div>

            <div class="form-row" style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label for="cat_status">Status</label>
                    <select id="cat_status" class="form-control">
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="cat_sort_order">Sort Order</label>
                    <input type="number" id="cat_sort_order" class="form-control" value="0">
                </div>
            </div>

            <div class="form-group mb-3">
                <label for="cat_description">Description</label>
                <textarea id="cat_description" rows="3" class="form-control" placeholder="Brief summary of treatments in this category..."></textarea>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--color-border);">
                <button type="button" class="btn btn-outline-gold btn-sm" onclick="closeCategoryModal()">Cancel</button>
                <button type="submit" class="btn btn-gold btn-sm" id="saveCategoryBtn">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Live Client-Side Realtime Search
    function filterCategoriesLive(query) {
        query = query.toLowerCase().trim();
        const rows = document.querySelectorAll('.category-data-row');
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

        const noMatchesRow = document.getElementById('no-live-matches-row');
        const emptyRow = document.getElementById('empty-categories-row');
        if (noMatchesRow) {
            if (visibleCount === 0 && rows.length > 0) {
                document.getElementById('liveSearchQuery').textContent = query;
                noMatchesRow.style.display = '';
            } else {
                noMatchesRow.style.display = 'none';
            }
        }
    }

    function autoGenerateSlug(name) {
        if (!document.getElementById('category_id').value) {
            const slug = name.toLowerCase()
                .replace(/[^\w\s-]/g, '')
                .replace(/[\s_-]+/g, '-')
                .replace(/^-+|-+$/g, '');
            document.getElementById('cat_slug').value = slug;
        }
    }

    function openNewCategoryModal() {
        document.getElementById('categoryModalTitle').textContent = 'Add New Category';
        document.getElementById('categoryForm').reset();
        document.getElementById('category_id').value = '';
        document.getElementById('saveCategoryBtn').textContent = 'Save';
        document.getElementById('categoryModal').classList.add('open');
    }

    function openEditCategoryModal(category) {
        document.getElementById('categoryModalTitle').textContent = 'Edit Category: ' + category.name;
        document.getElementById('category_id').value = category.id;
        document.getElementById('cat_name').value = category.name;
        document.getElementById('cat_slug').value = category.slug;
        document.getElementById('cat_description').value = category.description || '';
        document.getElementById('cat_status').value = category.status ? '1' : '0';
        document.getElementById('cat_sort_order').value = category.sort_order || 0;
        document.getElementById('saveCategoryBtn').textContent = 'Update';
        document.getElementById('categoryModal').classList.add('open');
    }

    function closeCategoryModal() {
        document.getElementById('categoryModal').classList.remove('open');
    }

    async function saveCategory(e) {
        e.preventDefault();
        const id = document.getElementById('category_id').value;
        const btn = document.getElementById('saveCategoryBtn');
        btn.disabled = true;
        btn.textContent = id ? 'Updating...' : 'Saving...';

        const formData = new FormData();
        formData.append('name', document.getElementById('cat_name').value);
        formData.append('slug', document.getElementById('cat_slug').value);
        formData.append('description', document.getElementById('cat_description').value);
        formData.append('status', document.getElementById('cat_status').value);
        formData.append('sort_order', document.getElementById('cat_sort_order').value);

        const url = id ? `/api/v1/admin/categories/${id}` : '/api/v1/admin/categories';
        if (id) {
            formData.append('_method', 'PUT');
        }

        try {
            const res = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json'
                },
                body: formData
            });
            const data = await res.json();
            if (data.success) {
                closeCategoryModal();
                showToast(id ? 'Category updated successfully!' : 'Category created successfully!', 'success');
                setTimeout(() => window.location.reload(), 600);
            } else {
                showToast(data.message || 'Validation error saving category', 'error');
            }
        } catch (err) {
            console.error(err);
            showToast('Failed to save category. Check console.', 'error');
        } finally {
            btn.disabled = false;
            btn.textContent = id ? 'Update' : 'Save';
        }
    }

    function deleteCategory(id, name) {
        confirmDeleteModal('Delete Category', name, async () => {
            try {
                const res = await fetch(`/api/v1/admin/categories/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json'
                    }
                });
                const data = await res.json();
                if (data.success) {
                    const row = document.getElementById(`cat-row-${id}`);
                    if (row) row.remove();
                    showToast(`Category "${name}" deleted successfully!`, 'success');
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showToast(data.message || 'Error deleting category', 'error');
                }
            } catch (err) {
                showToast('Failed to delete category.', 'error');
            }
        });
    }
</script>
@endsection
