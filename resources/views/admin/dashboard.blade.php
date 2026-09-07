@extends('layouts.admin')

@section('title', 'Executive Analytics Dashboard')
@section('breadcrumb_current', 'Executive Dashboard')
@section('page_title', 'Clinic Executive Dashboard & Performance Analytics')

@section('content')
<!-- Header Quick Action & Telemetry Status -->
<div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; margin-bottom: 1.25rem;">
    <div>
        <p style="font-size: 0.85rem; color: var(--color-charcoal-muted); margin: 0;">
            Live telemetry synchronized with clinic patient intake, appointment requests, and clinical services.
        </p>
    </div>
    <div style="display: flex; align-items: center; gap: 0.75rem;">
        <span class="badge-gold" style="display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.35rem 0.75rem;">
            <span style="width: 7px; height: 7px; border-radius: 50%; background: #2E7D32; display: inline-block; box-shadow: 0 0 0 2px rgba(46,125,50,0.2);"></span>
            <span>Live Database Telemetry</span>
        </span>
        <button type="button" onclick="window.location.reload()" class="btn btn-outline-gold btn-sm" style="display: inline-flex; align-items: center; gap: 0.35rem;" title="Refresh Data">
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"></polyline><polyline points="1 20 1 14 7 14"></polyline><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"></path></svg>
            <span>Sync</span>
        </button>
    </div>
</div>

<!-- Primary KPI Statistics Cards -->
<div class="dashboard-stats-grid">
    <!-- Card 1: Total Inquiries -->
    <div class="stat-widget-card">
        <div class="widget-icon-box bg-burgundy">📥</div>
        <div class="widget-info" style="flex: 1;">
            <div style="display: flex; justify-content: space-between; align-items: baseline;">
                <span class="widget-label">Total Inquiries</span>
                @if(($summary['today_inquiries'] ?? 0) > 0)
                    <span style="font-size: 0.7rem; font-weight: 700; color: #2E7D32; background: #E8F5E9; padding: 0.15rem 0.4rem; border-radius: 4px;">+{{ $summary['today_inquiries'] }} Today</span>
                @endif
            </div>
            <h3 class="widget-value">{{ $summary['total_inquiries'] ?? 0 }}</h3>
            <span class="widget-delta delta-new">
                {{ $summary['new_inquiries'] ?? 0 }} Unread / Pending
            </span>
        </div>
    </div>

    <!-- Card 2: Appointment Bookings -->
    <div class="stat-widget-card">
        <div class="widget-icon-box bg-gold">📅</div>
        <div class="widget-info" style="flex: 1;">
            <div style="display: flex; justify-content: space-between; align-items: baseline;">
                <span class="widget-label">Appointments</span>
                @if(($summary['today_appointments'] ?? 0) > 0)
                    <span style="font-size: 0.7rem; font-weight: 700; color: #2E7D32; background: #E8F5E9; padding: 0.15rem 0.4rem; border-radius: 4px;">+{{ $summary['today_appointments'] }} Today</span>
                @endif
            </div>
            <h3 class="widget-value">{{ $summary['appointment_requests'] ?? 0 }}</h3>
            <span class="widget-delta delta-active">
                Direct Consultation Requests
            </span>
        </div>
    </div>

    <!-- Card 3: Active CRM Leads -->
    <div class="stat-widget-card">
        <div class="widget-icon-box bg-teal">👥</div>
        <div class="widget-info" style="flex: 1;">
            <div style="display: flex; justify-content: space-between; align-items: baseline;">
                <span class="widget-label">Active CRM Leads</span>
            </div>
            <h3 class="widget-value">{{ $summary['total_leads'] ?? 0 }}</h3>
            <span class="widget-delta delta-converted">
                {{ $summary['converted_leads'] ?? 0 }} Converted Patients
            </span>
        </div>
    </div>

    <!-- Card 4: Pending Follow-ups -->
    <div class="stat-widget-card">
        <div class="widget-icon-box bg-coral">⏰</div>
        <div class="widget-info" style="flex: 1;">
            <div style="display: flex; justify-content: space-between; align-items: baseline;">
                <span class="widget-label">Pending Follow-ups</span>
                @if(($summary['today_followups'] ?? 0) > 0)
                    <span style="font-size: 0.7rem; font-weight: 700; color: #D32F2F; background: #FFEBEE; padding: 0.15rem 0.4rem; border-radius: 4px;">{{ $summary['today_followups'] }} Due Today</span>
                @endif
            </div>
            <h3 class="widget-value">{{ $summary['pending_followups'] ?? 0 }}</h3>
            <span class="widget-delta {{ ($summary['overdue_followups'] ?? 0) > 0 ? 'delta-alert' : 'delta-active' }}">
                @if(($summary['overdue_followups'] ?? 0) > 0)
                    {{ $summary['overdue_followups'] }} Overdue Touchpoints
                @else
                    All Scheduled on Track
                @endif
            </span>
        </div>
    </div>
</div>

<!-- Secondary Clinic Operations & Content Telemetry Strip -->
<div class="clinic-telemetry-bar">
    <div class="telemetry-mini-card">
        <span class="mini-icon">💉</span>
        <div class="mini-info">
            <span class="mini-val">{{ $summary['published_services'] ?? 0 }}</span>
            <span class="mini-lbl">Active Treatments</span>
        </div>
    </div>
    <div class="telemetry-mini-card">
        <span class="mini-icon">📂</span>
        <div class="mini-info">
            <span class="mini-val">{{ $summary['active_categories'] ?? 0 }}</span>
            <span class="mini-lbl">Clinical Categories</span>
        </div>
    </div>
    <div class="telemetry-mini-card">
        <span class="mini-icon">🩺</span>
        <div class="mini-info">
            <span class="mini-val">{{ $summary['active_doctors'] ?? 0 }}</span>
            <span class="mini-lbl">Medical Specialists</span>
        </div>
    </div>
    <div class="telemetry-mini-card">
        <span class="mini-icon">⭐</span>
        <div class="mini-info">
            <span class="mini-val">{{ $summary['approved_testimonials'] ?? 0 }}</span>
            <span class="mini-lbl">Patient Testimonials</span>
        </div>
    </div>
    <div class="telemetry-mini-card">
        <span class="mini-icon">📰</span>
        <div class="mini-info">
            <span class="mini-val">{{ $summary['published_posts'] ?? 0 }}</span>
            <span class="mini-lbl">Blog Articles</span>
        </div>
    </div>
</div>

<!-- Interactive Analytics Charts Grid - Row 1 -->
<div class="dashboard-chart-row-1">
    <!-- Chart 1: Monthly Inquiry & Appointment Growth Trends -->
    <div class="admin-panel-card" style="margin-bottom: 0;">
        <div class="panel-card-header">
            <div>
                <h3>Inquiry & Consultation Volume Trends</h3>
                <small class="text-muted">Past 6 months continuous patient volume based on actual database records</small>
            </div>
            <span class="badge-gold">Monthly Telemetry</span>
        </div>
        <div style="position: relative; height: 290px; width: 100%;">
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
        <div style="position: relative; height: 290px; width: 100%; display: flex; align-items: center; justify-content: center;">
            <canvas id="leadSourceChart"></canvas>
        </div>
    </div>
</div>

<!-- Interactive Analytics Charts Grid - Row 2 -->
<div class="dashboard-chart-row-2">
    <!-- Chart 3: CRM Conversion Pipeline Funnel -->
    <div class="admin-panel-card" style="margin-bottom: 0;">
        <div class="panel-card-header">
            <div>
                <h3>Inquiry & Lead Conversion Funnel</h3>
                <small class="text-muted">Live patient journey stages from initial inquiry to confirmed patient</small>
            </div>
        </div>
        <div style="position: relative; height: 260px; width: 100%;">
            <canvas id="pipelineFunnelChart"></canvas>
        </div>
    </div>

    <!-- Chart 4: Clinical Offerings & Demand Breakdown -->
    <div class="admin-panel-card" style="margin-bottom: 0;">
        <div class="panel-card-header">
            <div>
                <h3>Clinical Service Catalog & Demand</h3>
                <small class="text-muted">Distribution of published treatments across categories</small>
            </div>
        </div>
        <div style="position: relative; height: 220px; width: 100%;">
            <canvas id="categoryDemandChart"></canvas>
        </div>
        @if(!empty($summary['top_services']))
        <div style="margin-top: 0.85rem; padding-top: 0.75rem; border-top: 1px dashed var(--color-border); font-size: 0.8rem;">
            <span style="font-weight: 600; color: var(--color-charcoal); display: block; margin-bottom: 0.35rem;">
                🔥 Most Inquired Treatments:
            </span>
            <div style="display: flex; flex-wrap: wrap; gap: 0.4rem;">
                @foreach($summary['top_services'] as $srvName => $srvCount)
                    <span class="badge badge-gold" style="font-size: 0.72rem; padding: 0.25rem 0.55rem;">
                        {{ $srvName }} ({{ $srvCount }})
                    </span>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Two Column Activity Grid -->
<div class="dashboard-two-col-grid" style="margin-top: 1.5rem;">
    <!-- Recent Inquiries Table -->
    <div class="admin-panel-card" style="margin-bottom: 0;">
        <div class="panel-card-header">
            <div>
                <h3>Recent Patient Inquiries</h3>
                <small class="text-muted">Latest consultations awaiting confirmation</small>
            </div>
            <a href="{{ route('admin.inquiries') }}" class="btn-link-gold">View All ({{ $summary['total_inquiries'] ?? 0 }}) →</a>
        </div>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Patient Details</th>
                        <th>Interest / Procedure</th>
                        <th>Channel</th>
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
                            <span style="font-size: 0.75rem; color: var(--color-charcoal-muted);">
                                {{ $inq->source ?: ($inq->type === 'appointment' ? 'Appointment Modal' : 'Contact Form') }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $inq->status }}">{{ ucfirst(str_replace('_', ' ', $inq->status)) }}</span>
                        </td>
                        <td style="font-size: 0.8rem; color: var(--color-charcoal-muted);">
                            {{ $inq->created_at ? $inq->created_at->diffForHumans() : 'Recently' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No recent inquiries logged yet.</td>
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
                    <span class="fu-day">{{ $fu->follow_up_date ? $fu->follow_up_date->format('d') : '--' }}</span>
                    <span class="fu-month">{{ $fu->follow_up_date ? $fu->follow_up_date->format('M') : '--' }}</span>
                </div>
                <div class="fu-details">
                    <h4>{{ $fu->lead->name ?? 'Patient Lead' }}</h4>
                    <p>{{ $fu->note ?: 'Scheduled consultation follow-up' }}</p>
                    <small>Scheduled: {{ $fu->follow_up_time ? date('h:i A', strtotime($fu->follow_up_time)) : 'Flexible' }} &bull; Assigned: {{ $fu->assignedUser->name ?? 'Medical Team' }}</small>
                </div>
            </div>
            @empty
            <div style="padding: 2.5rem 1.5rem; text-align: center;">
                <span style="font-size: 2rem; display: block; margin-bottom: 0.5rem;">📋</span>
                <p style="color: var(--color-charcoal-muted); font-size: 0.875rem; margin-bottom: 1rem;">
                    No pending follow-ups scheduled for today.
                </p>
                <a href="{{ route('admin.leads') }}" class="btn btn-outline-gold btn-sm">Manage CRM Leads</a>
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
    // -------------------------------------------------------------
    // 1. Monthly Trends Chart (100% Real Database Telemetry)
    // -------------------------------------------------------------
    const trends = @json($summary['monthly_trends'] ?? []);
    const trendsLabels = trends.labels || Object.keys(trends);
    const totalsData = trends.totals || Object.values(trends);
    const appointmentsData = trends.appointments || [];
    const contactsData = trends.contacts || [];

    const trendsCtx = document.getElementById('monthlyTrendsChart');
    if (trendsCtx) {
        new Chart(trendsCtx, {
            type: 'line',
            data: {
                labels: trendsLabels,
                datasets: [
                    {
                        label: 'Total Volume',
                        data: totalsData,
                        borderColor: '#8B1538',
                        backgroundColor: 'rgba(139, 21, 56, 0.08)',
                        borderWidth: 2.5,
                        pointBackgroundColor: '#8B1538',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        tension: 0.35,
                        fill: true
                    },
                    {
                        label: 'Appointments',
                        data: appointmentsData,
                        borderColor: '#D4AF37',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        borderDash: [4, 4],
                        pointBackgroundColor: '#D4AF37',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        tension: 0.35
                    },
                    {
                        label: 'Contact Inquiries',
                        data: contactsData,
                        borderColor: '#00897B',
                        backgroundColor: 'transparent',
                        borderWidth: 2,
                        pointBackgroundColor: '#00897B',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 4,
                        tension: 0.35
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            boxWidth: 12,
                            padding: 12,
                            font: { family: 'Inter', size: 11, weight: '500' }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#14080B',
                        titleColor: '#F5D67D',
                        bodyColor: '#ffffff',
                        padding: 10,
                        cornerRadius: 6,
                        callbacks: {
                            label: function(ctx) {
                                return ` ${ctx.dataset.label}: ${ctx.raw} patient request${ctx.raw === 1 ? '' : 's'}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 11 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { precision: 0, font: { family: 'Inter', size: 11 } }
                    }
                }
            }
        });
    }

    // -------------------------------------------------------------
    // 2. Lead Source Donut Chart (Real Channel Breakdown)
    // -------------------------------------------------------------
    const sourceData = @json($summary['lead_sources'] ?? []);
    const sourceLabels = Object.keys(sourceData);
    const sourceValues = Object.values(sourceData);
    const sourceCtx = document.getElementById('leadSourceChart');

    if (sourceCtx) {
        new Chart(sourceCtx, {
            type: 'doughnut',
            data: {
                labels: sourceLabels,
                datasets: [{
                    data: sourceValues,
                    backgroundColor: [
                        '#8B1538',
                        '#D4AF37',
                        '#00897B',
                        '#25D366',
                        '#1F1F1F',
                        '#E64A19',
                        '#512DA8'
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '66%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 12,
                            font: { size: 11, family: 'Inter' }
                        }
                    },
                    tooltip: {
                        backgroundColor: '#14080B',
                        titleColor: '#F5D67D',
                        bodyColor: '#ffffff',
                        padding: 10,
                        cornerRadius: 6,
                        callbacks: {
                            label: function(ctx) {
                                const total = ctx.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((ctx.raw / total) * 100) : 0;
                                return ` ${ctx.label}: ${ctx.raw} (${percentage}%)`;
                            }
                        }
                    }
                }
            }
        });
    }

    // -------------------------------------------------------------
    // 3. Pipeline Funnel Bar Chart (Real Inquiries + Leads Funnel)
    // -------------------------------------------------------------
    const statusData = @json($summary['status_breakdown'] ?? []);
    const pipelineCtx = document.getElementById('pipelineFunnelChart');
    if (pipelineCtx) {
        new Chart(pipelineCtx, {
            type: 'bar',
            data: {
                labels: ['1. New Unread', '2. Contacted', '3. In Consultation', '4. Converted Patient', '5. Closed / Archived'],
                datasets: [{
                    label: 'Count',
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
                        '#7B1FA2',
                        '#2E7D32',
                        '#607D8B'
                    ],
                    borderRadius: 6
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
                        cornerRadius: 6,
                        callbacks: {
                            label: function(ctx) {
                                return ` ${ctx.raw} patient inquiry / lead${ctx.raw === 1 ? '' : 's'}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 10.5 } }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { precision: 0, font: { family: 'Inter', size: 11 } }
                    }
                }
            }
        });
    }

    // -------------------------------------------------------------
    // 4. Clinical Category Breakdown
    // -------------------------------------------------------------
    const catData = @json($summary['category_breakdown'] ?? []);
    const catCtx = document.getElementById('categoryDemandChart');
    if (catCtx) {
        new Chart(catCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(catData),
                datasets: [{
                    axis: 'y',
                    label: 'Published Treatments',
                    data: Object.values(catData),
                    backgroundColor: [
                        '#8B1538',
                        '#D4AF37',
                        '#00897B',
                        '#E64A19',
                        '#512DA8',
                        '#1976D2'
                    ],
                    borderRadius: 6
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#14080B',
                        titleColor: '#F5D67D',
                        bodyColor: '#ffffff',
                        padding: 10,
                        cornerRadius: 6,
                        callbacks: {
                            label: function(ctx) {
                                return ` ${ctx.raw} treatment procedure${ctx.raw === 1 ? '' : 's'} published`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: { precision: 0, font: { family: 'Inter', size: 11 } }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { font: { family: 'Inter', size: 11 } }
                    }
                }
            }
        });
    }
});
</script>
@endsection
