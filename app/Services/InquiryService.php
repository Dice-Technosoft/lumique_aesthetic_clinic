<?php

namespace App\Services;

use App\Jobs\SendAppointmentNotificationJob;
use App\Jobs\SendAppointmentThankYouJob;
use App\Jobs\SendInquiryNotificationJob;
use App\Jobs\SendInquiryThankYouJob;
use App\Models\Inquiry;
use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadNote;
use App\Models\LeadSource;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InquiryService
{
    public function createInquiry(array $data, ?string $ipAddress = null, ?string $userAgent = null): Inquiry
    {
        return DB::transaction(function () use ($data, $ipAddress, $userAgent) {
            $serviceName = null;
            if (!empty($data['service_id'])) {
                $service = Service::find($data['service_id']);
                $serviceName = $service?->title;
            } elseif (!empty($data['service_name'])) {
                $serviceName = $data['service_name'];
            }

            $inquiry = Inquiry::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'phone' => $data['phone'],
                'subject' => $data['subject'] ?? ($data['type'] === 'appointment' ? 'Appointment Booking Request' : 'Website Consultation Inquiry'),
                'message' => $data['message'] ?? null,
                'service_id' => $data['service_id'] ?? null,
                'service_name' => $serviceName,
                'preferred_date' => $data['preferred_date'] ?? null,
                'preferred_time' => $data['preferred_time'] ?? null,
                'source' => $data['source'] ?? ($data['type'] === 'appointment' ? 'Appointment Booking Modal' : 'Website Contact Form'),
                'type' => $data['type'] ?? 'contact',
                'status' => 'new',
                'priority' => $data['priority'] ?? 'medium',
                'ip_address' => $ipAddress,
                'user_agent' => $userAgent,
            ]);

            // Only automatically create Lead in CRM for direct Appointment bookings
            if ($inquiry->type === 'appointment') {
                $defaultSource = LeadSource::where('slug', 'website-appointment-modal')->first() 
                    ?? LeadSource::where('slug', 'website-contact-form')->first();
                $lead = Lead::create([
                    'inquiry_id' => $inquiry->id,
                    'lead_source_id' => $defaultSource?->id,
                    'name' => $inquiry->name,
                    'email' => $inquiry->email,
                    'phone' => $inquiry->phone,
                    'service_id' => $inquiry->service_id,
                    'service_name' => $inquiry->service_name,
                    'status' => 'new',
                    'priority' => $inquiry->priority,
                    'preferred_date' => $inquiry->preferred_date,
                    'preferred_time' => $inquiry->preferred_time,
                    'notes' => $inquiry->message,
                ]);

                LeadActivity::create([
                    'lead_id' => $lead->id,
                    'activity_type' => 'inquiry_received',
                    'description' => 'New appointment booking request received from website.',
                    'properties' => [
                        'source' => $inquiry->source,
                        'service' => $inquiry->service_name,
                    ],
                ]);
            }

            // Dispatch Dual-Email Jobs
            if ($inquiry->type === 'appointment') {
                SendAppointmentNotificationJob::dispatch($inquiry);
                SendAppointmentThankYouJob::dispatch($inquiry);
            } else {
                SendInquiryNotificationJob::dispatch($inquiry);
                SendInquiryThankYouJob::dispatch($inquiry);
            }

            return $inquiry;
        });
    }

    public function updateStatus(Inquiry $inquiry, string $status, ?int $assignedTo = null): Inquiry
    {
        $inquiry->status = $status;
        if ($assignedTo) {
            $inquiry->assigned_to = $assignedTo;
        }
        $inquiry->save();

        if ($inquiry->lead) {
            $inquiry->lead->status = $status;
            if ($assignedTo) {
                $inquiry->lead->assigned_to = $assignedTo;
            }
            $inquiry->lead->save();

            LeadActivity::create([
                'lead_id' => $inquiry->lead->id,
                'activity_type' => 'status_updated',
                'description' => "Status changed to {$status}",
            ]);
        }

        return $inquiry;
    }
}
