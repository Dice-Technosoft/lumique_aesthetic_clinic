<?php

namespace App\Services;

use App\Models\Lead;
use App\Models\LeadActivity;
use App\Models\LeadFollowUp;
use App\Models\LeadNote;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LeadService
{
    public function createLead(array $data, ?User $user = null): Lead
    {
        return DB::transaction(function () use ($data, $user) {
            $lead = Lead::create($data);

            LeadActivity::create([
                'lead_id' => $lead->id,
                'user_id' => $user?->id,
                'activity_type' => 'manual_lead_created',
                'description' => 'Lead created manually in CRM.',
                'properties' => $data,
            ]);

            return $lead;
        });
    }

    public function addNote(Lead $lead, string $noteText, ?User $user = null): LeadNote
    {
        $note = LeadNote::create([
            'lead_id' => $lead->id,
            'user_id' => $user?->id,
            'note' => $noteText,
        ]);

        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => $user?->id,
            'activity_type' => 'note_added',
            'description' => 'Note added to lead.',
        ]);

        return $note;
    }

    public function scheduleFollowUp(Lead $lead, array $data, ?User $user = null): LeadFollowUp
    {
        $time = $data['follow_up_time'] ?? null;
        if (!empty($time)) {
            try {
                $time = \Carbon\Carbon::parse($time)->format('H:i:s');
            } catch (\Throwable $e) {
                $time = null;
            }
        }

        $followUp = LeadFollowUp::create([
            'lead_id' => $lead->id,
            'assigned_to' => $data['assigned_to'] ?? $user?->id,
            'follow_up_date' => $data['follow_up_date'],
            'follow_up_time' => $time,
            'note' => $data['note'] ?? null,
            'status' => 'pending',
        ]);

        $lead->status = 'follow_up';
        $lead->save();

        LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => $user?->id,
            'activity_type' => 'followup_scheduled',
            'description' => "Follow-up scheduled for {$data['follow_up_date']}.",
            'properties' => $data,
        ]);

        return $followUp;
    }

    public function completeFollowUp(LeadFollowUp $followUp, ?string $completionNote = null, ?User $user = null): LeadFollowUp
    {
        $followUp->status = 'completed';
        $followUp->completed_at = now();
        if ($completionNote) {
            $followUp->note .= "\n[Completed Note]: " . $completionNote;
        }
        $followUp->save();

        LeadActivity::create([
            'lead_id' => $followUp->lead_id,
            'user_id' => $user?->id,
            'activity_type' => 'followup_completed',
            'description' => 'Follow-up marked as completed.',
        ]);

        return $followUp;
    }
}
