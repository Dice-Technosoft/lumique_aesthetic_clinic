<?php

namespace App\Mail;

use App\Models\Lead;
use App\Models\LeadFollowUp;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AdminFollowUpNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Lead $lead,
        public LeadFollowUp $followUp
    ) {
    }

    public function envelope(): Envelope
    {
        $dateStr = $this->followUp->follow_up_date ? $this->followUp->follow_up_date->format('M d, Y') : 'Upcoming';
        $timeStr = $this->followUp->follow_up_time ? ' at ' . $this->followUp->follow_up_time : '';
        return new Envelope(
            subject: 'Follow-Up Scheduled: ' . $this->lead->name . ' (' . $dateStr . $timeStr . ') - Lumique CRM',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.admin_followup',
            with: [
                'settings' => app(\App\Services\SettingsService::class)->all(),
            ],
        );
    }
}
