@extends('layouts.admin')

@section('title', 'Inquiries Management')
@section('breadcrumb_parent', 'Clinic CRM')
@section('breadcrumb_current', 'Inquiries & Bookings')
@section('page_title', 'Inquiry & Booking Submissions')

@section('content')
<div class="admin-panel-card">
    <div class="filter-header-row">
        <div class="filter-pills-group">
            <a href="{{ route('admin.inquiries') }}" class="filter-pill {{ !$status ? 'active' : '' }}">All Inquiries</a>
            <a href="{{ route('admin.inquiries', ['status' => 'new']) }}" class="filter-pill {{ $status === 'new' ? 'active' : '' }}">New Unread</a>
            <a href="{{ route('admin.inquiries', ['status' => 'contacted']) }}" class="filter-pill {{ $status === 'contacted' ? 'active' : '' }}">Contacted</a>
            <a href="{{ route('admin.inquiries', ['status' => 'converted']) }}" class="filter-pill {{ $status === 'converted' ? 'active' : '' }}">Converted</a>
        </div>

        <form action="{{ route('admin.inquiries') }}" method="GET" class="admin-search-wrapper">
            @if($status)<input type="hidden" name="status" value="{{ $status }}">@endif
            <span class="search-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
            </span>
            <input type="text" id="inquirySearchInput" name="search" value="{{ $search }}" placeholder="Search by name, email, or phone..." class="admin-search-input" oninput="filterInquiriesLive(this.value)">
            @if($search)
                <a href="{{ route('admin.inquiries', ['status' => $status]) }}" class="search-clear-link" title="Clear search">&times;</a>
            @endif
        </form>
    </div>

    <div class="table-responsive">
        <table class="admin-table" style="table-layout: fixed; width: 100%;">
            <thead>
                <tr>
                    <th style="width: 6%;">ID</th>
                    <th style="width: 23%;">Patient Name</th>
                    <th style="width: 20%;">Contact Info</th>
                    <th style="width: 16%;">Type / Service</th>
                    <th style="width: 12%;">Date Requested</th>
                    <th style="width: 11%;">Status</th>
                    <th style="width: 12%; text-align: right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($inquiries as $inq)
                <tr id="inquiry_row_{{ $inq->id }}" class="inquiry-data-row" data-search="{{ strtolower($inq->name . ' ' . $inq->email . ' ' . $inq->phone . ' ' . ($inq->service->title ?? '')) }}">
                    <td>#{{ $inq->id }}</td>
                    <td>
                        <strong>{{ $inq->name }}</strong>
                        @if($inq->message)
                        <div class="small text-muted" style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            "{{ $inq->message }}"
                        </div>
                        @endif
                    </td>
                    <td>
                        <div><a href="tel:{{ $inq->phone }}">{{ $inq->phone }}</a></div>
                        <small><a href="mailto:{{ $inq->email }}" class="text-muted">{{ $inq->email }}</a></small>
                    </td>
                    <td>
                        <span class="badge {{ $inq->type === 'appointment' ? 'badge-gold' : 'badge-neutral' }}">
                            {{ $inq->service_name ?: ucfirst($inq->type) }}
                        </span>
                    </td>
                    <td>
                        {{ $inq->preferred_date ? $inq->preferred_date->format('M d, Y') : 'Immediate' }}
                        @if($inq->preferred_time)<br><small class="text-muted">{{ $inq->preferred_time }}</small>@endif
                    </td>
                    <td>
                        <select onchange="updateInquiryStatus({{ $inq->id }}, this.value)" class="status-select status-{{ $inq->status }}">
                            <option value="new" {{ $inq->status === 'new' ? 'selected' : '' }}>New</option>
                            <option value="contacted" {{ $inq->status === 'contacted' ? 'selected' : '' }}>Contacted</option>
                            <option value="in_progress" {{ $inq->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="converted" {{ $inq->status === 'converted' ? 'selected' : '' }}>Converted</option>
                            <option value="closed" {{ $inq->status === 'closed' ? 'selected' : '' }}>Closed</option>
                            <option value="spam" {{ $inq->status === 'spam' ? 'selected' : '' }}>Spam</option>
                        </select>
                    </td>
                    <td style="text-align: right;">
                        <div class="table-actions-group">
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $inq->phone) }}?text=Hello%20{{ urlencode($inq->name) }},%20this%20is%20Lumique%20Aesthetic%20Clinic%20Mumbai%20regarding%20your%20consultation%20inquiry." 
                               target="_blank" 
                               class="action-icon-btn btn-view" 
                               data-tooltip="Chat on WhatsApp"
                               aria-label="WhatsApp Patient">
                               <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                            </a>
                            <a href="tel:{{ $inq->phone }}" 
                               class="action-icon-btn btn-edit" 
                               data-tooltip="Call Patient Phone"
                               aria-label="Call Patient">
                               <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center py-5">No inquiries found matching criteria.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="admin-pagination-row">
        {{ $inquiries->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Live Client-Side Realtime Search
    function filterInquiriesLive(query) {
        query = query.toLowerCase().trim();
        const rows = document.querySelectorAll('.inquiry-data-row');
        rows.forEach(row => {
            const rowData = row.getAttribute('data-search') || '';
            row.style.display = (!query || rowData.includes(query)) ? '' : 'none';
        });
    }

    async function updateInquiryStatus(id, status) {
        try {
            const res = await fetch(`/api/v1/admin/inquiries/${id}/status`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ status: status })
            });
            const data = await res.json();
            if (res.ok && data.success) {
                showToast('Status updated to: ' + status.replace('_', ' '), 'success');
            } else {
                showToast(data.message || 'Failed to update status', 'error');
            }
        } catch(e) {
            showToast('Error updating status', 'error');
        }
    }
</script>
@endsection
