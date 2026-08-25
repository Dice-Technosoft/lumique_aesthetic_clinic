<?php

namespace App\Jobs;

use App\Mail\AdminAppointmentNotificationMail;
use App\Models\Inquiry;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAppointmentNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Inquiry $inquiry)
    {
    }

    public function handle(): void
    {
        try {
            $adminEmail = SiteSetting::get('admin_notification_email');
            
            if (empty($adminEmail)) {
                return;
            }

            Mail::to($adminEmail)->send(new AdminAppointmentNotificationMail($this->inquiry));
        } catch (\Throwable $e) {
            Log::error('Failed to send admin appointment notification: ' . $e->getMessage(), [
                'inquiry_id' => $this->inquiry->id,
            ]);
        }
    }
}
