<?php

namespace App\Services;

use App\Models\BlogPost;
use App\Models\Inquiry;
use App\Models\Lead;
use App\Models\LeadFollowUp;
use App\Models\LeadSource;
use App\Models\Page;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\TeamMember;
use App\Models\Testimonial;
use App\Models\Video;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getDashboardSummary(): array
    {
        $today = now()->toDateString();

        $totalInquiries = Inquiry::count();
        $newInquiries = Inquiry::where('status', 'new')->count();
        $todayInquiries = Inquiry::whereDate('created_at', $today)->count();

        $appointmentRequests = Inquiry::where('type', 'appointment')->count();
        $todayAppointments = Inquiry::where('type', 'appointment')->whereDate('created_at', $today)->count();

        $totalLeads = Lead::count();
        $convertedLeads = Lead::where('status', 'converted')->count() + Inquiry::where('status', 'converted')->count();

        $pendingFollowups = LeadFollowUp::where('status', 'pending')->count();
        $todayFollowups = LeadFollowUp::where('status', 'pending')->whereDate('follow_up_date', $today)->count();
        $overdueFollowups = LeadFollowUp::where('status', 'pending')->whereDate('follow_up_date', '<', $today)->count();

        $publishedServices = Service::published()->count();
        $activeCategories = ServiceCategory::where('status', true)->count();
        $activeDoctors = TeamMember::active()->count();
        $approvedTestimonials = Testimonial::active()->count();
        $publishedPosts = BlogPost::published()->count();
        $publishedVideos = Video::published()->count();
        $publishedPages = Page::published()->count();

        return [
            // Core Inquiry & Appointment Counts
            'total_inquiries' => $totalInquiries,
            'new_inquiries' => $newInquiries,
            'today_inquiries' => $todayInquiries,
            'appointment_requests' => $appointmentRequests,
            'today_appointments' => $todayAppointments,

            // CRM Lead & Follow-up Counts
            'total_leads' => $totalLeads,
            'converted_leads' => $convertedLeads,
            'pending_followups' => $pendingFollowups,
            'today_followups' => $todayFollowups,
            'overdue_followups' => $overdueFollowups,

            // Clinical Catalog & Content Metrics
            'published_services' => $publishedServices,
            'active_categories' => $activeCategories,
            'active_doctors' => $activeDoctors,
            'approved_testimonials' => $approvedTestimonials,
            'published_posts' => $publishedPosts,
            'published_videos' => $publishedVideos,
            'published_pages' => $publishedPages,

            // Live Activity Feeds
            'recent_inquiries' => Inquiry::with('service')->latest()->limit(6)->get(),
            'upcoming_followups' => LeadFollowUp::with('lead', 'assignedUser')
                ->where('status', 'pending')
                ->orderBy('follow_up_date', 'asc')
                ->limit(5)
                ->get(),

            // Dynamic Chart Telemetry
            'monthly_trends' => $this->getInquiryMonthlyTrends(),
            'lead_sources' => $this->getLeadSourceBreakdown(),
            'status_breakdown' => $this->getLeadStatusBreakdown(),
            'category_breakdown' => $this->getServicesCategoryBreakdown(),
            'top_services' => $this->getTopDemandedServices(),
        ];
    }

    /**
     * Real Conversion Pipeline Funnel (combines both Inquiries and CRM Leads).
     */
    public function getLeadStatusBreakdown(): array
    {
        $inquiryStatuses = Inquiry::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $leadStatuses = Lead::whereNull('inquiry_id')
            ->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $new = ($inquiryStatuses['new'] ?? 0) + ($leadStatuses['new'] ?? 0);
        $contacted = ($inquiryStatuses['contacted'] ?? 0) + ($leadStatuses['contacted'] ?? 0);
        $inProgress = ($inquiryStatuses['in_progress'] ?? 0) 
            + ($leadStatuses['qualified'] ?? 0) 
            + ($leadStatuses['follow_up'] ?? 0)
            + ($leadStatuses['in_progress'] ?? 0);
        $converted = ($inquiryStatuses['converted'] ?? 0) + ($leadStatuses['converted'] ?? 0);
        $closed = ($inquiryStatuses['closed'] ?? 0) 
            + ($inquiryStatuses['spam'] ?? 0) 
            + ($leadStatuses['closed'] ?? 0) 
            + ($leadStatuses['lost'] ?? 0);

        return [
            'new' => $new,
            'contacted' => $contacted,
            'in_progress' => $inProgress,
            'converted' => $converted,
            'closed' => $closed,
        ];
    }

    /**
     * Real Intake Channel Breakdown from database records.
     */
    public function getLeadSourceBreakdown(): array
    {
        $sources = Inquiry::select('source', DB::raw('count(*) as total'))
            ->whereNotNull('source')
            ->where('source', '!=', '')
            ->groupBy('source')
            ->pluck('total', 'source')
            ->toArray();

        // Inquiries where source was not explicitly tagged
        $inquiriesWithoutSource = Inquiry::where(function ($q) {
            $q->whereNull('source')->orWhere('source', '');
        })->get();

        foreach ($inquiriesWithoutSource as $inq) {
            $channel = $inq->type === 'appointment' ? 'Appointment Booking Modal' : 'Website Contact Form';
            $sources[$channel] = ($sources[$channel] ?? 0) + 1;
        }

        // Direct CRM Leads without an inquiry
        $standaloneLeads = Lead::with('source')
            ->whereNull('inquiry_id')
            ->select('lead_source_id', DB::raw('count(*) as total'))
            ->whereNotNull('lead_source_id')
            ->groupBy('lead_source_id')
            ->get();

        foreach ($standaloneLeads as $lead) {
            $channelName = $lead->source?->name ?? 'Direct CRM Intake';
            $sources[$channelName] = ($sources[$channelName] ?? 0) + (int) $lead->total;
        }

        // Clean, structured default keys if no inquiries recorded yet
        if (empty($sources)) {
            return [
                'Appointment Booking Modal' => 0,
                'Website Contact Form' => 0,
            ];
        }

        return $sources;
    }

    /**
     * Real Treatment Offerings per Clinical Category.
     */
    public function getServicesCategoryBreakdown(): array
    {
        $categories = ServiceCategory::where('status', true)->pluck('name', 'slug')->toArray();

        $counts = Service::published()
            ->select('category', DB::raw('count(*) as total'))
            ->groupBy('category')
            ->pluck('total', 'category')
            ->toArray();

        $result = [];
        foreach ($categories as $slug => $name) {
            $result[$name] = (int) ($counts[$slug] ?? 0);
        }

        foreach ($counts as $catSlug => $count) {
            $name = $categories[$catSlug] ?? ucfirst(str_replace('-', ' ', (string) $catSlug));
            if (!isset($result[$name])) {
                $result[$name] = (int) $count;
            }
        }

        if (empty($result)) {
            return [
                'Skin Rejuvenation' => 0,
                'Hair Restoration' => 0,
                'Laser Treatments' => 0,
                'Aesthetics' => 0,
            ];
        }

        return $result;
    }

    /**
     * 100% Real Continuous Monthly Telemetry over the past 6 months (zero fake rand()).
     */
    public function getInquiryMonthlyTrends(): array
    {
        $driver = DB::getDriverName();
        $dateExpr = $driver === 'sqlite' ? "strftime('%Y-%m', created_at)" : "DATE_FORMAT(created_at, '%Y-%m')";

        $startDate = now()->subMonths(5)->startOfMonth();

        $records = Inquiry::where('created_at', '>=', $startDate)
            ->select(
                DB::raw("{$dateExpr} as month"),
                'type',
                DB::raw('count(*) as count')
            )
            ->groupBy('month', 'type')
            ->get();

        $labels = [];
        $appointments = [];
        $contacts = [];
        $totals = [];
        $rawMap = [];

        for ($i = 5; $i >= 0; $i--) {
            $carbon = now()->subMonths($i);
            $key = $carbon->format('Y-m');
            $label = $carbon->format('M Y');

            $labels[] = $label;
            $appointments[$key] = 0;
            $contacts[$key] = 0;
            $totals[$key] = 0;
            $rawMap[$key] = 0;
        }

        foreach ($records as $rec) {
            $month = $rec->month;
            if (isset($totals[$month])) {
                $cnt = (int) $rec->count;
                if ($rec->type === 'appointment') {
                    $appointments[$month] += $cnt;
                } else {
                    $contacts[$month] += $cnt;
                }
                $totals[$month] += $cnt;
                $rawMap[$month] += $cnt;
            }
        }

        return [
            'labels' => $labels,
            'totals' => array_values($totals),
            'appointments' => array_values($appointments),
            'contacts' => array_values($contacts),
            'raw_map' => $rawMap, // backward compatibility with any array map calls
        ];
    }

    /**
     * Top Demanded Services by patient inquiries & bookings.
     */
    public function getTopDemandedServices(): array
    {
        return Inquiry::whereNotNull('service_name')
            ->where('service_name', '!=', '')
            ->select('service_name', DB::raw('count(*) as total'))
            ->groupBy('service_name')
            ->orderByDesc('total')
            ->limit(5)
            ->pluck('total', 'service_name')
            ->toArray();
    }
}
