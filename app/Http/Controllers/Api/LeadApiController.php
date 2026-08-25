<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lead;
use App\Services\LeadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LeadApiController extends Controller
{
    public function __construct(
        protected LeadService $leadService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $query = Lead::with('assignedUser:id,name', 'leadSource:id,name', 'notesList.author:id,name', 'followups')->latest();
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        if ($request->has('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('name', 'like', "%{$s}%")
                  ->orWhere('email', 'like', "%{$s}%")
                  ->orWhere('phone', 'like', "%{$s}%")
                  ->orWhere('service_name', 'like', "%{$s}%");
            });
        }
        $leads = $query->paginate($request->integer('per_page', 15));
        return response()->json([
            'success' => true,
            'data' => $leads->items(),
            'meta' => [
                'current_page' => $leads->currentPage(),
                'per_page' => $leads->perPage(),
                'total' => $leads->total(),
            ],
        ]);
    }

    public function searchPatients(Request $request): JsonResponse
    {
        $q = trim($request->query('q', ''));
        if (strlen($q) < 1) {
            return response()->json(['success' => true, 'data' => []]);
        }

        $leadPatients = Lead::where(function ($query) use ($q) {
            $query->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('phone', 'like', "%{$q}%");
        })
        ->select('name', 'email', 'phone', 'service_name', 'created_at')
        ->latest()
        ->limit(10)
        ->get();

        $inquiryPatients = \App\Models\Inquiry::where(function ($query) use ($q) {
            $query->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('phone', 'like', "%{$q}%");
        })
        ->select('name', 'email', 'phone', 'service_name', 'created_at')
        ->latest()
        ->limit(10)
        ->get();

        $merged = $leadPatients->concat($inquiryPatients)->unique(function ($item) {
            return strtolower(trim($item->name) . '_' . trim($item->phone));
        })->values()->take(8);

        return response()->json([
            'success' => true,
            'data' => $merged,
        ]);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:30',
            'service_name' => 'nullable|string',
            'service_id' => 'nullable|exists:services,id',
            'preferred_date' => 'nullable|date',
            'preferred_time' => 'nullable|string|max:50',
            'lead_source_id' => 'nullable|exists:lead_sources,id',
            'status' => 'nullable|string',
            'priority' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
            'estimated_value' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        if (!empty($data['service_id']) && empty($data['service_name'])) {
            $svc = \App\Models\Service::find($data['service_id']);
            $data['service_name'] = $svc?->title;
        }

        $lead = $this->leadService->createLead($data, $request->user());

        // Create linked inquiry record with appointment type for CRM sync & email triggers
        $inquiry = \App\Models\Inquiry::create([
            'name' => $lead->name,
            'email' => $lead->email,
            'phone' => $lead->phone,
            'service_id' => $lead->service_id,
            'service_name' => $lead->service_name,
            'preferred_date' => $lead->preferred_date,
            'preferred_time' => $lead->preferred_time,
            'source' => 'Admin Appointment Desk',
            'type' => 'appointment',
            'status' => $lead->status ?? 'new',
            'priority' => $lead->priority ?? 'medium',
            'message' => $lead->notes,
        ]);

        $lead->inquiry_id = $inquiry->id;
        $lead->save();

        // Dispatch dual emails (patient confirmation + admin notification)
        \App\Jobs\SendAppointmentThankYouJob::dispatch($inquiry);
        \App\Jobs\SendAppointmentNotificationJob::dispatch($inquiry);

        return response()->json([
            'success' => true,
            'message' => 'Appointment created successfully! Confirmation emails dispatched.',
            'data' => $lead,
        ], 201);
    }

    public function updateStatus(Request $request, Lead $lead): JsonResponse
    {
        $validated = $request->validate([
            'status' => 'required|string|in:new,contacted,consultation_scheduled,follow_up,converted,lost',
        ]);

        $lead->status = $validated['status'];
        $lead->save();

        if ($lead->inquiry) {
            if ($validated['status'] === 'converted') {
                $lead->inquiry->status = 'converted';
                $lead->inquiry->save();
            }
        }

        \App\Models\LeadActivity::create([
            'lead_id' => $lead->id,
            'user_id' => $request->user()?->id,
            'activity_type' => 'status_updated',
            'description' => "Appointment status updated to " . ucfirst(str_replace('_', ' ', $lead->status)) . ".",
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Appointment status updated to ' . ucfirst(str_replace('_', ' ', $lead->status)),
            'data' => $lead,
        ]);
    }

    public function update(Request $request, Lead $lead): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'required|string|max:30',
            'service_name' => 'nullable|string',
            'service_id' => 'nullable|exists:services,id',
            'preferred_date' => 'nullable|date',
            'preferred_time' => 'nullable|string|max:50',
            'status' => 'nullable|string',
            'priority' => 'nullable|string',
            'estimated_value' => 'nullable|numeric',
            'notes' => 'nullable|string',
        ]);

        if (!empty($data['service_id']) && empty($data['service_name'])) {
            $svc = \App\Models\Service::find($data['service_id']);
            $data['service_name'] = $svc?->title;
        }

        $lead->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Appointment updated successfully in database',
            'data' => $lead,
        ]);
    }

    public function destroy(Lead $lead): JsonResponse
    {
        $lead->delete();

        return response()->json([
            'success' => true,
            'message' => 'Lead deleted successfully from database',
        ]);
    }

    public function addNote(Request $request, Lead $lead): JsonResponse
    {
        $request->validate(['note' => 'required|string|max:3000']);
        $note = $this->leadService->addNote($lead, $request->note, $request->user());

        return response()->json([
            'success' => true,
            'message' => 'Note added successfully',
            'data' => $note,
        ]);
    }

    public function scheduleFollowUp(Request $request, Lead $lead): JsonResponse
    {
        $data = $request->validate([
            'follow_up_date' => 'required|date',
            'follow_up_time' => 'nullable|string',
            'note' => 'nullable|string',
            'assigned_to' => 'nullable|exists:users,id',
        ]);

        $followUp = $this->leadService->scheduleFollowUp($lead, $data, $request->user());
        return response()->json([
            'success' => true,
            'message' => 'Follow-up scheduled successfully',
            'data' => $followUp,
        ]);
    }
}
