@extends('layouts.admin')

@section('title', 'Frequently Asked Questions (FAQs) - Lumique Admin')
@section('breadcrumb_parent', 'Website CMS')
@section('breadcrumb_current', 'FAQs Management')
@section('page_title', 'Frequently Asked Questions (FAQs)')

@section('content')
<div class="admin-panel-card">
    <div class="filter-header-row" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
        <div>
            <h3>All Clinic FAQs ({{ $faqs->total() }})</h3>
            <small class="text-muted">Manage patient questions, treatment advice, and accordion answers displayed across the clinic website</small>
        </div>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            <form action="{{ route('admin.faqs') }}" method="GET" class="admin-search-wrapper">
                <span class="search-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                </span>
                <input type="text" id="faqSearchInput" name="search" value="{{ $search ?? '' }}" placeholder="Search questions or answers..." class="admin-search-input" oninput="filterFaqsLive(this.value)">
                @if(!empty($search))
                    <a href="{{ route('admin.faqs') }}" class="search-clear-link" title="Clear search">&times;</a>
                @endif
            </form>
            <button class="btn btn-gold btn-sm" onclick="openNewFaqModal()">+ Add</button>
        </div>
    </div>

    <div class="table-responsive" style="overflow-x: hidden;">
        <table class="admin-table" style="table-layout: fixed; width: 100%;">
            <thead>
                <tr>
                    <th style="width: 32%;">Question</th>
                    <th style="width: 34%;">Answer</th>
                    <th style="width: 14%;">Category / Service</th>
                    <th style="width: 8%; text-align: center;">Status</th>
                    <th style="width: 12%; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($faqs as $faq)
                <tr id="faq_row_{{ $faq->id }}" class="faq-data-row" data-search="{{ strtolower($faq->question . ' ' . $faq->answer . ' ' . ($faq->category->name ?? '') . ' ' . ($faq->service->title ?? '')) }}">
                    <td>
                        <strong style="color: var(--color-charcoal); font-size: 0.9rem; line-height: 1.4; display: block;">{{ $faq->question }}</strong>
                    </td>
                    <td>
                        <div style="font-size: 0.85rem; color: var(--color-charcoal-muted); line-height: 1.5; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;" title="{{ $faq->answer }}">
                            {{ $faq->answer }}
                        </div>
                    </td>
                    <td>
                        @if($faq->category)
                            <span class="badge badge-gold" style="font-size: 0.72rem; margin-bottom: 2px;">{{ $faq->category->name }}</span>
                        @endif
                        @if($faq->service)
                            <span class="badge badge-neutral" style="font-size: 0.7rem; display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">🏥 {{ $faq->service->title }}</span>
                        @endif
                        @if(!$faq->category && !$faq->service)
                            <span class="text-muted" style="font-size: 0.8rem;">General FAQ</span>
                        @endif
                    </td>
                    <td style="text-align: center;">
                        <span class="status-badge status-{{ $faq->status ? 'published' : 'draft' }}">
                            {{ $faq->status ? 'Active' : 'Draft' }}
                        </span>
                    </td>
                    <td style="text-align: right;">
                        <div class="table-actions-group" style="justify-content: flex-end;">
                            <button type="button" class="action-icon-btn btn-edit" data-tooltip="Edit FAQ" aria-label="Edit FAQ" onclick='openEditFaqModal(@json($faq))'>
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path></svg>
                            </button>
                            <button type="button" class="action-icon-btn btn-delete" data-tooltip="Delete FAQ" aria-label="Delete FAQ" onclick="deleteFaq({{ $faq->id }}, '{{ addslashes($faq->question) }}')">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr id="empty-faqs-row">
                    <td colspan="5" class="text-center py-5 text-muted">No frequently asked questions found in database.</td>
                </tr>
                @endforelse
                <tr id="no-faqs-live-matches-row" style="display: none;">
                    <td colspan="5" class="text-center py-5 text-muted">
                        No FAQs matching "<span id="liveFaqSearchQuery"></span>".
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Luxury Styled Pagination -->
    <div class="admin-pagination-row">
        {{ $faqs->links() }}
    </div>
</div>

<!-- Add / Edit FAQ Modal -->
<div class="modal-overlay" id="faqModal">
    <div class="modal-card" style="max-width: 640px;">
        <button type="button" class="modal-close" onclick="closeFaqModal()">&times;</button>
        <div class="modal-header">
            <h3 id="faqModalTitle">Add FAQ Question</h3>
            <p id="faqModalSub" class="text-muted" style="font-size: 0.85rem;">Create or update clinical question and answer</p>
        </div>
        <form onsubmit="handleFaqSubmit(event)" id="faqForm">
            <input type="hidden" id="faq_id" name="id">

            <div class="form-group mb-3">
                <label for="faq_question">Question *</label>
                <input type="text" id="faq_question" name="question" required class="form-control" placeholder="e.g. How many PRP / GFC sessions will I need to see results?">
            </div>

            <div class="form-group mb-3">
                <label for="faq_answer">Answer / Explanation *</label>
                <textarea id="faq_answer" name="answer" rows="4" required class="form-control" placeholder="Detailed medical explanation, clinical protocols, expectations..."></textarea>
            </div>

            <div class="form-row" style="display: flex; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group" style="flex: 1;">
                    <label for="faq_category_id">FAQ Category</label>
                    <select id="faq_category_id" name="category_id" class="form-control">
                        <option value="">General Clinic Questions</option>
                        @foreach($faqCategories as $fCat)
                            <option value="{{ $fCat->id }}">{{ $fCat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group" style="flex: 1;">
                    <label for="faq_service_id">Specific Treatment Link (Optional)</label>
                    <select id="faq_service_id" name="service_id" class="form-control">
                        <option value="">All Treatments / General</option>
                        @foreach($services as $svc)
                            <option value="{{ $svc->id }}">{{ $svc->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-row" style="display: flex; gap: 1rem; align-items: center; margin-bottom: 1rem; padding: 0.75rem; background: var(--color-bg-light); border-radius: 6px;">
                <div class="form-group" style="flex: 1; margin: 0;">
                    <label for="faq_sort_order" style="font-size: 0.8rem; margin-bottom: 2px;">Display Order</label>
                    <input type="number" id="faq_sort_order" name="sort_order" class="form-control form-control-sm" value="0" min="0">
                </div>
                <label style="display: flex; align-items: center; gap: 0.5rem; margin: 0; font-size: 0.875rem; cursor: pointer; flex: 1;">
                    <input type="checkbox" id="faq_status" name="status" value="1" checked>
                    <span>Active on Website</span>
                </label>
            </div>

            <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; padding-top: 1rem; border-top: 1px solid var(--color-border);">
                <button type="button" class="btn btn-outline-gold btn-sm" onclick="closeFaqModal()">Cancel</button>
                <button type="submit" id="saveFaqBtn" class="btn btn-gold btn-sm">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Realtime Client Live Search
    function filterFaqsLive(query) {
        query = query.toLowerCase().trim();
        const rows = document.querySelectorAll('.faq-data-row');
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

        const noMatchesRow = document.getElementById('no-faqs-live-matches-row');
        if (noMatchesRow) {
            if (visibleCount === 0 && rows.length > 0) {
                document.getElementById('liveFaqSearchQuery').textContent = query;
                noMatchesRow.style.display = '';
            } else {
                noMatchesRow.style.display = 'none';
            }
        }
    }

    function openNewFaqModal() {
        document.getElementById('faqForm').reset();
        document.getElementById('faq_id').value = '';
        document.getElementById('faq_status').checked = true;
        document.getElementById('faq_sort_order').value = 0;
        document.getElementById('faqModalTitle').innerText = 'Add FAQ Question';
        document.getElementById('faqModalSub').innerText = 'Create a new question and answer for patients';
        document.getElementById('saveFaqBtn').innerText = 'Save';
        document.getElementById('faqModal').classList.add('open');
    }

    function openEditFaqModal(faq) {
        document.getElementById('faq_id').value = faq.id;
        document.getElementById('faq_question').value = faq.question || '';
        document.getElementById('faq_answer').value = faq.answer || '';
        document.getElementById('faq_category_id').value = faq.category_id || '';
        document.getElementById('faq_service_id').value = faq.service_id || '';
        document.getElementById('faq_sort_order').value = faq.sort_order || 0;
        document.getElementById('faq_status').checked = faq.status ? true : false;

        document.getElementById('faqModalTitle').innerText = 'Edit FAQ Question';
        document.getElementById('faqModalSub').innerText = 'Update question, answer, and category linkage';
        document.getElementById('saveFaqBtn').innerText = 'Update';
        document.getElementById('faqModal').classList.add('open');
    }

    function closeFaqModal() {
        document.getElementById('faqModal').classList.remove('open');
    }

    async function handleFaqSubmit(e) {
        e.preventDefault();
        const id = document.getElementById('faq_id').value;
        const btn = document.getElementById('saveFaqBtn');
        btn.disabled = true;
        btn.innerText = id ? 'Updating...' : 'Saving...';

        const payload = {
            question: document.getElementById('faq_question').value,
            answer: document.getElementById('faq_answer').value,
            category_id: document.getElementById('faq_category_id').value || null,
            service_id: document.getElementById('faq_service_id').value || null,
            sort_order: parseInt(document.getElementById('faq_sort_order').value) || 0,
            status: document.getElementById('faq_status').checked ? 1 : 0
        };

        const url = id ? `/api/v1/admin/faqs/${id}` : '/api/v1/admin/faqs';
        const method = id ? 'PUT' : 'POST';

        try {
            const res = await fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            });

            const data = await res.json();
            if (res.ok && data.success) {
                closeFaqModal();
                showToast(id ? 'FAQ question updated successfully!' : 'FAQ question created successfully!', 'success');
                setTimeout(() => location.reload(), 800);
            } else {
                showToast(data.message || 'Failed to save FAQ', 'error');
            }
        } catch(err) {
            showToast('Network error saving FAQ', 'error');
        } finally {
            btn.disabled = false;
            btn.innerText = id ? 'Update' : 'Save';
        }
    }

    function deleteFaq(id, question) {
        confirmDeleteModal('Delete FAQ Question', question, async () => {
            try {
                const res = await fetch(`/api/v1/admin/faqs/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                });

                const data = await res.json();
                if (res.ok && data.success) {
                    const row = document.getElementById(`faq_row_${id}`);
                    if (row) row.remove();
                    showToast(`FAQ question deleted successfully!`, 'success');
                } else {
                    showToast(data.message || 'Failed to delete FAQ', 'error');
                }
            } catch(err) {
                showToast('Network error deleting FAQ', 'error');
            }
        });
    }
</script>
@endsection
