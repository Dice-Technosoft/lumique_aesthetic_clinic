@extends('layouts.admin')

@section('title', 'Executive Analytics Dashboard')
@section('breadcrumb_current', 'Executive Dashboard')
@section('page_title', 'Clinic Executive Dashboard & Performance Analytics')

@section('content')
<!-- Key Statistics Cards -->
<div class="dashboard-stats-grid">
    <div class="stat-widget-card">
        <div class="widget-icon-box bg-burgundy">📥</div>
        <div class="widget-info">
            <span class="widget-label">Total Inquiries</span>
            <h3 class="widget-value">{{ $summary['total_inquiries'] }}</h3>
            <span class="widget-delta delta-new">{{ $summary['new_inquiries'] }} Unread / Pending</span>
        </div>
    </div>

    <div class="stat-widget-card">
        <div class="widget-icon-box bg-gold">📅</div>
        <div class="widget-info">
            <span class="widget-label">Appointment Bookings</span>
            <h3 class="widget-value">{{ $summary['appointment_requests'] }}</h3>
            <span class="widget-delta delta-active">Direct Modal Requests</span>
        </div>
    </div>

    <div class="stat-widget-card">
        <div class="widget-icon-box bg-teal">👥</div>
        <div class="widget-info">
            <span class="widget-label">Active CRM Leads</span>
            <h3 class="widget-value">{{ $summary['total_leads'] }}</h3>
            <span class="widget-delta delta-converted">{{ $summary['converted_leads'] }} Converted Patients</span>
        </div>
    </div>

    <div class="stat-widget-card">
        <div class="widget-icon-box bg-coral">⏰</div>
        <div class="widget-info">
            <span class="widget-label">Pending Follow-ups</span>
            <h3 class="widget-value">{{ $summary['pending_followups'] }}</h3>
            <span class="widget-delta delta-alert">Action Required Today</span>
        </div>
    </div>
</div>

<!-- Interactive Analytics Charts Grid -->
<div style="display: grid; grid-template-columns: 2fr 1.2fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Chart 1: Monthly Inquiry & Appointment Growth Trends -->
    <div class="admin-panel-card" style="margin-bottom: 0;">
        <div class="panel-card-header">
            <div>
                <h3>Inquiry & Consultation Volume Trends</h3>
                <small class="text-muted">Monthly incoming patient volume over the past 6 billing cycles</small>
            </div>
            <span class="badge-gold">Live Telemetry</span>
        </div>
        <div style="position: relative; height: 280px; width: 100%;">
            <canvas id="monthlyTrendsChart"></canvas>
        </div>
    </div>

    <!-- Chart 2: Lead Acquisition Sources -->
    <div class="admin-panel-card" style="margin-bottom: 0;">
        <div class="panel-card-header">
            <div>
                <h3>Patient Acquisition by Channel</h3>
                <small class="text-muted">Breakdown of inquiry intake channels</small>
            </div>
        </div>
        <div style="position: relative; height: 280px; width: 100%; display: flex; align-items: center; justify-content: center;">
            <canvas id="leadSourceChart"></canvas>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem;">
    <!-- Chart 3: CRM Conversion Pipeline -->
    <div class="admin-panel-card" style="margin-bottom: 0;">
        <div class="panel-card-header">
            <div>
                <h3>Lead Conversion Status Funnel</h3>
                <small class="text-muted">Stages from initial inquiry to confirmed patient</small>
            </div>
        </div>
        <div style="position: relative; height: 240px; width: 100%;">
            <canvas id="pipelineFunnelChart"></canvas>
        </div>
    </div>

    <!-- Chart 4: Treatment Category Demand -->
    <div class="admin-panel-card" style="margin-bottom: 0;">
        <div class="panel-card-header">
            <div>
                <h3>Clinical Service Demand Breakdown</h3>
                <small class="text-muted">Distribution across dermatology, hair, and laser offerings</small>
            </div>
        </div>
        <div style="position: relative; height: 240px; width: 100%;">
            <canvas id="categoryDemandChart"></canvas>
        </div>
    </div>
</div>

<!-- Two Column Activity Grid -->
<div class="dashboard-two-col-grid">
    <!-- Recent Inquiries Table -->
    <div class="admin-panel-card" style="margin-bottom: 0;">
        <div class="panel-card-header">
            <div>
                <h3>Recent Patient Inquiries</h3>
                <small class="text-muted">Latest consultations awaiting confirmation</small>
            </div>
            <a href="{{ route('admin.inquiries') }}" class="btn-link-gold">View All ({{ $summary['total_inquiries'] }}) →</a>
        </div>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Patient Details</th>
                        <th>Interest / Procedure</th>
                        <th>Status</th>
                        <th>Date Received</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($summary['recent_inquiries'] as $inq)
                    <tr>
                        <td>
                            <strong>{{ $inq->name }}</strong>
                            <div style="font-size: 0.75rem; color: var(--color-charcoal-muted);">{{ $inq->phone }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $inq->type === 'appointment' ? 'badge-gold' : 'badge-neutral' }}">
                                {{ $inq->service_name ?: ucfirst($inq->type) }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $inq->status }}">{{ ucfirst(str_replace('_', ' ', $inq->status)) }}</span>
                        </td>
                        <td style="font-size: 0.8rem; color: var(--color-charcoal-muted);">
                            {{ $inq->created_at->diffForHumans() }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-muted">No recent inquiries logged.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Upcoming CRM Follow-ups Panel -->
    <div class="admin-panel-card" style="margin-bottom: 0;">
        <div class="panel-card-header">
            <div>
                <h3>Upcoming CRM Follow-ups</h3>
                <small class="text-muted">Scheduled clinical touchpoints</small>
            </div>
            <a href="{{ route('admin.leads') }}" class="btn-link-gold">CRM Lead Pipeline →</a>
        </div>
        <div class="followup-list">
            @forelse($summary['upcoming_followups'] as $fu)
            <div class="followup-item-card">
                <div class="fu-date-badge">
                    <span class="fu-day">{{ $fu->follow_up_date->format('d') }}</span>
                    <span class="fu-month">{{ $fu->follow_up_date->format('M') }}</span>
                </div>
                <div class="fu-details">
                    <h4>{{ $fu->lead->name ?? 'Patient Lead' }}</h4>
                    <p>{{ $fu->note ?: 'Scheduled consultation follow-up' }}</p>
                    <small>Scheduled: {{ $fu->follow_up_time ? date('h:i A', strtotime($fu->follow_up_time)) : 'Flexible' }} &bull; Assigned: {{ $fu->assignedUser->name ?? 'Medical Team' }}</small>
                </div>
            </div>
            @empty
            <div style="padding: 2rem; text-align: center; color: var(--color-charcoal-muted); font-size: 0.875rem;">
                No pending follow-ups scheduled for today.
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Chart.js Engine -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Monthly Trends Chart
    const trendsData = @json($summary['monthly_trends'] ?? []);
    const trendsCtx = document.getElementById('monthlyTrendsChart');
    if (trendsCtx) {
        new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: Object.keys(trendsData),
                datasets: [{
                    label: 'Inquiries & Appointments',
                    data: Object.values(trendsData),
                    borderColor: '#C8101E',
                    backgroundColor: 'rgba(200, 16, 30, 0.08)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#B8860B',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.35,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#14080B',
                        titleColor: '#F5D67D',
                        bodyColor: '#ffffff',
                        padding: 10,
                        cornerRadius: 6
                    }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }

    // 2. Lead Source Donut Chart
    const sourceData = @json($summary['lead_sources'] ?? []);
    const sourceCtx = document.getElementById('leadSourceChart');
    if (sourceCtx) {
        new Chart(sourceCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(sourceData),
                datasets: [{
                    data: Object.values(sourceData),
                    backgroundColor: [
                        '#7A1C2E',
                        '#C8101E',
                        '#D4AF37',
                        '#25D366',
                        '#1F1F1F',
                        '#512DA8'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '68%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 14,
                            font: { size: 11, family: 'Inter' }
                        }
                    }
                }
            }
        });
    }

    // 3. Pipeline Funnel Bar Chart
    const statusData = @json($summary['status_breakdown'] ?? []);
    const pipelineCtx = document.getElementById('pipelineFunnelChart');
    if (pipelineCtx) {
        new Chart(pipelineCtx, {
            type: 'bar',
            data: {
                labels: ['New Inquiries', 'Contacted', 'In Progress', 'Converted', 'Closed'],
                datasets: [{
                    label: 'Leads Count',
                    data: [
                        statusData.new || 0,
                        statusData.contacted || 0,
                        statusData.in_progress || 0,
                        statusData.converted || 0,
                        statusData.closed || 0
                    ],
                    backgroundColor: [
                        '#1976D2',
                        '#F57C00',
                        '#512DA8',
                        '#2E7D32',
                        '#546E7A'
                    ],
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }

    // 4. Clinical Category Breakdown
    const catData = @json($summary['category_breakdown'] ?? []);
    const catCtx = document.getElementById('categoryDemandChart');
    if (catCtx) {
        new Chart(catCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(catData).map(c => c.charAt(0).toUpperCase() + c.slice(1).replace('-', ' ')),
                datasets: [{
                    axis: 'y',
                    label: 'Procedures Available',
                    data: Object.values(catData),
                    backgroundColor: [
                        '#7A1C2E',
                        '#B8860B',
                        '#C8101E',
                        '#00897B',
                        '#E64A19'
                    ],
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { precision: 0 }
                    },
                    y: { grid: { display: false } }
                }
            }
        });
    }
});
</script>
@endsection
