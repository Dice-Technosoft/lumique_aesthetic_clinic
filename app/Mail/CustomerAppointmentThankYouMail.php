<?php

namespace App\Mail;

use App\Models\Inquiry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CustomerAppointmentThankYouMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Inquiry $inquiry)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Appointment Request Received - Lumique Aesthetic Clinic Mumbai',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.customer_appointment_thank_you',
            with: [
                'settings' => app(\App\Services\SettingsService::class)->all(),
            ],
        );
    }
}
