<?php

namespace App\Jobs;

use App\Mail\CustomerAppointmentThankYouMail;
use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendAppointmentThankYouJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Inquiry $inquiry)
    {
    }

    public function handle(): void
    {
        try {
            Mail::to($this->inquiry->email)->send(new CustomerAppointmentThankYouMail($this->inquiry));
        } catch (\Throwable $e) {
            Log::error('Failed to send customer appointment thank you email: ' . $e->getMessage(), [
                'inquiry_id' => $this->inquiry->id,
            ]);
        }
    }
}
