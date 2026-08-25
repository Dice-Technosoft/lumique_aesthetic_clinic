<?php

namespace App\Jobs;

use App\Mail\AdminFollowUpNotificationMail;
use App\Models\Lead;
use App\Models\LeadFollowUp;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendFollowUpNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Lead $lead,
        public LeadFollowUp $followUp
    ) {
    }

    public function handle(): void
    {
        try {
            $adminEmail = SiteSetting::get('admin_notification_email');
            
            if (empty($adminEmail) || $adminEmail === 'info@lumiqueclinic.com') {
                $adminEmail = SiteSetting::get('contact_email') 
                    ?: config('mail.from.address') 
                    ?: \App\Models\User::first()?->email 
                    ?: 'info@dicetechnosoft.cloud';
            }

            Mail::to($adminEmail)->send(new AdminFollowUpNotificationMail($this->lead, $this->followUp));
        } catch (\Throwable $e) {
            Log::error('Failed to send admin follow-up notification: ' . $e->getMessage(), [
                'lead_id' => $this->lead->id,
                'follow_up_id' => $this->followUp->id,
            ]);
        }
    }
}
